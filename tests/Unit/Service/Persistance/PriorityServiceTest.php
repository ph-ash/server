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

    /**
     * @dataProvider provideMonitoringData
     */
    public function testCalculate(
        MonitoringDataDto $newMonitoringData,
        ?MonitoringDataDocument $oldMonitoringData,
        int $expectedPriority
    ): void {
        $this->monitoringDataRepository->find('id')->shouldBeCalledOnce()->willReturn($oldMonitoringData);

        self::assertSame($expectedPriority, $this->subject->calculate($newMonitoringData));
    }

    public function provideMonitoringData(): array
    {
        return [
            'old monitoring prio > new monitoring prio (both error)' => [
                $newMonitoringData = $this->createMonitoringDataDto('error', 10),
                $oldMonitoringData = $this->createMonitoringDataDocument('error', 20),
                20
            ],
            'old monitoring prio = new monitoring prio (both error)' => [
                $newMonitoringData = $this->createMonitoringDataDto('error', 20),
                $oldMonitoringData = $this->createMonitoringDataDocument('error', 20),
                20
            ],
            'old monitoring prio < new monitoring prio (both error)' => [
                $newMonitoringData = $this->createMonitoringDataDto('error', 20),
                $oldMonitoringData = $this->createMonitoringDataDocument('error', 10),
                20
            ],
            'old monitoring prio OK > new monitoring prio ERROR' => [
                $newMonitoringData = $this->createMonitoringDataDto('error', 10),
                $oldMonitoringData = $this->createMonitoringDataDocument('ok', 20),
                20
            ],
            'old monitoring prio OK < new monitoring prio ERROR' => [
                $newMonitoringData = $this->createMonitoringDataDto('error', 20),
                $oldMonitoringData = $this->createMonitoringDataDocument('ok', 10),
                20
            ],
            'old monitoring prio OK = new monitoring prio ERROR' => [
                $newMonitoringData = $this->createMonitoringDataDto('error', 20),
                $oldMonitoringData = $this->createMonitoringDataDocument('ok', 20),
                20
            ],
            'old monitoring prio ERROR > new monitoring prio OK' => [
                $newMonitoringData = $this->createMonitoringDataDto('ok', 10),
                $oldMonitoringData = $this->createMonitoringDataDocument('error', 20),
                10
            ],
            'old monitoring prio ERROR = new monitoring prio OK' => [
                $newMonitoringData = $this->createMonitoringDataDto('ok', 20),
                $oldMonitoringData = $this->createMonitoringDataDocument('error', 20),
                20
            ],
            'old monitoring prio ERROR < new monitoring prio OK' => [
                $newMonitoringData = $this->createMonitoringDataDto('ok', 20),
                $oldMonitoringData = $this->createMonitoringDataDocument('error', 10),
                20
            ],
            'no old monitoring | new monitoring error' => [
                $newMonitoringData = $this->createMonitoringDataDto('error', 20),
                null,
                20
            ],
            'no old monitoring | new monitoring ok' => [
                $newMonitoringData = $this->createMonitoringDataDto('ok', 10),
                null,
                10
            ],
        ];
    }

    private function createMonitoringDataDto(string $status, int $priority): MonitoringDataDto
    {
        return new MonitoringDataDto(
            'id',
            $status,
            'payload',
            $priority,
            60,
            new DateTimeImmutable(),
            'root.branch.leaf',
            5,
            '* 2'
        );
    }

    private function createMonitoringDataDocument(string $status, int $priority): MonitoringDataDocument
    {
        return new MonitoringDataDocument(
            'id',
            $status,
            new DateTime('2019-01-01 00:00:00'),
            'payload',
            $priority,
            60,
            new DateTimeImmutable(),
            'root.branch.leaf',
            5,
            '* 2'
        );
    }
}
