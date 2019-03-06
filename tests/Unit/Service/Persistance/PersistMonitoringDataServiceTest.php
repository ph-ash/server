<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Persistance;

use App\Document\MonitoringData;
use App\Dto\MonitoringData as MonitoringDataDto;
use App\Repository\MonitoringDataRepository;
use App\Service\Persistance\PersistMonitoringDataService;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class PersistMonitoringDataServiceTest extends TestCase
{
    private $monitoringDataRepository;

    /** @var PersistMonitoringDataService */
    private $subject;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->monitoringDataRepository = $this->prophesize(MonitoringDataRepository::class);

        $this->subject = new PersistMonitoringDataService($this->monitoringDataRepository->reveal());
    }

    /**
     * @throws Exception
     */
    public function testInvoke(): void
    {
        $statusChangedAt = new DateTimeImmutable('2019-01-01 00:00:00');
        $priority = 15;
        $monitoringDataDto = new MonitoringDataDto(
            'id', 'satus', 'payload', 1, 60, new DateTimeImmutable(), 'root.branch.leaf', null, null
        );
        $this->monitoringDataRepository->save(Argument::that(function (MonitoringData $monitoringData) use ($statusChangedAt, $priority) {
            return $monitoringData->getPriority() === $priority &&  $monitoringData->getStatusChangedAt() === $statusChangedAt;
        }))->shouldBeCalledOnce();

        $this->subject->invoke($monitoringDataDto, $statusChangedAt, $priority);
    }
}
