<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\EventManager,
    Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$eventManager = EventManager::getInstance();
$eventManager->addEventHandler("search", "BeforeIndex", [Ex2_630::class, "BeforeIndexHandler"]);

class Ex2_630 {
    private const IBLOCK_ID_REVIEWS = 5;

	public static function BeforeIndexHandler($arFields) {
		if($arFields["MODULE_ID"] == "iblock" && $arFields["PARAM2"] == self::IBLOCK_ID_REVIEWS) {
            $reviewsORM = CIBlockElement::GetList(
                [],
                [
                    'ID' => $arFields['ITEM_ID']
                ],
                false,
                false,
                [
                    'PROPERTY_AUTHOR',
                ]
            )->Fetch();

            $classFields = CUserFieldEnum::GetList(
                [],
                [
                    'ID' => $reviewsORM['PROPERTY_AUTHOR_VALUE'],
                ]
            )->Fetch();

            $arFields['TITLE'] = Loc::getMessage('NEW_TITLE', [
                '#OLD_TITLE#' => $arFields['TITLE'],
                '#CLASS#' => $classFields['VALUE'],
            ]);
		}
		return $arFields;
	}
}