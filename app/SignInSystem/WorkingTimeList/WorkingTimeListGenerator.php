<?php

namespace App\SignInSystem\WorkingTimeList;

use App\DataBaseManager\DataBaseManager;

class WorkingTimeListGenerator
{
    /**
     * @var DataBaseManager
     */
    private $dbmanager;

    /**
     * 今天的日期,Y-m-d
     *
     * @var string
     */
    private $today;

    /**
     * @var array
     */
    private $recv;

    public function __construct(array $recv)
    {
        $this->dbmanager = new DataBaseManager(env('SIGNINSYSTEM_SIGN_INFO_TABLE'));
        $this->today = date('Y-m-d');
        $this->recv = $recv;
    }


    public function handle(){
        try{
            $sign_out_times = $this->dbmanager->multi_where_find([
                'date'  => $this->today,
                'event' => 'signOut'
            ],false);

            $user_dbmanager = new DataBaseManager(env('SIGNINSYSTEM_USER_INFO_TABLE'));

            $data = [];
            $had = [];
            foreach ($sign_out_times as $row){
                $token = $row->token;
                if(in_array($token, $had)){
                    continue;
                }
                array_push($had,$token);

                $sign_in_time = $this->dbmanager->multi_where_find([
                    'token' => $token,
                    'date'  => $this->today,
                    'event' => 'signIn'
                ])->created_at;

                $data []= [
                    'workingTime'   => ($row->created_at - $sign_in_time),
//                    'photoUrl'      => $row->photoUrl,
//                    'declaration'   => $row->declaration,
                    'userName'      => $user_dbmanager->find($token,'token')->userName,
                    'timestamp'     => $row->created_at
                ];
            }

            $working_times = [];
            foreach ($data as $val){
                $working_times []= $val['workingTime'];
            }
            array_multisort($working_times,SORT_DESC,SORT_NUMERIC, $data);

            array_slice($data,0,100);

            return [
                'code'      => 0,
                'message'   => 'ok',
                'data'      => $data
            ];
        }catch(\Exception $e){
            return [
                'code'      => 8001,
                'message'   => '服务器错误',
            ];
        }
    }
}