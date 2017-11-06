<?php

class InfoAntrian extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		//change db
		$this->load->model('mMainModul');
		$this->mMainModul->ChangeDB(trim($this->session->userdata('gDB')));
		//eof change db
	}
	
	function index()
	{
		if (trim($this->session->userdata('gUserLevel')) <> '')
		{
			$this->load->view('vinfoantrian');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function GridDetil()
	{
		$xTgl = trim($this->input->post('fd_tgl'));
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mInfoAntrian');
		$sSQL = $this->mInfoAntrian->KodeRegAll($xTgl);
		$xTotal = $sSQL->num_rows();
		
		$sSQL = $this->mInfoAntrian->KodeReg($xTgl,$nStart,$nLimit);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_pesan'	=> ascii_to_entities(trim($xRow->fs_kd_pesan)),
					'fs_kd_reg'		=> ascii_to_entities(trim($xRow->fs_kd_reg)),
					'fs_jam'		=> ascii_to_entities(trim($xRow->fs_jam)),
					'fs_kd_mr'		=> ascii_to_entities(trim($xRow->fs_kd_mr)),
					'fs_nm_pasien'	=> ascii_to_entities(trim($xRow->fs_nm_pasien)),
					
					'fs_alamat'		=> ascii_to_entities(trim($xRow->fs_alamat)),
					'fb_periksa'	=> ascii_to_entities(trim($xRow->fb_periksa))
				);
			}
		}
		echo '({"total":"'.$xTotal.'","hasil":'.json_encode($xArr).'})';
	}
}
?>