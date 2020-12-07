<?php

use Apilayer\Coinlayer\Actions\Convert as ConvertAction;
use Apilayer\Coinlayer\CoinlayerClient;
use Apilayer\Coinlayer\Exceptions\CoinlayerException;
use Apilayer\Coinlayer\Responses\Convert as ConvertResponse;
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

$from = CryptoCurrency::BTC;
$to = CryptoCurrency::ETC;
$amount = 12.25;
$date = new DateTimeImmutable('2020-01-01');

try {
    $convertAction = new ConvertAction(
        $from,
        $to,
        $amount,
        $date
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
    /** @var ConvertResponse $convertResponse */
    $convertResponse = $coinlayerClient->perform($convertAction);
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
$success = $convertResponse->isSuccess();

/*
 * Links to terms & privacy.
 */
$terms = $convertResponse->getTerms();
$privacy = $convertResponse->getPrivacy();

/*
 * Duplicate convert request data
 */
$requestQuery = $convertResponse->getQuery();

/*
 * Convert information - timestamp & exchange rate
 */
$convertInfo = $convertResponse->getInfo();

/*
 * The conversion result.
 */
$result = $convertResponse->getResult();

/* All response classes implements JsonSerializable interface. */
$jsonRepresentation = json_encode($convertResponse, JSON_THROW_ON_ERROR);
