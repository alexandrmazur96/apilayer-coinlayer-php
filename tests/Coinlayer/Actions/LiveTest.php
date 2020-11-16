<?php

namespace Apilayer\Tests\Coinlayer\Actions;

use Apilayer\Coinlayer\Actions\ActionInterface;
use Apilayer\Coinlayer\Actions\Live;
use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Exceptions\InvalidArgumentException;
use Apilayer\Tests\TestCase;
use Generator;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class LiveTest extends TestCase
{
    public function testGetEndpoint(): void
    {
        $liveAction = new Live(
            null,
            null,
            null,
            null
        );

        self::assertEquals(ActionInterface::ENDPOINT_LIVE, $liveAction->getEndpoint());
    }

    /**
     * @dataProvider createObjFailureData
     * @param string|null $target
     * @param string[]|null $symbols
     * @param string $expectedExceptionMessage
     */
    public function testCreateObjFailure(
        ?string $target,
        ?array $symbols,
        string $expectedExceptionMessage
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage($expectedExceptionMessage);

        new Live(
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
        $liveAction = new Live(
            $target,
            $symbols,
            $expand,
            $callback
        );

        self::assertEquals($expectedData, $liveAction->getData());
    }

    public function createObjFailureData(): Generator
    {
        yield 'wrong-target' => [
            'WRONG',
            null,
            'Target currency [WRONG] is not available.',
        ];

        yield 'wrong-symbols-1' => [
            null,
            ['WRONG'],
            'Crypto currencies (symbols) list contains not available values [WRONG]',
        ];

        yield 'wrong-symbols-2' => [
            null,
            ['WRONG', CryptoCurrency::BTC],
            'Crypto currencies (symbols) list contains not available values [WRONG]',
        ];

        yield 'empty-symbols' => [
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
