<?php
/**
 * Cosix Bitrix Extender
 *
 * @package core
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * Parser for configuration file route table
 *
 * @package core
 */
class CSX_Server_RouteTableSectionParser
{
	public function parseSection($section)
	{
		$xpath = new DOMXPath($section->ownerDocument);

		$table = $xpath->query('route', $section);

		$data = array();
		foreach ($table as $node) {
			$name = $node->getAttribute('name');
			$data[$name] = array();

			foreach ($node->attributes as $att) {
				if ($att->name != 'name') {
					$data[$name][$att->name] = $this->prepare($att->value);
				}
			}

			if ($node->hasChildNodes()) {
				foreach ($node->childNodes as $child) {
					if ($child instanceof DOMText) {
						continue;
					}

					$data[$name][$child->nodeName] = array();
					if ($child->hasChildNodes()) {
						foreach ($child->childNodes as $item) {
							if ($item instanceof DOMText) {
								continue;
							}

							$data[$name][$child->nodeName][] = $this->prepare($item->textContent);
						}
					}
				}
			}
		}

		return $data;
	}

	protected function prepare($s)
	{
		$s = str_replace('%CSX_COSYX_URL%', CSX_COSYX_URL, $s);
		$s = str_replace('%CSX_APP_URL%', CSX_APP_URL, $s);
		return $s;
	}
}

?>