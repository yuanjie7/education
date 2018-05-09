<?php
namespace app\admin\controller;

use think\Db;
use think\Controller;

define("URL","http://www.miizi.cn/edu/");
class Course extends Controller
{
	/**
	 * 课程管理分类
	 */
	public function course_classification(){
		return view();
	}
    //查询分类数据
    public function selectClassInfo(){
    	$map = [];
    	$classname = input("classname");
    	if($classname){ $map['classname']  = ['like','%'.$classname.'%']; }
    	$pageIndex=input("page");//开始位置
        $pageSize=input("limit");//每页查询几条 
        $pageStart=(($pageIndex-1)*$pageSize);//开始位置 	
    	$data = Db::table('courseclassify')->where($map)->limit($pageStart,$pageSize)->select();
    	$count = Db::table('courseclassify')->where($map)->count('id');
    	$json = ['code'=>0,'msg'=>'','count'=>$count,"data"=>$data];
    	return json($json);
    }
    //新增分类页面
    public function class_add(){
    	//渲染模板
    	return view();
    }
    //新增分类处理
    public function classDoAdd(){
    	$data['classname'] = input("classname");
    	$result = Db::table('courseclassify')->insert($data);
    	if($result){
    		$json = ['code'=>0,'msg'=>$result,'result'=>'操作成功'];
    	}else{
    		$json = ['code'=>1,'msg'=>$result,'result'=>'操作失败'];
    	}
    	return json($json);
    }
    //查询分类详细信息
    public function classDetail(){
    	$map['id'] = input("id");
    	$result = Db::table('courseclassify')->where($map)->find();
    	if($result){
    		$json = ['code'=>0,'msg'=>$result,'result'=>'查询成功'];
    	}else{
    		$json = ['code'=>1,'msg'=>$result,'result'=>'暂无数据，查询失败'];
    	}
    	return json($json);
    }
    //修改分类信息
    public function classDoEdit(){
    	$map['id'] = input("id");
    	$data['classname'] = input("classname");
    	$result = Db::table('courseclassify')->where($map)->update($data);
    	if($result){
    		$json = ['code'=>0,'msg'=>$result,'result'=>'操作成功'];
    	}else{
    		$json = ['code'=>1,'msg'=>$result,'result'=>'操作失败'];
    	}
    	return json($json);
    }
	/**
	 * 课程管理
	 */
	public function course_management(){
		//查询分类数据
        $list = Db::table('courseclassify')->select();
        $this->assign("list",$list);
        //查询数据
        $result = $this->courseselect();
        $this->assign("result",$result);
        //渲染模板
		return view();
	}
	//查询课程数据
	public function courseselect(){
		$arr = Db::table('courseclassify')->field('id,classname')->select();
		foreach ($arr as $k => $v) {
			$ma1['classifyid'] = $v['id'];
			$arr[$k]['info'] = Db::table('coursebook')
							   ->field('courseid,coursename,belongedto')
							   ->where($ma1)
							   ->select();
			foreach ($arr[$k]['info'] as $key => $val) {
				$ma2['bookid'] = $val['courseid'];
		   		$arr[$k]['info'][$key]['chapter'] = Db::table('coursechapter')
		   											->field('chapterid,chaptername,belongedto')
												    ->where($ma2)
												    ->select();
		    }				   				   
		}
		return $arr;
		/*$result = Db::table('course')
				  ->alias('a')
				  ->join('courseclassify b','a.classifyid = b.id')
				  ->field('a.id,a.coursename,a.belongedto,b.classname')
				  ->select();*/
		//print_r($list_one);		  
	}
	//查询对应分类下的课程
	public function courselist(){
		$map['classifyid'] = input("id");
		$result = Db::table('coursebook')->field('id,coursename')->where($map)->select();
		if($result){
    		$json = ['code'=>0,'msg'=>$result,'result'=>'查询成功'];
    	}else{
    		$json = ['code'=>1,'msg'=>$result,'result'=>'暂无数据'];
    	}
    	return json($json);
	}
	//课程新增
	public function courseadd(){
		$data = $_POST;
		$flag = $data['flag'];
		unset($data['flag']);
		//新增课程
		if($flag==1){
			$result = Db::table('coursebook')->insert($data);
		}
		//新增章节
		if($flag==2){
			$result = Db::table('coursechapter')->insert($data);
		}
		if($result){
            $json = ['code'=>0,'msg'=>$result,'result'=>'操作成功'];
        }else{
            $json = ['code'=>1,'msg'=>$result,'result'=>'操作失败'];
        }
        return json($json);
	}
    //课程修改
    public function courseedit(){
        $data = $_POST;
        $map['courseid'] = $data['courseid'];
        if(isset($data['coursecover'])){
            $oldlogo = Db::table('coursebook')->field('coursecover')->where($map)->find(); 
            $del = ROOT_PATH.$oldlogo['coursecover']; 
        }
        $result = Db::table('coursebook')->where($map)->update($data);
        if($result){
            isset($del)?unlink($del):'';
            $json = ['code'=>0,'msg'=>$result,'result'=>'操作成功'];
        }else{
            $json = ['code'=>1,'msg'=>$result,'result'=>'操作失败'];
        }
        return json($json);
    }
    //查询封面简介
    public function search_one(){
        $map['courseid'] = input('courseid');
        $info = Db::table('coursebook')
                ->field('coursename,coursecover,introduction')
                ->where($map)
                ->find();
        $info['coursecover'] = URL.$info['coursecover'];     
        return json(['code'=>0,'msg'=>$info,'result'=>'操作成功']);        
    }
    //查询章节视频讲义
    public function search_two(){
        $map['chapterid'] = input('chapterid');
        $info = Db::table('coursechapter')
                ->field('coursevideo,coursehandout')
                ->where($map)
                ->find();
        $info['coursevideo'] = URL.$info['coursevideo'];         
        $info['coursehandout'] = explode("|",$info['coursehandout']);
        foreach ($info['coursehandout'] as $k => $v) {
           $info['coursehandout'][$k] = URL.$v;
        }       
        return json(['code'=>0,'msg'=>$info,'result'=>'操作成功']);        
    }
	//上传封面
    public function uploadCover(){
        // 获取表单上传文件
        $file = request()->file('file');
        if(empty($file)){
           return json(['code'=>1,'msg'=>'','result'=>'请选择文件上传']); 
        }
        // 移动到框架应用根目录/public/uploads/coursecover/目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/coursecover');
        if($info){
            // 输出保存路径
            $path = 'public/uploads/coursecover/'.$info->getSaveName();
            // 成功上传后 返回上传信息
            return json(['code'=>0,'msg'=>$path,'result'=>'上传成功']);
        }else{
            // 上传失败返回错误信息
            return json(['code'=>1,'msg'=>'','result'=>'上传失败']);
        }     
    }
    //上传讲义
    public function uploadHandout(){
        // 获取表单上传文件
        $file = request()->file('file');
        if(empty($file)){
           return json(['code'=>1,'msg'=>'','result'=>'请选择文件上传']); 
        }
        // 移动到框架应用根目录/public/uploads/coursehandout/目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/coursehandout');
        if($info){
            // 输出保存路径
            $path = 'public/uploads/coursehandout/'.$info->getSaveName();
            // 成功上传后 返回上传信息
            return json(['code'=>0,'msg'=>$path,'result'=>'上传成功']);
        }else{
            // 上传失败返回错误信息
            return json(['code'=>1,'msg'=>'','result'=>'上传失败']);
        }     
    }
    //上传视频
    public function uploadVideo(){
        $file = request()->file('file');
        if(empty($file)){
           return json(['code'=>1,'msg'=>'','result'=>'请选择文件上传']); 
        }
        // 移动到框架应用根目录/public/uploads/coursevideo/目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/coursevideo');
        if($info){
            // 输出保存路径
            $path = 'public/uploads/coursevideo/'.$info->getSaveName();
            // 成功上传后 返回上传信息
            return json(['code'=>0,'msg'=>$path,'result'=>'上传成功']);
        }else{
            // 上传失败返回错误信息
            return json(['code'=>1,'msg'=>'','result'=>'上传失败']);
        }
    }
}