<?php

namespace App\SignInSystem;

use App\SignInSystem\SignUp\SignUpHandler;
use App\SignInSystem\LogIn\LogInHandler;
use App\SignInSystem\LogOut\LogOutHandler;
use App\SignInSystem\SignIn\SignInHandler;
use App\SignInSystem\SignOut\SignOutHandler;
use App\SignInSystem\WorkingTimeList\WorkingTimeListGenerator;

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

        if($judge_res['code'] != 0){
            return $judge_res;
        }

        if($recv['event'] == 'signUp'){
            $signup = new SignUpHandler($recv);
            $res = $signup->signUp();

        } elseif ($recv['event'] == 'getAuthCode'){
            $signup = new SignUpHandler($recv);
            $res = $signup->getAuthCode();

        } elseif ($recv['event'] == 'logIn'){
            $login = new LogInHandler($recv);
            $res = $login->handle();

        } elseif ($recv['event'] == 'logOut'){
            $logout = new LogOutHandler($recv);
            $res = $logout->handle();

        } elseif ($recv['event'] == 'signIn'){
            $recv['table'] = 'declarations';
            $signin = new SignInHandler($recv);
            $res = $signin->handle();

        } elseif ($recv['event'] == 'signOut'){
            $recv['table'] = 'declarations';
            $signout = new SignOutHandler($recv);
            $res = $signout->handle();

        }elseif ($recv['event'] == 'workingTimeList'){
            $workingtimegenerator = new WorkingTimeListGenerator($recv);
            $res = $workingtimegenerator->handle();

        }else {
            $res = [
                'code'      => '1002',
                'message'   => '未知event'
            ];
        }

        return $res;

    }
}