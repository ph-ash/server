<?php

declare(strict_types=1);

namespace App\Document;

use DateTimeImmutable;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document
 */
class MonitoringData
{
    /**
     * @var string
     * @MongoDB\Id(strategy="NONE", type="string")
     */
    private $id;

    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    private $status;

    /**
     * @var string
     * @MongoDB\Field(type="string")
     */
    private $payload;

    /**
     * @var int
     * @MongoDB\Field(type="int")
     */
    private $priority;

    /**
     * @var int
     * @MongoDB\Field(type="int")
     */
    private $idleTimeoutInSeconds;

    /**
     * @var DateTimeImmutable
     * @MongoDB\Field(type="date")
     */
    private $date;

    public function __construct(string $id, string $status, string $payload, int $priority, int $idleTimeoutInSeconds, DateTimeImmutable $date)
    {
        $this->id = $id;
        $this->status = $status;
        $this->payload = $payload;
        $this->priority = $priority;
        $this->idleTimeoutInSeconds = $idleTimeoutInSeconds;
        $this->date = $date;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getIdleTimeoutInSeconds(): int
    {
        return $this->idleTimeoutInSeconds;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }
}
