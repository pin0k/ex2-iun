<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$eventManager = EventManager::getInstance();
$eventManager->addEventHandler("iblock", "OnBeforeIBlockElementUpdate", [Ex2_590::class, "OnBeforeIBlockElementUpdateHandler"]);
$eventManager->addEventHandler("iblock", "OnAfterIBlockElementUpdate", [Ex2_590::class, "OnAfterIBlockElementUpdateHandler"]);

class Ex2_590 {
    private const IBLOCK_ID_REVIEWS = 5;
    private const LENGTH_PREVIEW_TEXT = 5;
    private const ID_PROPERTY = 9;
    private static $arIdAuthors = [];
	public static function OnBeforeIBlockElementUpdateHandler(&$arFields) {
        if(intval($arFields['IBLOCK_ID'] === self::IBLOCK_ID_REVIEWS)) {
            if (mb_strlen($arFields['PREVIEW_TEXT']) <= self::LENGTH_PREVIEW_TEXT) {
                global $APPLICATION;
                $APPLICATION->throwException(Loc::getMessage('ERROR_MESSAGE', [
                    '#LENGTH#' => mb_strlen($arFields['PREVIEW_TEXT']),
                ]));
                return false;
            }

            if(str_contains($arFields['PREVIEW_TEXT'], '#del#')) {
                $arFields['PREVIEW_TEXT'] = str_replace($arFields['PREVIEW_TEXT'], '', $arFields['PREVIEW_TEXT']);
            }

            $elementORM = CIBlockElement::GetList(
                [],
                [
                    'ID' => $arFields['ID'],
                ],
                false,
                false,
                [
                    'PROPERTY_AUTHOR'
                ]
            )->fetch();

            static::$arIdAuthors['OLD'] = intval($elementORM['PROPERTY_AUTHOR_VALUE']);
        }
	}

    public static function OnAfterIBlockElementUpdateHandler(&$arFields) {
		if($arFields["RESULT"]) {
            static::$arIdAuthors['NEW'] = intval((reset($arFields['PROPERTY_VALUES'][self::ID_PROPERTY]))['VALUE']);

            if(static::$arIdAuthors['OLD'] != static::$arIdAuthors['NEW']) {
                CEventLog::Add([
                    "SEVERITY" => "INFO",
                    "AUDIT_TYPE_ID" => "ex2_590",
                    "MODULE_ID" => "iblock",
                    "ITEM_ID" => $arFields['ID'],
                    "DESCRIPTION" => Loc::getMessage('LOG_MESSAGE', [
                        '#ID#' => $arFields['ID'],
                        '#OLD_ID#' => static::$arIdAuthors['OLD'],
                        '#NEW_ID#' => static::$arIdAuthors['NEW'],
                    ]),
                ]);
            }
        }
	}
}
