<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
if(!CModule::IncludeModule("iblock"))
	echo $arReplace["SITE_ID"] = WIZARD_SITE_DIR;
		$ib = new CIBlock;
		$arFields = Array(
			"SITE_ID" => WIZARD_SITE_ID,
		);
		$res = $ib->Update($arIBlock["ID"], $arFields);