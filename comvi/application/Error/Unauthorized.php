<?php
namespace Error;
use Comvi\Core\AbstractController;
use Comvi\Core\CurrentURLAwareInterface;
use Comvi\Core\CurrentURLAwareTrait;
use Comvi\Core\RouterAwareInterface;
use Comvi\Core\RouterAwareTrait;

/**
 * Unauthorized Controller
 */
class Unauthorized extends AbstractController implements CurrentURLAwareInterface, RouterAwareInterface
{
	use CurrentURLAwareTrait;
	use RouterAwareTrait;

	public function actionIndex()
	{
		$view = $this->getView();
		$view->assign('title', '401 Unauthorized');
		$view->assign('description', 'The requested URL '.$this->build($this->current_url).' was unauthorized on this server.');

		return $view;
	}
}
?>