<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\GrowTiles;

use App\Document\MonitoringData;
use App\Service\GrowTiles\PriorityGrowthService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class PriorityGrowthServiceTest extends TestCase
{
    /** @var PriorityGrowthService */
    private $subject;

    public function setUp()
    {
        parent::setUp();

        $this->subject = new PriorityGrowthService();
    }

    /**
     * @dataProvider provideCalculateNewPriorityProvider
     */
    public function testCalculateNewPriority(int $expected, int $priority, ?string $growthExpression): void
    {
        $monitoring = new MonitoringData(
            '1',
            'error',
            new DateTimeImmutable(),
            '',
            $priority,
            60,
            new DateTimeImmutable(),
            null,
            null,
            $growthExpression
        );

        self::assertSame($expected, $this->subject->calculateNewPriority($monitoring));
    }

    public function provideCalculateNewPriorityProvider(): array
    {
        return [
            'default expression' => [2, 1, null],
            'addition' => [3, 1, '+2'],
            'multiplication' => [6, 2, '* 3'],
            'upper bound addition' => [PHP_INT_MAX, PHP_INT_MAX - 10, '+ 20'],
            'upper bound multiplication' => [PHP_INT_MAX, PHP_INT_MAX / 10, '*30'],
        ];
    }
}
