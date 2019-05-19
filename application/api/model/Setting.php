<?php


namespace app\api\model;

use think\Model;
use think\facade\Request;
use think\facade\Session;
use think\model\Collection;
use think\db;


class Setting extends Model
{
    public function systemPowerSwitch($handleValue){
        if($handleValue==null){
            $handleValue=true;
        }
        $setting=new Setting();
        //var_dump($handleValue);
        $switchResult=$setting->save([
            'meta_value'=>$handleValue
        ],['meta_key'=>'online']);
        if($switchResult==1){
            $result=['switchResult'=>'1'];
            return $result;
        }else{
            $result=['switchResult'=>'0','errMsg'=>'系统状态无变化'];
            return $result;
        }
    }
    public function checkSystemOnline(){
        $setting=new Setting();
        return $setting->where('meta_key','online')->value('meta_value');
}
}