<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\EventManager,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Mail\Event;
Loc::loadMessages(__FILE__);

$eventManager = EventManager::getInstance();
$eventManager->addEventHandler("main", "OnBeforeUserUpdate", [Ex2_601::class, "OnBeforeUserUpdateHandler"]);
$eventManager->addEventHandler("main", "OnAfterUserUpdate", [Ex2_601::class, "OnAfterUserUpdateHandler"]);

class Ex2_601 {
    private static $arIdStatus = [];

    public static function OnBeforeUserUpdateHandler(&$arFields) {
        $usersORM = CUser::GetList(
            '',
            '',
            [
                'ID' => $arFields['ID']
            ],
            [
                'SELECT' => ['UF_AUTHOR_STATUS']
            ]
        )->Fetch();

        static::$arIdStatus['OLD'] = intval($usersORM['UF_AUTHOR_STATUS']);
	}

	public static function OnAfterUserUpdateHandler(&$arFields) {
		if($arFields["RESULT"]) {
            static::$arIdStatus['NEW'] = intval($arFields['UF_AUTHOR_STATUS']);
            if(static::$arIdStatus['OLD'] != static::$arIdStatus['NEW']) {
                $statusORM = CIBlockElement::GetList(
                    [],
                    [
                        'IBLOCK_ID' => IBLOCK_ID_STATUS,
                    ],
                    false,
                    false,
                    [
                        'ID',
                        'NAME',
                    ]
                );
                $statusName = [];
                while($status = $statusORM -> Fetch()) {
                    foreach(static::$arIdStatus as $k=>$idStatus) {
                        if($idStatus === intval($status['ID'])) {   
                            $statusName[$k] = $status['NAME'];
                        }
                    }     
                }
                Event::send(array(
                    "EVENT_NAME" => "EX2_AUTHOR_INFO",
                    "LID" => $arFields['LID'],
                    "C_FIELDS" => array(
                        'OLD_UF_STATUS' => $statusName['OLD'],
                        'NEW_UF_STATUS' => $statusName['NEW'],
                    ),
                ));
            }    
        }
	}
}