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
     * @param string $target
     * @param null $where
     * @return array
     */
    public function write(array $data, string $target = '', $where = null){
        try{

            if($this->target != null && $target == ''){
                $target = $this->target;
            }

            if($this->where != null && $where == null){
                $where = $this->where;

            }

            $temp = $this->find($target,$where);
            if($temp['status'] == '0'){
                $row = $temp['result'];
            }else{
                return $temp;
            }

            if($row == null){
                DB::table($this->table)->insert($data);
            }else{
                DB::table($this->table)->where($where, '=', $target)->update($data);
            }

            return ['status'    => '0'];

        }catch (\Exception $e){
            return [
                'status'    => '1',
                'errorlog'  => 'write table error'.$e->getMessage()
            ];
        }
    }

    /**
     * @param string $target
     * @param string|null $where
     * @param bool $one_row
     * @return array
     */
    public function find(string $target = '',string $where = null, bool $one_row = true){
        try{

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

            return [
                'status'    => '0',
                'result'    => $row
            ];
        }catch (\Exception $e){
            return [
                'status'    => '1',
                'errorlog'  => 'find data error: '.$e->getMessage(),
            ];
        }
    }

}