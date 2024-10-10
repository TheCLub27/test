<?php

$webhookUrl = 'https://bitrix.intopweb.ru/rest/61/nqap7egtbgp8ecgl/';

$token = $_POST['auth']['token'] ?? null;
if (!$token || $token !== 'bxjsnb2vbwjxwtsbgjeer3u8nhop98zx') {
    die('Ошибка: Неверный токен');
}

$dealId = $_POST['data']['FIELDS']['ID'];


$dealResponse = file_get_contents($webhookUrl . 'crm.deal.get?id=' . $dealId);
$dealData = json_decode($dealResponse, true);

if (!$dealData || !isset($dealData['result'])) {
    die('Ошибка при получении данных сделки');
}

$companyId = $dealData['result']['COMPANY_ID'] ?? null; 
if (!$companyId) {
    die('Ошибка: Сделка не связана с компанией');
}


$filter = [
    'filter' => [
        'companyId' => $companyId,
    //фильтр по стадии
    // Код: DT172_16:NEW - Название: Подготовка
    // Код: DT172_16:CLIENT - Название: Согласование
    // Код: DT172_16:UC_5L1TJC - Название: Действует
    // Код: DT172_16:UC_8YZG4E - Название: Расторгнут
    // Код: DT172_16:SUCCESS - Название: Успех
    // Код: DT172_16:FAIL - Название: Провал
    // 'stageId' => 'DT172_16:UC_5L1TJC',
    ],
];

$contractsResponse = file_get_contents($webhookUrl . 'crm.item.list?' . http_build_query(['entityTypeId' => $contractTypeId]) . '&' . http_build_query($filter));
$contractsData = json_decode($contractsResponse, true);

if (!$contractsData || !isset($contractsData['result'])) {
    die('Ошибка при получении договоров компании');
}

$contracts = [];
foreach ($contractsData['result']['items'] as $contract) {
    $contracts[] = $contract['id'];
}


$customFieldForContracts = 'UF_CRM_1728374031'; //айди кастом поля


$updateResponse = file_get_contents($webhookUrl . 'crm.deal.update?' . http_build_query([
    'id' => $dealId,
    'fields' => [
        $customFieldForContracts => $contracts
    ]
]));
$updateData = json_decode($updateResponse, true);

if (!$updateData || $updateData['result'] !== true) {
    die('Ошибка при обновлении сделки');
}

echo "Сделка успешно обновлена. Договоры добавлены в поле '$customFieldForContracts'.";

