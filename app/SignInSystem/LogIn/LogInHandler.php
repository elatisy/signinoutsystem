<?php

namespace App\SignInSystem\LogIn;

use App\DataBaseManager\DataBaseManager;

class LogInHandler
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
        $this->dbmanager = new DataBaseManager($recv['table'],'account',$recv['account']);
        $this->recv = $recv;
    }

    public function handle(){
        try{
            $row = $this->dbmanager->find();
            if($row != null){
                if($row->password == $this->recv['password']){
                    $this->dbmanager->write([
                        'is_log_in' => true,
                    ]);
                    return [
                        'code'      => 0,
                        'message'   => 'ok',
                        'token'     => $row->token
                    ];
                }
            }

            return [
                'code'      => 7002,
                'message'   => '账号不存在或者密码错误'
            ];
        }catch (\Exception $e){
            return [
                'code'      => 8001,
                'message'   => '服务器错误'
            ];
        }
    }
}