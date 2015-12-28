<?php
namespace Home\Controller;
use Think\Controller;
use Org\Util\Rbac;
class RegisterController extends Controller {

    public function index(){
		$this->display('');
    }

    public function register(){
        $user = M('user');
        $condition = array(
            "username" => I('post.user')
        );
        $goal_user = $user->where($condition)->find();
        if(I('post.password') != I('post.check_password') || 
            strlen(I('post.password')) < 6 || strlen(I('post.password'))> 11){
            $info = array(
                    "info"  => "failed",
                    "state" => 400,
                );
            echo json_encode($info);
            //$this->ajaxReturn($info);
        }else if($goal_user){
            $info = array(
                    "info"  => "failed",
                    "state" => 401,
                );
            echo json_encode($info);
            //$this->ajaxReturn($info);
        }else{
            $salt = mt_rand(10,200000);
            $content = array(
                    "username"   => I('post.user'),
                    "password"   => md5(hash('sha256', ($salt % 3))).sha1(I('post.password')),
                    "email"      => I('post.email'),
                    "createtime" =>  date("Y-m-d H:i:s", time()),
                    "salt"       => $salt,
                    "status"     => 1
                );
            $result = $user->add($content);
            if(!$result){
                $info = array(
                    "info"  => "failed",
                    "state" => 404,
                );
                echo json_encode($info);
                //$this->ajaxReturn($info);
            }else{
                $info = array(
                    "info"  => "success",
                    "state" => 200,
                );
                echo json_encode($info);
            }
        }
    }
 

    public function _empty(){
        $this->display('404/index');
    }
}