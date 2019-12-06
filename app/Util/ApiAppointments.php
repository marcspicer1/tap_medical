<?php
namespace App\Util;

use GuzzleHttp\Client;
class ApiAppointments
{
    protected $client;
    protected $base_url;
    protected $jwt;
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->base_url = config('tapapi.api_base_url');
        $this->jwt = null;
    }

    public function apiAuth() {
        $email = config('tapapi.json_api_email');
        $password = config('tapapi.json_api_password');
        $params = ['email' => $email, 'password' => $password];
        $token = $this->postRequest('/auth', $params);
        if(isset($token->token)) {
            $this->jwt = $token->token;
        }
        return $this->jwt;
    }

    protected function postRequest($url, $params) {
        try {
            $response = $this->client->post($this->base_url.$url, [
                'body' => $params
            ]);
            return $this->responseHandler($response->getBody()->getContents(), 'json');
        } catch (\Exception $e) {
            return [];
        }
    }

    public function endpointRequestJSON($url) {
        try {
            $response = $this->client->get($this->base_url.$url,[
                'headers' => [
                    'Authorization'=> 'Bearer '.$this->jwt
                ]
            ]);
        } catch (\Exception $e) {
            return [];
        }

        return $this->responseHandler($response->getBody()->getContents(), 'json');
    }

    public function endpointRequestXML($url) {
        try {
            $username = config('tapapi.xml_api_username');
            $password = config('tapapi.xml_api_password');

            $auth = base64_encode($username.':'.$password);
            $response = $this->client->get($this->base_url.$url,[
                'headers' => [
                    'Authorization'=> "Basic ".$auth
                ]
            ]);
        } catch (\Exception $e) {
            return [];
        }
        return $this->responseHandler($response->getBody()->getContents(), 'xml');
    }

    public function responseHandler($response, $type) {
        if ($response) {
            if($type == 'json') {
                return json_decode($response);
            } else if($type == 'xml') {
                return new \SimpleXMLElement($response);
            }
        }

        return [];
    }
}
