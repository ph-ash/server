<?php

declare(strict_types=1);

namespace App\Repository;

use App\Document\MonitoringData;
use App\Exception\PersistenceLayerException;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Exception;
use LogicException;
use MongoRegex;

class MonitoringDataRepository extends ServiceDocumentRepository
{
    /**
     * @throws LogicException
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MonitoringData::class);
    }

    public function findAll(): array
    {
        $this->getDocumentManager()->clear(MonitoringData::class);
        return parent::findAll();
    }

    /**
     * @throws PersistenceLayerException
     */
    public function save(MonitoringData $monitoringData): void
    {
        try {
            $this->getDocumentManager()->persist($monitoringData);
            $this->getDocumentManager()->flush($monitoringData);
        } catch (Exception $exception) {
            throw new PersistenceLayerException('MonitoringData save failed.', 0, $exception);
        }
    }

    /**
     * @throws PersistenceLayerException
     */
    public function findLeafs(string $path)
    {
        $qb = $this->getDocumentManager()->createQueryBuilder('m');
        try {
            $qb->field('path')->equals(new MongoRegex('/^' . $path . '\..*/'));
            return $qb->getQuery()->execute();

        } catch (Exception $exception) {
            throw new PersistenceLayerException('Failed to find leafs.', 0, $exception);
        }
    }
}
