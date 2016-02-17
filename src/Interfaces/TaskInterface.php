<?php

namespace Jupitern\Dispatcher\Interfaces;

interface TaskInterface
{

	/**
	 * Instance a Task
	 *
	 * @param array $params
	 * @param string $name
	 * @param string $description
	 * @param string $pid
	 * @return TaskInterface
	 */
	public static function instance($name = '', $description = '', $pid = null);

	/**
	 * set params
	 *
	 * @param array $name
	 * @param string $value
	 */
	public function setParams(array $params);

	/**
	 * set param
	 *
	 * @param array $name
	 * @param string $value
	 */
	public function setParam($name, $value);

	/**
	 * get param
	 *
	 * @param array $name
	 * @return mixed
	 */
	public function getParam($name);

	/**
	 * run task
	 *
	 * @return boolean whether the task performed without error
	 */
	public function run();

	/**
	 * execute task operations
	 *
	 * @return mixed task return value
	 */
	public function execute();

}