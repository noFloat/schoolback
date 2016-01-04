<?php
namespace Home\Controller;
use Think\Controller;
/**
* @name 名字
* @abstract 申明变量/类/方法
* @access 指明这个变量、类、函数/方法的存取权限
* @author 函数作者的名字和邮箱地址
* @category 组织packages
* @copyright 指明版权信息
* @const 指明常量
* @deprecate 指明不推荐或者是废弃的信息
* @example 示例
* @exclude 指明当前的注释将不进行分析，不出现在文挡中
* @final 指明这是一个最终的类、方法、属性，禁止派生、修改。
* @global 指明在此函数中引用的全局变量
* @include 指明包含的文件的信息
* @link 定义在线连接
* @module 定义归属的模块信息
* @modulegroup 定义归属的模块组
* @package 定义归属的包的信息
* @param 定义函数或者方法的参数信息
* @return 定义函数或者方法的返回信息
* @see 定义需要参考的函数、变量，并加入相应的超级连接。
* @since 指明该api函数或者方法是从哪个版本开始引入的
* @static 指明变量、类、函数是静态的。
* @throws 指明此函数可能抛出的错误异常,极其发生的情况
* @todo 指明应该改进或没有实现的地方
* @var 定义说明变量/属性。
* @version 定义版本信息
*/
class ExcelController extends BaseController {



    public function index(){
    	$this->display('');
    }

    public function showExcel(){
    	$student = M('student');
    	$goal_id = explode(',',I('post.stunum'));
    	array_shift($goal_id);
    	$num = count($goal_id);
    	$last_goal = array();
    	for($i = 0;$i<$num;$i++){
    		$condition = array(
    			'stu_id' => $goal_id[$i]
    		);
    		$goal_student = $student->where($condition)->field(
    			'stu_name,stu_id,sex,class_id,idcard,province,mail,phone'
    			)->find();
    		array_push($last_goal,$goal_student);
    	}
    	$str = "姓名,学号,性别,班级,身份证号,省份,邮箱,电话\n";   
	    $str = iconv('utf-8','gb2312',$str);   
	    $student = M('student');
	    $str = $student->select();
	    for($i=0;$i<$num;$i++){
	    	$middle .= implode(",",$last_goal[$i])."\n";
	    }     
	    $filename = date('Ymd').rand().'.csv'; //设置文件名  
	    $middle = iconv('utf-8','gb2312',$middle);
	    header("Content-type:text/csv");   
	    header("Content-Disposition:attachment;filename=".$filename);   
	    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');   
	    header('Expires:0');   
	    header('Pragma:public');   
	    echo $middle;   
    }

    public function export(){
    	$this->display('');
    }

    public function import(){  
    	$upload = new \Think\Upload();
		$upload->maxSize = 0;
		$upload->exts = array('xls', 'xlsx');
		$upload->rootPath  =  "./Public/Excel/";
		$upload->saveName = time().'_'.mt_rand();
		$upload->autoSub = false;
	    $a = $upload->upload();
        if($upload->getError() != null){
            $this->error("您未上传文件");
        }
    	import("Org.Util.PHPExcel");
		//要导入的xls文件，位于根目录下的Public文件夹
		$filename="./Public/Excel/".$upload->saveName.".".$a['excel']['ext'];
		//创建PHPExcel对象，注意，不能少了\
		$PHPExcel=new \PHPExcel();
		//如果excel文件后缀名为.xls，导入这个类
        if($a['excel']['ext'] == 'xls'){
            import("Org.Util.PHPExcel.Reader.Excel5");
            $PHPReader=new \PHPExcel_Reader_Excel5();
        }else{
            import("Org.Util.PHPExcel.Reader.Excel2007");
        $PHPReader=new \PHPExcel_Reader_Excel2007();
        }
		$PHPExcel=$PHPReader->load($filename);
		//获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
		$currentSheet=$PHPExcel->getSheet(0);
		//获取总列数
		$allColumn=$currentSheet->getHighestColumn();
		//获取总行数
		$allRow=$currentSheet->getHighestRow();
		//循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
		$sql_standard = array(
				A => "stu_name",
				B => "stu_id",
				C => "sex",
				D => "class_id",
				E => "idcard",
				F => "province",
			);
		for($currentRow=1;$currentRow<=$allRow;$currentRow++){
			//从哪列开始，A表示第一列
			for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
				//数据坐标
				$address=$currentColumn.$currentRow;
				//读取到的数据，保存到数组$arr中
				$arr[$currentRow][$sql_standard[$currentColumn]]=$currentSheet->getCell($address)->getValue();
			}
		
		}
		array_shift($arr);
		$excel =  M('student');
		$num = count($arr);
        foreach($arr as $key => $value){
        	$condition = array(
        			"stu_id" => $arr[$key]['stu_id'],
        		);
            $salt = mt_rand(10,200000);
            $value['salt'] = $salt;
            $value['password'] = md5(hash('sha256', ($salt % 3))).sha1(substr($value['idcard'],-6));
        	$output = $excel->where($condition)->find();
        	//var_dump($output);
        	if($output){
            	$goal = $excel->where($condition)->data($value)->save();
        	}else{
        		$goal = $excel->add($value);
        	}
        }	
        if($goal){
        	$info = array(
        		"info"  => "success",
                "state" => 200,
        	);
            $log = D('Log');
            $log->addLog('导入学生');
            $this->success("添加成功");
        }else{
        	$info = array(
                    "info"  => "failed",
                    "state" => 404,
            );
            $this->error("添加失败");
        }
    }


    public function searchStudent(){
    	if(I('post.stunum')!=null){
    		$condition['stu_id'] = I('post.stunum');
    	}
    	if(I('post.classid')!=null){
    		$condition['class_id'] = I('post.classid');
    	}
    	if(I('post.province')!=null){
    		$condition['province'] = I('post.province');
    	}
    	$student = M('student');
    	$goal_student = $student->where($condition)->select();
    	if(!$goal_student){
    		$info = array(
                    "info"  => "failed",
                    "state" => 404,
                );
            echo json_encode($info);
    	}else{
    		$info = array(
                    "info"  => "success",
                    "state" => 200,
                    "data"  => $goal_student,
                );
            echo json_encode($info);
    	}
    }

    public function update(){
        $student = M('student');
        $content = array(
                "stu_name" => I('post.stu_name'),
                "sex"  => I('post.stu_sex'),
                "class_id" => I('post.stu_class'),
                "province" => I('post.stu_province'),
                "status"   => I('post.status'),
            );
        $condition = array(
                "stu_id" => I('post.stu_id')
            );
        $goal = $student->where($condition)->data($content)->save();
        if($goal){
            $this->success('修改成功');
        }else{
            $this->error("修改失败");
        }
    }

    public function addAnnex(){
        $upload = new \Think\Upload();
        $upload->maxSize = 0;
        $upload->exts = array('docx', 'doc',"zip");
        $upload->rootPath  =  "./Public/Annex/";
        $upload->saveName = time().'_'.mt_rand();
        $upload->autoSub = false;
        $a = $upload->upload();
        $annex = M('annex');
        $content = array(
                "username" => session('username'),
                "date"     => date("Y-m-d H:i:s", time()),
                "title" => I('post.title'),
                "position" => "./Public/Annex/".$upload->saveName.".".$a['fold']['ext'],
                'state'    => 1
            );
        $goal = $annex->where($condition)->add($content);
        if($goal){
            $log = D('Log');
            $log->addLog('添加附件');
            $this->success("添加成功");
        }else{
            $log = D('Log');
            $log->addLog('添加附件失败');
            $this->error("添加失败");
        }
    }

    public function addOne(){
        $salt = mt_rand(10,200000);
        $content = array(
            'stu_name' => I('post.addone_name'),
            'sex'      => I('post.addone_sex'),
            'stu_id'    => I('post.addone_stuid'),
            'province' => I('post.addone_province'),
            'class_id' => I('post.addone_classid'),
            'idcard'   => I('post.addone_idcard'),
            'password' => md5(hash('sha256', ($salt % 3))).sha1(substr(I('post.addone_idcard'),-6)),
            'salt'     => $salt,

        );
        foreach($content as $key => $value){
            if($value = null){
                $info = array(
                    "info"  => "请保证数据无空值",
                    "state" => 404,
                );
                echo json_encode($info);
            }
        }
        $student = M('student');
        $condition = array(
                "stu_id" => I('post.addone_stuid')
            );
        $goal_student = $student->where($condition)->find();
        if($goal_student){
            $info = array(
                    "info"  => "该学生已存在，添加失败",
                    "state" => 404,
                );
            echo json_encode($info);
        }else{
            $student->add($content);
            $info = array(
                "info"  => "添加成功",
                "state" => 200,
            );
            echo json_encode($info);
        }
    }

    public function _empty() {
        $this->display('404/index');
    }
}

