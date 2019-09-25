<?php

use Riskified\Full\Model\Api\Builder\Advice;

class Riskified_Full_AdviceController extends Mage_Core_Controller_Front_Action
{
    /**
     * Controller Advise-API-call Action.
     */
    public function callAction()
    {
        $helper = Mage::helper('full/advice_adviceBody');
        $array = ["checkout" => [
            "id" => '122',
            "email" => "a@fraud.pl",
            "currency" => "USD",
            "total_price" => '23',
            "payment_details" => [
                [
                    "avs_result_code" => "Y",
                    "credit_card_bin" => "492044",
                    "credit_card_company" => "Visa",
                    "credit_card_number" => "4111111111111111",
                    "cvv_result_code" => "M"
                ]
            ],
            "_type" => 'credit_card',
            "gateway" => 'credit_card'
        ]
        ];

        $json = json_encode($array);
        $apiRequestResponse = $helper->sendJsonAdviseRequest($json);
        $status = $apiRequestResponse->checkout->status;
        $authType = $apiRequestResponse->checkout->authentication_type->auth_type;

        return $this->returnResponse($status, $authType);
    }

    private function returnResponse($status, $authType)
    {
        if($status != "captured"){
            $adviceCallStatus = false;
        }else {
            if($authType == "sca" || $authType == "tra"){
                $adviceCallStatus = false;
            }else{
                $adviceCallStatus = true;
            }
        }

        return $adviceCallStatus;
    }
}