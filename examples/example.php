<?php 

/**
*
*
*/

require_once __DIR__ . "/../library.php";

use vSEMstat\Services\TopvisorService;
use vSEMstat\Services\MetrikaService;


$topvisor = new TopvisorService;

$topvisor->setUserId('145817');
$topvisor->setApiKey('e1371c99815280ac79ab');
$topvisor->setProjectID('1879368');

$topvisorData = $topvisor->request([
	'date_start' => '2020-12-15',
	'region_index' => 1,
	'show_visibility' => 1,
]);

var_dump($topvisorData);

$metrika = new MetrikaService;
$metrika->setCounterId('123456');

$metrika->setToken('123');
$metrika->setTokenFromConfig(__DIR__ . '/oauth_key.key');

$metrika->setMetrics("ym:s:visits");
$metrika->setDemensions("ym:s:searchEngineRootName");
$metrika->setFilters("ym:s:searchEngineRootName=='Google' AND ym:s:startURLPathFull=~'^/$'");

$metrikaData = $metrika->request([
	'date_start' => '2020-12-15',
]);

var_dump($metrikaData);



