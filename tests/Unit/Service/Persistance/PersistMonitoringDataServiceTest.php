<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Persistance;

use App\Document\MonitoringData;
use App\Dto\MonitoringData as MonitoringDataDto;
use App\Factory\MonitoringDataDtoFactory;
use App\Repository\MonitoringDataRepository;
use App\Service\Persistance\PersistMonitoringDataService;
use DateTime;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class PersistMonitoringDataServiceTest extends TestCase
{
    private $monitoringDataDtoFactory;
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
        $this->monitoringDataDtoFactory = $this->prophesize(MonitoringDataDtoFactory::class);

        $this->subject = new PersistMonitoringDataService(
            $this->monitoringDataRepository->reveal(),
            $this->monitoringDataDtoFactory->reveal()
        );
    }

    /**
     * @throws Exception
     */
    public function testInvoke(): void
    {
        $monitoringDataDto = new MonitoringDataDto(
            'id', 'satus', 'payload', 1, 60, new DateTimeImmutable(), 'root.branch.leaf', null, null
        );
        $this->monitoringDataDtoFactory->createFrom(Argument::type(MonitoringData::class))->willReturn($monitoringDataDto);
        $this->monitoringDataRepository->find('id')->shouldBeCalledOnce()->willReturn(null);
        $this->monitoringDataRepository->save(Argument::type(MonitoringData::class))->shouldBeCalledOnce();

        $this->subject->invoke($monitoringDataDto);
    }

    /**
     * @throws Exception
     */
    public function testInvokeNoStatusChange(): void
    {
        $monitoringDataDto = new MonitoringDataDto(
            'id', 'satus', 'payload', 1, 60, new DateTimeImmutable(), 'root.branch.leaf', 5, '* 2'
        );
        $monitoringData = new MonitoringData(
            'id', 'satus', new DateTime('2019-01-01 00:00:00'), 'payload', 1, 60, new DateTimeImmutable(), 'root.branch.leaf', 5, '* 2'
        );
        $this->monitoringDataDtoFactory->createFrom(Argument::type(MonitoringData::class))->willReturn($monitoringDataDto);
        $this->monitoringDataRepository->find('id')->shouldBeCalledOnce()->willReturn($monitoringData);
        $this->monitoringDataRepository->save(
            Argument::that(
                function (MonitoringData $monitoringData) {
                    return $monitoringData->getStatusChangedAt() == new DateTimeImmutable('2019-01-01 00:00:00');
                }
            )
        )->shouldBeCalledOnce();

        $this->subject->invoke($monitoringDataDto);
    }

    /**
     * @throws Exception
     */
    public function testInvokeNoPriorityChange(): void
    {
        $monitoringDataDto = new MonitoringDataDto(
            'id', 'satus', 'payload', 1, 60, new DateTimeImmutable(), 'root.branch.leaf', 5, '* 2'
        );
        $monitoringData = new MonitoringData(
            'id', 'satus', new DateTime('2019-01-01 00:00:00'), 'payload', 5, 60, new DateTimeImmutable(), 'root.branch.leaf', 5, '* 2'
        );
        $this->monitoringDataDtoFactory->createFrom(Argument::type(MonitoringData::class))->willReturn($monitoringDataDto);
        $this->monitoringDataRepository->find('id')->shouldBeCalledOnce()->willReturn($monitoringData);
        $this->monitoringDataRepository->save(
            Argument::that(
                function (MonitoringData $monitoringData) {
                    return $monitoringData->getPriority() === 5;
                }
            )
        )->shouldBeCalledOnce();

        $this->subject->invoke($monitoringDataDto);
    }
}
