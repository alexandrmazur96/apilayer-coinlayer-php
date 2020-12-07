<?php

use Apilayer\Coinlayer\Actions\Live as LiveAction;
use Apilayer\Coinlayer\CoinlayerClient;
use Apilayer\Coinlayer\Enums\TargetCurrency;
use Apilayer\Coinlayer\Exceptions\CoinlayerException;
use Apilayer\Coinlayer\Responses\Live as LiveResponse;
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

$targetCurrency = TargetCurrency::UAH;
$cryptoCurrencies = [CryptoCurrency::BTC, CryptoCurrency::BQ];
$expand = false;

try {
    $liveAction = new LiveAction(
        $targetCurrency,
        $cryptoCurrencies,
        $expand
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
    /** @var LiveResponse $liveResponse */
    $liveResponse = $coinlayerClient->perform($liveAction);
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
$success = $liveResponse->isSuccess();

/*
 * Links to terms & privacy.
 */
$terms = $liveResponse->getTerms();
$privacy = $liveResponse->getPrivacy();

/* Methods below just reflect the same request parameters. */
$targetCurrency = $liveResponse->getTarget();

/* Request performing timestamp */
$timestamp = $liveResponse->getTimestamp();

/* A JSON array containing the requested cryptocurrency data. */
$rates = $liveResponse->getRates();

foreach ($rates as $cryptoCurrency => $rate) {
    echo 'Symbol: ', $cryptoCurrency, ' rate: ', $rate, PHP_EOL;
}

/* All response classes implements JsonSerializable interface. */
$jsonRepresentation = json_encode($liveResponse, JSON_THROW_ON_ERROR);
