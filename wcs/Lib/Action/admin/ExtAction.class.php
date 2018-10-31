<?php
class ExtAction extends BaseAction {

    protected $config = array(
        'cid'=>         null,
        'class'=>       __CLASS__,
        'map'=>         array(),
        'title'=>       "",
        'tpl'=>array(
            "show"=>    "ext/show",
            "add"=>     "ext/form",
            "edit"=>    "ext/form"
        ),
        'pagesize'=>    '20',
        'addparams'=>   '',
        'hidden'=>      array(),
        'sortable'=>    false,
        'sortField'=>   "",
        'filterable'=>  false,
        'filterField'=> "",
        'filterHtml'=>  "",
        'addable'=>     false,
        'deleteable'=>  false,
        'search'=>      array('like'=>array(),'orderby'=>array()),
        'returnUrl'=>   array('add'=>'','edit'=>'','jump'=>''),
        'handleWidth'=> '70px',
    );

    public function __construct(){

        $this->bindData($this->config,$this->_config);
        parent::__construct();
    }

    protected function bindData(&$parent, $data)
    {
        if (is_array($data))
        {

            foreach ($data as $k => $v)
            {
                if (is_array($v) && $this->isAssociative($v))
                {
                    //$parent[$k] = array();
                    $this->bindData($parent[$k], $v);
                }
                else
                {
                    $parent[$k] = $v;
                }
            }

        }

    }


    protected  function isAssociative($array)
    {
        if (is_array($array))
        {
            foreach (array_keys($array) as $k => $v)
            {
                if ($k !== $v)
                {
                    return true;
                }
            }
        }

        return false;
    }


    public function show(){
        import('ORG.Util.Page');

        $map = $this->config['map'];
        //处理检索排序
        $orderby = $this->_param('orderby');
        if(!empty($orderby) ){
            $arr = orderByParse($orderby);
            if(!empty($arr)){
                $map_orderby = "`$arr[0]` $arr[1]";
            }
        }

        $map_orderby = !empty($map_orderby)?$map_orderby:'`id` desc';
        $orderby = !empty($orderby)?$orderby:'id_desc';

        //查理查询
        $searchkey = $this->_param('searchkey');
        $searchby = $this->_param('searchby');
        if(!empty($searchkey)){
            $map[$searchby] = array('like','%'.$searchkey.'%');
            $this->assign("searchkey",$searchkey);
        }

        /*select准备*/
        $arrOrderby = $this->config['search']['orderby'];
        $orderby_html = getOptions($arrOrderby,$orderby);
        $arrSearchby = $this->config['search']['like'];
        $searchby_html = getOptions($arrSearchby,$searchby);
        $this->assign('orderby_html',$orderby_html);
        $this->assign('searchby_html',$searchby_html);

        $cid = $this->config['cid'];
        if(empty($cid)){
            $this->error("读取栏目模型出错！");
            return;
        }

        $m2 = new ChannelModel();
        $arr = $m2->field('nid,type,fieldset')->where('id='.$cid)->find();
        if(!empty($arr)){
            $addtable = $arr['nid'];
            $this->assign('addtable',$addtable);
            $m = M($addtable);
        }else{
            $this->error("模型类别不存在！");
            return;
        }

        //获取列表的头
        $head_list = array();
        $fieldset = unserialize($arr['fieldset']);
        foreach($fieldset as $k => $v){
            if(array_key_exists("display",$v) && $v['display'] == 1 ){
                $head_list[$k]['name'] = $v['intro'];
                $head_list[$k]['value'] = $v['name'];
            }
        }
        $this->assign("head_list",$head_list);

        //分页核心代码
        $count = $m->where($map)->count();
        $Page  = new Page($count,$this->config['pagesize'],$this->config['addparams']);
        $show  = $Page->show();
        $list = $m->where($map)->limit($Page->firstRow.','.$Page->listRows)->order($map_orderby)->select();
        $this->assign('list',$list);
        $this->assign('page',$show);

        /*position指定以及一些问候信息*/
        $current = $this->config['title']."管理列表";
        $position = getPosition($this->config['title']."管理列表");
        $this->assign('current',$current);
        $this->assign('position',$position);
        $this->assign('welcome',getWelcome());
        $this->assign("config",$this->config);
        $this->display($this->config['tpl']['show']);
    }



	public function add(){
		require(APP_INC_PATH.'form/Zebra_Form.php');
        $cid = $this->config['cid'];
		if(empty($cid)){
			$this->error("读取栏目模型出错！"); return;
		}

		$m = new ChannelModel();
		$arr = $m->where('id='.$cid)->find();
		$fields = unserialize($arr['fieldset']);
		$form = new Zebra_Form('form','post',U('extform/save'));
        $form->add('hidden','nid',$arr['nid']);
        $form->add('hidden','channel',$cid);

        if(method_exists($this,"addMoreForm")){
            $this->addMoreForm($form);
        }

        AutoForm($form,$fields);
		$form->add('submit', 'btnsubmit', '确定',array("returnUrl"=>$this->config['returnUrl']['add']));
		$rs = $form->render('*horizontal');
		$this->assign('form_html',$rs);


        /*position指定以及一些问候信息*/
        $current = $this->config['title']."添加";
        $position = getPosition(array($this->config['title'].'管理列表'=>'show',$current=>''));
        $this->assign('current',$current);
        $this->assign('position',$position);
        $this->assign('welcome',getWelcome());


		$this->display($this->config['tpl']['add']);
	}
	
	
	public function edit(){
		require(APP_INC_PATH.'form/Zebra_Form.php');
        $cid = $this->config['cid'];
		$aid=isset($_GET['aid'])?$_GET['aid']:null;

		if($aid == null || $cid == null){
			$this->error("读取内容 id 出错！");
			return;
		}
		$m2 = new ChannelModel();
		$arr2 = $m2->field('nid,addtable,fieldset,type,title')->where('id='.$cid)->find();
		$addtable = $arr2['addtable'];
		if(empty($addtable)){
			$this->error('读取附加表失败！'); return;
		}
		$fields = unserialize($arr2['fieldset']);

		$m = M($addtable);
		$odata = $m->where('id='.$aid)->find();
		$form = new Zebra_Form('form','post',U('extform/update'));
		$form->add('text', 'jurl',$this->config['returnUrl']['jump'],array('type' => 'hidden'));
		$form->add('text', 'nid',$arr2['nid'],array('type' => 'hidden'));
		$form->add('text', 'id',$aid,array('type' => 'hidden'));

        if(method_exists($this,"editMoreForm")){
            $this->editMoreForm($form);
        }

		AutoForm($form,$fields,$odata);
		$form->add('submit', 'btnsubmit', '确定',array("returnUrl"=>$this->config['returnUrl']['edit']));
		$rs = $form->render('*horizontal');
		$this->assign('form_html',$rs);

        /*position指定以及一些问候信息*/
        $current = $this->config['title']."修改";
        $position = getPosition(array($this->config['title'].'管理列表'=>'show',$current=>''));
        $this->assign('current',$current);
        $this->assign('position',$position);
        $this->assign('welcome',getWelcome());

		$this->display($this->config['tpl']['edit']);
	}
	

    //通常使用ajax异步删除取代该方法
	public function del(){

		$id = isset($_GET['aid'])?$_GET['aid']:null;
        $cid = $this->config['cid'];
        if($id == null || $cid == null ){
			$this->error("读取内容模型出错！");
			return;
		}
		$m2 = new ChannelModel();
		$arr2 = $m2->field('addtable,fieldset,type')->where('id='.$cid)->find();
		$addtable = $arr2['addtable'];
		if(empty($addtable)){
			$this->error('读取附加表失败！'); return;
		}

		if($arr2['type']==2){
			$m = M($addtable);
			if($m->where('id='.$id)->delete()!==false){
				$this->success("删除内容成功！"); return;
			}else{
				$this->error("删除内容失败！"); return;
			}
		}else{
			$this->error("模型类型不存在！"); return;
		}
			
	}
	
	

	public function export(){
		
		$cid = $this->config['cid'];
		if(empty($cid)){
			$this->error("读取栏目模型出错！"); return;
		}
		$m = new ChannelModel();
		$arr = $m->where('id='.$cid)->find();
		$fields = unserialize($arr['fieldset']);
		
        $m= M($arr['addtable']);   //查出数据
		$data=$m->where('1=1')->order('id desc')->select();
        $name=$arr['addtable'];    //生成的Excel文件文件名
	    $res=$this->exportexcel($data,$name);
		
		/*position指定以及一些问候信息*/
        $current = $this->config['title']."管理列表";
        $position = getPosition($this->config['title']."管理列表");
        $this->assign('current',$current);
        $this->assign('position',$position);
        $this->assign('welcome',getWelcome());
        $this->assign("config",$this->config);
        $this->display($this->config['tpl']['show']);
	}

	 /* 导出excel函数*/
    public function exportexcel($data,$name='Excel'){
		include_once(APP_INC_PATH .'excel/PHPExcel.php');
		include_once(APP_INC_PATH .'excel/PHPExcel/IOFactory.php');
         error_reporting(E_ALL);
         $objPHPExcel = new PHPExcel();

        /*以下是一些设置 ，什么作者  标题啊之类的*/
         $objPHPExcel->getProperties()->setCreator("")
                               ->setLastModifiedBy("")
                               ->setTitle("数据EXCEL导出")
                               ->setSubject("数据EXCEL导出")
                               ->setDescription("备份数据")
                               ->setKeywords("excel")
                               ->setCategory("result file");
         /*以下就是对处理Excel里的数据， 横着取数据，主要是这一步，其他基本都不要改*/
        foreach($data as $k => $v){
             $a=array_keys($data[$k]);
			 $fieldnum=count(array_keys($data[$k]));
             $num=$k+1;
			 $kk=3;
			 //Excel的第A列，$a[$kk]查出数组的键值，下面以此类推
			// $objPHPExcel->getActiveSheet()->setTitle('Simple');
             $objPHPExcel->setActiveSheetIndex(0)
                          ->setCellValue('A'.$num, ' '.$v[$a[$kk]])
						  ->setCellValue('B'.$num, ' '.$v[$a[$kk+1]])
						  ->setCellValue('C'.$num, ' '.$v[$a[$kk+2]])
						  ->setCellValue('D'.$num, ' '.$v[$a[$kk+3]])
						  ->setCellValue('E'.$num, ' '.$v[$a[$kk+4]])
						  ->setCellValue('F'.$num, ' '.$v[$a[$kk+5]])
						  ->setCellValue('G'.$num, ' '.$v[$a[$kk+6]])
						  ->setCellValue('H'.$num, ' '.$v[$a[$kk+7]])
						  ->setCellValue('I'.$num, ' '.$v[$a[$kk+8]])
						  ->setCellValue('J'.$num, ' '.$v[$a[$kk+9]])
						  ->setCellValue('K'.$num, ' '.$v[$a[$kk+10]])
						  ->setCellValue('L'.$num, ' '.$v[$a[$kk+11]])
						  ->setCellValue('M'.$num, ' '.$v[$a[$kk+12]])
						  ->setCellValue('N'.$num, ' '.$v[$a[$kk+13]])
						  ->setCellValue('O'.$num, ' '.$v[$a[$kk+14]])
						  ->setCellValue('P'.$num, ' '.$v[$a[$kk+15]])
						  ->setCellValue('Q'.$num, ' '.$v[$a[$kk+16]])
						  ->setCellValue('R'.$num, ' '.$v[$a[$kk+17]])
						  ->setCellValue('S'.$num, ' '.$v[$a[$kk+18]])
						  ->setCellValue('T'.$num, ' '.$v[$a[$kk+19]])
						  ->setCellValue('U'.$num, ' '.$v[$a[$kk+20]])
						  ->setCellValue('V'.$num, ' '.$v[$a[$kk+21]])
						  ->setCellValue('W'.$num, ' '.$v[$a[$kk+22]])
						  ->setCellValue('X'.$num, ' '.$v[$a[$kk+23]])
						  ->setCellValue('Y'.$num, ' '.$v[$a[$kk+24]])
						  ->setCellValue('Z'.$num, ' '.$v[$a[$kk+25]])
						  ;   
		      }
			 
            $objPHPExcel->getActiveSheet()->setTitle($name);
            $objPHPExcel->setActiveSheetIndex(0);
             header('Content-Type: application/vnd.ms-excel');
             header('Content-Disposition: attachment;filename="'.$name.'.xls"');
             header('Cache-Control: max-age=0');
             $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
             $objWriter->save('php://output');
             exit;
	      
      }


}