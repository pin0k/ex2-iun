<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$rsUsers = CUser::GetList(
	'',
	'',
	[
		'GROUPS_ID' => [CURRENT_GROUP],
		'UF_AUTHOR_STATUS' => CURRENT_STATUS,
	],
	[
		'SELECT' => [
			'UF_AUTHOR_STATUS'
		],
		'FIELDS' => [
			'ID',
			'LOGIN',
		],
	]
);

$arAuthors = [];
while($user = $rsUsers->Fetch()) {
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
while($reviews = $reviewsORM->Fetch()) {
	foreach($arAuthors as $i=>$author) {
		if(intval($reviews['PROPERTY_AUTHOR_VALUE']) === intval($author['ID'])) {
			$arReviews[] = $reviews;
		}
	}	
}

$countProducts = 0;
foreach ($arResult['ITEMS'] as $key => $arItem) {
	foreach($arReviews as $j => $reviews) {
		if(intval($reviews['PROPERTY_PRODUCT_VALUE']) === intval($arItem['ID'])) {
			$arItem['REVIEWS'][] = $reviews;
		}
	}

	if(array_key_exists('REVIEWS', $arItem)) {
		$countProducts ++;
	}


	$arItem['PRICES']['PRICE']['PRINT_VALUE'] = number_format((float)$arItem['PRICES']['PRICE']['PRINT_VALUE'], 0, '.', ' ');
	$arItem['PRICES']['PRICE']['PRINT_VALUE'] .= ' '.$arItem['PROPERTIES']['PRICECURRENCY']['VALUE_ENUM'];

	$arResult['ITEMS'][$key] = $arItem;
}


// echo '<pre>';
// var_dump($countProducts);
// echo '</pre>';
$arResult['COUNT_REVIEWS'] = $countProducts;
$arResult['FIRST_REVIEWS'] = reset($arReviews);

$this->__component->SetResultCacheKeys(['COUNT_REVIEWS', 'FIRST_REVIEWS']);