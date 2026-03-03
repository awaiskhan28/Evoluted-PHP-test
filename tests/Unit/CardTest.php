<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CardService;
use Illuminate\Support\Facades\File;

class CardTest extends TestCase
{
    protected string $tempFile;

    protected function setUp(): void
    {
        parent::setUp();
        // Temp file in public folder
        $this->tempFile = public_path('test_input.txt');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
        parent::tearDown();
    }

    /** 
     * Unit test with a controlled file in public/
     */
    public function test_parse_input_creates_correct_structure_with_temp_file()
    {
        $content = "Card 1: 41 48 83 86 17 | 83 86 6 31 17 9 48 53";
        File::put($this->tempFile, $content);

        $service = new CardService();
        $data = $service->parseInput($this->tempFile);

        $this->assertIsArray($data);
        $this->assertEquals(1, $data[0]['card']);
        $this->assertEquals([41, 48, 83, 86, 17], $data[0]['winning']);
        $this->assertEquals([83, 86, 6, 31, 17, 9, 48, 53], $data[0]['yours']);
    }

    /** 
     * Optional smoke test using the actual input.txt file
     */
    public function test_parse_input_with_actual_public_file()
    {
        $filePath = public_path('input.txt');

        if (!file_exists($filePath)) {
            $this->markTestSkipped('public/input.txt does not exist, skipping this test.');
        }

        $service = new CardService();
        $data = $service->parseInput($filePath);

        $this->assertIsArray($data);

        if (!empty($data)) {
            $this->assertArrayHasKey('card', $data[0]);
            $this->assertArrayHasKey('winning', $data[0]);
            $this->assertArrayHasKey('yours', $data[0]);
        }
    }

    public function test_calculate_points_with_single_card()
    {
        $service = new CardService();

        $cards = [
            [
                'card' => 1,
                'winning' => [17, 41, 48, 83, 86],
                'yours' => [6, 9, 17, 31, 48, 53, 83, 86]
            ]
        ];

        $points = $service->calculatePoints($cards);
        $this->assertEquals(8, $points);
    }

    public function test_calculate_points_with_multiple_cards()
    {
        $service = new CardService();

        $cards = [
            [
                'card' => 1,
                'winning' => [1, 2, 3],
                'yours' => [3, 2, 1]
            ],
            [
                'card' => 2,
                'winning' => [4, 5],
                'yours' => [4, 6]
            ]
        ];

        $points = $service->calculatePoints($cards);
        $this->assertEquals(5, $points);
    }

    public function test_calculate_points_returns_zero_if_no_matches()
    {
        $service = new CardService();

        $cards = [
            [
                'card' => 1,
                'winning' => [1, 2, 3],
                'yours' => [4, 5, 6]
            ]
        ];

        $points = $service->calculatePoints($cards);
        $this->assertEquals(0, $points);
    }
}
