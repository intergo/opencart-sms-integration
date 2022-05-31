<?php

namespace Opencart\Admin\Controller\Extension\Smsto\Smsto;

class Send extends \Opencart\System\Engine\Controller
{
	public function index(): void
	{
		$this->load->language('extension/smsto/module/settings');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];
		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/smsto/smsto/send', 'user_token=' . $this->session->data['user_token'])
		];

		$data['vue_url'] = $this->url->link('extension/smsto/smsto/send|vue', 'user_token=' . $this->session->data['user_token']);

		$data['user_token'] = $this->session->data['user_token'];
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->document->setTitle($this->language->get('heading_title'));
		$ch = curl_init();
		ob_start();
		curl_setopt($ch, CURLOPT_URL, 'https://integration.sms.to/component_bulk_sms/manifest.json');
		$response = curl_exec($ch);
		curl_close($ch);
		$response = ob_get_clean();
		$manifest = json_decode($response, true);
		
		$data['script_file'] = $manifest['src/main.ts']['file'];
		$data['css_file'] = $manifest['src/main.ts']['css'][0];
		$data['route_params'] =  $this->url->link('extension/smsto/smsto/send|params', 'user_token=' . $this->session->data['user_token']);
		$data['route_smsto'] =  $this->url->link('extension/smsto/smsto/send|callsmsto', 'user_token=' . $this->session->data['user_token']);

		$this->response->setOutput($this->load->view('extension/smsto/smsto/send', $data));
	}

	public function vue(): void
	{
		$this->document->setTitle($this->language->get('heading_title'));
		$ch = curl_init();
		ob_start();
		curl_setopt($ch, CURLOPT_URL, 'https://integration.sms.to/component_bulk_sms/manifest.json');
		$response = curl_exec($ch);
		curl_close($ch);
		$response = ob_get_clean();
		$manifest = json_decode($response, true);
		
		$data['script_file'] = $manifest['src/main.ts']['file'];
		$data['css_file'] = $manifest['src/main.ts']['css'][0];
		$data['route_params'] =  $this->url->link('extension/smsto/smsto/send|params', 'user_token=' . $this->session->data['user_token']);
		$data['route_smsto'] =  $this->url->link('extension/smsto/smsto/send|callsmsto', 'user_token=' . $this->session->data['user_token']);

		$this->response->setOutput($this->load->view('extension/smsto/smsto/vue'), $data);
	}

}
