<?php
namespace app\index\model;
use think\Model;

class Article extends Model
{
    public function category()
    {
        return $this->hasOne('category','id','category_id');
    }

    public function comment()
    {
        return $this->hasMany('comment','article_id','id');
    }


}