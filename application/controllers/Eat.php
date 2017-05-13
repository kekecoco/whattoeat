<?php
/**
 * Created by PhpStorm.
 * User: Wu Lihua <maikekechn@gmail.com>
 * Time: 2017/5/7 下午8:19
 */

class EatController extends Yaf_Controller_Abstract {

	public function indexAction()
	{
		$params = $this->getParams();

		$response = [];
		if (!$params) {
			$response['ec'] = 400;
			$response['em'] = '参数错误,必须填写当前标志地点。';
			exit(json_encode($response));
		}

		// landmark坐标获取
		$keyword_params = [];
		$keyword_params['keywords'] = $params['landmark'];
		$keyword_params['keywords'] = $params['landmark'];



	}


	private function getIp()
	{


	}



	/**
	 * @return array|bool
	 */
	private function getParams()
	{
		$params = [];
		$params['landmark'] = $this->getRequest()->getPost('landmark', '');
		$params['range'] = $this->getRequest()->getPost('range', 1);
		$params['feel'] = $this->getRequest()->getPost('feel', '');
		$params['eat_fav'] = $this->getRequest()->getPost('eat', '');

		// 参数检查
		if ($params['landmark'] == '') {
			return false;
		} else {
			return $params;
		}
	}
}