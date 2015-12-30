<?php
namespace Home\Controller;
use Think\Controller;
use Org\Util\Rbac;
class PersonalController extends Controller {

    function _initialize(){
        if(!isset($_SESSION['username'])) {
            $this->redirect('login/index');exit;
        }
    }

    public function index(){
		$this->display('');
    }

    public function change(){
        $student = M('student');
        $condition = array(
                'stu_id' => session('stu_id'),
            );
        $content = array(
                "mail" => I('post.mail'),
                "phone"  => I('post.phone')
            );
        $goal = $student->where($condition)->data($content)->save();
        if($goal){
            $info = array(
                "info"  => "修改成功",
            );
            echo json_encode($info);
        }else{
            $info = array(
                "info"  => "修改失败",
            );
            echo json_encode($info);
        }
    }

    public function _empty(){
        $this->display('404/index');
    }
}