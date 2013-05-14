<?php
namespace Comvi\Core;

/**
 * Declare Profiler Aware interface.
 *
 * @package		Comvi.Core
 */
interface ProfilerAwareInterface
{
	public function setProfiler(Profiler $profiler);
}
