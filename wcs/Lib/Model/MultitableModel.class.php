<?php


class MultitableModel extends Model
{

    protected $mainTable = null;
    protected $addTable = null;
    protected $mainTableInsertId;

    /**
     * @return mixed
     */
    public function getMainTableInsertId()
    {
        return $this->mainTableInsertId;
    }


    public function MultitableModel($mainTable, $addTable)
    {
        if (empty($mainTable) || empty($addTable)) {
            $this->mainTable = null;
            return null;
        } else {
            //$arr = C('EXT_MULTI_TABLE');
            $this->mainTable = $mainTable;
            $this->addTable = $addTable;
            parent::__construct($mainTable);
        }
    }


    public function mfind()
    {
        $originalArr = $this->find();
        $pk = $this->fields['_pk'];
        $id = $originalArr[$pk];

        if (!empty($this->addTable)) {
            foreach ($this->addTable as $k => $v) {
                $madd = M($v);
                $addArr = $madd->where("fid=$id")->select();
                $originalArr[$k] = $addArr;
            }
        }

        return $originalArr;
    }


    public function mselect()
    {

        $originalArr = $this->select();

        if (!empty($originalArr)) {
            foreach ($originalArr as $k => $v) {
                $pk = $this->fields['_pk'];
                $id = $originalArr[$k][$pk];
                foreach ($this->addTable as $k2 => $v2) {
                    $madd = M($v2);
                    $addArr = $madd->where("fid=$id")->select();
                    $originalArr[$k][$k2] = $addArr;
                }
            }
        }

        return $originalArr;
    }


    public function mcreateAndSave($data = null)
    {
        // 如果没有传值默认取POST数据
        if (empty($data)) {
            $data = $_POST;
        } elseif (is_object($data)) {
            $data = get_object_vars($data);
        } elseif (!is_array($data)) {
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }
        $error = array();

        if (!empty($this->addTable)) {
            foreach ($this->addTable as $k => $v) {
                if (!empty($data[$k])) {
                    $m = null;
                    $m = M($v);
                    foreach ($data[$k] as $k2 => $v2) {
                        $m->create($v2);
                        if ($m->save() === false) {
                            $error[] = $k;
                        }
                    }
                    unset($data[$k]);
                }
            }
        }

        $this->create($data);
        if ($this->save() === false) {
            return false;
        }

        return $error;
    }


    public function mcreateAndAdd($data = null)
    {
        // 如果没有传值默认取POST数据
        if (empty($data)) {
            $data = $_POST;
        } elseif (is_object($data)) {
            $data = get_object_vars($data);
        } elseif (!is_array($data)) {
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }
        $error = array();

        $this->create($data);
        if ($this->add() === false) {
            return false;
        }

        $fid = $this->getLastInsID();
        $this->mainTableInsertId = $fid;

        if (!empty($this->addTable)) {
            foreach ($this->addTable as $k => $v) {
                if (!empty($data[$k])) {
                    $m = null;
                    $m = M($v);
                    foreach ($data[$k] as $k2 => $v2) {
                        $v2['fid'] = $fid;
                        $m->create($v2);
                        if ($m->add() === false) {
                            $error[] = $k;
                        }
                    }
                    unset($data[$k]);
                }
            }

        }

        return $error;
    }

    public function mdelete($id)
    {
        if (empty($id)) {
            return false;
        } else {
            if ($this->where("id in ('" . $id . "')")->delete() === false) {
                return false;
            } else {
                if (!empty($this->addTable)) {
                    foreach ($this->addTable as $k => $v) {
                        $madd = M($v);
                        $addArr = $madd->where("fid in ('" . $id . "')")->delete();
                    }
                }
                return true;
            }
        }
        return false;
    }

}


?>