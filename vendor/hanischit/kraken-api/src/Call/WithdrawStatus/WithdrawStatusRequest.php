<?php


namespace HanischIt\KrakenApi\Call\WithdrawStatus;
use HanischIt\KrakenApi\Enum\VisibilityEnum;
use HanischIt\KrakenApi\Model\RequestInterface;

/**
 * Class LedgersInfoRequest
 * @package HanischIt\KrakenApi\Call\LedgersInfo
 */
class WithdrawStatusRequest implements RequestInterface
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
    private $method;

    public function __construct(
        $aclass = 'currency',
        $asset = 'DASH',
        $method = null
    ) {
        $this->aclass = $aclass;
        $this->asset = $asset;
     
        $this->method = $method;
   
    }


    /**
     * Returns the api request name
     *
     * @return string
     */
    public function getMethod()
    {
        return 'WithdrawStatus';
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
     
        $ret["method"] = $this->method;
     
        return $ret;
    }

    /**
     * @return string
     */
    public function getResponseClassName()
    {
        return WithdrawStatusResponse::class;
    }
}
