<?php

namespace Apilayer\Coinlayer\Actions;

use Apilayer\Coinlayer\Responses\Factories\ListResponseFactory;
use Apilayer\Coinlayer\Responses\Factories\ResponseFactoryInterface;

/**
 * @psalm-immutable
 */
class ListAction implements ActionInterface
{

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
        return [];
    }

    public function getResponseFactory(): ResponseFactoryInterface
    {
        return new ListResponseFactory();
    }
}
