<?php
namespace Comvi\Core;
use Comvi\Core\Helper\Number;

/**
 * Display benchmark results: total execution time, memory usage, queries you have run.
 *
 * This information can be useful during development in order to help with
 * debugging and optimization.
 *
 * @package		Comvi.Core
 */
class Profiler
{
	protected $name;
	protected $markers = array();


	/**
	 * Constructor.
	 */
	public function __construct($name = 'application')
	{
		$this->name = $name;
	}

	/**
	 * Mark start point
	 */
	public function mark($time = null, $memory_usage = null)
	{
		$this->markers['time']			= ($time !== null) ? $time : microtime(true);
		$this->markers['memory_usage']	= ($memory_usage !== null) ? $memory_usage : memory_get_usage();
	}

	/**
	 * Get total execution time
	 */
	public function getExecutionTime($precision = 4)
	{
		$elapsed = microtime(true) - $this->markers['time'];

		return round($elapsed, $precision);
	}

	/**
	 * Get the memory used
	 */
	public function getMemoryUsed($byte_format = true)
	{
		$used = memory_get_usage() - $this->markers['memory_usage'];

		if ($byte_format === true) {
			return Number::formatByte($used);
		}

		return $used;
	}
}
?>