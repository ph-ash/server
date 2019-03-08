<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Persistance;

use App\Document\MonitoringData as MonitoringDataDocument;
use App\Dto\MonitoringData as MonitoringDataDto;
use App\Repository\MonitoringDataRepository;
use App\Service\Persistance\StatusChangedService;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class StatusChangedServiceTest extends TestCase
{
    private $monitoringDataRepository;
    /** @var StatusChangedService */
    private $subject;

    public function setUp()
    {
        parent::setUp();
        $this->monitoringDataRepository = $this->prophesize(MonitoringDataRepository::class);

        $this->subject = new StatusChangedService($this->monitoringDataRepository->reveal());
    }

    public function testCalculateNoPersistedDocument(): void
    {
        $date = new DateTimeImmutable();
        $monitoringDataDto = new MonitoringDataDto(
            'id', 'satus', 'payload', 1, 60, $date, 'root.branch.leaf', 5, '* 2'
        );
        $this->monitoringDataRepository->find('id')->shouldBeCalledOnce()->willReturn(null);

        self::assertSame($date, $this->subject->calculate($monitoringDataDto));
    }

    public function testCalculateDifferentStatus(): void
    {
        $date = new DateTimeImmutable();
        $statusChangedAt = new DateTime('2019-01-01 00:00:00');
        $monitoringDataDto = new MonitoringDataDto(
            'id', 'satus', 'payload', 1, 60, $date, 'root.branch.leaf', 5, '* 2'
        );
        $monitoringDataDocument = new MonitoringDataDocument(
            'id', 'error', $statusChangedAt, 'payload', 1, 60, new DateTimeImmutable(), 'root.branch.leaf', 5, '* 2'
        );
        $this->monitoringDataRepository->find('id')->shouldBeCalledOnce()->willReturn($monitoringDataDocument);

        self::assertSame($date, $this->subject->calculate($monitoringDataDto));
    }

    public function testCalculate(): void
    {
        $date = new DateTimeImmutable();
        $statusChangedAt = new DateTime('2019-01-01 00:00:00');
        $monitoringDataDto = new MonitoringDataDto(
            'id', 'satus', 'payload', 1, 60, $date, 'root.branch.leaf', 5, '* 2'
        );
        $monitoringDataDocument = new MonitoringDataDocument(
            'id', 'satus', $statusChangedAt, 'payload', 1, 60, new DateTimeImmutable(), 'root.branch.leaf', 5, '* 2'
        );
        $this->monitoringDataRepository->find('id')->shouldBeCalledOnce()->willReturn($monitoringDataDocument);

        self::assertSame($statusChangedAt, $this->subject->calculate($monitoringDataDto));
    }
}
