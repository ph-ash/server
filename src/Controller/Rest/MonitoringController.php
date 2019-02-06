<?php

declare(strict_types=1);

namespace App\Controller\Rest;

use App\Dto\MonitoringData;
use App\Exception\PersistenceLayerException;
use App\Service\IncomingMonitoringDataDispatcher;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class MonitoringController extends FOSRestController
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
     *     response=422,
     *     description="When you try to push monitoringdata into a branch"
     * )
     *
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     required=true,
     *     allowEmptyValue=false,
     *     @SWG\Schema(ref=@Model(type=MonitoringData::class))
     *
     * )
     *
     * @SWG\Tag(name="Monitoring")
     *
     * @ParamConverter("monitoringData", converter="fos_rest.request_body")
     *
     * @throws PersistenceLayerException
     */
    public function postMonitoringData(
        IncomingMonitoringDataDispatcher $incomingMonitoringDataDispatcher,
        MonitoringData $monitoringData
    ): JsonResponse {
        //TODO add tests
        $incomingMonitoringDataDispatcher->invoke($monitoringData);
        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}
