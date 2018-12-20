<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Normalizer;

use App\Service\Normalizer\ExceptionNormalizer;
use Exception;
use PHPUnit\Framework\TestCase;
use stdClass;

class ExceptionNormalizerTest extends TestCase
{
    /** @var ExceptionNormalizer */
    private $subject;

    public function setUp()
    {
        parent::setUp();

        $this->subject = new ExceptionNormalizer();
    }

    /**
     * @throws Exception
     */
    public function testSupportsNormalizationSuccess(): void
    {
        $exception = new Exception();
        self::assertTrue($this->subject->supportsNormalization($exception));
    }

    /**
     * @throws Exception
     */
    public function testSupportsNormalizationFail(): void
    {
        $exception = new stdClass();
        self::assertFalse($this->subject->supportsNormalization($exception));
    }

    /**
     * @throws Exception
     */
    public function testNormalize(): void
    {

        $context = [
            'template_data' => [
                'exception' => new Exception('this is an exceptionmessage'),
                'status' => 'someStatus',
                'status_code' => 'someStatusCode'
            ]
        ];

        $result = $this->subject->normalize(null, null, $context);

        self::assertArrayHasKey('status', $result);
        self::assertArrayHasKey('message', $result);
        self::assertArrayHasKey('statusCode', $result);
        self::assertSame('this is an exceptionmessage', $result['message']);
        self::assertSame('someStatus', $result['status']);
        self::assertSame('someStatusCode', $result['statusCode']);
    }
}
