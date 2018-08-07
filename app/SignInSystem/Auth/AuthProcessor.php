<?php

namespace App\SignInSystem\Auth;

use App\DataBaseManager\DataBaseManager;

class AuthProcessor
{
    public function auth($recv){
        $dbmanager = new DataBaseManager('signinsystem','token',$recv['token']);
        $row = $dbmanager->find();
        if($row == null){
            return [
                'status' => false,
                'result' => [
                        'code'      => 3004,
                        'message'   => '未知token'
                    ]

            ];
        }

        if($row->account != $recv['account']){
            return [
                'status' => false,
                'result' => [
                    'code'      => 3005,
                    'message'   => 'token和账号不匹配'
                ]
            ];
        }

        return [
            'status' => true,
            'result' => [
                'code'      => 0,
                'message'   => 'ok'
            ]
        ];
    }
}