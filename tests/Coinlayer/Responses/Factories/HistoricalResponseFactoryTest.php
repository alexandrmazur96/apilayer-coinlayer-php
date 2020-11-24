<?php

namespace Apilayer\Tests\Responses\Factories;

use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Exceptions\CoinlayerErrorCodes;
use Apilayer\Coinlayer\Exceptions\CoinlayerException;
use Apilayer\Coinlayer\Responses\Factories\HistoricalResponseFactory;
use Apilayer\Tests\TestCase;
use Exception;
use Generator;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class HistoricalResponseFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $historicalResponseFactory = new HistoricalResponseFactory();
        $historicalResponse = $historicalResponseFactory->create(
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
            ]
        );

        self::assertEquals(
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
            $historicalResponse->toArray()
        );
    }

    /**
     * @throws CoinlayerException
     * @psalm-suppress InvalidArgument
     */
    public function testCreateWrongResponseFormat(): void
    {
        $historicalResponseFactory = new HistoricalResponseFactory();

        $this->expectException(CoinlayerException::class);
        $this->expectExceptionMessage('Unexpected response from coinlayer API - []');
        $historicalResponseFactory->create([]);
    }

    /**
     * @dataProvider createFailedResponseData
     * @param int $code
     * @param string $type
     * @throws Exception
     */
    public function testCreateFailedResponse(int $code, string $type): void
    {
        $historicalResponseFactory = new HistoricalResponseFactory();

        try {
            $historicalResponseFactory->create(
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
