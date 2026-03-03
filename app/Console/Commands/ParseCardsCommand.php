<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CardService;
use Illuminate\Support\Facades\File;

class ParseCardsCommand extends Command
{
    protected $signature = 'cards:parse';
    protected $description = 'Parse input.txt and convert to JSON';

    public function handle(CardService $service)
    {
        $inputPath = public_path('input.txt');
        $outputPath = storage_path('cards.json');

        if (!file_exists($inputPath)) {
            $this->error('input.txt not found in public folder.');
            return Command::FAILURE;
        }

        $cards = $service->parseInput($inputPath);
        if (!is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0755, true);
        }

        file_put_contents($outputPath, json_encode($cards, JSON_PRETTY_PRINT));

        $this->info('Cards parsed and saved to storage/cards.json');
    }
}