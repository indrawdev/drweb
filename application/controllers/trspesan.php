<?php

class TrsPesan extends CI_Controller
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
			$this->load->view('vtrspesan');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function KodePesan()
	{
		$xKdPesan = trim($this->input->post('fs_kd_pesan'));
		$xNama = trim($this->input->post('fs_nama'));
		$xAlamat = trim($this->input->post('fs_alamat'));
		$xTglPeriksa = trim($this->input->post('fd_tgl_periksa'));
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodePesanAll($xKdPesan,$xNama,$xAlamat,$xTglPeriksa);
		$xTotal = $sSQL->num_rows();
		
		$sSQL = $this->mSearch->KodePesan($xKdPesan,$xNama,$xAlamat,$xTglPeriksa,$nStart,$nLimit);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_pesan'		=> ascii_to_entities(trim($xRow->fs_kd_pesan)),
					'fd_tgl_periksa'	=> ascii_to_entities(trim($xRow->fd_tgl_periksa)),
					'fd_tgl_pesan'		=> ascii_to_entities(trim($xRow->fd_tgl_pesan)),
					'fs_kd_mr'			=> ascii_to_entities(trim($xRow->fs_kd_mr)),
					'fs_nm_pasien'		=> ascii_to_entities(trim($xRow->fs_nm_pasien)),
					
					'fs_alamat'			=> ascii_to_entities(trim($xRow->fs_alamat)),
					'fs_tlp'			=> ascii_to_entities(trim($xRow->fs_tlp)),
					'fs_kd_jk'			=> ascii_to_entities(trim($xRow->fs_kd_jk)),
					'fs_nm_jk'			=> ascii_to_entities(trim($xRow->fs_nm_jk)),
					'fs_gol_darah'		=> ascii_to_entities(trim($xRow->fs_gol_darah)),
					
					'fd_tgl_lahir'		=> ascii_to_entities(trim($xRow->fd_tgl_lahir)),
					'fn_tinggi'			=> ascii_to_entities(trim($xRow->fn_tinggi)),
					'fn_berat'			=> ascii_to_entities(trim($xRow->fn_berat)),
					'fs_kd_agama'		=> ascii_to_entities(trim($xRow->fs_kd_agama)),
					'fs_nm_agama'		=> ascii_to_entities(trim($xRow->fs_nm_agama)),
					
					'fs_kd_pendidikan'	=> ascii_to_entities(trim($xRow->fs_kd_pendidikan)),
					'fs_nm_pendidikan'	=> ascii_to_entities(trim($xRow->fs_nm_pendidikan)),
					'fs_kd_pekerjaan'	=> ascii_to_entities(trim($xRow->fs_kd_pekerjaan)),
					'fs_nm_pekerjaan'	=> ascii_to_entities(trim($xRow->fs_nm_pekerjaan))
				);
			}
		}
		echo '({"total":"'.$xTotal.'","hasil":'.json_encode($xArr).'})';
	}
	
	function CekSimpan()
	{
		$xKdPesan = trim($this->input->post('fs_kd_pesan'));
		$xNama = trim($this->input->post('fs_nm_pasien'));
		$xAlamat = trim($this->input->post('fs_alamat'));
		
		$this->load->model('mMainModul');
		$sSQL = $this->mMainModul->CekTempo();
		
		if ($sSQL->num_rows() > 0)
		{
			$sSQL = $sSQL->row();
			$xTempo = $sSQL->fs_status;
			
			if (trim($xTempo) == 'EXPIRED')
			{
				$xHasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Simpan Gagal, Jatuh Tempo telah expired!!<br>Silakan perpanjang masa pakai terlebih dulu...'
				);
				echo json_encode($xHasil);
				return;
			}
		}
		else
		{
			$xHasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Simpan Gagal, Jatuh Tempo telah expired!!<br>Silakan perpanjang masa pakai terlebih dulu...'
			);
			echo json_encode($xHasil);
			return;
		}
		
		if (trim($xKdPesan) == '' or trim($xKdPesan) == 'BARU')
		{
			$xHasil = array(
				'sukses'	=> true,
				'hasil'		=> 'lanjut'
			);
			echo json_encode($xHasil);
		}
		else
		{
			$this->load->model('mTrsPesan');
			$sSQL = $this->mTrsPesan->CekKodePesan($xKdPesan);
			
			if ($sSQL->num_rows() > 0)
			{
				$xHasil = array(
					'sukses'	=> true,
					'hasil'		=> 'No.Pendaftaran sudah ada, Apakah Anda ingin meng-update?'
				);
				echo json_encode($xHasil);
			}
			else
			{
				$xHasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Simpan Gagal, No.Pendaftaran tidak diketahui!!'
				);
				echo json_encode($xHasil);
			}
		}
	}
	
	function Simpan()
	{
		$xKdPesan = trim($this->input->post('fs_kd_pesan'));
		$xTglPeriksa = trim($this->input->post('fd_tgl_periksa'));
		$xTglPesan = trim($this->input->post('fd_tgl_pesan'));
		$xKdMR = trim($this->input->post('fs_kd_mr'));
		$xNama = trim($this->input->post('fs_nm_pasien'));
		
		$xAlamat = trim($this->input->post('fs_alamat'));
		$xTelp = trim($this->input->post('fs_tlp'));
		$xKdSex = trim($this->input->post('fb_sex'));
		$xGolDarah = trim($this->input->post('fs_gol_darah'));
		$xTglLahir = trim($this->input->post('fd_tgl_lahir'));
		
		$xTinggi = trim($this->input->post('fn_tinggi'));
		$xBerat = trim($this->input->post('fn_berat'));
		$xKdAgama = trim($this->input->post('fs_kd_agama'));
		$xKdPendidikan = trim($this->input->post('fs_kd_pendidikan'));
		$xKdPekerjaan = trim($this->input->post('fs_kd_pekerjaan'));
		
		$xNmKerabat = trim($this->input->post('fs_nm_kerabat'));
		$xAlmKerabat = trim($this->input->post('fs_alm_kerabat'));
		$xHubungan = trim($this->input->post('fs_hubungan'));
		$xTelpkerabat = trim($this->input->post('fs_tlp_kerabat'));
		$xPrefix = trim($this->input->post('fd_prefix'));
		
		$xTglSimpan = trim($this->input->post('fd_simpan'));
		
		
		// Simpan MR
		$xUpdate = false;
		$this->load->model('mTrsReg');
		$sSQL = $this->mTrsReg->CekKodeMR($xKdMR);
		
		if ($sSQL->num_rows() > 0)
		{
			$xUpdate = true;
		}
		else
		{
			$this->db->trans_begin();
			
			$this->load->model('mMainModul');
			$sSQL = $this->mMainModul->AmbilKodeMR();
			
			if ($sSQL->num_rows() > 0)
			{
				$sSQL = $sSQL->row();
				$xKdMR = $sSQL->fn_mr;
				$xData = array(
					'fn_mr'	=> $xKdMR
				);
				
				$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'";
				
				$this->db->where($xWhere);
				$this->db->update('tm_parameter', $xData);
			}
			else
			{
				$xKdMR = sprintf("%09d",'1');
				$xData = array(
					'fn_mr' => $xKdMR
				);
				
				$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'";
				
				$this->db->where($xWhere);
				$this->db->update('tm_parameter', $xData);
			}
			
			$xUpdate = false;
			$xKdMR = 'MR'.$xKdMR;
			$this->db->trans_commit();
		}
		
		$xDt = array(
			'fs_kd_dokter'		=> trim($this->session->userdata('gID')),
			'fs_kd_mr'			=> trim($xKdMR),
			'fs_nm_pasien'		=> trim($xNama),
			'fs_alamat'			=> trim($xAlamat),
			'fs_tlp'			=> trim($xTelp),
			
			'fb_sex'			=> trim($xKdSex),
			'fs_gol_darah'		=> trim($xGolDarah),
			'fd_tgl_lahir'		=> trim($xTglLahir),
			'fn_tinggi'			=> trim($xTinggi),
			'fn_berat'			=> trim($xBerat),
			
			'fs_kd_agama'		=> trim($xKdAgama),
			'fs_kd_pendidikan'	=> trim($xKdPendidikan),
			'fs_kd_pekerjaan'	=> trim($xKdPekerjaan)
		);
		
		if ($xUpdate == false)
		{
			if (trim($xKdMR) <> '')
			{
				$xDt2 = array(
					'fs_usr'	=> trim($this->session->userdata('gUser')),
					'fd_usr'	=> trim($xTglSimpan),
					'fs_upd'	=> trim($this->session->userdata('gUser')),
					'fd_upd'	=> trim($xTglSimpan)
				);
				$xData = array_merge($xDt,$xDt2);
				
				$this->db->insert('tm_mr', $xData);
			}
			else
			{
				$xHasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Simpan Gagal, No.Rekam Medis tidak diketahui!!<br>Silakan coba lagi kemudian...'
				);
				echo json_encode($xHasil);
				return;
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
					AND fs_kd_mr = '".trim($xKdMR)."'";
			
			$this->db->where($xWhere);
			$this->db->update('tm_mr', $xData);
		}
		// Eof Simpan MR
		
		
		// Simpan pesan
		$xUpdate = false;
		$this->load->model('mTrsPesan');
		$sSQL = $this->mTrsPesan->CekKodePesan($xKdPesan);
		
		if ($sSQL->num_rows() > 0)
		{
			$xUpdate = true;
		}
		else
		{
			$this->db->trans_begin();
			$xPrefix = $xPrefix;
			
			$this->load->model('mMainModul');
			$sSQL = $this->mMainModul->AmbilKodePesan($xPrefix);
			
			if ($sSQL->num_rows() > 0)
			{
				$sSQL = $sSQL->row();
				$xKdPesan = $sSQL->fn_pesan;
				$xData = array(
					'fn_pesan' => $xKdPesan
				);
				
				$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'";
				
				$this->db->where($xWhere);
				$this->db->update('tm_parameter', $xData);
			}
			else
			{
				$xKdPesan = $xPrefix.'001';
				$xData = array(
					'fn_pesan' => $xKdPesan
				);
				
				$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'";
				
				$this->db->where($xWhere);
				$this->db->update('tm_parameter', $xData);
			}
			
			$xUpdate = false;
			$xKdPesan = 'DF'.$xKdPesan;
			$this->db->trans_commit();
		}
		
		$xDt = array(
			'fs_kd_dokter'	=> trim($this->session->userdata('gID')),
			'fs_kd_pesan'	=> trim($xKdPesan),
			'fd_periksa'	=> trim($xTglPeriksa),
			'fd_pesan'		=> trim($xTglPesan),
			'fs_kd_mr'		=> trim($xKdMR)
		);
		
		if ($xUpdate == false)
		{
			if (trim($xKdPesan) <> '')
			{
				$xDt2 = array(
					'fs_usr'	=> trim($this->session->userdata('gUser')),
					'fd_usr'	=> trim($xTglSimpan),
					'fs_upd'	=> trim($this->session->userdata('gUser')),
					'fd_upd'	=> trim($xTglSimpan)
				);
				$xData = array_merge($xDt,$xDt2);
				
				$this->db->insert('tx_pesan', $xData);
				
				$xHasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Simpan Sukses',
					'kodemr'	=> $xKdMR,
					'kodepesan'	=> $xKdPesan
				);
				echo json_encode($xHasil);
			}
			else
			{
				$xHasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Simpan Gagal, No.Pendaftaran tidak diketahui!!<br>Silakan coba lagi kemudian...'
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
					AND	fs_kd_pesan = '".trim($xKdPesan)."'";
			
			$this->db->where($xWhere);
			$this->db->update('tx_pesan', $xData);
			
			$xHasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Simpan Update Sukses',
				'kodemr'	=> $xKdMR,
				'kodepesan'	=> $xKdPesan
			);
			echo json_encode($xHasil);
		}
		// Eof Simpan pesan
	}
}
?>