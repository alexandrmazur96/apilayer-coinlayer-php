<?php

namespace Apilayer\Tests\Coinlayer\Responses\Factories;

use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Exceptions\CoinlayerErrorCodes;
use Apilayer\Coinlayer\Exceptions\CoinlayerException;
use Apilayer\Coinlayer\Responses\Factories\ChangeResponseFactory;
use Apilayer\Coinlayer\ValueObjects\ChangeInfo;
use Apilayer\Tests\TestCase;
use Exception;
use Generator;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ChangeResponseFactoryTest extends TestCase
{
    /**
     * @throws CoinlayerException
     */
    public function testCreate(): void
    {
        $changeResponseFactory = new ChangeResponseFactory();
        $changeResponse = $changeResponseFactory->create(
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
            ]
        );

        self::assertEquals(
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
            $changeResponse->toArray()
        );
    }

    /**
     * @throws CoinlayerException
     * @psalm-suppress InvalidArgument
     */
    public function testCreateWrongResponseFormat(): void
    {
        $changeResponseFactory = new ChangeResponseFactory();

        $this->expectException(CoinlayerException::class);
        $this->expectExceptionMessage('Unexpected response from coinlayer API - []');
        $changeResponseFactory->create([]);
    }

    /**
     * @dataProvider createFailedResponseData
     * @param int $code
     * @param string $type
     * @throws Exception
     */
    public function testCreateFailedResponse(int $code, string $type): void
    {
        $changeResponseFactory = new ChangeResponseFactory();

        try {
            $changeResponseFactory->create(
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
