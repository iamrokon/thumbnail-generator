<?php
namespace App\Services;

use App\Services\Contracts\ImageProcessingServiceInterface;

class NodeJsImageProcessingService implements ImageProcessingServiceInterface
{
    public function process(int $itemId): array
    {
        // Simulate sending the request to Node.js
        \Log::info("Sending item {$itemId} to Node.js service...");

        sleep(1); // simulate network delay

        $statuses = ['processed', 'failed'];

        return [
            'status' => $statuses[array_rand($statuses)],
            'thumbnail_path' => "/thumbnails/{$itemId}.jpg"
        ];
    }
}
