<?php

use Apilayer\Coinlayer\Actions\ListAction;
use Apilayer\Coinlayer\CoinlayerClient;
use Apilayer\Coinlayer\Exceptions\CoinlayerException;
use Apilayer\Coinlayer\Responses\ListResponse;
use Apilayer\Coinlayer\Exceptions\InvalidArgumentException;
use Apilayer\Coinlayer\Enums\HttpSchema;
use Apilayer\Coinlayer\ValueObjects\CryptoCurrencyInfo;
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

$listAction = new ListAction();

try {
    /** @var ListResponse $listResponse */
    $listResponse = $coinlayerClient->perform($listAction);
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
$success = $listResponse->isSuccess();

/*
 * Links to terms & privacy.
 */
$terms = $listResponse->getTerms();
$privacy = $listResponse->getPrivacy();

$fiat = $listResponse->getFiat();
foreach ($fiat as $targetCurrency => $targetCurrencyFullName) {
    echo '[', $targetCurrency, ': ', $targetCurrencyFullName, ']', PHP_EOL;
}

$cryptoCurrenciesInfo = $listResponse->getCrypto();

/** @var CryptoCurrencyInfo $cryptoCurrencyInfo */
foreach ($cryptoCurrenciesInfo as $cryptoCurrency => $cryptoCurrencyInfo) {
    echo 'Info for ', $cryptoCurrency, PHP_EOL;
    echo str_repeat('-', 32);
    echo 'Symbol: ', $cryptoCurrencyInfo->getSymbol(), PHP_EOL;
    echo 'Name: ', $cryptoCurrencyInfo->getName(), PHP_EOL;
    echo 'Name full: ', $cryptoCurrencyInfo->getNameFull(), PHP_EOL;
    echo 'Max supply: ', $cryptoCurrencyInfo->getMaxSupply(), PHP_EOL;
    echo 'Icon: ', $cryptoCurrencyInfo->getIconUrl(), PHP_EOL;
}

/* All response classes implements JsonSerializable interface. */
$jsonRepresentation = json_encode($listResponse, JSON_THROW_ON_ERROR);
