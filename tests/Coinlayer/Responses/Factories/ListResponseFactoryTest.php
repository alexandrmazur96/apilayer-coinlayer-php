<?php

namespace Apilayer\Tests\Responses\Factories;

use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Exceptions\CoinlayerErrorCodes;
use Apilayer\Coinlayer\Exceptions\CoinlayerException;
use Apilayer\Coinlayer\Responses\Factories\ListResponseFactory;
use Apilayer\Coinlayer\ValueObjects\CryptoCurrencyInfo;
use Apilayer\Tests\TestCase;
use Exception;
use Generator;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ListResponseFactoryTest extends TestCase
{
    /**
     * @throws CoinlayerException
     */
    public function testCreate(): void
    {
        $listResponseFactory = new ListResponseFactory();
        $listResponse = $listResponseFactory->create(
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
            ]
        );

        self::assertEquals(
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
            $listResponse->toArray()
        );
    }

    /**
     * @throws CoinlayerException
     * @psalm-suppress InvalidArgument
     */
    public function testCreateWrongResponseFormat(): void
    {
        $listResponseFactory = new ListResponseFactory();

        $this->expectException(CoinlayerException::class);
        $this->expectExceptionMessage('Unexpected response from coinlayer API - []');
        $listResponseFactory->create([]);
    }

    /**
     * @dataProvider createFailedResponseData
     * @param int $code
     * @param string $type
     * @throws Exception
     */
    public function testCreateFailedResponse(int $code, string $type): void
    {
        $listResponseFactory = new ListResponseFactory();

        try {
            $listResponseFactory->create(
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
