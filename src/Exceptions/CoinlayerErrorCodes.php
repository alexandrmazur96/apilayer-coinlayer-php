<?php

namespace Apilayer\Coinlayer\Exceptions;

/**
 * Represents error code to error type mapping.
 * @link https://coinlayer.com/documentation#errors
 */
abstract class CoinlayerErrorCodes
{
    public const CODE_101 = 101;
    public const CODE_102 = 102;
    public const CODE_103 = 103;
    public const CODE_104 = 104;
    public const CODE_105 = 105;
    public const CODE_106 = 106;
    public const CODE_201 = 201;
    public const CODE_202 = 202;
    public const CODE_302 = 302;
    public const CODE_403 = 403;
    public const CODE_404 = 404;
    public const CODE_501 = 501;
    public const CODE_502 = 502;
    public const CODE_503 = 503;
    public const CODE_504 = 504;
    public const CODE_505 = 505;

    /** The requested resource does not exist. */
    public const TYPE_404_NOT_FOUND = '404_not_found';
    /** No API Key was specified or an invalid API Key was specified. */
    public const TYPE_INVALID_ACCESS_KEY = 'invalid_access_key';
    /** The requested API endpoint does not exist. */
    public const TYPE_INVALID_API_FUNCTION = 'invalid_api_function';
    /** The maximum allowed API amount of monthly API requests has been reached. */
    public const TYPE_USAGE_LIMIT_REACHED = 'usage_limit_reached';
    /** The current subscription plan does not support this API endpoint. */
    public const TYPE_FUNCTION_ACCESS_RESTRICTED = 'function_access_restricted';
    /** The current request did not return any results. */
    public const TYPE_NO_RATES_AVAILABLE = 'no_rates_available';
    /** The account this API request is coming from is inactive. */
    public const TYPE_INACTIVE_USER = 'inactive_user';
    /** An invalid target currency has been entered. */
    public const TYPE_INVALID_TARGET_CURRENCY = 'invalid_target_currency';
    /** One or more invalid symbols have been specified. */
    public const TYPE_INVALID_CURRENCY_SYMBOLS = 'invalid_currency_symbols';
    /** An invalid date has been specified. [historical, convert] */
    public const TYPE_INVALID_DATE = 'invalid_date';
    /** No or an invalid amount has been specified. [convert] */
    public const TYPE_INVALID_CONVERSATION_AMOUNT = 'invalid_conversion_amount';
    /** No or an invalid timeframe has been specified. [timeframe] */
    public const TYPE_NO_TIMEFRAME_SUPPLIED = 'no_timeframe_supplied';
    /** No or an invalid "start_date" has been specified. [timeframe, change] */
    public const TYPE_INVALID_START_DATE = 'invalid_start_date';
    /** No or an invalid "end_date" has been specified. [timeframe, change] */
    public const TYPE_INVALID_END_DATE = 'invalid_end_date';
    /** An invalid timeframe has been specified. [timeframe, change] */
    public const TYPE_INVALID_TIME_FRAME = 'invalid_time_frame';
    /** The specified timeframe is too long, exceeding 365 days. [timeframe, change] */
    public const TYPE_TIME_FRAME_TOO_LONG = 'time_frame_too_long';

    /**
     * @psalm-var array<self::CODE_*,self::TYPE_*>
     */
    public const MAP_CODE_TO_TYPE = [
        self::CODE_101 => self::TYPE_INVALID_ACCESS_KEY,
        self::CODE_102 => self::TYPE_INACTIVE_USER,
        self::CODE_103 => self::TYPE_INVALID_API_FUNCTION,
        self::CODE_104 => self::TYPE_USAGE_LIMIT_REACHED,
        self::CODE_105 => self::TYPE_FUNCTION_ACCESS_RESTRICTED,
        self::CODE_106 => self::TYPE_NO_RATES_AVAILABLE,
        self::CODE_201 => self::TYPE_INVALID_TARGET_CURRENCY,
        self::CODE_202 => self::TYPE_INVALID_CURRENCY_SYMBOLS,
        self::CODE_302 => self::TYPE_INVALID_DATE,
        self::CODE_403 => self::TYPE_INVALID_CONVERSATION_AMOUNT,
        self::CODE_404 => self::TYPE_404_NOT_FOUND,
        self::CODE_501 => self::TYPE_NO_TIMEFRAME_SUPPLIED,
        self::CODE_502 => self::TYPE_INVALID_START_DATE,
        self::CODE_503 => self::TYPE_INVALID_END_DATE,
        self::CODE_504 => self::TYPE_INVALID_TIME_FRAME,
        self::CODE_505 => self::TYPE_TIME_FRAME_TOO_LONG,
    ];

    /**
     * @psalm-var array<self::TYPE_*,self::CODE_*>
     */
    public const MAP_TYPE_TO_CODE = [
        self::TYPE_INVALID_ACCESS_KEY => self::CODE_101,
        self::TYPE_INACTIVE_USER => self::CODE_102,
        self::TYPE_INVALID_API_FUNCTION => self::CODE_103,
        self::TYPE_USAGE_LIMIT_REACHED => self::CODE_104,
        self::TYPE_FUNCTION_ACCESS_RESTRICTED => self::CODE_105,
        self::TYPE_NO_RATES_AVAILABLE => self::CODE_106,
        self::TYPE_INVALID_TARGET_CURRENCY => self::CODE_201,
        self::TYPE_INVALID_CURRENCY_SYMBOLS => self::CODE_202,
        self::TYPE_INVALID_DATE => self::CODE_302,
        self::TYPE_INVALID_CONVERSATION_AMOUNT => self::CODE_403,
        self::TYPE_404_NOT_FOUND => self::CODE_404,
        self::TYPE_NO_TIMEFRAME_SUPPLIED => self::CODE_501,
        self::TYPE_INVALID_START_DATE => self::CODE_502,
        self::TYPE_INVALID_END_DATE => self::CODE_503,
        self::TYPE_INVALID_TIME_FRAME => self::CODE_504,
        self::TYPE_TIME_FRAME_TOO_LONG => self::CODE_505,
    ];
}
