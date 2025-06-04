<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Loader,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Entity\Query,
    Bitrix\Iblock\ElementTable,
    Bitrix\Main\Type\DateTime,
    Bitrix\Iblock\IblockTable;
Loc::loadMessages(__FILE__);

class Ex2_610 {
    private const IBLOCK_ID_REVIEWS = 5;
    private static $lastTimeCheck = '';
    public static function Agent_ex_610($lastTimeExec = '') {
        if(Loader::includeModule("iblock")) {
            $result = static::prepareResult($lastTimeExec);

            //var_dump($result);

            if($result) {
                CEventLog::Add(array(
                    "SEVERITY" => "INFO",
                    "AUDIT_TYPE_ID" => "ex2_610",
                    "MODULE_ID" => "iblock",
                    "DESCRIPTION" => Loc::getMessage('AGENT_LOG_MESSAGE', [
                        '#DATE_TIME#' => static::$lastTimeCheck,
                        '#COUNT#' => count($result),
                    ]),
                ));
            }
        }

        return 'Agent_ex_610();';
    }

    private static function prepareResult($lastTimeExec) {
        $query = new Query(ElementTable::getEntity());

        $query -> setSelect(
            [
                'ID',
                'NAME',
                'IBLOCK_ID',
                'IBLOCK_NAME' => 'IBLOCK.NAME',
                'TIMESTAMP_X' => 'TIMESTAMP_X',
            ]
        );

        $query -> setFilter(
            [
                'IBLOCK_ID' => self::IBLOCK_ID_REVIEWS,
                '>TIMESTAMP_X' => $lastTimeExec ?: (new DateTime()) -> add("-1 day"),
            ]
        );

        $query -> setOrder(
            [
                'IBLOCK_ID' => 'asc',
            ]
        );

        $query -> registerRuntimeField(
            'IBLOCK',
            [
                'data_type' => IblockTable::class,
                'reference' => [
                    '=this.IBLOCK_ID' => 'ref.ID',
                ]
            ]
        );

        static::$lastTimeCheck = $lastTimeExec ?: (new DateTime()) -> add("-1 day") -> toString();

        return $query -> exec() -> fetchAll();
    }
}