<?php

namespace Jupitern\Dispatcher\Entities;
use Jupitern\Dispatcher\Interfaces\JobInterface;
use Jupitern\Dispatcher\Interfaces\TaskInterface;
use \Exception;

class Job implements JobInterface
{

	public $pid;
    public $name;
    public $description;
	/** @var array Lib\JobDispatcher\Interfaces\TaskInterface */
    public $tasks = [];
	public $nextRunDate;

	public $success = false;
	public $errorMsg = '';

	/** @var \ArrayIterator */
	private $tasksIterator;
	private $taskSkip = false;
	private $terminateExecution = false;

	/** @var \Datetime */
	public $startDate = null;
	/** @var \Datetime */
	public $endDate = null;


	protected function __construct( $name, $description, $pid = null )
	{
		$this->pid = $pid ?: md5(uniqid().rand(1000, 9999));
		$this->name = $name;
		$this->description = $description;
	}

	/**
	 * Initializes the object.
	 *
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
	 * add task
	 *
	 * @return JobInterface
	 */
	public function addTask( TaskInterface $task )
	{
		$this->tasks[] = $task;
		return $this;
	}

	/**
	 * get task
	 *
	 * @return $task TaskInterface
	 */
	public function getTask( $pid )
	{
		foreach ($this->tasks as $task) {

			if ($task->pid == $pid) {
				return $task;
			}
		}
		return null;
	}

	/**
	 * execute job by runing all tasks
	 *
	 * @return boolean whether the job performed as expected
	 */
	public function run()
	{
		try {
			$this->init();
			$this->startDate = new \DateTime('NOW');

			// execute tasks
			$this->tasksIterator = (new \ArrayObject($this->tasks))->getIterator();

			while ($this->tasksIterator->valid()) {

				$task = $this->tasksIterator->current();

				if (method_exists($this, "beforeTask_".$task->pid)) {
					$this->{"beforeTask_".$task->pid}($task);
					if ($this->taskSkip) {
						$this->taskSkip = false;
						continue;
					}
					if ($this->terminateExecution) break;
				}

				try {
					$this->beforeTask($task);
					$task->run();
					$this->afterTask($task);
				}
				catch (Exception $e) {
					$this->afterTask($task);
					throw $e;
				}

				if (method_exists($this, "afterTask_".$task->pid)) {
					$this->{"afterTask_" . $task->pid}($task);
					if ($this->taskSkip) {
						$this->taskSkip = false;
						continue;
					}
					if ($this->terminateExecution) break;
				}

				$this->tasksIterator->next();
			}
			$this->endDate = new \DateTime('NOW');

			$this->shutdown();
		}
		catch (\Exception $e) {
			$this->errorMsg = $e->getMessage() .PHP_EOL . PHP_EOL. $e->getTraceAsString();
			$this->endDate = new \DateTime('NOW');
			return false;
		}
		return true;
	}

	/**
	 * set next task to execute in current job
	 * @param $taskPID task PID (Process ID)
	 * @return boolean whether skip is possible
	 */
	public function skipTo( $taskPID )
	{
		$this->taskSkip = true;
		$this->tasksIterator->rewind();
		while ($this->tasksIterator->valid()) {

			$task = $this->tasksIterator->current();
			if ($task->pid == $taskPID) {
				return;
			}
			$this->tasksIterator->next();
		}
	}

	/**
	 * skip all tasks and end job execution
	 */
	public function skipToEnd()
	{
		$this->terminateExecution = true;
	}

	/**
	 * set next run date
	 *
	 * @param \DateTime $datetime
	 * @return JobInterface
	 */
	public function setNextRunDate( \DateTime $datetime )
	{
		$this->nextRunDate = $datetime;
		return $this;
	}

	/**
	 * get next run date
	 *
	 * @return \DateTime DateTime
	 */
	public function getNextRunDate()
	{
		return $this->nextRunDate;
	}

	/**
	 * called right before job starts execution
	 */
	public function init(){}

	/**
	 * called right after job ends execution
	 */
	public function shutdown(){}

	/**
	 * called before each task is executed
	 */
	public function beforeTask(TaskInterface $task){}

	/**
	 * called after each task is executed
	 */
	public function afterTask(TaskInterface $task){}

}