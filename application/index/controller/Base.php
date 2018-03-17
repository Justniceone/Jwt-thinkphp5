<?php
namespace app\index\controller;

use extend\jwt\JWT;
use extend\jwt\SignatureInvalidException;
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

    public function decode()
    {
        $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjoxLCJpc19hZG1pbiI6MCwiZXhwIjoxNTIxMjcyNDk3fQ.h1DMSjNNYyUnR95-EAlONl33vkBzFahLYGe-u8-oBSk";

        try {
            $user = JWT::decode($token, 'awdagrsjgshgkdawfadsht', ['HS256']);
            var_dump($user);
        }catch (SignatureInvalidException $e)
        {
            echo '签名认证失败';
        }catch (\UnexpectedValueException $e)
        {
            echo '不支持的签名格式';
        }
    }
}