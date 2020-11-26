<?php

namespace Apilayer\Coinlayer\Actions;

use Apilayer\Coinlayer\Responses\Factories\ListResponseFactory;
use Apilayer\Coinlayer\Responses\Factories\ResponseFactoryInterface;

/**
 * @psalm-immutable
 */
class ListAction implements ActionInterface
{
    private ?string $callback;

    /**
     * @param string|null $callback
     */
    public function __construct(?string $callback = null)
    {
        $this->callback = $callback;
    }

    /**
     * @psalm-return ActionInterface::ENDPOINT_LIST
     */
    public function getEndpoint(): string
    {
        return ActionInterface::ENDPOINT_LIST;
    }

    /**
     * @psalm-return array{callback?:string}
     */
    public function getData(): array
    {
        $request = [];

        if ($this->callback !== null) {
            $request['callback'] = $this->callback;
        }

        return $request;
    }

    public function getResponseFactory(): ResponseFactoryInterface
    {
        return new ListResponseFactory();
    }
}
