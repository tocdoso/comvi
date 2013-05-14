<?php
namespace Comvi\Core;

/**
 * Declare Document Aware trait.
 *
 * @package		Comvi.Core
 */
trait DocumentAwareTrait
{
	protected $document;

	public function setDocument(AbstractDocument $document)
	{
		$this->document = $document;
	}
}
