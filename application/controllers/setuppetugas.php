<?php

class SetupPetugas extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		if (trim($this->session->userdata('gUserLevel')) <> '')
		{
			$this->load->view('vsetuppetugas');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function KodePaket()
	{
		$this->load->database();
		
		$xKdPaket = trim($this->input->post('fs_kd_paket'));
		$xNmPaket = trim($this->input->post('fs_nm_paket'));
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodePaketAksesAll($xKdPaket,$xNmPaket);
		$xTotal = $sSQL->num_rows();
		
		$sSQL = $this->mSearch->KodePaketAkses($xKdPaket,$xNmPaket,$nStart,$nLimit);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_paket'	=> ascii_to_entities(trim($xRow->fs_kd_paket)),
					'fs_nm_paket'	=> ascii_to_entities(trim($xRow->fs_nm_paket))
				);
			}
		}
		echo '({"total":"'.$xTotal.'","hasil":'.json_encode($xArr).'})';
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
					'fs_password'	=> $this->encrypt->decode(trim($xRow->fs_password),$xKey),
					'fs_kd_akses'	=> $xRow->fs_kd_akses,
					'fs_nm_akses'	=> $xRow->fs_nm_akses
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
		$xKdPass = trim($this->input->post('fs_pass'));
		$xKdPass2 = trim($this->input->post('fs_pass2'));
		
		if ($xKdPass <> $xKdPass2)
		{
			$xHasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Simpan Gagal, kedua password tidak sama!!'
			);
			echo json_encode($xHasil);
			return;
		}
		
		$this->load->model('mMainModul');
		$sSQL = $this->mMainModul->ValidUser($xKdPetugas);
		
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
	
	function Simpan()
	{
		//change db
		$this->load->model('mMainModul');
		$this->mMainModul->ChangeDB(trim($this->session->userdata('gDB')));
		//eof change db
		
		$xKdPetugas = trim($this->input->post('fs_kd_user'));
		$xNmPetugas = trim($this->input->post('fs_nm_user'));
		$xKey = 'dr';
		$xKdPass = $this->encrypt->encode(trim($this->input->post('fs_pass')),$xKey);
		$xKdAkses = trim($this->input->post('fs_kd_akses'));
		$xNmAkses = trim($this->input->post('fs_nm_akses'));
		$xTglSimpan = trim($this->input->post('fd_simpan'));
		
		$xUpdate = false;
		$this->load->model('mSetupPetugas');
		$sSQL = $this->mSetupPetugas->CekKodePetugas($xKdPetugas);
		
		if ($sSQL->num_rows() > 0)
		{
			$xUpdate = true;
		}
		
		$xDt = array(
			'fs_kd_dokter'	=> trim($this->session->userdata('gID')),
			'fs_kd_user'	=> trim($xKdPetugas),
			'fs_nm_user'	=> trim($xNmPetugas),
			'fs_password'	=> trim($xKdPass),
			'fs_kd_akses'	=> trim($xKdAkses),
			
			'fs_nm_akses'	=> trim($xNmAkses)
		);
		
		if ($xUpdate == false)
		{
			if (trim($xKdPetugas) <> '')
			{
				$xDt2 = array(
					'fs_usr'	=> trim($this->session->userdata('gUser')),
					'fd_usr'	=> trim($xTglSimpan),
					'fs_upd'	=> trim($this->session->userdata('gUser')),
					'fd_upd'	=> trim($xTglSimpan)
				);
				$xData = array_merge($xDt,$xDt2);
				
				$this->db->insert('tm_user', $xData);
				
				$xHasil = array(
					'sukses'	=> true
				);
				echo json_encode($xHasil);
			}
			else
			{
				$xHasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Simpan Gagal, Kode Petugas tidak diketahui!!<br>Silakan coba lagi kemudian...'
				);
				echo json_encode($xHasil);
			}
		}
		else
		{
			$xDt2 = array(
				'fs_upd'	=> trim($this->session->userdata('gUser')),
				'fd_upd'	=> trim($xTglSimpan)
			);
			$xData = array_merge($xDt,$xDt2);
			
			$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
					AND	fs_kd_user = '".trim($xKdPetugas)."'";
			
			$this->db->where($xWhere);
			$this->db->update('tm_user', $xData);
			
			$xHasil = array(
				'sukses'	=> true
			);
			echo json_encode($xHasil);
		}
	}
	
	function Simpan2()
	{
		$this->load->database();
		
		$xKdPetugas = trim($this->input->post('fs_kd_user'));
		$xKdPass = trim($this->input->post('fs_pass'));
		$xKdAkses = trim($this->input->post('fs_kd_akses'));
		$xTglSimpan = trim($this->input->post('fd_simpan'));
		
		$xUpdate = false;
		$this->load->model('mSetupPetugas');
		$sSQL = $this->mSetupPetugas->CekKodePetugas2($xKdPetugas);
		
		if ($sSQL->num_rows() > 0)
		{
			$xUpdate = true;
		}
		else
		{
			$this->load->model('mSetupPetugas');
			$sSQL = $this->mSetupPetugas->KodeDokter();
			
			if ($sSQL->num_rows() > 0)
			{
				$sSQL = $sSQL->row();
				$xNmDokter = $sSQL->fs_nm_dokter;
				$xAlamat = $sSQL->fs_alamat;
				$xTelp = $sSQL->fs_tlp;
				$xEmail = $sSQL->fs_email;
				
				$xNmDB = $sSQL->fs_nm_db;
				$xKota = $sSQL->fs_kota;
				$xKdPaket = $sSQL->fs_kd_paket;
				$TglReg = $sSQL->fd_reg;
				$TglJatuhTempo = $sSQL->fd_jatuh_tempo;
			}
		}
		
		if ($xUpdate == false)
		{
			$xData = array(
				'fs_kd_dokter'		=> trim($this->session->userdata('gID')),
				'fs_nm_dokter'		=> trim($xNmDokter),
				'fs_kd_user'		=> trim($xKdPetugas),
				'fs_password'		=> trim($xKdPass),
				'fs_alamat'			=> trim($xAlamat),
				
				'fs_kota'			=> trim($xKota),
				'fs_tlp'			=> trim($xTelp),
				'fs_email'			=> trim($xEmail),
				'fs_nm_db'			=> trim($xNmDB),
				
				'fs_kd_paket'		=> trim($xKdPaket),
				'fs_kd_akses'		=> trim($xKdAkses),
				'fd_reg'			=> trim($TglReg),
				'fd_jatuh_tempo'	=> trim($TglJatuhTempo),
				
				'fs_usr'			=> trim($this->session->userdata('gUser')),
				'fd_usr'			=> trim($xTglSimpan),
				'fs_upd'			=> trim($this->session->userdata('gUser')),
				'fd_upd'			=> trim($xTglSimpan)
			);
			
			$this->db->insert('tm_dokter', $xData);
			
			$xHasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Simpan Sukses'
			);
			echo json_encode($xHasil);
		}
		else
		{
			$xData = array(
				'fs_password'	=> trim($xKdPass),
				'fs_kd_akses'	=> trim($xKdAkses),
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
}
?>