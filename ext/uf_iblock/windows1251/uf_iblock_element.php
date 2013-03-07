<?
/**
 *
 * ���������������� �������� �������� ������ "����� � ���������"
 *
 * @author: Sergey Leshchenko [mailto:prevedgreat@gmail.com]
 * Date: 2010-12-00 (08 Dec 2010)
 * Version: 0.0.2
 *
 */

// ������������ ���������� ������� �������� ������ OnUserTypeBuildList
// ������� ��������� ��� ���������� ������ ����� ���������������� �������
AddEventHandler('main', 'OnUserTypeBuildList', array('CUserTypeIBlockElement', 'GetUserTypeDescription'), 5000);
class CUserTypeIBlockElement {
	// ---------------------------------------------------------------------
	// ����� ��������� ������� ������:
	// @param array $arUserField - ���������� (���������) ��������
	// @param array $arHtmlControl - ������ ���������� �� ����� (�������� �������, ����� ����� ���-���� � �.�.)
	// ---------------------------------------------------------------------

	// ������� �������������� � �������� ����������� ������� OnUserTypeBuildList
	function GetUserTypeDescription() {
		return array(
			// ���������� �������������
			'USER_TYPE_ID' => 'iblock_element',
			// ��� ������, ������ �������� ��������� ��������� ����
			'CLASS_NAME' => 'CUserTypeIBlockElement',
			// �������� ��� ������ � ������ ����� ���������������� �������
			'DESCRIPTION' => '����� � ���������� ���������',
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
		// ������� ���������� ������ ������ ������������ ���������
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
	// @return string - HTML ��� ������
	function GetEditFormHTML($arUserField, $arHtmlControl) {
		$iIBlockId = intval($arUserField['SETTINGS']['IBLOCK_ID']);
		$sReturn = '';
		// ������� �������� ��������� �� �������� ��������, ������������ � $arHtmlControl['VALUE']
		$arElements = CUserTypeIBlockElement::_getElements($arHtmlControl['VALUE']);
		// html ���� ���-����� ��� �������� ��������
		$sReturn .= '<div>'.CUserTypeIBlockElement::_getItemFieldHTML($arHtmlControl['VALUE'], $iIBlockId, $arElements, $arHtmlControl['NAME']).'</div>';
		return $sReturn;
	}

	// ������� ���������� ��� ������ ����� �������������� �������� �������������� ��������
	// @return string - HTML ��� ������
	function GetEditFormHTMLMulty($arUserField, $arHtmlControl) {
		$iIBlockId = intval($arUserField['SETTINGS']['IBLOCK_ID']);
		// ������� �������� ��������� �� ��������� ��������, ������������ � ������� �������� $arHtmlControl['VALUE']
		$arElements = CUserTypeIBlockElement::_getElements($arHtmlControl['VALUE']);

		$sReturn = '<table cellspacing="0" id="tb'.md5($arHtmlControl['NAME']).'">';
		// html ���� ���-����� ��� ������� �������� ��������
		foreach($arHtmlControl['VALUE'] as $iKey => $iValue) {
			$sReturn .= '<tr><td><div>'.CUserTypeIBlockElement::_getItemFieldHTML($iValue, $iIBlockId, $arElements, $arHtmlControl['NAME']).'</div></td></tr>';
		}
		// html ���� ���-����� ��� ���������� ������ �������� ��������
		$sReturn .= '<tr><td><div>'.CUserTypeIBlockElement::_getItemFieldHTML(0, $iIBlockId, array(), $arHtmlControl['NAME']).'</div></td></tr>';
		// html ����� ��� ������ ��������... (����� �������������� ������ ���������)
		$sReturn .= '<tr><td><div>'.CUserTypeIBlockElement::_getItemFieldHTML(0, $iIBlockId, array(), $arHtmlControl['NAME'], 'y').'</div></td></tr>';
		$sReturn .= '</table>';
		return $sReturn;
	}

	// ������� ���������� ��� ������ ������� �� �������� ������
	// @return string - HTML ��� ������
	function GetFilterHTML($arUserField, $arHtmlControl) {
		//$sVal = intval($arHtmlControl['VALUE']);
		//$sVal = $sVal > 0 ? $sVal : '';
		//return '<input type="text" name="'.$arHtmlControl['NAME'].'" size="20" value="'.$sVal.'" />';
		return CUserTypeIBlockElement::GetEditFormHTML($arUserField, $arHtmlControl);
	}

	// ������� ���������� ��� ������ �������� �������� � ������ ���������
	// @return string - HTML ��� ������
	function GetAdminListViewHTML($arUserField, $arHtmlControl) {
		$iElementId = intval($arHtmlControl['VALUE']);
		if($iElementId > 0) {
			$arElements = CUserTypeIBlockElement::_getElements($iElementId);
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
		static $bWasJs = false;
		// ������� ��� ���������� js-�������, ���������� �� ���������� ����� ����� �������� �������������� ��������
		// �.�. � ������ ��������� �������������� �������� �������� ��-�� ajax ���������� ������� ��������� ������� js
		// ����������� ���� ��� �� ��������
		if(!$bWasJs) {
			$bWasJs = true;
			// ����� ������ � ������ ��-�� ������������ �������� ������ 
			// �� �����-���� /bitrix/admin/iblock_element_search.php �� ���������� ��������
			ob_start();
			?><script type="text/javascript">
				var oIBListUF = {
					oCounter: {},
					addNewRowIBListUF: function(mFieldCounterName, sTableId, sFieldName, sOpenWindowUrl, sSpanId) {
						var oTbl = document.getElementById(sTableId);
						var oRow = oTbl.insertRow(oTbl.rows.length-1);
						var oCell = oRow.insertCell(-1);
						if(!this.oCounter.mFieldCounterName) {
							this.oCounter.mFieldCounterName = 0;
						}
						var sK = 'n'+this.oCounter.mFieldCounterName;
						this.oCounter.mFieldCounterName = parseInt(this.oCounter.mFieldCounterName) + 1;
						sOpenWindowUrl += '&k='+sK;
						sSpanId += '_'+sK;
						oCell.innerHTML = '<input type="text" id="'+sFieldName+'['+sK+']" name="'+sFieldName+'['+sK+']" value="" size="5" />';
						oCell.innerHTML += '<input type="button" value="..." onclick="jsUtils.OpenWindow(\''+sOpenWindowUrl+'\', 600, 500);" />';
						oCell.innerHTML += '&nbsp;<span id="'+sSpanId+'"></span>';
				}
				};
			</script><?
			$sReturn .= ob_get_clean();
		}
		
		if(!empty($arHtmlControl['VALUE']) && is_array($arHtmlControl['VALUE'])) {
			$arElements = CUserTypeIBlockElement::_getElements($arHtmlControl['VALUE']);
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
	// @return string - HTML ��� ������
	function GetAdminListEditHTML($arUserField, $arHtmlControl) {
		return CUserTypeIBlockElement::GetEditFormHTML($arUserField, $arHtmlControl);
	}

	// ������� ���������� ��� ������ �������������� �������� �������� � ������ ��������� � ������ ��������������
	// @return string - HTML ��� ������
	function GetAdminListEditHTMLMulty($arUserField, $arHtmlControl) {
		$iIBlockId = intval($arUserField['SETTINGS']['IBLOCK_ID']);
		$arElements = CUserTypeIBlockElement::_getElements($arHtmlControl['VALUE']);

		// ���� �������������� �������� �������� 
		$sTableId = 'tb'.md5($arHtmlControl['NAME']);
		$sReturn = '<table cellspacing="0" id="'.$sTableId.'">';
		foreach($arHtmlControl['VALUE'] as $iKey => $iValue) {
			$sReturn .= '<tr><td><div>'.CUserTypeIBlockElement::_getItemFieldHTML($iValue, $iIBlockId, $arElements, $arHtmlControl['NAME']).'</div></td></tr>';
		}
		// ���� ���������� ������ �������� �������� 
		$sReturn .= '<tr><td><div>'.CUserTypeIBlockElement::_getItemFieldHTML(0, $iIBlockId, array(), $arHtmlControl['NAME']).'</div></td></tr>';

		// ������ ��������... (�������� js-�������, ������� ���� ��������� ������� GetAdminListViewHTMLMulty)
		$sFieldName_ = str_replace('[]', '', $arHtmlControl['NAME']);
		$mFieldCounterName = md5($sFieldName_);
		$sOpenWindowUrl = '/bitrix/admin/iblock_element_search.php?lang='.LANG.'&amp;IBLOCK_ID='.$iIBlockId.'&amp;n='.$sFieldName_.'&amp;m=n';
		$sSpanId = 'sp_'.$mFieldCounterName;
		$sReturn .= '<tr><td><div><input type="button" onclick="oIBListUF.addNewRowIBListUF(\''.$mFieldCounterName.'\', \''.$sTableId.'\', \''.$sFieldName_.'\', \''.$sOpenWindowUrl.'\', \''.$sSpanId.'\')" value="��������" /></div></td></tr>';
		$sReturn .= '</table>';
		return $sReturn;
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
	// @param int $iIBlockId - ID ��������������� ����� ��� ������ ��������� �� ���������
	// @param array $arElements - ������ ��������� ��������� � ������� = ��������������� ��������� ���������
	// @param string $sFieldName - ��� ��� ���� ���-�����
	// @param string $sMulty - n|y - ������������ (n) ��� ������������� ������� �������� 
	// @return string - HTML ��� ������
	// @private
	function _getItemFieldHTML($iValue, $iIBlockId, $arElements, $sFieldName, $sMulty = 'n') {
		$sReturn = '';
		$iValue = intval($iValue);
		$sKey = randstring(3);
		$sName = 'UF_IBELEMENT_'.randstring(3);
		$sRandId = $sName.'_'.$sKey;
		$sElementName = '';
		if(!empty($arElements[$iValue])) {
			$sElementName = '<a href="'.BX_PERSONAL_ROOT.'/admin/iblock_element_edit.php?ID='.$arElements[$iValue]['ID'].'&type='.$arElements[$iValue]['IBLOCK_TYPE_ID'].'&lang='.LANG.'&IBLOCK_ID='.$arElements[$iValue]['IBLOCK_ID'].'&find_section_section=-1">'.$arElements[$iValue]['NAME'].'</a>';
		}
		$md5Name = md5($sName);
		$sValue = $iValue > 0 ? $iValue : '';
		$sButtonValue = $sMulty == 'y' ? '�������� ...' : '...';
		$sReturn .= '<input type="text" name="'.$sFieldName.'" id="'.$sName.'" value="'.$sValue.'" size="5" />';
		$sReturn .= '<input type="button" value="'.$sButtonValue.'" onclick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_element_search.php?lang='.LANG.'&amp;IBLOCK_ID='.$iIBlockId.'&amp;n='.$sName.'&amp;m='.$sMulty.'&amp;k='.$sKey.'\', 600, 500);" />';
		$sReturn .= '&nbsp;<span id="sp_'.$md5Name.'_'.$sKey.'" >'.$sElementName.'</span>';

		if($sMulty == 'y') {
			$sJsMV = 'MV_'.$md5Name;
			// ������ ������ ������
			$sFieldName_ = str_replace('[]', '', $sFieldName);
			$sJsFuncName = 'InS'.$md5Name;
			ob_start();
			?><script type="text/javascript">
				var <?=$sJsMV?> = 0;
				var <?=$sJsFuncName?> = function(sId, sName) {
					var oTbl = document.getElementById('tb<?=md5($sFieldName)?>');
					var oRow = oTbl.insertRow(oTbl.rows.length-1);
					var oCell = oRow.insertCell(-1);
					var sK = 'n'+<?=$sJsMV?>;
					oCell.innerHTML = '<input type="text" id="<?=$sFieldName_?>['+sK+']" name="<?=$sFieldName_?>['+sK+']" value="'+sId+'" size="5" />';
					oCell.innerHTML += '<input type="button" value="..." onclick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_element_search.php?lang=<?=LANG?>&amp;IBLOCK_ID=<?=$iIBlockId?>&amp;n=<?=$sFieldName_?>&amp;k='+sK+'\', 600, 500);" />';
					oCell.innerHTML += '&nbsp;<span id="sp_<?=md5($sFieldName_)?>_'+<?=sK?>+'">'+sName+'</span>';
					<?=$sJsMV?>++;
				};
			</script><?
			$sReturn .= ob_get_clean();
		}
		return $sReturn;
	}

	// ������� ��������� ������� ��������� �� ��������� ��������
	// @param mixed $mElementId - �������� �������� (������ ��� ����� �����)
	// @return array - ������ ��������� ��������� � ������� = ��������������� ��������� ���������
	// @private
	function _getElements($mElementId = array()) {
		$arReturn = array();

		if(!empty($mElementId)) {

			// ��������� 2010-12-08 (YYYY-MM-DD)
			if(!CModule::IncludeModule('iblock')) {
				return $arReturn;
			}

			$arFilter = array(
				'ID' => array()
			);
			$mElementId = is_array($mElementId) ? $mElementId : array($mElementId);
			foreach($mElementId as $iValue) {
				$iValue = intval($iValue);
				if($iValue > 0) {
					$arFilter['ID'][] = $iValue;
				}
			}
			if(!empty($arFilter['ID'])) {
				$arSelect = array(
					'ID',
					'NAME',
					'IBLOCK_ID',
					'IBLOCK_TYPE_ID'
				);
				$rsItems = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
				while($arItem = $rsItems->GetNext(false, false)) {
					$arReturn[$arItem['ID']] = $arItem;
				}
			}
		}
		return $arReturn;
	}
}
