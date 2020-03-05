# Загрузка новостей с помощью AJAX на сайте под управлением 1С-Битрикс

Из этой статьи читатель узнает о том, как можно организовать загрузку новостей в компонентах с помощью AJAX. Как известно, в компонентах 1С-Битрикс по-умолчанию существует постраничная навигация, использующая AJAX. Я же хочу организовать функционал следующим образом: при загрузке страницы посетителю будет показано определённое в параметрах компонента количество новостей. Если новостей больше, чем указано в параметрах компонента, то после списка будет показана ссылка с текстом «Показать еще новости», при клике на которую будет производиться загрузка следующей «порции» новостей. Ссылка будет появляться до тех пор, пока не загрузятся все новости.

На самом деле, новости я выбрал просто для примера, Вы можете организовать загрузку любых элементов, хранящихся в инфоблоках. Не обязательно новостей, но и статей, часто задаваемых вопросов и многого другого.

Как обычно, в своих статьях я использую демо-версию системы редакции «Стандарт». При установке выбираю вариант «для разработчиков». Так как в инфоблоке «Новости магазина» не так много элементов, я делаю по 3 копии каждой новости, чтобы как можно наглядней показать их загрузку с помощью AJAX.

Сначала, в папке /content/ создадим папку news2, а в ней – 2 файла index.php и detail.php. В файле index.php разместим компонент bitrix:news.list, настроив его на инфоблок «Новости магазина» с количеством новостей на странице - 5, а в файле detail.php - компонент bitrix:news.detail, который будет показывать детальное содержимое новости. В рамках нашей задачи изменять будем только шаблон компонента bitrix:news.list.

Итак, приступаем…

После размещения компонента bitrix:news.list на странице index.php, скопируем его шаблон и назовем его "ajax_list". После копирования он будет находиться в папке /bitrix/templates/.default/components/bitrix/news.list/ajax_list/.

В файле template.php заменим код нижнего блока постраничной навигации

```php
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
     <br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
```
своим блоком HTML-кода со ссылкой «Показать еще новости», при клике на которой будут загружаться новости.

```php
<?if($totalPages > 1):?>
    <div class="more-items">
        <a href="#" id="load-items">Показать еще новости</a>
    </div>
<?endif;?>
```

Затем немного стилизуем его. В файл style.css добавим стили для блока и ссылки.

```html

div.more-items{border-top:1px solid #ccc; border-bottom:1px solid #ccc; padding:10px 0; text-align:center; margin-top:30px;}
a#load-items{font-size:14px; font-weight:bold;}
```

В параметрах вызова компонента bitrix:news.list в файле /content/news2/index.php нужно добавить еще один параметр ("AJAX" => $_REQUEST["AJAX"]), значение которого будет браться из массива $_REQUEST. После добавления указанного параметра, редактировать параметры компонента из публичной части сайта нельзя, иначе наш параметр просто затрется. Значение этого параметра будет зависеть от типа загрузки страницы: если страница загружена по ссылке «Показать еще новости», то значение этого параметра буде равно “Y”, иначе значения просто не будет, то есть при первой загрузке страницы это параметр будет иметь значение null. В зависимости от значения $arParams[“AJAX”], в шаблоне компонента будут работать определённые участки кода. В этом можно будет убедиться, если посмотреть содержимое файла template.php. Скачать его можно по ссылке в конце статьи.

Для решения поставленной задачи, в файле template.php нам будут необходимы следующие данные:

* номер текущей страницы – его мы должны знать на каждой странице, то есть при каждой загрузке страницы через AJAX;
* общее количество страниц;
* номер постраничной навигации на странице – нужен для формирования ссылок в постраничной навигации. Если на странице размещено несколько разных компонентов с постраничной навигацией, этот параметр формирует ссылку, указывая «страницы» какого именно компонента показывать. В нашем случае не очень важен, так как у нас один компонент на странице, но на будущее пригодится, если вдруг вы решите добавить еще один компонент с таким же функционалом.
Все эти данные можно получить из объекта, хранящегося в $arResult["NAV_RESULT"].

В самом начале файла template.php, после первой строки, сохраним указанные выше параметры в переменных.

```php
<?php
// номер текущей страницы
$curPage = $arResult["NAV_RESULT"]->NavPageNomer;
// всего страниц - номер последней страницы
$totalPages = $arResult["NAV_RESULT"]->NavPageCount;
// номер постраничной навигации на странице
$navNum = $arResult["NAV_RESULT"]->NavNum;
?>
```

Теперь можно переходить к написанию скрипта на javaScript, который будет делать всю основную «работу», а именно управлять загрузкой новостей. Этот код мы, конечно же, разместим в файле script.js в папке шаблона компонента. У меня скрипт получился следующий.

```javascript
function newsLoader(p){
    var o = this;
    this.root = $(p.root);
    this.newsBlock = $(p.newsBlock, this.root);
    this.newsLoader = $(p.newsLoader);
    this.ajaxLoader = $(p.ajaxLoader);  
    this.curPage = 1;
    this.loadSett = (p.loadSett);
    // загружаем дополнительные новости
    this.loadMoreNews = function(){
        var loadUrl = location.href;
            if(location.search != ''){
                loadUrl += '&';
            }
            else{
                loadUrl += '?';
            }
            loadUrl  += 'PAGEN_'+ o.loadSett.navNum +'=' + (++o.curPage);           
            o.ajaxLoader.show();
        $.ajax({
            url: loadUrl,
            type: "POST",
            data:{
                AJAX: 'Y'                  
                }
            }).done(function(html){
                 o.newsBlock.append(html);
                 o.ajaxLoader.hide();
                  
                if(o.curPage == o.loadSett.endPage){
               o.newsLoader.parent().hide(); 
              }             
              });
      } 
    this.init = function(){
        o.newsLoader.click(function(event){
            o.loadMoreNews();
            event.preventDefault();
        })
    }
}
```

Код скрипта несложный – это «класс», состоящий всего из 2 методов, первого (loadMoreNews), который собственно и загружает новости и второго (init) - в котором инициализируется обработчик клика по ссылке для загрузки новостей. В самом начале скрипта из файла script.js мы создаем свойства, в которые сохраняем объекты: идентификатор «обертки» списка новостей, идентификатор ссылки-загрузчика, путь к gif-изображению, которое будет появляться во время загрузки, а также объект с настройками.

В файле template.php создаем объект «класса» newsLoader.

```php
<?if($totalPages > 1):?>
      $(function(){
     var newsSetLoader = new newsLoader({
        root: '.news-list',
        newsBlock: '.news-wrap',
        newsLoader: '#load-items',
        ajaxLoader: '#ajax-loader img',
            loadSett:{
                endPage: <?=$totalPages?>,
                navNum: <?=$navNum?>
            }   
        });
        newsSetLoader.init();
});
   <?endif;?>
```

Теперь осталось только добавить стили для gif-изображения, которое появляется в процессе загрузки в файл style.css.

```html
#ajax-loader img{width:20px; height:20px; vertical-align:middle; display:none;}
```
