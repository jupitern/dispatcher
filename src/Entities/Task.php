<?php

namespace Jupitern\Dispatcher\Entities;
use Jupitern\Dispatcher\Interfaces\TaskInterface;


abstract class Task implements TaskInterface
{

	public $pid;
    public $name;
    public $description;
    public $params;

	public $success = false;
	public $errorMsg = '';
	public $output = null;      // task return value

	/** @var \Datetime */
    public $startDate = null;
	/** @var \Datetime */
    public $endDate = null;


	protected function __construct($name = '', $description = '', $pid = null)
	{
		$this->name = $name;
		$this->description = $description;
		$this->pid = $pid ?: md5(uniqid().rand(1000, 9999));
	}

	/**
	 * set params
	 *
	 * @$params array $name associative array [param => value, ...]
	 * @param string $value
	 */
	public function setParams(array $params)
	{
		$this->params = $params;
		return $this;
	}

	/**
	 * set param
	 *
	 * @param array $name
	 * @param string $value
	 */
	public function setParam($name, $value)
	{
		$this->params[$name] = $value;
	}

	/**
	 * get param
	 *
	 * @param array $name
	 * @return mixed
	 */
	public function getParam($name)
	{
		return $this->params[$name];
	}

	/**
	 * Initializes the object.
	 *
	 * @param array $params
	 * @param string $name
	 * @param string $description
	 * @param string $pid
	 * @return static
	 */
	public static function instance($name = '', $description = '', $pid = null)
	{
		return new static($name, $description, $pid);
	}

	/**
	 * run task operations
	 *
	 * @return mixed calls execute method and returns its value
	 */
	public function run()
	{
		try{
			$this->startDate = new \DateTime();
			$this->output = $this->execute();
			$this->endDate = new \DateTime();
			$this->success = true;
		}
		catch (\Exception $e) {
			$this->errorMsg = $e->getMessage();
			$this->success = false;
			$this->endDate = new \DateTime();
			throw new \Exception("Task '{$this->pid}' error: {$e->getMessage()}");
		}
		return $this->output;
	}

	/**
	 * execute task operations
	 *
	 * @return mixed task return value
	 */
	abstract public function execute();

}