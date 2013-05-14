<?php
namespace Comvi\Core;

/**
 * Declare Router interface.
 *
 * @package		Comvi.Core
 */
interface RouterInterface
{
	public function parse(URI &$uri);
	public function build(URI &$uri);
}
