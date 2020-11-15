<?php

namespace Apilayer\Coinlayer\Responses;

use JsonSerializable;

abstract class DataAbstractResponse implements JsonSerializable
{
    private bool $success;
    private string $terms;
    private string $privacy;

    /**
     * @param bool $success
     * @param string $terms
     * @param string $privacy
     */
    public function __construct(bool $success, string $terms, string $privacy)
    {
        $this->success = $success;
        $this->terms = $terms;
        $this->privacy = $privacy;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getTerms(): string
    {
        return $this->terms;
    }

    public function getPrivacy(): string
    {
        return $this->privacy;
    }

    abstract public function toArray(): array;
}
