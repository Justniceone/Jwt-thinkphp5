<?php
namespace app\index\controller;

use think\Controller;
use think\Cookie;
use think\Db;
use think\Request;
use think\Session;

class Base extends Controller
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        if ( !Session::get('info'))
        {
            //验证cookie中的登录信息
            if(!Cookie::get('id') || !Cookie::get('password'))
            {
                $this->error('请先登录','/index/home/login');
            }else
            {
                //判断是否正确
                $user = Db::name('users')->where(['id'=>Cookie::get('id')])->find();
                if($user)
                {
                    //对比密码
                    if(Cookie::get('password') == md5($user['password']))
                    {
                        //密码正确,保持登录信息到session
                        Session::set('info',['id'=>$user['id'],'username'=>$user['username']]);
                        $this->success('自动登录成功');
                    }
                }
                $this->error('自动登录失败,请重新登录','/index/home/login');
            }
        }
    }
}