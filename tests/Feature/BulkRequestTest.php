<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Services\BulkRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
// use Mockery;
use App\Models\BulkRequest;

class BulkRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_validates_request_data_before_storing_bulk_request()
    {
        $user = User::factory()->create(['tier' => 'Free']);

        $this->actingAs($user)
            ->post(route('bulk-requests.store'), [])
            ->assertSessionHasErrors(['image_urls']);
    }

    /** @test */
    public function it_creates_bulk_request_and_redirects_for_inertia_requests()
    {
        $user = User::factory()->create(['tier' => 'Free']);

        $response = $this->actingAs($user)
            ->post(route('bulk-requests.store'), [
                'image_urls' => 'https://example.com/image1.jpg',
            ]);

        $bulk = BulkRequest::first();

        $response->assertRedirect(route('bulk-requests.index'))
                 ->assertSessionHas('success', 'Bulk request submitted.');

        $this->assertDatabaseHas('bulk_requests', [
            'id' => $bulk->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function it_returns_json_response_when_request_expects_json()
    {
        $user = User::factory()->create(['tier' => 'Free']);

        $response = $this->actingAs($user)
            ->postJson(route('bulk-requests.store'), [
                'image_urls' => 'https://example.com/image1.jpg',
            ]);

        $bulk = BulkRequest::first();

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Bulk request submitted',
                     'data' => ['id' => $bulk->id],
                 ]);
    }
}
