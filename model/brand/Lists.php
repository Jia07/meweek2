<?php

namespace app\admin\model\brand;

use basic\ModelBasic;
use traits\ModelTrait;

class Lists extends ModelBasic
{
    use ModelTrait;

    protected $table = 'eb_brand';

    public static function getAll()
    {
        return self::order('id desc,name desc')->field(['name', 'id'])->select();
    }

    public static function getAllList($where)
    {
        $data = self::setWhere($where)->page((int)$where['page'], (int)$where['limit'])->select();
        $count = self::setWhere($where)->count();
        return compact('data', 'count');
    }

    public static function setWhere($where)
    {
        $model = self::order('id desc,name desc');
        if ($where['name'] != '') $model = $model->where('name', 'like', "%$where[name]%");
        if ($where['cid'] != '') $model = $model->where('id', $where['cid']);
        return $model;
    }
}
