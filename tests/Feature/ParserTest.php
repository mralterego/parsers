<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Helpers\CMS;


class ParserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDomains()
    {
        $res =  CMS::domains(true);
        $this->assertInternalType('array', $res);
    }

    public function testAllDomains()
    {
        $res =  CMS::getDomains(true);
        $this->assertInternalType('array', $res);
    }

    public function testOrgByUrl()
    {
        $url = "http://www.miranda.ru/";
        $name = CMS::getOrgByUrl($url);
        $this->assertEquals($name, "ООО Фирма Миранда");
    }

    public function testDistrInfo()
    {
        $url = "http://www.miranda.ru/";
        $arr = CMS::getActualDistrInfo($url);

        $this->assertArrayHasKey('db_name', $arr);
    }



}
