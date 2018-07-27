<?php

namespace App\SignInSystem;

use App\SignInSystem\SignUp\SignUpHandler;

class Hub
{
    private $table = 'signinsystem';

    /**
     * 事件识别,服务分发以及数据合理判断中心
     * @param $recv array 来自Controller的$request->all()
     * @return array
    */
    public function handle(array $recv){
        $recv = array_merge($recv,['table'  =>  $this->table]);

        $judger = new Judger();
        $judge_res = $judger->judge($recv);

        if($judge_res['code'] != '0'){
            return $judge_res;
        }

        if($recv['event'] == 'signUp'){
            $signup = new SignUpHandler($recv);
            $res = $signup->signUp();
            return $res;
        } elseif ($recv['event'] == 'getAuthCode'){
            $signup = new SignUpHandler($recv);
            $res = $signup->getAuthCode();
            return $res;
        }else{
            return [
                'code'      => '1002',
                'message'   => '未知event'
            ];
        }
    }
}