<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCreationRequest extends Model
{
    use SoftDeletes;
    protected $connection = 'hrm';
    protected $table = 'tbl_user_creation_request';
    protected $guarded = ['id'];
    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_parent_id');
    }
}
