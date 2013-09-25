<?php
/**
 * Cosix Bitrix Extender
 *
 * @package lib
 * @version $Id$
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 */

/**
 *
 * @package lib
 */
class CSX_IBlock extends CSX_Singleton
{
	protected function __construct($args = array())
	{
		CModule::IncludeModule('iblock');
	}

	/**
	 * @return CSX_IBlock
	 */
	public static function getInstance()
	{
		return self::_getInstance(__CLASS__);
	}

	public function getById($id)
	{
		$rs = CIBlockElement::GetByID($id);
		if ($ar = $rs->GetNext()) {
			return $ar;
		}
		else {
			return null;
		}
	}

	public function getByIdElement($id)
	{
		$rs = CIBlockElement::GetByID($id);
		if ($ar = $rs->GetNextElement()) {
			return $ar;
		}
		else {
			return null;
		}
	}

	public function getByIdFull($id, $options = array())
	{
		$rs = CIBlockElement::GetByID($id);
		if ($el = $rs->GetNextElement()) {
			$ar = $el->GetFields();
			$ar['PROPERTIES'] = $el->GetProperties();

			if (isset($options['resolve_links'])) {
				foreach ($ar['PROPERTIES'] as &$arValue) {
					if ($arValue['PROPERTY_TYPE']=='E') {
						$arValue['VALUE'] = $this->getByIdFull($arValue['VALUE']);
					}
				}
			}

			return $ar;
		}
		else {
			return null;
		}
	}

	public function getByIdCached($id, $expire = 600)
	{
		$cache = CSX_Cache::getStore();
		$key = 'iblock_element_' . $id;

		if (!is_array($ar = $cache->get($key))) {
			$ar = $this->getById($id);
			$cache->set($key, $ar, 0, $expire);
		}

		return $ar;
	}

	public function getByIdElementCached($id, $expire = 600)
	{
		$cache = CSX_Cache::getStore();
		$key = 'iblock_eelement_' . $id;

		if (!is_array($ar = $cache->get($key))) {
			$ar = $this->getByIdElement($id);
			$cache->set($key, $ar, 0, $expire);
		}

		return $ar;
	}

	public function getList($arOrder = array("SORT" => "ASC"),
							$arFilter = array(),
							$arGroupBy = false,
							$arNavStartParams = false,
							$arSelectFields = array(),
							$arOptions = array()
	)
	{
		$rs = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
		if (isset($arOptions['executed'])) {
			$arOptions['executed']($rs);
		}

		$rows = array();
		while ($ar = $rs->GetNext()) {
			$rows[] = $ar;
		}

		return $rows;
	}

	public function getListElement($arOrder = array("SORT" => "ASC"),
							$arFilter = array(),
							$arGroupBy = false,
							$arNavStartParams = false,
							$arSelectFields = array())
	{
		$rs = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
		$rows = array();
		while ($ar = $rs->GetNextElement()) {
			$rows[] = $ar;
		}

		return $rows;
	}

	public function getListCached($arOrder = array("SORT" => "ASC"),
								  $arFilter = array(),
								  $arGroupBy = false,
								  $arNavStartParams = false,
								  $arSelectFields = array(),
								  $expire = 600)
	{
		$cache = CSX_Cache::getStore();
		$key = 'iblock_list_' . $this->getKey($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);

		if (!is_array($rows = $cache->get($key))) {
			$rows = $this->getList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
			$cache->set($key, $rows, 0, $expire);
		}

		return $rows;
	}

	public function getListHash($arOrder = array("SORT" => "ASC"),
							$arFilter = array(),
							$arGroupBy = false,
							$arNavStartParams = false,
							$arSelectFields = array())
	{
		$rs = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
		$rows = array();
		while ($ar = $rs->GetNext()) {
			$rows[$ar['ID']] = $ar;
		}

		return $rows;
	}

	public function getListHashCached($arOrder = array("SORT" => "ASC"),
								  $arFilter = array(),
								  $arGroupBy = false,
								  $arNavStartParams = false,
								  $arSelectFields = array(),
								  $expire = 600)
	{
		$cache = CSX_Cache::getStore();
		$key = 'iblock_list_' . $this->getKey($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);

		if (!is_array($rows = $cache->get($key))) {
			$rows = $this->getListHash($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
			$cache->set($key, $rows, 0, $expire);
		}

		return $rows;
	}

	public function getSingle($arOrder = array("SORT" => "ASC"),
							  $arFilter = array(),
							  $arGroupBy = false,
							  $arNavStartParams = false,
							  $arSelectFields = array())
	{
		$rs = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
		if ($ar = $rs->GetNext()) {
			return $ar;
		}
		else {
			return null;
		}
	}

	public function getCount($arFilter = array())
	{
		return CIBlockElement::GetList(false, $arFilter, array(), false, false);
	}

	public function getSingleCached($arOrder = array("SORT" => "ASC"),
								  $arFilter = array(),
								  $arGroupBy = false,
								  $arNavStartParams = false,
								  $arSelectFields = array(),
								  $expire = 600)
	{
		$cache = CSX_Cache::getStore();
		$key = 'iblock_list_single_' . $this->getKey($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);

		if (!is_array($ar = $cache->get($key))) {
			$ar = $this->getSingle($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
			$cache->set($key, $ar, 0, $expire);
		}

		return $ar;
	}

	public function getSingleElement($arOrder = array("SORT" => "ASC"),
		$arFilter = array(),
		$arGroupBy = false,
		$arNavStartParams = false,
		$arSelectFields = array())
	{
		$rs = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
		if ($ar = $rs->GetNextElement()) {
			return $ar;
		}
		else {
			return null;
		}
	}

	public function getSingleElementCached($arOrder = array("SORT" => "ASC"),
		$arFilter = array(),
		$arGroupBy = false,
		$arNavStartParams = false,
		$arSelectFields = array(),
		$expire = 600)
	{
		$cache = CSX_Cache::getStore();
		$key = 'iblock_list_esingle_' . $this->getKey($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);

		if (!is_array($ar = $cache->get($key))) {
			$ar = $this->getSingleElement($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
			$cache->set($key, $ar, 0, $expire);
		}

		return $ar;
	}

	protected function getKey()
	{
		$args = func_get_args();
		return md5(serialize($args));
	}

	public function setUrlTemplates(&$rs, $arSubst = array()) {
		foreach ($rs as &$arItem) {
			$this->setUrlTemplatesItem($arItem, $arSubst);
		}
	}

	public function setUrlTemplatesItem(&$arItem, $arSubst = array()) {
		foreach ($arSubst as $key => $val) {
			$arItem['LIST_PAGE_URL'] = str_replace("#{$key}#", $val, $arItem['LIST_PAGE_URL']);
			$arItem['DETAIL_PAGE_URL'] = str_replace("#{$key}#", $val, $arItem['DETAIL_PAGE_URL']);
			$arItem['SECTION_PAGE_URL'] = str_replace("#{$key}#", $val, $arItem['SECTION_PAGE_URL']);
		}

		$this->setUrlTemplatesItemEx($arItem);
	}

	protected function setUrlTemplatesItemEx(&$arItem, $stack = array(), &$arSubItem = null) {
		$prefix = !empty($stack) ? implode('_', $stack) . '_' : '';
		if ($arSubItem===null) $arSubItem = &$arItem;

		foreach ($arSubItem as $key => $val) {
			if (is_array($val)) {
				$cstack = $stack;
				$cstack[] = $key;
				$this->setUrlTemplatesItemEx($arItem, $cstack, $val);
			}
			else {
				$arItem['LIST_PAGE_URL'] = str_replace("#{$prefix}{$key}#", $val, $arItem['LIST_PAGE_URL']);
				$arItem['DETAIL_PAGE_URL'] = str_replace("#{$prefix}{$key}#", $val, $arItem['DETAIL_PAGE_URL']);
				$arItem['SECTION_PAGE_URL'] = str_replace("#{$prefix}{$key}#", $val, $arItem['SECTION_PAGE_URL']);
			}
		}

	}
}