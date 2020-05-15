<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    const NAME_MAX_SIZE = 200;

    protected $fillable = [
        'name',
    ];

    protected $visible = [
        'id', 
        'name'
    ];
}
