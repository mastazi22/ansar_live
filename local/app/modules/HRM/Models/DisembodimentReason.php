<?php


namespace App\modules\HRM\Models;


use Illuminate\Database\Eloquent\Model;

class DisembodimentReason extends Model
{
    protected $connection = 'hrm';
    protected $table = 'db_amis.tbl_disembodiment_reason';
    protected $guarded = ['id'];
}