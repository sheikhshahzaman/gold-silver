<?php

namespace App\Console\Commands;

use App\Services\PriceEngine\PriceFetcher;
use Illuminate\Console\Command;

class FetchPricesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prices:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch latest gold, silver, and currency prices from all sources';

    /**
     * Execute the console command.
     */
    public function handle(PriceFetcher $fetcher): int
    {
        $this->info('Fetching latest prices...');

        $success = $fetcher->fetchAndStore();

        if ($success) {
            $this->info('Prices updated successfully.');
            return Command::SUCCESS;
        }

        $this->error('Failed to fetch prices from all sources.');
        return Command::FAILURE;
    }
}
