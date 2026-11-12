<?php

namespace Tests\Unit;

use App\Services\QuotaGenerationService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class QuotaGenerationServiceTest extends TestCase
{
    public function test_rejects_month_below_one(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new QuotaGenerationService)->handle(2026, 0);
    }

    public function test_rejects_month_above_twelve(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new QuotaGenerationService)->handle(2026, 13);
    }
}
