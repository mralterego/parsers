<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistributorFilials extends Model
{
    /**
     * Таблица для хранения экземпляров объектов
     * @var string
     */
    protected $table = 'distributor_filials';

    /**
     * Разрешить автоинкремент
     * @var bool
     */
    public $incrementing = true;

    /**
     * Включить/выключить поля timestamp (created_at, updated_at)
     * @var bool
     */
    public $timestamps = true;

    /**
     * Атрибуты которые не могут быть присвоены через массовое заполнение
     * @var array
     */
    protected $guarded = ['id'];
}
