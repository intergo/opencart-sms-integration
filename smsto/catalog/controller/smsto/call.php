<?php
namespace Opencart\Catalog\Controller\Extension\Smsto\Smsto;

class Call extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$api_key = $this->config->get('module_smsto_api_key');
		$method = strtoupper($_GET['_method']);
		$url = $_GET['_url'];
		$payload = $_GET['payload'] ?? json_encode([]);
		
		$this->load->model('extension/smsto/smsto/call');
		$response = $this->model_extension_smsto_smsto_call->callSmsto($api_key, $method, $url, $payload);
		$this->response->setOutput($response);
	}
}
