<?php

declare(strict_types=1);

namespace App\Controller\Rest;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

/**
 * @Route("/api")
 */
class MonitoringController extends FOSRestController
{
    /**
     * @Route("/monitoring/data", methods={"POST"})
     * @SWG\Response(
     *     response=201,
     *     description="Expects the data that will be posted to the dashboard",
     * )
     *
     * @SWG\Tag(name="Monitoring")
     */
    public function postMonitoringData()
    {

    }
}