<?php

namespace App\SignInSystem\SignOutList;

use App\DataBaseManager\DataBaseManager;

class SignOutListGenerator
{
    /**
     * @var DataBaseManager
     */
    private $dbmanager;

    /**
     * @var array
     */
    private $recv;

    private $today;

    public function __construct($recv){
        $this->dbmanager = new DataBaseManager(env('SIGNINSYSTEM_SIGN_INFO_TABLE'));
        $this->recv = $recv;
        $this->today = date('Y-m-d');
    }

    public function handle(){
        try{
            $rows = $this->dbmanager->multi_where_find([
                        'date'      => $this->today,
                        'event'     => 'signOut'
                        ],false);

            $data = [];
            $had = [];
            $user_dbmanager = new DataBaseManager(env('SIGNINSYSTEM_USER_INFO_TABLE'));
            foreach ($rows as $row){
                $token = $row->token;

                if(in_array($token, $had)){
                    continue;
                }
                $had []= $token;

                $data []= [
                    'userName'      => $user_dbmanager->find($token,'token')->userName,
                    'timestamp'     => $row->created_at,
                    'declaration'   => $row->declaration,
                    'photoUrl'      => $row->photoUrl
                ];
            }

            $key_array = [];
            foreach ($data as $val){
                $key_array []= $val['timestamp'];
            }
            array_multisort($key_array, SORT_DESC, SORT_REGULAR, $data);

            array_slice($data,0,100);

            return [
                'code'      => 0,
                'message'   => 'ok',
                'data'      => $data
            ];
        }catch (\Exception $e){
            return [
                'code'      => 8001,
                'message'   => '服务器错误',
            ];
        }
    }
}