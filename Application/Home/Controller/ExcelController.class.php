<?php
namespace Home\Controller;
use Think\Controller;

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
    	$this->display('');exit;
    	$str = "姓名,性别,年龄\n";   
	    $str = iconv('utf-8','gb2312',$str);   
	    $student = M('student');
	    $str = $student->select();

	    for($i=0;$i<16;$i++){
	    	$middle .= implode(",",$str[$i])."\n";
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

    public function import(){  
    	$upload = new \Think\Upload();
		$upload->maxSize = 0;
		$upload->exts = array('xls', 'xlsx');
		$upload->rootPath  =  "./Public/Excel/";
		$upload->saveName = time().'_'.mt_rand();
		$upload->autoSub = false;
	    $a = $upload->upload();
    	import("Org.Util.PHPExcel");
		//要导入的xls文件，位于根目录下的Public文件夹
		$filename="./Public/Excel/".$upload->saveName.".".$a['excel']['ext'];
		//创建PHPExcel对象，注意，不能少了\
		$PHPExcel=new \PHPExcel();
		//如果excel文件后缀名为.xls，导入这个类
		//import("Org.Util.PHPExcel.Reader.Excel5");
		//如果excel文件后缀名为.xlsx，导入这下类
		import("Org.Util.PHPExcel.Reader.Excel2007");
		$PHPReader=new \PHPExcel_Reader_Excel2007();

		//$PHPReader=new \PHPExcel_Reader_Excel5();
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
				F => "province"
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
            $excel->add($value);
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


    public function _empty() {
        $this->display('404/index');
    }
}

