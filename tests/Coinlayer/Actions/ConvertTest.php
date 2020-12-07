<?php

namespace Apilayer\Tests\Coinlayer\Actions;

use Apilayer\Coinlayer\Actions\ActionInterface;
use Apilayer\Coinlayer\Actions\Convert;
use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Exceptions\InvalidArgumentException;
use Apilayer\Tests\TestCase;
use DateTimeImmutable;
use Generator;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ConvertTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testGetEndpoint(): void
    {
        $convertAction = new Convert(
            CryptoCurrency::BTC,
            CryptoCurrency::BTS,
            12.125,
            null
        );

        self::assertEquals(ActionInterface::ENDPOINT_CONVERT, $convertAction->getEndpoint());
    }

    /**
     * @dataProvider createObjDataProvider
     * @param string $from
     * @param string $to
     * @param float $amount
     * @param string $expectedExceptionMessage
     * @throws InvalidArgumentException
     */
    public function testCreateObjFailure(
        string $from,
        string $to,
        float $amount,
        string $expectedExceptionMessage
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        new Convert(
            $from,
            $to,
            $amount,
            null
        );
    }

    /**
     * @dataProvider getDataProvider
     * @param DateTimeImmutable|null $date
     * @param array $expectedData
     * @throws InvalidArgumentException
     */
    public function testGetData(?DateTimeImmutable $date, array $expectedData): void
    {
        $convertAction = new Convert(
            CryptoCurrency::BTC,
            CryptoCurrency::BTG,
            12.125,
            $date
        );

        $defaultExpectedData = [
            'from' => CryptoCurrency::BTC,
            'to' => CryptoCurrency::BTG,
            'amount' => 12.125,
        ];

        $expectedData = array_merge($defaultExpectedData, $expectedData);

        $actualData = $convertAction->getData();
        self::assertEquals($expectedData, $actualData);
    }

    public function createObjDataProvider(): Generator
    {
        yield 'wrong-from' => [
            'WRONG',
            CryptoCurrency::BTS,
            12.125,
            '`WRONG` symbol is not available.',
        ];

        yield 'wrong-to' => [
            CryptoCurrency::BTC,
            'WRONG',
            12.125,
            '`WRONG` symbol is not available.',
        ];

        yield 'wrong-amount' => [
            CryptoCurrency::BTC,
            CryptoCurrency::BTG,
            -1.01,
            'Amount [-1.01] should be greater than 0.',
        ];
    }

    public function getDataProvider(): Generator
    {
        yield 'with-optional-date' => [
            new DateTimeImmutable('2020-01-01'),
            [
                'date' => '2020-01-01',
            ],
        ];

        yield 'with-optional-callback' => [
            null,
            [],
        ];

        yield 'with-filled-optional' => [
            new DateTimeImmutable('2020-01-01'),
            [
                'date' => '2020-01-01',
            ],
        ];
    }
}
