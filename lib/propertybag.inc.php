<?php
class CSX_PropertyBag extends CSX_Singleton implements CSX_IHashable {
	protected $propertyBag = null;

	protected function __construct($args = array())
	{
		$this->propertyBag = new CSX_Hash();
	}

    /**
     * @return CSX_PropertyBag
     */
    public static function getInstance()
	{
		return self::_getInstance(__CLASS__);
	}

	/**
	 *     IHashable implementation
	 */
	public function set()
	{
		$args = func_get_args();
		return call_user_func_array(array($this->propertyBag, 'set'), $args);
	}

	public function get()
	{
		$args = func_get_args();
		return call_user_func_array(array($this->propertyBag, 'get'), $args);
	}

	public function has()
	{
		$args = func_get_args();
		return call_user_func_array(array($this->propertyBag, 'has'), $args);
	}

	public function remove()
	{
		$args = func_get_args();
		return call_user_func_array(array($this->propertyBag, 'remove'), $args);
	}

	public function & getHashRef()
	{
		return $this->propertyBag->getHashRef();
	}
}