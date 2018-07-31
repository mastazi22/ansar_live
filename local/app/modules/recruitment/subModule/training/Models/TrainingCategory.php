<?php

namespace App\modules\recruitment\subModule\training\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingCategory extends Model
{
    protected $table = "training_category";
    protected $guarded = ["id"];
    protected $connection = "recruitment.training";
}
