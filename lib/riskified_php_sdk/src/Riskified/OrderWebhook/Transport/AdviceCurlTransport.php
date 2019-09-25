<?php

namespace Riskified\OrderWebhook\Transport;

use Riskified\Common\Signature\HttpDataSignature;
use Riskified\Common\Riskified;

class AdviceCurlTransport extends CurlTransport
{
    public function __construct()
    {
        parent::__construct(new HttpDataSignature(), null);
    }

    /**
     * Overwritten parent function wth 'Advise-Api-Call' endpoint.
     * @param object $json
     * @param String $endpoint
     * @return mixed
     * @throws \Riskified\OrderWebhook\Exception\MalformedJsonException
     * @throws \Riskified\OrderWebhook\Exception\UnsuccessfulActionException
     */
    public function send_json_request($json, $endpoint = 'advise') {
        $ch = curl_init($this->endpoint_prefix().$endpoint);
        $curl_options = array(
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $this->headers($json),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => $this->user_agent,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_DNS_USE_GLOBAL_CACHE => $this->dns_cache,
            CURLOPT_FAILONERROR => false
        );
        curl_setopt_array($ch, $curl_options);

        $this->requestData['endpoint'] = $this->endpoint_prefix().$endpoint;
        $this->requestData['payload'] = $json;

        $body = curl_exec($ch);
        $this->requestData['responseBody'] = $body;
        if (curl_errno($ch)) {
            throw new Exception\CurlException(curl_error($ch), curl_errno($ch));
        }

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->requestData['responseStatus'] = $status;
        curl_close($ch);

        return $this->json_response($body, $status);
    }

    /**
     * @param $body
     * @param $status
     * @return mixed
     * @throws \Riskified\OrderWebhook\Exception\MalformedJsonException
     * @throws \Riskified\OrderWebhook\Exception\UnsuccessfulActionException
     */
    private function json_response($body, $status) {
        $response = json_decode($body);

        if (!$response)
            throw new Exception\MalformedJsonException($body, $status);
        if($status != 200)
            throw new Exception\UnsuccessfulActionException($body, $status);

        return $response;
    }

}