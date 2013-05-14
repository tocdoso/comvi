<?php
namespace Error;
use Comvi\Core\AbstractController;
use Comvi\Core\CurrentURLAwareInterface;
use Comvi\Core\CurrentURLAwareTrait;
use Comvi\Core\RouterAwareInterface;
use Comvi\Core\RouterAwareTrait;

/**
 * NotFound Controller
 */
class NotFound extends AbstractController implements CurrentURLAwareInterface, RouterAwareInterface
{
	use CurrentURLAwareTrait;
	use RouterAwareTrait;

	public function actionIndex()
	{
		$view = $this->getView();
		$view->assign('title', '404 Not Found');
		$view->assign('description', 'The requested URL '.$this->build($this->current_url).' was not found on this server.');

		return $view;
	}
}
?>