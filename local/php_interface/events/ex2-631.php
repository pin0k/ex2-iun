<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\EventManager,
    Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$eventManager = EventManager::getInstance();
$eventManager->addEventHandler("search", "BeforeIndex", [Ex2_631::class, "BeforeIndexHandler"]);

class Ex2_631 {
	public static function BeforeIndexHandler($arFields) {
		if($arFields["MODULE_ID"] == "iblock" && $arFields["PARAM2"] == IBLOCK_ID_REVIEWS) {
            
            $elementsORM = CIBlockElement::GetList(
                [],
                [
                    'ID' => $arFields['ITEM_ID'],
                ],
                false,
                false,
                [
                    'ID',
                    'PROPERTY_AUTHOR',
                ]
            )->Fetch();

            $userORM = CUser::GetList(
                '',
                '',
                [
                    'ID' => $elementsORM['PROPERTY_AUTHOR_VALUE'],
                ],
                [
                    'FIELDS' => ['LOGIN']
                ]
            )->Fetch();           

            $arFields['TITLE'] = Loc::getMessage('NEW_TITLE_EX2_631', [
                '#OLD_TITLE#' => $arFields['TITLE'],
                '#LOGIN#' => $userORM['LOGIN'],
            ]);            
		}
		return $arFields;
	}
}