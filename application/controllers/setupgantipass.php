<?php

class SetupGantiPass extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		if (trim($this->session->userdata('gUserLevel')) <> '')
		{
			$this->load->view('vsetupgantipass');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function KodePetugas()
	{
		//change db
		$this->load->model('mMainModul');
		$this->mMainModul->ChangeDB(trim($this->session->userdata('gDB')));
		//eof change db
		
		$xKdPetugas = trim($this->input->post('fs_kd_user'));
		$xNmPetugas = trim($this->input->post('fs_nm_user'));
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodePetugasAll($xKdPetugas,$xNmPetugas);
		$xTotal = $sSQL->num_rows();
		
		$sSQL = $this->mSearch->KodePetugas($xKdPetugas,$xNmPetugas,$nStart,$nLimit);
		
		if ($sSQL->num_rows() > 0)
		{
			$xArr = array();
			$xKey = 'dr';
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_user'	=> $xRow->fs_kd_user,
					'fs_nm_user'	=> $xRow->fs_nm_user,
					'fs_password'	=> $this->encrypt->decode(trim($xRow->fs_password),$xKey)
				);
			}
		}
		echo '({"total":"'.$xTotal.'","hasil":'.json_encode($xArr).'})';
	}
	
	function CekSimpan()
	{
		//change db
		$this->load->model('mMainModul');
		$this->mMainModul->ChangeDB(trim($this->session->userdata('gDB')));
		//eof change db
		
		$xKdPetugas = trim($this->input->post('fs_kd_user'));
		$xKdLama = trim($this->input->post('fs_lama'));
		$xKdPass = trim($this->input->post('fs_pass'));
		$xKdPass2 = trim($this->input->post('fs_pass2'));
		
		$this->load->model('mMainModul');
		$sSQL = $this->mMainModul->ValidUserPass($xKdPetugas);
		
		if ($sSQL->num_rows() > 0)
		{
			$sSQL = $sSQL->row();
			$xKdPetugas = $sSQL->fs_kd_user;
			$xKey = 'dr';
			$xUserPassword = $this->encrypt->decode($sSQL->fs_password,$xKey);
			
			if (trim($xKdLama) <> trim($xUserPassword))
			{
				$xHasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Simpan Gagal, password lama tidak benar!!'
				);
				echo json_encode($xHasil);
				return;
			}
		}
		
		if ($xKdPass <> $xKdPass2)
		{
			$xHasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Simpan Gagal, kedua password tidak sama!!'
			);
			echo json_encode($xHasil);
			return;
		}
		
		if (trim($xKdPetugas) == '')
		{
			$xHasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Simpan Gagal, Kode Petugas tidak diketahui!!'
			);
			echo json_encode($xHasil);
		}
		else
		{
			$this->load->model('mSetupGantiPass');
			$sSQL = $this->mSetupGantiPass->CekKodePetugas($xKdPetugas);
			
			if ($sSQL->num_rows() > 0)
			{
				$xHasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Apakah Anda ingin meng-update?'
				);
				echo json_encode($xHasil);
			}
			else
			{
				$xHasil = array(
					'sukses'	=> true,
					'hasil'		=> 'lanjut'
				);
				echo json_encode($xHasil);
			}
		}
	}
	
	function Simpan()
	{
		//change db
		$this->load->model('mMainModul');
		$this->mMainModul->ChangeDB(trim($this->session->userdata('gDB')));
		//eof change db
		
		$xKdPetugas = trim($this->input->post('fs_kd_user'));
		$xKey = 'dr';
		$xKdPass = $this->encrypt->encode(trim($this->input->post('fs_pass')),$xKey);
		$xTglSimpan = trim($this->input->post('fd_simpan'));
		
		$this->load->model('mSetupGantiPass');
		$sSQL = $this->mSetupGantiPass->CekKodePetugas($xKdPetugas);
		
		if ($sSQL->num_rows() > 0)
		{
			$sSQL = $sSQL->row();
			
			$xData = array(
				'fs_password'	=> trim($xKdPass),
				'fs_upd'		=> trim($this->session->userdata('gUser')),
				'fd_upd'		=> trim($xTglSimpan)
			);
			
			$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
					AND	fs_kd_user = '".trim($xKdPetugas)."'";
			
			$this->db->where($xWhere);
			$this->db->update('tm_user', $xData);
			
			$xHasil = array(
				'sukses'	=> true
			);
			echo json_encode($xHasil);
		}
		else
		{
			$xHasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Simpan Gagal, Kode Petugas tidak diketahui!!'
			);
			echo json_encode($xHasil);
		}
	}
	
	function Simpan2()
	{
		$this->load->database();
		
		$xKdPetugas = trim($this->input->post('fs_kd_user'));
		$xKdPass = trim($this->input->post('fs_pass'));
		$xTglSimpan = trim($this->input->post('fd_simpan'));
		
		$xData = array(
			'fs_password'	=> trim($xKdPass),
			'fs_upd'		=> trim($this->session->userdata('gUser')),
			'fd_upd'		=> trim($xTglSimpan)
		);
		
		$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	fs_kd_user = '".trim($xKdPetugas)."'";
		
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