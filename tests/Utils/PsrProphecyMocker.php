<?php

/** @noinspection PhpUndefinedMethodInspection */

namespace Apilayer\Tests\Utils;

use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, RequestInterface, ResponseInterface, StreamInterface};

trait PsrProphecyMocker
{
    public function mockClientInterfaceException(): ObjectProphecy
    {
        return $this->prophesize(ClientExceptionInterface::class);
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ObjectProphecy
     * @psalm-return ObjectProphecy<ClientInterface>
     * @psalm-suppress TooManyArguments
     * @psalm-suppress TooManyTemplateParams
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function mockClientInterface(RequestInterface $request, ResponseInterface $response): ObjectProphecy
    {
        $clientInterfaceProphecyObj = $this->prophesize(ClientInterface::class);

        $clientInterfaceProphecyObj
            ->sendRequest($request)
            ->willReturn($response);

        return $clientInterfaceProphecyObj;
    }

    /**
     * @param RequestInterface $request
     * @return ObjectProphecy
     * @psalm-suppress TooManyArguments
     */
    public function mockRequestFactoryInterface(RequestInterface $request): ObjectProphecy
    {
        $requestFactoryProphecyObj = $this->prophesize(RequestFactoryInterface::class);

        $requestFactoryProphecyObj
            ->createRequest(Argument::cetera())
            ->willReturn($request);

        return $requestFactoryProphecyObj;
    }

    /**
     * @param int $statusCode
     * @param StreamInterface|null $body
     * @param array $headers
     * @return ObjectProphecy
     * @psalm-suppress TooManyArguments
     */
    public function mockResponseInterface(
        int $statusCode,
        StreamInterface $body = null,
        array $headers = []
    ): ObjectProphecy {
        $responseProphecyObj = $this->prophesize(ResponseInterface::class);
        $responseProphecyObj
            ->getStatusCode()
            ->willReturn($statusCode);

        $responseProphecyObj
            ->withStatus(Argument::cetera())
            ->willReturn($responseProphecyObj);

        $responseProphecyObj
            ->getReasonPhrase()
            ->willReturn('reason phrase');

        $responseProphecyObj = $this->setUpMessageInterfaceMock($responseProphecyObj, $body, $headers);

        return $responseProphecyObj;
    }

    /**
     * @param string $body
     * @return ObjectProphecy
     * @psalm-suppress TooManyArguments
     */
    public function mockStreamInterface(string $body): ObjectProphecy
    {
        $streamProphecyObj = $this->prophesize(StreamInterface::class);
        $streamProphecyObj
            ->__toString()
            ->willReturn($body);

        $streamProphecyObj->close();
        $streamProphecyObj->detach();

        $streamProphecyObj
            ->getSize()
            ->willReturn(strlen($body));

        $streamProphecyObj
            ->tell()
            ->willReturn(0);

        $streamProphecyObj
            ->eof()
            ->willReturn(false);

        $streamProphecyObj
            ->isSeekable()
            ->willReturn(true);

        $streamProphecyObj->seek(Argument::cetera());
        $streamProphecyObj->rewind();

        $streamProphecyObj
            ->isWritable()
            ->willReturn(false);

        $streamProphecyObj
            ->write(Argument::cetera())
            ->willReturn(0);

        $streamProphecyObj
            ->isReadable()
            ->willReturn(true);

        $streamProphecyObj
            ->read(Argument::cetera())
            ->willReturn($body);

        $streamProphecyObj
            ->getContents()
            ->willReturn($body);

        $streamProphecyObj
            ->getMetadata(Argument::cetera())
            ->willReturn('meta');

        return $streamProphecyObj;
    }

    /**
     * @param string $httpMethod
     * @param string $uri
     * @param StreamInterface|null $body
     * @param array $headers
     * @return ObjectProphecy
     * @psalm-suppress TooManyArguments
     */
    public function mockRequestInterface(
        string $httpMethod,
        string $uri,
        StreamInterface $body = null,
        array $headers = []
    ): ObjectProphecy {
        $requestProphecyObj = $this->prophesize(RequestInterface::class);
        $requestProphecyObj
            ->getRequestTarget()
            ->willReturn('/');

        $requestProphecyObj
            ->withRequestTarget(Argument::cetera())
            ->willReturn($requestProphecyObj);

        $requestProphecyObj
            ->getMethod()
            ->willReturn($httpMethod);

        $requestProphecyObj
            ->withMethod($httpMethod)
            ->willReturn($requestProphecyObj);

        $requestProphecyObj
            ->getUri()
            ->willReturn($uri);

        $requestProphecyObj
            ->withUri($uri)
            ->willReturn($requestProphecyObj);

        $requestProphecyObj = $this->setUpMessageInterfaceMock($requestProphecyObj, $body, $headers);

        return $requestProphecyObj;
    }

    /**
     * @param ObjectProphecy $messageProphecyObj
     * @param StreamInterface|null $body
     * @param array $headers
     * @return ObjectProphecy
     * @psalm-suppress TooManyArguments
     */
    private function setUpMessageInterfaceMock(
        ObjectProphecy $messageProphecyObj,
        StreamInterface $body = null,
        array $headers = []
    ): ObjectProphecy {
        $messageProphecyObj
            ->getProtocolVersion()
            ->willReturn('1.1');

        $messageProphecyObj
            ->withProtocolVersion(Argument::cetera())
            ->willReturn($messageProphecyObj);

        $messageProphecyObj
            ->getHeaders()
            ->willReturn($headers);

        $messageProphecyObj
            ->hasHeader(Argument::cetera())
            ->willReturn(true);

        $messageProphecyObj
            ->getHeader(Argument::cetera())
            ->willReturn([]);

        $messageProphecyObj
            ->getHeaderLine(Argument::cetera())
            ->willReturn('');

        $messageProphecyObj
            ->withHeader(Argument::cetera())
            ->willReturn($messageProphecyObj);

        $messageProphecyObj
            ->withAddedHeader(Argument::cetera())
            ->willReturn($messageProphecyObj);

        $messageProphecyObj
            ->withoutHeader(Argument::cetera())
            ->willReturn($messageProphecyObj);

        if ($body === null) {
            $messageProphecyObj
                ->withBody(Argument::cetera())
                ->willReturn($messageProphecyObj);

            $messageProphecyObj
                ->getBody()
                ->willReturn(null);
        } else {
            $messageProphecyObj
                ->withBody($body)
                ->willReturn($messageProphecyObj);

            $messageProphecyObj
                ->getBody()
                ->willReturn($body);
        }

        return $messageProphecyObj;
    }
}
