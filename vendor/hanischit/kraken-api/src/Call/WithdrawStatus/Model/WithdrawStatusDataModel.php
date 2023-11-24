<?php

namespace HanischIt\KrakenApi\Call\WithdrawStatus\Model;

/**
 * Class OHLCDataModel
 * @package HanischIt\KrakenApi\Call\OHLCData\Model
 */
class WithdrawStatusDataModel
{
	
    /**
     * @var int
     */
    private $method;
	 private $aclass;
    /**
     * @var float
     */
    private $asset;
    /**
     * @var float
     */
    private $refid;
    /**
     * @var float
     */
    private $txid;
    /**
     * @var float
     */
    private $info;
    /**
     * @var float
     */
    private $amount;
    /**
     * @var float
     */
    private $fee;
    /**
     * @var int
     */
    private $time;
  private $status;
   private $status_prop;
    /**
     * OHLCDataModel constructor.
     * @param int $time
     * @param float $open
     * @param float $high
     * @param float $low
     * @param float $close
     * @param float $vwap
     * @param float $volume
     * @param int $count
     */
    public function __construct($method ,$aclass ,  $asset, $refid, $txid, $info, $amount, $fee, $time, $status,$status_prop)
    {
        $this->method = $method;
		$this->aclass = $aclass;
        $this->asset = $asset;
        $this->refid = $refid;
        $this->txid = $txid;
        $this->info = $info;
        $this->amount = $amount;
        $this->fee = $fee;
        $this->time = $time;
		 $this->status = $status;
		 $this->status_prop = $status_prop;
    }

    /**
     * @return int
     */
    public function getMethod()
    {
        return $this->method;
    }
	
	 public function getAclass()
    {
        return $this->aclass;
    }

    /**
     * @return float
     */
    public function getAsset()
    {
        return $this->asset;
    }

    /**
     * @return float
     */
    public function getRefid()
    {
        return $this->refid;
    }

    /**
     * @return float
     */
    public function getTxid()
    {
        return $this->txid;
    }

    /**
     * @return float
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return float
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }
	
	 public function getStatus()
    {
        return $this->status;
    }
	 public function getStatusProp()
    {
        return $this->status_prop;
    }
}
