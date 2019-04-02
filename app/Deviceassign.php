<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deviceassign extends Model
{
     protected $fillable = [
        'user_id', 'device_id', 'status','created_at'
    ];
    
    //Define the table name
    protected $table = 'devices_assigned';
}
