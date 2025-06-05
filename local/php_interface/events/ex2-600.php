<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\EventManager,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Mail\Event;
Loc::loadMessages(__FILE__);

$eventManager = EventManager::getInstance();
$eventManager->addEventHandler("main", "OnAfterUserUpdate", [Ex2_600::class, "OnAfterUserUpdateHandler"]);
$eventManager->addEventHandler("main", "OnBeforeUserUpdate", [Ex2_600::class, "OnBeforeUserUpdateHandler"]);

class Ex2_600 {
    private static $arIdClassAuthor = [];
    public static function OnBeforeUserUpdateHandler(&$arFields) {
        $usersORM = CUser::GetList(
            '',
            '',
            [
                'ID' => $arFields['ID'],
            ],
            [
                'SELECT' => ['UF_USER_CLASS']
            ]
        )->Fetch();

        static::$arIdClassAuthor['OLD'] = intval($usersORM['UF_USER_CLASS']);
	}

	public static function OnAfterUserUpdateHandler(&$arFields) {
		if($arFields["RESULT"]) {
            static::$arIdClassAuthor['NEW'] = intval($arFields['UF_USER_CLASS']);

            if(static::$arIdClassAuthor['OLD'] != static::$arIdClassAuthor['NEW']) {
                $className = [];
                foreach(static::$arIdClassAuthor as $key => $id) {
                    $elementsORM = CUserFieldEnum::GetList(
                        [],
                        [
                            'ID' => $id,
                        ]
                    )->fetch();
                    $className[$key] = $elementsORM['VALUE'];
                }
                Event::send(array(
                    "EVENT_NAME" => "EX2_AUTHOR_INFO",
                    "LID" => $arFields['LID'],
                    "C_FIELDS" => array(
                        'OLD_USER_CLASS' => $className['OLD'],
                        'NEW_USER_CLASS' => $className['NEW'],
                    ),
                ));
            }

            if($_POST['user_info_event'] == "Y") {
                $classORM = CUserFieldEnum::GetList(
                    [],
                    [
                        'ID' => $arFields['UF_USER_CLASS']
                    ]
                )->Fetch();
                Event::send(array(
                    "EVENT_NAME" => "USER_INFO",
                    "LID" => $arFields['LID'],
                    "C_FIELDS" => array(
                        'NAME' => $arFields['NAME'],
                        'LAST_NAME' => $arFields['LAST_NAME'],
                        'ID' => $arFields['ID'], 
                        'MESSAGE' => 'Администратор сайта изменил ваши регистрационные данные',
                        'STATUS' => ($arFields['ACTIVE'] == 'Y') ? 'Активен' : 'Не активен',
                        'LOGIN' => $arFields['LOGIN'],
                        'CLASS' => $classORM['VALUE'],
                    ),
                )); 
            }
        }
	}
}