<?php
/**
 * Bitrix Extender Project
 *
 * @package config
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * Configuration section parser for phpThumb engine
 *
 * @package config
 */
class CSX_Config_ThumbSectionParser {
	public function parseSection($section) {
		$xpath = new DOMXPath($section->ownerDocument);
		
		$param = $xpath->query('rule', $section);

		$data = array();
		if ($param->length>0) {
			for ($i=0;$i<$param->length;$i++) {
				$node = $param->item($i);

				$name = $node->getAttribute('name');
				$data[$name] = array();
				
				foreach ($node->attributes as $att) {
					if ($att->name!='name') {
						$data[$name][$att->name] = $att->value;
					}
				}
			}			
		}

		return $data;
	}
}