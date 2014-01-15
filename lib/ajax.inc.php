<?php
/**
 * Cosix Bitrix Extender
 *
 * @package ui
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 * @package ui
 */
class CSX_Ajax {
	/**
	 * Method to handle ajax requests (like ASP.NET UpdatePanel).
	 * Reset output buffer and outputs only target component.
	 * Usually client script replaces some container content with received data.
	 *
	 * @param $key
	 * @param $component
	 * @param $arParams
	 */
	public static function handleRequest($key, &$component, &$arParams) {
		if (CSX_Server::getRequest()->has("ajax_{$key}")=='yes') {
			CSX_Server::getRequest()->remove("ajax_{$key}");

			global $APPLICATION;
			$APPLICATION->RestartBuffer();

			$arNewParams = array();
			foreach ($arParams as $key => $value) {
				if (substr($key, 0, 1) == '~') {
					$arNewParams[substr($key, 1)] = $value;
				}
			}
			$arNewParams['CSX_AJAX_CALL'] = true;

			$APPLICATION->IncludeComponent(
				$component->GetName(),
				$component->GetTemplateName(),
				$arNewParams,
				false,
				array('HIDE_ICONS' => 'Y')
			);

			die();
		}
	}
}