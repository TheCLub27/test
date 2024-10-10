<?php
use Bitrix\Main\Loader;
use Bitrix\Crm\DealTable;

AddEventHandler("crm", "OnBeforeCrmDealAdd", "OnBeforeCrmDealAddHandler");
AddEventHandler("crm", "OnBeforeCrmDealUpdate", "OnBeforeCrmDealUpdateHandler");

function OnBeforeCrmDealAddHandler(&$fields) {
    if (!Loader::includeModule('crm')) {
        return;
    }

   
    if (isset($fields['COMPANY_ID']) && $fields['COMPANY_ID']) {
        $companyId = $fields['COMPANY_ID'];
        $contractTypeId = 1056;

        $contracts = getContractsByCompanyId($companyId, $contractTypeId);
        
        if (!empty($contracts)) {
            $customFieldForContracts = 'UF_CRM_1728455416';
            $fields[$customFieldForContracts] = $contracts; 
        }
    }
}

function OnBeforeCrmDealUpdateHandler(&$fields) {
    if (!Loader::includeModule('crm')) {
        return;
    }

    
    if (isset($fields['COMPANY_ID']) && $fields['COMPANY_ID']) {
        $companyId = $fields['COMPANY_ID'];
        $contractTypeId = 172;

        $contracts = getContractsByCompanyId($companyId, $contractTypeId);
        
        if (!empty($contracts)) {
            $customFieldForContracts = 'UF_CRM_1728374031';
            $fields[$customFieldForContracts] = $contracts; 
        }
    }
}


function getContractsByCompanyId($companyId, $contractTypeId) {
    $factory = Bitrix\Crm\Service\Container::getInstance()->getFactory($contractTypeId);
    if (!$factory) {
        return [];
    }

    $contractsResult = $factory->getItems([
        'filter' => ['=COMPANY_ID' => $companyId]
    ]);

    $contracts = [];
    foreach ($contractsResult as $contract) {
        $contracts[] = $contract->getId();
    }

    return $contracts;
}
?>