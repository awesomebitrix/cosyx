<?php
/**
 * Cosyx Bitrix Extender Project
 *
 * @package mvc
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * @package mvc
 */
class CSX_Mvc_PhpView extends CSX_Mvc_View {
	public function fetch() {
		foreach ($this->params as $k => &$v) {
			$GLOBALS[$k] = $v;
		}
		
		ob_start();
		include (CSX_APP_DIR . '/views/' . $this->view . '.inc.php');
		$c = ob_get_contents();
		ob_end_clean();
		
		return $c;
	}
}