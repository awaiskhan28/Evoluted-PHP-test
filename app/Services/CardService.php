<?php

namespace App\Services;

class CardService
{
    public function parseInput(string $filePath): array
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $cards = [];

        foreach ($lines as $line) {
            if (!preg_match('/^Card\s+(\d+):\s*(.+?)\s*\|\s*(.+)$/', $line, $matches)) {
                continue;
            }

            $cardNumber = (int)$matches[1];

            $winning = array_map('intval', preg_split('/\s+/', trim($matches[2])));
            $yours   = array_map('intval', preg_split('/\s+/', trim($matches[3])));

        

            $cards[] = [
                'card' => $cardNumber,
                'winning' => $winning,
                'yours' => $yours,
            ];
        }

        return $cards;
    }

    public function calculatePoints(array $cards): int
    {
        $total = 0;

        foreach ($cards as $card) {
            $matches = array_intersect($card['winning'], $card['yours']);
            $count = count($matches);

            if ($count > 0) {
                $points = 1;
                for ($i = 1; $i < $count; $i++) {
                    $points *= 2;
                }
                $total += $points;
            }
        }

        return $total;
    }
}