<?php

namespace Database\Seeders;

use App\Models\NewsTicker;
use Illuminate\Database\Seeder;

class NewsTickerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tickers = [
            [
                'title' => 'Welcome to PakGold Rates - Your trusted source for real-time gold and silver prices in Pakistan.',
                'content' => 'Stay updated with the latest gold and silver rates across all karats including 24K, 22K, 21K, and 18K.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Gold prices are updated every 5 minutes from multiple reliable sources.',
                'content' => 'We fetch prices from PakGold.net, international gold APIs, and currency exchange services to provide the most accurate rates.',
                'is_active' => true,
                'sort_order' => 2,
            ],
        ];

        foreach ($tickers as $ticker) {
            NewsTicker::updateOrCreate(
                ['title' => $ticker['title']],
                $ticker
            );
        }
    }
}
