<?php

namespace SolveCase\TelenorBulkSms;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Cache;

class TelenorSmsClient
{

    protected $client;

    public function __construct(HttpClient $client = null){
        $this->client = $client;
    }

    private function client(){
        return $this->client ?: $this->client = new HttpClient([ 'base_uri' => config('telenorbulksms.base_url')]);
    }

    public function send($params){
        $token = Cache::get('telenorsms_access_token');
        if(empty($token)){
            throw new \Exception('Telenor Sms Token cannot be empty.');
        }

        $headers = [
            'Authorization' => 'Bearer ' . $token,                
        ];

        return $this->client()->post('v3/mm/en/communicationMessage/send', [
            'headers' => $headers,
            'json' =>  $params 
        ]);
    
    }
}