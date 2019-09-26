<?php

require_once(Mage::getBaseDir('lib') . DIRECTORY_SEPARATOR . 'riskified_php_sdk' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Riskified' . DIRECTORY_SEPARATOR . 'OrderWebhook/Transport/CurlTransport.php');
use Riskified\Common\Signature\HttpDataSignature;

class AdviceCurlTransport extends Riskified\OrderWebhook\Transport\CurlTransport
{
    /**
     * AdviceCurlTransport constructor.
     */
    public function __construct()
    {
        parent::__construct(new HttpDataSignature(), null);
    }

    /**
     * @param $json
     * @param $endpoint
     * @throws Exception\CurlException
     * @throws Exception\UnsuccessfulActionException
     */
    public function executeJsonRequest($json, $endpoint)
    {

        return $this->send_json_request($json, $endpoint);
    }
}