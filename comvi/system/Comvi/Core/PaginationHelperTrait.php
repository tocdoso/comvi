<?php
namespace Comvi\Core;
use PHPPagination\Pagination;

/**
 * Declare URL Helper Trait.
 *
 * @package		Comvi.Core
 */
trait PaginationHelperTrait
{
	protected $limit = 30;

	protected function getPagination($total)
	{
		$pagination = new Pagination;
		$pagination->setCurrent($this->getPage());
		$pagination->setTotal((int) $total);
		$pagination->setRPP($this->getLimit());

		return $pagination;
	}

	protected function getPage()
	{
		return isset($this->params['page']) ? (int) $this->params['page'] : 1;
	}

	protected function getLimit()
	{
		if (!isset($this->params['limit'])) {
			return $this->limit;
		}

		return min($this->limit, (int) $this->params['limit']);
	}
}
