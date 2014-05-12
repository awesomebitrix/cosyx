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
        } else {
            return null;
        }
    }

    public function getByCode($iblockId, $code)
    {
        $ar = $this->getSingle(
            false,
            array(
                'IBLOCK_ID' => $iblockId,
                'CODE' => $code,
            ),
            false,
            false,
            array(
                'ID', 'NAME', 'CODE'
            )
        );

        if (!empty($ar)) {
            return $ar;
        } else {
            return null;
        }
    }

    public function getByIdElement($id)
    {
        $rs = CIBlockElement::GetByID($id);
        if ($ar = $rs->GetNextElement()) {
            return $ar;
        } else {
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
                    if ($arValue['PROPERTY_TYPE'] == 'E') {
                        if (is_array($arValue['VALUE'])) {
                            foreach ($arValue['VALUE'] as &$v) {
                                $v = $this->getByIdFull($v);
                            }
                        } else {
                            $arValue['VALUE'] = $this->getByIdFull($arValue['VALUE']);
                        }
                    }
                }
            }

            return $ar;
        } else {
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

    /**
     * @param array $arOrder
     * @param array $arFilter
     * @param bool $arGroupBy
     * @param bool $arNavStartParams
     * @param array $arSelectFields
     * @param array $arOptions
     * @return array
     */
    public function getList(
        $arOrder = array("SORT" => "ASC"),
        $arFilter = array(),
        $arGroupBy = false,
        $arNavStartParams = false,
        $arSelectFields = array(),
        $arOptions = array()
    )
    {
        $obj = $this->getListEx($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields, $arOptions);
        return $obj['rows'];
    }

    /**
     * @param array $arOrder
     * @param array $arFilter
     * @param bool $arGroupBy
     * @param bool $arNavStartParams
     * @param array $arSelectFields
     * @param array $arOptions
     * @return array
     */
    public function getListEx(
        $arOrder = array("SORT" => "ASC"),
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

        return array(
            'rows' => $rows,
            'rowcount' => intval($rs->NavRecordCount),
            'pagecount' => $rs->NavPageCount,
            'page' => $rs->NavPageNomer,
            'pagesize' => $rs->NavPageSize,
        );
    }

    public function getListElement(
        $arOrder = array("SORT" => "ASC"),
        $arFilter = array(),
        $arGroupBy = false,
        $arNavStartParams = false,
        $arSelectFields = array()
    )
    {
        $rs = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
        $rows = array();
        while ($ar = $rs->GetNextElement()) {
            $rows[] = $ar;
        }

        return $rows;
    }

    public function getListCached(
        $arOrder = array("SORT" => "ASC"),
        $arFilter = array(),
        $arGroupBy = false,
        $arNavStartParams = false,
        $arSelectFields = array(),
        $expire = 600
    )
    {
        $cache = CSX_Cache::getStore();
        $key = 'iblock_list_' . $this->getKey($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);

        if (!is_array($rows = $cache->get($key))) {
            $rows = $this->getList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
            $cache->set($key, $rows, 0, $expire);
        }

        return $rows;
    }

    public function getListHash(
        $arOrder = array("SORT" => "ASC"),
        $arFilter = array(),
        $arGroupBy = false,
        $arNavStartParams = false,
        $arSelectFields = array()
    )
    {
        $rs = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
        $rows = array();
        while ($ar = $rs->GetNext()) {
            $rows[$ar['ID']] = $ar;
        }

        return $rows;
    }

    public function getListHashCached(
        $arOrder = array("SORT" => "ASC"),
        $arFilter = array(),
        $arGroupBy = false,
        $arNavStartParams = false,
        $arSelectFields = array(),
        $expire = 600
    )
    {
        $cache = CSX_Cache::getStore();
        $key = 'iblock_list_' . $this->getKey($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);

        if (!is_array($rows = $cache->get($key))) {
            $rows = $this->getListHash($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
            $cache->set($key, $rows, 0, $expire);
        }

        return $rows;
    }

    public function getSingle(
        $arOrder = array("SORT" => "ASC"),
        $arFilter = array(),
        $arGroupBy = false,
        $arNavStartParams = false,
        $arSelectFields = array()
    )
    {
        $rs = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
        if ($ar = $rs->GetNext()) {
            return $ar;
        } else {
            return null;
        }
    }

    public function getCount($arFilter = array())
    {
        return CIBlockElement::GetList(false, $arFilter, array(), false, false);
    }

    public function getSingleCached(
        $arOrder = array("SORT" => "ASC"),
        $arFilter = array(),
        $arGroupBy = false,
        $arNavStartParams = false,
        $arSelectFields = array(),
        $expire = 600
    )
    {
        $cache = CSX_Cache::getStore();
        $key = 'iblock_list_single_' . $this->getKey(
                $arOrder,
                $arFilter,
                $arGroupBy,
                $arNavStartParams,
                $arSelectFields
            );

        if (!is_array($ar = $cache->get($key))) {
            $ar = $this->getSingle($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
            $cache->set($key, $ar, 0, $expire);
        }

        return $ar;
    }

    public function getSingleElement(
        $arOrder = array("SORT" => "ASC"),
        $arFilter = array(),
        $arGroupBy = false,
        $arNavStartParams = false,
        $arSelectFields = array()
    )
    {
        $rs = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelectFields);
        if ($ar = $rs->GetNextElement()) {
            return $ar;
        } else {
            return null;
        }
    }

    public function getSingleElementCached(
        $arOrder = array("SORT" => "ASC"),
        $arFilter = array(),
        $arGroupBy = false,
        $arNavStartParams = false,
        $arSelectFields = array(),
        $expire = 600
    )
    {
        $cache = CSX_Cache::getStore();
        $key = 'iblock_list_esingle_' . $this->getKey(
                $arOrder,
                $arFilter,
                $arGroupBy,
                $arNavStartParams,
                $arSelectFields
            );

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

    public function setUrlTemplates(&$rs, $arSubst = array())
    {
        foreach ($rs as &$arItem) {
            $this->setUrlTemplatesItem($arItem, $arSubst);
        }
    }

    public function setUrlTemplatesItem(&$arItem, $arSubst = array())
    {
        foreach ($arSubst as $key => $val) {
            $arItem['LIST_PAGE_URL'] = str_replace("#{$key}#", $val, $arItem['LIST_PAGE_URL']);
            $arItem['DETAIL_PAGE_URL'] = str_replace("#{$key}#", $val, $arItem['DETAIL_PAGE_URL']);
            $arItem['SECTION_PAGE_URL'] = str_replace("#{$key}#", $val, $arItem['SECTION_PAGE_URL']);
        }

        $this->setUrlTemplatesItemEx($arItem);
    }

    protected function setUrlTemplatesItemEx(&$arItem, $stack = array(), &$arSubItem = null)
    {
        $prefix = !empty($stack) ? implode('_', $stack) . '_' : '';
        if ($arSubItem === null) {
            $arSubItem = & $arItem;
        }

        foreach ($arSubItem as $key => $val) {
            if (is_array($val)) {
                $cstack = $stack;
                $cstack[] = $key;
                $this->setUrlTemplatesItemEx($arItem, $cstack, $val);
            } else {
                $arItem['LIST_PAGE_URL'] = str_replace("#{$prefix}{$key}#", $val, $arItem['LIST_PAGE_URL']);
                $arItem['DETAIL_PAGE_URL'] = str_replace("#{$prefix}{$key}#", $val, $arItem['DETAIL_PAGE_URL']);
                $arItem['SECTION_PAGE_URL'] = str_replace("#{$prefix}{$key}#", $val, $arItem['SECTION_PAGE_URL']);
            }
        }
    }

    public function truncate($iblockId)
    {
        $rsRows = CIBlockElement::GetList(
            false,
            array(
                'IBLOCK_ID' => $iblockId,
            ),
            false,
            false,
            array(
                'ID'
            )
        );

        while ($arRow = $rsRows->GetNext()) {
            CIBlockElement::Delete($arRow['ID']);
        }

        return true;
    }

    public function minimify(&$arList, $options = array())
    {
        foreach ($arList as &$arItem) {
            $this->minimifyItem($arItem, $options);
        }

        return $arList;
    }

    public function minimifyItem(&$arItem, $options = array())
    {
        if (is_array($options['allowed_keys'])) {
            $options['allowed_keys'][] = 'PROPERTIES';
            foreach ($arItem as $k => $v) {
                if (!in_array($k, $options['allowed_keys'])) {
                    unset($arItem[$k]);
                }
            }
        }

        if (isset($options['use_raw_values'])) {
            foreach ($arItem as $k => $v) {
                if ($k[0] == '~') {
                    $arItem[substr($k, 1)] = $v;
                    unset($arItem[$k]);
                }
            }
        }

        if (isset($options['strip_raw_values'])) {
            foreach ($arItem as $k => $v) {
                if ($k[0] == '~') {
                    unset($arItem[$k]);
                }
            }
        }

        if (isset($arItem['PROPERTIES'])) {
            foreach ($arItem['PROPERTIES'] as $k => $v) {
                $arItem['PROPERTIES'][$k] = isset($options['use_raw_values']) ? $v['~VALUE'] : $v['VALUE'];
            }
        }

        return $arItem;
    }
}