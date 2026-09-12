<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject; 

#[Fillable(['identifier', 'title', 'description', 'year', 'date', 'language', 'creator'])]
#[Table('archive_movies')]
class ArchiveMovie extends Model
{
    //
}
