<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penukaran extends MY_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->select 		= '*';
		$this->from   		= 'view_penukaran';
		$this->order_by   	= ['penukaran_id'=>'DESC'];
		$this->order 		= ['penukaran_id', 'ticket_ids', 'name', 'email', 'phone', 'category', 'ticket_count', 'foto_penukar', 'penukaran_datetime'];
		$this->search 		= ['penukaran_id', 'ticket_ids', 'name', 'email', 'phone', 'category', 'ticket_count', 'foto_penukar', 'penukaran_datetime'];

	}

	public function index(){

		if (!$this->hasLogin()) {
			redirect('admin/site/login');
		}

		$this->fragment['js'] = [ 
			base_url('assets/js/pages/admin/penukaran.js') 
		];

		$this->fragment['pagename'] = 'admin/pages/penukaran.php';
		$this->load->view('admin/layout/main-site', $this->fragment);
	}

	public function view()
	{
		$data = array();
		$res = $this->sitemodel->get_datatable($this->select, $this->from, $this->order_by, $this->search, $this->order);
		$q = $this->db->last_query();
		$a = 1;

		foreach ($res as $row) {
			$col = array();
			$col[] = $row->ticket_ids;
			$col[] = $row->name;
			$col[] = $row->email;
			$col[] = $row->phone;
			$col[] = $row->category;
			$col[] = $row->ticket_count;
			$col[] = '<img src="'.base_url() .$row->foto_penukar.'" style="width:150px; height=150px;">';
			$col[] = ($row->penukaran_datetime ? date('d/m/Y H:i:s', strtotime($row->penukaran_datetime)) : '-' );
			$data[] = $col;
			$a++;
		}
		$output = array(
			"draw" 				=> $_POST['draw'],
			"recordsTotal" 		=> $this->sitemodel->get_datatable_count_all($this->from),
			"recordsFiltered" 	=> $this->sitemodel->get_datatable_count_filtered($this->select, $this->from, $this->order_by, $this->search, $this->order),
			"data" 				=> $data,
			"q"					=> $q

		);
		echo json_encode($output);
		exit;
	}

	
}