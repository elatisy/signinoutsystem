<?php

namespace App\SignInSystem\SignUp;

use App\SMSSender\qcloudSMSSender\SMSSender;
use App\DataBaseManager\DataBaseManager;
use Carbon\Carbon;

class SignUpHandler
{
    /**
     * @var DataBaseManager
     */
    private $dbmanager;



    /**
     * @var array
     */
    private $recv;



    /**
     * 验证码有效时间
     * @var int second
     */
    private $max_time   = 1800;

    /**
     * 验证码请求间隔
     * @var int second
     */
    private $limit_time = 60;


    /**
     * @var integer
     */
    private $now;

    public function __construct(array $recv)
    {
        $this->dbmanager = new DataBaseManager($recv['table'],'phoneNumber',$recv['phoneNumber']);

        unset($recv['event']);
        unset($recv['table']);
        $this->recv = $recv;

        $this->now = Carbon::now()->timestamp;
    }

    /**
     * @return array
     */
    public function signUp(){

        $row = $this->dbmanager->find($this->recv['phoneNumber']);
        if($row == null){
            return [
                'code'      => 6003,
                'message'   => '未请求验证码'
            ];
        }

        if($row->is_signed_up || $this->dbmanager->find($this->recv['account'],'account') != null){
            return [
                'code'      => 6001,
                'message'   => '账号已存在'
            ];
        }

        if($this->recv['authCode'] != $row->authCode){
            return [
                'code'      => 6002,
                'message'   => '验证码错误'
            ];
        }

        do{
            $token = $this->createToken();
            $check = $this->dbmanager->find($token,'token');
        }while($check != null);

        $db_result = $this->dbmanager->write(array_merge($this->recv, [
            'is_signed_up'  => true,
            'token'         => $token,
        ]));

        if($db_result){
            return [
                'code'      =>  0,
                'message'   =>  'ok'
            ];
        }

        return [
            'code'      => 8001,
            'message'   => '服务器错误'
        ];
    }


    public function getAuthCode(){
        try{
            $row = $this->dbmanager->find($this->recv['phoneNumber']);
            if($row != null){
                if($row->is_signed_up){
                    return [
                        'code'      => 6001,
                        'message'   => '账号已存在'
                    ];
                }

                if((intval($this->now) - intval($row->authCode_requestTime)) < $this->limit_time){
                    return [
                        'code'      => 4002,
                        'message'   => '验证码请求过于频繁',
                    ];
                }

            }

            $authcode = strval(rand(10000,99999));
            $message = [$authcode, strval(( $this->max_time / 60 ))];

            $smssender = new SMSSender();
            $smssender->send($message, $this->recv['phoneNumber'], 'signup');
            $this->dbmanager->write(array_merge($this->recv,[
                'authCode'              => $authcode,
                'authCode_requestTime'  => $this->now,
                'is_signed_up'          => false,
            ]));

            return [
                'code'      => 0,
                'message'   => 'ok'
            ];
        }catch (\Exception $e){
            return [
                'code'      => 8001,
                'message'   => '服务器错误'
            ];
        }
    }

    private function createToken(){
        $characters = 'qwertyuiopasdfghjklzxcvbnm123456789QWERTYUIOPASDFGHJKLZXCVBNM';
        $res = '';
        $len = strlen($characters) - 1;
        for($i = 0;$i < 32; ++$i){
            $res .= $characters[rand(0,$len)];
        }
        return $res;
    }

}