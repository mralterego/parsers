<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteDomains extends Model
{
    protected $connection = 'pgsql';

    /**
     * Таблица для хранения экземпляров объектов
     * @var string
     */
    protected $table = 'site_domains';


    public $incrementing = true;

    /**
     * Включить/выключить поля timestamp (created_at, updated_at)
     * @var bool
     */
    public $timestamps = true;

    protected $fillable = ['site_id', 'hostname', 'active'];
}
