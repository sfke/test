<?php

class CommonAction extends BaseAction
{
    //密码找回相关内容 =================== 开始
    //密码找回第一步，让用户输入需要找回的账号
    public function forgetPwd()
    {
        $theme = C('SYS_DEFAULT_THEME');
        $this->display($theme . ':step1');
    }

    //发送重置密码的URL到该用户注册时的邮箱
    public function getPwdBack()
    {
        require(APP_INC_PATH . 'phpmail/ini.php');
        $userid = $this->_post('userid');
        $email = $this->_post('email');
        $verify = $this->_post('verify');

        if (empty($userid) || empty($email)) {
            $this->error("用户名和邮箱不能为空！");
            return;
        }

        if ($_SESSION['verify'] != md5($verify) && '-1' != $verify) {
            $this->error('验证码错误！');
            return;
        }

        $m = M('member');
        $arr = $m->where("userid = '" . $userid . "'")->find();
        if (empty($arr)) {
            $this->error("该用户不存在！");
            return;
        } else if ($arr['email'] != $_POST['email']) {
            $this->error("邮箱与注册邮箱地址不符！");
            return;
        }

        $key = md5(strrev($arr['userid'])) . md5(strrev($arr['pwd']));
        $to = $arr['email'];
        $body = "<a href='http://" . C('JL_BASEHOST') . C('JL_CMSPATH') . "/index.php/common/changepwd?id=" . $arr['id'] . "&check=" . $key . "'>点此重置密码！</a><span style='color:red'>    (修改密码后改链接自动失效)</span>";
        try {
            $mail->AddAddress($to); //邮件的发送地址
            $mail->MsgHTML($body); //邮件的内容
            $mail->Send();
        } catch (phpmailerException $e) {
            $this->error("发送邮件时出现错误,请联系客服人员！");
            return;
        }
        $this->assign("email", $to);
        $this->assign("userid", $userid);
        $theme = C('SYS_DEFAULT_THEME');
        $this->display($theme . ':step2');
    }

    //让用户输入新密码
    public function changePwd()
    {
        $action = $this->_post('action');
        $id = $this->_post('id');
        $pwd = $this->_post('pwd');

        if ($this->isPost() && $action = "resetpwd") {
            $m = M('member');
            $data['id'] = $id;
            $data['pwd'] = strrev(MD5($pwd));

            if ($m->save($data) === false) {
                $this->error('重置密码失败，请联系管理员！');
                return;
            }

            $theme = C('SYS_DEFAULT_THEME');
            $this->display($theme . ':step3');
        } else {
            $id = $this->_get('id');
            $check = $this->_get('check');
            if (empty($id)) {
                $this->error('会员id有误！请联系管理员！');
                return;
            }
            $m = M('member');
            $arr = $m->where('id=' . $id)->find();
            if (empty($arr)) {
                $this->error('会员不存在！请联系管理员！');
                return;
            }

            $key = md5(strrev($arr['userid'])) . md5(strrev($arr['pwd']));

            if ($check != $key) {
                $this->error('改密码重置地址已经失效！');
                return;
            }

            $this->assign("id", $id);
            $this->assign("userid", $arr['userid']);
            $theme = C('SYS_DEFAULT_THEME');
            $this->display($theme . ':step2');
        }
    }
    //密码找回相关内容 =================== 结束

    //人才招聘相关    ==================== 开始
    public function resume()
    {
        $type = $this->_get('type');
        $name = $this->_get('name');

        $work = array("护士" => 2, "助理护士" => 2, "支助" => 2, "导医" => 2, "医疗" => 1, "医技" => 1, "管理" => 1, "其它" => 1, "高层次人才引进" => 1);
        $workOptions = "";
        foreach ($work as $k => $v) {
            if ($name == $k) {
                $flag = 'selected = "selected"';
            } else {
                $flag = "";
            }
            $workOptions .= '<option value="' . $k . '" _type="' . $v . '"  ' . $flag . '  >' . $k . '</option>';
        }

        $keshiOptions = "";
        $keshi = array("呼吸科", "神经内科", "心内科", "血液科", "内分泌科", "老年科", "肾病风湿科", "消化科", "肝胆外科", "骨科", "泌尿外科", "神经外科", "胃肠外科", "重症医学科", "甲乳外科", "麻醉科", "胸外科", "心外科", "妇产科", "急诊科", "康复科", "美容科", "皮肤科", "儿科", "中医科", "耳鼻喉科", "口腔科", "眼科", "影像科", "病理科", "超声诊断科", "核医学科", "检验科", "心功能科", "中心实验室", "肿瘤科", "药剂科", "其它");
        foreach ($keshi as $v) {
            $keshiOptions .= '<option value="' . $v . '"  >' . $v . '</option>';
        }

        switch ($type) {
            case 1 :
                $tpl = "resume1";
                break;
            case 2 :
                $tpl = "resume2";
                break;
            default :
                $tpl = "resume2";
                $type = 2;
                break;
        }

        $this->assign("type", $type);
        $this->assign("name", $name);
        $this->assign("workOptions", $workOptions);
        $this->assign("keshiOptions", $keshiOptions);
        $this->assign("jl_title", "招聘简历 - " . C('JL_WEBNAME'));
        $theme = C('SYS_DEFAULT_THEME');
        $this->display($theme . ':' . $tpl);
    }

    //招聘表单打印
    public function resumePrint()
    {
        $rid = $this->_get('rid');
        $act = $this->_get('act');

        $m = new ResumeModel();
        $arr = $m->getData($rid);
        $this->assign("arr", $arr);
        $this->assign("jl_title", "招聘简历 - " . C('JL_WEBNAME'));
        $theme = C('SYS_DEFAULT_THEME');
        $this->assign("defaultimg", C('SYS_DEFAULT_IMG'));
        if (empty($act)) {
            $this->display($theme . ':print' . $arr['type']);
        } else {
            $docname = $arr['name'] . "_" . $arr['gangwei'] . "_" . date("Y-m-d", $arr['time']);
            header("Content-type:application/vnd.ms-word");
            header("Content-Disposition:filename=" . $docname . ".doc");
            $html = $this->fetch($theme . ':print' . $arr['type']);
            echo $html;
        }
    }
    //人才招聘相关    ==================== 结束

    //问卷调查（投票模块）
    public function vote()
    {
        $mvotetype = M("Votetype");
        $id = $this->_param('id');
        $id = !empty($id) ? $id : 1;
        $votetypeArr = $mvotetype->where("id=" . $id)->find();

        if (empty($votetypeArr)) {
            $this->error("该投票主题没有任何问题！");
            return;
        }

        $this->assign('jl_title', "问卷调查 - " . $votetypeArr['title'] . ' - ' . C('JL_WEBNAME'));
        $this->assign('jl_desc', "问卷调查 - " . $votetypeArr['title'] . ' - ' . C('JL_WEBNAME'));
        $this->assign('jl_key', "问卷调查");
        $this->assign("defaultimg", C('SYS_DEFAULT_IMG'));
        $this->assign("votetypeArr", $votetypeArr);
        $this->assign("id", $id);
        $theme = C('SYS_DEFAULT_THEME');
        $this->display($theme . ":vote");
    }

    //rss
    public function rss()
    {
        require(APP_INC_PATH . 'rss/rss.class.php');
        $rss = new RSS('', "http://" . C('JL_BASEHOST') . C('JL_CMSPATH'), "jltech");
        $rss->AddItem('RSS Class', "http://www.xxx.com", "xxx", time());
        echo $rss->show();
    }
}
?>