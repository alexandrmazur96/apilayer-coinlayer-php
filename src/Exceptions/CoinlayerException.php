<?php

namespace Apilayer\Coinlayer\Exceptions;

use Exception;
use Throwable;
use Apilayer\Coinlayer\Exceptions\CoinlayerErrorCodes;

/**
 * @link https://coinlayer.com/documentation#errors
 */
class CoinlayerException extends Exception
{
    private string $type;

    /**
     * @param string $message
     * @param Throwable|null $previous
     * @psalm-param CoinlayerErrorCodes::CODE_*|0|int|string $code
     * @psalm-param CoinlayerErrorCodes::TYPE_*|'internal_lib_error'|string $type
     */
    public function __construct(
        $message = "",
        $code = 0,
        Throwable $previous = null,
        string $type = 'internal_lib_error'
    ) {
        parent::__construct($message, (int)$code, $previous);
        $this->type = $type;
    }

    /**
     * @psalm-return CoinlayerErrorCodes::TYPE_*|'internal_lib_error'|string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
