<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkRequestItem extends Model
{
    /** @use HasFactory<\Database\Factories\BulkRequestItemFactory> */
    use HasFactory;
    protected $fillable = [
        'bulk_request_id', 'image_url', 'status', 'processed_at', 'error_message'
    ];

    public function bulkRequest()
    {
        return $this->belongsTo(BulkRequest::class);
    }
}
