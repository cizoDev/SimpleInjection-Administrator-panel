<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'name', 'unique_id', 'is_assigned', 'status','created_at'
    ];
    
    //Define the table name
    protected $table = 'devices';
    
}
