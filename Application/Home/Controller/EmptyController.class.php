<?php

namespace Home\Controller;
use Think\Controller;
use Think\Exception;

class EmptyController extends Controller {
    public function index(){
//        $indexSite = U(CONTROLLER_NAME . '/' . ACTION_NAME);
//        if(!file_exists($indexSite)) {
//            throw new Exception('对不起, 你所使用的' . ACTION_NAME . '并未找到');
//        }
        $this->display('404/index');
    }

    public function _empty() {
        $this->display('404/index');
    }
}