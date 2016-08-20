<?php

namespace Core\Translate\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $fillable = [
        'lang',
        'group',
        'key',
        'value'
    ];

}
