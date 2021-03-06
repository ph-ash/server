<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Event\GrowTilesEvent;
use App\Factory\GrowTilesEvent as GrowTilesEventFactory;
use App\Service\GrowTilesDispatcherService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GrowTilesDispatcherServiceTest extends TestCase
{
    private $eventDispatcher;
    private $growTilesEventFactory;
    /** @var GrowTilesDispatcherService */
    private $subject;

    public function setUp(): void
    {
        parent::setUp();
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->growTilesEventFactory = $this->prophesize(GrowTilesEventFactory::class);

        $this->subject = new GrowTilesDispatcherService($this->eventDispatcher->reveal(), $this->growTilesEventFactory->reveal());
    }

    public function testInvoke(): void
    {
        $event = new GrowTilesEvent([]);
        $this->growTilesEventFactory->create()->shouldBeCalled()->willReturn($event);

        $this->eventDispatcher->dispatch('monitoring.grow-tiles', $event)->shouldBeCalled();

        $this->subject->invoke();
    }
}
