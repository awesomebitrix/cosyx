<?php
/**
 * Cosix Bitrix Extender
 *
 * @package core
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * @package core
 */
abstract class CSX_Server_Response
{
	protected $content = '';
	protected $isSent = false;

	function __construct()
	{
	}

	/**
	 * Send response
	 *
	 * @abstract
	 * @access public
	 * @return void
	 */
	final function send()
	{
		$this->isSent = true;
		$this->sendResponse();
	}

	function isSent()
	{
		return $this->isSent;
	}

	abstract protected function sendResponse();

	/**
	 * Append content to response
	 *
	 * @param string $content
	 * @access public
	 * @return void
	 */
	function append($content)
	{
		$this->setContent($this->getContent() . $content);
	}

	/**
	 * Get content
	 *
	 * @access public
	 * @return string
	 */
	function getContent()
	{
		return $this->content;
	}

	/**
	 * Set content
	 *
	 * @param string $content
	 * @access public
	 * @return void
	 */
	function setContent($content)
	{
		$this->content = $content;
	}

	/**
	 * Serialize response to string value
	 *
	 * @access public
	 * @return string
	 */
	function save()
	{
		return $this->content;
	}

	/**
	 * Restore response from string
	 *
	 * @param string $data
	 * @access public
	 * @return boolean
	 */
	function restore($data)
	{
		if (is_string($data)) {
			$this->content = $data;
			return true;
		}
		return false;
	}
}
