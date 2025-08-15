<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkRequest extends Model
{
    /** @use HasFactory<\Database\Factories\BulkRequestFactory> */
    use HasFactory;
    protected $fillable = ['user_id', 'status'];

    public function items()
    {
        return $this->hasMany(BulkRequestItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
