<?php
namespace Home\Controller;
use Think\Controller;
use Org\Util\Rbac;
class LoginController extends BaseController {

    public function index(){
		$this->display('Login/index');
    }

    public function checkLogin(){
    	import('Org.Util.Rbac');
    	var_dump($_POST);
    	echo C('USER_AUTH_ON');
    	echo C('USER_AUTH_KEY');
    	//Rbac::checkLogin();
    }
    // function _initialize(){
    //     //if(!IS_AJAX) $this->error('你访问的页面不存在，请稍后再试');
    //     $this->display('Login/index');
    // }
 

    public function _empty(){
        $this->display('404/index');
    }
}