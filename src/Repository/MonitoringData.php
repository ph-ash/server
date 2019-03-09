<?php

declare(strict_types=1);

namespace App\Repository;

use App\Document\MonitoringData as MonitoringDataDocument;
use App\Exception\PersistenceLayerException;
use Doctrine\ODM\MongoDB\Cursor;

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
    public function findLeafs(string $path): Cursor;

    /**
     * @throws PersistenceLayerException
     */
    public function delete(string $id): void;
}

