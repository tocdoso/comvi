<?php
namespace Comvi\Core;

/**
 * HTML_Element class, creates an html element, like in js.
 *
 * @package		Comvi.Core
 */

class HTML_Element
{
	protected static $self_closers = array('input','img','hr','br','meta','link');

	/* vars */
	public $type;
	protected $attributes;


	/* constructor */
	public function __construct($type, $attrs = array())
	{
		$this->type			= strtolower($type);
		$this->attributes	= $attrs;
	}

	/* get */
	public function get($attribute = null)
	{
		if ($attribute === null) {
			return $this->attributes;
		}

		return isset($this->attributes[$attribute]) ? $this->attributes[$attribute] : null;
	}

	/* set -- array or key,value */
	public function set($attribute, $value = '')
	{
		if(!is_array($attribute)) {
			$this->attributes[$attribute] = $value;
		}
		else {
			$this->attributes = array_merge($this->attributes, $attribute);
		}
	}

	/* inject */
	/*function inject($object)
	{
		if (@get_class($object) == __class__) {
			$this->attributes['text'].= $object->build();
		}
	}*/

	/* to String */
	function __toString()
	{
		// start
		$build = '<'.$this->type;

		// add attributes
		foreach($this->attributes as $key => $value) {
			if($key != 'text') {
				$build.= ' '.$key.'="'.$value.'"';
			}
		}

		// closing
		if(!in_array($this->type, static::$self_closers)) {
			$text = isset($this->attributes['text']) ? $this->attributes['text'] : '';
			$build.= '>'.$text.'</'.$this->type.'>';
		}
		else {
			$build.= ' />';
		}

		// return it
		return $build;
	}
}
?>