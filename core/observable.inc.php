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
class CSX_Observable implements CSX_IObservable
{
	//
	protected $events = array();
	protected $handlers = array();

	public function addEvent($name)
	{
		$this->events[] = $name;
	}

	public function attachEvent($name, $handler)
	{
		if (!in_array($name, $this->events)) {
			throw new CSX_Exception('No event registered [' . $name . ']');
		}

		$this->handlers[] = array(
			'name' => $name
		, 'handler' => $handler
		);

		return $this;
	}

	public function fireEvent($name, $args = null)
	{
		foreach ($this->handlers as $item) {
			if ($item['name'] == $name) {
				call_user_func_array($item['handler'], $args);
			}
		}
	}
}

?>