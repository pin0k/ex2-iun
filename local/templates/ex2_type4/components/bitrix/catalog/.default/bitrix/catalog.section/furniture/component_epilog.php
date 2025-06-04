<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

if(!empty($arResult['COUNT_REVIEWS']) && $arResult['COUNT_REVIEWS'] > 0) {
    $APPLICATION->SetPageProperty('ex2_meta', str_replace('#count#', $arResult['COUNT_REVIEWS'], $APPLICATION->GetProperty('ex2_meta')));
}

if(!empty($arResult['FIRST_REVIEWS'])) {
    $APPLICATION->AddViewContent(
        'ADDITIAONAL_REVIEWS',
        '<div id="filial-special" class="information-block">
            <div class="top"></div>
            <div class="information-block-inner">
                <h3>'.Loc::getMessage('TITLE').'</h3>
                <div class="special-product">
                    <div class="special-product-title">'.
                        $arResult['FIRST_REVIEWS']['NAME']   
                   .'</div>
                </div>
            </div>
            <div class="bottom"></div>
        </div>'
    );
}