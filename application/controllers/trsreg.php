<?php

class TrsReg extends CI_Controller
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
			$this->load->view('vtrsreg');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function KodeAgama()
	{
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodeAgama();
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_agama'	=> ascii_to_entities(trim($xRow->fs_kd_agama)),
					'fs_nm_agama'	=> ascii_to_entities(trim($xRow->fs_nm_agama))
				);
			}
		}
		echo json_encode($xArr);
	}
	
	function KodeGolDarah()
	{
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodeGolDarah();
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_gol_darah'	=> ascii_to_entities(trim($xRow->fs_gol_darah))
				);
			}
		}
		echo json_encode($xArr);
	}
	
	function KodePendidikan()
	{
		$xKdPendidikan = trim($this->input->post('fs_kd_pendidikan'));
		$xNmPendidikan = trim($this->input->post('fs_nm_pendidikan'));
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodePendidikan($xKdPendidikan,$xNmPendidikan);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_pendidikan'	=> ascii_to_entities(trim($xRow->fs_kd_pendidikan)),
					'fs_nm_pendidikan'	=> ascii_to_entities(trim($xRow->fs_nm_pendidikan))
				);
			}
		}
		echo json_encode($xArr);
	}
	
	function KodePekerjaan()
	{
		$xKdPekerjaan = trim($this->input->post('fs_kd_pekerjaan'));
		$xNmPekerjaan = trim($this->input->post('fs_nm_pekerjaan'));
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodePekerjaan($xKdPekerjaan,$xNmPekerjaan);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_pekerjaan'	=> ascii_to_entities(trim($xRow->fs_kd_pekerjaan)),
					'fs_nm_pekerjaan'	=> ascii_to_entities(trim($xRow->fs_nm_pekerjaan))
				);
			}
		}
		echo json_encode($xArr);
	}
	
	function KodeReg()
	{
		$xKdReg = trim($this->input->post('fs_kd_reg'));
		$xKdMR = trim($this->input->post('fs_kd_mr'));
		$xNmPasien = trim($this->input->post('fs_nm_pasien'));
		$xAlamat = trim($this->input->post('fs_alamat'));
		$xTglPeriksa = trim($this->input->post('fd_tgl_periksa'));
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodeRegAll($xKdReg,$xKdMR,$xNmPasien,$xAlamat,$xTglPeriksa);
		$xTotal = $sSQL->num_rows();
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodeReg($xKdReg,$xKdMR,$xNmPasien,$xAlamat,$xTglPeriksa,$nStart,$nLimit);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_reg'			=> ascii_to_entities(trim($xRow->fs_kd_reg)),
					'fs_kd_pesan'		=> ascii_to_entities(trim($xRow->fs_kd_pesan)),
					'fd_tgl_masuk'		=> ascii_to_entities(trim($xRow->fd_tgl_masuk)),
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
					'fs_nm_pekerjaan'	=> ascii_to_entities(trim($xRow->fs_nm_pekerjaan)),
					'fs_nm_kerabat'		=> ascii_to_entities(trim($xRow->fs_nm_kerabat)),
					
					'fs_alm_kerabat'	=> ascii_to_entities(trim($xRow->fs_alm_kerabat)),
					'fs_hubungan'		=> ascii_to_entities(trim($xRow->fs_hubungan)),
					'fs_tlp_kerabat'	=> ascii_to_entities(trim($xRow->fs_tlp_kerabat))
				);
			}
		}
		echo '({"total":"'.$xTotal.'","hasil":'.json_encode($xArr).'})';
	}
	
	function KodePesan()
	{
		$xKdPesan = trim($this->input->post('fs_kd_pesan'));
		$xNmPasien = trim($this->input->post('fs_nm_pasien'));
		$xAlamat = trim($this->input->post('fs_alamat'));
		$xTglPeriksa = trim($this->input->post('fd_tgl_periksa'));
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodePesanRegAll($xKdPesan,$xNmPasien,$xAlamat,$xTglPeriksa);
		$xTotal = $sSQL->num_rows();
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodePesanReg($xKdPesan,$xNmPasien,$xAlamat,$xTglPeriksa,$nStart,$nLimit);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_pesan'		=> ascii_to_entities(trim($xRow->fs_kd_pesan)),
					'fd_tgl_masuk'		=> ascii_to_entities(trim($xRow->fd_tgl_masuk)),
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
					'fs_nm_pekerjaan'	=> ascii_to_entities(trim($xRow->fs_nm_pekerjaan)),
					'fs_nm_kerabat'		=> ascii_to_entities(trim($xRow->fs_nm_kerabat)),
					'fs_alm_kerabat'	=> ascii_to_entities(trim($xRow->fs_alm_kerabat)),
					
					'fs_hubungan'		=> ascii_to_entities(trim($xRow->fs_hubungan)),
					'fs_tlp_kerabat'	=> ascii_to_entities(trim($xRow->fs_tlp_kerabat))
				);
			}
		}
		echo '({"total":"'.$xTotal.'","hasil":'.json_encode($xArr).'})';
	}
	
	function KodeMR()
	{
		$xKdMR = trim($this->input->post('fs_kd_mr'));
		$xNmPasien = trim($this->input->post('fs_nm_pasien'));
		$xAlamat = trim($this->input->post('fs_alamat'));
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodeMRAll($xKdMR,$xNmPasien,$xAlamat);
		$xTotal = $sSQL->num_rows();
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodeMR($xKdMR,$xNmPasien,$xAlamat,$nStart,$nLimit);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
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
					
					'fs_nm_pekerjaan'	=> ascii_to_entities(trim($xRow->fs_nm_pekerjaan)),
					'fs_nm_kerabat'		=> ascii_to_entities(trim($xRow->fs_nm_kerabat)),
					'fs_alm_kerabat'	=> ascii_to_entities(trim($xRow->fs_alm_kerabat)),
					'fs_hubungan'		=> ascii_to_entities(trim($xRow->fs_hubungan)),
					'fs_tlp_kerabat'	=> ascii_to_entities(trim($xRow->fs_tlp_kerabat))
				);
			}
		}
		echo '({"total":"'.$xTotal.'","hasil":'.json_encode($xArr).'})';
	}
	
	function CekSimpan()
	{
		$xKdReg = trim($this->input->post('fs_kd_reg'));
		$xKdMR = trim($this->input->post('fs_kd_mr'));
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
		
		if (trim($xKdMR) == '' or trim($xKdMR) == 'BARU')
		{
			$this->load->model('mTrsPesan');
			$sSQL = $this->mTrsPesan->cek_nama($xNama,$xAlamat);
			
			if ($sSQL->num_rows() > 0)
			{
				$sSQL = $sSQL->row();
				$xKdMR = $sSQL->fs_kd_mr;
				$xHasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Simpan Gagal,<br>
						Nama: '.trim($xNama).'<br>Alamat: '.trim($xAlamat).'<br>Data sudah pernah ada dengan Kode MR: '.trim($xKdMR).' !!'
				);
				echo json_encode($xHasil);
				return;
			}
		}
		
		if (trim($xKdReg) == '' or trim($xKdReg) == 'BARU')
		{
			$xHasil = array(
				'sukses'	=> true,
				'hasil'		=> 'lanjut'
			);
			echo json_encode($xHasil);
		}
		else
		{
			$this->load->model('mTrsReg');
			$sSQL = $this->mTrsReg->CekKodeReg($xKdReg);
			
			if ($sSQL->num_rows() > 0)
			{
				$xHasil = array(
					'sukses'	=> true,
					'hasil'		=> 'No.Registrasi sudah ada, Apakah Anda ingin meng-update?'
				);
				echo json_encode($xHasil);
			}
			else
			{
				$xHasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Simpan Gagal, No.Registrasi tidak diketahui!!'
				);
				echo json_encode($xHasil);
			}
		}
	}
	
	function Simpan()
	{
		$xKdReg = trim($this->input->post('fs_kd_reg'));
		$xKdPesan = trim($this->input->post('fs_kd_pesan'));
		$xTgl = trim($this->input->post('fd_tgl_masuk'));
		$xJam = trim($this->input->post('fs_jam_masuk'));
		$xKdMR = trim($this->input->post('fs_kd_mr'));
		
		$xNama = trim($this->input->post('fs_nm_pasien'));
		$xAlamat = trim($this->input->post('fs_alamat'));
		$xTelp = trim($this->input->post('fs_tlp'));
		$xKdSex = trim($this->input->post('fb_sex'));
		$xGolDarah = trim($this->input->post('fs_gol_darah'));
		
		$xTglLahir = trim($this->input->post('fd_tgl_lahir'));
		$xUmurTh = trim($this->input->post('fn_th'));
		$xUmurBln = trim($this->input->post('fn_bl'));
		$xUmurHr = trim($this->input->post('fn_hr'));
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
					'fn_mr' => $xKdMR
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
			'fs_kd_pekerjaan'	=> trim($xKdPekerjaan),
			'fs_nm_kerabat'		=> trim($xNmKerabat),
			'fs_alm_kerabat'	=> trim($xAlmKerabat),
			
			'fs_hubungan'		=> trim($xHubungan),
			'fs_tlp_kerabat'	=> trim($xTelpkerabat)
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
		
		
		// update tx_pesan
		$xData = array(
			'fb_reg' => '1'
		);
		
		$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
					AND fs_kd_pesan = '".trim($xKdPesan)."'";
		
		$this->db->where($xWhere);
		$this->db->update('tx_pesan', $xData);
		// Eof update tx_pesan
		
		
		// Simpan Reg
		$xUpdate = false;
		$this->load->model('mTrsReg');
		$sSQL = $this->mTrsReg->CekKodeReg($xKdReg);
		
		if ($sSQL->num_rows() > 0)
		{
			$xUpdate = true;
		}
		else
		{
			$this->db->trans_begin();
			$xPrefix = $xPrefix;
			
			$this->load->model('mMainModul');
			$sSQL = $this->mMainModul->AmbilKodeReg($xPrefix);
			
			if ($sSQL->num_rows() > 0)
			{
				$sSQL = $sSQL->row();
				$xKdReg = $sSQL->fn_reg;
				$xData = array(
					'fn_reg' => $xKdReg
				);
				
				$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'";
				
				$this->db->where($xWhere);
				$this->db->update('tm_parameter', $xData);
			}
			else
			{
				$xKdReg = $xPrefix.'001';
				$xData = array(
					'fn_reg' => $xKdReg
				);
				
				$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'";
				
				$this->db->where($xWhere);
				$this->db->update('tm_parameter', $xData);
			}
			
			$xUpdate = false;
			$xKdReg = 'RG'.$xKdReg;
			$this->db->trans_commit();
		}
		
		$xDt = array(
			'fs_kd_dokter'	=> trim($this->session->userdata('gID')),
			'fs_kd_reg'		=> trim($xKdReg),
			'fs_kd_pesan'	=> trim($xKdPesan),
			'fs_kd_mr'		=> trim($xKdMR),
			'fd_tgl_masuk'	=> trim($xTgl),
			'fs_jam_masuk'	=> trim($xJam),
			
			'fs_ket_umur'	=> trim($xUmurTh).' th '.trim($xUmurBln).' bl '.trim($xUmurHr).' hr'
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
				
				$this->db->insert('tx_reg', $xData);
				
				$xHasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Simpan Sukses',
					'kodemr'	=> $xKdMR,
					'kodereg'	=> $xKdReg
				);
				echo json_encode($xHasil);
			}
			else
			{
				$xHasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Simpan Gagal, No.Rekam Medis tidak diketahui!!<br>Silakan coba lagi kemudian...'
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
					AND fs_kd_reg = '".trim($xKdReg)."'";
			
			$this->db->where($xWhere);
			$this->db->update('tx_reg', $xData);
			
			$xHasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Simpan Update Sukses',
				'kodemr'	=> $xKdMR,
				'kodereg'	=> $xKdReg
			);
			echo json_encode($xHasil);
		}
		// Eof Simpan Reg
	}
	
	function CekHapus()
	{
		$xKdReg = trim($this->input->post('fs_kd_reg'));
		
		if (trim($xKdReg) == '' or trim($xKdReg) == 'BARU')
		{
			$xHasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Hapus Gagal, No.Registrasi kosong!!'
			);
			echo json_encode($xHasil);
		}
		else
		{
			$this->load->model('mTrsReg');
			$sSQL = $this->mTrsReg->CekKodeReg($xKdReg);
			
			if ($sSQL->num_rows() > 0)
			{
				$sSQL = $sSQL->row();
				$periksa = trim($sSQL->fb_periksa);
				
				if ($periksa == '1')
				{
					$xHasil = array(
						'sukses'	=> false,
						'hasil'		=> 'Hapus Gagal, No.Registrasi ini sudah diperiksa!!'
					);
				}
				else
				{
					$xHasil = array(
						'sukses'	=> true,
						'hasil'		=> 'Apakah Anda yakin untuk menghapus?'
					);
				}
				echo json_encode($xHasil);
			}
			else
			{
				$xHasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Hapus Gagal, No.Registrasi tidak diketahui!!'
				);
				echo json_encode($xHasil);
			}
		}
	}
	
	function Hapus()
	{
		$xKdReg = trim($this->input->post('fs_kd_reg'));
		$xKdPesan = trim($this->input->post('fs_kd_pesan'));
		
		// update tx_pesan
		$xData = array(
			'fb_reg' => '0'
		);
		
		$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
					AND fs_kd_pesan = '".trim($xKdPesan)."'";
		
		$this->db->where($xWhere);
		$this->db->update('tx_pesan', $xData);
		// Eof update tx_pesan
		
		// Hapus reg
		$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
					AND fs_kd_reg = '".trim($xKdReg)."'";
		
		$this->db->where($xWhere);
		$this->db->delete('tx_reg');
		// Eof Hapus reg
		
		$xHasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Hapus Sukses',
			'kodereg'	=> $xKdReg
		);
		echo json_encode($xHasil);
	}
}
?>