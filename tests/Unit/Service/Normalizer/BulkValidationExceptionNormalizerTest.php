<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Normalizer;

use App\Dto\MonitoringData;
use App\Exception\BulkValidationException;
use App\Exception\ValidationException;
use App\Service\Normalizer\BulkValidationExceptionNormalizer;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

class BulkValidationExceptionNormalizerTest extends TestCase
{
    /** @var BulkValidationExceptionNormalizer */
    private $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = new BulkValidationExceptionNormalizer();
    }

    /**
     * @throws Exception
     */
    public function testSupportsNormalizationFailure(): void
    {
        $data = new stdClass();
        self::assertFalse($this->subject->supportsNormalization($data));
    }

    /**
     * @throws Exception
     */
    public function testSupportsNormalizationSuccess(): void
    {
        $data = new BulkValidationException([]);
        self::assertTrue($this->subject->supportsNormalization($data));
    }

    /**
     * @throws Exception
     */
    public function testNormalize(): void
    {
        $id = 'id';
        $path = 'path';
        $monitoringData = new MonitoringData(
            $id, 'status', 'payload', 1, 50, new DateTimeImmutable(), $path, null, null
        );
        $valdtionExceptionMessage = 'someErrorMessage';
        $bulkValidationExceptionMessage = 'bulkErrorMessage';

        $validationException = new ValidationException($monitoringData, $valdtionExceptionMessage);
        $bulkValidationException = new BulkValidationException([$validationException], $bulkValidationExceptionMessage);


        $context = [
            'template_data' => [
                'status' => 'error',
                'status_code' => 404
            ]
        ];

        /** @var array $result */
        $result = $this->subject->normalize($bulkValidationException, null, $context);

        self::assertSame('error', $result['status']);
        self::assertArrayHasKey('errors', $result);
        self::assertNotEmpty($result['errors']);
        self::assertSame($path, $result['errors'][0]['path']);
        self::assertSame($id, $result['errors'][0]['id']);
        self::assertSame($valdtionExceptionMessage, $result['errors'][0]['message']);
        self::assertSame($bulkValidationExceptionMessage, $result['message']);
    }

    /**
     * @throws Exception
     */
    public function testWrongObject(): void
    {
        $object = new stdClass();

        $this->expectException(NotNormalizableValueException::class);
        $this->subject->normalize($object);
    }
}
