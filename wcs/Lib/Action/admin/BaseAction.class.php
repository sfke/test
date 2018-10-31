<?php

class BaseAction extends Action
{
    function _initialize()
    {
        import('ORG.Util.Cookie');
        // 用户权限检查
        if (C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE')))) {
            import('ORG.Util.RBAC');
            if (!RBAC::AccessDecision()) {
                //检查认证识别号
                if (!session(C('USER_AUTH_KEY'))) {
                    //跳转到认证网关
                    redirect(PHP_FILE . C('USER_AUTH_GATEWAY'));
                }
                // 没有权限 抛出错误
                if (C('RBAC_ERROR_PAGE')) {
                    // 定义权限错误页面
                    redirect(PHP_FILE . C('RBAC_ERROR_PAGE'));
                } else {
                    if (C('GUEST_AUTH_ON')) {
                        $this->assign('jumpUrl', PHP_FILE . C('USER_AUTH_GATEWAY'));
                    }
                    // 提示错误信息
                    $this->error(L('_VALID_ACCESS_'));
                }
            }
        }
        $this->startup();
        if(C('SYS_RECYCLE_MODE')) $this->CleanRecycle();
    }

    protected function startup()
    {
        //导入栏目权限类
        import('@.Class.Permission');
        $this->assign("defaultimg", C('SYS_DEFAULT_IMG'));
    }

    protected function CleanRecycle(){
        //处理栏目
        $m = new ArctypeModel();
        $map['status'] = -1;
        $map['recycledate'] = array('lt', strtotime("-30 day"));
        $find = $m->where($map)->getField('id', true);
        if ($find) {
            $condition['id'] = array('in', $find);
            $m->where($condition)->delete();
        }
        //处理文章
        $m2 = new ArchivesModel();
        $map2['status'] = -1;
        $map2['recycledate'] = array('lt', strtotime("-30 day"));
        $find2 = $m2->where($map2)->getField('id', true);
        if ($find2) {
            $m3 = new ChannelModel();
            $channelArr = $m3->field('id,addtable')->select();
            $addtableArr = array();
            foreach ($channelArr as $v) {
                $addtableArr[$v['id']] = $v['addtable'];
            }
            foreach ($find2 as $v) {
                $temp = $m2->getOne($v);
                $addtable = $addtableArr[$temp['channel']];
                $m2->_link['addfields']['class_name'] = $addtable;
                $m2->relation(true)->delete($v);
            }
        }
    }
}