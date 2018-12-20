<?php

declare(strict_types=1);

namespace App\Tests\Unit\Security;

use App\Security\TokenAuthenticator;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use UnexpectedValueException;

class TokenAuthenticatorTest extends TestCase
{
    /** @var TokenAuthenticator */
    private $subject;

    public function setUp()
    {
        parent::setUp();

        $this->subject = new TokenAuthenticator();
    }

    /**
     * @throws Exception
     */
    public function testStart(): void
    {
        $request = new Request();

        $result = $this->subject->start($request);
        self::assertSame(
            '{"message":"Authorization header with \u0027Bearer TOKEN\u0027 Required"}',
            $result->getContent()
        );
        self::assertSame(Response::HTTP_UNAUTHORIZED, $result->getStatusCode());
        self::assertArrayHasKey('www-authenticate', $result->headers->all());
    }


    /**
     * @dataProvider getSupportedAuths
     */
    public function testSupports(bool $expected, ?string $authorization): void
    {
        $request = new Request([], [], [], [], [], ['HTTP_Authorization' => $authorization]);

        $result = $this->subject->supports($request);

        self::assertSame($expected, $result);
    }

    public function getSupportedAuths(): array
    {
        return [
            [false, null],
            [false, 'someToken'],
            [false, 'BearerSomeToken'],
            [true, 'Bearer sometoken'],
            [false, 'someToken Bearer'],
            [false, 'someToken Bearer '],
        ];
    }

    /**
     * @throws UnexpectedValueException
     */
    public function testGetCredentials(): void
    {
        $request = new Request([], [], [], [], [], ['HTTP_Authorization' => 'Bearer someToken']);

        $result = $this->subject->getCredentials($request);

        self::assertSame('someToken', $result);
    }

    /**
     * @throws Exception
     */
    public function testGetUser(): void
    {
        $user = new User('someToken', null);
        $userProvider = $this->prophesize(UserProviderInterface::class);

        $userProvider->loadUserByUsername('someToken')->willReturn($user);

        $result = $this->subject->getUser('someToken', $userProvider->reveal());

        self::assertSame($user, $result);
    }

    /**
     * @throws Exception
     */
    public function testCheckCredentials(): void
    {
        $user = new User('someToken', null);
        $result = $this->subject->checkCredentials('someToken', $user);

        self::assertTrue($result);
    }

    /**
     * @throws Exception
     */
    public function testOnAuthenticationFailure(): void
    {
        $request = new Request();
        $exception = new AuthenticationException();

        $result = $this->subject->onAuthenticationFailure($request, $exception);
        self::assertSame("[\"wrong credentials\"]", $result->getContent());
        self::assertSame(Response::HTTP_UNAUTHORIZED, $result->getStatusCode());
        self::assertArrayHasKey('www-authenticate', $result->headers->all());
    }

    /**
     * @throws Exception
     */
    public function testSupportsRememberMe()
    {
        self::assertFalse($this->subject->supportsRememberMe());
    }


}
