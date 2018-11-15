<?php

declare(strict_types=1);

namespace App\Dto;

use Swagger\Annotations as SWG;

class MonitoringData
{
    /**
     * @var string
     * @SWG\Property(description="The id of the Data", example="some_id")
     */
    private $id;

    /**
     * @var string
     * @SWG\Property(description="The status of the Data", example="green")
     */
    private $status;

    /**
     * @var string
     * @SWG\Property(description="The payload of the Data", example="This is an Errormessage")
     */
    private $payload;

    /**
     * @var int
     * @SWG\Property(description="The idle timeout of the data, after this time the tile turns yellow", example=60)
     */
    private $idleTimeoutInMinutes;

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

    public function getIdleTimeoutInMinutes(): int
    {
        return $this->idleTimeoutInMinutes;
    }
}