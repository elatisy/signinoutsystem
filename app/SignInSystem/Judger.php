<?php

namespace App\SignInSystem;


class Judger
{
    /**
     * 最大长度,汉字算1字符
    */
    private $maxlen = 16;

    /**
     * 储存每个事件所需信息字段
    */
    private $events = [
        'getAuthCode'    => [ 'phoneNumber'] ,
        'signUp'         => ['userName', 'account', 'password', 'phoneNumber','authCode']
    ];

    /**
     * @param $recv array
     * @return array
    */
    public function judge(array $recv){

        if(!isset($recv['event'])){
            return [
                'code'      => '1001',
                'message'   => 'event缺失'
            ];
        }

        if(!isset( $this->events[ $recv['event'] ] ) ){
            return [
                'code'      => '1002',
                'message'   => '未知event'
            ];
        }

        $info = $this->events[$recv['event']];

        foreach ($info as $key){
            if(!isset($recv[$key])){
                return [
                    'code'      => '3002',
                    'message'   => $key.'缺失'
                ];
            }

            if(mb_strlen($recv[$key]) > $this->maxlen){
                return [
                    'code'      => '3003',
                    'message'   => $key.'长度超限,最大长度: '.strval($this->maxlen),
                ];
            }

        }

        return [
            'code'      => '0',
            'message'   => 'ok'
        ];
    }
}