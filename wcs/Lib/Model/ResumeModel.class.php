<?php

class ResumeModel extends MultitableModel
{


    public function ResumeModel($type = 1, $arr = null)
    {

        if (!empty($addtable)) {
            $addtable = $arr;
        } else {
            if ($type == 1) {
                $addtable = array("school" => "resumeSchool", "family" => "resumeFamily", "work" => "resumeWork", "science" => "resumeScience", "academic" => "resumeAcademic", "paper" => "resumePaper");
            } else if ($type == 2) {
                $addtable = array("school" => "resumeSchool", "family" => "resumeFamily", "work" => "resumeWork");
            }
        }
        parent::__construct("Resume", $addtable);
    }

    //用于每张分表只有一条数据
    public function madd($d = null)
    {
        $data = !empty($d) ? $d : $_POST;
        if (empty($data)) {
            return false;
        } else {
            $adddata = $data;
            foreach ($data as $k => $v) {
                if (strpos($k, "_") !== false) {
                    list($table, $field) = explode("_", $k);

                    if (is_array($v)) {
                        foreach ($v as $k2 => $v2) {
                            $adddata[$table][$k2][$field] = $v2;
                        }
                    } else {
                        $adddata[$table][0][$field] = $v;
                    }

                    unset($adddata[$k]);
                } else {
                    continue;
                }
            }


            //排除空项目
            foreach ($this->addTable as $k => $v) {
                foreach ($adddata[$k] as $k2 => $v2) {
                    $temp = array_filter($v2);
                    if (empty($temp)) {
                        unset($adddata[$k][$k2]);
                    } else {
                        continue;
                    }
                }
            }

            $bool = $this->mcreateAndAdd($adddata);
            return $bool;
        }
    }

    //用于每张分表只有多条数据
    public function mmadd()
    {

        /*          $schoolNum = count($data['schoolname']);
                    for($i=0;$i<$schoolNum;$i++){
                        $data['school'][$i]['schoolname'] = $data['schoolname'][$i];
                        $data['school'][$i]['education'] = $data['education'][$i];
                        $data['school'][$i]['major'] = $data['major'][$i];
                        $data['school'][$i]['graduate'] = $data['graduate'][$i];
                    }
        */

    }


    public function getData($id)
    {
        $arr = $this->where("id =" . $id)->mfind();
        return $arr;
    }


}


?>