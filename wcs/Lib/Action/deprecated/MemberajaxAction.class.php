<?php

	class MemberajaxAction extends Action{ 

/*=============================通用逻辑开始=================================*/		
		public function loginCheck(){
			if(empty($_POST)){
				echo 'n';
				return;
			}else{
                $field = $_POST['name'];
				$userid = $_POST['param'];
                if($field == 'userid'){
                    $m = M('member');
                    $arr = $m->where("userid ='$userid'")->find();
                    if(empty($arr)){
                        echo '该用户不存在';
                        return;
                    }else{
                        echo 'y';
                        return;
                    }
                }else{
                    echo '数据不正确';
                }
			}		
		}
		
		//配合jquery表单验证
		public function registerCheck(){
			if(empty($_POST)){
				echo 'n';
				return;
			}else{
				$field = $_POST['name'];
				$value = $_POST['param'];
				$m = M('member');
				if($field == 'userid'){
					$arr = $m->where("userid ='$value'")->find();
					if(!empty($arr)){
						echo '该会员已经存在';
						return;
					}else{
						echo 'y';
						return;
					}
				}else if($field == 'email'){
					$arr = $m->where("email ='$value'")->find();
					if(!empty($arr)){
						echo '该email已经被使用';
						return;
					}else{
						echo 'y';
						return;
					}
				}else if($field == 'phone'){
                    $arr = $m->where("phone ='$value'")->find();
                    if(!empty($arr)){
                        echo '该手机号码已经被使用';
                        return;
                    }else{
                        echo 'y';
                        return;
                    }
                }else if($field == 'verify'){
					if($_SESSION['verify'] != md5($value)){
						echo '验证码有误！';
						return;
					}else{
						echo 'y';
						return;
					}
				}else if($field == 'sms_code'){

                    if($value == $_SESSION['sms_code']){
                        if(time() - $_SESSION['sms_lasttime']>120){
                            echo '验证码已经过期！';
                            return;
                        }else{
                            echo 'y';
                            return;
                        }
                    }else{
                        echo '验证码错误！';
                        return;
                    }

				}else if($field == 'pwd'){
					$mid = $_GET['mid'];
					if(empty($mid)){
						echo "查询会员信息失败！";	
						return;
					}
					
					$arr = $m->field('pwd')->where('id='.$mid)->find();
					if(empty($arr)){
						echo '查询会员信息失败！';
						return;
					}else{
						$oldpwd = $arr['pwd'];
						if($oldpwd == strrev(md5($value))){
							echo 'y';
							return;
						}else{
							echo "原密码不正确！";
							return;
						}
						

					}
				}else{
					echo '数据不正确';
				}
					
			}

		}

/*=============================通用逻辑结束=================================*/
		
	
		
	}

?>