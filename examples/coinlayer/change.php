<?php

use Apilayer\Coinlayer\Actions\Change as ChangeAction;
use Apilayer\Coinlayer\CoinlayerClient;
use Apilayer\Coinlayer\Exceptions\CoinlayerException;
use Apilayer\Coinlayer\Responses\Change as ChangeResponse;
use Apilayer\Coinlayer\Exceptions\InvalidArgumentException;
use Apilayer\Coinlayer\ValueObjects\ChangeInfo;
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

$startDate = new DateTimeImmutable('2020-01-01');
$endDate = new DateTimeImmutable('2020-01-25');
$target = TargetCurrency::UAH;
$symbols = [CryptoCurrency::BTC, CryptoCurrency::ETC];
$callback = 'some_callback';

try {
    $changeAction = new ChangeAction(
        $startDate,
        $endDate,
        $target,
        $symbols,
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
    /** @var ChangeResponse $changeResponse */
    $changeResponse = $coinlayerClient->perform($changeAction);
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
$success = $changeResponse->isSuccess();

/*
 * Links to terms & privacy.
 */
$terms = $changeResponse->getTerms();
$privacy = $changeResponse->getPrivacy();

/* Indicates that request was performed to /change endpoint. */
$isChange = $changeResponse->isChange();

/*
 * Methods below just reflect the same request parameters.
 */
$startDate = $changeResponse->getStartDate();
$endDate = $changeResponse->getEndDate();
$target = $changeResponse->getTarget();

/* The cryptocurrency's exchange rate info */
$rates = $changeResponse->getRates();

/**
 * @var ChangeInfo $rateInfo
 */
foreach ($rates as $cryptoCurrency => $rateInfo) {
    echo 'Rates change information for ', $cryptoCurrency, PHP_EOL;
    echo 'Change: ', $rateInfo->getChange(), PHP_EOL;
    echo 'Change percent: ', $rateInfo->getChangePct(), PHP_EOL;
    echo 'Start rate: ', $rateInfo->getStartRate(), PHP_EOL;
    echo 'End rate: ', $rateInfo->getEndRate(), PHP_EOL;
}

/* All response classes implements JsonSerializable interface. */
$jsonRepresentation = json_encode($changeResponse, JSON_THROW_ON_ERROR);
