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
class CSX_IBlockProperty extends CSX_Singleton
{
    protected function __construct($args = array())
    {
        CModule::IncludeModule('iblock');
    }

    /**
     * @return CSX_IBlockProperty
     */
    public static function getInstance()
    {
        return self::_getInstance(__CLASS__);
    }

    public function getListValueByXmlId($iblockId, $code, $xmlId)
    {
        $rsPropertyEnum = CIBlockPropertyEnum::GetList(
            array(
                "SORT" => "ASC"
            ),
            array(
                "IBLOCK_ID" => $iblockId,
                "CODE" => $code,
            )
        );

        $arResult = null;
        while ($arProperty = $rsPropertyEnum->GetNext()) {
            if ($arProperty['XML_ID'] == $xmlId) {
                $arResult = $arProperty;
            }
        }

        return $arResult;
    }

    public function getListValueById($id)
    {
        return CIBlockPropertyEnum::GetByID($id);
    }

    public function getById($id, $iblockId) {
        $res = CIBlockProperty::GetByID($id, $iblockId);
        return $res->GetNext();
    }

    public function getByCode($code, $iblockId) {
        $res = CIBlockProperty::GetByID($code, $iblockId);
        return $res->GetNext();
    }
}