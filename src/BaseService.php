<?php 

namespace vSEMstat\Services;

/**
*
*
*/

class BaseService
{
	protected $url;
	protected $header;
	protected $post;

	public function __construct(array $params = null)
	{
		$this->url = '';
		$this->header = '';
		$this->post = [];

		if(is_array($params)){

			if(!empty($params['url'])){
				$this->url = $params['url'];
			}

			if(!empty($params['header'])){
				$this->header = $params['header'];
			}

			if(!empty($params['post'])){
				$this->post = $params['post'];
			}
		}
	}

	protected function getDataFromSource() : ?array
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->post));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$object = curl_exec($ch);
		curl_close($ch);
		$object = json_decode($object, true);

		return $object;
	}

	public function getTest()
	{
		return 'test';
	}
}
