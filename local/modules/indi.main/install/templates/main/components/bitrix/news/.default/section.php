<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if ($arParams['USE_RSS'] == 'Y') {
	$rssUrl = str_replace(
		array('#SECTION_ID#', '#SECTION_CODE#'),
		array(urlencode($arResult['VARIABLES']['SECTION_ID']), urlencode($arResult['VARIABLES']['SECTION_CODE'])),
		$arResult['FOLDER'] . $arResult['URL_TEMPLATES']['rss_section']
	);
	if (method_exists($APPLICATION, 'addheadstring')) {
		$APPLICATION->AddHeadString('<link rel="alternate" type="application/rss+xml" title="' . $rssUrl . '" href="' . $rssUrl . '"/>');
	}
	?><a class="pull-right" href="<?=$rssUrl?>" target="_self">
		<img alt="RSS" src="<?=$templateFolder?>/kaluga.kuzov-auto.ru/images/feed.png"/>
	</a><?
}

if ($arParams['USE_SEARCH'] == 'Y') {
	?><h2><?=GetMessage('SEARCH_LABEL')?></h2><?
	$APPLICATION->IncludeComponent(
		'bitrix:search.form',
		'flat',
		array(
			'PAGE' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['search']
		),
		$component
	);
}

if ($arParams['USE_FILTER'] == 'Y') {
	$APPLICATION->IncludeComponent(
		'bitrix:catalog.filter',
		'',
		array(
			'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
			'IBLOCK_ID' => $arParams['IBLOCK_ID'],
			'FILTER_NAME' => $arParams['FILTER_NAME'],
			'FIELD_CODE' => $arParams['FILTER_FIELD_CODE'],
			'PROPERTY_CODE' => $arParams['FILTER_PROPERTY_CODE'],
			'CACHE_TYPE' => $arParams['CACHE_TYPE'],
			'CACHE_TIME' => $arParams['CACHE_TIME'],
			'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
		),
		$component
	);
}

$APPLICATION->IncludeComponent(
	'bitrix:news.list',
	'',
	array(
		'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
		'IBLOCK_ID' => $arParams['IBLOCK_ID'],
		'NEWS_COUNT' => $arParams['NEWS_COUNT'],
		'SORT_BY1' => $arParams['SORT_BY1'],
		'SORT_ORDER1' => $arParams['SORT_ORDER1'],
		'SORT_BY2' => $arParams['SORT_BY2'],
		'SORT_ORDER2' => $arParams['SORT_ORDER2'],
		'FIELD_CODE' => $arParams['LIST_FIELD_CODE'],
		'PROPERTY_CODE' => $arParams['LIST_PROPERTY_CODE'],
		'DISPLAY_PANEL' => $arParams['DISPLAY_PANEL'],
		'SET_TITLE' => $arParams['SET_TITLE'],
		'SET_STATUS_404' => $arParams['SET_STATUS_404'],
		'INCLUDE_IBLOCK_INTO_CHAIN' => $arParams['INCLUDE_IBLOCK_INTO_CHAIN'],
		'ADD_SECTIONS_CHAIN' => $arParams['ADD_SECTIONS_CHAIN'],
		'CACHE_TYPE' => $arParams['CACHE_TYPE'],
		'CACHE_TIME' => $arParams['CACHE_TIME'],
		'CACHE_FILTER' => $arParams['CACHE_FILTER'],
		'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
		'DISPLAY_TOP_PAGER' => $arParams['DISPLAY_TOP_PAGER'],
		'DISPLAY_BOTTOM_PAGER' => $arParams['DISPLAY_BOTTOM_PAGER'],
		'PAGER_TITLE' => $arParams['PAGER_TITLE'],
		'PAGER_TEMPLATE' => $arParams['PAGER_TEMPLATE'],
		'PAGER_SHOW_ALWAYS' => $arParams['PAGER_SHOW_ALWAYS'],
		'PAGER_DESC_NUMBERING' => $arParams['PAGER_DESC_NUMBERING'],
		'PAGER_DESC_NUMBERING_CACHE_TIME' => $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'],
		'PAGER_SHOW_ALL' => $arParams['PAGER_SHOW_ALL'],
		'DISPLAY_NAME' => 'Y',
		'PREVIEW_TRUNCATE_LEN' => $arParams['PREVIEW_TRUNCATE_LEN'],
		'ACTIVE_DATE_FORMAT' => $arParams['LIST_ACTIVE_DATE_FORMAT'],
		'USE_PERMISSIONS' => $arParams['USE_PERMISSIONS'],
		'GROUP_PERMISSIONS' => $arParams['GROUP_PERMISSIONS'],
		'FILTER_NAME' => $arParams['FILTER_NAME'],
		'HIDE_LINK_WHEN_NO_DETAIL' => $arParams['HIDE_LINK_WHEN_NO_DETAIL'],
		'CHECK_DATES' => $arParams['CHECK_DATES'],
		'PARENT_SECTION' => $arResult['VARIABLES']['SECTION_ID'],
		'PARENT_SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
		'DETAIL_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['detail'],
		'SECTION_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['section'],
		'IBLOCK_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['news'],
	),
	$component
);