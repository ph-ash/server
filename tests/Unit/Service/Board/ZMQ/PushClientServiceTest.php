<?php

namespace App\Tests\Unit\Service\Board\ZMQ;

use App\Dto\MonitoringData;
use App\Exception\PushClientException;
use App\Factory\ContextFactory;
use App\Factory\LoopFactory;
use App\Service\Board\ZMQ\PushClientService;
use DateTime;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use React\EventLoop\LoopInterface;
use React\ZMQ\Context;
use React\ZMQ\SocketWrapper;
use Symfony\Component\Serializer\SerializerInterface;
use ZMQ;
use ZMQSocketException;

class PushClientServiceTest extends TestCase
{
    private $subject;
    private $loopFactory;
    private $contextFactory;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $serializer = $this->prophesize(SerializerInterface::class);
        $this->contextFactory = $this->prophesize(ContextFactory::class);
        $this->loopFactory = $this->prophesize(LoopFactory::class);


        $this->subject = new PushClientService($serializer->reveal(), $this->contextFactory->reveal(), $this->loopFactory->reveal());
    }

    /**
     * @throws Exception
     */
    public function testSendZMQException(): void
    {
        $monitoringData = new MonitoringData('id', 'status', 'payload', 1, 10, new DateTime(), 'somepath');

        $loop = $this->getMockBuilder(LoopInterface::class)->getMock();

        $loop
            ->expects($this->never())
            ->method('addReadStream');

        $this->loopFactory->create()->willReturn($loop);

        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $socketWrapper = $this->getMockBuilder(SocketWrapper::class)->disableOriginalConstructor()->getMock();

        $socketWrapper->method('__call')->with(
            $this->equalTo('connect'),
            $this->equalTo(['tcp://127.0.0.1:5555'])
        )->willThrowException(new ZMQSocketException());

        $context->method('__call')->with(
            $this->equalTo('getSocket'),
            $this->equalTo([ZMQ::SOCKET_PUSH])
        )->willReturn($socketWrapper);

        $this->contextFactory->create($loop)->willReturn($context);

        $this->expectException(PushClientException::class);
        $this->subject->send($monitoringData);
    }

    /**
     * @throws Exception
     */
    public function testInvalidArgumentException(): void
    {
        $monitoringData = new MonitoringData('id', 'status', 'payload', 1, 10, new DateTime(), 'somepath');

        $loop = $this->getMockBuilder(LoopInterface::class)->getMock();

        $loop
            ->expects($this->never())
            ->method('addReadStream');

        $this->loopFactory->create()->willReturn($loop);

        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $socketWrapper = $this->getMockBuilder(SocketWrapper::class)->disableOriginalConstructor()->getMock();

        $socketWrapper->method('__call')->with(
            $this->equalTo('connect'),
            $this->equalTo(['tcp://127.0.0.1:5555'])
        );

        $socketWrapper->method('on')->with(
            $this->equalTo('error'),
            $this->equalTo(function ($e) {
                throw new PushClientException('Server responded with an error.', 0, $e);
            })
        )->willThrowException(new InvalidArgumentException());

        $context->method('__call')->with(
            $this->equalTo('getSocket'),
            $this->equalTo([ZMQ::SOCKET_PUSH])
        )->willReturn($socketWrapper);

        $this->contextFactory->create($loop)->willReturn($context);

        $this->expectException(PushClientException::class);
        $this->subject->send($monitoringData);
    }

    /**
     * @throws Exception
     */
    public function testSuccess(): void
    {
        $monitoringData = new MonitoringData('id', 'status', 'payload', 1, 10, new DateTime(), 'somepath');

        $loop = $this->getMockBuilder(LoopInterface::class)->getMock();

        $loop
            ->expects($this->never())
            ->method('addReadStream');

        $this->loopFactory->create()->willReturn($loop);

        $context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $socketWrapper = $this->getMockBuilder(SocketWrapper::class)->disableOriginalConstructor()->getMock();

        $context->method('__call')->with(
            $this->equalTo('getSocket'),
            $this->equalTo([ZMQ::SOCKET_PUSH])
        )->willReturn($socketWrapper);

        $this->contextFactory->create($loop)->willReturn($context);

        $this->subject->send($monitoringData);
    }
}
