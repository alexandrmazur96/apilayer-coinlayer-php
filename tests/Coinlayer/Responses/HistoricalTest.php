<?php

namespace Apilayer\Tests\Responses;

use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Responses\Historical;
use Apilayer\Tests\TestCase;
use DateTimeImmutable;
use Exception;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class HistoricalTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testResponseClassData(): void
    {
        $historicalResponse = new Historical(
            true,
            'test_terms',
            'test_privacy',
            1605613527,
            TargetCurrency::UAH,
            true,
            '2020-01-01',
            [
                CryptoCurrency::SIX_ELEVEN => 0.389165,
                CryptoCurrency::ABC => 59.99,
                CryptoCurrency::ACP => 0.014931,
            ]
        );

        self::assertTrue($historicalResponse->isSuccess());
        self::assertEquals('test_terms', $historicalResponse->getTerms());
        self::assertEquals('test_privacy', $historicalResponse->getPrivacy());
        self::assertEquals(TargetCurrency::UAH, $historicalResponse->getTarget());
        self::assertEquals((new DateTimeImmutable())->setTimestamp(1605613527), $historicalResponse->getTimestamp());
        self::assertEquals(new DateTimeImmutable('2020-01-01'), $historicalResponse->getDate());
        self::assertEquals(
            [
                CryptoCurrency::SIX_ELEVEN => 0.389165,
                CryptoCurrency::ABC => 59.99,
                CryptoCurrency::ACP => 0.014931,
            ],
            $historicalResponse->getRates()
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

        self::assertEquals(
            json_encode(
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
                ,
                JSON_THROW_ON_ERROR
            ),
            json_encode($historicalResponse, JSON_THROW_ON_ERROR)
        );
    }
}
