<?php

namespace Apilayer\Tests\Responses;

use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Responses\Live as LiveResponse;
use Apilayer\Tests\TestCase;
use DateTimeImmutable;
use JsonException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class LiveTest extends TestCase
{
    /**
     * @throws JsonException
     */
    public function testResponseClassData(): void
    {
        $live = new LiveResponse(
            true,
            'test_terms',
            'test_privacy',
            1605613527,
            TargetCurrency::UAH,
            [
                CryptoCurrency::BTC => 1.125,
                CryptoCurrency::BTG => 0.12,
            ]
        );

        self::assertTrue($live->isSuccess());
        self::assertEquals('test_terms', $live->getTerms());
        self::assertEquals('test_privacy', $live->getPrivacy());
        self::assertEquals(
            (new DateTimeImmutable())->setTimestamp(1605613527),
            $live->getTimestamp()
        );
        self::assertEquals(TargetCurrency::UAH, $live->getTarget());
        self::assertEquals(
            [
                CryptoCurrency::BTC => 1.125,
                CryptoCurrency::BTG => 0.12,
            ],
            $live->getRates()
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
            $live->toArray()
        );
        self::assertEquals(
            json_encode(
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
                JSON_THROW_ON_ERROR
            ),
            json_encode($live, JSON_THROW_ON_ERROR)
        );
    }
}
