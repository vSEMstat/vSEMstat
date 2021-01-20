<?php 

/**
*
*
*/

require_once __DIR__ . "/../../src/vSEMstat.php";

use vSEMstat\Services\TopvisorService;
use vSEMstat\Services\MetrikaService;


// чиатем конфигурацию базы данных
$dbConfig = include('db.config.php');
$db = dbConnection($dbConfig);


// создем сервис топвизора
$topvisor = new TopvisorService;

// основные параметры
$topvisor->setUserId('');
$topvisor->setApiKey('');
$topvisor->setProjectID('');


// создаем сервис метрики
$metrika = new MetrikaService;
$metrika->setCounterId('');

// задаем токен доступа
$metrika->setToken('');

// или берем ключ из файла с ключом 
// $metrika->setTokenFromConfig(__DIR__ . '/../../oauth/oauth_key.key');

$metrika->setMetrics("ym:s:visits,ym:s:users");
$metrika->setDemensions("ym:s:searchEngineRootName");

$dates = [
	'2020-12-07',
	date('Y-m-d', strtotime('today')),
];

$TopvisorGoogleA = [
	'https://topvisor.com/project/dynamics/2535875/#historyView=1&templateDateRange=data_range_1y&dates=&date1=20.12.2019&date2=20.12.2020&typeRange=2&typeRangeCompare=4&countDates=31&regionsIndexes=1&competitorsIds=&folderId=0&groupId=0&tags=&dynamic=&minPos=&maxPos=&onlyExistsFirstDate=0',
	'https://topvisor.com/project/dynamics/2535875/#historyView=1&templateDateRange=data_range_1y&dates=&date1=20.12.2019&date2=20.12.2020&typeRange=2&typeRangeCompare=4&countDates=31&regionsIndexes=1&competitorsIds=&folderId=0&groupId=0&tags=&dynamic=&minPos=&maxPos=&onlyExistsFirstDate=0',
	'https://topvisor.com/project/dynamics/2535875/#historyView=1&templateDateRange=data_range_1y&dates=&date1=20.12.2019&date2=20.12.2020&typeRange=2&typeRangeCompare=4&countDates=31&regionsIndexes=1&competitorsIds=&folderId=0&groupId=0&tags=&dynamic=&minPos=&maxPos=&onlyExistsFirstDate=0',
];

$TopvisorGoogleB = [
	'https://topvisor.com/project/dynamics/2535875/#historyView=1&templateDateRange=data_range_1y&dates=&date1=20.12.2019&date2=20.12.2020&typeRange=2&typeRangeCompare=4&countDates=31&regionsIndexes=1&competitorsIds=&folderId=257868&groupId=13220597&tags=&dynamic=&minPos=&maxPos=&onlyExistsFirstDate=0',
];

$TopvisorYandexA = [
	'https://topvisor.com/project/dynamics/2535875/#historyView=1&templateDateRange=data_range_1y&dates=&date1=20.12.2019&date2=20.12.2020&typeRange=2&typeRangeCompare=4&countDates=31&regionsIndexes=1&competitorsIds=&folderId=0&groupId=0&tags=&dynamic=&minPos=&maxPos=&onlyExistsFirstDate=0',
	'https://topvisor.com/project/dynamics/2535875/#historyView=1&templateDateRange=data_range_1y&dates=&date1=20.12.2019&date2=20.12.2020&typeRange=2&typeRangeCompare=4&countDates=31&regionsIndexes=1&competitorsIds=&folderId=0&groupId=0&tags=&dynamic=&minPos=&maxPos=&onlyExistsFirstDate=0',
	'https://topvisor.com/project/dynamics/2535875/#historyView=1&templateDateRange=data_range_1y&dates=&date1=20.12.2019&date2=20.12.2020&typeRange=2&typeRangeCompare=4&countDates=31&regionsIndexes=1&competitorsIds=&folderId=0&groupId=0&tags=&dynamic=&minPos=&maxPos=&onlyExistsFirstDate=0',
];

$TopvisorYnadexB = [
	'https://topvisor.com/project/dynamics/2535875/#historyView=1&templateDateRange=data_range_1y&dates=&date1=20.12.2019&date2=20.12.2020&typeRange=2&typeRangeCompare=4&countDates=31&regionsIndexes=1&competitorsIds=&folderId=257868&groupId=13220597&tags=&dynamic=&minPos=&maxPos=&onlyExistsFirstDate=0',
];

$metrikaFilterYnadexA = "ym:s:searchEngineRootName=@'Яндекс'";
$metrikaFilterGoogleA = "ym:s:searchEngineRootName=@'Google'";

$metrikaFilterYnadexB = "ym:s:searchEngineRootName=@'Яндекс'";
$metrikaFilterGoogleB = "ym:s:searchEngineRootName=@'Google'";


$period = new DatePeriod(
     new DateTime($dates[0]),
     new DateInterval('P1D'),
     new DateTime($dates[1])
);

echo "Период обработки с $dates[0] по $dates[1]\n";

$count = 0;
foreach ($period as $day) {


	$toDatabase = [];

	$strDate = $day->format('Y-m-d');

	echo "Обработка $strDate\n";

	$toDatabase[$strDate]['GoogleA'] = getAvgAndVisibilities($TopvisorGoogleA, $day, $topvisor);
	$toDatabase[$strDate]['GoogleB'] = getAvgAndVisibilities($TopvisorGoogleB, $day, $topvisor);

	$toDatabase[$strDate]['YandexA'] = getAvgAndVisibilities($TopvisorYandexA, $day, $topvisor);
	$toDatabase[$strDate]['YandexB'] = getAvgAndVisibilities($TopvisorYnadexB, $day, $topvisor);

	$toDatabase[$strDate]['metrikaYandexA'] = getVisitsAndUsers($metrikaFilterYnadexA, $day, $metrika);
	$toDatabase[$strDate]['metrikaGoogleA'] = getVisitsAndUsers($metrikaFilterGoogleA, $day, $metrika);

	$toDatabase[$strDate]['metrikaYandexB'] = getVisitsAndUsers($metrikaFilterYnadexB, $day, $metrika);
	$toDatabase[$strDate]['metrikaGoogleB'] = getVisitsAndUsers($metrikaFilterGoogleB, $day, $metrika);

	// сохраняем строку в базу данных
	saveToDatabase('report', $toDatabase, $db);

	$count++;
}

echo "Завершено. Обработанно дней: $count\n";


// ##########




function parseUrl(string $url)
{
	$data = parse_url($url);

	preg_match_all('/\d+/', $data['path'], $matches);
	$projectID = $matches[0][0];


	parse_str($data['fragment'], $data1);

	$result = [
		'region_index' => $data1['regionsIndexes'],
		'project_id' => $projectID,
	];

	$groupId = !empty($data1['groupId']) ? $data1['groupId'] : 0;
	$folderId = !empty($data1['folderId']) ? $data1['folderId'] : 0;

	if ($groupId != 0) {

		$result = array_merge($result, [
			'filters' => [
				[
					'name' => 'group_id',
					'operator' => 'EQUALS',
					'values' => [$groupId],
				]
			],
		]);
	}
	else if ($folderId != 0){
		$result = array_merge($result, [
			'filters' => [
				[
					'name' => 'group_folder_id',
					'operator' => 'EQUALS',
					'values' => [$folderId],
				]
			],
		]);
	}

	return $result;
}



function getAvgAndVisibilities(array $urls, $day, $service) : array
{
	$avg = 0;
	$visibilities = 0;
	$count = count($urls);

	foreach ($urls as $url) {

		$parseData = parseUrl($url);
		
		$data = $service->request(array_merge($parseData, [
			'date_start' => $day->format('Y-m-d'),
			'date_end' => $day->format('Y-m-d'),
		]));

		$avg += $data['result']['avgs'][1];
		$visibilities += $data['result']['visibilities'][1];

	}

	return [
		'avg' => $avg / $count,
		'visibilities' => $visibilities / $count,
	];
}

function getVisitsAndUsers($filter, $day, $service) : array
{
	static $count = 0;

	// применяем фильтрацию
	if(!empty($filter))
		$service->setFilters($filter);

	// выборка по текущему дню
	$data = $service->request([
		'date_start' => $day->format('Y-m-d'),
		'date_end' => $day->format('Y-m-d'),
	]);

	// собираем наименования метрик из ответа 
	$metrics = [];
	if(!empty($data['query']) && !empty($data['query']['metrics'])){
		
		foreach ($data['query']['metrics'] as $key => $metrika) {

			$metrika = preg_replace('/^.*\:(\w+)$/', '$1', $metrika);
			$metrics[$metrika] = $key;
		}
	}

	// формируем структурированный результат
	$result = [];
	if(!empty($data['data'])){

		foreach ($data['data'] as $item) {

			if(!empty($item['dimensions'][0]['name'])) {

				foreach ($metrics as $name => $index) {

					if(!empty($item['metrics']))
						// запись вида 
						// 		$result['Яндекс']['visits'] = количество
						// 		$result['Яндекс']['users'] = количество
						// $result[$item['dimensions'][0]['name']][$name] = $item['metrics'][$index];

						// более простая запись вида
						// 		$result['visits'] = количество
						// 		$result['users'] = количество
						$result[$name] = $item['metrics'][$index];
				}
			}
		}
	}

	$count++;

	return $result;
}

function saveToDatabase(string $tableName, array $row, $handler)
{
	static $firstRun = true;

	if($firstRun){
		dbCreateTable($tableName, $row, $handler);
	}

	$id = dbInsert($tableName, $row, $handler);

	$firstRun = false;
	return $id;
}

function dbConnection($config)
{
	$host = $config['host'];
	$database = $config['database'];
	
	try {

		$handler = new PDO("mysql:host=$host;dbname=$database", $config['username'], $config['password']);

	} catch (PDOException $e) {
	    print "Error!: " . $e->getMessage();
	    die();
	}

	return $handler;
}

function dbCreateTable(string $tableName, array $row, $handler)
{
	$date = array_key_first($row);
	$data = $row[$date];

	$str = 'date DATETIME NOT NULL, ';

	foreach ($data as $key => $metrics) {
		
		foreach ($metrics as $metric => $value) {
			
			// генерация имени поля в таблице
			$columnName = $key . $metric;


			$str .= "$columnName ";

			if(is_float($value))
				$str .= 'FLOAT NOT NULL,';
			else if(is_integer($value))
				$str .= 'INT NOT NULL,';
			else 
				$str .= 'VARCHAR(255) NOT NULL,';
		}
	}

	// отрезать последнюю запятую
	$str = rtrim($str, ", ");
	$sql = "CREATE TABLE IF NOT EXISTS $tableName($str)";

	try {

		$handler->exec($sql);

	} catch (PDOException $e) {
	    print "Error!: " . $e->getMessage();
	    die();
	}
}

function dbInsert(string $tableName, array $row, $handler)
{
	$date = array_key_first($row);
	$data = $row[$date];

	$fields = "date,";
	$values = "'$date',";

	foreach ($data as $key => $metrics) {
		
		foreach ($metrics as $metric => $value) {
			
			// генерация имени поля в таблице
			$columnName = $key . $metric;

			$fields .= "$columnName,";
			$values .= "'$value',";
		}
	}
	// отрезать последнюю запятую
	$fields = rtrim($fields, ", ");
	$values = rtrim($values, ", ");
		
	try {

		$sql = "INSERT INTO $tableName ($fields) VALUES ($values)";
		$handler->exec($sql);

	} catch (PDOException $e) {
	    print "Error!: " . $e->getMessage();
	    die();
	}

	return $handler;
}

