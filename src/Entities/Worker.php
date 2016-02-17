<?php

namespace Jupitern\Dispatcher\Entities;

use Jupitern\Dispatcher\Interfaces\WorkerInterface;
use Jupitern\Dispatcher\Interfaces\JobInterface;


class Worker implements WorkerInterface
{

	public $pid;
    public $name;
	public $description;

	public $errorMsg = null;

	/** @var array Lib\JobDispatcher\Interfaces\JobInterface */
    public $jobs;


	public function __construct( $name, $description, $pid = null )
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
	public static function instance( $name = '', $description = '', $pid = null )
	{
		return new static($name, $description, $pid);
	}

	/**
	 * Add a Job
	 *
	 * @param JobInterface $job
	 * @return static
	 */
	public function addJob( JobInterface $job )
	{
		$this->jobs[] = $job;
		return $this;
	}

	/**
	 * run worker
	 *
	 * @return boolean whether the job performed as expected
	 */
	public function run()
	{
		foreach ($this->jobs as $job) {
			$nextRunDate = $job->getNextRunDate();

			if ($nextRunDate instanceof \DateTime && $nextRunDate <= new \DateTime()) {
				if ($job->run() === false) {
					return false;
				}
			}
		}
		return true;
	}

}