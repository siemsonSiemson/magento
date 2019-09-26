<?php

use Riskified\Full\Model\Api\Builder\Advice;

class Riskified_Full_AdviceController extends Mage_Core_Controller_Front_Action
{
    /**
     * Controller Advise-API-call Action.
     * @return Zend_Controller_Response_Abstract
     */
    public function callAction()
    {
        $helper = Mage::helper('full/advice_adviceBody');
        $cart = Mage::getModel('checkout/cart')->getQuote();
        $paymentObject = $cart->getPayment();
        $paymentMethodObject = $paymentObject->getMethodInstance();

        $array = ["checkout" => [
            "id" => $cart['entity_id'],
            "email" => 'as@as.pl',
            "currency" => $cart['quote_currency_code'],
            "total_price" => $cart['grand_total'],
            "payment_details" => [
                [
                    "avs_result_code" => "Y",
                    "credit_card_bin" => "492044",
                    "credit_card_company" => "Visa",
                    "credit_card_number" => "4111111111111111",
                    "cvv_result_code" => "M"
                ]
            ],
            "_type" => $paymentMethodObject->getFormBlockType(),
            "gateway" => $paymentMethodObject->getCode()
        ]
        ];

        $json = json_encode($array);
        $apiRequestResponse = $helper->sendJsonAdviseRequest($json);
        $status = $apiRequestResponse->checkout->status;
        $authType = $apiRequestResponse->checkout->authentication_type->auth_type;

        return $this->getResponse()
            ->clearHeaders()
            ->setHeader('HTTP/1.0', 200, true)
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode(array('advise_status' => $this->returnResponse($status, $authType))));
    }

    /**
     * Function returns 'Advise-Call' depending on authentication type (email address).
     * @param $status
     * @param $authType
     * @return bool
     */
    private function returnResponse($status, $authType)
    {
        if($status == "captured"){
            $adviceCallStatus = true;
        }elseif($status == "created") {
            if($authType == "sca" || $authType == "tra"){
                $adviceCallStatus = false;
            }else{
                $adviceCallStatus = true;
            }
        }else{
            $adviceCallStatus = false;
        }

        return $adviceCallStatus;
    }
}