<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class File extends Model
{

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($file) {
            $file->{$file->getKeyName()} = (string) Str::uuid();
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }

    protected $fillable = [
        'fileName', 'fileMail', 'location', 'state', 'contentTranslate', 'contentToTranslate', 'translator', 'sourceLang', 'targetLang', 'fileType'
    ];
}
