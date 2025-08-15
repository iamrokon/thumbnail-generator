<?php

namespace App\Jobs;

use App\Events\BulkRequestUpdated;
use App\Models\BulkRequestItem;
use App\Services\Contracts\ImageProcessingServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessSingleImage implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, Dispatchable;

    protected int $itemId;
    protected string $queueName;

    public function __construct(int $itemId, string $queueName = 'default')
    {
        $this->itemId = $itemId;
        $this->queueName = $queueName;
    }

    public function handle(ImageProcessingServiceInterface $imageService)
    {
        $item = BulkRequestItem::find($this->itemId);

        if (! $item) {
            return;
        }

        // Process via injected service
        $result = $imageService->process($this->itemId);

        // Update DB
        $item->status = $result['status'];
        $item->processed_at = now();
        $item->save();

        event((new BulkRequestUpdated($item->bulk_request_id))->onQueue($this->queueName));
    }
}
