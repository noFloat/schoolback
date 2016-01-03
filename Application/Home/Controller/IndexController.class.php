<?php
namespace Home\Controller;
use Think\Controller;

class IndexController extends Controller {
	function _initialize(){
        if(!isset($_SESSION['username'])) {
            $this->redirect('login/index');exit;
        }
    }
    public function index(){
    	$type = array(
	    		"0" => 'jwzx',
	    		"1" => 'cyxw'
    		);
    	for($i = 0; $i<2;$i++){
	    	$data = array(
		        	"type" => $type[$i],
		        	"page" => 0,
		        	"size" => 45
	        	);
	    	$output = $this->curl_init("http://hongyan.cqupt.edu.cn/cyxbsMobile/index.php/home/news/searchtitle",$data);
	    	$output = (array)$this->objectToArray(json_decode($output));
	    	$output = $output['data'];
	    	//var_dump($output);
	    	$data['page'] = 1;
	    	$output_middle = $this->curl_init("http://hongyan.cqupt.edu.cn/cyxbsMobile/index.php/home/news/searchtitle",$data);
	    	$output_middle = (array)$this->objectToArray(json_decode($output_middle));
	    	for($m = 0;$m<30;$m++){
	    		array_push($output,$output_middle['data'][$m]);
	    	}
	    	$num = count($output);
	    	$Page = new \Think\Page($num,10);
	    	$Page->lastSuffix=false;
		    $Page->setConfig('prev','上一页');
		    $Page->setConfig('next','下一页');
		    $Page->setConfig('last','末页');
		    $Page->setConfig('first','首页');
		    $Page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
			$show = $Page->show(); 
			$lists = array_slice($output, $Page->firstRow,$Page->listRows);
			$this->assign('page'.$i,$show);
			$this->assign('nodes'.$i,$lists);
		}
		$annex = M('annex');
		$goal = $annex->where('state = 1')->select();
		$this->assign('goal',$goal);// 赋值数据集
		$count = $annex->where('state=1')->count();// 查询满足要求的总记录数$Page       
		$need_annex= new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
		$need_annex->lastSuffix=false;
	    $need_annex->setConfig('prev','上一页');
	    $need_annex->setConfig('next','下一页');
	    $need_annex->setConfig('last','末页');
	    $need_annex->setConfig('first','首页');
	    $need_annex->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
		$annex_show = $need_annex->show();// 分页显示输出
		$this->assign('annex',$annex_show);// 赋值分页输出
		$this->display('Index/index');
    }

    public function searchContent(){
    	$data = array(
    		"type"      => I('get.type'),
    		"articleid" => I('get.articleid')
    		);
    	$output = $this->curl_init("http://hongyan.cqupt.edu.cn/cyxbsMobile/index.php/home/news/searchContent?type=".I('get.type')."&articleid=".I('get.articleid'),$data);
		$output = (array)$this->objectToArray(json_decode($output));
		$this->assign('content',$output['data']['content']);
		$this->display('Index/content');
		//$this->display('Index/index');
    }

    private function objectToArray($e){
	    $e=(array)$e;
	    foreach($e as $k=>$v){
	        if( gettype($v)=='resource' ) return;
	        if( gettype($v)=='object' || gettype($v)=='array' )
	            $e[$k]=(array)$this->objectToArray($v);
	    }
	    return $e;
	}

    private function curl_init($url,$data){//初始化目标网站
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt ($ch, CURLOPT_POSTFIELDS,$data);
        $output = curl_exec($ch);
        return $output;
    }

   	public function searchStudent(){
   		$data['stunum'] = I('post.stunum');
   		$output = $this->curl_init("http://hongyan.cqupt.edu.cn/cyxbsMobile/index.php/home/searchPeople",$data);
   		$output = json_encode(json_decode($output));
   		echo $output;
   	}


    public function destory(){
    	session(null);
    	$this->redirect('Login/index');
    }

    public function _empty() {
        $this->display('404/index');
    }
}

