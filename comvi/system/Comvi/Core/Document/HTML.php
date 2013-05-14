<?php
namespace Comvi\Core;
use Comvi\Core\HTML_Element;
use Comvi\Core\URI;

/**
 * Document_HTML class, provides an easy interface to parse and display an html document
 *
 * @package		Comvi.Core
 * @subpackage	Document
 */

class Document_HTML extends AbstractDocument
{
	static public function parseFilenameVersion($filename)
	{
		$pattern = '/^([A-Za-z0-9]+)(?:\-((?:[0-9]+)(?:(?:\.[0-9]+)*)))?/';
		$info	 = pathinfo($filename);

		preg_match($pattern, $info['filename'], $matches);

		return array(
			'name'		=> $matches[1],
			'version'	=> isset($matches[2]) ? $matches[2] : null,
			'ext'		=> $info['extension']
		);
	}


	public $type = 'html';

	/**
	 * Array of head elements
	 *
	 * @var	array
	 */
	public $head_elements = array();
	public $resources = array();

	public $static_url;


	/**
	 * Class constructor.
	 *
	 * @param	array	$options Associative array of options
	 */
	public function __construct($options = array())
	{
		if (array_key_exists('static_url', $options)) {
			$this->static_url = $options['static_url'];
		}

		// Add default mime type and document metadata (meta data syncs with mime type by default)
		$this->addMeta(array('name' => 'Content-Type', 'content' => 'text/html'));

		parent::__construct($options);
	}

	public function addHeadElement($type, $attrs = array())
	{
		$element = new HTML_Element($type, $attrs);
		$this->head_elements[] = $element;

		return $this;
	}

	public function getHeadElements($type = null)
	{
		if ($type === null) {
			return $this->head_elements;
		}

		$select_elements = array();

		foreach ($this->head_elements as $head_element) {
			if ($head_element->type === $type) {
				$select_elements[] = $head_element;
			}
		}

		return $select_elements;
	}

	public function flushHeadElements()
	{
		foreach ($this->head_elements as $key => $element) {
			echo $element."\n";
			unset($this->head_elements[$key]);
		}

		return $this;
	}

	/**
	 * Add a meta tag.
	 */
	public function addMeta($attrs)
	{
		if (isset($attrs['name']) && strtolower($attrs['name']) === 'content-type') {
			// Syncing with HTTP-header
			$this->mime = $attrs['content'];
			return $this;
		}

		return $this->addHeadElement('meta', $attrs);
	}

	public function addDescription($desc)
	{
		$attrs = array(
			'name'		=> 'description',
			'content'	=> $desc
		);

		return $this->addMeta($attrs);
	}

	public function addMobileMeta($content = 'width=device-width, initial-scale=1, maximum-scale=1')
	{
		$attrs = array(
			'name'		=> 'viewport',
			'content'	=> $content
		);

		return $this->addMeta($attrs);
	}

	public function addLink($attrs)
	{
		return $this->addHeadElement('link', $attrs);
	}

	public function addFavicon($url)
	{
		$attrs = array(
			'rel'	=> 'shortcut icon',
			'href'	=> new URI($url)
		);

		return $this->addLink($attrs);
	}

	public function addIcon($url, $size = null)
	{
		$attrs = array(
			'rel'	=> 'apple-touch-icon',
			//'rel'	=> 'apple-touch-icon-precomposed',
			'href'	=> new URI($url)
		);		

		if ($size !== null) {
			$attrs['sizes'] = $size.'x'.$size;
		}

		return $this->addLink($attrs);
	}

	public function addIPhoneIcon($url, $size = null)
	{
		return $this->addIcon($url, 57);
	}

	public function addIPhone4Icon($url, $size = null)
	{
		return $this->addIcon($url, 114);
	}

	public function addIPadIcon($url, $size = null)
	{
		return $this->addIcon($url, 72);
	}

	public function addIPad3Icon($url, $size = null)
	{
		return $this->addIcon($url, 144);
	}

	protected function addResource($url)
	{
		$info = static::parseFilenameVersion($url);
		$hash = $info['name'].'.'.$info['ext'];

		if (!array_key_exists($hash, $this->resources)) {
			$this->resources[$hash] = $info['version'];
			return true;
		}
		elseif ($this->resources[$hash] === null) {
			return false;
		}
		elseif ($info['version'] === null) {
			$this->resources[$hash] = $info['version'];
			return true;
		}
		elseif (version_compare($info['version'], $this->resources[$hash]) > 0) {
			$this->resources[$hash] = $info['version'];
			return true;
		}

		return false;
	}

	public function addStyle($url)
	{
		if (!$this->addResource($url)) {
			return $this;
		};

		$attrs = array(
			'type'	=> 'text/css',
			'rel'	=> 'stylesheet',
			'href'	=> new URI($url)
		);

		return $this->addLink($attrs);
	}

	public function addScript($url)
	{
		if (!$this->addResource($url)) {
			return $this;
		};

		$attrs = array(
			'type'	=> 'text/javascript',
			'src'	=> new URI($url)
		);

		return $this->addHeadElement('script', $attrs);
	}

	/*public function addStyleArray($data)
	{
		$data = implode("\n",$data);

		return $this->addStyleText($data);
	}*/

	public function addStyleText($data)
	{
		$attrs = array(
			'type'	=> 'text/css',
			'text'	=> "\n".$data."\n"
		);

		return $this->addHeadElement('style', $attrs);
	}

	public function addScriptText($data)
	{
		$attrs = array(
			'type'	=> 'text/javascript',
			'text'	=> "\n".$data."\n"
		);

		return $this->addHeadElement('script', $attrs);
	}
}
?>