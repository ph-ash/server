<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\GrowTiles;

use App\Document\MonitoringData;
use App\Service\GrowTiles\GrowableFilterService;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

class GrowableFilterServiceTest extends TestCase
{
    /** @var GrowableFilterService */
    private $subject;

    public function setUp()
    {
        parent::setUp();

        $this->subject = new GrowableFilterService();
    }

    /**
     * @dataProvider provideIsGrowableData
     */
    public function testIsGrowable(
        bool $expected,
        int $idleTimeoutInSeconds,
        DateTimeInterface $statusChangedAt,
        ?int $tileExpansionIntervalCount,
        ?DateTimeInterface $lastTileExpansion
    ): void {
        $monitoring = new MonitoringData(
            '1',
            'error',
            $statusChangedAt,
            '',
            1,
            $idleTimeoutInSeconds,
            new DateTimeImmutable(),
            null,
            $tileExpansionIntervalCount,
            null
        );
        if ($lastTileExpansion) {
            $monitoring->setLastTileExpansion($lastTileExpansion);
        }

        self::assertSame($expected, $this->subject->isGrowable($monitoring));
    }

    public function provideIsGrowableData(): array
    {
        return [
            'no statusChangedAt normal' => [true, 60, new DateTimeImmutable('-60 second'), null, null],
            'no statusChangedAt edge lambda' => [true, 60, new DateTimeImmutable('-57 second'), null, null],
            'no statusChangedAt lambda + x' => [false, 60, new DateTimeImmutable('-55 second'), null, null],
            'statusChangedAt normal' => [
                true, 60, new DateTimeImmutable('-60 second'), null, new DateTimeImmutable('-60 second')
            ],
            'statusChangedAt edge lambda' => [
                true, 60, new DateTimeImmutable('-60 second'), null, new DateTimeImmutable('-57 second')
            ],
            'statusChangedAt lambda + x' => [
                false, 60, new DateTimeImmutable('-60 second'), null, new DateTimeImmutable('-55 second')
            ],
            'count default' => [true, 60, new DateTimeImmutable('-60 second'), null, null],
            'count passed' => [true, 60, new DateTimeImmutable('-60 second'), 1, null],
            'count + 1' => [false, 60, new DateTimeImmutable('-60 second'), 2, null],
            'interval count 1' => [true, 30, new DateTimeImmutable('-60 second'), 1, null],
            'interval count 2' => [true, 30, new DateTimeImmutable('-60 second'), 2, null],
            'interval count 3' => [false, 30, new DateTimeImmutable('-60 second'), 3, null],
        ];
    }
}
