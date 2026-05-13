<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anh extends Model
{
    protected $table = 'anh';
    protected $fillable = ['ten_anh', 'file_id'];
}