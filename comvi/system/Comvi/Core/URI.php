<?php
namespace Comvi\Core;
use Comvi\Core\Helper\URL;

/**
 * URI Class.
 *
 * This class serves two purposes. First to parse a URI and provide a common interface
 * for the Comvi Framework to access and manipulate a URI.  Second to attain the URI of
 * the current executing script from the server regardless of server.
 *
 * @package		Comvi.Core
 */
class URI
{
	/**
	 * Does a UTF-8 safe version of PHP parse_url function
	 * @see http://us3.php.net/manual/en/function.parse-url.php
	 * 
	 * @param string URL to parse
	 * @return associative array or false if badly formed URL. 
	 */	
	public static function parseUrl($url) {
		$result = array();
		// Build arrays of values we need to decode before parsing
		$entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
		$replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "$", ",", "/", "?", "%", "#", "[", "]");
		// Create encoded URL with special URL characters decoded so it can be parsed
		// All other characters will be encoded
		$encodedURL = str_replace($entities, $replacements, urlencode($url));
		// Parse the encoded URL
		$encodedParts = parse_url($encodedURL);
		// Now, decode each value of the resulting array
		foreach ($encodedParts as $key => $value) {
			$result[$key] = urldecode($value);
		}
		return $result;
	}

	/**
	 * Build a query from a array (reverse of the PHP parse_str()).
	 *
	 * @static
	 * @return	string The resulting query string.
	 * @see	parse_str()
	 */
	public static function buildQuery($params, $akey = null)
	{
		if (!is_array($params) || count($params) == 0) {
			return false;
		}

		return urldecode(http_build_query($params, '', '&'));
	}


	/**
	 * @var string Original URI
	 */
	protected $uri = null;

	/**
	 * @var string Protocol
	 */
	protected $scheme = null;

	/**
	 * @var string Host
	 */
	protected $host = null;

	/**
	 * @var integer Port
	 */
	protected $port = null;

	/**
	 * @var string Username
	 */
	protected $user = null;

	/**
	 * @var string Password
	 */
	protected $pass = null;

	/**
	 * @var string Path
	 */
	protected $path = null;

	/**
	 * @var string Query
	 */
	protected $query = null;

	/**
	 * @var string Anchor (everything after the "#")
	 */
	protected $fragment = null;

	/**
	 * @var array Query variable hash.
	 */
	protected $vars = array();

	//public $parsed = null;


	/**
	 * Constructor.
	 * You can pass a URI string to the constructor to initialise a specific URI.
	 *
	 * @param	string	$uri	The optional URI string
	 */
	public function __construct($uri = null)
	{
		if (!is_null($uri)) {
			$this->parse($uri);
		}
	}

	/**
	 * Parse a given URI and populate the class fields.
	 *
	 * @param	string $uri The URI string to parse.
	 * @return	boolean True on success.
	 */
	public function parse($uri)
	{
		// Initialise variables
		$retval = false;

		// Set the original URI to fall back on
		$this->uri = $uri;

		/*
		 * Parse the URI and populate the object fields.  If URI is parsed properly,
		 * set method return value to true.
		 */

		if ($_parts = static::parseUrl($uri)) {
			$retval = true;
		}

		//We need to replace &amp; with & for parse_str to work right...
		if (isset($_parts['query']) && strpos($_parts['query'], '&amp;')) {
			$_parts['query'] = str_replace('&amp;', '&', $_parts['query']);
		}

		$this->scheme = isset ($_parts['scheme']) ? $_parts['scheme'] : null;
		$this->user = isset ($_parts['user']) ? $_parts['user'] : null;
		$this->pass = isset ($_parts['pass']) ? $_parts['pass'] : null;
		$this->host = isset ($_parts['host']) ? $_parts['host'] : null;
		$this->port = isset ($_parts['port']) ? $_parts['port'] : null;
		$this->path = !empty($_parts['path']) ? $_parts['path'] : null;
		$this->query = isset ($_parts['query'])? $_parts['query'] : null;
		$this->fragment = isset ($_parts['fragment']) ? $_parts['fragment'] : null;

		//parse the query

		if (isset($_parts['query'])) {
			parse_str($_parts['query'], $this->vars);
		}

		return $retval;
	}

	/**
	 * Magic method to get the string representation of the URI object.
	 *
	 * @return	string
	 */
	public function __toString()
	{
		$uri = $this->toString(array('scheme', 'user', 'pass', 'host', 'port'));
		$uri .= $this->isShorten() ? URL::base(true) : '';
		$uri .= $this->toString(array('path', 'query', 'fragment'));

		return $uri;
	}

	/**
	 * Returns full uri string.
	 *
	 * @access	public
	 * @param	array $parts An array specifying the parts to render.
	 * @return	string The rendered URI string.
	 */
	public function toString($parts = array('scheme', 'user', 'pass', 'host', 'port', 'path', 'query', 'fragment'))
	{
		$query = $this->getQuery(); //make sure the query is created

		$uri = '';
		$uri .= in_array('scheme', $parts)  ? (!empty($this->scheme) ? $this->scheme.'://' : '') : '';
		$uri .= in_array('user', $parts)	? $this->user : '';
		$uri .= in_array('pass', $parts)	? (!empty ($this->pass) ? ':' : '') .$this->pass. (!empty ($this->user) ? '@' : '') : '';
		$uri .= in_array('host', $parts)	? $this->host : '';
		$uri .= in_array('port', $parts)	? (!empty ($this->port) ? ':' : '').$this->port : '';
		$uri .= in_array('path', $parts)	? $this->path : '';
		$uri .= in_array('query', $parts)	? (!empty ($query) ? '?'.$query : '') : '';
		$uri .= in_array('fragment', $parts)? (!empty ($this->fragment) ? '#'.$this->fragment : '') : '';

		return $uri;
	}

	/**
	 * Set URI scheme (protocol)
	 * ie. http, https, ftp, etc...
	 *
	 * @param   string  $scheme  The URI scheme.
	 *
	 * @return  void
	 */
	public function setScheme($scheme)
	{
		$this->scheme = $scheme;
	}

	/**
	 * Get URI scheme (protocol)
	 * ie. http, https, ftp, etc...
	 *
	 * @return  string  The URI scheme.
	 */
	public function getScheme()
	{
		return $this->scheme;
	}

	/**
	 * Set URI host.
	 *
	 * @param   string  $host  The URI host.
	 *
	 * @return  void
	 */
	public function setHost($host)
	{
		$this->host = $host;
	}

	/**
	 * Get URI host
	 * Returns the hostname/ip or null if no hostname/ip was specified.
	 *
	 * @return  string  The URI host.
	 */
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * Set URI port.
	 *
	 * @param   integer  $port  The URI port number.
	 *
	 * @return  void
	 */
	public function setPort($port)
	{
		$this->port = $port;
	}

	/**
	 * Get URI port
	 * Returns the port number, or null if no port was specified.
	 *
	 * @return  integer  The URI port number.
	 */
	public function getPort()
	{
		return (isset($this->port)) ? $this->port : null;
	}

	/**
	 * Set URI username.
	 *
	 * @param   string  $user  The URI username.
	 *
	 * @return  void
	 */
	public function setUser($user)
	{
		$this->user = $user;
	}

	/**
	 * Get URI username
	 * Returns the username, or null if no username was specified.
	 *
	 * @return  string  The URI username.
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * Set URI password.
	 *
	 * @param   string  $pass  The URI password.
	 *
	 * @return  void
	 */
	public function setPass($pass)
	{
		$this->pass = $pass;
	}

	/**
	 * Get URI password
	 * Returns the password, or null if no password was specified.
	 *
	 * @return  string  The URI password.
	 */
	public function getPass()
	{
		return $this->pass;
	}

	/**
	 * Set the URI path string.
	 *
	 * @param	string $path The URI path string.
	 */
	public function setPath($path)
	{
		//$this->path = static::cleanPath($path);
		$this->path = !empty($path) ? $path : null;
	}

	public function append($value)
	{
		$this->path .= $value;
	}

	public function prepend($value)
	{
		$this->path = $value.$this->path;
	}

	/*public function trim()
	{
		$this->setPath(trim($this->path, '/'));
	}*/

	/**
	 * Gets the URI path string.
	 *
	 * @return  string  The URI path string.
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Sets the query to a supplied string in format:
	 *		foo=bar&x=y
	 *
	 * @param	mixed (array|string) $query The query string.
	 */
	public function setQuery($query)
	{
		if (is_array($query))
		{
			$this->vars = $query;
		} else {
			if (strpos($query, '&amp;') !== false)
			{
				$query = str_replace('&amp;','&',$query);
			}
			parse_str($query, $this->vars);
		}

		//empty the query
		$this->query = null;
	}

	/**
	 * Returns flat query string.
	 *
	 * @return	string Query string.
	 */
	public function getQuery()
	{
		//If the query is empty build it first
		if (is_null($this->query)) {
			$this->query = static::buildQuery($this->vars);
		}

		return $this->query;
	}

	/**
	 * Set the URI anchor string
	 * everything after the "#".
	 *
	 * @param   string  $anchor  The URI anchor string.
	 *
	 * @return  void
	 */
	public function setFragment($anchor)
	{
		$this->fragment = $anchor;
	}

	/**
	 * Get the URI archor string
	 * Everything after the "#".
	 *
	 * @return  string  The URI anchor string.
	 */
	public function getFragment()
	{
		return $this->fragment;
	}

	/**
	 * Adds a query variable and value, replacing the value if it
	 * already exists and returning the old value.
	 *
	 * @param	string $name Name of the query variable to set.
	 * @param	string $value Value of the query variable.
	 * @return	$this
	 */
	public function setVar($name, $value)
	{
		$this->vars[$name] = $value;

		//empty the query
		$this->query = null;

		return $this;
	}

	/**
	 * Set the variable array
	 *
	 * @param	array	An associative array with variables
	 * @param	boolean	If True, the array will be merged instead of overwritten
	 * @return	$this
	 */
	public function setVars($vars, $merge = true)
	{
		if ($merge) {
			$this->vars = array_merge($this->vars, $vars);
		} else {
			$this->vars = $vars;
		}

		//empty the query
		$this->query = null;

		return $this;
	}

	/**
	 * Removes an item from the query string variables if it exists.
	 *
	 * @param	string $name Name of variable to remove.
	 * @return	$this
	 */
	public function delVar($name)
	{
		if (array_key_exists($name, $this->vars))
		{
			unset($this->vars[$name]);

			//empty the query
			$this->query = null;
		}

		return $this;
	}

	/**
	 * Removes items from the query string variables if it exists.
	 *
	 * @param	array	An associative array with variables
	 * @return	$this
	 */
	/*public function delVars($vars)
	{
		foreach ($vars as $name => $value) {
			if (is_int($name)) {
				$this->delVar($value);
			}
			elseif($this->getVar($name) === $value) {
				$this->delVar($name);
			}
		}

		return $this;
	}*/

	/**
	 * Checks if variable exists.
	 *
	 * @param	string $name Name of the query variable to check.
	 * @return	bool exists.
	 */
	public function hasVar($name)
	{
		return array_key_exists($name, $this->vars);
	}

	/**
	 * Returns a query variable by name.
	 *
	 * @param	string $name	Name of the query variable to get.
	 * @param	string $default	Default value to return if the variable is not set.
	 * @return	array Query variables.
	 */
	public function getVar($name, $default = null)
	{
		if (array_key_exists($name, $this->vars)) {
			return $this->vars[$name];
		}
		return $default;
	}

	/**
	 * Returns entire query variables
	 *
	 * @return	array Query variables.
	 */
	public function getVars()
	{
		return $this->vars;
	}

	/**
	 * Checks whether the current URI is using HTTPS.
	 *
	 * @return	boolean True if using SSL via HTTPS.
	 */
	public function isSSL()
	{
		return ($this->scheme == 'https') ? true : false;
	}

	/**
	 * Checks if the supplied URL is internal.
	 *
	 * @return	boolean True if Internal.
	 */
	public function isInternal()
	{
		if ($this->isShorten()) {
			return true;
		}

		$base = $this->toString(array('scheme', 'user', 'pass', 'host', 'port', 'path'));
		if (stripos($base, URL::base()) === 0) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if the supplied URL was shorten.
	 *
	 * @return	boolean True if was shorten.
	 */
	public function isShorten()
	{
		$host = $this->toString(array('scheme', 'user', 'pass', 'host', 'port'));
		if (!empty($host)) {
			return false;
		}

		if (preg_match('#^/#i', $this->path)) {
			return false;
		}

		return true;
	}

	/**
	 * Shorten internal URI.
	 *
	 * @return	void
	 */
	public function shorten()
	{
		if ($this->isInternal() && !$this->isShorten()) {
			foreach (array('scheme', 'user', 'pass', 'host', 'port') as $prop) {
				$this->$prop = null;
			}

			// Get site path.
			$base = URL::base(true);

			// Remove the base URI path.
			if (strpos($this->path, $base) === 0) {
				$this->path = ($this->path === $base) ? null : substr($this->path, strlen($base));
			}
		}
	}

	/**
	 * build full internal URI.
	 *
	 * @return	void
	 */
	public function full()
	{
		if ($this->isShorten()) {
			// save temporary shorten path.
			$path	= $this->path;
			$base	= URL::base();
			$parts	= static::parseUrl($base);

			foreach ($parts as $prop => $val) {
				$this->$prop = $val;
			}

			$this->path .= $path;
		}
	}

	public function isChildOf($uri)
	{
		if (!$uri instanceof static) {
			$uri = new static($uri);
		}

		$this->shorten();
		$uri->shorten();

		if (!$this->getPath() && $uri->getPath()) {
			//die(var_dump($uri->getPath()).'_2_PATH_is_Child_Of_'.var_dump($this->getPath()));
			return false;
		}

		if ($this->getPath()) {
			if (!$uri->getPath()) {
				return false;
			}
			elseif (!strpos($uri->getPath(), $this->getPath()) === 0) {
				die($uri->getPath().'_PATH_is_Child_Of_'.$this->getPath());
				return false;
			}
		}

		if(array_diff_assoc($this->getVars(), $uri->getVars())) {
			//echo($this."\n_3_PATH_is_Child_Of_\n".$uri."\n\n");
			return false;
		}

//print_r($this);
//print_r($uri);

		return true;
	}

	public function equal($uri)
	{
		//print_r($this);
		//print_r($uri);
		//die();
		if (!$uri instanceof static) {
			$uri = new static($uri);
		}

		return $this->isChildOf($uri) && $uri->isChildOf($this);
	}

	public function hasWWW($force = false)
	{
		if ($force === true) {
			$this->full();
		}

		if (empty($this->host)) {
			return null;
		}

		return (strpos($this->host, 'www.') === 0) ? true : false;
	}

	public function removeWWW()
	{
		if ($this->hasWWW() === true) {
			$this->host = substr($this->host, 4);
		}
	}

	public function addWWW()
	{
		if ($this->hasWWW() === false) {
			$this->host = 'www.'.$this->host;
		}
	}
}
?>