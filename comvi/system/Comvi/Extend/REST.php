<?php
namespace Comvi\Extend;

/**
 * Comvi REST Class.
 *
 * Make REST requests to RESTful services with simple syntax.
 *
 * @package		Comvi.Extend
 */

class REST
{
    protected $supported_formats = array(
		'xml' 				=> 'application/xml',
		'json' 				=> 'application/json',
		'serialize' 		=> 'application/vnd.php.serialized',
		'php' 				=> 'text/plain',
    	'csv'				=> 'text/csv'
	);

    protected $auto_detect_formats = array(
		'application/xml' 	=> 'xml',
		'text/xml' 			=> 'xml',
		'application/json' 	=> 'json',
		'text/json' 		=> 'json',
		'text/csv' 			=> 'csv',
		'application/csv' 	=> 'csv',
    	'application/vnd.php.serialized' => 'serialize'
	);

	protected $curl;

	protected $server;
	protected $ca_info;
	protected $auth;
	protected $user;
	protected $pass;

	protected $is_https = false;

	protected $format;
	protected $mime_type;


    function __construct($config = array())
    {
		$this->curl = new \CURL();

		// If a URL was passed to the library
		empty($config) OR $this->initialize($config);
    }

    public function initialize($config)
    {
		isset($config['server']) AND $this->server = $config['server'];
		isset($config['auth']) AND $this->auth = $config['auth'];
		isset($config['user']) AND $this->user = $config['user'];
		isset($config['pass']) AND $this->pass = $config['pass'];

		if (substr($this->server, 0, 5) === 'https') {
			$this->is_https = true;
			isset($config['ca_info']) AND $this->ca_info = $config['ca_info'];
		}
    }

    public function get($uri, $params = array(), $format = null)
    {
    	return $this->call('get', $uri, $params, $format);
    }

    public function post($uri, $params = array(), $format = null)
	{
        return $this->call('post', $uri, $params, $format);
    }

    public function put($uri, $params = array(), $format = null)
    {
        return $this->call('put', $uri, $params, $format);
    }

    public function delete($uri, $params = array(), $format = null)
    {
        return $this->call('delete', $uri, $params, $format);
    }

    protected function call($method, $uri, $params = array(), $format = null)
    {
		$this->curl->setUrl($this->server.$uri);

    	if ($format !== null) {
			$this->format($format);
		}

		if ($this->mime_type !== null) {
			$this->curl->setHeader('Accept', $this->mime_type);
		}

		if ($this->is_https === true) {
			if (empty($this->ca_info)) {
				$this->curl->setSSL(false);
			}
			else {
				$this->curl->setSSL(true, 2, $this->ca_info);
			}
		}

        // If authentication is enabled use it
        if ($this->auth != '' && $this->user != '') {
        	$this->curl->setLogin($this->user, $this->pass, $this->auth);
        }

        // We still want the response even if there is an error code over 400
        //$this->curl->setOption('FailOnError', false);

        // Call the correct method with parameters
        $response = $this->curl->{$method}($params);

        // Execute and return the response from the REST server
		//die($this->curl->error_string);
        // Format and return
        return $this->formatResponse($response);
    }

    public function setApiKey($key, $name = 'X-API-KEY')
	{
		$this->curl->setHeader($name, $key);
	}

    public function acceptLanguage($lang)
	{
		if (is_array($lang)) {
			$lang = implode(', ', $lang);
		}

		$this->curl->setHeader('Accept-Language', $lang);
	}

    public function setHeader($name, $content = null)
	{
		$this->curl->setHeader($name, $content);
	}

    // If a type is passed in that is not supported, use it as a mime type
    public function format($format)
	{
		if (array_key_exists($format, $this->supported_formats)) {
			$this->format = $format;
			$this->mime_type = $this->supported_formats[$format];
		}
		else {
			$this->mime_type = $format;
		}
	}

	// Return HTTP status code
	public function getStatus()
	{
		return $this->curl->getStatus();
	}

	// Return curl info by specified key, or whole array
	public function getInfo($key = null)
	{
		return $this->curl->getInfo($key);
	}

	protected function formatResponse($response)
	{
		// It is a supported format, so just run its formatting method
		if (array_key_exists($this->format, $this->supported_formats)) {
			return $this->{'_'.$this->format}($response);
		}

		// Find out what format the data was returned in
		$returned_mime = $this->curl->getInfo('content_type');

		// If they sent through more than just mime, strip it off
		if (strpos($returned_mime, ';')) {
			list($returned_mime) = explode(';', $returned_mime);
		}

		$returned_mime = trim($returned_mime);

		if (array_key_exists($returned_mime, $this->auto_detect_formats)) {
			return $this->{'_'.$this->auto_detect_formats[$returned_mime]}($response);
		}

		return $response;
	}


    // Format XML for output
    protected function _xml($string)
    {
    	return $string ? (array) simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA) : array();
    }

    // Format HTML for output
    // This function is DODGY! Not perfect CSV support but works with my REST_Controller
    protected function _csv($string)
    {
		$data = array();

		// Splits
		$rows = explode("\n", trim($string));
		$headings = explode(',', array_shift($rows));
		foreach ($rows as $row) {
			// The substr removes " from start and end
			$data_fields = explode('","', trim(substr($row, 1, -1)));

			if (count($data_fields) === count($headings)) {
				$data[] = array_combine($headings, $data_fields);
			}

		}

		return $data;
    }

    // Encode as JSON
    protected function _json($string)
    {
    	return json_decode(trim($string));
    }

    // Encode as Serialized array
    protected function _serialize($string)
    {
    	return unserialize(trim($string));
    }

    // Encode raw PHP
    protected function _php($string)
    {
    	$string = trim($string);
    	$populated = array();
    	eval("\$populated = \"$string\";");
    	return $populated;
    }
}
?>