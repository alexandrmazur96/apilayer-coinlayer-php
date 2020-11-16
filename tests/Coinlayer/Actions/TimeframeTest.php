<?php

namespace Apilayer\Tests\Coinlayer\Actions;

use Apilayer\Coinlayer\Actions\ActionInterface;
use Apilayer\Coinlayer\Actions\Timeframe;
use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Exceptions\InvalidArgumentException;
use Apilayer\Tests\TestCase;
use DateTimeImmutable;
use Generator;

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
     * @param array|null $symbols
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
}
