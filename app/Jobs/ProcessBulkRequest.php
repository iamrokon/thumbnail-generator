<?php

namespace App\Jobs;

use App\Events\BulkRequestUpdated;
use App\Models\BulkRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBulkRequest implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, Dispatchable;

    protected int $bulkRequestId;
    protected string $queueName;

    public function __construct(int $bulkRequestId, string $queueName = 'default')
    {
        $this->bulkRequestId = $bulkRequestId;
        $this->queueName = $queueName;
    }

    public function handle()
    {
        $bulkRequest = BulkRequest::with('items')->find($this->bulkRequestId);

        if (! $bulkRequest) {
            return;
        }

        $bulkRequest->update(['status' => 'processing']);

        foreach ($bulkRequest->items as $item) {
            ProcessSingleImage::dispatch($item->id)
                ->onQueue($this->queueName);
        }

        event((new BulkRequestUpdated($item->bulk_request_id))->onQueue($this->queueName));
    }
}
