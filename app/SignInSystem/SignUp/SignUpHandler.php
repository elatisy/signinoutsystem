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
        if($row['status'] == '1'){
            return [
                'code'      => '8001',
                'message'   => '服务器错误'
            ];
        }

        $row = $row['result'];
        if($row == null){
            return [
                'code'      => '6003',
                'message'   => '未请求验证码'
            ];
        }

        if($row->is_signed_up){
            return [
                'code'      => '6001',
                'message'   => '账号已存在'
            ];
        }

        if($this->recv['authCode'] != $row->authCode){
            return [
                'code'      => '6002',
                'message'   => '验证码错误'
            ];
        }

        $db_result = $this->dbmanager->write(array_merge($this->recv, [
            'is_signed_up'  => true,
        ]));

        if($db_result['status'] == '0'){
            return [
                'code'      =>  '0',
                'message'   =>  'ok'
            ];
        }
        return [
            'code'      => '8001',
            'message'   => '服务器错误'
        ];
    }


    public function getAuthCode(){
        try{
            $res = $this->dbmanager->find($this->recv['phoneNumber']);
            if($res['status'] == '1'){
                return [
                    'code'      => '8001',
                    'message'   => '服务器错误'
                ];
            }

            $row = $res['result'];
            if($row != null){
                if($row->is_signed_up){
                    return [
                        'code'      => '6001',
                        'message'   => '账号已存在'
                    ];
                }

                if(($this->now - $row->authCode_requestTime) > $this->limit_time){
                    return [
                        'code'      => '4002',
                        'message'   => '验证码请求过于频繁'
                    ];
                }

            }

            $authcode = strval(rand(10000,99999));
            $message = [$authcode, strval(( $this->max_time / 60 ))];

            $smssender = new SMSSender();
            $sms_result = $smssender->send($message, $this->recv['phoneNumber'], 'signup');
            $db_result  = $this->dbmanager->write(array_merge($this->recv,[
                'authCode'              => $authcode,
                'authCode_requestTime'  => $this->now,
                'is_signed_up'          => false,
            ]));

            if($sms_result['status'] == '0' && $db_result['status'] == '0'){
                return [
                    'code'      => '0',
                    'message'   => 'ok'
                ];
            }else{
                return [
                    'code'      => '8001',
                    'message'   => '服务器错误'
                ];
            }
        }catch (\Exception $e){
            return ['message'   => $e->getMessage()];
        }
    }

}