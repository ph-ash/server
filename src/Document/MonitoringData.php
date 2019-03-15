<?php

declare(strict_types=1);

namespace App\Document;

use DateTime;
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
     * @var DateTime
     * @MongoDB\Field(type="date")
     */
    private $statusChangedAt;

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
     * @var DateTime
     * @MongoDB\Field(type="date")
     */
    private $date;

    /**
     * @var string|null
     * @MongoDB\Field(type="string")
     */
    private $path;

    /**
     * @var int|null
     * @MongoDB\Field(type="int")
     */
    private $tileExpansionIntervalCount;

    /**
     * @var string|null
     * @MongoDB\Field(type="string")
     */
    private $tileExpansionGrowthExpression;

    /**
     * @var DateTime
     * @MongoDB\Field(type="date")
     */
    private $lastTileExpansion;

    public function __construct(
        string $id,
        string $status,
        DateTimeInterface $statusChangedAt,
        string $payload,
        int $priority,
        int $idleTimeoutInSeconds,
        DateTimeInterface $date,
        ?string $path,
        ?int $tileExpansionIntervalCount,
        ?string $tileExpansionGrowthExpression
    ) {
        $this->id = $id;
        $this->status = $status;
        $this->statusChangedAt = $statusChangedAt;
        $this->payload = $payload;
        $this->priority = $priority;
        $this->idleTimeoutInSeconds = $idleTimeoutInSeconds;
        $this->date = $date;
        $this->path = $path;
        $this->tileExpansionIntervalCount = $tileExpansionIntervalCount;
        $this->tileExpansionGrowthExpression = $tileExpansionGrowthExpression;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getStatusChangedAt(): DateTimeInterface
    {
        return $this->statusChangedAt ?? $this->date;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
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

    public function getTileExpansionIntervalCount(): ?int
    {
        return $this->tileExpansionIntervalCount;
    }

    public function getTileExpansionGrowthExpression(): ?string
    {
        return $this->tileExpansionGrowthExpression;
    }

    public function getLastTileExpansion(): ?DateTimeInterface
    {
        return $this->lastTileExpansion;
    }

    public function setLastTileExpansion(DateTimeInterface $lastTileExpansion): void
    {
        $this->lastTileExpansion = $lastTileExpansion;
    }
}
