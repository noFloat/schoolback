<?php
namespace Home\Controller;
use Think\Controller;
use Org\Util\Rbac;
class SearchController extends Controller {

    public function index(){
		$this->display('');
    }

    public function _empty(){
        $this->display('404/index');
    }
}