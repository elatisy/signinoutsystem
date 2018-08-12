<?php

namespace App\SignInSystem\LogOut;

use App\DataBaseManager\DataBaseManager;
use App\SignInSystem\Auth\AuthProcessor;

class LogOutHandler
{
    /**
     * @var DataBaseManager
     */
    private $dbmanager;

    /**
     * @var array
     */
    private $recv;

    public function __construct($recv)
    {
        $this->dbmanager = new DataBaseManager($recv['table'],'token',$recv['token']);
        $this->recv = $recv;
    }

    public function handle(){
        try{
            $authprocessor = new AuthProcessor();
            $res = $authprocessor->auth($this->recv);
            if($res['status']){
                $this->dbmanager->write([
                    'is_log_in' => false
                ]);
            }

            return $res['result'];
        }catch(\Exception $e){
            return [
                'code'      => 8001,
                'message'   => '服务器错误'
            ];
        }
    }
}