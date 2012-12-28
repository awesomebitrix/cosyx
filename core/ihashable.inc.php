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
interface CSX_IHashable
{
	/**
	 * Set value
	 *
	 * This method takes variable number of arguments as
	 * path to value and the last argument as a value to set.
	 *
	 * <code>
	 * $hash->set('foo', 'bar', 'val');
	 * // equal to hash array('foo' => array('bar' => 'val'));
	 * </code>
	 *
	 * @param string $name,[$name,[$name,...]] path to value
	 * @param mixed $value value
	 * @access public
	 * @return void
	 */
	function set();

	/**
	 * Get value
	 *
	 * This method takes variable number of arguments as
	 * path to value and return it.
	 *
	 * <code>
	 * $hash = new xHash(array('foo' => array('bar' => 'val')));
	 * $hash->get('foo', 'bar');
	 * // will return 'val'
	 * $hash->get('foo');
	 * // will return array('bar' => 'val')
	 * </code>
	 *
	 * @param string $name,[$name,[$name,...]] path to value
	 * @access public
	 * @return mixed
	 */
	function get();

	/**
	 * Check if value exists
	 *
	 * This method takes variable number of arguments as
	 * path to value and return true if it exists or false otherwise.
	 *
	 * <code>
	 * $hash = new xHash(array('foo' => array('bar' => 'val')));
	 * $hash->has('foo');
	 * $hash->has('foo', 'bar');
	 * // both will return true
	 * $hash->has('bar');
	 * // will return false
	 * </code>
	 *
	 * @param string $name,[$name,[$name,...]] path to value
	 * @access public
	 * @return bool
	 */
	function has();

	/**
	 * Remove value
	 *
	 * This method takes variable number of arguments as
	 * path to value and remove it.
	 *
	 * @param string $name,[$name,[$name,...]] path to value
	 * @access public
	 * @return void
	 */
	function remove();

	function &getHashRef();
}