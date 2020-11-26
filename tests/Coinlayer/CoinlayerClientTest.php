<?php

namespace Apilayer\Tests\Coinlayer;

use Apilayer\Coinlayer\Actions\ActionInterface;
use Apilayer\Coinlayer\CoinlayerClient;
use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Enums\HttpSchema;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Exceptions\CoinlayerException;
use Apilayer\Coinlayer\Exceptions\InvalidArgumentException;
use Apilayer\Coinlayer\Responses\Factories\ListResponseFactory;
use Apilayer\Coinlayer\Responses\Factories\ResponseFactoryInterface;
use Apilayer\Coinlayer\ValueObjects\ChangeInfo;
use Apilayer\Coinlayer\ValueObjects\CoinlayerInfo;
use Apilayer\Coinlayer\ValueObjects\CryptoCurrencyInfo;
use Apilayer\Coinlayer\Responses\Factories\ChangeResponseFactory;
use Apilayer\Coinlayer\Responses\Factories\ConvertResponseFactory;
use Apilayer\Coinlayer\Responses\Factories\HistoricalResponseFactory;
use Apilayer\Coinlayer\Responses\Factories\LiveResponseFactory;
use Apilayer\Coinlayer\Responses\Factories\TimeframeResponseFactory;
use Apilayer\Coinlayer\ValueObjects\Query;
use Apilayer\Tests\TestCase;
use Apilayer\Tests\Utils\PsrProphecyMocker;
use Exception;
use Generator;
use JsonException;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class CoinlayerClientTest extends TestCase
{
    use ProphecyTrait;
    use PsrProphecyMocker;

    /** @psalm-var 'test_api_key' */
    private const TEST_API_KEY = 'test_api_key';

    /**
     * @psalm-suppress TooManyTemplateParams
     */
    public function testClientCreatingThrowsException(): void
    {
        /** @var StreamInterface $streamMock */
        $streamMock = $this->mockStreamInterface('test')
            ->reveal();

        /** @var RequestInterface $requestMock */
        $requestMock = $this->mockRequestInterface('GET', '/test', $streamMock, [])
            ->reveal();

        /** @var ResponseInterface $responseMock */
        $responseMock = $this->mockResponseInterface(200, $streamMock, [])
            ->reveal();

        /** @var RequestFactoryInterface $requestFactoryMock */
        $requestFactoryMock = $this->mockRequestFactoryInterface($requestMock)
            ->reveal();

        /** @var ClientInterface $clientMock */
        $clientMock = $this->mockClientInterface($requestMock, $responseMock)
            ->reveal();

        $this->expectException(InvalidArgumentException::class);
        /** @psalm-suppress InvalidArgument */
        $this->getCoinlayerClient(
            $clientMock,
            $requestFactoryMock,
            'wrong-scheme'
        );
    }

    /**
     * @throws CoinlayerException|InvalidArgumentException
     * @psalm-suppress TooManyArguments
     */
    public function testClientPerformThrowsException(): void
    {
        /** @var StreamInterface $streamMock */
        $streamMock = $this->mockStreamInterface('[1,2,3]')
            ->reveal();

        /** @var RequestInterface $requestMock */
        $requestMock = $this->mockRequestInterface('GET', '/test', $streamMock, [])
            ->reveal();

        /** @var RequestFactoryInterface $requestFactoryMock */
        $requestFactoryMock = $this->mockRequestFactoryInterface($requestMock)
            ->reveal();

        $clientProphecyObj = $this->prophesize(ClientInterface::class);

        /** @var Exception $exception */
        $exception = $this->mockClientInterfaceException()->reveal();
        $clientProphecyObj
            ->sendRequest(Argument::cetera())
            ->willThrow($exception);

        /** @var ClientInterface $clientMock */
        $clientMock = $clientProphecyObj->reveal();

        $coinlayerClient = $this->getCoinlayerClient(
            $clientMock,
            $requestFactoryMock
        );

        $apiActionProphecyObj = $this->prophesize(ActionInterface::class);
        $apiActionProphecyObj->getEndpoint()->willReturn(ActionInterface::ENDPOINT_LIVE);
        $apiActionProphecyObj->getData()->willReturn(['a' => 'b']);

        /** @var ActionInterface $apiActionMock */
        $apiActionMock = $apiActionProphecyObj->reveal();

        $this->expectException(CoinlayerException::class);

        $coinlayerClient->perform($apiActionMock);
    }

    /**
     * @throws InvalidArgumentException|CoinlayerException
     * @psalm-suppress TooManyArguments
     * @psalm-suppress TooManyTemplateParams
     */
    public function testClientInvalidEndpointThrowsException(): void
    {
        /** @var StreamInterface $streamMock */
        $streamMock = $this->mockStreamInterface('["a", "b"]')
            ->reveal();

        /** @var RequestInterface $requestMock */
        $requestMock = $this->mockRequestInterface('GET', '/test', $streamMock, [])
            ->reveal();

        /** @var ResponseInterface $responseMock */
        $responseMock = $this->mockResponseInterface(200, $streamMock, [])
            ->reveal();

        /** @var RequestFactoryInterface $requestFactoryMock */
        $requestFactoryMock = $this->mockRequestFactoryInterface($requestMock)
            ->reveal();

        /** @var ClientInterface $clientMock */
        $clientMock = $this->mockClientInterface($requestMock, $responseMock)
            ->reveal();

        $coinlayerClient = $this->getCoinlayerClient(
            $clientMock,
            $requestFactoryMock,
        );

        $apiActionProphecyObj = $this->prophesize(ActionInterface::class);
        $apiActionProphecyObj->getEndpoint()->willReturn('wrong-endpoint');
        $apiActionProphecyObj->getData()->willReturn(['a' => 'b']);
        $apiActionProphecyObj->getResponseFactory()->willReturn(new ListResponseFactory());

        /** @var ActionInterface $apiActionMock */
        $apiActionMock = $apiActionProphecyObj->reveal();

        $this->expectException(CoinlayerException::class);
        $this->expectErrorMessage('Unexpected response from coinlayer API');

        $coinlayerClient->perform($apiActionMock);
    }

    /**
     * @dataProvider clientSuccessfulResponseData
     * @param array $responseData
     * @param array $expectedData
     * @throws JsonException
     *
     * @psalm-param class-string<ResponseFactoryInterface> $responseFactoryClass
     * @psalm-param ActionInterface::* $endpoint
     *
     * @psalm-suppress TooManyTemplateParams
     * @psalm-suppress TooManyArguments
     */
    public function testClientSuccessfulRequest(
        string $endpoint,
        array $responseData,
        string $responseFactoryClass,
        array $expectedData
    ): void {
        /** @var StreamInterface $streamMock */
        $streamMock = $this->mockStreamInterface(json_encode($responseData, JSON_THROW_ON_ERROR))
            ->reveal();

        /** @var RequestInterface $requestMock */
        $requestMock = $this->mockRequestInterface('GET', $endpoint, null, [])
            ->reveal();

        /** @var ResponseInterface $responseMock */
        $responseMock = $this->mockResponseInterface(200, $streamMock, [])
            ->reveal();

        /** @var RequestFactoryInterface $requestFactoryMock */
        $requestFactoryMock = $this->mockRequestFactoryInterface($requestMock)
            ->reveal();

        /** @var ClientInterface $clientMock */
        $clientMock = $this->mockClientInterface($requestMock, $responseMock)
            ->reveal();

        $coinlayerClient = $this->getCoinlayerClient(
            $clientMock,
            $requestFactoryMock
        );

        $apiActionProphecyObj = $this->prophesize(ActionInterface::class);
        $apiActionProphecyObj->getEndpoint()->willReturn($endpoint);
        $apiActionProphecyObj->getData()->willReturn([]);
        $apiActionProphecyObj->getResponseFactory()->willReturn(new $responseFactoryClass());

        /** @var ActionInterface $apiActionMock */
        $apiActionMock = $apiActionProphecyObj->reveal();

        $result = $coinlayerClient->perform($apiActionMock);

        self::assertEquals($expectedData, $result->toArray());
    }

    /**
     * @return Generator
     * @psalm-suppress TooManyArguments
     */
    public function clientSuccessfulResponseData(): Generator
    {
        yield 'list-endpoint' => [
            ActionInterface::ENDPOINT_LIST,
            [
                'success' => true,
                'crypto' => [
                    CryptoCurrency::BTC => [
                        'symbol' => 'BTC',
                        'name' => 'Bitcoin',
                        'name_full' => 'Bitcoin',
                        'max_supply' => 12222,
                        'icon_url' => 'path/to/url',
                    ],
                ],
                'fiat' => [
                    TargetCurrency::UAH => 'Hryvna',
                    TargetCurrency::USD => 'Bucks',
                ],
            ],
            ListResponseFactory::class,
            [
                'success' => true,
                'crypto' => [
                    CryptoCurrency::BTC => new CryptoCurrencyInfo(
                        'BTC',
                        'Bitcoin',
                        'Bitcoin',
                        12222,
                        'path/to/url'
                    ),
                ],
                'fiat' => [
                    TargetCurrency::UAH => 'Hryvna',
                    TargetCurrency::USD => 'Bucks',
                ],
            ],
        ];
        yield 'live-endpoint' => [
            ActionInterface::ENDPOINT_LIVE,
            [
                'success' => true,
                'terms' => 'test_terms',
                'privacy' => 'test_privacy',
                'timestamp' => 1605613527,
                'target' => TargetCurrency::UAH,
                'rates' => [
                    CryptoCurrency::BTC => 1.125,
                    CryptoCurrency::BTG => 0.12,
                ],
            ],
            LiveResponseFactory::class,
            [
                'success' => true,
                'terms' => 'test_terms',
                'privacy' => 'test_privacy',
                'timestamp' => 1605613527,
                'target' => TargetCurrency::UAH,
                'rates' => [
                    CryptoCurrency::BTC => 1.125,
                    CryptoCurrency::BTG => 0.12,
                ],
            ],
        ];
        yield 'change-endpoint' => [
            ActionInterface::ENDPOINT_CHANGE,
            [
                'success' => true,
                'terms' => 'test_terms',
                'privacy' => 'test_privacy',
                'change' => true,
                'start_date' => '2020-01-01',
                'end_date' => '2020-01-25',
                'target' => TargetCurrency::UAH,
                'rates' => [
                    CryptoCurrency::BTC => [
                        'start_rate' => 1.125,
                        'end_rate' => 1.225,
                        'change' => 0.1,
                        'change_pct' => 1.21,
                    ],
                ],
            ],
            ChangeResponseFactory::class,
            [
                'success' => true,
                'terms' => 'test_terms',
                'privacy' => 'test_privacy',
                'change' => true,
                'start_date' => '2020-01-01',
                'end_date' => '2020-01-25',
                'target' => TargetCurrency::UAH,
                'rates' => [
                    CryptoCurrency::BTC => new ChangeInfo(1.125, 1.225, 0.1, 1.21),
                ],
            ],
        ];
        yield 'convert-endpoint' => [
            ActionInterface::ENDPOINT_CONVERT,
            [
                'success' => true,
                'terms' => 'test_terms',
                'privacy' => 'test_privacy',
                'query' => [
                    'from' => CryptoCurrency::BTC,
                    'to' => CryptoCurrency::BRIT,
                    'amount' => 6,
                ],
                'info' => [
                    'timestamp' => 1605613527,
                    'rate' => 1.1,
                ],
                'result' => 12.125,
            ],
            ConvertResponseFactory::class,
            [
                'success' => true,
                'terms' => 'test_terms',
                'privacy' => 'test_privacy',
                'query' => new Query(CryptoCurrency::BTC, CryptoCurrency::BRIT, 6),
                'info' => new CoinlayerInfo(1605613527, 1.1),
                'result' => 12.125,
            ],
        ];
        yield 'timeframe-endpoint' => [
            ActionInterface::ENDPOINT_TIMEFRAME,
            [
                'success' => true,
                'terms' => 'test_terms',
                'privacy' => 'test_privacy',
                'timeframe' => true,
                'start_date' => '2020-01-01',
                'end_date' => '2020-01-02',
                'target' => TargetCurrency::UAH,
                'rates' => [
                    '2020-01-01' => [
                        CryptoCurrency::BTC => 1.125,
                        CryptoCurrency::BQ => 0.94,
                    ],
                    '2020-01-02' => [
                        CryptoCurrency::BTC => 1.225,
                        CryptoCurrency::BQ => 1.01,
                    ],
                ],
            ],
            TimeframeResponseFactory::class,
            [
                'success' => true,
                'terms' => 'test_terms',
                'privacy' => 'test_privacy',
                'timeframe' => true,
                'start_date' => '2020-01-01',
                'end_date' => '2020-01-02',
                'target' => TargetCurrency::UAH,
                'rates' => [
                    '2020-01-01' => [
                        CryptoCurrency::BTC => 1.125,
                        CryptoCurrency::BQ => 0.94,
                    ],
                    '2020-01-02' => [
                        CryptoCurrency::BTC => 1.225,
                        CryptoCurrency::BQ => 1.01,
                    ],
                ],
            ],
        ];
        yield 'historical-endpoint' => [
            '2020-01-01',
            [
                'success' => true,
                'terms' => 'test_terms',
                'privacy' => 'test_privacy',
                'timestamp' => 1605613527,
                'target' => TargetCurrency::UAH,
                'historical' => true,
                'date' => '2020-01-01',
                'rates' => [
                    CryptoCurrency::SIX_ELEVEN => 0.389165,
                    CryptoCurrency::ABC => 59.99,
                    CryptoCurrency::ACP => 0.014931,
                ],
            ],
            HistoricalResponseFactory::class,
            [
                'success' => true,
                'terms' => 'test_terms',
                'privacy' => 'test_privacy',
                'timestamp' => 1605613527,
                'target' => TargetCurrency::UAH,
                'historical' => true,
                'date' => '2020-01-01',
                'rates' => [
                    CryptoCurrency::SIX_ELEVEN => 0.389165,
                    CryptoCurrency::ABC => 59.99,
                    CryptoCurrency::ACP => 0.014931,
                ],
            ],
        ];
    }

    /**
     * @param ClientInterface $httpClient
     * @param RequestFactoryInterface $requestFactory
     * @return CoinlayerClient
     * @throws InvalidArgumentException
     *
     * @psalm-param HttpSchema::* $scheme
     */
    private function getCoinlayerClient(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        $scheme = HttpSchema::SCHEMA_HTTP
    ): CoinlayerClient {
        return new CoinlayerClient(
            $httpClient,
            $requestFactory,
            self::TEST_API_KEY,
            $scheme
        );
    }
}
