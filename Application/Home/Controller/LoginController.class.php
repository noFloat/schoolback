<?php
namespace Home\Controller;
use Think\Controller;
use Org\Util\Rbac;
class LoginController extends Controller {

    public function index(){
		$this->display('');
    }

    public function checkLogin(){
    	// import('Org.Util.Rbac');
    	// var_dump($_POST);
    	// echo C('USER_AUTH_ON');
    	// echo C('USER_AUTH_KEY');
    	// if(I('post.user_name') == "admin"){
    		
    		
    	// }
    	//Rbac::checkLogin();
        $user = M('user');
        $salt_condition =array(
                "username" => I('post.user_name'),
                "statis"   => 1
            );
        $goal_salt = $user->where($salt_condition)->find();
        if(!session("?testtime")){
            session('testtime',0);
        }
        if(session("testtime") > 4){
            $info = array(
                    "info"  => "尝试次数过多，请稍后再试",
                    "state" => 400,
                );
            echo json_encode($info);
        }else if(!$goal_salt){
            $info = array(
                    "info"  => "用户不存在",
                    "state" => 401,
                );
            session('testtime',session('testtime')+1);
            echo json_encode($info);
        }else{
            $condition = array(
                    "username" => I('post.user_name'),
                    "password" => md5(hash('sha256', ($goal_salt['salt'] % 3))).sha1(I('post.password')),
                    "status"   => 1,
                );
            $goal_user = $user->where($condition)->find();
            if(!$goal_user){
                session('testtime',session('testtime')+1);
                $info = array(
                    "info"  => "密码错误",
                    "state" => 404,
                );
                echo json_encode($info);
            }else{
                $info = array(
                    "info"  => "success",
                    "state" => 200,
                );
                session('testtime',0);
                session(C("USER_AUTH_KEY"), $goal_user["id"]);
                session('username',$goal_user['username']);
                if($goal_user['username'] == C('RBAC_SUPERADMIN')) {
                    session(C('ADMIN_AUTH_KEY'), true);
                }
                RBAC::saveAccessList();
                echo json_encode($info);
            }
        }
    }
    // function _initialize(){
    //     //if(!IS_AJAX) $this->error('你访问的页面不存在，请稍后再试');
    //     $this->display('Login/index');
    // }
 

    public function _empty(){
        $this->display('404/index');
    }
}