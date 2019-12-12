<?php

namespace App\modules\HRM\Models;

use App\models\User;
use Illuminate\Database\Eloquent\Model;

class RequestDumper extends Model
{
    //
    protected $connection = 'hrm';
    protected $table = 'tbl_request_history';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getRequestDataAttribute($value)
    {
        try {
            return unserialize($value);
        } catch (\Exception $e) {
            return (Object)["message" => "unserializable Error"];
        }
    }
}
