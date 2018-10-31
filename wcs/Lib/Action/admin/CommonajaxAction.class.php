<?php

class CommonajaxAction extends Action
{

    public function getLocalHash()
    {
        $m = M('filehash');
        $arr = $m->field('realfile')->select();
        if (!empty($arr)) {
            $fileArr = array();
            foreach ($arr as $v) {
                $fileArr[] = $v['realfile'];
            }
            echo json_encode($fileArr);
            return;
        } else {
            echo -1;
            return;
        }
    }


    public function checkLocalHash()
    {
        $clearM = M();
        $table = C('DB_PREFIX') . "checkinfo";
        $sql = "truncate table " . $table;
        if ($clearM->execute($sql) === false) {
            echo -1;
            return;
        }
        traverse(APP_REAL_PATH . 'lib', 'php', 'checkLocalDbfileHash');
        $m = M('checkinfo');
        $infoArr = $m->field('realfile,type')->select();
        if (empty($infoArr)) {
            echo 1;
            return;
        } else {
            $fileArr = array();
            $i = 0;
            foreach ($infoArr as $v) {
                $fileArr[$i]['file'] = $v['realfile'];
                $fileArr[$i]['type'] = $v['type'];
                $i++;
            }
            echo json_encode($fileArr);
            return;
        }
    }


    public function getSysconfig()
    {
        $m = M('sysconfig');
        $arr = $m->field('id,varname,value')->where("varname in ('JL_SERVER','JL_HTML_CACHE','SYS_SAFE_MODE','SYS_FILTER','SYS_DEFAULT_EDITOR','SYS_RECYCLE_MODE','SYS_DATETIME_MODE')")->select();
        if (empty($arr)) {
            echo -1;
            return;
        } else {
            echo json_encode($arr);
        }
    }


    public function sysconfigSwitch()
    {
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $value = isset($_POST['value']) ? $_POST['value'] : 1;
        $m = M('sysconfig');
        $data['value'] = $value;
        $data['id'] = $id;
        $m->create($data);
        if ($m->save() === false) {
            echo -1;
            return;
        } else {
            init_sysconfig();
            echo 1;
            return;
        }
    }

    public function resumeMoreInfo()
    {
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $m = M('Resume');
        $arr = $m->where("id = $id")->find();

        if (!empty($arr)) {
            $html = '<table width="100%" style="border: 1px solid #9AA0AE;background:#EBECF1;">
				<tr>
				<td colspan="3">应聘职位：' . $arr['gangwei'] . '</td>
				</tr>
				<tr>
				<td width="30%">姓名：' . $arr['name'] . '</td>
				<td width="30%">性别： ' . $arr['sex'] . '</td>
				<td width="30%">婚姻状况：  ' . $arr['marriaged'] . '</td>
				</tr>
				<tr>
				<td width="30%">身高：' . $arr['high'] . '</td>
				<td width="30%">出生年月： ' . $arr['year'] . ' 年 ' . $arr['month'] . ' 月 </td>
				<td width="30%">民族：  ' . $arr['nation'] . '</td>
				</tr>
				<tr>
				<td >电话：' . $arr['phone'] . '</td>
				<td colspan="2">E_mail：' . $arr['email'] . '</td>
				</tr>
				</table>';

            //echo $html;
            $rs['html'] = $html;
            $rs['code'] = 1;

        } else {
            $rs['code'] = -1;
            $rs['html'] = "";
        }
        echo json_encode($rs);
    }


    /*
            public function mallorderInfo(){
                $id = isset($_POST['id'])?$_POST['id']:null;
                $m = new MultitableModel('resume');
                $arr = $m->where("id = $id")->mfind();

                if(!empty($arr)){
                    $html = '<table width="100%" style="border: 1px solid #9AA0AE;background:#EBECF1;">
                    <tr>
                    <th colspan="3" scope="col" class="bgtit">个人基本资料</th>
                    </tr>
                    <tr>
                    <td width="30%">下单人姓名：'.$arr['name'].'</td>
                    <td width="30%">下单人手机： '.$arr['phone'].'</td>
                    <td width="30%">购买商品名称：  '.$arr['gname'].'</td>
                    </tr>
                    <tr>
                    <td colspan="3">购买商品编号：'.$arr['gsn'].'</td>
                    </tr>
                    <tr>
                    <td colspan="3">购买数量：'.$arr['num'].'</td>
                    </tr>
                    <tr>
                    <td colspan="3" >备注：'.$arr['mark1'].'</td>
                    </tr>
                    <tr>
                    <th colspan="3" scope="col" class="bgtit">收货地址</th>
                    </tr>
                    <tr>
                    <td colspan="3">收货人姓名：'.$arr['cname'].'</td>
                    </tr>
                    <tr>
                    <td >收货人电话：'.$arr['cphone'].'</td>
                    <td colspan="2">收货人地址：'.$arr['address'].'</td>
                    </tr>
                    <tr>
                    <td colspan="3">邮政编码：'.$arr['zcode'].'</td>
                    </tr>
                    <tr>
                    <td colspan="3" >收货备注：'.$arr['mark2'].'</td>
                    </tr>
                    </table>';

                    echo $html;
                    return;
                }else{
                    echo -1;
                    return;
                }
            }
    */

    //表单是否处理状态公共处理方法
    public function commonFormChangeStatus()
    {
        $id = !empty($_POST['id']) ? $_POST['id'] : null;
        $field = !empty($_POST['field']) ? $_POST['field'] : null;
        $status = isset($_POST['status']) ? $_POST['status'] : null;
        $table = isset($_POST['table']) ? $_POST['table'] : null;

        if ($field === null || $status === null || $table === null) {
            echo -1;
            return;
        } else {
            $m = M($table);
            $arr = $m->where('id =' . $id)->find();
            if (empty($arr)) {
                echo -3;
                return;
            } else {
                $data = array();
                $data['id'] = $id;
                $data[$field] = $status;
                if ($m->save($data) !== false) {
                    echo 1;
                    return;
                } else {
                    return -1;
                    return;
                }
            }
        }
        return;
    }
}
?>