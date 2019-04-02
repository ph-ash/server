<?php

declare(strict_types=1);

namespace App\Repository;

use App\Document\MonitoringData as MonitoringDataDocument;
use App\Exception\PersistenceLayerException;

interface MonitoringData
{
    public function findAll(): array;

    /**
     * @throws PersistenceLayerException
     */
    public function save(MonitoringDataDocument $monitoringData): void;

    /**
     * @throws PersistenceLayerException
     */
    public function isPathIncludedInBranch(string $path): bool;

    /**
     * @throws PersistenceLayerException
     */
    public function isLeafIncludedInPath(string $path): bool;

    /**
     * @throws PersistenceLayerException
     */
    public function delete(string $id): void;
}

