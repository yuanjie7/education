<?php
namespace app\index\controller;

use think\Db;
use think\Controller;
/**
 * 接口--课程部分
 */ 
define("URL","http://www.miizi.cn/edu/");
class Course extends Controller
{
	//全部课程列表
	public function allCourseList()
		$list = Db::table('coursebook')
				->field('courseid,coursecover,coursename,clicknum')
		        ->select();
		if(!$list){
			return json(['code'=>1,'count'=>0,'msg'=>'暂无数据','result'=>'']);
		} 
		return json(['code'=>0,'count'=>count($list),'msg'=>'查询成功','result'=>$list]);         
	}
	//课程信息
	public function courseInfo(){
		$ma1['courseid'] = $ma2['bookid'] = input('courseid');
		if(!$ma1['courseid']){
			return json(['code'=>1,'count'=>0,'msg'=>'请传入参数:[courseid]','result'=>'']);
		}
		$info = Db::table('coursebook')
				->field('courseid,coursecover,coursename,introduction,belongedto')
				->where($ma1)
				->find();
		//章节目录		
		$info['catalog'] = Db::table('coursechapter')
			               ->field('chapterid,chaptername')
						   ->where($ma2)
						   ->select();		
		if(!$info){
			return json(['code'=>1,'count'=>0,'msg'=>'暂无数据','result'=>'']);
		} 
		$info['coursecover'] = URL.$info['coursecover']; 
		return json(['code'=>0,'count'=>count($info),'msg'=>'查询成功','result'=>$info]);		
	}
	//章节详情
	public function courseView(){
		$ma1['chapterid'] = input('chapterid');
		if(!$ma1['chapterid']){
			return json(['code'=>1,'count'=>0,'msg'=>'请传入参数:[chapterid]','result'=>'']);
		}
		$info = Db::table('coursechapter')
		        ->field('belongedto,chaptername,coursevideo,coursehandout,bookid')
		        ->where($ma1)
		        ->find();
		$ma2['bookid'] = $info['bookid'];       
		$img = explode('|',$info['coursehandout']); 
		foreach ($img as $k => $v) {
       		$img[$k] = URL.$v;
        } 
        $result['belongedto'] = $info['belongedto'];
        $result['chaptername'] = $info['chaptername'];
        $result['coursevideo'] = URL.$info['coursevideo'];      
        $result['coursehandout'] = $img; 
        $result['catalog'] = Db::table('coursechapter')
			                ->field('chapterid,chaptername')
						    ->where($ma2)
						    ->select();
		if(!$result){
			return json(['code'=>1,'count'=>0,'msg'=>'暂无数据','result'=>'']);
		} 
		return json(['code'=>0,'count'=>count($result),'msg'=>'查询成功','result'=>$result]);        
	}
	//
}