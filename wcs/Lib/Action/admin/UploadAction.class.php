<?php

/**
 * 模板管理
 * @author Administrator
 *
 */
class UploadAction extends BaseAction
{

    /**
     * 文件管理器
     */
    public function fileManage()
    {

         
        $realpath = $GLOBALS['_SERVER']["DOCUMENT_ROOT"].'/' .__Upload__;
        /*position指定以及一些问候信息*/
        $current = "文件管理器";
        $position = getPosition("文件管理器");
        $this->assign('current', $current);
        $this->assign('position', $position);
        $this->assign('welcome', getWelcome());
		$this->assign('uploadpath', str_replace("\\","/",$realpath));
        $this->display();
    }


}


?>