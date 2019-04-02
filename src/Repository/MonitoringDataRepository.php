<?php

declare(strict_types=1);

namespace App\Repository;

use App\Document\MonitoringData;
use App\Enum\MonitoringStatus;
use App\Exception\PersistenceLayerException;
use App\Repository\MonitoringData as MonitoringDataInterface;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\Cursor;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\LockMode;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Exception;
use LogicException;
use MongoRegex;

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

    public function findAllErroneousMonitorings(): array
    {
        return $this->findBy(['status' => MonitoringStatus::ERROR()->getValue()]);
    }

    /**
     * @param mixed $id Identifier.
     * @throws PersistenceLayerException
     */
    public function find($id, $lockMode = LockMode::NONE, $lockVersion = null): ?MonitoringData
    {
        $document = null;
        try {
            /** @var MonitoringData $document */
            $document = parent::find($id, $lockMode, $lockVersion);
        } catch (LockException | MappingException $exception) {
            throw new PersistenceLayerException('MonitoringData find failed.', 0, $exception);
        }
        return $document;
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

    public function findLeafs(string $path): Cursor
    {
        $qb = $this->getDocumentManager()->createQueryBuilder(MonitoringData::class);
        try {
            $qb->field('path')->equals(new MongoRegex('/' . $path . '..*/'))->limit(1);
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
