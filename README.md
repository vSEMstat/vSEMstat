# О проекте

Быстрая библиотека для работы в качестве API клиента для Топвизор и Яндекс.Метрика.

## Использование

1. Скачать библиотеку на ваш хостинг
2. Создать пустую базу данных Mysql
3. Заполнить конфигруацию в файле /oauth/config.php
4. Выполнить файл /examples/EasyExperiment/index.php
5. Готовые данные уже собраны в вашей базе mysql!

## Описание конфигурации

Все настройки библиотеки, а также некоторые пользовательские данные хранятся в файле /oauth/config.php
Файл разбит на несколько секций:

* metrika
* topvisor
* mysql

А также пользовательская конфигурация

* период импорта данных
* списки URL из личного кабинета Топвизора для импорта групп и папок

В файле /oauth/config.php есть более подробные комментарии по каждой позиции

## Библиотека

### src

Код библиотеки расположен в каталоге src. Единая точка входа - файл vSEMstat.php - именно он подключается в примере. Файл vSEMstat.php подключает все компоненты библиотеки.

Компоненты библиотеки:

* BaseService.php - базовые методы сервисов
* MetrikaService.php - реализация методов для работы с API Яндекс.Метрика
* TopvisorService.php - реализация методов для работы с API Топвизор

### oauth

Каталог содержит конфигурационный файл config.php
Каталог содержит пример получения Oauth токена для доступа к вашему приложению Яндекс.Метрика. Для его использования разместите код библиотеки на вашем хостинге, введите данные приложения яндекса в файле конфигурации config.php и откройте в браузере ссылку
```
https://ваш_адрес_хоста/oauth/index.php
```
После авторизации в яндексе произойдет переадресация на этот же адрес и на странице отобразится Oauth токен. Токен автоматически сохранится в файл /oauth/oauth_key.key. Этот токен потребуется при работе с API Яндекс.Метрика

### examples

#### EasyExperiment

Содержит файл index.php, который реализует простой пример использования библиотеки для выбора данных из Яндекс.Метрика и Топвизор и формирует представление в Mysql таблице.
