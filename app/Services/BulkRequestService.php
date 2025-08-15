<?php

namespace App\Services;

use App\Models\BulkRequest;
use App\Jobs\ProcessBulkRequest;
use App\Models\BulkRequestItem;
use Illuminate\Pagination\LengthAwarePaginator;

class BulkRequestService
{
    public function getUserBulkRequests(int $userId, ?string $status = null, int $perPage = 20): LengthAwarePaginator
    {
        $query = BulkRequestItem::whereHas('bulkRequest', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });

        if ($status) {
            $query->where('status', $status);
        }

        return $query->orderByDesc('created_at')
                     ->paginate($perPage)
                     ->withQueryString();
    }
    
    public function createBulkRequest(array $urls, int $userId, string $tier): BulkRequest
    {
        $tierConfig = config('tiers')[$tier];

        if (count($urls) > $tierConfig['limit']) {
            throw new \InvalidArgumentException("Limit exceeded. Max {$tierConfig['limit']} URLs");
        }

        $bulk = BulkRequest::create([
            'user_id' => $userId,
            'status'  => 'pending',
        ]);

        foreach ($urls as $url) {
            $bulk->items()->create([
                'image_url' => $url,
                'status'    => 'pending',
            ]);
        }

        ProcessBulkRequest::dispatch($bulk->id, $tier)
            ->onQueue($tierConfig['queue']);

        return $bulk;
    }
}
