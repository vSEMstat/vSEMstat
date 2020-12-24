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
	protected $regionsIndexes;
	protected $folderId;
	protected $groupId;
	protected $filters;

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

	public function setFilter($filters)
	{
		$this->filters = $filters;
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

		if(!empty($post['project_id'])){
			$this->projectID = $post['project_id'];
		}

		$this->post = array_merge($post, [
			'project_id' => $this->projectID,
			'show_visibility' => 1,
			'group_folder_id_depth' => 1,
			// 'show_dynamics' => 1,
			'show_avg' => 1,
		]);

		if(!empty($this->filters)){

			$this->post = array_merge($this->post, [
				'filters' => [
					[
						'name' => 'group_folder_id',
						'operator' => 'EQUALS',
						'values' => $this->filters,
					]
				],
			]);
		}

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
