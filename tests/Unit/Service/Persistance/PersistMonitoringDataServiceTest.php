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
        $monitoringDataDto = new MonitoringDataDto(
            'id',
            'satus',
            'payload',
            1,
            60,
            new DateTimeImmutable(),
            'root.branch.leaf'
        );
        $this->monitoringDataRepository->save(Argument::type(MonitoringData::class))->shouldBeCalledOnce();

        $this->subject->invoke($monitoringDataDto);
    }
}
