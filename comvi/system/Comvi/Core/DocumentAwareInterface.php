<?php
namespace Comvi\Core;

/**
 * Declare Document Aware interface.
 *
 * @package		Comvi.Core
 */
interface DocumentAwareInterface
{
	public function setDocument(AbstractDocument $document);
}
