<?php

class SetupIdDokter extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		if (trim($this->session->userdata('gID')) <> '')
		{
			$this->load->view('vsetupiddokter');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function KodeDokter()
	{
		//change db
		$this->load->model('mMainModul');
		$this->mMainModul->ChangeDB(trim($this->session->userdata('gDB')));
		//eof change db
		
		$this->load->model('mSetupIdDokter');
		$sSQL = $this->mSetupIdDokter->KodeDokter();
		
		if ($sSQL->num_rows() > 0)
		{
			$sSQL = $sSQL->row();
			$xNmDokter = $sSQL->fs_nm_dokter;
			$xAlamat = $sSQL->fs_alamat;
			$xKota = $sSQL->fs_kota;
			$xTelp = $sSQL->fs_tlp;
			$xEmail = $sSQL->fs_email;
		}
		else
		{
			$xNmDokter = '';
			$xAlamat = '';
			$xKota = '';
			$xTelp = '';
			$xEmail = '';
		}
		
		$xHasil = array(
			'sukses'	=> true,
			'nmdokter'	=> $xNmDokter,
			'alamat'	=> $xAlamat,
			'kota'		=> $xKota,
			'tlp'		=> $xTelp,
			
			'email'		=> $xEmail
		);
		echo json_encode($xHasil);
	}
	
	function CekSimpan()
	{
		$xHasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Apakah Anda ingin meng-update?'
		);
		echo json_encode($xHasil);
	}
	
	function Simpan()
	{
		//change db
		$this->load->model('mMainModul');
		$this->mMainModul->ChangeDB(trim($this->session->userdata('gDB')));
		//eof change db
		
		$xNmDokter = trim($this->input->post('fs_nm_dokter'));
		$xAlamat = trim($this->input->post('fs_alamat'));
		$xKota = trim($this->input->post('fs_kota'));
		$xTelp = trim($this->input->post('fs_tlp'));
		$xEmail = trim($this->input->post('fs_email'));
		$xTglSimpan = trim($this->input->post('fd_simpan'));
		
		$xData = array(
			'fs_nm_dokter'	=> trim($xNmDokter),
			'fs_alamat' 	=> trim($xAlamat),
			'fs_kota' 		=> trim($xKota),
			'fs_tlp' 		=> trim($xTelp),
			'fs_email' 		=> trim($xEmail)
		);
		
		$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'";
		
		$this->db->where($xWhere);
		$this->db->update('tm_dokter', $xData);
				
		$new = array(
			'gKota'=>trim($xKota)
			);
		$this->session->set_userdata($new);
		
		$xHasil = array(
			'sukses'	=> true
		);
		echo json_encode($xHasil);
	}
	
	function Simpan2()
	{
		$this->load->database();
		
		$xNmDokter = trim($this->input->post('fs_nm_dokter'));
		$xAlamat = trim($this->input->post('fs_alamat'));
		$xKota = trim($this->input->post('fs_kota'));
		$xTelp = trim($this->input->post('fs_tlp'));
		$xEmail = trim($this->input->post('fs_email'));
		$xTglSimpan = trim($this->input->post('fd_simpan'));
		
		$xData = array(
			'fs_nm_dokter'	=> trim($xNmDokter),
			'fs_alamat' 	=> trim($xAlamat),
			'fs_kota' 		=> trim($xKota),
			'fs_tlp' 		=> trim($xTelp),
			'fs_email' 		=> trim($xEmail),
			
			'fs_upd' 		=> trim($this->session->userdata('gUser')),
			'fd_upd' 		=> trim($xTglSimpan)
		);
		
		$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'";
		
		$this->db->where($xWhere);
		$this->db->update('tm_dokter', $xData);
		
		$xHasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Simpan Update Sukses'
		);
		echo json_encode($xHasil);
	}
}
?>