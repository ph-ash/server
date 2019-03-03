<?php

declare(strict_types=1);

namespace App\Service\Normalizer;

use Exception;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ExceptionNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = []): array
    {
        $templateData = $context['template_data'];

        /** @var FlattenException $exception */
        $exception = $templateData['exception'];
        return [
            'status' => $templateData['status'],
            'message' => $exception->getMessage(),
            'statusCode' => $templateData['status_code']

        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Exception;
    }
}
