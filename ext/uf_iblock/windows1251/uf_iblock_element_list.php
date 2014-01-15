<?
/**
 *
 * ���������������� �������� �������� ������ "����� � ��������� � ���� ������"
 *
 * @author: Sergey Leshchenko [mailto:prevedgreat@gmail.com]
 * Date: 2011-02-15 (15 Feb 2011)
 * Version: 0.0.3
 *
 */

// ������������ ���������� ������� �������� ������ OnUserTypeBuildList
// ������� ��������� ��� ���������� ������ ����� ���������������� �������
AddEventHandler('main', 'OnUserTypeBuildList', array('CUserTypeIBlockElementList', 'GetUserTypeDescription'), 5000);
class CUserTypeIBlockElementList {
	// ---------------------------------------------------------------------
	// ����� ��������� ������� ������:
	// @param array $arUserField - ���������� (���������) ��������
	// @param array $arHtmlControl - ������ ���������� �� ����� (�������� �������, ����� ����� ���-���� � �.�.)
	// ---------------------------------------------------------------------

	// ������� �������������� � �������� ����������� ������� OnUserTypeBuildList
	function GetUserTypeDescription() {
		return array(
			// ���������� �������������
			'USER_TYPE_ID' => 'iblock_element_list',
			// ��� ������, ������ �������� ��������� ��������� ����
			'CLASS_NAME' => 'CUserTypeIBlockElementList',
			// �������� ��� ������ � ������ ����� ���������������� �������
			'DESCRIPTION' => '����� � ���������� ��������� � ���� ������',
			// ������� ��� �� ������� ����� �������� �������� �������
			'BASE_TYPE' => 'int',
		);
	}

	// ������� ���������� ��� ���������� ������ ��������
	// ��� ��������������� SQL ������� �������� ������� �������� ��������
	// @return string - SQL
	function GetDBColumnType($arUserField) {
		switch(strtolower($GLOBALS['DB']->type)) {
			case 'mysql':
				return 'int(18)';
			break;
			case 'oracle':
				return 'number(18)';
			break;
			case 'mssql':
				return "int";
			break;
		}
	}

	// ������� ���������� ����� ����������� ���������� (��������) �������� � ��
	// @return array - ������ ���������� ���������� ��� ��������, ����� ������������ � �������� � ��
	function PrepareSettings($arUserField) {
		// ��������, � ���������� �������� ����� ����������� �����
		$iIBlockId = intval($arUserField['SETTINGS']['IBLOCK_ID']);
		return array(
			'IBLOCK_ID' => $iIBlockId > 0 ? $iIBlockId : 0
		);
	}

	// ������� ���������� ��� ������ ����� ���������� (��������) ��������
	// @param bool $bVarsFromForm - ���� �������� �����
	// @return string - HTML ��� ������
	function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm) {
		$result = '';

		// ��������� 2010-12-08 (YYYY-MM-DD)
		if(!CModule::IncludeModule('iblock')) {
			return $result;
		}

		// ������� �������� �������� 
		if($bVarsFromForm) {
			$value = $GLOBALS[$arHtmlControl['NAME']]['IBLOCK_ID'];
		} elseif(is_array($arUserField)) {
			$value = $arUserField['SETTINGS']['IBLOCK_ID'];
		} else {
			$value = '';
		}
		$result .= '
		<tr style="vertical-align: top;">
			<td>�������������� ���� �� ���������:</td>
			<td>
				'.GetIBlockDropDownList($value, $arHtmlControl['NAME'].'[IBLOCK_TYPE_ID]', $arHtmlControl['NAME'].'[IBLOCK_ID]').'
			</td>
		</tr>
		';
		return $result;
	}

	// ������� ��������� �������� ��������
	// ��������� � $GLOBALS['USER_FIELD_MANAGER']->CheckField() ��� ����������/���������
	// @param array $value �������� ��� �������� �� ����������
	// @return array ������ �������� ("id","text") ������
	function CheckFields($arUserField, $value) {
		$aMsg = array();
		return $aMsg;
	}

	// ������� ���������� ��� ������ ����� �������������� �������� ��������
	// ��� �� ���������� (� �����) � ��� ������ ����� �������������� �������������� ��������
	// @return string - HTML ��� ������
	function GetEditFormHTML($arUserField, $arHtmlControl) {
		$iIBlockId = intval($arUserField['SETTINGS']['IBLOCK_ID']);
		$sReturn = '';
		$sReturn .= '<div>'.CUserTypeIBlockElementList::_getItemFieldHTML($arHtmlControl['VALUE'], $iIBlockId, $arHtmlControl['NAME']).'</div>';
		return $sReturn;
	}

	// ������� ���������� ��� ������ ������� �� �������� ������
	// @return string - HTML ��� ������
	function GetFilterHTML($arUserField, $arHtmlControl) {
		//$sVal = intval($arHtmlControl['VALUE']);
		//$sVal = $sVal > 0 ? $sVal : '';
		//return '<input type="text" name="'.$arHtmlControl['NAME'].'" size="20" value="'.$sVal.'" />';
		return CUserTypeIBlockElementList::GetEditFormHTML($arUserField, $arHtmlControl);
	}

	// ������� ���������� ��� ������ �������� �������� � ������ ���������
	// @return string - HTML ��� ������
	function GetAdminListViewHTML($arUserField, $arHtmlControl) {
		$iElementId = intval($arHtmlControl['VALUE']);
		if($iElementId > 0) {
			$arElements = CUserTypeIBlockElementList::_getElements($arUserField['SETTINGS']['IBLOCK_ID']);
			// ������� � �������: [ID ��������] ��� �������� (���� �������)
			return '['.$iElementId.'] '.(isset($arElements[$iElementId]) ? $arElements[$iElementId]['NAME'] : '');
		} else {
			return '&nbsp;';
		}
	}

	// ������� ���������� ��� ������ �������� �������������� �������� � ������ ���������
	// @return string - HTML ��� ������
	function GetAdminListViewHTMLMulty($arUserField, $arHtmlControl) {
		$sReturn = '';
		if(!empty($arHtmlControl['VALUE']) && is_array($arHtmlControl['VALUE'])) {
			$arElements = CUserTypeIBlockElementList::_getElements($arUserField['SETTINGS']['IBLOCK_ID']);
			$arPrint = array();
			// ������� � �������: [ID ��������] ��� �������� (���� �������) � ������������ " / " ��� ������� ��������
			foreach($arHtmlControl['VALUE'] as $iElementId) {
				$arPrint[] = '['.$iElementId.'] '.(isset($arElements[$iElementId]) ? $arElements[$iElementId]['NAME'] : '');
			}
			$sReturn .= implode(' / ', $arPrint);
		} else {
			$sReturn .=  '&nbsp;';
		}
		return $sReturn;
	}

	// ������� ���������� ��� ������ �������� �������� � ������ ��������� � ������ ��������������
	// ��� �� ���������� (� �����) � ��� �������������� ��������
	// @return string - HTML ��� ������
	function GetAdminListEditHTML($arUserField, $arHtmlControl) {
		return CUserTypeIBlockElementList::GetEditFormHTML($arUserField, $arHtmlControl);
	}

	// ������� ������ ������� ������������� �������� ���� ��� ������
	// @return string - �������� ����������
	function OnSearchIndex($arUserField) {
		if(is_array($arUserField['VALUE'])) {
			return implode("\r\n", $arUserField['VALUE']);
		} else {
			return $arUserField['VALUE'];
		}
	}

	// ������� ���������� ����� ����������� �������� � ��
	// @param mixed $value - �������� ��������
	// @return string - �������� ��� ������� � ��
	function OnBeforeSave($arUserField, $value) {
		if(intval($value) > 0) {
			return intval($value);
		}
	}

	// ������� ��������� html ��� ���� �������������� ��������
	// @param int $iValue - �������� ��������
	// @param int $iIBlockId - ID ��������������� ����� ��� ������ ���������
	// @param string $sFieldName - ��� ��� ���� ���-�����
	// @return string - HTML ��� ������
	// @private
	function _getItemFieldHTML($iValue, $iIBlockId, $sFieldName) {
		$sReturn = '';
		// ������� ������ ���� ��������� ���������
		$arElements = CUserTypeIBlockElementList::_getElements($iIBlockId);
		$sReturn = '<select size="1" name="'.$sFieldName.'">
		<option value=""> </option>';
		foreach($arElements as $arItem) {
			$sReturn .= '<option value="'.$arItem['ID'].'"';
			if($iValue == $arItem['ID']) {
				$sReturn .= ' selected="selected"';
			}
			$sReturn .= '>'.$arItem['NAME'].'</option>';
		}
		$sReturn .= '</select>';
		return $sReturn;
	}

	// ������� ��������� ������� ��������� ���������
	// @param int $iIBlockId - ID ��������������� ����� ��� ������ ���������
	// @param bool $bResetCache - ������������ "����������� ���" ��� ���������
	// @return array - ������ ��������� ��������� � ������� = ��������������� ��������� ���������
	// @private
	function _getElements($iIBlockId = false, $bResetCache = false) {
		static $arVirtualCache = array();
		$arReturn = array();
		$iIBlockId = intval($iIBlockId);
		if(!isset($arVirtualCache[$iIBlockId]) || $bResetCache) {

			// ��������� 2010-12-08 (YYYY-MM-DD)
			if(!CModule::IncludeModule('iblock')) {
				return $arReturn;
			}

			if($iIBlockId > 0) {
				$arFilter = array(
					'IBLOCK_ID' => $iIBlockId
				);
				$arSelect = array(
					'ID',
					'NAME',
					'IBLOCK_ID',
					'IBLOCK_TYPE_ID'
				);
				$rsItems = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
				while($arItem = $rsItems->GetNext(false, false)) {
					// ��������� 2011-02-15 ��� GetList
					$arItem['VALUE'] = $arItem['NAME'];
					$arReturn[$arItem['ID']] = $arItem;
				}
			}
			$arVirtualCache[$iIBlockId] = $arReturn;
		} else {
			$arReturn = $arVirtualCache[$iIBlockId];
		}
		return $arReturn;
	}

	// ��������� 2011-02-15
	function GetList($arUserField) {
		$dbReturn = new CDBResult;
		$arElements = self::_getElements($arUserField['SETTINGS']['IBLOCK_ID']);
		$dbReturn->InitFromArray($arElements);
		return $dbReturn;
	}
}
