<?php

namespace Apilayer\Coinlayer;

use Apilayer\Coinlayer\Actions\ActionInterface;
use Apilayer\Coinlayer\Responses\DataAbstractResponse;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Apilayer\Coinlayer\Enums\HttpSchema;

abstract class BaseClient
{
    /** @psalm-var HttpSchema::SCHEMA_* */
    protected string $schema;
    protected string $apiKey;
    protected RequestFactoryInterface $httpRequestFactory;
    protected ClientInterface $httpClient;

    abstract public function getApiBaseUrl(): string;

    abstract public function perform(ActionInterface $action): DataAbstractResponse;

    /**
     * @param ActionInterface $action
     * @return array<string,mixed>
     */
    protected function prepareData(ActionInterface $action): array
    {
        $data = $action->getData();
        $data['access_key'] = $this->apiKey;

        return $data;
    }

    /**
     * @param string $endpoint
     * @param array<string,mixed> $data
     * @return string
     */
    protected function buildApiUrl(string $endpoint, array $data): string
    {
        return sprintf(
            '%s://%s/%s?%s',
            $this->schema,
            $this->getApiBaseUrl(),
            $endpoint,
            $this->buildApiQuery($data)
        );
    }

    /**
     * @param array<string,mixed> $data
     * @return string
     */
    private function buildApiQuery(array $data): string
    {
        $str = '';

        /**
         * @var string $key
         * @var mixed $datum
         */
        foreach ($data as $key => $datum) {
            if (is_array($datum)) {
                /** @psalm-var array $datum */
                $str .= '&' . $key . '=' . join(',', $datum);
                continue;
            }

            /** @psalm-var scalar $datum */
            $str .= '&' . $key . '=' . $datum;
        }

        return $str;
    }
}
