<?php
namespace app\admin\controller;

use think\Db;
use think\Controller;

define("URL","http://www.miizi.cn/edu/");
/**
 * 讲师部分
 */
class Lecturer extends Controller
{
	//主页显示
	public function index(){
		return view();
	}
	//讲师列表
	public function lecturerLsit(){
		$map = [];
		$lecturername = input("lecturername");
    	if($lecturername){ $map['lecturername']  = ['like','%'.$lecturername.'%']; }
        input("lecturerphone")?$map['lecturerphone'] = input("lecturerphone"):'';
        $pageIndex=input("page");//开始位置
        $pageSize=input("limit");//每页查询几条 
        $pageStart=(($pageIndex-1)*$pageSize);//开始位置    
        $data = Db::table('lecturer')->where($map)->limit($pageStart,$pageSize)->select();
        $count = Db::table('lecturer')->where($map)->count('lecturerid');
        $json = ['code'=>0,'msg'=>'','count'=>$count,"data"=>$data];
        return json($json);        
	}
	
}