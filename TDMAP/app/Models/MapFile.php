<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapFile extends Model
{
    protected $fillable = [
'user_id',
'dc_moi',
'dc_cu',
'quy_hoach',
'tinh'
];
}
