<?php


namespace HanischIt\KrakenApi\Call\WithdrawFunds;
use HanischIt\KrakenApi\Enum\VisibilityEnum;
use HanischIt\KrakenApi\Model\RequestInterface;

/**
 * Class LedgersInfoRequest
 * @package HanischIt\KrakenApi\Call\LedgersInfo
 */
class WithdrawFundsRequest implements RequestInterface
{


    /**
     * @var string
     */
    private $aclass;
    /**
     * @var string
     */
    private $asset;
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
     * @param string $asset
     * @param string $key
     * @param string $amount

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
        return 'Withdraw';
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
        return WithdrawFundsResponse::class;
    }
}
