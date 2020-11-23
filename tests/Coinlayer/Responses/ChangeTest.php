<?php

namespace Apilayer\Tests\Responses;

use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Responses\Change;
use Apilayer\Coinlayer\ValueObjects\ChangeInfo;
use Apilayer\Tests\TestCase;
use DateTimeImmutable;
use Exception;
use JsonException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ChangeTest extends TestCase
{

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function testResponseClassData(): void
    {
        $changeResponse = new Change(
            true,
            'test_terms',
            'test_privacy',
            true,
            '2020-01-01',
            '2020-01-25',
            TargetCurrency::UAH,
            [
                CryptoCurrency::BTC => [
                    'start_rate' => 1.125,
                    'end_rate' => 1.225,
                    'change' => 0.1,
                    'change_pct' => 1.21,
                ],
            ]
        );

        self::assertTrue($changeResponse->isSuccess());
        self::assertEquals('test_terms', $changeResponse->getTerms());
        self::assertEquals('test_privacy', $changeResponse->getPrivacy());
        self::assertTrue($changeResponse->isChange());
        self::assertEquals(new DateTimeImmutable('2020-01-01'), $changeResponse->getStartDate());
        self::assertEquals(new DateTimeImmutable('2020-01-25'), $changeResponse->getEndDate());
        self::assertEquals(TargetCurrency::UAH, $changeResponse->getTarget());
        self::assertEquals(
            [
                CryptoCurrency::BTC => new ChangeInfo(1.125, 1.225, 0.1, 1.21),
            ],
            $changeResponse->getRates()
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

        self::assertEquals(
            json_encode(
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
                JSON_THROW_ON_ERROR
            ),
            json_encode($changeResponse, JSON_THROW_ON_ERROR)
        );
    }
}
