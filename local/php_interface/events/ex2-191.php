<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\EventManager;

$eventManager = EventManager::getInstance();
$eventManager->addEventHandler("main", "OnBuildGlobalMenu", [Ex2_190::class, "OnBuildGlobalMenuHandler"]);

class Ex2_190 {
    private const CURRENT_GROUP = 5;
    private const CURRENT_MENU_ITEM = 'global_menu_content';
    public static function OnBuildGlobalMenuHandler(&$aGlobalMenu, &$aModuleMenu) {
        global $USER;
        if(in_array(self::CURRENT_GROUP, CUser::GetUserGroup($USER->GetID()))) {
            foreach($aGlobalMenu as $key=>$data) {
                if($key != self::CURRENT_MENU_ITEM) {
                    unset($aGlobalMenu[$key]);
                }
            }

            foreach($aModuleMenu as $key=>$data) {
                if($data['parent_menu'] != self::CURRENT_MENU_ITEM) {
                    unset($aModuleMenu[$key]);
                }
            }

            $aGlobalMenu['global_menu_fast_access'] = [
                'menu_id' => 'fast_access',
                'text' => 'Быстрый доступ',
                'title' => 'Быстрый доступ',
                'sort' => 200,
                'items_id' => 'global_menu_fast_access',
                'items' => [
                    [
                        'parent_menu' => 'global_menu_fast_access',
                        'text' => 'Ссылка 1',
                        'title' => 'Ссылка 1',
                        'url' => 'https://test1',
                    ],
                    [
                        'parent_menu' => 'global_menu_fast_access',
                        'text' => 'Ссылка 2',
                        'title' => 'Ссылка 2',
                        'url' => 'https://test2',
                    ]
                ],
            ];
        }
    }
}