<?php

namespace App\Events;

use App\Models\BulkRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class BulkRequestUpdated implements ShouldQueue, ShouldBroadcast
{
    use  InteractsWithQueue, Queueable, Dispatchable, InteractsWithSockets, SerializesModels;

    public $bulkRequestId;
    protected string $queueName;

    public function __construct($bulkRequestId, string $queueName = 'default')
    {
        $this->bulkRequestId = $bulkRequestId;
        $this->queueName = $queueName;
    }

    public function broadcastOn()
    {
        return new Channel('bulk-requests');
    }

    public function broadcastAs()
    {
        return 'BulkRequestUpdated';
    }
}
