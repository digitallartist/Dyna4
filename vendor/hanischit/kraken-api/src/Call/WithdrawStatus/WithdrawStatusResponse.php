<?php

namespace HanischIt\KrakenApi\Call\WithdrawStatus;
use HanischIt\KrakenApi\Call\WithdrawStatus\Model\WithdrawStatusDataModel;
use HanischIt\KrakenApi\Model\ResponseInterface;

/**
 * Class TradeBalanceResponse
 * @package HanischIt\KrakenApi\Call\TradeBalance
 */
class WithdrawStatusResponse implements ResponseInterface
{
    /**
     * @var float
     */
	    private $withdrawStatusDataModels;
		/*
			private $method;
			private $aclass;
			private $asset;
			private $refid;
			private $txid;
			private $info;
			private $amount;
			private $fee;
			private $time;
			private $status;
			private $status_prop;
			*/
		/*
		private $cancel-pending;
		private $canceled;
		private $cancel-denied;
		private $return;
		private $onhold;*/
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
		/*	echo "a<br/>";
				echo "<br/>";
	print_r($data);
	
	echo "<br/>";
      	echo "<br/>";
		*/
		 foreach ($data as $key => $value) {
		
			
		
           // foreach ($value as $key2 => $value2) {
			
                $this->withdrawStatusDataModels[] = new WithdrawStatusDataModel(
				$value['method'],$value['aclass'],$value['asset'],$value['refid'],$value['txid'],$value['info'],$value['amount'],$value['fee'],$value['time'],$value['status'],@$value['status-prop']);
       
		  // }
		 }	
        /*
			$this->method = $data["method"];
			$this->aclass = $data["aclass"];
			$this->asset = $data["asset"];
			$this->refid = $data["refid"];
			$this->txid = $data["txid"];
			$this->info = $data["info"];
			$this->amount = $data["amount"];
			$this->fee = $data["fee"];
			$this->time = $data["time"];
			$this->status = $data["status"];
			$this->status_prop = $data["status-prop"];*/
			/*$this->cancel-pending = $data["cancel-pending"];
			$this->cancel-denied = $data["cancel-denied"];
			$this->return = $data["return"];
			$this->onhold = $data["onhold"];*/

    }

    /**
     * @return float
     */
  public function getWithdrawStatusDataModels()
    {
        return $this->withdrawStatusDataModels;
    }
}
