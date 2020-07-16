<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileManager extends Model
{
    //
    protected $fillable = ['pwd', 'fileName', 'size', 'fileNameMd'];
}
