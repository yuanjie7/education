<?php
namespace app\index\controller;

use think\Db;
class Index
{
    public function index()
    {
      //return 'Index'; 
       
      //查询数据
      $data = Db::table('admin_user')->select();
      return view();
    }

}
