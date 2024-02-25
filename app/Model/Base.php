<?php

declare(strict_types=1);
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
use function Hyperf\Config\config;

/**
 * 创建model:
 * php bin/hyperf.php gen:model table_name
 */
abstract class Base extends Model
{
    const STATUS_1 = 1;
    const STATUS_2 = 2;

    const STATUS_TEXT = [
        self::STATUS_1 => '正常',
        self::STATUS_2 => '锁定',
    ];

    protected $limit = 0;
    protected $formatTree = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->limit = 8;//(int)setting('limit_page')
    }

    /**
     * 获取上一篇
     * @param $id
     * @return \Hyperf\Database\Model\Model|\Hyperf\Database\Query\Builder|object|null
     */
    protected function prev($id){
        return $this->select(['*'])->where('status', self::STATUS_1)->where('id','>', $id)->first();
    }

    /**
     * 获取下一篇
     * @param $id
     * @return \Hyperf\Database\Model\Model|\Hyperf\Database\Query\Builder|object|null
     */
    protected function next($id){
        return $this->select(['*'])->where('status', self::STATUS_1)->where('id','<', $id)->first();
    }

    /**
     * 更新或者新增
     * @param $data
     * @return bool
     */
    protected function renew($data = []){
        if($data['id']){
            $info = $this->findOrFail($data['id']);
            return  $info->update($data);
        }
        return $this->create($data);
    }

    /**
     * 批量更新数据
     * @param $ids
     * @param $data
     * @return int
     */
    protected function store($ids = [], $data = []){
        return $this->whereIn('id', $ids)->update($data);
    }

    /**
     * 详情
     * @param $id
     * @param $fields
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Builder[]|\Hyperf\Database\Model\Collection|\Hyperf\Database\Model\Model|null
     */
    protected function info($id = 0, $fields = ['*']){
        return $this->find($id, $fields);
    }

    /**
     * 键值集合
     * @param $params 筛选条件
     * @param $field 集合值
     * @param $key 集合键
     * @param $order 排序字段
     * @param $sort 排序
     * @return \Hyperf\Collection\Collection
     */
    protected function tree($params = [], $field = 'title', $key = 'id', $order = 'id', $sort = 'asc'){
        return $this->where(function ($query) use ($params) {
            if (isset($params['status']) && $params['status']) {
                $query->where('status', $params['status']);
            }
            if (isset($params['parent_id']) && $params['parent_id']) {
                $query->where('parent_id', $params['parent_id']);
            }
            if (isset($params['time']) && $params['time']) {
                $query->where('time', '<>' , '');
            }
            if (isset($params['id']) && $params['id']) {
                $query->where('id', '>', $params['id']);
            }
        })->orderBy($order, $sort)->pluck($field, $key);
    }

    /**
     * 查重
     * @param $field
     * @param $params
     * @return \Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|null
     */
    protected function repeat($field = '', $params = []){
        return $this->where('id', '<>', $params['id'])
            ->where($field, $params[$field])
            ->first();
    }

    /**
     * 判断是否有子节点
     * @param $pid
     * @return bool
     */
    protected function child($pid = 0){
        $child = $this->where('parent_id',$pid)->first();
        if($child){
            return true;
        }
        return false;
    }


    /**
     * 将格式数组转换为树
     * @param $list
     * @param string $title
     * @param string $pk
     * @param string $pid
     * @param int $root
     * @return mixed
     */
    protected function toFormatTree($list, $title = 'title', $pk = 'id', $pid = 'pid', $root = 0)
    {
        $list = list_to_tree($list, $pk, $pid, '_child', $root);
        $this->_toFormatTree($list, 0, $title);
        return $this->formatTree;
    }

    /**
     * 将格式数组转换为树
     * @param $list
     * @param int $level
     * @param string $title
     */
    protected function _toFormatTree($list, $level = 0, $title = 'title')
    {
        foreach ($list as $val) {
            $tmp_str = str_repeat("&nbsp;", $level * 4);
            $tmp_str .= "╚═";
            $val['level'] = $level;
            $val['title_show'] = $level == 0 ? $val[$title] : $tmp_str . $val[$title];
            if (!array_key_exists('_child', $val)) {
                array_push($this->formatTree, $val);
            } else {
                $tmp_ary = $val['_child'];
                unset($val['_child']);
                array_push($this->formatTree, $val);
                $this->_toFormatTree($tmp_ary, $level + 1, $title); //进行下一层递归
            }
        }
    }

    /**
     * 获取排序相关
     * @param $params
     * @return array
     * @throws \Exception
     */
    protected function orderSort($params){
        $sort = 'id';
        $by = 'desc';
        if (isset($params['sort']) && $params['sort']) {
            $sort = $params['sort'];
        }
        if (isset($params['order']) && $params['order']) {
            $order = $params['order'];
            $by = str_replace("ending", "", $order);
        }
        if(!in_array($sort, $this->fillable)){
            throw new \Exception('字段非法');
        }
        if(!in_array($by, ['asc', 'desc'])){
            throw new \Exception('排序非法');
        }
        return [
            'sort' => $sort,
            'by' => $by
        ];
    }

    /**
     * 自动增加或者减少
     * @param $field
     * @param $type 0减少 1增加
     * @param $id
     * @param $number
     * @return int
     */
    protected function matic($field = '', $id = 0, $type = 1, $number = 1){
        if($type){
            $this->where('id', $id)->increment($field, $number);
        }else{
            $this->where('id', $id)->decrement($field, $number);
        }
    }
}
