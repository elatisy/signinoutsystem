<?php


namespace App\DataBaseManager;

use Illuminate\Support\Facades\DB;

/**
 * 负责数据库的增删查改
*/
class DataBaseManager
{
    private $table  = '';
    private $where  = null;
    private $target = null;
    public function __construct(string $table, string $where = null, string $target = null)
    {
        $this->table    = $table;
        $this->where    = $where;
        $this->target   = $target;
    }

    /**
     * 如果没有传入参数$where或者$where为null则使用私有成员$this->where
     * 如果私有成员也为null则默认为插入数据
     * @param array $data
     * @param bool $force_insert
     * @param string $target
     * @param string $where
     * @return boolean
     */
    public function write(array $data, bool $force_insert = false, string $target = '', $where = null){

        if($force_insert){
            DB::table($this->table)->insert($data);
            return true;
        }

        if($this->target != null && $target == ''){
            $target = $this->target;
        }

        if($this->where != null && $where == null){
            $where = $this->where;
        }

        try{
            $row = $this->find($target,$where);
        }catch(\Exception $e){
            return false;
        }

        if($row == null){
            DB::table($this->table)->insert($data);
        }else{
            DB::table($this->table)->where($where, '=', $target)->update($data);
        }

        return true;
    }

    /**
     * @param string $target
     * @param string|null $where
     * @param bool $one_row
     * @return object
     */
    public function find(string $target = '',string $where = null, bool $one_row = true){
        if($this->target != null && $target == ''){
            $target = $this->target;
        }

        if($where == null){
            $where = $this->where;
        }

        if($where == null){
            $row = null;
        }else{
            if($one_row){
                $row = DB::table($this->table)->where($where, '=', $target)->first();
            }else{
                $row = DB::table($this->table)->where($where, '=', $target)->get();
            }
        }
        return $row;
    }

}