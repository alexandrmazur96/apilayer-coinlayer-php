<?php

namespace Apilayer\Tests\Coinlayer\Responses\Factories;

use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Exceptions\CoinlayerErrorCodes;
use Apilayer\Coinlayer\Exceptions\CoinlayerException;
use Apilayer\Coinlayer\Responses\Factories\LiveResponseFactory;
use Apilayer\Tests\TestCase;
use Exception;
use Generator;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class LiveResponseFactoryTest extends TestCase
{
    /**
     * @throws CoinlayerException
     */
    public function testCreate(): void
    {
        $liveResponseFactory = new LiveResponseFactory();
        $liveResponse = $liveResponseFactory->create(
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
            ]
        );

        self::assertEquals(
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
            $liveResponse->toArray()
        );
    }

    /**
     * @throws CoinlayerException
     * @psalm-suppress InvalidArgument
     */
    public function testCreateWrongResponseFormat(): void
    {
        $liveResponseFactory = new LiveResponseFactory();

        $this->expectException(CoinlayerException::class);
        $this->expectExceptionMessage('Unexpected response from coinlayer API - []');
        $liveResponseFactory->create([]);
    }

    /**
     * @dataProvider createFailedResponseData
     * @param int $code
     * @param string $type
     * @throws Exception
     */
    public function testCreateFailedResponse(int $code, string $type): void
    {
        $liveResponseFactory = new LiveResponseFactory();

        try {
            $liveResponseFactory->create(
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
