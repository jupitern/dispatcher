<?php

namespace Jupitern\Dispatcher\Interfaces;
use Jupitern\Dispatcher\Interfaces\JobInterface;

interface WorkerInterface
{

	/**
	 * Instance a Worker
	 *
	 * @param string $name
	 * @param string $description
	 * @param string $pid
	 * @return WorkerInterface
	 */
	public static function instance( $name = '', $description = '', $pid = null );

	/**
	 * add Job
	 *
	 * @param $job JobInterface job
	 * @return boolean whether the job performed as expected
	 */
	public function addJob( JobInterface $job );

	/**
	 * run worker
	 *
	 * @return boolean whether the job performed as expected
	 */
	public function run();

}