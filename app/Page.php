<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';

    const NAME_MAX_SIZE = 200;
    const CONTENT_MAX_SIZE = 1024*1024;

    protected $fillable = [
        'name',
        'text',
        'status',
    ];

    protected $visible = [
        'id', 
        'name',
        'status',
    ];

    function folder()
    {
        return $this->belongsTo('App\Folder');
    }    
}
