<?php
/**
 * Created by PhpStorm.
 * User: elati
 * Date: 2018/7/26
 * Time: 13:47
 */

namespace App\SMSSender\qcloudSMSSender;

use Qcloud\Sms\SmsSingleSender;

class SMSSender
{
    /**
     * @var array   key:event   value:.env中的常量
     */
    private $modelids = [
        'signup'    =>  'QCLOUDSMS_MODEL_ID_SIGNUP',
    ];

    /**
     * 如果.env中没有请在最后一个变量($model)中直接填modelid
     * @param array $message
     * @param string $phoneNumber
     * @param string $model
     * @return array|mixed
     */
    public function send(Array $message,string $phoneNumber,string $model){
        try{
            $appid      = env('QCLOUDSMS_APP_ID');
            $appkey     = env('QCLOUDSMS_APP_KEY');

            if(!isset($this->modelids[$model])){
                $modelid = $model;
            }else{
                $modelid = env($this->modelids[$model]);
            }

            $sender = new SmsSingleSender($appid, $appkey);
            $result = json_decode($sender->sendWithParam('86',$phoneNumber, $modelid, $message,'','',''),true);
            return array_merge($result,['status' => '0']);
        }catch(\Exception $e){
            return [
                'status'    => '1',
                'message'   => '发送短信错误'.$e->getMessage(),
            ];
        }
    }
}