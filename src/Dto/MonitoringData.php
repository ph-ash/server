<?php

declare(strict_types=1);

namespace App\Dto;

use DateTimeImmutable;
use Swagger\Annotations as SWG;

class MonitoringData
{
    /**
     * @var string
     * @SWG\Property(description="The id of the Data", example="monitoringData Id 1")
     */
    private $id;

    /**
     * @var string
     * @SWG\Property(description="The status of the Data", example="ok")
     */
    private $status;

    /**
     * @var string
     * @SWG\Property(description="The payload of the Data", example="This is an Errormessage")
     */
    private $payload;

    /**
     * @var int
     * @SWG\Property(description="The priority of the Data, different numbers show different sizes on the board", example=1)
     */
    private $priority;

    /**
     * @var int
     * @SWG\Property(description="The idle timeout of the data, after this time the tile turns yellow", example=60)
     */
    private $idleTimeoutInSeconds;

    /**
     * @var DateTimeImmutable
     * @SWG\Property(description="The time when this monitoring Data was generated")
     */
    private $date;

    public function __construct(
        string $id,
        string $status,
        string $payload,
        int $priority,
        int $idleTimeoutInSeconds,
        DateTimeImmutable $date
    ) {
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

    public function getIdleTimeoutInSeconds(): int
    {
        return $this->idleTimeoutInSeconds;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }
}
