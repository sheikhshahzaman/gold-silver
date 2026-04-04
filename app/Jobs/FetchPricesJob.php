<?php

namespace App\Jobs;

use App\Services\PriceEngine\PriceFetcher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchPricesJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 60;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 2;

    /**
     * The number of seconds after which the job's unique lock will be released.
     */
    public int $uniqueFor = 120;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(PriceFetcher $fetcher): void
    {
        $success = $fetcher->fetchAndStore();

        if (!$success) {
            Log::warning('FetchPricesJob: Price fetch failed on attempt ' . $this->attempts());

            if ($this->attempts() >= $this->tries) {
                Log::error('FetchPricesJob: All attempts exhausted');
            }
        }
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return 'fetch-prices';
    }
}
