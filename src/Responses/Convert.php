<?php

namespace Apilayer\Coinlayer\Responses;

use Apilayer\Coinlayer\ValueObjects\CoinlayerInfo;
use Apilayer\Coinlayer\ValueObjects\Query;

/**
 * @psalm-import-type _Query from \Apilayer\Coinlayer\CoinlayerClient
 * @psalm-import-type _Info from \Apilayer\Coinlayer\CoinlayerClient
 *
 * @psalm-type _ConvertResponseData=array{
 *      success:bool,
 *      terms:string,
 *      privacy:string,
 *      query:Query,
 *      info:CoinlayerInfo,
 *      result:float
 * }
 */
class Convert extends DataAbstractResponse
{
    private Query $query;
    private CoinlayerInfo $info;
    private float $result;

    /**
     * @param bool $success
     * @param string $terms
     * @param string $privacy
     * @param float $result
     *
     * @psalm-param _Query $query
     * @psalm-param _Info $info
     */
    public function __construct(
        bool $success,
        string $terms,
        string $privacy,
        array $query,
        array $info,
        float $result
    ) {
        parent::__construct($success, $terms, $privacy);
        $this->query = new Query($query['from'], $query['to'], $query['amount']);
        $this->info = new CoinlayerInfo($info['timestamp'], $info['rate']);
        $this->result = $result;
    }

    /**
     * @return Query
     */
    public function getQuery(): Query
    {
        return $this->query;
    }

    /**
     * @return CoinlayerInfo
     */
    public function getInfo(): CoinlayerInfo
    {
        return $this->info;
    }

    /**
     * @return float
     */
    public function getResult(): float
    {
        return $this->result;
    }

    /**
     * @psalm-return _ConvertResponseData
     */
    public function toArray(): array
    {
        return [
            'success' => $this->isSuccess(),
            'terms' => $this->getTerms(),
            'privacy' => $this->getPrivacy(),
            'query' => $this->getQuery(),
            'info' => $this->getInfo(),
            'result' => $this->getResult(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
