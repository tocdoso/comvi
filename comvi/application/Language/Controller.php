<?php
namespace Language;
use Comvi\Core\AbstractController;
use Comvi\Core\ConfigAwareInterface;
use Comvi\Core\ConfigAwareTrait;
use Comvi\Core\URLHelperInterface;
use Comvi\Core\URLHelperTrait;
use Comvi\Core\RouterAwareInterface;
use Comvi\Core\RouterAwareTrait;
use Comvi\Core\DocumentAwareInterface;
use Comvi\Core\DocumentAwareTrait;

/**
 * Languages Controller.
 */
class Controller extends AbstractController implements ConfigAwareInterface, URLHelperInterface, RouterAwareInterface, DocumentAwareInterface
{
	use ConfigAwareTrait;
	use URLHelperTrait;
	use RouterAwareTrait;
	use DocumentAwareTrait;

	public function getIndex()
	{
		$view = $this->getView();

		return $view;
	}
}
?>