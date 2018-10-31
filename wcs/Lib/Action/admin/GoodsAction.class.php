<?php

// 本类由系统自动生成，仅供测试用途
class GoodsAction extends BaseAction {
		
	
		public function show(){
			import('ORG.Util.Page');// 导入类
			$m = new GoodsModel();
			if(!empty($_POST['action'])){
				$tid = isset($_POST['tid'])?$_POST['tid']:null;
				if($tid!=null){
					$page_params = 'tid='.$tid;
				}
				
				
				if($_POST['action'] == 'filter'){
					if(!empty($_POST['orderby'])){
						$arr = orderByParse($_POST['orderby']);
						if(is_array($arr)){
							$map_orderby = "`$arr[0]` $arr[1]";
						}
						$orderby = $_POST['orderby'];
						setcookie("orderby_goods", $_POST['orderby']);
						setcookie("map_orderby_gooda", $map_orderby);
			
					}
			
					if(!empty($_POST['searchkey'])){
						$this->assign('searchkey',$_POST['searchkey']);
						$map[$_POST['searchby']] = array('like','%'.$_POST['searchkey'].'%');
					}
				}
			}else{
				$tid = isset($_GET['tid'])?$_GET['tid']:null;
				if(!empty($_COOKIE['map_orderby_gooda'])){
			
					$map_orderby = $_COOKIE['map_orderby_gooda'];
					$orderby = $_COOKIE['orderby_goods'];
				}
			
			
			}
				
			if($tid!=null){
				//获取栏目旗下所有子栏目数据
				$isParentArctype = isParent('category',$tid);
				if($isParentArctype){
					$tids = getAllSon('category',$tid);
					$tids .=",".$tid;
					$map['catid'] = array('in',$tids);
				}else{
					$map['catid'] = array('eq',$tid);
				}
				$this->assign('tid',$tid);
			}
			
			//排序
			$map_orderby = !empty($map_orderby)?$map_orderby:'id desc';
			
			$count = $m->where($map)->count();// 查询满足要求的总记录数
			$Page  = new Page($count,C('SYS_PAGE_SIZE'),$page_params);// 实例化分页类 传入总记录数和每页显示的记录数
			$show  = $Page->show();// 分页显示输出
				
			//如果是搜索，则在一页内显示所有数据(不分页)
			if(!empty($map[$_POST['searchby']])) {
				$Page->listRows = 1000;
				$show = "一共搜索到 ".$count." 条数据";
			}
				
			$list = $m->where($map)->limit($Page->firstRow.','.$Page->listRows)->order($map_orderby)->select();
			$this->assign('list',$list);// 赋值数据集
			$this->assign('page',$show);// 赋值分页输出
				
				 	
			/*select准备*/
			$arrOrderby = array('id_desc'=>'ID 降序','id_asc'=>'ID 升序','price_desc'=>'价格降序','price_asc'=>'价格升序','number_desc'=>'库存降序','number_asc'=>'库存升序','catid_desc'=>'所属栏目 升序','catid_asc'=>'所属栏目 降序');
			$orderby_html = getOptions($arrOrderby,$orderby);
			$arrSearchby = array('name'=>'商品名称','id'=>'商品id','sn'=>'商品货号','price'=>'商品价格');
			$searchby_html = getOptions($arrSearchby,$_POST['searchby']);
			
			$this->assign('orderby_html',$orderby_html);
			$this->assign('searchby_html',$searchby_html);
				
				
			/*position指定以及一些问候信息*/
			$current = "商品管理列表";
			$position = getPosition(array("商品栏目列表"=>"__GROUP__/category/show","商品管理列表"=>''));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
				
			$this->display();

		}

		
		public function add(){
			
			require(APP_INC_PATH.'form/Zebra_Form.php');
				
			$m2 = new CategoryModel();
			$categoryArr = $m2->categorySelectArr(); 
			$categoryArr[0] =" - 请选择 - ";
			$categoryHtml = selectArrToHtml($categoryArr);
			$this->assign('categoryHtml',$categoryHtml);
			
			$m4 = new GoodstypeModel();
			/*商品种类下拉选框*/
			$html = $m4->getSelectHtml();
			$this->assign('select_html',$html);
			$this->assign('action','add');
			
			$m3 = M('brand');
			$brandTemp = $m3->order('id asc')->select();
			if(empty($brandTemp)) {
				$brandArr[0] ="其他";
			}else{
				$brandArr[0] ="其他";
				foreach($brandTemp as $v){
					
					$brandArr[$v['id']] = $v['name'];
					
				}
				
			}
				
			$form = new Zebra_Form('form','post',U('shopform/goodsadd'));
				
			$form->add('label', 'label_name', 'name', '商品名称:',array('style'=>'width:80px;'));
			$obj = & $form->add('text', 'name','');
			$obj->set_rule(array(
					'required' => array('error', '请输入商品名称!')
			));
				
/* 			$form->add('label', 'label_color', 'color', '名称颜色:'); 
			$obj = & $form->add('color', 'color','#000000',array("style"=>"width:80px;"));  
			$obj->set_rule(array(
					//'required' => array('error', '请输入字段默认值!')
			)); */
			
			
			$form->add('label', 'label_sn', 'sn', '商品货号:');
			$obj = & $form->add('text', 'sn','');
			$obj->set_rule(array(
					//'required' => array('error', '必须填写商品货号!')
			));
				
			$form->add('label', 'label_catid', 'catid', '商品类别 :');
			$obj = & $form->add('select', 'catid', '');
			$obj->add_options($categoryArr,true);
			$obj->set_rule(array(
					'required' => array('error', '请输入字段名称!')
			));

/* 			$form->add('label', 'label_brand', 'brand', '商品品牌:');
			$obj = & $form->add('select', 'brand', '');
			$obj->add_options($brandArr,true); */
			$form->add('label', 'label_brand', 'brand', '商品品牌:');
			$obj = & $form->add('text', 'brand','');
			
			
/* 			$form->add('label', 'label_number', 'number', '商品库存：');
			$obj = & $form->add('text', 'number','99',array("style"=>"width:80px;"));
			$obj->set_rule(array(
					'required' => array('error', '请输入字段名称!'),
					'alphanumeric' =>array('.','error','数值不合法！')
			)); */

			$form->add('label', 'label_price', 'price', '会员售价：'); 
			$obj = & $form->add('text', 'price','',array("style"=>"width:80px;"));
			$obj->set_rule(array(
					'required' => array('error', '请输入字段名称!'),
					'alphanumeric' =>array('.','error','数值不合法！')
			));
			
			$form->add('label', 'label_mprice', 'mprice', '原售价：'); 
			$obj = & $form->add('text', 'mprice','',array("style"=>"width:80px;"));
			$obj->set_rule(array(
					'required' => array('error', '请输入字段名称!'),
					'alphanumeric' =>array('.','error','数值不合法！！')
			));
			
/* 			$form->add('label', 'label_weight', 'weight', '单重（克）：');
			$obj = & $form->add('text', 'weight','',array("style"=>"width:80px;"));
			$obj->set_rule(array(
					'required' => array('error', '请输入字段名称!'),
					'alphanumeric' =>array('.','error','数值不合法！')
			));
			
			$form->add('label', 'label_gcolor', 'gcolor', '可选颜色:');
			$obj = & $form->add('textarea', 'gcolor',''); */

			$form->add('label', 'label_litpic', 'litpic', '商品图片:'); 
			$obj = & $form->add('kimg', 'litpic','',array('style' => 'width:400px'));
			$obj->set_rule(array(
					//'required' => array('error', '请输入字段默认值!')
			));
				
/* 			$form->add('label', 'label_desc', 'desc', '商品简介:'); 
			$obj = & $form->add('textarea', 'desc',''); */
			
			
			$form->add('label', 'label_txt', 'txt', '详细描述:'); //空间名  id  for属性 里面的话
			$obj = & $form->add('kind', 'txt','',array('style'=>'width:700px;height:300px;'));  //可以改id
			$obj->set_rule(array(
					//'required' => array('error', '请输入字段默认值!')
			));
				
			
			$form->add('label', 'label_status', 'status', '状态:');
			$obj = & $form->add('radios', 'status', array(
					'1' =>  '上架',
					'0' =>  '下架'
			),'1');
			$obj->set_rule(array(
					'required' => array('error', '必须选择状态！')
			));
			
			$form->add('submit', 'btnsubmit', '确定');
			$form_html =  $form->render('*horizontal');
			$this->assign('form_html',$form_html);
					
			/*position指定以及一些问候信息*/
			$current = "商品添加";
			$position = getPosition(array('商品管理列表'=>'__GROUP__/goods/show','商品添加'=>''));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
			
	
			$this->display('form');
		}

		public function edit(){
			$gid = isset($_GET['gid'])?$_GET['gid']:null;
			if($gid == null){
				$this->error("读取商品id失败！");
			}
			require(APP_INC_PATH.'form/Zebra_Form.php');
			$m = new GoodsModel();
			$arr = $m->where('id='.$gid)->find();
			
			
			
			$m5 = M('goodsAttr');
			$goodsattrArr =  $m5->where('gid='.$gid)->find();
			$d_gtid = 0;
			if(!empty($goodsattrArr)){ 
				$d_attrid = $goodsattrArr['attrid'];
				$m6 = M('attribute');
				$d_attr = $m6->where('id='.$d_attrid)->find();
				if(!empty($d_attr)){
					$d_gtid = $d_attr['fid'];
				}
			}
			$this->assign('d_gtid',$d_gtid);
			
			
			$m4 = new GoodstypeModel();
			/*商品种类下拉选框*/
			$html = $m4->getSelectHtml($d_gtid);
			$this->assign('select_html',$html);
			$this->assign('action','edit');
			
			$m2 = new CategoryModel();
			$categoryArr = $m2->categorySelectArr();
			$categoryArr[0] =" - 请选择 - ";
			$categoryHtml = selectArrToHtml($categoryArr);
			$this->assign('categoryHtml',$categoryHtml);
			
			$m3 = M('brand');
			$brandTemp = $m3->order('id asc')->select();
			if(empty($brandTemp)) {
				$brandArr[0] ="其他";
			}else{
				$brandArr[0] ="其他";
				foreach($brandTemp as $v){
					$brandArr[$v['id']] = $v['name'];
				}
		
			}
			
			
			//商品相册
			$m7 = M('goodsImages');
			$goodsImgArr = $m7->where('gid='.$gid)->select();
			$goodsImgHtml = '';
			$j = 0;
			foreach($goodsImgArr as $v){
				if($j%2==0) $flag = "even";
				else $flag="";
				$j++;
				$goodsImgHtml.='<tr class="row '.$flag.'"><td><img style="border:1px solid #1A1A1A"  src="'.$v['url'].'" width="80px" /></td><td><label>标题：</label></td><td><input type="hidden" name="images[]" value="'.$v['url'].'" ></input><input type="text" class="control text" name="intro[]" value="'.$v['intro'].'" ></input></td><td><input type="button" class="control button"  value="移除" onclick="removeImg(this);" ></td></tr>';
				
			}
			$this->assign('goodsImgHtml',$goodsImgHtml);
			
			
			
			$form = new Zebra_Form('form','post',U('shopform/goodsedit'));
			$form->add('text', 'id',$gid,array('type' => 'hidden'));
			$form->add('label', 'label_name', 'name', '商品名称:',array('style'=>'width:80px;'));
			$obj = & $form->add('text', 'name',$arr['name']);
			$obj->set_rule(array(
					'required' => array('error', '请输入商品名称!')
			));
		
/* 			$form->add('label', 'label_color', 'color', '名称颜色:');
			$obj = & $form->add('color', 'color',$arr['color'],array("style"=>"width:80px;"));
			$obj->set_rule(array(
					//'required' => array('error', '请输入字段默认值!')
			)); */
				
				
			$form->add('label', 'label_sn', 'sn', '商品货号:');
			$obj = & $form->add('text', 'sn',$arr['sn']);
			$obj->set_rule(array(
					//'required' => array('error', '必须填写商品货号!')
			));
		
			$form->add('label', 'label_catid', 'catid', '商品类别 :');
			$obj = & $form->add('select', 'catid', $arr['catid']);
			$obj->add_options($categoryArr,true);
			$obj->set_rule(array(
					'required' => array('error', '请输入字段名称!')
			));
		
/* 			$form->add('label', 'label_brand', 'brand', '商品品牌:');
			$obj = & $form->add('select', 'brand', $arr['brand']);
			$obj->add_options($brandArr,true); */
			$form->add('label', 'label_brand', 'brand', '商品品牌:');
			$obj = & $form->add('text', 'brand',$arr['brand']);
			
			
/* 			$form->add('label', 'label_number', 'number', '商品库存：');
			$obj = & $form->add('text', 'number',$arr['number'],array("style"=>"width:80px;"));
			$obj->set_rule(array(
					'required' => array('error', '请输入字段名称!'),
					'alphanumeric' =>array('.','error','命名不合法！')
			)); */
		
			$form->add('label', 'label_price', 'price', '会员售价：');
			$obj = & $form->add('text', 'price',$arr['price'],array("style"=>"width:80px;"));
			$obj->set_rule(array(
					'required' => array('error', '请输入字段名称!'),
					'alphanumeric' =>array('.','error','命名不合法！')
			));
				
			$form->add('label', 'label_mprice', 'mprice', '原售价：');
			$obj = & $form->add('text', 'mprice',$arr['mprice'],array("style"=>"width:80px;"));
			$obj->set_rule(array(
					'required' => array('error', '请输入字段名称!'),
					'alphanumeric' =>array('.','error','命名不合法！')
			));
				
/* 			$form->add('label', 'label_weight', 'weight', '单重（克）：');
			$obj = & $form->add('text', 'weight',$arr['weight'],array("style"=>"width:80px;"));
			$obj->set_rule(array(
					'required' => array('error', '请输入字段名称!'),
					'alphanumeric' =>array('.','error','数值不合法！')
			));
			
			$form->add('label', 'label_gcolor', 'gcolor', '可选颜色:');
			$obj = & $form->add('textarea', 'gcolor',$arr['gcolor']); */
		
			$form->add('label', 'label_litpic', 'litpic', '商品图片:');
			$obj = & $form->add('kimg', 'litpic',$arr['litpic'],array('style' => 'width:400px'));
			$obj->set_rule(array(
					//'required' => array('error', '请输入字段默认值!')
			));
		
/* 			$form->add('label', 'label_desc', 'desc', '商品简介:');
			$obj = & $form->add('textarea', 'desc',$arr['desc']); */
				
				
			$form->add('label', 'label_txt', 'txt', '详细描述:'); //空间名  id  for属性 里面的话
			$obj = & $form->add('kind', 'txt',$arr['txt'],array('style'=>'width:700px;height:300px;'));  //可以改id
			$obj->set_rule(array(
					//'required' => array('error', '请输入字段默认值!')
			));
		
				
			$form->add('label', 'label_is_on_sale', 'is_on_sale', '状态:');
			$obj = & $form->add('radios', 'is_on_sale', array(
					'1' =>  '上架',
					'0' =>  '下架'
			),$arr['is_on_sale']);
			$obj->set_rule(array(
					'required' => array('error', '必须选择状态！')
			));
				
			$form->add('submit', 'btnsubmit', '确定');
			$form_html =  $form->render('*horizontal');
			$this->assign('form_html',$form_html);
			
			$this->assign('gid',$gid);
		
			/*position指定以及一些问候信息*/
			$current = "商品修改";
			$position = getPosition(array('商品管理列表'=>'__GROUP__/goods/show?tid='.$arr['catid'],'商品修改'=>''));
			$this->assign('current',$current);
			$this->assign('position',$position);
			$this->assign('welcome',getWelcome());
				
				
			$this->display('form');
		}
		
		
		
		
		
		
}

