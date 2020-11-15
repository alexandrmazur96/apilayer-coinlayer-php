<?php

use Apilayer\Coinlayer\Actions\Historical as HistoricalAction;
use Apilayer\Coinlayer\CoinlayerClient;
use Apilayer\Coinlayer\Exceptions\CoinlayerException;
use Apilayer\Coinlayer\Responses\Historical as HistoricalResponse;
use Apilayer\Coinlayer\Exceptions\InvalidArgumentException;
use Apilayer\Coinlayer\Enums\CryptoCurrency;
use Apilayer\Coinlayer\Enums\HttpSchema;
use Apilayer\Coinlayer\Enums\TargetCurrency;
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

$date = new DateTimeImmutable('2020-01-01');
$target = TargetCurrency::UAH;
$symbols = [CryptoCurrency::BTC, CryptoCurrency::ETH];
$expand = false;
$callback = 'some_callback';

try {
    $historicalAction = new HistoricalAction(
        $date,
        $target,
        $symbols,
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
    /** @var HistoricalResponse $historicalResponse */
    $historicalResponse = $coinlayerClient->perform($historicalAction);
} catch (CoinlayerException $e) {
    /**
     * This exception may be caused by the different reasons:
     * - HTTP client throw an error (exception would be caught and re-thrown
     *          by CurrencylayerException with same parameters);
     * - Unable to decode response JSON (Unlikely);
     * - API respond without 'success' key - in this case check exception
     *          message about what exactly API respond;
     * - API respond with {success:false}. Check exception message and code.
     */
    echo 'Failed to perform API request - ', $e->getMessage(), PHP_EOL;
    die(1);
}

/* Always true here. */
$success = $historicalResponse->isSuccess();

/*
 * Links to terms & privacy.
 */
$terms = $historicalResponse->getTerms();
$privacy = $historicalResponse->getPrivacy();

/* Indicates that request was performed to /historical endpoint. */
$isHistorical = $historicalResponse->isHistorical();

/*
 * Methods below just reflect the same request parameters.
 */
$targetCurrency = $historicalResponse->getTarget();
$historicalDate = $historicalResponse->getDate();

/* Request performing timestamp */
$timestamp = $historicalResponse->getTimestamp();

/* A JSON array containing the requested cryptocurrency data. */
$rates = $historicalResponse->getRates();

/* All response classes implements JsonSerializable interface. */
$jsonRepresentation = json_encode($historicalResponse, JSON_THROW_ON_ERROR);
