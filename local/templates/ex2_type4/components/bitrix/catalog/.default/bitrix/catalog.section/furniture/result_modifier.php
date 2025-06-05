<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$usersORM = CUser::GetList(
	'',
	'',
	[
		'UF_AUTHOR_STATUS' => CURRENT_STATUS_AUTHOR
	],
	[
		'SELECT' => ['UF_AUTHOR_STATUS',],
		'FIELDS' => ['ID',],
	]
);

$arAuthors = [];
while($user = $usersORM->Fetch()) {
	$arAuthors[] = $user;
}

$reviewsORM = CIBlockElement::GetList(
	[],
	[
		'IBLOCK_ID' => IBLOCK_ID_REVIEWS,
		'ACTIVE' => 'Y',
	],
	false,
	false,
	[
		'ID',
		'NAME',
		'PROPERTY_AUTHOR',
		'PROPERTY_PRODUCT',
	]
);

$arReviews = [];
while ($reviews = $reviewsORM->Fetch()) {
	foreach($arAuthors as $j=>$author) {
		if(intval($reviews['PROPERTY_AUTHOR_VALUE']) === intval($author['ID'])) {
			$arReviews[] = $reviews;
		}
	}
}

$countProductWidthReviews = 0;
foreach ($arResult['ITEMS'] as $key => $arItem) {
	foreach($arReviews as $k=>$reviews) {
		if(intval($arItem['ID']) === intval($reviews['PROPERTY_PRODUCT_VALUE'])) {
			$arItem['REVIEWS'][] = $reviews;
		}
	}

	if(array_key_exists('REVIEWS', $arItem)) {
		$countProductWidthReviews++;
	}

	$arItem['PRICES']['PRICE']['PRINT_VALUE'] = number_format((float)$arItem['PRICES']['PRICE']['PRINT_VALUE'], 0, '.', ' ');
	$arItem['PRICES']['PRICE']['PRINT_VALUE'] .= ' '.$arItem['PROPERTIES']['PRICECURRENCY']['VALUE_ENUM'];

	$arResult['ITEMS'][$key] = $arItem;
}

$arResult['COUNT_REVIEWS'] = $countProductWidthReviews;

$this->__component->SetResultCacheKeys(['COUNT_REVIEWS',]);
