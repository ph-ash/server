<?php

declare(strict_types=1);

namespace App\Controller\Rest;

use App\Dto\BulkMonitoringData;
use App\Dto\MonitoringData;
use App\Service\BulkIncomingMonitoringDataDispatcher;
use App\Service\DeleteMonitoringDataDispatcher;
use App\Service\IncomingMonitoringDataDispatcher;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class MonitoringController extends AbstractFOSRestController
{
    /**
     * @Route("/monitoring/data", methods={"POST"})
     * @SWG\Response(
     *     response=201,
     *     description="Expects the data that will be posted to the dashboard"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="When authentication header is missing or wrong credentials are given"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="When you try to push monitoringdata into a branch"
     * )
     *
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     required=true,
     *     allowEmptyValue=false,
     *     @SWG\Schema(ref=@Model(type=MonitoringData::class))
     * )
     *
     * @SWG\Tag(name="Single-Monitoring")
     *
     * @ParamConverter("monitoringData", converter="fos_rest.request_body")
     *
     * @throws Exception
     */
    public function postMonitoringData(
        IncomingMonitoringDataDispatcher $incomingMonitoringDataDispatcher,
        MonitoringData $monitoringData
    ): JsonResponse {
        $incomingMonitoringDataDispatcher->invoke($monitoringData);
        return new JsonResponse(null, Response::HTTP_CREATED);
    }

    /**
     * @Route("/monitoring/{id}", methods={"DELETE"})
     * @SWG\Response(
     *     response=204,
     *     description="When the data has been deleted successfully"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="When authentication header is missing or wrong credentials are given"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="When you try to delete a branch which has leafs"
     * )
     *
     * @SWG\Tag(name="Single-Monitoring")
     *
     * @throws Exception
     */
    public function deleteMonitoringData(
        DeleteMonitoringDataDispatcher $deleteMonitoringDataDispatcher,
        string $id
    ): JsonResponse {
        $deleteMonitoringDataDispatcher->invoke($id);
        return new JsonResponse();
    }

    /**
     * @Route("/monitoring/data/bulk", methods={"POST"})
     * @SWG\Response(
     *     response=201,
     *     description="Expects the data that will be posted to the dashboard"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="When authentication header is missing or wrong credentials are given"
     * )
     *
     * @SWG\Response(
     *     response=400,
     *     description="When you try to push monitoringdata into a branch"
     * )
     *
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     required=true,
     *     allowEmptyValue=false,
     *     @SWG\Schema(ref=@Model(type=BulkMonitoringData::class))
     * )
     *
     * @SWG\Tag(name="Bulk-Monitoring")
     *
     * @ParamConverter("bulkMonitoringData", converter="fos_rest.request_body")
     *
     * @throws Exception
     */
    public function postBulkMonitoringData(
        BulkIncomingMonitoringDataDispatcher $bulkIncomingMonitoringDataDispatcher,
        BulkMonitoringData $bulkMonitoringData
    ): JsonResponse {
        $bulkIncomingMonitoringDataDispatcher->invoke($bulkMonitoringData);
        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}
