<?php
// Регистрируем обработчик события главного модуля OnUserTypeBuildList
// Событие создается при построении списка типов пользовательских свойств
AddEventHandler('main', 'OnUserTypeBuildList', array('CUserTypeHtmlElement', 'GetUserTypeDescription'), 5000);

class CUserTypeHtmlElement {
   // ---------------------------------------------------------------------
   // Общие параметры методов класса:
   // @param array $arUserField - метаданные (настройки) свойства
   // @param array $arHtmlControl - массив управления из формы (значения свойств, имена полей веб-форм и т.п.)
   // ---------------------------------------------------------------------

   // Функция регистрируется в качестве обработчика события OnUserTypeBuildList
   function GetUserTypeDescription() {
      return array(
         // уникальный идентификатор
         'USER_TYPE_ID' => 'uf_html_element',
         // имя класса, методы которого формируют поведение типа
         'CLASS_NAME' => 'CUserTypeHtmlElement',
         // название для показа в списке типов пользовательских свойств
         'DESCRIPTION' => 'HTML',
         // базовый тип на котором будут основаны операции фильтра
         'BASE_TYPE' => 'text',
	 'PROPERTY_TYPE' => 'HTML'
      );
   }

   // Функция вызывается при добавлении нового свойства
   // для конструирования SQL запроса создания столбца значений свойства
   // @return string - SQL
   function GetDBColumnType($arUserField) {
      switch(strtolower($GLOBALS['DB']->type)) {
         case 'mysql':
            return 'text';
         break;
         case 'oracle':
            return 'varchar(4000)';
         break;
         case 'mssql':
            return "nvarchar(max)";
         break;
      }
   }

   // Функция вызывается перед сохранением метаданных (настроек) свойства в БД
   // @return array - массив уникальных метаданных для свойства, будет сериализован и сохранен в БД
   function PrepareSettings($arUserField) {
		 return array();
   }

   // Функция вызывается при выводе формы метаданных (настроек) свойства
   // @param bool $bVarsFromForm - флаг отправки формы
   // @return string - HTML для вывода
   function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm) {
      return '';
   }

   // Функция валидатор значений свойства
   // вызвается в $GLOBALS['USER_FIELD_MANAGER']->CheckField() при добавлении/изменении
   // @param array $value значение для проверки на валидность
   // @return array массив массивов ("id","text") ошибок
   function CheckFields($arUserField, $value) {
      $aMsg = array();
      return $aMsg;
   }

   // Функция вызывается при выводе формы редактирования значения свойства
   // @return string - HTML для вывода
   function GetEditFormHTML($arUserField, $arHtmlControl) {
      // html поля веб-формы для текущего значения
      $sReturn .= '<div>'.CUserTypeHtmlElement::_getItemFieldHTML($arHtmlControl['VALUE'], $iIBlockId, $arElements, $arHtmlControl['NAME']).'</div>';
      return $sReturn;
   }

   // Функция вызывается при выводе формы редактирования значения множественного свойства
   // @return string - HTML для вывода
   function GetEditFormHTMLMulty($arUserField, $arHtmlControl) {
      $sReturn .= '<div>'.CUserTypeHtmlElement::_getItemFieldHTML($arHtmlControl['VALUE'], $iIBlockId, $arElements, $arHtmlControl['NAME']).'</div>';
      return $sReturn;
   }

   // Функция вызывается при выводе фильтра на странице списка
   // @return string - HTML для вывода
   function GetFilterHTML($arUserField, $arHtmlControl) {
      return CUserTypeHtmlElement::GetEditFormHTML($arUserField, $arHtmlControl);
   }

   // Функция вызывается при выводе значения свойства в списке элементов
   // @return string - HTML для вывода
   function GetAdminListViewHTML($arUserField, $arHtmlControl) {
        return ' ';
   }

   // Функция вызывается при выводе значения множественного свойства в списке элементов
   // @return string - HTML для вывода
   function GetAdminListViewHTMLMulty($arUserField, $arHtmlControl) {
      return ' ';
   }

   // Функция вызывается при выводе значения свойства в списке элементов в режиме редактирования
   // @return string - HTML для вывода
   function GetAdminListEditHTML($arUserField, $arHtmlControl) {
      return CUserTypeHtmlElement::GetEditFormHTML($arUserField, $arHtmlControl);
   }

   // Функция вызывается при выводе множественного значения свойства в списке элементов в режиме редактирования
   // @return string - HTML для вывода
   function GetAdminListEditHTMLMulty($arUserField, $arHtmlControl) {
      return CUserTypeHtmlElement::GetEditFormHTML($arUserField, $arHtmlControl);
   }

   // Функция должна вернуть представление значения поля для поиска
   // @return string - посковое содержимое
   function OnSearchIndex($arUserField) {
      if(is_array($arUserField['VALUE'])) {
         return implode("\r\n", $arUserField['VALUE']);
      } else {
         return $arUserField['VALUE'];
      }
   }

   // Функция вызывается перед сохранением значений в БД
   // @param mixed $value - значение свойства
   // @return string - значение для вставки в БД
   function OnBeforeSave($arUserField, $value) {
		 return $value;
   }

   // Функция генерации html для поля редактирования свойства
   // @param int $iValue - значение свойства
   // @param int $iIBlockId - ID информационного блока для поиска элементов по умолчанию
   // @param array $arElements - массив элементов инфоблока с ключами = идентификаторам элементов инфоблока
   // @param string $sFieldName - имя для поля веб-формы
   // @param string $sMulty - n|y - поэлементная (n) или множественная вставка значений 
   // @return string - HTML для вывода
   // @private
   function _getItemFieldHTML($iValue, $iIBlockId, $arElements, $sFieldName, $sMulty = 'n') {
      $sReturn = '';
			$sReturn = '<textarea cols="80" rows="12" name="'.$sFieldName.'">' . $iValue . '</textarea>';
			return $sReturn;
   }

}