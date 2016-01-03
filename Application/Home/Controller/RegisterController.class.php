<?php
namespace Home\Controller;
use Think\Controller;
use Org\Util\Rbac;
///
class RegisterController extends Controller {

    public function index(){
		$this->display('');
    }

    public function register(){
        $user = M('user');
        $condition = array(
            "username" => I('post.user_name')
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
                    "username"   => I('post.user_name'),
                    "password"   => md5(hash('sha256', ($salt % 3))).sha1(I('post.password')),
                    "email"      => I('post.email'),
                    "createtime" =>  date("Y-m-d H:i:s", time()),
                    "salt"       => $salt,
                    "status"     => 0
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
    public function forget(){
        $this->display('');
    }

    public function sendMail(){
        $student = M('student');
        $condition = array(
            "stu_id" => I('post.user_id')
        );
        $goal_student = $student->where($condition)->find();
        if($goal_student['mail'] == null){
            $info = array(
                    "info"  => "未申请邮箱",
                    "state" => 404,
                );
            echo json_encode($info);
        }else{
            Vendor('Mail.PHPMailerAutoload');
            $mail = new \PHPMailer;
         
            $mail->isSMTP();        //设置PHPMailer使用SMTP服务器发送Email
            $mail->Host = 'smtp.163.com';   //指定SMTP服务器 可以是smtp.126.com, gmail, qq等服务器 自行查询
            $mail->SMTPAuth = true;
            $mail->CharSet='UTF-8';     //设置字符集 防止乱码
            $mail->Username = 'nofloat@163.com';  //发送人的邮箱账户
            $mail->Password = 'nscylvrwktgjutel';   //发送人的邮箱密码
            $mail->Port = 25;   //SMTP服务器端口
         
            $mail->From = 'nofloat@163.com';            //发件人邮箱地址
            $mail->FromName = 'nextTeam';                //发件人名称
            $mail->addAddress($goal_student['mail']);      // 收件人邮箱地址 此处可以发送多个
         
            $mail->WordWrap = 50;                                 // 换行字符数
            $mail->isHTML(true);                                  // 设置邮件格式为HTML
         
            $mail->Subject = '验证码申请';       //邮件标题
            $check_code = rand(1000,9999);
            session('check_code',$check_code);
            session('check_user',I('post.user_id'));
            $mail->Body    = '尊敬的先生/女士:<br/>您的找回密码是:'.$check_code;         
            if(!$mail->send()) {
                $info = array(
                    "info"  => "邮件发送失败",
                    "state" => 404,
                );
                echo json_encode($info);
            } else {
                $info = array(
                    "info"  => "邮件发送成功",
                    "state" => 200,
                );
                echo json_encode($info);
            }
        }
        exit;
    }

    public function changePassword(){
        if(I('post.check_code')!= session('check_code')){
            $info = array(
                    "info"  => "验证码输入错误",
                    "state" => 404,
                );
            echo json_encode($info);
        }else if (I('post.user_id') != session('check_user')){
            $info = array(
                    "info"  => "不是对应用户",
                    "state" => 404,
                );
            echo json_encode($info);     
        }else if(I('post.password') != I('post.check_password') || 
            strlen(I('post.password')) < 6 || strlen(I('post.password'))> 11){
            $info = array(
                    "info"  => "请核对新密码（密码长度6-11位）",
                    "state" => 404,
                );
            echo json_encode($info); 
        }else{
            $student = M('student');
            $condition = array(
                'stu_id' => I('post.user_id')
            );
            $salt = mt_rand(10,200000);
            $content = array(
                "password"   => md5(hash('sha256', ($salt % 3))).sha1(I('post.password')),
                "salt"       => $salt,
            );
            $goal_result = $student->where($condition)->data($content)->save();
            if($goal_result){
                $info = array(
                    "info"  => "修改成功",
                    "state" => 200,
                );
                echo json_encode($info); 
            }else{
                $info = array(
                    "info"  => "系统错误",
                    "state" => 404,
                );
                echo json_encode($info); 
            }
        }
    }

    public function _empty(){
        $this->display('404/index');
    }
}