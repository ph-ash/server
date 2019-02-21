<?php

declare(strict_types=1);

namespace App\Service\Normalizer;

use App\Exception\BulkValidationException;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BulkValidationExceptionNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        //TODO implement -> where to get the BulkValidationException from? is it in the context or in object?
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
        return $data instanceof BulkValidationException;
    }
}
