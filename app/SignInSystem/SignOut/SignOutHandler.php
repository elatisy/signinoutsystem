<?php

namespace App\SignInSystem\SignOut;

use App\DataBaseManager\DataBaseManager;
use App\SignInSystem\Auth\AuthProcessor;
use Carbon\Carbon;

class SignOutHandler
{
    /**
     * @var array
     */
    private $recv;

    /**
     * @var DataBaseManager
     */
    private $dbmanager;

    /**
     * @var string
     */
    private $now_day;

    /**
     * @var integer
     */
    private $timestamp;

    public function __construct(array $recv)
    {
        $this->dbmanager = new DataBaseManager($recv['table']);
        $this->recv = $recv;
        $this->now_day = date('Y-m-d');
        $this->timestamp = intval($recv['timestamp']);
    }

    public function handle(){
        try{
            $authprocessor = new AuthProcessor();
            $res = $authprocessor->auth([
                'account'   => $this->recv['account'],
                'token'     => $this->recv['token']
            ]);

            if(!$res['status']){
                return $res['result'];
            }

            $rows = $this->dbmanager->multi_where_find([
                'token'     => $this->recv['token'],
                'event'     => 'signOut',
                'date'      => $this->now_day
            ]);

            if($rows != null){
                return [
                    'code'      => 9002,
                    'message'   => '今日已签退'
                ];
            }

            $rows = $this->dbmanager->multi_where_find([
                'token'     => $this->recv['token'],
                'event'     => 'signIn',
                'date'      => $this->now_day
            ],false);

            $sign_in_time = 99999999999;
            foreach ($rows as $row){
                if($sign_in_time > $row->created_at){
                    $sign_in_time = $row->created_at;
                }
            }

            if($sign_in_time == 99999999999){
                return [
                    'code'      => 9001,
                    'message'   => '今日尚未签到'
                ];
            }

            $temp_dbmanager = new DataBaseManager('signinsystem','token',$this->recv['token']);
            $row = $temp_dbmanager->find();
            $total_work_time = $this->timestamp - $sign_in_time;
            if($row->totalWorkTime != null){
                $total_work_time += intval($row->totalWorkTime);
            }
            $temp_dbmanager->write([
                'totalWorkTime' => strval($total_work_time)
            ]);

            if(!isset($this->recv['declaration'])){
                $this->recv['declaration'] = '充实的一天~';
            }

            $this->dbmanager->write([
                'token'         => $this->recv['token'],
                'event'         => 'signOut',
                'created_at'    => $this->timestamp,
                'date'          => $this->now_day,
                'declaration'   => $this->recv['declaration'],
                'photoUrl'      => $this->recv['photoUrl']
            ],true);

            return [
                'code'      => 0,
                'message'   => 'ok'
            ];

        }catch(\Exception $e){
            return [
                'code'      => 8001,
                'message'   => '服务器错误'
            ];
        }
    }

}