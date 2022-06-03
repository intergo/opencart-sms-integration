<?php

namespace Opencart\Admin\Controller\Extension\Smsto\Module;

class Settings extends \Opencart\System\Engine\Controller
{
	public function index(): void
	{
		$this->load->language('extension/smsto/module/settings');

		$this->document->setTitle($this->language->get('smsto_heading_title'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('smsto_heading_title'),
			'href' => $this->url->link('extension/smsto/module/settings', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/smsto/module/settings|save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');

		$data['module_smsto_status'] = $this->config->get('module_smsto_status');
		$data['module_smsto_api_key'] = $this->config->get('module_smsto_api_key');
		$data['module_smsto_sender_id'] = $this->config->get('module_smsto_sender_id');
		$data['module_smsto_phone'] = $this->config->get('module_smsto_phone');
		$data['module_smsto_show_reports'] = $this->config->get('module_smsto_show_reports');
		$data['module_smsto_show_people'] = $this->config->get('module_smsto_show_people');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/smsto/module/settings', $data));
	}

	public function save(): void
	{
		$this->load->language('extension/smsto/module/settings');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/smsto/module/settings')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('module_smsto', $this->request->post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function install(): void
	{
		// Register events
		$this->load->model('setting/event');
		$this->model_setting_event->addEvent('smsto_order_alert', 'SMSto order alert', 'catalog/model/checkout/order/addHistory/before', 'extension/smsto/smsto/order', true, 1);

		// Fix permissions
		$this->load->model('user/user_group');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/smsto/smsto/send');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/smsto/smsto/send');

		// Add menu in left column
		$path = __DIR__ . '/../../../../../admin/controller/common/column_left.php';
		$search = 'return $this->load->view(\'common/column_left\', $data);';
		$replace = '
		// SMSto
		$data[\'menus\'][] = [
			\'id\'       => \'menu-smsto\',
			\'icon\'	   => \'fas fa-comment\',
			\'name\'	   => \'SMSto\',
			\'href\'     => $this->url->link(\'extension/smsto/smsto/send\', \'user_token=\' . $this->session->data[\'user_token\']),
			\'children\' => []
		];

		return $this->load->view(\'common/column_left\', $data);
		';
		if (file_exists($path)) {
			$this->replaceInFile($search, $replace, $path);
		}

		// Add button send from customer.twig
		$path = __DIR__ . '/../../../../../admin/view/template/customer/customer.twig';
		$search = '<div class="float-end">';
		$replace = '
		<div class="float-end">
        	<button type="button" data-bs-toggle="tooltip" title="SMS" id="button-smsto" class="btn btn-light"><i class="fas fa-comment"></i></button>
		';
		if (file_exists($path)) {
			$this->replaceInFile($search, $replace, $path);
		}

		// Add js send from customer.twig
		$path = __DIR__ . '/../../../../../admin/view/template/customer/customer.twig';
		$search = '//--></script>';
		$replace = '
		$(\'#button-smsto\').on(\'click\', function() {
  
			url = \'index.php?route=extension/smsto/smsto/send&user_token={{ user_token }}\';
		
			let ids = \'\';
			var selected = $(\'input[name*=\\\'selected\\\']\');    
			for(var i=0;i<selected.length;i++)
			{
				if(selected[i].checked===true)
				{
					ids = ids + selected[i].value + \',\';
				}
			}
			ids = ids.slice(0, -1);
			if (ids == \'\') {
			  return;
			}
			url += \'&customer_ids=\' + encodeURIComponent(ids);
		
			$(location).attr(\'href\', url);
		});
		
		//--></script>
		';
		if (file_exists($path)) {
			$this->replaceInFile($search, $replace, $path);
		}

	}

	public function uninstall(): void
	{
		// remove events
		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('smsto_order_alert');

		// Fix permissions
		$this->load->model('user/user_group');
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'extension/smsto/smsto/send');
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'extension/smsto/smsto/send');

		// Remove menu in left column
		$path = __DIR__ . '/../../../../../admin/controller/common/column_left.php';
		$replace = 'return $this->load->view(\'common/column_left\', $data);';
		$search = '
		// SMSto
		$data[\'menus\'][] = [
			\'id\'       => \'menu-smsto\',
			\'icon\'	   => \'fas fa-comment\',
			\'name\'	   => \'SMSto\',
			\'href\'     => $this->url->link(\'extension/smsto/smsto/send\', \'user_token=\' . $this->session->data[\'user_token\']),
			\'children\' => []
		];

		return $this->load->view(\'common/column_left\', $data);
		';
		if (file_exists($path)) {
			$this->replaceInFile($search, $replace, $path);
		}

		// Remove button send from customer.twig
		$path = __DIR__ . '/../../../../../admin/view/template/customer/customer.twig';
		$replace = '<div class="float-end">';
		$search = '
		<div class="float-end">
        	<button type="button" data-bs-toggle="tooltip" title="SMS" id="button-smsto" class="btn btn-light"><i class="fas fa-comment"></i></button>
		';
		if (file_exists($path)) {
			$this->replaceInFile($search, $replace, $path);
		}

		// Remove js send from customer.twig 
		$path = __DIR__ . '/../../../../../admin/view/template/customer/customer.twig';
		$replace = '//--></script>';
		$search = '
		$(\'#button-smsto\').on(\'click\', function() {
  
			url = \'index.php?route=extension/smsto/smsto/send&user_token={{ user_token }}\';
		
			let ids = \'\';
			var selected = $(\'input[name*=\\\'selected\\\']\');    
			for(var i=0;i<selected.length;i++)
			{
				if(selected[i].checked===true)
				{
					ids = ids + selected[i].value + \',\';
				}
			}
			ids = ids.slice(0, -1);
			if (ids == \'\') {
			  return;
			}
			url += \'&customer_ids=\' + encodeURIComponent(ids);
		
			$(location).attr(\'href\', url);
		});
		
		//--></script>
		';
		if (file_exists($path)) {
			$this->replaceInFile($search, $replace, $path);
		}
	}

	/**
	 * Replace a given string within a given file.
	 *
	 * @param  string  $search
	 * @param  string  $replace
	 * @param  string  $path
	 * @return void
	 */
	protected function replaceInFile($search, $replace, $path)
	{
		file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
	}
}
