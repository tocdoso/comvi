<?php
namespace Comvi\Core\View;

/**
 * Placeholder class for a Comvi View.
 *
 * @package		Comvi.Core.View
 */
class Placeholder
{
	protected $data = array();
	protected $prefix = '';
	protected $postfix = '';
	protected $separator = "\n";
	//protected $indent = 0;

	public function setPrefix($prefix)
	{
		$this->prefix = $prefix;
		return $this;
	}

	public function setPostfix($postfix)
	{
		$this->postfix = $postfix;
		return $this;
	}

	public function setSeparator($separator)
	{
		$this->separator = $separator;
		return $this;
	}

	/*public function setIndent($indent)
	{
		$this->indent = $indent;
		return $this;
	}*/

	public function append($string)
	{
		$this->data[] = $string;
		return $this;
	}

	public function prepend($string)
	{
		array_unshift($this->data, $string);
		return $this;
	}

	public function isEmpty()
	{
		return empty($this->data);
	}

	public function count()
	{
		return count($this->data);
	}

	public function __toString()
	{
		return $this->count()
			? $this->prefix
			.implode($this->separator, $this->data)
			.$this->postfix
			: '';
	}
}
