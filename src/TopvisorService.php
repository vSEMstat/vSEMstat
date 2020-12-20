<?php 

namespace vSEMstat\Services;

/**
*
*
*/

class TopvisorService extends BaseService
{
	protected $user;
	protected $apiKey;
	protected $projectID;

	public function __construct($user = null, $apiKey = null, $projectID = null)
	{
		$this->user = $user;
		$this->apiKey = $apiKey;
		$this->projectID = $projectID;

		parent::__construct([
			'url' => 'https://api.topvisor.com/v2/json/get/positions_2/summary',
			'header' => [],
			'post' => [],
		]);
	}

	public function setUserId($userId)
	{
		$this->userId = $userId;
	}

	public function setApiKey($apiKey)
	{
		$this->apiKey = $apiKey;
	}

	public function setProjectID($projectID)
	{
		$this->projectID = $projectID;
	}

	public function request(array $post = null)
	{

		if(empty($post['date_start']))
			$post['date_start'] = date('Y-m-d', strtotime('today'));

		if(empty($post['date_end']))
			$post['date_end'] = date('Y-m-d', strtotime('today'));

		$post['dates'] = [
			$post['date_start'], 
			$post['date_end']
		];

		unset($post['date_start'], $post['date_end']);

		$this->post = array_merge($post, [
			'project_id' => $this->projectID,
		]);

		$this->buildHeader();

		return $this->getDataFromSource();
	}

	private function buildHeader()
	{
		$this->header = [
			'Content-type: application/json', 
			'User-Id: ' . $this->userId, 
			'Authorization: bearer ' . $this->apiKey
		];
	}
}
