<?php
use Bitrix\Main\Localization\Loc,
	Bitrix\Main\SystemException,
	Bitrix\Main\Loader,
	Bitrix\Sale;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

CBitrixComponent::includeComponentClass("custom:catalog.viewed.products");

class CNoveltyComponent extends CCustomCatalogViewedProductsComponent
{
	/**
	 * @param $params
	 * @override
	 * @return array
	 */
	public function onPrepareComponentParams($params)
	{
		$params = parent::onPrepareComponentParams($params);

		if(!isset($params["CACHE_TIME"]))
			$params["CACHE_TIME"] = 86400;

		$params["DETAIL_URL"] = trim($params["DETAIL_URL"]);

		if(isset($params["BY"]) && is_array($params["BY"]))
		{
			if(count($params["BY"]))
			{
				$params["BY"] = array_values($params["BY"]);
				$params["BY"] = $params["BY"][0];
			}
			else
				$params["BY"] = "AMOUNT";
		}

		if(!isset($params["BY"]) || !strlen(trim($params["BY"])))
			$params["BY"] = "AMOUNT";


		if(isset($params["PERIOD"]))
		{
			if(is_array($params["PERIOD"]))
			{
				if(count($params["PERIOD"]))
				{
					$params["PERIOD"] = array_values($params["PERIOD"]);
					$params["PERIOD"] = $params["PERIOD"][0];
				}
				else
					$params["PERIOD"] = 0;
			}
			else
			{
				$params["PERIOD"] = (int)$params["PERIOD"];
				if($params["PERIOD"] < 0)
					$params["PERIOD"] = 0;
			}
		}
		else
		{
			$params["PERIOD"] = 0;
		}

		if(!isset($params['FILTER']) || empty($params['FILTER']) || !is_array($params['FILTER']))
			$params['FILTER'] = array();

		return $params;
	}


	/**
	 * @override
	 * @return bool
	 */
	protected function extractDataFromCache()
	{
		if($this->arParams['CACHE_TYPE'] == 'N')
			return false;

		$userGroups = implode(",", Bitrix\Main\UserTable::getUserGroupIds($this->getUserId()));
		return !($this->startResultCache(false, $userGroups));
	}

	/**
	 * @override
	 * @return void
	 */
	protected function formatResult()
	{
		parent::formatResult();
		$this->arResult['PERIOD'] 	= $this->arParams['PERIOD'];
		$this->arResult['BY'] 		= $this->arParams['BY'];
	}

	/**
	 * @override
	 * @return integer[]
	 */
	protected function getProductIds()
	{
        $productIds = [];
        if ($this->arParams['PAGE_ELEMENT_COUNT'] > 0) {
            $res = CIBlockElement::GetList(
                ["CREATED" => "DESC"],
                ['IBLOCK_ID' => 2, "!PROPERTY_NEWPRODUCT" => false, 'CATALOG_AVAILABLE' => "Y", ">CATALOG_QUANTITY" => 0],
                false,
                ["nPageSize" => $this->arParams['PAGE_ELEMENT_COUNT']],
                ['ID']
            );
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $productIds[] = $arFields['ID'];
            }
        }
        return $productIds;
	}


	/**
	 * @override
	 * @throws Exception
	 */
	protected function checkModules()
	{
		parent::checkModules();
		if(!$this->isSale)
			throw new SystemException(Loc::getMessage("CVP_SALE_MODULE_NOT_INSTALLED"));
	}
}