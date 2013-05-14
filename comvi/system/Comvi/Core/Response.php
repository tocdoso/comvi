<?php
namespace Comvi\Core;

/**
 * Response Class.
 *
 * This class serves to provide the Comvi Framework with a common interface to access
 * response variables.  This includes header and body.
 *
 * @package		Comvi.Core
 */
class Response
{
	/**
	 * @var  array  An array of status codes and messages.
	 */
	protected static $statuses = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		507 => 'Insufficient Storage',
		509 => 'Bandwidth Limit Exceeded'
	);

	/**
	 * @var  int  The HTTP status code.
	 */
	protected $status = 200;

	protected $headers = array();

	protected $body;


	/**
	 * Sets up the response with a body and a status code.
	 *
	 * @param  string  $body    The response body
	 * @param  string  $status  The response status
	 */
	public function __construct($body = null, $status = 200, $headers = array())
	{
		$this->body		= $body;
		$this->status	= $status;
		$this->headers	= $headers;
	}

	/**
	 * Set the response status code.
	 *
	 * @param   string  $status  The status code
	 * @return  $this
	 */
	public function setStatus($status)
	{
		$this->status = $status;

		return $this;
	}

	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Add a header.
	 *
	 * @param	string	$name
	 * @param	string	$value
	 *
	 * @return	$this
	 */
	public function addHeader($name, $value)
	{
		$this->headers[] = array(
			'name'	=> $name,
			'value'	=> $value
		);

		return $this;
	}

	/**
	 * Set a header, replaces any headers already defined with that $name.
	 *
	 * @param	string	$name
	 * @param	string	$value
	 *
	 * @return	$this
	 */
	public function setHeader($name, $value)
	{
		foreach ($this->headers as $key => $header) {
			if ($name === $header['name']) {
				unset($this->headers[$key]);
			}
		}

		return $this->addHeader($name, $value);
	}

	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * Set body content.
	 *
	 * If body content already defined, this will replace it.
	 *
	 * @param   string  $content  The content to set to the response body.
	 *
	 * @return  $this
	 */
	public function setBody($content)
	{
		$this->body = $content;

		return $this;
	}

	public function getBody()
	{
		return $this->body;
	}

	public function send()
	{
		$this->sendHeaders();
		$this->sendBody();
	}

	/**
	 * Send all headers.
	 *
	 * @return	bool
	 */
	public function sendHeaders()
	{
		if (headers_sent()) {
			return false;
		}

		// Send the protocol/status line first, FCGI servers need different status header
		if (!empty($_SERVER['FCGI_SERVER_VERSION'])) {
			header('Status: '.$this->status.' '.self::$statuses[$this->status]);
		}
		else {
			$protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
			header($protocol.' '.$this->status.' '.self::$statuses[$this->status]);
		}

		foreach ($this->headers as $header) {
			header($header['name'] . ': ' . $header['value'], false);
		}

		return true;
	}

	/**
	 * Send entire body.
	 *
	 * @param	boolean	$compress	If true, compress the data
	 *
	 * @return	string
	 */
	public function sendBody($compress = false)
	{
		if ($compress === false || !ini_get('zlib.output_compression') || (ini_get('output_handler') != 'ob_gzhandler')) {
			echo $this->body;
		}
		else {
			$data = self::compress($this->body);
			echo $data;
		}
	}

	/**
	 * Compress the data
	 *
	 * Checks the accept encoding of the browser and compresses the data before
	 * sending it to the client.
	 *
	 * @param	string	$data	data
	 *
	 * @return	string	compressed data
	 */
	protected static function compress($data)
	{
		$encoding = self::clientEncoding();

		if (!$encoding) {
			return $data;
		}

		if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
			return $data;
		}

		if (headers_sent()) {
			return $data;
		}

		if (connection_status() !== 0) {
			return $data;
		}


		$level = 4; // ideal level

		/*
		$size		= strlen($data);
		$crc		= crc32($data);

		$gzdata		= "\x1f\x8b\x08\x00\x00\x00\x00\x00";
		$gzdata		.= gzcompress($data, $level);

		$gzdata	= substr($gzdata, 0, strlen($gzdata) - 4);
		$gzdata	.= pack("V",$crc) . pack("V", $size);
		*/

		$gzdata = gzencode($data, $level);

		self::setHeader('Content-Encoding', $encoding);
		self::setHeader('X-Content-Encoded-By', 'Comvi! 1.0');

		return $gzdata;
	}

	/**
	 * Check, whether client supports compressed data
	 *
	 * @return	boolean
	 */
	protected static function clientEncoding()
	{
		if (!isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
			return false;
		}

		$encoding = false;

		if (false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
			$encoding = 'gzip';
		}

		if (false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip')) {
			$encoding = 'x-gzip';
		}

		return $encoding;
	}

	/**
	 * Magic method, returns the output of [static::render].
	 *
	 * @return  string
	 * @uses    CView::render
	 */
	public function __toString()
	{
		return (string) $this->body;
	}
}
?>