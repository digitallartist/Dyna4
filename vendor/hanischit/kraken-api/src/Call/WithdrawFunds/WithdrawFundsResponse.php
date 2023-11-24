<?php

namespace HanischIt\KrakenApi\Call\WithdrawFunds;



use HanischIt\KrakenApi\Model\ResponseInterface;

/**
 * Class TradeBalanceResponse
 * @package HanischIt\KrakenApi\Call\TradeBalance
 */
class WithdrawFundsResponse implements ResponseInterface
{
    /**
     * @var float
     */
    private $refid;
    /**
    

    /**
     * @param array $data
     */
    public function manualMapping($data)
    {
        $this->refid = $data["refid"];

    }

    /**
     * @return float
     */
    public function getRefid()
    {
        return $this->refid;
    }

    
}
