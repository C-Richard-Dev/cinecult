<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchiveMovie extends Model
{
    protected $table = 'archive_movies';

    protected $fillable = [
        'identifier',
        'title',
        'description',
        'year',
        'date',
        'language',
        'creator',
    ];
}
