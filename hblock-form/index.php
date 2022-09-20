<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Форма обратной связи");
?>
<!--Подключаем необходимые библиотеки (не стал подключать в header, так как нам нужна сейчас только одна страница. Также использовал cdn для удобства, в обычной работе скачиваю и подключаю напрямую)-->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script src="/bitrix/templates/books/themes/blue_default/assets/js/mask.js"></script>
<!--Форма с отправкой данных в highloadblock-->
<form id="hblock-form">
  <div class="mb-3">
    <label for="fio" class="form-label">ФИО</label>
    <input name="fio" id="fio" class="form-control" type="text" placeholder="Иванов Иван Иванович" required>
  </div>
  <div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input name="email" id="email" class="form-control" type="email" placeholder="Введите ваш Email" required>
  </div>
  <div class="mb-3">
    <label for="phone" class="form-label">Телефон</label>
    <input name="phone" id="phone" class="form-control" type="text" placeholder="Введите ваш телефон">
  </div>
  <div class="mb-3">
    <label for="question" class="form-label">Ваш вопрос</label>
    <input name="question" id="question" class="form-control" type="text" placeholder="Введите ваш вопрос" required>
  </div>
  <button type="submit" name="submit" class="btn btn-primary">Отправить</button>
</form>
<!--Компонент списка higload блоков-->
 <?$APPLICATION->IncludeComponent(
	"bitrix:highloadblock.list",
	"test.form",
	Array(
		"BLOCK_ID" => "2",
		"CHECK_PERMISSIONS" => "N",
		"DETAIL_URL" => "",
		"FILTER_NAME" => "",
		"PAGEN_ID" => "page",
		"ROWS_PER_PAGE" => "5",
		"SORT_FIELD" => "UF_FIO",
		"SORT_ORDER" => "ASC"
	)
);?><br>
<!--Стили (также не стал выносить в отдельный файл, так как 1 страница)-->
<style>
  #hblock-form {
    margin-bottom: 40px;
  }
  .reports-head-cell-title {
    cursor: default;
  }
  th:nth-child(2) .reports-head-cell-title, th:nth-child(6) .reports-head-cell-title {
    cursor: pointer;
  }
</style>
<!--Простенький скрипт отправки через ajax-->
<script>
  let submit = document.querySelector('#hblock-form button'),
      form   = document.querySelector('#hblock-form');

  //Проверка есть ли форма на странице
  if (form) {
    //Изначально блокируем кнопку отправки
    $(submit).prop('disabled', true);
    //Простая валидация считывающая ввод значений в инпуты и отключающая кнопку Отправить если значение полей с атрибутом required пустое
    $('input[required]').keyup(function(){
      $('input[required]').each(function(){
        if ($(this).val().length <= 0) {
          $(submit).prop('disabled', true);
        } else {
          $(submit).prop('disabled', false);
        }
      });
    });
  //Добавляем слушатель на отправку
    form.addEventListener('submit', function(e){
  //Отменяем отправку по умолчанию
        e.preventDefault();
        $this = $(this);
  //Блокируем кнопку отправки чтобы пользователь не отправлял несколько заявок
        $(submit).prop('disabled', true);
  //Отправляем данные через с помощью ajax
          $.ajax({
              type: "POST",
              url: "/ajax.php",
              data: jQuery(this).serialize(),
              //На всякий случай смотрим что возвращает файл ajax.php
              success: function(msg) {
                  //Проверяем что отдает нам ajax.php если не прошла валидация с его стороны выведет сообщение об ошибке, если прошла то сообщение о успешной отправке
                  if (msg === 'Ошибка! Одно из обязательных полей пустое!' ) {
                    $(form).after('<div class="alert alert-warning">' + msg + '</div>');
                    $(submit).prop('disabled', false);
                  } else {
                    $(form).html('<div class="alert alert-success">Ваше сообщение успешно отправлено!</div>');
                    $(form).css('margin-top','40px');
                  }
              }
          });
    });
  }


  //Вызов функции маски для телефона
  Inputmask("+7 (999) 999-99-99").mask(document.querySelectorAll('input[name="phone"]'));
</script>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
