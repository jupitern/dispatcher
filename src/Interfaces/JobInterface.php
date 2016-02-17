<?php

namespace Jupitern\Dispatcher\Interfaces;
use Jupitern\Dispatcher\Interfaces\TaskInterface;

interface JobInterface {

	/**
	 * Instance a Job
	 *
	 * @param string $name
	 * @param string $description
	 * @param string $pid
	 * @return JobInterface
	 */
	public static function instance( $name = '', $description = '', $pid = null );

	/**
	 * Add task
	 *
	 * @param TaskInterface $task
	 * @return JobInterface
	 */
	public function addTask( TaskInterface $task );

	/**
	 * get task
	 *
	 * @return $task TaskInterface
	 */
	public function getTask( $pid );

	/**
	 * Execute job by executing all job tasks
	 *
	 * @return boolean whether the job performed as expected
	 */
	public function run();

	/**
	 * Set next task to execute
	 *
	 * @param string $taskPID task PID (Process ID)
	 * @return boolean whether skip is possible
	 */
	public function skipTo( $taskPID );

	/**
	 * skip all tasks and end job execution
	 */
	public function skipToEnd();

	/**
	 * set next run date
	 *
	 * @param \DateTime $datetime
	 */
	public function setNextRunDate( \DateTime $datetime );

	/**
	 * Get next run date
	 *
	 * @return \DateTime
	 */
	public function getNextRunDate();

	/**
	 * called right before job starts execution
	 */
	public function init();

	/**
	 * called right after job ends execution
	 */
	public function shutdown();

	/**
	 * called before a task is executed
	 *
	 * @param TaskInterface $task
	 */
	public function beforeTask(TaskInterface $task);

	/**
	 * called after a task is executed
	 *
	 * @param TaskInterface $task
	 */
	public function afterTask(TaskInterface $task);
}