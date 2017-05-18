<?php
/**
 * Created by PhpStorm.
 * User: Wu Lihua <maikekechn@gmail.com>
 * Time: 2017/5/7 下午8:19
 */
use \Curl\Curl;

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

		// 配置项获取
		$config_path = APPLICATION_PATH . '/conf/application.ini';
		$config = new Yaf_Config_Ini($config_path);
		$map_config = $config->map->get('map');

		if (substr_count($params['landmark'], ',') > 0) {
			$keyword = implode('|', explode(',', $params['landmark']));
		} else {
			$keyword = $params['landmark'];
		}

		// IP获取
		$ip = $this->getIp();

		if ('unknown' == $ip) {
			$response['ec'] = 400;
			$response['em'] = '无法获取当前IP！';
			exit(json_encode($response));
		}

		// IP定位
		$location_params = [];
		$location_params['key'] = $map_config->key;
		$location_params['ip'] = '106.37.74.57';

		$ip_location = $this->sendRequest($map_config->ip, 'get', $location_params);

		if (0 == $ip_location->status) {
			if (!empty($params)) {
				$city = $params['city'];
			} else {
				$response['ec'] = 400;
				$response['em'] = '无法获取当前所在城市！';
				exit(json_encode($response));
			}
		} else {
			$city = $ip_location->city;
		}

		// landmark坐标获取
		$keyword_params = [];
		$keyword_params['key'] = $map_config->key;
		$keyword_params['keywords'] = $keyword;
		$keyword_params['city'] = $city;
		$keyword_params['citylimit'] = true;
		$keyword_params['offset'] = 10;

		$keyword_res = $this->sendRequest($map_config->keyword_search, 'get',$keyword_params);

		if (0 == $keyword_res->status) {
			$response['ec'] = 400;
			$response['em'] = '无法获取当前位置坐标！';
			exit(json_encode($response));
		}

		$coordinate = $keyword_res->pois[0]->location;

		if (isset($range)) {
			switch ($range) {
				case 1:
					$range = 1000;
					break;
				case 2:
					$range = 2000;
					break;
				case 3:
					$range = 5000;
					break;
			}
		} else {
			$range = 1000;
		}

		// 周边搜索
		$around_params = [];
		$around_params['key'] = $map_config->key;
		$around_params['location'] = $coordinate;
		$around_params['keywords'] = '美食';
		$around_params['city'] = $city;
		$around_params['radius'] = $range;
		$around_params['sortrule'] = 'weight';
		$around_params['page'] = mt_rand(1, 20);

		$around_search = $this->sendRequest($map_config->around_search, 'get', $around_params);

		$choose_rand = mt_rand(0, count($around_search->pois));

		$choose = [];
		$choose['name'] = $around_search->pois[$choose_rand]->name;
		$choose['detail'] = $around_search->pois[$choose_rand]->type;
		$choose['address'] = $around_search->pois[$choose_rand]->address;
		$choose['time'] = time();

		echo json_encode($choose);
		return false;
	}

	/**
	 * 获取IP地址
	 * @return string
	 */
	private function getIp()
	{
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['REMOTE_ADDR'])) {
			$ip = $_SERVER['REMOTE_ADDR'];
		} else {
			$ip = 'unknown';
		}

		return $ip;
	}

	/**
	 * @param $requestUrl
	 * @param string $method
	 * @param $params
	 * @return
	 */
	private function sendRequest($requestUrl, $method='GET',$params)
	{
		if (empty($requestUrl)) {
			return false;
		}

		// 请求头设置
		$curl = new Curl();
		$curl->setHeader('Content-type', 'application/json');

		if ('get' === strtolower($method)) {
			if (is_array($params)) {
				$curl->get($requestUrl, $params);
			}
		} elseif ('post' === strtolower($method)) {
			if (is_array($params)) {
				$curl->post($requestUrl, $params);
			}
		}

		if ($curl->error) {
			return false;
		} else {
			return $curl->response;
		}
	}

	/**
	 * @return array|bool
	 */
	private function getParams()
	{
		$params = [];
		$params['landmark'] = $this->getRequest()->getPost('landmark', '');
		$params['city'] = $this->getRequest()->getPost('city', '');
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