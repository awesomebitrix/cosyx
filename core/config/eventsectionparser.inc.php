<?php
/**
 * Cosix Bitrix Extender
 *
 * @package config
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * Configuration section parser for application events
 *
 * @package config
 */
class CSX_Config_EventSectionParser
{
	public function parseSection($section)
	{
		$xpath = new DOMXPath($section->ownerDocument);

		$handlers = array();

		$list = $xpath->query('event', $section);
		foreach ($list as $item) {
			$chandlers = array();

			$lhndl = $xpath->query('handler', $item);
			foreach ($lhndl as $hitem) {
				$chandlers[] = array(
					'class' => $hitem->getAttribute('class')
				, 'handler' => $hitem->getAttribute('handler')
				);
			}

			$name = $item->getAttribute('name');
			if (array_key_exists($name, $handlers)) {
				$handlers[$name] = array_merge($handlers[$name], $chandlers);
			}
			else {
				$handlers[$name] = $chandlers;
			}
		}

		return $handlers;
	}
}

?>