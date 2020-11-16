<?php

namespace Apilayer\Tests\Coinlayer\Actions;

use Apilayer\Coinlayer\Actions\ActionInterface;
use Apilayer\Coinlayer\Actions\ListAction;
use Apilayer\Tests\TestCase;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ListTest extends TestCase
{
    public function testGetEndpoint(): void
    {
        $listAction = new ListAction(null);
        self::assertEquals(ActionInterface::ENDPOINT_LIST, $listAction->getEndpoint());
    }

    public function testGetData(): void
    {
        $listAction = new ListAction(null);
        self::assertEquals([], $listAction->getData());

        $listAction = new ListAction('some_callback');
        self::assertEquals(['callback' => 'some_callback'], $listAction->getData());
    }
}
