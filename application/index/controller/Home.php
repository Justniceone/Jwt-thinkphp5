<?php
namespace app\index\controller;

use extend\jwt\JWT;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use think\Controller;
use think\Cookie;
use think\Db;
use think\Log;
use think\Session;
use think\Validate;

class Home extends Controller
{
    public function Login()
    {
        if($this->request->isPost())
        {
            $username = $this->request->post('username');
            $password = $this->request->post('password');
            $validate = new Validate([
                'username'  => 'require|max:25',
                'password' => 'require|min:3'
            ]);
            $data = [
                'username'  => $username,
                'password' => $password
            ];
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            $user = Db::name('users')->where(['username'=>$username,'password'=>$password])->find();
            if(!$user)  $this->error('用户名或密码错误');
            //判断是否自动登录
            if($this->request->post('remember'))
            {
                Cookie::set('id',$user['id'],86400);
                Cookie::set('password',md5($user['password']),86400);
            }
            Session::set('info',['id'=>$user['id'],'username'=>$user['username']]);
            $this->success('登录成功','/index/article/lists');
        }
        return $this->fetch('form');
    }

    public function logout()
    {
        Session::delete('info',null);
        Cookie::delete('id');
        Cookie::delete('password');
        $this->success('注销成功','/index/home/login');
    }

    public function token()
    {
        $payload = ['user_id'=>1,'is_admin'=>0,'exp'=>time()+ 86400];
        $key = 'awdagrsjgshgkdawfadsht';
        $token = JWT::encode($payload,$key);
        var_dump($token);
    }

    public function upload()
    {
        if($this->request->isPost())
        {
            //先上传到服务器再上传到七牛云
            $file = $_FILES['img'];
            $key = 'bp_'.time();
            $accessKey = 'gEaQS_5EWRYAAuz7nZc9plt40jRRb6HU7MI0aXwh';
            $secretKey = 'ABWGH-ma6kW55zcfwgzPxQJ9KUe_PQDXBn3ImW6q';
            $auth = new Auth($accessKey, $secretKey);
            $bucket = 'bopang';
            $link = 'http://ovy9vleun.bkt.clouddn.com/';
            // 生成上传Token
            $token = $auth->uploadToken($bucket);

            // 构建 UploadManager 对象
            $uploadMgr = new UploadManager();
            try
            {
                list($ret,$err) = $uploadMgr->putFile($token,$key,$file['tmp_name']);
                if(isset($ret['key']))
                {
                    return json(['code'=>200,'msg'=>'','data'=>['url'=>$link.$ret['key']]]);
                }
            }catch (\Exception $e)
            {
                Log::write('uploaded to qiniu failed at'.date('Y-m-d H:i:s'));
            }
            return json(['code'=>500,'msg'=>'上传失败','data'=>[]]);
        }
        return $this->fetch('upload');
    }

    public function reg()
    {
        $str = file_get_contents('http://edu.51cto.com/t/exam/list/id-25.html');
        //preg_match_all('/<ul class="subNav">([\s\S]*?)<\/ul>/m',$str,$subject);
        preg_match_all('/<ul class="NavList">([\s\S]*?)<\/ul>\n<\/div> /',$str,$subject);
        preg_match_all('/<p>(.*?)<\/p>/',$subject[0][0],$master);
        var_dump($master);
    }
}