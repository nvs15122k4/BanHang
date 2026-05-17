<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionItem extends Model
{
    protected $fillable = ['promotion_id', 'loai', 'gia_tri'];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}
