<?php
namespace app\index\controller;

use think\Db;
use think\Controller;
/**
 * 接口--我的部分
 */ 
define("URL","http://www.miizi.cn/edu/");
class Mine extends Controller
{
	/**
	 * 登录
	 */ 
	public function login(){
		//接收账号、密码
		$map['phone'] = input('phone');
		$post_pwd = input('password');
		if(!$map['phone']){ 
			return json(['code'=>1,'count'=>0,'msg'=>'请传入参数:[phone]','result'=>'']); 
		}
		if(!$post_pwd){ 
			return json(['code'=>1,'count'=>0,'msg'=>'请传入参数:[password]','result'=>'']); 
		}
		//查询账号对应数据
		$info = Db::table('user')
			    ->field('id,userlogo,username,password,campussource,expirationdate')
		        ->where($map)
		        ->find();  
		if(!$info){
			return json(['code'=>1,'count'=>0,'msg'=>'该用户不存在','result'=>'']);
		}            
		//验证密码        
		if($post_pwd!=$info['password']){
			return json(['code'=>1,'count'=>0,'msg'=>'密码错误','result'=>'']);
		}
		unset($info['password']);
		$info['userlogo'] = URL.$info['userlogo'];  
		return json(['code'=>0,'count'=>0,'msg'=>'登录成功','result'=>$info]);
	}
	/**
	 * 修改资料
	 */
	//查询个人信息
	public function personalInfo(){
		//根据id查询数据
		$map['id'] = input('id');
		if(!$map['id']){ 
			return json(['code'=>1,'count'=>0,'msg'=>'请传入参数:[id]','result'=>'']);
		}
		$info = Db::table('user')
			    ->field('userlogo,username,sex,age,idnumber')
		        ->where($map)
		        ->find(); 
		if(!$info){
			return json(['code'=>1,'count'=>0,'msg'=>'暂无数据','result'=>'']);
		}   
		$info['userlogo'] = URL.$info['userlogo'];   
		return json(['code'=>0,'count'=>count($info),'msg'=>'查询成功','result'=>$info]);        
	}
	//校区来源列表
	public function campusSource(){ 
		$map['status'] = 1;
		$info = Db::table('campus')
				->field('campusname')	
		        ->where($map)
		        ->select();
		if(!$info){
			return json(['code'=>1,'count'=>0,'msg'=>'暂无数据','result'=>'']);
		}  
		return json(['code'=>0,'count'=>count($info),'msg'=>'查询成功','result'=>$info]);       
	}
	//数据提交
	public function infoSubmit(){

	}
	/**
	 * 修改密码
	 */
	public function updatePassword(){
		//输入新旧密码
		$map['id'] = input('id');
		$oldpassword = input('oldpassword');
		$newpassword = input('newpassword');
		if(!$map['id']){ 
			return json(['code'=>1,'count'=>0,'msg'=>'请传入参数:[id]','result'=>'']);
		}
		if(!$oldpassword){ 
			return json(['code'=>1,'count'=>0,'msg'=>'请传入参数:[oldpassword]','result'=>'']);
		}
		if(!$newpassword){ 
			return json(['code'=>1,'count'=>0,'msg'=>'请传入参数:[newpassword]','result'=>'']);
		}
		if($oldpassword==$newpassword){
			return json(['code'=>1,'count'=>0,'msg'=>'新密码不能与旧密码相同','result'=>'']);
		}
		$info = Db::table('user')
		        ->field('password') 
		        ->where($map)
		        ->find();
		if($oldpassword!=$info['password']){
			return json(['code'=>1,'count'=>0,'msg'=>'旧密码错误，请重试','result'=>'']);
		} 
		$data['password'] = $newpassword;
		$result = Db::table('user')
		          ->where($map)
		          ->update($data);       
		if($result){
            $json = ['code'=>0,'msg'=>$result,'result'=>'操作成功'];
        }else{
            $json = ['code'=>1,'msg'=>$result,'result'=>'操作失败'];
        }
        return json($json);
	}
	/**
	 * 
	 */
}