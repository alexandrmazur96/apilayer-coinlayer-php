<?php

namespace Apilayer\Tests\Coinlayer\Actions;

use Apilayer\Coinlayer\Actions\ActionInterface;
use Apilayer\Coinlayer\Actions\Timeframe;
use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Exceptions\InvalidArgumentException;
use Apilayer\Tests\TestCase;
use DateTimeImmutable;
use Generator;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class TimeframeTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     */
    public function testGetEndpoint(): void
    {
        $timeframeAction = new Timeframe(
            new DateTimeImmutable('2020-01-01'),
            new DateTimeImmutable('2020-01-25'),
            null,
            null,
            null,
            null
        );

        self::assertEquals(ActionInterface::ENDPOINT_TIMEFRAME, $timeframeAction->getEndpoint());
    }

    /**
     * @dataProvider createObjFailureData
     * @param DateTimeImmutable $startDate
     * @param DateTimeImmutable $endDate
     * @param string|null $target
     * @param string[]|null $symbols
     * @param string $expectedExceptionMessage
     * @throws InvalidArgumentException
     */
    public function testCreateObjFailure(
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        ?string $target,
        ?array $symbols,
        string $expectedExceptionMessage
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        new Timeframe(
            $startDate,
            $endDate,
            $target,
            $symbols,
            null,
            null
        );
    }

    /**
     * @dataProvider getDataProvider
     * @param string|null $target
     * @param string[]|null $symbols
     * @param bool|null $expand
     * @param string|null $callback
     * @param array $expectedData
     * @throws InvalidArgumentException
     */
    public function testGetData(
        ?string $target,
        ?array $symbols,
        ?bool $expand,
        ?string $callback,
        array $expectedData
    ): void {
        $timeframeAction = new Timeframe(
            new DateTimeImmutable('2020-01-01'),
            new DateTimeImmutable('2020-01-25'),
            $target,
            $symbols,
            $expand,
            $callback
        );

        $defaultExpectedData = [
            'start_date' => '2020-01-01',
            'end_date' => '2020-01-25',
        ];

        $expectedData = array_merge($defaultExpectedData, $expectedData);

        self::assertEquals($expectedData, $timeframeAction->getData());
    }

    public function createObjFailureData(): Generator
    {
        yield 'wrong-dates' => [
            new DateTimeImmutable('2020-01-25'),
            new DateTimeImmutable('2020-01-01'),
            null,
            null,
            'Start date [2020-01-25] should be lower than or equal to end date [2020-01-01].',
        ];

        yield 'wrong-target' => [
            new DateTimeImmutable('2020-01-01'),
            new DateTimeImmutable('2020-01-25'),
            'WRONG',
            null,
            'Target currency [WRONG] is not available.',
        ];

        yield 'wrong-symbols-1' => [
            new DateTimeImmutable('2020-01-01'),
            new DateTimeImmutable('2020-01-25'),
            null,
            ['WRONG'],
            'Crypto currencies (symbols) list contains not available values [WRONG]',
        ];

        yield 'wrong-symbols-2' => [
            new DateTimeImmutable('2020-01-01'),
            new DateTimeImmutable('2020-01-25'),
            null,
            [CryptoCurrency::BTC, 'WRONG'],
            'Crypto currencies (symbols) list contains not available values [WRONG]',
        ];

        yield 'empty-symbols' => [
            new DateTimeImmutable('2020-01-01'),
            new DateTimeImmutable('2020-01-25'),
            null,
            [],
            'If symbols passed they should not be empty',
        ];
    }

    public function getDataProvider(): Generator
    {
        yield 'with-optional-target' => [
            TargetCurrency::UAH,
            null,
            null,
            null,
            [
                'target' => TargetCurrency::UAH,
            ],
        ];

        yield 'with-optional-symbols' => [
            null,
            [CryptoCurrency::BTC],
            null,
            null,
            [
                'symbols' => [CryptoCurrency::BTC],
            ],
        ];

        yield 'with-optional-expand-1' => [
            null,
            null,
            true,
            null,
            [
                'expand' => 1,
            ],
        ];

        yield 'with-optional-expand-2' => [
            null,
            null,
            false,
            null,
            [
                'expand' => 0,
            ],
        ];

        yield 'with-optional-callback' => [
            null,
            null,
            null,
            'some_callback',
            [
                'callback' => 'some_callback',
            ],
        ];

        yield 'with-filled-optional' => [
            TargetCurrency::UAH,
            [CryptoCurrency::BTC],
            true,
            'some_callback',
            [
                'target' => TargetCurrency::UAH,
                'symbols' => [CryptoCurrency::BTC],
                'expand' => 1,
                'callback' => 'some_callback',
            ],
        ];
    }
}
