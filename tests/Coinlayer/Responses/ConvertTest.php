<?php

namespace Apilayer\Tests\Responses;

use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Responses\Convert;
use Apilayer\Coinlayer\ValueObjects\CoinlayerInfo;
use Apilayer\Coinlayer\ValueObjects\Query;
use Apilayer\Tests\TestCase;
use JsonException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ConvertTest extends TestCase
{
    /**
     * @throws JsonException
     */
    public function testResponseClassData(): void
    {
        $convertResponse = new Convert(
            true,
            'test_terms',
            'test_privacy',
            [
                'from' => CryptoCurrency::BTC,
                'to' => CryptoCurrency::BRIT,
                'amount' => 6,
            ],
            [
                'timestamp' => 1605613527,
                'rate' => 1.1,
            ],
            12.125
        );

        self::assertTrue($convertResponse->isSuccess());
        self::assertEquals('test_terms', $convertResponse->getTerms());
        self::assertEquals('test_privacy', $convertResponse->getPrivacy());
        self::assertEquals(new Query(CryptoCurrency::BTC, CryptoCurrency::BRIT, 6), $convertResponse->getQuery());
        self::assertEquals(new CoinlayerInfo(1605613527, 1.1), $convertResponse->getInfo());
        self::assertEquals(12.125, $convertResponse->getResult());
        self::assertEquals(
            [
                'success' => true,
                'terms' => 'test_terms',
                'privacy' => 'test_privacy',
                'query' => new Query(CryptoCurrency::BTC, CryptoCurrency::BRIT, 6),
                'info' => new CoinlayerInfo(1605613527, 1.1),
                'result' => 12.125,
            ],
            $convertResponse->toArray()
        );
        self::assertEquals(
            json_encode(
                [
                    'success' => true,
                    'terms' => 'test_terms',
                    'privacy' => 'test_privacy',
                    'query' => [
                        'from' => CryptoCurrency::BTC,
                        'to' => CryptoCurrency::BRIT,
                        'amount' => 6,
                    ],
                    'info' => [
                        'timestamp' => 1605613527,
                        'rate' => 1.1,
                    ],
                    'result' => 12.125,
                ],
                JSON_THROW_ON_ERROR
            ),
            json_encode(
                $convertResponse,
                JSON_THROW_ON_ERROR
            )
        );
    }
}
