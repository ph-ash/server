<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Validation;

use App\Dto\MonitoringData;
use App\Exception\ValidationException;
use App\Service\Validation\MonitoringDataValidationService;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MonitoringDataValidationServiceTest extends TestCase
{
    private $validator;
    /** @var MonitoringDataValidationService */
    private $subject;

    /**
     * @throws Exception
     */
    public function setUp()
    {
        parent::setUp();
        $this->validator = $this->prophesize(ValidatorInterface::class);

        $this->subject = new MonitoringDataValidationService($this->validator->reveal());
    }

    /**
     * @throws Exception
     */
    public function testWithError(): void
    {
        $dateTime = new DateTimeImmutable();
        $id = 'id';
        $status = 'status';
        $payload = 'payload';
        $priority = 1;
        $idleTimeoutInSeconds = 50;
        $path = 'path';
        $monitoringData = new MonitoringData(
            $id,
            $status,
            $payload,
            $priority,
            $idleTimeoutInSeconds,
            $dateTime,
            $path
        );

        $message = 'constraintViolation';

        $violationList = $this->prophesize(ConstraintViolationListInterface::class);
        $violationList->count()->willReturn(1);

        $constraintViolation = $this->prophesize(ConstraintViolationInterface::class);
        $constraintViolation->getMessage()->willReturn($message);


        $this->validator->validate($monitoringData)->willReturn($violationList->reveal());
        $violationList->get(0)->willReturn($constraintViolation->reveal());


        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage($message);
        $this->subject->invoke($monitoringData);
    }

    /**
     * @throws Exception
     */
    public function testWithoutError(): void
    {
        $dateTime = new DateTimeImmutable();
        $id = 'id';
        $status = 'status';
        $payload = 'payload';
        $priority = 1;
        $idleTimeoutInSeconds = 50;
        $path = 'path';
        $monitoringData = new MonitoringData(
            $id,
            $status,
            $payload,
            $priority,
            $idleTimeoutInSeconds,
            $dateTime,
            $path
        );


        $violationList = $this->prophesize(ConstraintViolationListInterface::class);
        $violationList->count()->willReturn(0);


        $this->validator->validate($monitoringData)->willReturn($violationList->reveal());

        $violationList->get(Argument::type('int'))->shouldNotBeCalled();

        $this->subject->invoke($monitoringData);
    }
}
