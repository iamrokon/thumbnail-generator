<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBulkRequest;
use App\Services\BulkRequestService;
use Inertia\Inertia;
use App\Models\BulkRequestItem;

class BulkRequestController extends Controller
{
    protected BulkRequestService $service;

    public function __construct(BulkRequestService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $status = request()->query('status');
        $items = $this->service->getUserBulkRequests(auth()->id(), $status);

        if (request()->wantsJson()) {
            return response()->json($items);
        }

        return Inertia::render('BulkRequests/Index', [
            'items' => $items,
            'filters' => [
                'status' => $status,
            ],
            'tier' => auth()->user()->tier,
            'limits' => config('tiers'),
        ]);
    }

    public function store(StoreBulkRequest $request)
    {
        try {
            $bulk = $this->service->createBulkRequest(
                $request->getUrls(),
                auth()->id(),
                auth()->user()->tier
            );

            if (!$request->wantsJson()) {
                return redirect()
                    ->route('bulk-requests.index')
                    ->with('success', 'Bulk request submitted.');
            }

            return response()->json(['message' => 'Bulk request submitted', 'data' => $bulk]);

        } catch (\InvalidArgumentException $e) {
            if ($request->inertia()) {
                return back()->withErrors(['image_urls' => $e->getMessage()]);
            }
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
