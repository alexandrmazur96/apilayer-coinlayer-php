<?php

namespace Apilayer\Tests\Responses;

use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Responses\Timeframe;
use Apilayer\Tests\TestCase;
use DateTimeImmutable;
use Exception;
use JsonException;

class TimeframeTest extends TestCase
{
    /**
     * @throws JsonException
     * @throws Exception
     */
    public function testResponseClassData(): void
    {
        $timeframe = new Timeframe(
            true,
            'test_terms',
            'test_privacy',
            true,
            '2020-01-01',
            '2020-01-02',
            TargetCurrency::UAH,
            [
                '2020-01-01' => [
                    CryptoCurrency::BTC => 1.125,
                    CryptoCurrency::BQ => 0.94,
                ],
                '2020-01-02' => [
                    CryptoCurrency::BTC => 1.225,
                    CryptoCurrency::BQ => 1.01,
                ],
            ]
        );

        self::assertTrue($timeframe->isSuccess());
        self::assertEquals('test_terms', $timeframe->getTerms());
        self::assertEquals('test_privacy', $timeframe->getPrivacy());
        self::assertTrue($timeframe->isTimeframe());
        self::assertEquals(new DateTimeImmutable('2020-01-01'), $timeframe->getStartDate());
        self::assertEquals(new DateTimeImmutable('2020-01-02'), $timeframe->getEndDate());
        self::assertEquals(TargetCurrency::UAH, $timeframe->getTarget());
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
            $timeframe->toArray()
        );
        self::assertEquals(
            json_encode(
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
                JSON_THROW_ON_ERROR
            ),
            json_encode($timeframe, JSON_THROW_ON_ERROR)
        );
    }
}
