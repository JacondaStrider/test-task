<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//Здесь происходит добавление элемента в higload который явно указываем по идентификатору
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
const MY_HL_BLOCK_ID = 2;
CModule::IncludeModule('highloadblock');
function GetEntityDataClass($HlBlockId)
{
    if (empty($HlBlockId) || $HlBlockId < 1)
    {
        return false;
    }
    $hlblock = HLBT::getById($HlBlockId)->fetch();
    $entity = HLBT::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    return $entity_data_class;
}
//Получаем данные и подставляем их в нужные поля
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $entity_data_class = GetEntityDataClass(MY_HL_BLOCK_ID);
  $data = array(
        'UF_FIO'         => $_POST['fio'],
        'UF_EMAIL'       => $_POST['email'],
        'UF_PHONE'       => $_POST['phone'],
        'UF_QUESTION'    => $_POST['question']
     );
  //Добавляем дополнительную валидацию на проверку пустых полей с серверной стороны
  if ($_POST['fio'] != '' && $_POST['email'] != '' && $_POST['question'] != '') {
    $result = $entity_data_class::add($data);
  } else {
    echo $msg = 'Ошибка! Одно из обязательных полей пустое!';
  }

  return $result;
}
?>
