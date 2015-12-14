<?php

namespace Home\Controller;
use Think\Controller;
use Org\Util\Rbac;
class BaseController extends Controller {
    function _initialize(){
    //     // if(!isset($_SESSION[C('USER_AUTH_KEY')])) {
    //     //     $this->redirect('Login/index');
    //     // }

    //     $notAuth = in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE'))) || in_array(ACTION_NAME, C('NOT_AUTH_ACTION'));

    // //     //权限验证
    //     if(C('USER_AUTH_ON') && !$notAuth) {
    //         import('Org.Util.RBAC');
    //         RBAC::AccessDecision(GROUP_NAME) || $this->error("你没有对应的权限");
    //     }
    }
}