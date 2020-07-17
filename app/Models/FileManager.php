<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileManager extends Model
{
    //
    protected $fillable = ['pwd', 'fileName', 'size', 'fileNameMd'];
    use SoftDeletes;
}
