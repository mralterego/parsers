<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/parsers', 'Parser\ParserController@parseAndSave');

Route::get('/sitesfill', 'Parser\ParserController@fillSitesTable');

Route::get('/domainsfill', 'Parser\ParserController@fillSiteDomainsTable');

Route::get('/filials', 'Parser\ParserController@getFilialsInfo');

Route::get('/parsetest', 'Parser\ParserController@parsetest');