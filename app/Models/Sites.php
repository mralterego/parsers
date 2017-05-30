<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Sites extends Model
{
    protected $connection = 'pgsql';

    /**
     * Таблица для хранения экземпляров объектов
     * @var string
     */
    protected $table = 'sites';

    /**
     * Идентификатор
     * @var string
     */
    public $incrementing = true;

    /**
     * Включить/выключить поля timestamp (created_at, updated_at)
     * @var bool
     */
    public $timestamps = true;

    /**
     * Атрибуты доступные к заполнению
     * @var array
     */
    protected $fillable = ['title', 'active'];

}
