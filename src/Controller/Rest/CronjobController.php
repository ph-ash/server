<?php

declare(strict_types=1);

namespace App\Controller\Rest;

use App\Service\GrowTilesDispatcher;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/internal")
 */
class CronjobController extends AbstractController
{
    /**
     * @Route("/grow-tiles", methods={"POST"})
     * @throws InvalidArgumentException
     */
    public function growTiles(GrowTilesDispatcher $growTilesDispatcher)
    {
        $growTilesDispatcher->invoke();
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
