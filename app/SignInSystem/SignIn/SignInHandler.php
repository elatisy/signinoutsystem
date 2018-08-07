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

    public function __construct($recv)
    {
        $this->dbmanager = new DataBaseManager($recv['table'],'token',$recv['token']);
        $this->authprocessor = new AuthProcessor();
        $this->recv = $recv;
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

            if(!isset($this->recv['declaration'])){
                $this->recv['declaration'] = '早起的鸟儿有虫吃~';
            }

            $this->dbmanager->write([
                'token'         => $this->recv['token'],
                'date'          => date('Y_m_d'),
                'declaration'   => $this->recv['declaration'],
                'event'         => 'signIn',
                'created_at'    => intval(Carbon::now()->timestamp)
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