<?php

class CommonformAction extends BaseAction
{
    //人才招聘信息提交
    public function resume()
    {
        if (empty($_POST)) {
            $this->error("请勿恶意提交");
            return;
        }
        //主表多分表结构数据模型
        $data = $_POST;
        $data['time'] = time();
        $m = new ResumeModel($data['type']);
        $bool = $m->madd($data);

        if (!empty($bool)) {
            $this->error("投递简历失败！请稍后再试！");
            return false;
        } else {
            $rid = $m->getMainTableInsertId();
            $this->assign('waitSecond', '10');
            $this->success("投递简历成功！点击<a target='_blank' href='" . U('common/resumePrint?rid=' . $rid) . "'>打印</a>简历!", U('common/resume'));
            return true;
        }
    }

    //投票提交
    public function vote()
    {
        if (empty($_POST)) {
            $this->error("请勿恶意提交");
            return;
        }

        $data = $_POST;
        //限制投票
        $m2 = M('ipCheck');
        $map = array();
        $map['date'] = date('Y-m-d', time());
        $map['ip'] = get_client_ip();
        $map['fid'] = $data['id'];
        $check = $m2->where($map)->find();
        if (!empty($check)) {
            $this->error("您今天已经投过票了！");
            return;
        } else {
            $m2->create($map);
            $m2->add();
        }
        /*===========扩展信息保存开始============*/
        if (!empty($data['ext_remark'])) {
            $extM = M('Voteext');
            $extdata['fid'] = $data['id'];
            $extdata['remark'] = $data['ext_remark'];
            $extdata['ks'] = implode(" ", $data['ext_ks']);
            $extdata['date'] = date('Y-m-d', time());
            $extdata['time'] = time();
            $extM->create($extdata);
            $extM->add();
        }

        /*===========扩展信息保存结束============*/
        $itemsArr = array();
        foreach ($data as $k => $v) {
            if (strpos($k, "votea_") !== false) {
                $temp = array();
                $temp = explode("_", $v);
                $itemsArr[$temp[0]] = $temp[1];
            }
        }
        $m = M('Votepoll');
        $date = date('Y-m-d', time());
        if (!empty($itemsArr)) {
            foreach ($itemsArr as $k => $v) {
                $map = array();
                $map['date'] = array("eq", $date);
                $map['id'] = array('eq', $v);
                $check = $m->where($map)->find();
                //不存在
                if (empty($check)) {
                    $newDara = array();
                    $newDara['id'] = $v;
                    // $newDara['fid'] = $k;
					$tempk = explode("-", $k);
					$newDara['fid'] = $tempk[0];
                    $newDara['date'] = $date;
                    $newDara['poll'] = 1;
                    $m->create($newDara);
                    $m->add();
                } else {
                    $m->where($map)->setInc("poll", 1);
                }
            }
        }
        
		//下面跳转地址根据自身的改写代码
        if ($data['id'] == 1) {
           // $this->success("提交问卷成功！", U('index/vote?id=' . $data['id']));
			$this->success("提交问卷成功！", U('index/show?tid=3'));
        }else if ($data['id'] == 2) {
            //$this->success("提交问卷成功！", U('index/survey?id=' . $data['id']));
			$this->success("提交问卷成功！", U('index/show?tid=4'));
        }else if ($data['id'] == 3) {
			$this->success("提交问卷成功！", U('index/show?tid=5'));
        }
    }

    //搜索
    public function search()
    {
		$begin = microtime(TRUE);
        import('ORG.Util.PageFront');
        $key = trim($this->_param("key"));
        $type = $this->_param("type");
        $type = !empty($type) ? $type : 1;
        //$key = iconv("gbk","utf-8",$key);
        if (empty($type)) {
            $this->error("搜索范围未指定！");
        }
        $rsArr = array();

        //主表模型查询
        if ($type == 1) {
            $m = M('archives');
			$tpl = "search";
            $map = array();
            $map['siteid'] = getSiteId();
            $map['title'] = array('like', "%" . $key . "%");
			//$map['title'] =array('like',array("%". $key . "%","%". $key ."%"),"OR"); //一个字段 多条件 示例
            $pagesize = 10;
            $count = $m->where($map)->count(); // 查询满足要求的总记录数
            $Page = new Page($count, $pagesize, 'key='.$key); // 实例化分页类 传入总记录数和每页显示的记录数
            $show = $Page->searchshow(); // 分页显示输出
            $rsArr = $m->where($map)->limit($Page->firstRow . ',' . $Page->listRows)->select();
        } else if ($type == 2) {
            //更多类型查询逻辑，例如：photo模型表的搜索
			 $m = M();
			 $tpl = "search";
             $pagesize = 10;
             $count = $m->table(C('DB_PREFIX').'archives arc, '.C('DB_PREFIX').'addphoto p')->where('arc.id = p.aid and arc.title like "%'.$key.'%"')->order('arc.sortrank desc')->count();
             $Page = new Page($count, $pagesize, 'key='.$key);
             $show = $Page->searchshow();
             $rsArr = $m->table(C('DB_PREFIX').'archives arc, '.C('DB_PREFIX').'addphoto p')->where('arc.id = p.aid and arc.title like "%'.$key.'%"')->order('arc.sortrank desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        }
        
        $end = microtime(TRUE);
        $time = $end-$begin; //搜索执行时间
        $this->assign("time", $time); 
		$this->assign("key", $key);
        $this->assign("rsArr", $rsArr);
        $this->assign("pageline", $show);
        $this->assign("count", $count);
        $theme = C('SYS_DEFAULT_THEME');
        $this->display($theme . ':' . $tpl);
    }

    //通用表单提交
    public function commonform()
    {
        $verify = $this->_param('verify');
        $type = $this->_param('type');
        if ($_SESSION['verify'] != md5($verify) && '-1' != $verify) {
            $this->error('验证码错误！');
            return;
        }

        if (empty($type)) {
            $this->error("表单类型出错！");
            return;
        }

        switch ($type) {
            case 1 :
                $table = "faq";
                break;
            case 2 :
                $table = "msg";
                break;
			case 3 :
                $table = "cpdd"; //自定义的产品订单表
                break;
            default :
                $this->error("表单类型出错！");
                return;
        }

        $data = $_POST;
        //附加信息
        switch ($type) {
            case 1 :
                $data['time'] = time();
                $data['check'] = 2;
                $data['channel'] = 19;
                $data['siteid'] = getSiteId();
                break;
            case 2 :
			    //留言时间字段存在 msg_date 为 varchar 类型时使用 
               // $data['msg_date'] = date('Y-m-d', time());
                break;
			case 3 :
                //自定义的产品订单表使用
			    //$data['porder']="p".date('Ymdhms', time()); //产品订单号 根据时间生成
				//$data['sffk'] = "no";	  // 是否付款了
                break;
                
            default :
                break;
        }

        $m = M($table);
        $m->create($data);
        if ($m->add() !== false) {
			 if($type==3){
				 
			   $url= "http://".$_SERVER['HTTP_HOST']."/plus/alipay/alipayapi.php";

			   $post_data['WIDout_trade_no']       = "p".date('Ymdhms', time());
               $post_data['WIDsubject']      =  $data['title'] ;
               $post_data['WIDtotal_fee'] =  $data['price'];
			   $post_data['WIDbody'] =  ''; //订单描述
			   $post_data['WIDshow_url'] =  ''; //商品展示地址
              
			   $ch = curl_init();

               curl_setopt ($ch, CURLOPT_URL, $url);

               curl_setopt ($ch, CURLOPT_POST, 1);
               
               if($post_data != ''){

                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

                }

               curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 

               curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);

               curl_setopt($ch, CURLOPT_HEADER, false);
                 
               $file_contents = curl_exec($ch);

               curl_close($ch);
               
               echo $file_contents;
			   
			   die();
			 }
 
            $this->success("您的信息已经成功提交！");
            return;
        } else {
            $this->success("信息提交失败！请稍后再试！");
            return;
        }
    }
}
?>