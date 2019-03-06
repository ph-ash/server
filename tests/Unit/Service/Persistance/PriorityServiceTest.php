<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Persistance;

use App\Repository\MonitoringDataRepository;
use App\Service\Persistance\PriorityService;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use App\Document\MonitoringData as MonitoringDataDocument;
use App\Dto\MonitoringData as MonitoringDataDto;

class PriorityServiceTest extends TestCase
{
    private $monitoringDataRepository;
    /** @var PriorityService */
    private $subject;

    public function setUp()
    {
        parent::setUp();
        $this->monitoringDataRepository = $this->prophesize(MonitoringDataRepository::class);

        $this->subject = new PriorityService($this->monitoringDataRepository->reveal());
    }

    public function testCalculateNoPersistedDocument(): void
    {
        $monitoringDataDto = new MonitoringDataDto(
            'id', 'satus', 'payload', 1, 60, new DateTimeImmutable(), 'root.branch.leaf', 5, '* 2'
        );
        $this->monitoringDataRepository->find('id')->shouldBeCalledOnce()->willReturn(null);

        self::assertSame(1, $this->subject->calculate($monitoringDataDto));
    }

    public function testCalculateSameOrLowerPriority(): void
    {
        $monitoringDataDto = new MonitoringDataDto(
            'id', 'satus', 'payload', 15, 60, new DateTimeImmutable(), 'root.branch.leaf', 5, '* 2'
        );
        $monitoringData = new MonitoringDataDocument(
            'id', 'satus', new DateTime('2019-01-01 00:00:00'), 'payload', 9, 60, new DateTimeImmutable(), 'root.branch.leaf', 5, '* 2'
        );
        $this->monitoringDataRepository->find('id')->shouldBeCalledOnce()->willReturn($monitoringData);

        self::assertSame(15, $this->subject->calculate($monitoringDataDto));
    }

    public function testCalculate(): void
    {
        $monitoringDataDto = new MonitoringDataDto(
            'id', 'satus', 'payload', 15, 60, new DateTimeImmutable(), 'root.branch.leaf', 5, '* 2'
        );
        $monitoringData = new MonitoringDataDocument(
            'id', 'satus', new DateTime('2019-01-01 00:00:00'), 'payload', 27, 60, new DateTimeImmutable(), 'root.branch.leaf', 5, '* 2'
        );
        $this->monitoringDataRepository->find('id')->shouldBeCalledOnce()->willReturn($monitoringData);

        self::assertSame(27, $this->subject->calculate($monitoringDataDto));
    }
}
