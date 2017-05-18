<?php
/**
 * Created by PhpStorm.
 * User: Wu Lihua <maikekechn@gmail.com>
 * Time: 2017/5/14 下午8:40
 */
class RedisClientModel {

	protected $host;
	protected $port;
	protected $password;
	private static $redis_client;

	public function __construct($host, $port=6379, $password='')
	{
		$this->host = $host;
		$this->port = $port;
		$this->password = $password;
	}

	/**
	 * @return bool
	 */
	private function connect()
	{
		if (empty($this->host) || empty($this->port)) {
			return false;
		}

		self::$redis_client = phpiredis_connect('127.0.0.1', 6379);

		if (!empty($this->password)) {
			$response = phpiredis_command(self::$redis_client, ['auth', $this->password]);
			if ($response) {
				return true;
			} else {
				return false;
			}
		}
		return true;
	}

	/**
	 * @param array $operator
	 * @return bool
	 */
	public function singleCommand(array $operator)
	{
		if (!$this->connect()) {
			return false;
		}

		if (!is_array($operator)) {
			return false;
		}

		return phpiredis_command(self::$redis_client, $operator);
	}

	/**
	 * @param array $operator
	 * @return bool
	 */
	public function multiCommand(array $operator)
	{
		if (!$this->connect()) {
			return false;
		}

		if (!is_array($operator)) {
			return false;
		}

		return phpiredis_multi_command(self::$redis_client, $operator);
	}
}