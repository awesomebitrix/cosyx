<?php
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

	public function getList($arOrder = array("SORT" => "ASC"),
							$arFilter = array(),
							$arGroupBy = false,
							$arNavStartParams = false,
							$arSelectFields = array())
	{
		$rs = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
		$rows = array();
		while($ar = $rs->GetNext()) {
			$rows[] = $ar;
		}

		return $rows;
	}
}