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
class CSX_Factory extends CSX_Hash
{
	private $className;

	function __construct($className = null, $params = null)
	{
		parent::__construct($params);
		if (!empty($className)) {
			$this->className = $className;
		}
	}

	public static function resolve($var, $className)
	{
		if (null === $var || is_array($var)) {
			return new CSX_Factory($className, $var);
		}
		elseif ($var instanceof CSX_Factory) {
			if (false == $var->getClassName()) {
				$var->setClassName($className);
			}
			return $var;
		}
		else {
			throw new CSX_Exception('CSX_Factory object, array of params or null is expected');
		}
	}

	public function getClassName()
	{
		return $this->className;
	}

	public function setClassName($className)
	{
		$this->className = $className;
	}

	public function construct()
	{
		return $this->createObject(false);
	}

	public function constructProxy()
	{
		return $this->createObject(true);
	}

	protected function createObject($isProxy)
	{
		$className = CSX_Compat::resolveClassName($this->className);
		if ($isProxy) {
			return new CSX_Proxy($className, array($this->get()));
		}
		else {
			return new $className($this->get());
		}
	}
}
