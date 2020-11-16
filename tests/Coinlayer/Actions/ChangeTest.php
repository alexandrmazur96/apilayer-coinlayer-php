<?php

namespace Apilayer\Tests\Coinlayer\Actions;

use Apilayer\Coinlayer\Actions\ActionInterface;
use Apilayer\Coinlayer\Actions\Change;
use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Exceptions\InvalidArgumentException;
use Apilayer\Tests\TestCase;
use DateTimeImmutable;
use Generator;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ChangeTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testGetEndpoint(): void
    {
        $changeAction = new Change(
            new DateTimeImmutable('2020-01-01'),
            new DateTimeImmutable('2020-01-25'),
            TargetCurrency::UAH,
            [CryptoCurrency::BTC],
            null
        );
        self::assertEquals(ActionInterface::ENDPOINT_CHANGE, $changeAction->getEndpoint());
    }

    /**
     * @dataProvider createObjDataProvider
     * @param DateTimeImmutable $startDate
     * @param DateTimeImmutable $endDate
     * @param string $target
     * @param array $symbols
     * @param string $expectedExceptionMessage
     *
     * @throws InvalidArgumentException
     */
    public function testCreateObjFailure(
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        string $target,
        array $symbols,
        string $expectedExceptionMessage
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        new Change(
            $startDate,
            $endDate,
            $target,
            $symbols,
            null
        );
    }

    /**
     * @dataProvider getDataProvider
     * @param DateTimeImmutable $startDate
     * @param DateTimeImmutable $endDate
     * @param string|null $target
     * @param array|null $symbols
     * @param string|null $callback
     * @param array $expectedData
     * @throws InvalidArgumentException
     */
    public function testGetData(
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        ?string $target,
        ?array $symbols,
        ?string $callback,
        array $expectedData
    ): void {
        $changeAction = new Change(
            $startDate,
            $endDate,
            $target,
            $symbols,
            $callback
        );

        $actualData = $changeAction->getData();
        self::assertEquals($expectedData, $actualData);
    }

    public function getDataProvider(): Generator
    {
        yield 'without-optional' => [
            new DateTimeImmutable('2020-01-01'),
            new DateTimeImmutable('2020-01-25'),
            null,
            null,
            null,
            [
                'start_date' => '2020-01-01',
                'end_date' => '2020-01-25',
            ],
        ];

        yield 'with-optional-target' => [
            new DateTimeImmutable('2020-01-01'),
            new DateTimeImmutable('2020-01-25'),
            TargetCurrency::UAH,
            null,
            null,
            [
                'start_date' => '2020-01-01',
                'end_date' => '2020-01-25',
                'target' => TargetCurrency::UAH,
            ],
        ];

        yield 'with-optional-symbols' => [
            new DateTimeImmutable('2020-01-01'),
            new DateTimeImmutable('2020-01-25'),
            null,
            [CryptoCurrency::BTC],
            null,
            [
                'start_date' => '2020-01-01',
                'end_date' => '2020-01-25',
                'symbols' => [CryptoCurrency::BTC],
            ],
        ];

        yield 'with-optional-callback' => [
            new DateTimeImmutable('2020-01-01'),
            new DateTimeImmutable('2020-01-25'),
            null,
            null,
            'some_callback',
            [
                'start_date' => '2020-01-01',
                'end_date' => '2020-01-25',
                'callback' => 'some_callback',
            ],
        ];

        yield 'with-filled-optional' => [
            new DateTimeImmutable('2020-01-01'),
            new DateTimeImmutable('2020-01-25'),
            TargetCurrency::UAH,
            [CryptoCurrency::BTC],
            'some_callback',
            [
                'start_date' => '2020-01-01',
                'end_date' => '2020-01-25',
                'target' => TargetCurrency::UAH,
                'symbols' => [CryptoCurrency::BTC],
                'callback' => 'some_callback'
            ],
        ];
    }

    public function createObjDataProvider(): Generator
    {
        yield 'wrong-dates' => [
            new DateTimeImmutable('2020-01-20'),
            new DateTimeImmutable('2020-01-10'),
            TargetCurrency::UAH,
            [CryptoCurrency::BTC],
            'Start date [2020-01-20] should be lower than or equal to end date [2020-01-10].',
        ];

        yield 'wrong-target-currency' => [
            new DateTimeImmutable('2020-01-01'),
            new DateTimeImmutable('2020-01-25'),
            'WRONG',
            [CryptoCurrency::BTC],
            'Target currency [WRONG] is not available.',
        ];

        yield 'wrong-crypto-currencies-1' => [
            new DateTimeImmutable('2020-01-01'),
            new DateTimeImmutable('2020-01-25'),
            TargetCurrency::UAH,
            ['WRONG'],
            'Crypto currencies (symbols) list contains not available values [WRONG]',
        ];

        yield 'wrong-crypto-currencies-2' => [
            new DateTimeImmutable('2020-01-01'),
            new DateTimeImmutable('2020-01-25'),
            TargetCurrency::UAH,
            [CryptoCurrency::BTC, 'WRONG'],
            'Crypto currencies (symbols) list contains not available values [WRONG]',
        ];

        yield 'empty-crypto-currencies' => [
            new DateTimeImmutable('2020-01-01'),
            new DateTimeImmutable('2020-01-25'),
            TargetCurrency::UAH,
            [],
            'If symbols passed they should not be empty',
        ];
    }
}
