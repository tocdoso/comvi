<?php
namespace Comvi\Core;
use Comvi\Core\Exception\HttpFoundException;
use Comvi\Core\Exception\HttpNotFoundException;

/**
 * Class to create and parse routes.
 *
 * @package		Comvi.Core
 */
class Router implements RouterInterface
{
	const NONE_WWW	= 0;
	const WWW		= 1;

	const NONE		= 0;
	const BUILD		= 1;
	const PARSE		= 2;


	/**
	 * The preferred domain.
	 *
	 * @var integer
	 */
	protected $preferred_domain = NULL;

	/**
	 * Index File
	 *
	 *
	 * Typically this will be your index.php file, unless you've renamed it to
	 * something else. If you are using mod_rewrite to remove the page set this
	 * variable so that it is blank.
	 */
	protected $index_file = 'index.php';

	protected $remove_index_file = true;

	/**
	 * Default module string for index page.
	 *
	 * @var integer
	 */
	//protected $index = '';


	public function __construct($options = array())
	{
		if (array_key_exists('preferred_domain', $options)) {
			$this->preferred_domain = $options['preferred_domain'];
		}

		if (array_key_exists('index_file', $options)) {
			$this->index_file = $options['index_file'];
		}

		if (array_key_exists('remove_index_file', $options)) {
			$this->remove_index_file = $options['remove_index_file'];
		}

		if (array_key_exists('index', $options)) {
			$this->index = $options['index'];
		}

		$this->index = new URI($this->index);
	}

	public function checkBeforeParse(URI $uri)
	{
		$this->checkPreferredDomain($uri);
	}

	/**
	 * Parse the URI.
	 *
	 * @param	object	The URI
	 *
	 * @return	void
	 */
	public function parse(URI &$uri)
	{
		$this->removeIndexFile($uri);
		//$uri->full();
		/*if ($uri->getPath()) {
			throw new HttpNotFoundException();
		}*/
	}

	/**
	 * Build the URI.
	 *
	 * @param	object	The URI
	 *
	 * @return	void
	 */
	public function build(URI &$uri)
	{
		//$this->buildIndexPage($uri);
		$this->addIndexFile($uri);
	}

	public function checkPreferredDomain(URI $uri)
	{
		if ($this->preferred_domain === null) {
			return;
		}

		$host = $uri->getHost();

		if (empty($host)) {
			return;
		}

		if ($uri->hasWWW() === true && $this->preferred_domain === static::NONE_WWW) {
			$uri->removeWWW();
			throw new HttpFoundException($uri);
		}

		if ($uri->hasWWW() === false && $this->preferred_domain === static::WWW) {
			$uri->addWWW();
			throw new HttpFoundException($uri);
		}
	}

	/**
	 * Remove index file in URI.
	 *
	 * @param	object	The URI
	 *
	 * @return	void
	 */
	protected function removeIndexFile(URI &$uri)
	{
		if (!$uri->isShorten()) {
			$uri->shorten();
			$path = $uri->getPath();
			if (strpos($path, $this->index_file) === 0) {
				//$path = ($path === $this->index_file) ? null : substr($path, strlen($this->index_file));
				$path = ltrim(substr($path, strlen($this->index_file)), '/');

				$uri->setPath($path);
			}
			$uri->full();
		}
	}

	/*protected function addIndexFile(URI &$uri)
	{
		if ($this->remove_index_file === false) {
			$uri->prepend($this->index_file);
		}
	}*/

	protected function addIndexFile(URI &$uri)
	{
		if ($this->remove_index_file === false) {
			$uri->prepend($uri->getPath() ? $this->index_file.'/' : $this->index_file);
		}
	}
}
