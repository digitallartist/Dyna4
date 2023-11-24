<?php


namespace HanischIt\KrakenApi\Call\WithdrawInfo;
use HanischIt\KrakenApi\Enum\VisibilityEnum;
use HanischIt\KrakenApi\Model\RequestInterface;

/**
 * Class LedgersInfoRequest
 * @package HanischIt\KrakenApi\Call\LedgersInfo
 */
class WithdrawInfoRequest implements RequestInterface
{


    /**
     * @var string
     */
    private $aclass;
    /**
     * @var string
     */
    private $currency;
    /**
     * @var string
     */
    private $key;
    /**
     * @var string
     */
    private $amount;
    /**

    /**
     * LedgersInfoRequest constructor.
     * @param string $aclass
     * @param string $currency
     * @param string $key
     * @param string $amount
	 
	 aclass = asset class (optional):
    currency (default)
asset = asset being withdrawn
key = withdrawal key name, as set up on your account
amount = amount to withdraw

     */
    public function __construct(
        $aclass = 'currency',
        $asset = null,
        $key = null,
        $amount = null
    ) {
        $this->aclass = $aclass;
        $this->asset = $asset;
        $this->key = $key;
        $this->amount = $amount;
   
    }


    /**
     * Returns the api request name
     *
     * @return string
     */
    public function getMethod()
    {
        return 'WithdrawInfo';
    }

    /**
     * @return string
     */
    public function getVisibility()
    {
        return VisibilityEnum::VISIBILITY_PRIVATE;
    }

    /**
     * @return array
     */
    public function getRequestData()
    {
        $ret = [];
        $ret["aclass"] = $this->aclass;
        $ret["asset"] = $this->asset;
        $ret["key"] = $this->key;
        $ret["amount"] = $this->amount;
     
        return $ret;
    }

    /**
     * @return string
     */
    public function getResponseClassName()
    {
        return WithdrawInfoResponse::class;
    }
}
