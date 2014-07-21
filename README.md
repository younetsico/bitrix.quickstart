# bitrix.quickstart
---
Набор скриптов для быстрого старта сайта на Bitrix.

Проект в начальной стадии разработки.

Цель проекта: возможность быстро накатить типовую файловую структуру и ряд сниппетов на свежеустановленный дистрибутив bitrix.

Проект предполагает широкое использование папки /local, в которой расположены все файлы, необходимые разработчику. Папка /bitrix в идеале должна быть полностью занесена в .gitignore, поскольку содержит ядро системы.

## Autoload

Автозагрузка классов.

Больше не придется писать `CModule::IncludeModule('iblock');` в каждом компоненте или скрипте.

Инициализация автолоадера происходит при создании нового экземпляра класса \BitrixQuickStart\Autoloader() в файле init.php.

Большинство стандартных модулей, указанных в документации, будут подключаться автоматически. Это реализовано за счет «таблицы» соответствий между названиями классов и модулями. Например:

    '/^CIBlock/' => 'iblock'
    
Данное выражение указывает автолоадеру, что все классы, начинающиеся на 'CIBlock' относятся к модулю информационных блоков. «Таблица» соответствий захардкожена в классе \BitrixQuickStart\Autoloader(), и может быть расширена методом `addClassMapItem`. Пример:

    $autoloader = new \BitrixQuickStart\Autoloader();
    $autoloader->addClassMapItem('/^CFooBar/', 'foobar');
    
Теперь все методы классов, начинающихся на 'CFooBar' будут работать без отдельного подключения модуля 'foobar'.

Автолоадер подключает также все пользовательские классы, расположенные в папке /local/classes. Каждый класс должен располагаться в отдельном файле, имя которого должно совпадать с названием класса. Например, класс UserHandlers должен быть расположен по адресу `/local/classes/UserHandlers.php`.

Если нужно задать дополнительную директорию, в которой хранятся пользовательские классы, можно воспользоваться методом `addAutoloadPath`. Пример:

    $autoloader = new \BitrixQuickStart\Autoloader();
    $autoloader->addAutoloadPath('/includes/my/classes/');
    
Путь указывается от корня сайта.

## Базовый шаблон сайта

Типовая файловая структура bitrix-шаблона расположена по адресу `/local/templates/main`.

Общие рекомендации по шаблонам bitrix:

1. Все представления (`views`), включая шаблоны компонентов, лучше помещать в папку основного шаблона сайта: `/local/templates/main/components`. Впоследствии это может упросить жизнь при проведении редизайна сайта.

2. Лучше избегать большого количества шаблонов сайта, поскольку сторонний разработчик для внесения минимальной правки в шаблон потратит больше времени на поиск нужного шаблона, нежели на внесение изменений.

3. Избегайте использования автоматически подключаемых файлов style.css в шаблонах компонентов, т.к. это затрудняет внесение изменений. Используйте стандартный автоподключаемый файл styles.css в корне шаблона сайта в качестве единого файла стилей.

4. Клиентскую логику лучше выносить в файлы script.js, которые расположены в шаблонах компонентов и подключаются автоматически. Использовать единый файл с обработчиками событий DOM не возбраняется, но необходимо обязательно комментировать, к какой части страницы относится тот или иной JavaScript-код и где он применяется.

## Robots.txt

Файл полностью закрывает сайт от индексации поисковиками. Это делается для того, чтобы тестовые площадки, размещенные в сети, не оттягивали на себя трафик, отображаясь в результатах поиска наравне с основным сайтом.

В дальнейшем рекомендуется не отслеживать изменения robots.txt, поскольку последние версии bitrix позволяют контент-редакторам и SEO-специалистам править robots.txt через админку.