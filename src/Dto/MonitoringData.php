<?php

declare(strict_types=1);

namespace App\Dto;

use DateTimeInterface;
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
     * @SWG\Property(
     *     description="The priority of the Data, different numbers show different sizes on the board",
     *     example=1
     * )
     */
    private $priority;

    /**
     * @var int
     * @SWG\Property(description="The idle timeout of the data, after this time the tile turns yellow", example=60)
     */
    private $idleTimeoutInSeconds;

    /**
     * @var DateTimeInterface
     * @SWG\Property(description="The time when this monitoring Data was generated")
     */
    private $date;

    /**
     * @var string|null
     * @SWG\Property(
     *      description="The path in the in the monitoring treemap, format: rootName.branchName.leafName.
     *      If no path is set, root is used",
     *      example="monitoring.team_phash.database"
     * )
     */
    private $path;

    /**
     * @var int
     * @SWG\Property(
     *     description="The number of idle timeout intervals after which a red tile grows in priority",
     *     example=1
     * )
     */
    private $tileExpansionIntervalCount;

    /**
     * @var string
     * @SWG\Property(description="The expression of priority growth", example="+ 1")
     */
    private $tileExpansionGrowthExpression;

    public function __construct(
        string $id,
        string $status,
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
        $this->payload = $payload;
        $this->priority = $priority;
        $this->idleTimeoutInSeconds = $idleTimeoutInSeconds;
        $this->date = $date;
        $this->path = $path;
        $this->tileExpansionIntervalCount = $tileExpansionIntervalCount ?? 1;
        $this->tileExpansionGrowthExpression = $tileExpansionGrowthExpression ?? ' + 1';
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

    public function getTileExpansionIntervalCount(): int
    {
        return $this->tileExpansionIntervalCount;
    }

    public function getTileExpansionGrowthExpression(): string
    {
        return $this->tileExpansionGrowthExpression;
    }
}
