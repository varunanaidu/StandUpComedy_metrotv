<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Registration extends MY_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->select 		= '*';
		$this->from   		= 'view_register';
		$this->order_by   	= ['status'=>'DESC'];
		$this->order 		= ['name', 'email', 'phone', 'category_item', 'qty_item', 'status','created_date'];
		$this->search 		= ['name', 'email', 'phone', 'category_item', 'qty_item', 'status','created_date'];

	}

	public function index(){

		if (!$this->hasLogin()) {
			redirect('admin/site/login');
		}

		$this->fragment['js'] = [ 
			base_url('assets/js/pages/admin/registration.js') 
		];

		$this->fragment['pagename'] = 'admin/pages/registration.php';
		$this->load->view('admin/layout/main-site', $this->fragment);
	}

	public function view()
	{
		$filter = false;
		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');

		if ($start_date != '' && $end_date != '') {
			$filter = [
				'start_date' => $start_date,
				'end_date'   => $end_date,
			];
		}

		$data = array();
		$res = $this->sitemodel->get_datatable($this->select, $this->from, $this->order_by, $this->search, $this->order, $filter);
		$q = $this->db->last_query();
		$a = 1;

		foreach ($res as $row) {
			$col = array();
			// $col[] = '<button class="btn btn-danger btn-delete" data-id="'.$row->registration_id.'"><i class="fas fa-minus-circle"></i></button>';
			//$col[] = '<button class="btn btn-success btn-verif" data-id="'.$row->ticket_ids.'"><i class="fas fa-check"></i></button>';
			$col[] = $row->name;
			$col[] = $row->email;
			$col[] = $row->phone;
			$col[] = $row->category_item;
			$col[] = $row->qty_item;
			$col[] = ($row->status == 1 ? 'Sudah Bayar': ($row->status == 2 ? 'Belum Bayar' : 'Belum Bayar'));
			$col[] = $row->created_date;
			$data[] = $col;
			$a++;
		}
		$output = array(
			"draw" 				=> $_POST['draw'],
			"recordsTotal" 		=> $this->sitemodel->get_datatable_count_all($this->from),
			"recordsFiltered" 	=> $this->sitemodel->get_datatable_count_filtered($this->select, $this->from, $this->order_by, $this->search, $this->order, $filter),
			"data" 				=> $data,
			"q"					=> $q

		);
		echo json_encode($output);
		exit;
	}
}
