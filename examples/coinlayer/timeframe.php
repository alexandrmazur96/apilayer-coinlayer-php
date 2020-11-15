<?php

use Apilayer\Coinlayer\Actions\Timeframe as TimeframeAction;
use Apilayer\Coinlayer\CoinlayerClient;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Exceptions\CoinlayerException;
use Apilayer\Coinlayer\Responses\Timeframe as TimeframeResponse;
use Apilayer\Coinlayer\Exceptions\InvalidArgumentException;
use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Enums\HttpSchema;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;

require_once __DIR__ . '/../../vendor/autoload.php';

$coinlayerApiKey = '<your API key>';

$psr18HttpClient = Psr18ClientDiscovery::find();
$psr17RequestFactory = Psr17FactoryDiscovery::findRequestFactory();

try {
    $coinlayerClient = new CoinlayerClient(
        $psr18HttpClient,
        $psr17RequestFactory,
        $coinlayerApiKey,
        HttpSchema::SCHEMA_HTTP
    );
} catch (InvalidArgumentException $e) {
    /**
     * This exception may be caused by passing wrong HTTP schema.
     * Use {@see HttpSchema} constants for available schemas values to avoid this exception.
     */
    echo 'Failed to create coinlayer client - ', $e->getMessage(), PHP_EOL;
    die(1);
}

$startDate = new DateTimeImmutable('2020-01-01');
$endDate = new DateTimeImmutable('2020-01-25');
$targetCurrency = TargetCurrency::UAH;
$cryptoCurrencies = [CryptoCurrency::BTC, CryptoCurrency::CLD];
$expand = false;
$callback = 'some_callback';

try {
    $timeframeAction = new TimeframeAction(
        $startDate,
        $endDate,
        $targetCurrency,
        $cryptoCurrencies,
        $expand,
        $callback
    );
} catch (InvalidArgumentException $e) {
    /**
     * This exception may be caused by passing wrong parameters to the constructor.
     * See {@see InvalidArgumentException::getMessage()} for more details.
     */
    echo 'Failed to create action - ', $e->getMessage(), PHP_EOL;
    die(1);
}

try {
    /** @var TimeframeResponse $timeframeResponse */
    $timeframeResponse = $coinlayerClient->perform($timeframeAction);
} catch (CoinlayerException $e) {
    /**
     * This exception may be caused by the different reasons:
     * - HTTP client throw an error (exception would be caught and re-thrown
     *          by CoinlayerException with same parameters);
     * - Unable to decode response JSON (Unlikely);
     * - API respond without 'success' key - in this case check exception
     *          message about what exactly API respond;
     * - API respond with {success:false}. Check exception message and code.
     */
    echo 'Failed to perform API request - ', $e->getMessage(), PHP_EOL;
    die(1);
}

/* Always true here. */
$success = $timeframeResponse->isSuccess();

/*
 * Links to terms & privacy.
 */
$terms = $timeframeResponse->getTerms();
$privacy = $timeframeResponse->getPrivacy();

/* Indicates that request was performed to /timeframe endpoint. */
$timeframeResponse->isTimeframe();

/* Methods below just reflect the same request parameters. */
$targetCurrency = $timeframeResponse->getTarget();
$startDate = $timeframeResponse->getStartDate();
$endDate = $timeframeResponse->getEndDate();

/* A JSON object containing crypto data for each day in the requested period. */
$rates = $timeframeResponse->getRates();

/** @var array<string,float> $rateMap */
foreach ($rates as $date => $rateMap) {
    echo 'For date ', $date, PHP_EOL;
    foreach ($rateMap as $cryptoCurrency => $rateInfo) {
        echo 'Crypto currency: ', $cryptoCurrency, ' rate ', $rateInfo, PHP_EOL;
    }
}

/* All response classes implements JsonSerializable interface. */
$jsonRepresentation = json_encode($timeframeResponse, JSON_THROW_ON_ERROR);
