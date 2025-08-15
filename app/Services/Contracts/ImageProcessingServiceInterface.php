<?php
namespace App\Services\Contracts;

interface ImageProcessingServiceInterface
{
    /**
     * Process an image and return result array
     * [
     *   'status' => 'processed'|'failed',
     *   'thumbnail_path' => string
     * ]
     */
    public function process(int $itemId): array;
}
