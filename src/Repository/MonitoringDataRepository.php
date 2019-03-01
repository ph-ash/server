<?php

declare(strict_types=1);

namespace App\Repository;

use App\Document\MonitoringData;
use App\Dto\MonitoringData as MonitoringDataDto;
use App\Exception\PersistenceLayerException;
use App\Repository\MonitoringData as MonitoringDataInterface;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\Cursor;
use Exception;
use LogicException;

class MonitoringDataRepository extends ServiceDocumentRepository implements MonitoringDataInterface
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

    public function save(MonitoringData $monitoringData): void
    {
        try {
            $this->getDocumentManager()->persist($monitoringData);
            $this->getDocumentManager()->flush($monitoringData);
        } catch (Exception $exception) {
            throw new PersistenceLayerException('MonitoringData save failed.', 0, $exception);
        }
    }

    public function findLeafs(string $path): Cursor
    {
        $qb = $this->getDocumentManager()->createQueryBuilder(MonitoringData::class);
        try {
            $qb->field('path')->equals(new \MongoRegex('/' . $path . '..*/'))->limit(1);
            /** @var Cursor $result */
            return $qb->getQuery()->execute();
        } catch (Exception $exception) {
            throw new PersistenceLayerException('Failed to find leafs.', 0, $exception);
        }
    }

    public function delete(string $id): void
    {
        try {
            $qb = $this->createQueryBuilder();
            $qb->remove()
                ->field('id')->equals($id)
                ->getQuery()
                ->execute();
        } catch (Exception $exception) {
            throw new PersistenceLayerException(sprintf('Failed to delete monitoringData with id %s.', $id), 0, $exception);
        }
    }
}
