<?php

declare(strict_types=1);

namespace App\Service\Normalizer;

use App\Exception\BulkValidationException;
use App\Exception\ValidationException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BulkValidationExceptionNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        if (!$object instanceof BulkValidationException) {
            throw new NotNormalizableValueException(
                sprintf('Object %s is not a BulkValidationException', \get_class($object))
            );
        }

        $templateData = $context['template_data'];
        $errors = [];

        /** @var ValidationException $validatorException */
        foreach ($object->getValidationExceptions() as $validatorException) {
            $errors[] = [
                'message' => $validatorException->getMessage(),
                'path' => $validatorException->getPath(),
                'id' => $validatorException->getId()
            ];
        }

        return [
            'status' => $templateData['status'],
            'message' => $object->getMessage(),
            'errors' => $errors,
            'statusCode' => $templateData['status_code']
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof BulkValidationException;
    }
}
