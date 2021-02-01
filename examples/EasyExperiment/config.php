<?php

return [

	'metrika' => [
		'client_id' => '',		// Идентификатор приложения
		'client_secret' => '',	// Пароль приложения
		'counter_id' => '',		// счетчик метрики
		'token' => ''			// токен доступа
	],

	'topvisor' => [
		'user_id' => '',		// идентификатор пользователя
		'api_key' => '',		// api ключ
		'project_id' => '',		// идентиикатор проекта
	],

	'mysql' => [
	    'host' => 'mysql',		// имя хоста сервера базы данных
	    'username' => 'root',	// пользователь базы данных
	    'password' => 'root',	// пароль
	    'database' => 'vSEMstat',// имя базы данных
	],


	'dates' = [
		'2020-12-07',						// дата начала импорта
		date('Y-m-d', strtotime('today')),  // дата завершения импорта
	],

	// список URL из личного кабинета Топвизора для списка 1
	'topvisor_google_a' => [
		'https://topvisor.com/project/dynamics/2535875/#historyView=1&templateDateRange=data_range_1y&dates=&date1=20.12.2019&date2=20.12.2020&typeRange=2&typeRangeCompare=4&countDates=31&regionsIndexes=1&competitorsIds=&folderId=0&groupId=0&tags=&dynamic=&minPos=&maxPos=&onlyExistsFirstDate=0',
		'https://topvisor.com/project/dynamics/2535875/#historyView=1&templateDateRange=data_range_1y&dates=&date1=20.12.2019&date2=20.12.2020&typeRange=2&typeRangeCompare=4&countDates=31&regionsIndexes=1&competitorsIds=&folderId=0&groupId=0&tags=&dynamic=&minPos=&maxPos=&onlyExistsFirstDate=0',
		'https://topvisor.com/project/dynamics/2535875/#historyView=1&templateDateRange=data_range_1y&dates=&date1=20.12.2019&date2=20.12.2020&typeRange=2&typeRangeCompare=4&countDates=31&regionsIndexes=1&competitorsIds=&folderId=0&groupId=0&tags=&dynamic=&minPos=&maxPos=&onlyExistsFirstDate=0',
	],
	// список URL из личного кабинета Топвизора для списка 2
	'topvisor_google_b' = [
		'https://topvisor.com/project/dynamics/2535875/#historyView=1&templateDateRange=data_range_1y&dates=&date1=20.12.2019&date2=20.12.2020&typeRange=2&typeRangeCompare=4&countDates=31&regionsIndexes=1&competitorsIds=&folderId=257868&groupId=13220597&tags=&dynamic=&minPos=&maxPos=&onlyExistsFirstDate=0',
	],
	// список URL из личного кабинета Топвизора для списка 3
	'topvisor_yandex_a' = [
		'https://topvisor.com/project/dynamics/2535875/#historyView=1&templateDateRange=data_range_1y&dates=&date1=20.12.2019&date2=20.12.2020&typeRange=2&typeRangeCompare=4&countDates=31&regionsIndexes=1&competitorsIds=&folderId=0&groupId=0&tags=&dynamic=&minPos=&maxPos=&onlyExistsFirstDate=0',
		'https://topvisor.com/project/dynamics/2535875/#historyView=1&templateDateRange=data_range_1y&dates=&date1=20.12.2019&date2=20.12.2020&typeRange=2&typeRangeCompare=4&countDates=31&regionsIndexes=1&competitorsIds=&folderId=0&groupId=0&tags=&dynamic=&minPos=&maxPos=&onlyExistsFirstDate=0',
		'https://topvisor.com/project/dynamics/2535875/#historyView=1&templateDateRange=data_range_1y&dates=&date1=20.12.2019&date2=20.12.2020&typeRange=2&typeRangeCompare=4&countDates=31&regionsIndexes=1&competitorsIds=&folderId=0&groupId=0&tags=&dynamic=&minPos=&maxPos=&onlyExistsFirstDate=0',
	],
	// список URL из личного кабинета Топвизора для списка 4
	'topvisor_yandex_b' = [
		'https://topvisor.com/project/dynamics/2535875/#historyView=1&templateDateRange=data_range_1y&dates=&date1=20.12.2019&date2=20.12.2020&typeRange=2&typeRangeCompare=4&countDates=31&regionsIndexes=1&competitorsIds=&folderId=257868&groupId=13220597&tags=&dynamic=&minPos=&maxPos=&onlyExistsFirstDate=0',
	],

	// фильтры A яндекс и google
	'metrika_yandex_a' => "ym:s:searchEngineRootName=@'Яндекс'",
	'metrika_google_a' => "ym:s:searchEngineRootName=@'Google'",

	// фильтры B яндекс и google
	'metrika_yandex_a' => "ym:s:searchEngineRootName=@'Яндекс'",
	'metrika_google_b' => "ym:s:searchEngineRootName=@'Google'",
];
