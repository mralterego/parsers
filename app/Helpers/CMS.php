<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Cache;


class CMS
{
    public static function getDomains()
    {
        $distrs = config('distrs.distrs');
        $domains = [];

        foreach ($distrs as $d){
            $k = "";
            foreach ($d['url_status'] as $key => $status){
                if ($status == "true"){
                    $k = $key;
                    break;
                }
            }
            if ($k !== ""){
                array_push($domains, $d['url'][$k]);
            }
        }

        return $domains;
    }

    public static function getOrgByUrl($url)
    {
        $distrs = config('distrs.distrs');
        foreach ($distrs as $d){
            if (in_array($url, $d['url'])){
                return $d['name'];
            }
        }
    }

    public static function getContacts($address, $phone, $email,  $filter)
    {
        $filter_contacts = [" ", "&nbsp;"];

        $address = str_replace($filter, "", strip_tags($address));

        $splited_adr = explode("ул.", $address);

        $phone = str_replace($filter, "", mb_strtolower(strip_tags($phone)));
        $email = str_replace($filter, "", mb_strtolower(strip_tags($email)));

        if (count($splited_adr) > 1){
            $contact_each = [
                'address' => str_replace($filter_contacts," ","ул.".$splited_adr[1]),
                'phones' => explode(",", trim(str_replace($filter_contacts,"", $phone))),
                'emails' => explode(",", trim(str_replace($filter_contacts,"", $email))),
            ];
            return $contact_each;
        } else {
            $contact_each = [
                'address' => $address,
                'phones' => explode(",", trim(str_replace($filter_contacts,"", $phone))),
                'emails' => explode(",", trim(str_replace($filter_contacts,"", $email))),
            ];
            return $contact_each;
        }

    }

    public static function contacts($html, $contacts_tag, $filter, $url)
    {
        $contacts_all = [];

        if ($html->find($contacts_tag, 0) != null){
            foreach ($html->find($contacts_tag) as $key => $contacts)
            {
                if ($contacts->find('li', 2) == null){
                    $adress = $contacts->find('li', 0);
                    $phone = $contacts->find('li', 1);
                    $adress = str_replace($filter, "", strip_tags($adress));
                    $phone = str_replace($filter, "", mb_strtolower(strip_tags($phone)));
                    $contact_each = [
                        'address' => $adress,
                        'emails' => "",
                        'phones' => explode(",", trim($phone)),
                    ];
                    if (is_array(CMS::getCityOrOrg($adress, $filter))){
                        $arr = CMS::getCityOrOrg($adress, $filter);
                        $contact_each['city'] = $arr['city'];
                        $contact_each['org'] = CMS::getOrgByUrl($url);
                    } else {
                        $contact_each['city'] = CMS::getCityOrOrg($adress, $filter);
                    }
                    $contact_each['url'] = $url;
                    array_push($contacts_all, $contact_each);

                    # если элементов три
                } else if ($contacts->find('li', 3) == null AND $contacts->find('li', 2) != null){

                    $adress = $contacts->find('li', 0);
                    $phone = $contacts->find('li', 1);
                    $email = $contacts->find('li', 2);

                    $contact_each = CMS::getContacts($adress, $phone, $email, $filter);

                    if (is_array(CMS::getCityOrOrg($adress, $filter))){
                        $arr = CMS::getCityOrOrg($adress, $filter);
                        $contact_each['city'] = $arr['city'];
                        $contact_each['org'] = CMS::getOrgByUrl($url);
                    } else {
                        $contact_each['city'] = CMS::getCityOrOrg($adress, $filter);
                    }
                    $contact_each['url'] = $url;
                    array_push($contacts_all, $contact_each);

                    # если элементов больше трех
                } else {
                    $adress = $contacts->find('li', 0);
                    $phone = $contacts->find('li', 1);
                    $email = $contacts->find('li', 3);

                    $contact_each = CMS::getContacts($adress, $phone, $email, $filter);

                    if (is_array(CMS::getCityOrOrg($adress, $filter))){
                        $arr = CMS::getCityOrOrg($adress, $filter);
                        $contact_each['city'] = $arr['city'];
                        $contact_each['org'] = CMS::getOrgByUrl($url);
                    } else {
                        $contact_each['city'] = CMS::getCityOrOrg($adress, $filter);
                    }
                    $contact_each['url'] = $url;
                    array_push($contacts_all, $contact_each);
                }
            }
        }

        return $contacts_all;
    }

    public static function getCityOrOrg($address, $filter)
    {
        $splited = explode("г.", $address);
        if (strpos($splited[0], "ООО") != false){
            $organization = str_replace($filter, "", strip_tags($splited[0]));
            $splited_twice = explode(",", $splited[1]);
            $city = $splited_twice[0];
            return [
                'org' => trim($organization),
                'city' => trim($city),
            ];
        } else {
            $splited_twice = explode(",", $splited[1]);
            $city = $splited_twice[0];
            return trim($city);
        }
    }

    public static function getOrg($html, $header_title_tag, $url)
    {
        if ($html->find($header_title_tag , 0) != null){
            $organization_name = $html->find($header_title_tag , 0)->innertext;
            return $organization_name;
        } else {
            $organization_name = CMS::getOrgByUrl($url);
            return $organization_name;
        }
    }

    public static function parseInfromproect($html, $url)
    {
        $filter_contacts = ["&nbsp;"];
        $contact_arr = [];
        $main_tag = ".about_hotline";
        foreach ($html->find($main_tag) as $key => $contact)
        {
            $contacts = [];
            $city = strip_tags($contact->find('strong', 0)->innertext);
            $list = $contact->find('ul', 0);
            $contacts['org'] = CMS::getOrgByUrl($url);
            $city = mb_substr($city, 3);
            $contacts['city'] = $city;
            $contacts['url'] = $url;
            $contacts['emails'] = [];
            if ($list-> find("li", 2) == null){
                $custom_splited = explode(":", strip_tags($list-> find("li", 1)));
                $custom['title'] = mb_strtolower($custom_splited[0]);
                $custom['value'] = trim($custom_splited[1]);
                $contacts['custom'] = $custom;
            } else if ($list-> find("li", 3) == null){
                $phones = explode(",", mb_substr($list->find("li", 1)->innertext, 6));
                $contacts['phones'] = $phones;
                $custom_splited = explode(":", strip_tags($list-> find("li", 2)));
                $custom['title'] = mb_strtolower($custom_splited[0]);
                $custom['value'] = trim($custom_splited[1]);
                $contacts['custom'] = $custom;
            } else {
                $adr_splited = explode($city, $list->find("li", 1)->innertext);
                $phones = explode(",", mb_substr($list->find("li", 2)->innertext, 6));
                $contacts['address'] = str_replace($filter_contacts, "", trim(substr($adr_splited[1], 1)));
                $contacts['phones'] = $phones;
                $custom_splited = explode(":", strip_tags($list-> find("li", 3)));
                $custom['title'] = mb_strtolower($custom_splited[0]);
                $custom['value'] = trim($custom_splited[1]);
                $contacts['custom'] = $custom;
            }
            array_push($contact_arr, $contacts);
        }

        return $contact_arr;
    }

    public static function parseSochi($html, $url)
    {
        $filter_contacts = [" ", "&nbsp;"];
        $contacts_arr = [];
        $main_tag = ".about_hotline ul";
        foreach ($html->find($main_tag) as $key => $contact)
        {

            if ($key > 0){
                $contacts = [];
                $org = trim($contact->find("li", 0)->innertext);
                $city_splited = explode("г.", trim(strip_tags($contact->find("li", 1)->innertext)));
                $city_splited_twice = explode(",", $city_splited[1]);
                $city = trim($city_splited_twice[0]);
                $adr_splited = explode($city, $contact->find("li", 1)->innertext);
                $address = substr($adr_splited[1], 2);
                $phone_splited = explode(":", strip_tags($contact->find("li", 2)->innertext));
                $phones = str_replace($filter_contacts, "", $phone_splited[1]);
                $email_splited = explode(":", strip_tags($contact->find("li", 3)->innertext));
                $emails = explode(",", trim($email_splited[1]));
                $contacts['org'] = $org;
                $contacts['city'] = $city;
                $contacts['address'] = $address;
                $contacts['emails'] = $emails;
                $contacts['url'] = $url;
                $contacts['phones'] = explode(",", $phones);
                array_push($contacts_arr, $contacts);
            }

        }
        return $contacts_arr;
    }

    public static function parseMiranda($html,  $url)
    {
        $filter_contacts = [" ", "&nbsp;"];
        $contacts_arr = [];
        $main_tag = ".about_hotline ul";
        foreach ($html->find($main_tag) as $key => $contact)
        {
            $contacts = [];
            $org = trim(strip_tags($contact->find("li", 0)->innertext));
            $city_splited = explode("г.", trim(strip_tags($contact->find("li", 1)->innertext)));
            $city_splited_twice = explode(",", $city_splited[1]);
            $city = trim($city_splited_twice[0]);
            $adr_splited = explode($city, $contact->find("li", 1)->innertext);
            $address = substr($adr_splited[1], 2);
            $phone_splited = explode(":", strip_tags($contact->find("li", 2)->innertext));
            $phones = str_replace($filter_contacts, "", $phone_splited[1]);
            $email_splited = explode(":", strip_tags($contact->find("li", 3)->innertext));
            $emails = explode(",", trim($email_splited[1]));
            $contacts['org'] = $org;
            $contacts['city'] = $city;
            $contacts['address'] = $address;
            $contacts['emails'] = $emails;
            $contacts['url'] = $url;
            $contacts['phones'] = explode(",", $phones);
            array_push($contacts_arr, $contacts);
        }

        return $contacts_arr;
    }

    public static function parseNtd($html, $url)
    {
        $filter_contacts = [" ", "&nbsp;"];
        $contacts_arr = [];
        $main_tag = ".ab_right ul";
        $org =  CMS::getOrgByUrl($url);
        $email = "softmarket@mail.stv.ru";
        foreach ($html->find($main_tag) as $key => $contact)
        {
            $contacts = [];
            $city_splited = explode("г.", trim(strip_tags($contact->find("li", 0)->innertext)));
            $city_splited_twice = explode(",", $city_splited[1]);
            $city = trim($city_splited_twice[0]);
            $adr_splited = explode($city, $contact->find("li", 0)->innertext);
            $address = substr($adr_splited[1], 2);
            $phone_splited = explode(":", strip_tags($contact->find("li", 1)->innertext));
            $phones = str_replace($filter_contacts, "", $phone_splited[1]);
            $contacts['org'] = $org;
            $contacts['city'] = $city;
            $contacts['url'] = $url;
            $contacts['address'] = $address;
            $contacts['emails'] = explode(",", $email);
            $contacts['phones'] = explode(",", $phones);
            array_push($contacts_arr, $contacts);
        }
        return $contacts_arr;

    }

    public static function parseUrengoi($html, $url)
    {
        $main_tag = ".left_contacts";
        $contacts_arr = [];
        $content = $html->find($main_tag, 0);

        $phones_html = $html->find($main_tag." h3", 0)->innertext;
        $phones_splited = explode("<br />", $phones_html);

        $address_html = $html->find($main_tag." p", 0)->innertext;
        $address_splited = explode("г.",  $address_html);
        $city_splited = explode(",", $address_splited[1]);
        $city = trim(strip_tags($city_splited[0]));
        $contacts_arr['city'] = $city;
        $contacts_arr['url'] = $url;
        $contacts_arr['org'] = CMS::getOrgByUrl($url);
        $address = explode($city, $address_html);
        $contacts_arr['address'] = trim(substr($address[1], 2));

        foreach($phones_splited as $key => $val){
            $contacts_arr['phones'][$key] = strip_tags(trim($phones_splited[$key]));
        }

        foreach ($content->find("p a") as $key => $mail){
            $contacts_arr['emails'][$key] = trim(strip_tags($mail->innertext));
        }

        return $contacts_arr;

    }

    public static function parseNovatech($html, $url)
    {
        $main_tag = ".cont_box";
        $contacts_arr = [];
        $content = $html->find($main_tag, 0);
        $address_html = $html->find($main_tag." .one_cont", 0)->innertext;
        $address_full = trim(strip_tags($address_html));
        $city_splited = explode("г.", $address_full);
        $city_splited_twice = explode(",", $city_splited[1]);
        $city = trim($city_splited_twice[0]);
        $address_splited = explode($city, $address_html);
        $address = trim($address_splited[1]);
        $contacts_arr['address'] = substr(strip_tags($address), 2);
        $contacts_html = strip_tags($html->find($main_tag." .one_cont", 1)->innertext);
        $contacts_splited = explode(":", $contacts_html);
        $contacts_arr['phones'][] = trim($contacts_splited[1]);

        $emails_html = strip_tags($html->find($main_tag." .one_cont", 2)->innertext);
        $emails_splited = explode(":", $emails_html);
        $contacts_arr['emails'][] = trim($emails_splited[1]);
        $contacts_arr['org'] = CMS::getOrgByUrl($url);
        $contacts_arr['url'] = $url;
        $contacts_arr['city'] = $city;
        return $contacts_arr;
    }


}