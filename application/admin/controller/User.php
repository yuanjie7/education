<?php
namespace app\admin\controller;

use think\Db;
use think\Controller;
/**
 * 用户管理
 */
define("URL","http://www.miizi.cn/edu/");
class User extends Controller
{	
    /**
     * 校区管理
     */
    //首页显示页面
    public function campus(){
    	//渲染模板
    	return view();
    }
    //查询校区数据
    public function selectCampusInfo(){
    	$map = [];
    	$campusname = input("campusname");
    	if($campusname){ $map['campusname']  = ['like','%'.$campusname.'%']; }
    	$pageIndex=input("page");//开始位置
        $pageSize=input("limit");//每页查询几条 
        $pageStart=(($pageIndex-1)*$pageSize);//开始位置 	
    	$data = Db::table('campus')->where($map)->limit($pageStart,$pageSize)->select();
    	$count = Db::table('campus')->where($map)->count('id');
    	$json = ['code'=>0,'msg'=>'','count'=>$count,"data"=>$data];
    	return json($json);
    }
    //新增校区页面
    public function campus_add(){
    	//渲染模板
    	return view();
    }
    //新增校区处理
    public function campusDoAdd(){
    	$data['campusname'] = input("campusname");
    	$result = Db::table('campus')->insert($data);
    	if($result){
    		$json = ['code'=>0,'msg'=>$result,'result'=>'操作成功'];
    	}else{
    		$json = ['code'=>1,'msg'=>$result,'result'=>'操作失败'];
    	}
    	return json($json);
    }
    //查询校区详细信息
    public function campusDetail(){
    	$map['id'] = input("id");
    	$result = Db::table('campus')->where($map)->find();
    	if($result){
    		$json = ['code'=>0,'msg'=>$result,'result'=>'查询成功'];
    	}else{
    		$json = ['code'=>1,'msg'=>$result,'result'=>'暂无数据，查询失败'];
    	}
    	return json($json);
    }
    //修改校区信息
    public function campusDoEdit(){
    	$map['id'] = input("id");
    	$data['campusname'] = input("campusname");
    	$result = Db::table('campus')->where($map)->update($data);
    	if($result){
    		$json = ['code'=>0,'msg'=>$result,'result'=>'操作成功'];
    	}else{
    		$json = ['code'=>1,'msg'=>$result,'result'=>'操作失败'];
    	}
    	return json($json);
    }
    /**
     * 会员管理
     */
    //首页显示
    public function index()
    {   
        //查询校区列表数据
        $map['status'] = 1;
        $list = Db::table('campus')->where($map)->select();
        $this->assign("list",$list);
        //渲染模板
        return view();
    } 
    //查询会员数据
    public function selectUserInfo(){
        $map = [];
        input("username")?$map['username'] = input("username"):'';
        input("phone")?$map['phone'] = input("phone"):'';
        input("sex")?$map['sex'] = input("sex"):'';
        input("campus")?$map['campussource'] = input("campus"):'';
        $pageIndex=input("page");//开始位置
        $pageSize=input("limit");//每页查询几条 
        $pageStart=(($pageIndex-1)*$pageSize);//开始位置    
        $data = Db::table('user')->where($map)->limit($pageStart,$pageSize)->select();
        $count = Db::table('user')->where($map)->count('id');
        $json = ['code'=>0,'msg'=>'','count'=>$count,"data"=>$data];
        return json($json);
    }
    //新增会员部分
    //上传图片
    public function uploadImg(){
        // 获取表单上传文件
        $file = request()->file('file');
        if(empty($file)){
           return json(['code'=>1,'msg'=>'','result'=>'请选择文件上传']); 
        }
        // 移动到框架应用根目录/public/uploads/userlogo/目录下
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads\userlogo');
        if($info){
            // 输出保存路径
            $path = 'public/uploads/userlogo/'.$info->getSaveName();
            // 成功上传后 返回上传信息
            return json(['code'=>0,'msg'=>$path,'result'=>'上传成功']);
        }else{
            // 上传失败返回错误信息
            return json(['code'=>1,'msg'=>'','result'=>'上传失败']);
        }     
    }
    //新增会员信息
    public function userDoAdd(){
        $data = $_POST;
        !$data['registerdate']?$data['registerdate'] = date("Y-m-d H:i:s",time()):'';
        $result = Db::table('user')->insert($data);
        if($result){
            $json = ['code'=>0,'msg'=>$result,'result'=>'操作成功'];
        }else{
            $json = ['code'=>1,'msg'=>$result,'result'=>'操作失败'];
        }
        return json($json);
    }
    //会员详情信息
    public function userDetail(){
        $map['id'] = input("id");
        $result = Db::table('user')->where($map)->find();
        $result['userlogo'] = URL.$result['userlogo'];
        if($result){
            $json = ['code'=>0,'msg'=>$result,'result'=>'查询成功'];
        }else{
            $json = ['code'=>1,'msg'=>$result,'result'=>'暂无数据，查询失败'];
        }
        return json($json);
    }
    //会员信息修改
    public function userDoEdit(){
        $data = $_POST;
        $map['id'] = $data['id'];
        if(isset($data['userlogo'])){
            $oldlogo = Db::table('user')->field('userlogo')->where($map)->find(); 
            $del = ROOT_PATH.$oldlogo['userlogo']; 
        }
        $result = Db::table('user')->where($map)->update($data);
        if($result){
            isset($del)?unlink($del):'';
            $json = ['code'=>0,'msg'=>$result,'result'=>'操作成功'];
        }else{
            $json = ['code'=>1,'msg'=>$result,'result'=>'操作失败'];
        }
        return json($json);
    }
}
