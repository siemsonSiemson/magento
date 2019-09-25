<?php

require_once(Mage::getBaseDir('lib') . DIRECTORY_SEPARATOR . 'riskified_php_sdk' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Riskified' . DIRECTORY_SEPARATOR . 'autoloader.php');

use Riskified\Common\Riskified;
use Riskified\Common\Validations;
use Riskified\Common\Signature\HttpDataSignature;
use Riskified\OrderWebhook\Transport\AdviceCurlTransport;


class Riskified_Full_Helper_Advice_AdviceBody extends Mage_Core_Helper_Abstract
{
    /**
     * @var AdviceCurlTransport
     */
    private $adviceCurl;

    /**
     * Riskified_Full_Helper_Advice_AdviceBody constructor.
     */
    public function __construct()
    {
        $this->initSdk();
        $this->adviceCurl = new AdviceCurlTransport();
    }

    /**
     * Same function as parent function.
     */
    private function initSdk()
    {
        $helper = Mage::helper('full');
        $authToken = $helper->getAuthToken();
        $env = constant($helper->getConfigEnv());
        $shopDomain = $helper->getShopDomain();
        $this->version = $helper->getExtensionVersion();
        $sdkVersion = \Riskified\Common\Riskified::VERSION;

        Mage::helper('full/log')->log("Riskified initSdk() - shop: $shopDomain, env: $env, token: $authToken, extension_version: $this->version, sdk_version: $sdkVersion");
        Riskified::init($shopDomain, $authToken, $env, Validations::SKIP);
    }

    /**
     * @param $json
     * @throws \Riskified\OrderWebhook\Exception\MalformedJsonException
     * @throws \Riskified\OrderWebhook\Exception\UnsuccessfulActionException
     */
    public function sendJsonAdviseRequest($json)
    {
        $this->adviceCurl->send_json_request($json);
    }
}