<?php 

/**
*
*
*/

require_once __DIR__ . "/../../src/vSEMstat.php";

use vSEMstat\Services\TopvisorService;
use vSEMstat\Services\MetrikaService;


// чиатем конфигурацию
$config = include('config.php');

$db = dbConnection($config['mysql']);


// создем сервис топвизора
$topvisor = new TopvisorService;

// основные параметры
$topvisor->setUserId($config['topvisor']['user_id']);
$topvisor->setApiKey(['topvisor']['api_key']);
$topvisor->setProjectID(['topvisor']['project_id']);


// создаем сервис метрики
$metrika = new MetrikaService;
$metrika->setCounterId($config['metrika']['counter_id']);

// берем ключ из файла с ключом 
$metrika->setTokenFromConfig(__DIR__ . '/../../oauth/oauth_key.key');

// или задаем токен доступа из настроек
// $metrika->setToken($config['metrika']['token']);

// список импортируемых метрик
$metrika->setMetrics('ym:s:visits,ym:s:users');
//  список импортируемых измерений
$metrika->setDemensions('ym:s:searchEngineRootName');

$dates = $config['dates'];

$TopvisorGoogleA = $config['topvisor_google_a'];
$TopvisorGoogleB = $config['topvisor_google_b'];
$TopvisorYandexA = $config['topvisor_yandex_a'];
$TopvisorYnadexB = $config['topvisor_yandex_b'];

$metrikaFilterYnadexA = $config['metrika_yandex_a'];
$metrikaFilterGoogleA = $config['metrika_google_a'];

$metrikaFilterYnadexB = $config['metrika_yandex_b'];
$metrikaFilterGoogleB = $config['metrika_google_b'];

// создание списка дат за период
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

