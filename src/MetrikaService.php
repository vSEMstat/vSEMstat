<?php 

namespace vSEMstat\Services;

/**
*
*
*/
class MetrikaService extends BaseService
{
	protected $token;
	protected $counterId;
	protected $metrics;
	protected $dimensions;
	protected $filters;

	public function __construct($token = null, $counterId= null)
	{
		$this->token = $token;
		$this->counterId = $counterId;

		parent::__construct([
			'url' => $this->getInitUrl(),
			'header' => [],
			'post' => [],
		]);
	}

	private function getInitUrl()
	{
		return 'https://api-metrika.yandex.ru/stat/v1/data';
	}

	public function setToken($token)
	{
		$this->token = $token;
	}

	public function setTokenFromConfig($filename)
	{
		if(file_exists($filename))
			$this->token = file_get_contents($filename);
		else
			throw new \Exception("File $filename not found.", 1);
			
	}

	public function setCounterId($counterId)
	{
		$this->counterId = $counterId;
	}

	public function setMetrics($metrics)
	{
		$this->metrics = $metrics;
	}

	public function setDemensions($dimensions)
	{
		$this->dimensions = $dimensions;
	}

	public function setFilters($filters)
	{
		$this->filters = $filters;
	}

	
	public function request(array $post = null)
	{

		if(empty($post['date_start']))
			$post['date_start'] = date('Y-m-d', strtotime('today'));

		if(empty($post['date_end']))
			$post['date_end'] = date('Y-m-d', strtotime('today'));

		$dates = [
			$post['date_start'], 
			$post['date_end']
		];

		$urlParams = "?ids={$this->counterId}&metrics={$this->metrics}&dimensions={$this->dimensions}&filters={$this->filters}&date1={$dates[0]}&date2={$dates[1]}";

		$this->url = $this->getInitUrl();
		$this->url .= $urlParams;

		$this->buildHeader();

		return $this->getDataFromSource();
	}

	private function buildHeader()
	{
		$this->header = [
			'Content-type: application/x-yametrika+json', 
			'Authorization: OAuth' . $this->token,
		];
	}


}
