<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulkRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only logged-in users can submit bulk requests
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'image_urls' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'image_urls.required' => 'Please provide at least one image URL.',
        ];
    }

    /**
     * Process and return the URLs as an array.
     */
    public function getUrls(): array
    {
        return array_filter(array_map('trim', explode("\n", $this->input('image_urls'))));
    }
}
