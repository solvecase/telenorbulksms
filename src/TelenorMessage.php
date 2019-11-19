<?php

namespace SolveCase\TelenorBulkSms;

class TelenorMessage {

    public $payload = [];

    public static function create($content = ''){
        return new static($content);
    }

    public function __construct($content = ''){
        $this->content($content);
        $this->payload['sender'] = [
            '@type' => '5',
            'name' => config('telenorbulksms.sms.sender')
        ];
        $this->payload['sendTime'] = date('Y-m-d\TH:i:sP');
        $this->payload['characteristic'][] = ['name' => 'UserName', 'value' => config('telenorbulksms.sms.username')];
        $this->payload['characteristic'][] = ['name' => 'Password', 'value' => config('telenorbulksms.sms.password')];
    }

    public function content($content){            
        if(mb_strlen($content) == strlen($content)){
            $this->payload['type'] = 'TEXT';
            $this->payload['content'] = $content;
        }else{
            $this->payload['type'] = 'MULTILINGUAL';
            $this->payload['content'] = strtoupper(str_replace(array('"', '\u'), array('',''), json_encode($content)));
            $this->payload['characteristic'][] = ['name' => 'Udhi', 'value' => '1'];
        }

        return $this;
    }

    public function to($number){
        $this->payload['receiver'][] = [
            '@type' => '1',
            'phoneNumber' => "$number"
        ];

        return $this;
    }

    public function toNotGiven()
    {
        return !isset($this->payload['receiver']);
    }

    public function toArray()
    {
        return $this->payload;
    }
}