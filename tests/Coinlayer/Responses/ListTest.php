<?php

namespace Apilayer\Tests\Responses;

use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Responses\ListResponse;
use Apilayer\Coinlayer\ValueObjects\CryptoCurrencyInfo;
use Apilayer\Tests\TestCase;
use JsonException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ListTest extends TestCase
{
    /**
     * @throws JsonException
     */
    public function testResponseClassData(): void
    {
        $list = new ListResponse(
            true,
            [
                CryptoCurrency::BTC => [
                    'symbol' => 'BTC',
                    'name' => 'Bitcoin',
                    'name_full' => 'Bitcoin',
                    'max_supply' => 12222,
                    'icon_url' => 'path/to/url',
                ],
            ],
            [
                TargetCurrency::UAH => 'Hryvna',
                TargetCurrency::USD => 'Bucks',
            ]
        );

        self::assertTrue($list->isSuccess());
        self::assertEquals('', $list->getTerms());
        self::assertEquals('', $list->getPrivacy());
        self::assertEquals(
            [
                CryptoCurrency::BTC => new CryptoCurrencyInfo(
                    'BTC',
                    'Bitcoin',
                    'Bitcoin',
                    12222,
                    'path/to/url'
                ),
            ],
            $list->getCrypto()
        );
        self::assertEquals(
            [
                TargetCurrency::UAH => 'Hryvna',
                TargetCurrency::USD => 'Bucks',
            ],
            $list->getFiat()
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
            $list->toArray()
        );
        self::assertEquals(
            json_encode(
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
                JSON_THROW_ON_ERROR
            ),
            json_encode($list, JSON_THROW_ON_ERROR)
        );
    }
}
