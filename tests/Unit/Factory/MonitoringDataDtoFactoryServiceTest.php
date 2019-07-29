<?php

declare(strict_types=1);

namespace App\Tests\Unit\Factory;

use App\Document\MonitoringData as MonitoringDataDocument;
use App\Factory\MonitoringDataDtoFactoryService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class MonitoringDataDtoFactoryServiceTest extends TestCase
{
    /** @var MonitoringDataDtoFactoryService */
    private $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = new MonitoringDataDtoFactoryService();
    }

    public function testCreateFrom(): void
    {
        $date = new DateTimeImmutable();
        $document = new MonitoringDataDocument(
            'id 1', 'ok', new DateTimeImmutable(), 'p', 1, 60, $date, 'a.b', 5, null
        );

        $dto = $this->subject->create($document);
        self::assertSame('id 1', $dto->getId());
        self::assertSame('ok', $dto->getStatus());
        self::assertSame('p', $dto->getPayload());
        self::assertSame(1, $dto->getPriority());
        self::assertSame(60, $dto->getIdleTimeoutInSeconds());
        self::assertSame($date, $dto->getDate());
        self::assertSame('a.b', $dto->getPath());
        self::assertSame(5, $dto->getTileExpansionIntervalCount());
        self::assertNull($dto->getTileExpansionGrowthExpression());
    }
}
