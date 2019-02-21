<?php

declare(strict_types=1);

namespace App\Service\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ValidatorExceptionNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        // TODO: Implement normalize() method.
    }

    public function supportsNormalization($data, $format = null)
    {
        // TODO: Implement supportsNormalization() method.
    }
}
