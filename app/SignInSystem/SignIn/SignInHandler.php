<?php


namespace App\SignInSystem\SignIn;

use App\DataBaseManager\DataBaseManager;
use App\SignInSystem\Auth\AuthProcessor;
use Carbon\Carbon;

class SignInHandler
{
    /**
     * @var DataBaseManager
     */
    private $dbmanager;

    /**
     * @var AuthProcessor
     */
    private $authprocessor;

    /**
     * @var array
     */
    private $recv;

    /**
     * @var string
     */
    private $now_day;

    /**
     * @var integer
     */
    private $timestamp;

    public function __construct($recv)
    {
        $this->dbmanager = new DataBaseManager($recv['table'],'token',$recv['token']);
        $this->authprocessor = new AuthProcessor();
        $this->recv = $recv;
        $this->now_day = date('Y-m-d');
        $this->timestamp = intval($recv['timestamp']);
    }

    public function handle(){
        try{
            $res = $this->authprocessor->auth([
                'account'   => $this->recv['account'],
                'token'     => $this->recv['token']
            ]);

            if(!$res['status']){
                return $res['result'];
            }

            $row = $this->dbmanager->multi_where_find([
                'token'     => $this->recv['token'],
                'event'     => 'signOut',
                'date'      => $this->now_day
            ]);

//            if($row != null){
//                return [
//                    'code'      => 9002,
//                    'message'   => '今日已签退'
//                ];
//            }

            if(!isset($this->recv['declaration'])){
                $this->recv['declaration'] = '早起的鸟儿有虫吃~';
            }

            $this->dbmanager->write([
                'token'         => $this->recv['token'],
                'date'          => $this->now_day,
                'declaration'   => $this->recv['declaration'],
                'event'         => 'signIn',
                'created_at'    => $this->timestamp,
                'photoUrl'      => $this->recv['photoUrl']
            ],true);

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

}