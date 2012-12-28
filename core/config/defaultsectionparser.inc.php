<?php
/**
 * Cosix Bitrix Extender
 *
 * @package core
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * Default section parser for configuration file
 *
 * @package config
 */
class CSX_Config_DefaultSectionParser
{
	public function parseSection($section)
	{
		$xpath = new DOMXPath($section->ownerDocument);

		$param = $xpath->query('param', $section);

		$data = array();
		if ($param->length > 0) {
			for ($i = 0; $i < $param->length; $i++) {
				$node = $param->item($i);

				if ($node->getAttribute('name') != null) {
					$name = $node->getAttribute('name');
					$data[$name] = $param->item($i)->textContent;
				}
				else {
					$data[] = $node->textContent;
				}
			}
		}

		return $data;
	}
}

?>