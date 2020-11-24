<?php

namespace Apilayer\Tests\Responses\Factories;

use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Exceptions\CoinlayerErrorCodes;
use Apilayer\Coinlayer\Exceptions\CoinlayerException;
use Apilayer\Coinlayer\Responses\Factories\ConvertResponseFactory;
use Apilayer\Coinlayer\ValueObjects\CoinlayerInfo;
use Apilayer\Coinlayer\ValueObjects\Query;
use Apilayer\Tests\TestCase;
use Exception;
use Generator;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ConvertResponseFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $convertResponseFactory = new ConvertResponseFactory();
        $convertResponse = $convertResponseFactory->create(
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
            ]
        );

        self::assertEquals(
            [
                'success' => true,
                'terms' => 'test_terms',
                'privacy' => 'test_privacy',
                'query' => new Query(CryptoCurrency::BTC, CryptoCurrency::BRIT, 6),
                'info' => new CoinlayerInfo(1605613527, 1.1),
                'result' => 12.125,
            ],
            $convertResponse->toArray()
        );
    }

    /**
     * @throws CoinlayerException
     * @psalm-suppress InvalidArgument
     */
    public function testCreateWrongResponseFormat(): void
    {
        $convertResponseFactory = new ConvertResponseFactory();

        $this->expectException(CoinlayerException::class);
        $this->expectExceptionMessage('Unexpected response from coinlayer API - []');
        $convertResponseFactory->create([]);
    }

    /**
     * @dataProvider createFailedResponseData
     * @param int $code
     * @param string $type
     * @throws Exception
     */
    public function testCreateFailedResponse(int $code, string $type): void
    {
        $convertResponseFactory = new ConvertResponseFactory();

        try {
            $convertResponseFactory->create(
                [
                    'success' => false,
                    'error' => [
                        'info' => 'test error',
                        'code' => $code,
                        'type' => $type,
                    ],
                ]
            );
        } catch (CoinlayerException $e) {
            self::assertEquals('test error', $e->getMessage());
            self::assertEquals($code, $e->getCode());
            self::assertEquals($type, $e->getType());

            return;
        }

        self::fail('No exception occurred while it expected');
    }

    public function createFailedResponseData(): Generator
    {
        foreach (CoinlayerErrorCodes::MAP_CODE_TO_TYPE as $code => $type) {
            yield 'code: ' . $code . ' type: ' . $type => [
                $code,
                $type,
            ];
        }
    }
}
