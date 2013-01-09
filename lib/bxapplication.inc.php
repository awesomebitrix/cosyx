<?php
class CSX_BXApplication extends CSX_ProxyObject {
	public function AddHeadStylesheet($src) {
		$this->object->AddHeadString("<link href=\"{$src}\" type=\"text/css\" rel=\"stylesheet\" />", true);
	}

	public static function isPageMain() {
		global $APPLICATION;

		return $APPLICATION->GetCurPage(true) == SITE_DIR . 'index.php';
	}
}