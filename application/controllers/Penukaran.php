<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penukaran extends MY_Controller {

	public function index(){
		$this->load->view('penukaran/index');
	}

	public function refresh_data_payload(){
		$arr = [];
		$data_payload = $this->sitemodel->view('register', 'data_payload');
		foreach ($data_payload as $row) {
			$arr[] = unserialize($row->data_payload);
		}
		return $arr;
	}

	public function find(){
		$response;
		/*** Check POST or GET ***/
		if ( !$_POST ){$this->response['msg'] = "Invalid parameters.";echo json_encode($this->response);exit;}
		/*** Params ***/
		/*** Required Area ***/
		$key = $this->input->post("key");
		/*** Optional Area ***/
		/*** Validate Area ***/
		if ( empty($key) ){$this->response['msg'] = "Invalid parameter.";echo json_encode($this->response);exit;}
		/*** Accessing DB Area ***/
		//$check = $this->sitemodel->view('register', '*', ['ticket_ids'=>$key]);
		$data_check = $this->refresh_data_payload();
		// echo json_encode($data_check);die;
		for ($a = 0; $a < count($data_check); $a++) {
			$length = 0;

			if ( isset($data_check[$a]['ticket_ids']) && is_array($data_check[$a]['ticket_ids']) ) {
				$length = count($data_check[$a]['ticket_ids']);
			}

			if ($length > 0) {
				for ($b = 0; $b < count($data_check[$a]['ticket_ids']); $b++) { 
					if ( $key == $data_check[$a]['ticket_ids'][$b] ) {
						$this->response['type'] = 'done';
						$this->response['msg'] = $data_check[$a];
						echo json_encode($this->response);
						die;
					}
				}
			}
		}
		$this->response['msg'] = "No data found.";
		echo json_encode($this->response);
		exit;
	}

	

	public function save_penukaran()
	{
		// echo json_encode($this->input->post());die;
		/*** Check POST or GET ***/
		if ( !$_POST ){$this->response['msg'] = "Invalid parameters.";echo json_encode($this->response);exit;}

		$ticket_ids 		= $this->input->post('ticket_ids');
		$category		    = $this->input->post('category');
		$ticket_count		= $this->input->post('ticket_count');
		$name 		 	    = $this->input->post('name');
		$email 				= $this->input->post('email');
		$phone 				= $this->input->post('phone');
		$foto_penukar 		= $_FILES['foto_penukar'];

		$data = [
			'ticket_ids'		 => $ticket_ids,
			'category'  		 => $category,
			'ticket_count'		 => $ticket_count,
			'name'				 => $name,
			'email'				 => $email,
			'phone'				 => $phone,
			'penukaran_datetime' => date('Y-m-d H:i:s'),
		];

		// echo json_encode($data);die;

		if ( $foto_penukar['name'] != '' ) {
        	$temp_name = $foto_penukar['name'];
        	$target_dir = 'assets/public/registran/'.$name.'/';

			$ext = explode('.', $temp_name);
			$end = strtolower(end($ext));

			if (!file_exists($target_dir)) {
				mkdir($target_dir, 0777, true);
			}

			$attachment_name = $target_dir."foto_penukar.".$end;

			move_uploaded_file($foto_penukar['tmp_name'], $attachment_name);
			$data['foto_penukar'] = $attachment_name;
        }

		$check_name = $this->sitemodel->view('tr_penukaran', '*', ['email'=>$email]);
		if ($check_name) {$this->response['msg'] = "Anda telah melakukan penukaran tiket pada pukul ".date('d/m/Y H:i:s', strtotime($check_name[0]->penukaran_datetime));echo json_encode($this->response);exit;}

		$penukaran_id = $this->sitemodel->insert('tr_penukaran', $data);

		/*** Result Area ***/
		$this->response['type'] = 'done';
		$this->response['id'] = $penukaran_id;
		echo json_encode($this->response);
		exit;
	}

}
