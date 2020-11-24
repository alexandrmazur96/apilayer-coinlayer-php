<?php

namespace Apilayer\Tests\Coinlayer\Responses\Factories;

use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Exceptions\CoinlayerErrorCodes;
use Apilayer\Coinlayer\Exceptions\CoinlayerException;
use Apilayer\Coinlayer\Responses\Factories\TimeframeResponseFactory;
use Apilayer\Tests\TestCase;
use Exception;
use Generator;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class TimeframeResponseFactoryTest extends TestCase
{
    /**
     * @throws CoinlayerException
     */
    public function testCreate(): void
    {
        $timeframeResponseFactory = new TimeframeResponseFactory();
        $timeframeResponse = $timeframeResponseFactory->create(
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
            ]
        );

        self::assertEquals(
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
            $timeframeResponse->toArray()
        );
    }

    /**
     * @throws CoinlayerException
     * @psalm-suppress InvalidArgument
     */
    public function testCreateWrongResponseFormat(): void
    {
        $timeframeResponseFactory = new TimeframeResponseFactory();

        $this->expectException(CoinlayerException::class);
        $this->expectExceptionMessage('Unexpected response from coinlayer API - []');
        $timeframeResponseFactory->create([]);
    }

    /**
     * @dataProvider createFailedResponseData
     * @param int $code
     * @param string $type
     * @throws Exception
     */
    public function testCreateFailedResponse(int $code, string $type): void
    {
        $timeframeResponseFactory = new TimeframeResponseFactory();

        try {
            $timeframeResponseFactory->create(
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
