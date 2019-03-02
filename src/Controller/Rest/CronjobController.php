<?php

declare(strict_types=1);

namespace App\Controller\Rest;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/internal")
 */
class CronjobController extends AbstractController
{
    /**
     * @Route("/grow-tiles", methods={"POST"})
     */
    public function growTiles()
    {
        return new JsonResponse('rumsmuelf');
    }
}
