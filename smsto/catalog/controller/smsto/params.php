<?php
namespace Opencart\Catalog\Controller\Extension\Smsto\Smsto;
class Params extends \Opencart\System\Engine\Controller {
	public function index() : void {
		$data = [
			"success" => true,
			"message" => null,
			"messages" => null,
			"data" => [
				"show_reports" => $this->config->get('module_smsto_show_reports'),
				"show_people" => $this->config->get('module_smsto_show_people')
			]
		];
		$this->response->setOutput(json_encode($data));
	}
}
