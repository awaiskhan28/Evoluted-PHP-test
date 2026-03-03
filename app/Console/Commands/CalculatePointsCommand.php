<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CardService;

class CalculatePointsCommand extends Command
{
    protected $signature = 'cards:points';
    protected $description = 'Calculate total points from JSON';

    public function handle(CardService $service)
    {
        $filePath = storage_path('cards.json');

        if (!file_exists($filePath)) {
            $this->error('cards.json not found. Run cards:parse first.');
            return;
        }

        $cards = json_decode(file_get_contents($filePath), true);

        $points = $service->calculatePoints($cards);

        $this->info("Total Points: {$points}");
    }
}