<?php
namespace app\index\controller;

use app\index\model\Tag;
use think\Db;

class Article extends Base
{
    public function lists()
    {
        $lists = \app\index\model\Article::all();

        return $this->fetch('lists',['lists'=>$lists]);
    }

    public function detail($id)
    {
        //点击量加1
        $list = \app\index\model\Article::get($id);
        $list->setInc('view');
        //$tag = Tag::get($id);
        $sql = 'select a.*,group_concat(t.tname) as tag_name from article a join art_tag  `at` on a.id = `at`.aid join tag t on t.id = `at`.tid  where a.id ='.$id.'  group by a.id ';
        $tags = Db::query($sql);
        return $this->fetch('detail',['list'=>$list,'tags'=>$tags]);
    }
}