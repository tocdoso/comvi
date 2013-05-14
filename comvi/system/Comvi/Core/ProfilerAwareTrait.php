<?php
namespace Comvi\Core;

/**
 * Declare Profiler Aware trait.
 *
 * @package		Comvi.Core
 */
trait ProfilerAwareTrait
{
	protected $profiler;

	public function setProfiler(Profiler $profiler)
	{
		$this->profiler = $profiler;
	}
}
