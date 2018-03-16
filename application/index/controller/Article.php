<?php
namespace app\index\controller;

use think\Db;

class Article extends Base
{
    public function lists()
    {
        $lists = Db::name('articles')->select();
        return $this->fetch('lists',['lists'=>$lists]);
    }
}