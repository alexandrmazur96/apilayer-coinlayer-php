<?php

namespace Apilayer\Coinlayer\Actions;

use Apilayer\Coinlayer\Responses\Factories\ResponseFactoryInterface;

interface ActionInterface
{
    public const ENDPOINT_LIVE = 'live';
    public const ENDPOINT_HISTORICAL = 'historical';
    public const ENDPOINT_CONVERT = 'convert';
    public const ENDPOINT_TIMEFRAME = 'timeframe';
    public const ENDPOINT_CHANGE = 'change';
    public const ENDPOINT_LIST = 'list';

    /**
     * @psalm-return self::ENDPOINT_*|string
     */
    public function getEndpoint(): string;

    /** @psalm-return array<string,mixed> */
    public function getData(): array;

    /** @return ResponseFactoryInterface */
    public function getResponseFactory(): ResponseFactoryInterface;
}
