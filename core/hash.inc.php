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
class CSX_Hash implements CSX_IHashable
{
	private $hash = array();

	/**
	 * Constructor
	 *
	 * @param array $hash hash data
	 * @access protected
	 * @return void
	 */
	public function __construct($hash = null)
	{
		if (is_array($hash)) {
			$this->hash = $hash;
		}
		elseif (null !== $hash) {
			throw new CSX_Exception('CSX_Hash constructor expect array or null as an argument');
		}
	}

	public function set()
	{
		$argv = func_get_args();
		if (1 == count($argv) && is_array($argv[0])) {
			foreach ($argv[0] as $k => $v) {
				$this->set($k, $v);
			}
		}
		elseif (count($argv) > 1) {
			$attr =& $this->hash;
			while (count($argv) > 1) {
				$key = array_shift($argv);
				if (isset($attr[$key]) && ($attr[$key] instanceof CSX_IHashable) && (count($argv) > 1)) {
					call_user_func_array(array($attr[$key], 'set'), $argv);
				}
				else {
					if (!isset($attr[$key]) || !is_array($attr[$key])) {
						$attr[$key] = array();
					}
					$attr =& $attr[$key];
				}
			}
			$attr = $argv[0];
		}
		else {
			throw new CSX_Exception('Hash::set() method required at least two params or one being an array of key-value pairs');
		}
	}

	public function get()
	{
		$argv = func_get_args();
		$attr = $this->hash;
		while (count($argv)) {
			$key = array_shift($argv);
			if (!is_array($attr) || !isset($attr[$key])) {
				return null;
			}
			if (($attr[$key] instanceof CSX_IHashable) && count($argv)) {
				return call_user_func_array(array($attr[$key], 'get'), $argv);
			}
			else {
				$attr = $attr[$key];
			}
		}
		return $attr;
	}

	public function has()
	{
		$argv = func_get_args();
		$attr = $this->hash;
		while (count($argv)) {
			$key = array_shift($argv);
			if (!is_array($attr) || !array_key_exists($key, $attr)) {
				return false;
			}
			if (($attr[$key] instanceof CSX_IHashable) && count($argv)) {
				return call_user_func_array(array($attr[$key], 'has'), $argv);
			}
			else {
				if (array_key_exists($key, $attr) && null === $attr[$key]) {
					return true;
				}
				$attr = $attr[$key];
			}
		}
		return isset($attr);
	}

	public function remove()
	{
		$argv = func_get_args();
		if (count($argv) > 0) {
			$attr =& $this->hash;
			$keys = array();
			while (count($argv)) {
				$key = array_shift($argv);
				if (!is_array($attr) || !array_key_exists($key, $attr)) {
					return;
				}
				elseif ($attr[$key] instanceof CSX_IHashable) {
					return call_user_func_array(array($attr[$key], 'remove'), $argv);
				}
				else {
					$attr =& $attr[$key];
					$keys[] = "'" . str_replace("'", "\'", $key) . "'";
				}
			}
			eval('unset($this->hash[' . implode("][", $keys) . ']);');
		}
	}

	public function setHashRef(&$hash)
	{
		$this->hash =& $hash;
	}

	public function & getHashRef()
	{
		return $this->hash;
	}
}