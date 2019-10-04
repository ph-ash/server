<?php

declare(strict_types=1);

namespace App\Dto\Outgoing;

use DateTimeInterface;

class MonitoringData
{
    private $id;
    private $status;
    private $payload;
    private $priority;
    private $idleTimeoutInSeconds;
    private $date;
    private $path;
    private $tileExpansionIntervalCount;
    private $tileExpansionGrowthExpression;
    private $statusChangedAt;

    public function __construct(
        string $id,
        string $status,
        string $payload,
        int $priority,
        int $idleTimeoutInSeconds,
        DateTimeInterface $date,
        ?string $path,
        int $tileExpansionIntervalCount = null,
        string $tileExpansionGrowthExpression = null,
        DateTimeInterface $statusChangedAt = null
    ) {
        $this->id = $id;
        $this->status = $status;
        $this->payload = $payload;
        $this->priority = $priority;
        $this->idleTimeoutInSeconds = $idleTimeoutInSeconds;
        $this->date = $date;
        $this->path = $path;
        $this->tileExpansionIntervalCount = $tileExpansionIntervalCount;
        $this->tileExpansionGrowthExpression = $tileExpansionGrowthExpression;
        $this->statusChangedAt = $statusChangedAt;
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

    public function getIdleTimeoutInSeconds(): int
    {
        return $this->idleTimeoutInSeconds;
    }

    public function getPriority(): int
    {
        return $this->priority;
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

    public function getStatusChangedAt(): ?DateTimeInterface
    {
        return $this->statusChangedAt;
    }
}
