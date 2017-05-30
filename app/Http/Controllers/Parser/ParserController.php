<?php

namespace App\Http\Controllers\Parser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;
use App\Helpers\CMS;
use App\Models\Sites;
use App\Models\SiteDomains;
use App\Models\DistributorFilials;


class ParserController extends Controller
{


    public function parseAndSave()
    {

        $html = new \Htmldom("");

        return "hello";
    }

    public function fillSitesTable()
    {
        $sites = config('distrs.distrs');
        foreach ($sites as $site)
        {
            $existing_names = Sites::where("title", $site['name'])->get();
            if (empty($existing_names[0])){
                $item = [
                    'title' => $site['name'],
                    'active' =>  true,
                ];
                Sites::create($item);
            }
        }
    }
    public function fillSiteDomainsTable()
    {
        $sites = Sites::get();
        $domains = config('distrs.distrs');
        if (!empty($sites)){
            foreach ($domains as $domain){
                $existing_id = Sites::where("title", $domain['name'])->pluck('id')->toArray();
                $existing_names = SiteDomains::where("hostname", $domain['name'])->get()->toArray();
                foreach($domain['url'] as $key => $url){
                    if (empty($existing_names)){
                        $item = [
                            'site_id' => $existing_id[0],
                            'hostname' => $url,
                            'active' =>  $domain['url_status'][$key],
                        ];

                        SiteDomains::create($item);
                    }
                }
            }
        }
    }

    public function parseTest()
    {
        $url = "http://novateh.ru/";
        $html = new \Htmldom("http://novateh.ru/contacts/");
        $contacts = CMS::parseNovatech($html, $url);
    }

    public function getFilialsInfo()
    {

        $contacts_uri = "contacts.html";
        $contacts_tag = ".about_hotline ul";
        $header_title_tag = ".header_right .hr_name";
        $filter = ['тел.', 'почта:', 'Адрес:', 'email:', 'e-mail:', 'телефоны:', 'телефон/факс:', 'тел/факс:', 'тел./факс:'];

        $domains = CMS::getDomains();
        $result = [];

      // dd($domains);


        foreach ($domains as $key => $url){


            switch ($url){
                case "http://novateh.ru/":
                    $html = new \Htmldom("http://novateh.ru/contacts/");
                    $contacts = CMS::parseNovatech($html, $url);
                    array_push($result, $contacts);
                    continue;
                case "http://aldan116.ru/":
                    continue;
                case "http://texpert-yanao.ru/":
                    $html = new \Htmldom($url."kontakty");
                    $contacts = CMS::parseUrengoi($html, $url);
                    array_push($result, $contacts);
                    continue;
                case "http://kodeks.pro/":
                    continue;
                case "http://www.miranda.ru/":
                    $html = new \Htmldom($url.$contacts_uri);
                    $contacts = CMS::parseMiranda($html, $url);
                    foreach ($contacts as $arr){
                        array_push($result, $arr);
                    }
                    continue;
                case "http://xn----ftbb9aza8e.xn--p1ai/":
                    $html = new \Htmldom($url.$contacts_uri);
                    $contacts = CMS::parseNtd($html, $url);
                    foreach ($contacts as $arr){
                        array_push($result, $arr);
                    }
                    continue;
                case "http://cntd-sib.ru/":
                    $html = new \Htmldom($url.$contacts_uri);
                    $contacts = CMS::parseSochi($html, $url);
                    foreach ($contacts as $arr){
                        array_push($result, $arr);
                    }
                    continue;
                case "http://tehexpert.info/":
                    $html = new \Htmldom($url.$contacts_uri);
                    $contacts = CMS::parseInfromproect($html, "http://tehexpert.info/");
                    foreach ($contacts as $arr){
                        array_push($result, $arr);
                    }
                    continue;
                case "http://dsochi.cntd.ru:8080/":
                    $html = new \Htmldom($url.$contacts_uri);
                    $contacts =  CMS::parseSochi($html, $url);
                    foreach ($contacts as $arr){
                        array_push($result, $arr);
                    }
                    continue;

                default:
                    $html = new \Htmldom($url.$contacts_uri);
                    $contacts_all = CMS::contacts($html, $contacts_tag, $filter, $url);
                    foreach($contacts_all as $contacts){
                        if (!isset($contacts['org'])){
                            $contacts['org'] = CMS::getOrgByUrl($url);
                            array_push($result, $contacts);
                        } else {
                            array_push($result, $contacts);
                        }
                    }
            }
        }


        $filter = ['телефон:', ];
        foreach ($result as $key => $contact){
            echo $contact['org']."<br>";
            echo $contact['url']."<br>";
            if (!isset($contact['custom'])){
                $contact['custom'] = "";
            }
            if (!isset($contact['address'])){
                $contact['address'] = "";
            }
            if (!isset($contact['phones'])){
                $contact['phones'] = "";
            } else {
                foreach ($contact['phones'] as $phone){
                    str_replace($filter, "", $phone);
                }
            }

            $json_contacts = [
                'main' => [
                    'show' => false,
                    'address' => $contact['address'],
                    'phones' => $contact['phones'],
                    'emails' => $contact['emails'],
                ],
                'std' => [
                    'address' => $contact['address'],
                    "map_coordinates" => "",
                    'phones' => $contact['phones'],
                    'emails' => $contact['emails'],
                ],
                "custom" => $contact['custom']
            ];
            $existing_id = SiteDomains::where("hostname", $contact['url'])->pluck('site_id')->toArray();
            $item = [
                'site_id' => $existing_id[0],
                'city' => $contact['city'],
                'org' => $contact['org'],
                'header_title' => $contact['org'],
                'contacts' => json_encode($json_contacts),
                'published' => false,
                'sort' => $key
            ];
            DistributorFilials::create($item);
        }





    }

}