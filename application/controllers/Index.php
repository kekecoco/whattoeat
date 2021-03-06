<?php
/**
 * @name IndexController
 * @author deepin
 */
class IndexController extends Yaf_Controller_Abstract {

	public function indexAction() {
		// 配置路径
		$config_path = APPLICATION_PATH . '/conf/application.ini';
		$config = new Yaf_Config_Ini($config_path);
		$public_config = $config->public->get('public');
		$url = $config->common->application->get('url');

		$this->getView()->assign('public', $public_config);
		$this->getView()->assign('url', $url);
        return TRUE;
	}
}
