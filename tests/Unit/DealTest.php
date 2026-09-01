<?php

namespace Tests\Unit;

use App\Models\Deal;
use Tests\TestCase;

class DealTest extends TestCase
{
    public function test_stage_label_translates_the_stage(): void
    {
        $deal = new Deal(['stage' => Deal::STAGE_WON]);

        $this->assertSame('Ganho', $deal->stage_label);
    }

    public function test_unknown_stage_falls_back_to_the_raw_value(): void
    {
        $deal = new Deal(['stage' => 'custom']);

        $this->assertSame('custom', $deal->stage_label);
    }

    public function test_only_open_stages_count_as_pipeline(): void
    {
        $this->assertTrue((new Deal(['stage' => Deal::STAGE_PROSPECTING]))->isOpen());
        $this->assertTrue((new Deal(['stage' => Deal::STAGE_NEGOTIATION]))->isOpen());
        $this->assertFalse((new Deal(['stage' => Deal::STAGE_WON]))->isOpen());
        $this->assertFalse((new Deal(['stage' => Deal::STAGE_LOST]))->isOpen());
    }
}
