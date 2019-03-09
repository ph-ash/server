<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Board\ZMQ;

use App\Exception\ZMQClientException;
use App\Factory\ContextFactory;
use App\Factory\LoopFactory;
use App\Service\Board\ZMQ\ClientService;
use App\ValueObject\Channel;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use React\EventLoop\LoopInterface;
use React\ZMQ\Context;
use React\ZMQ\SocketWrapper;
use ZMQ;
use ZMQSocketException;

class ClientServiceTest extends TestCase
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

        $this->contextFactory = $this->prophesize(ContextFactory::class);
        $this->loopFactory = $this->prophesize(LoopFactory::class);


        $this->subject = new ClientService($this->contextFactory->reveal(), $this->loopFactory->reveal());
    }

    /**
     * @throws Exception
     */
    public function testSendZMQException(): void
    {
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
            $this->equalTo(['tcp://127.0.0.1:5555', true])
        )->willThrowException(new ZMQSocketException());

        $context->method('__call')->with(
            $this->equalTo('getSocket'),
            $this->equalTo([ZMQ::SOCKET_PUB])
        )->willReturn($socketWrapper);

        $this->contextFactory->create($loop)->willReturn($context);

        $this->expectException(ZMQClientException::class);
        $this->subject->send('string', new Channel('push'));
    }

    /**
     * @throws Exception
     */
    public function testInvalidArgumentException(): void
    {
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
            $this->equalTo(['tcp://127.0.0.1:5555', true])
        );

        $socketWrapper->method('on')->with(
            $this->equalTo('error'),
            $this->equalTo(function ($e) {
                throw new ZMQClientException('Server responded with an error.', 0, $e);
            })
        )->willThrowException(new InvalidArgumentException());

        $context->method('__call')->with(
            $this->equalTo('getSocket'),
            $this->equalTo([ZMQ::SOCKET_PUB])
        )->willReturn($socketWrapper);

        $this->contextFactory->create($loop)->willReturn($context);

        $this->expectException(ZMQClientException::class);
        $this->subject->send('string', new Channel('push'));
    }

    /**
     * @throws Exception
     */
    public function testSuccess(): void
    {
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
            $this->equalTo([ZMQ::SOCKET_PUB])
        )->willReturn($socketWrapper);

        $channel = new Channel('push');

        $this->contextFactory->create($loop)->willReturn($context);

        $this->subject->send('message', $channel);
    }
}
