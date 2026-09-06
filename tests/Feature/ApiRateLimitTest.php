<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

/**
 * Read and write API traffic must not share a rate-limit bucket.
 *
 * Inline `throttle:x,1` limits all hash to "domain|ip", so the app's background
 * polling used to exhaust the order endpoint's allowance and every checkout
 * failed with "Too many attempts".
 */
class ApiRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_read_traffic_does_not_consume_the_write_allowance(): void
    {
        // Far more reads than the write limit of 60/min.
        for ($i = 0; $i < 80; $i++) {
            $this->getJson('/api/app-config')->assertOk();
        }

        // A write must still be accepted afterwards.
        $response = $this->postJson('/api/verify', ['serial' => 'no-such-serial']);

        $this->assertNotSame(
            429,
            $response->getStatusCode(),
            'Read traffic drained the write rate limiter.'
        );
    }

    public function test_read_and_write_limiters_use_separate_keys(): void
    {
        $this->assertNotNull(RateLimiter::limiter('api-read'));
        $this->assertNotNull(RateLimiter::limiter('api-write'));
        $this->assertNotNull(RateLimiter::limiter('api-internal'));
    }
}
