<?php

declare(strict_types=1);

namespace App\Document;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(repositoryClass="App\Repository\MonitoringDataRepository")
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

    /**
     * @var string|null
     * @MongoDB\Field(type="string")
     */
    private $path;

    public function __construct(
        string $id,
        string $status,
        string $payload,
        int $priority,
        int $idleTimeoutInSeconds,
        DateTimeInterface $date,
        ?string $path
    ) {
        $this->id = $id;
        $this->status = $status;
        $this->payload = $payload;
        $this->priority = $priority;
        $this->idleTimeoutInSeconds = $idleTimeoutInSeconds;
        $this->date = $date;
        $this->path = $path;
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

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }
}
