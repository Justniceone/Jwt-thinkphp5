<?php
namespace app\index\model;

use think\Model;

class Tag extends Model
{
    public function article()
    {
        return  $this->belongsToMany('Article','art_tag','tid','id');
    }
}