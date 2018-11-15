<?php

declare(strict_types=1);

namespace App\Controller\Rest;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use App\Dto\MonitoringData;

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
     */
    public function postMonitoringData(): JsonResponse
    {
        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}
