<?php

namespace HanischIt\KrakenApi\Call\WithdrawInfo;



use HanischIt\KrakenApi\Model\ResponseInterface;

/**
 * Class TradeBalanceResponse
 * @package HanischIt\KrakenApi\Call\TradeBalance
 */
class WithdrawInfoResponse implements ResponseInterface
{
    /**
     * @var float
     */
    private $method;
	private $limit;
	private $fee;
	/*
	method = name of the withdrawal method that will be used
limit = maximum net amount that can be withdrawn right now
fee = amount of fees that will be paid
*/
    /**
    

    /**
     * @param array $data
     */
    public function manualMapping($data)
    {
        $this->method = $data["method"];
		$this->limit = $data["limit"];
		$this->fee = $data["fee"];

    }

    /**
     * @return float
     */
    public function getFee()
    {
        return $this->fee;
    }

    
}
