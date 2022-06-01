<?php

namespace Opencart\Catalog\Model\Extension\Smsto\Smsto;

use Exception;
use GuzzleHttp\Client;

class Call extends \Opencart\System\Engine\Model
{

        /**
         * Method to call SMSto api
         * 
         * @author Panayiotis Halouvas <phalouvas@kainotomo.com>
         *
         * @param string $api_key
         * @param string $method
         * @param string $url
         * @param string|array|null $payload
         * @return string
         */
        public function callSmsto(string $api_key, string $method, string $url, $payload = null): string
        {
                $hasApiKey = 0 !== strlen($api_key);
                if (!$hasApiKey) {
                        throw new Exception("Missing API key", 401);
                }

                $method = strtoupper($method);
                if ($method == 'GET') {
                        $params = http_build_query(json_decode($payload) ?? []);
                        if ($params) {
                                $url = $url . '?' . $params;
                        }
                }

                $ch = curl_init();
                ob_start();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        "Authorization: Bearer $api_key",
                        'Content-Type: application/json',
                ]);

                if ($method != 'GET') {
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                }

                $response = curl_exec($ch);
                curl_close($ch);
                return $response;
        }
}
