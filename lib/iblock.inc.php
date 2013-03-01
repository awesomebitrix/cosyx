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
							$arSelectFields = array())
	{
		$rs = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
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

	protected function getKey()
	{
		$args = func_get_args();
		return md5(serialize($args));
	}

}