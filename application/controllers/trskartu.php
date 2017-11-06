<?php

class TrsKartu extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		if (trim($this->session->userdata('gUserLevel')) <> '')
		{
			$this->load->view('vtrskartu');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function CekKodeReg()
	{
		//change db
		$this->load->model('mMainModul');
		$this->mMainModul->ChangeDB(trim($this->session->userdata('gDB')));
		//eof change db
		
		$xKdReg = trim($this->input->post('fs_kd_reg'));
		
		$this->load->model('mTrsKartu');
		$sSQL = $this->mTrsKartu->CekKodeReg($xKdReg);
		
		if ($sSQL->num_rows() > 0)
		{
			$xHasil = array(
				'sukses'	=> true
			);
			echo json_encode($xHasil);
		}
		else
		{
			$xHasil = array(
				'sukses'	=> false
			);
			echo json_encode($xHasil);
		}
	}
	
	function KodeReg()
	{
		//change db
		$this->load->model('mMainModul');
		$this->mMainModul->ChangeDB(trim($this->session->userdata('gDB')));
		//eof change db
		
		$xKdReg = trim($this->input->post('fs_kd_reg'));
		$xKdMR = trim($this->input->post('fs_kd_mr'));
		$xNmPasien = trim($this->input->post('fs_nm_pasien'));
		$xAlamat = trim($this->input->post('fs_alamat'));
		$xTglPeriksa = trim($this->input->post('fd_tgl_periksa'));
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodeKartuRegAll($xKdReg,$xKdMR,$xNmPasien,$xAlamat,$xTglPeriksa);
		$xTotal = $sSQL->num_rows();
		
		$sSQL = $this->mSearch->KodeKartuReg($xKdReg,$xKdMR,$xNmPasien,$xAlamat,$xTglPeriksa,$nStart,$nLimit);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_reg'			=> ascii_to_entities(trim($xRow->fs_kd_reg)),
					'fd_tgl_periksa'	=> ascii_to_entities(trim($xRow->fd_tgl_periksa)),
					'fs_kd_mr'			=> ascii_to_entities(trim($xRow->fs_kd_mr)),
					'fs_nm_pasien'		=> ascii_to_entities(trim($xRow->fs_nm_pasien)),
					'fs_alamat'			=> ascii_to_entities(trim($xRow->fs_alamat)),
					
					'fd_tgl_lahir'		=> ascii_to_entities(trim($xRow->fd_tgl_lahir)),
					'fn_tinggi'			=> ascii_to_entities(trim($xRow->fn_tinggi)),
					'fn_berat'			=> ascii_to_entities(trim($xRow->fn_berat)),
					'fs_anamnesa'		=> ascii_to_entities(trim($xRow->fs_anamnesa)),
					'fs_diagnosa'		=> ascii_to_entities(trim($xRow->fs_diagnosa)),
					
					'fs_tindakan'		=> ascii_to_entities(trim($xRow->fs_tindakan)),
					'fs_kd_icd'			=> ascii_to_entities(trim($xRow->fs_kd_icd)),
					'fs_nm_icd'			=> ascii_to_entities(trim($xRow->fs_nm_icd)),
					'fn_biaya'			=> ascii_to_entities(trim($xRow->fn_biaya)),
					'fn_obat'			=> ascii_to_entities(trim($xRow->fn_obat))
				);
			}
		}
		echo '({"total":"'.$xTotal.'","hasil":'.json_encode($xArr).'})';
	}
	
	function GridICD()
	{
		$this->load->database();
		
		$xCari = trim($this->input->post('fs_cari'));
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodeIcdAll($xCari);
		$xTotal = $sSQL->num_rows();
		
		$sSQL = $this->mSearch->KodeIcd($xCari,$nStart,$nLimit);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_icd'	=> ascii_to_entities(trim($xRow->fs_kd_icd)),
					'fs_nm_icd'	=> ascii_to_entities(trim($xRow->fs_nm_icd))
				);
			}
		}
		echo '({"total":"'.$xTotal.'","hasil":'.json_encode($xArr).'})';
	}
	
	function GridBarang()
	{
		//change db
		$this->load->model('mMainModul');
		$this->mMainModul->ChangeDB(trim($this->session->userdata('gDB')));
		//eof change db
		
		$xKdReg = trim($this->input->post('fs_kd_reg'));
		
		$this->load->model('mTrsKartu');
		$sSQL = $this->mTrsKartu->ListBarang($xKdReg);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_barang'	=> ascii_to_entities(trim($xRow->fs_kd_barang)),
					'fs_nm_barang'	=> ascii_to_entities(trim($xRow->fs_nm_barang)),
					'fs_ket'		=> ascii_to_entities(trim($xRow->fs_ket))
				);
			}
		}
		echo json_encode($xArr);
	}
	
	function GridHistori()
	{
		//change db
		$this->load->model('mMainModul');
		$this->mMainModul->ChangeDB(trim($this->session->userdata('gDB')));
		//eof change db
		
		$xKdMR = trim($this->input->post('fs_kd_mr'));
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mTrsKartu');
		$sSQL = $this->mTrsKartu->HistoriAll($xKdMR);
		$xTotal = $sSQL->num_rows();
		
		$sSQL = $this->mTrsKartu->Histori($xKdMR,$nStart,$nLimit);
		
		$xArr = array();
		$xKet = '';
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xKet = '';
				$icd = '';
				$this->load->model('mTrsKartu');
				$xsSQL = $this->mTrsKartu->KetObat(trim($xRow->fd_tgl),$xKdMR);
				if ($sSQL->num_rows() > 0)
				{
					foreach ($xsSQL->result() as $xRow1)
					{
						if (trim($xKet) == '')
						{
							$xKet = trim($xRow1->fs_nm_barang).' ( '.trim($xRow1->fs_ket).' )';
						}
						else
						{
							$xKet = $xKet."</br>".trim($xRow1->fs_nm_barang).' ( '.trim($xRow1->fs_ket).' )';
						}
					}
				}
				
				if (trim($icd) == '')
				{
					if (trim($xRow->fs_kd_icd) <> '')
					{
						$icd = trim($xRow->fs_kd_icd).' : '.trim($xRow->fs_nm_icd);
					}
				}
				else
				{
					if (trim($xRow->fs_kd_icd) <> '')
					{
						$icd = $icd."</br>".trim($xRow->fs_kd_icd).' : '.trim($xRow->fs_nm_icd);
					}
				}
				
				$xArr[] = array(
					'fd_tgl_periksa'	=> ascii_to_entities(trim($xRow->fd_tgl_periksa)),
					'fs_kd_reg'			=> ascii_to_entities(trim($xRow->fs_kd_reg)),
					'fs_anamnesa'		=> ascii_to_entities(trim($xRow->fs_anamnesa)),
					'fs_diagnosa'		=> ascii_to_entities(trim($xRow->fs_diagnosa)),
					'fs_icd'			=> ascii_to_entities(trim($icd)),
					
					'fs_tindakan'		=> ascii_to_entities(trim($xRow->fs_tindakan)),
					'fs_ket'			=> ascii_to_entities(trim($xKet))
				);
			}
		}
		
		echo json_encode($xArr);
	}
	
	function CekSimpan()
	{
		//change db
		$this->load->model('mMainModul');
		$this->mMainModul->ChangeDB(trim($this->session->userdata('gDB')));
		//eof change db
		
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
		
		$xKdReg = trim($this->input->post('fs_kd_reg'));
		
		if (trim($xKdReg) == '')
		{
			$xHasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Simpan Gagal, No.Registrasi kosong!!<br>silakan isi No.Registrasi terlebih dahulu...'
			);
			echo json_encode($xHasil);
		}
		else
		{
			$this->load->model('mTrsKartu');
			$sSQL = $this->mTrsKartu->CekKodeReg($xKdReg);
			
			if ($sSQL->num_rows() > 0)
			{
				$xHasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Simpan?'
				);
				echo json_encode($xHasil);
			}
			else
			{
				$xHasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Simpan Gagal, No.Registrasi tidak diketahui!!<br>Silakan coba lagi kemudian...'
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
		
		$xKdReg = trim($this->input->post('fs_kd_reg'));
		$xTglPeriksa = trim($this->input->post('fd_tgl_periksa'));
		$xJamPeriksa = trim($this->input->post('fs_jam_periksa'));
		$anamnesa = trim($this->input->post('fs_anamnesa'));
		$diagnosa = trim($this->input->post('fs_diagnosa'));
		
		$xTindakan = trim($this->input->post('fs_tindakan'));
		$xKdIcd = trim($this->input->post('fs_kd_icd'));
		$xNmIcd = trim($this->input->post('fs_nm_icd'));
		$xBiayaperiksa = trim($this->input->post('fn_biaya'));
		$xBiayaobat = trim($this->input->post('fn_obat'));
		
		$xTotal = trim($this->input->post('fn_total'));
		$xPrefix = trim($this->input->post('fd_prefix'));
		$xTglSimpan = trim($this->input->post('fd_simpan'));
		
		// update tx_reg
		$xData = array(
			'fb_periksa' => '1'
		);
		
		$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
					AND fs_kd_reg = '".trim($xKdReg)."'";
		
		$this->db->where($xWhere);
		$this->db->update('tx_reg', $xData);
		// Eof update tx_reg
		
		$xUpdate = false;
		$this->load->model('mTrsKartu');
		$sSQL = $this->mTrsKartu->cek_kdkartu($xKdReg);
		
		if ($sSQL->num_rows() > 0)
		{
			$xUpdate = true;
		}
		
		$xDt = array(
			'fs_kd_dokter'		=> trim($this->session->userdata('gID')),
			'fs_kd_reg'			=> trim($xKdReg),
			'fd_tgl_periksa'	=> trim($xTglPeriksa),
			'fs_jam_periksa'	=> trim($xJamPeriksa),
			'fs_anamnesa'		=> trim($anamnesa),
			
			'fs_diagnosa'		=> trim($diagnosa),
			'fs_tindakan'		=> trim($xTindakan),
			'fs_kd_icd'			=> trim($xKdIcd),
			'fs_nm_icd'			=> trim($xNmIcd),
			'fn_biaya'			=> trim($xBiayaperiksa),
			
			'fn_obat'			=> trim($xBiayaobat),
			'fn_total'			=> trim($xTotal),
		);
		
		if ($xUpdate == false)
		{
			if (trim($xKdReg) <> '')
			{
				$xDt2 = array(
					'fs_usr'	=> trim($this->session->userdata('gUser')),
					'fd_usr'	=> trim($xTglSimpan),
					'fs_upd'	=> trim($this->session->userdata('gUser')),
					'fd_upd'	=> trim($xTglSimpan)
				);
				$xData = array_merge($xDt,$xDt2);
				
				$this->db->insert('tx_kartu', $xData);
			}
			else
			{
				$xHasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Simpan Gagal, No.Registrasi tidak diketahui!!<br>Silakan coba lagi kemudian...'
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
					AND	fs_kd_reg = '".trim($xKdReg)."'";
			
			$this->db->where($xWhere);
			$this->db->update('tx_kartu', $xData);
		}
		
		
		//Hapus detail
		$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	fs_kd_reg = '".trim($xKdReg)."'";
		
		$this->db->where($xWhere);
		$this->db->delete('tx_resep_detail');
		//eof Hapus detail
		
		$xKdBarang = explode('|', trim($this->input->post('fs_kd_barang')));
		$xKet = explode('|', trim($this->input->post('fs_ket')));
		
		$xJml = count($xKdBarang) - 1;
		if ($xJml != 0)
		{
			for ($i=1; $i<=$xJml; $i++)
			{
				$xData = array(
					'fs_kd_dokter'	=> trim($this->session->userdata('gID')),
					'fs_kd_reg'		=> trim($xKdReg),
					'fs_kd_barang'	=> trim($xKdBarang[$i]),
					'fs_ket'		=> trim($xKet[$i])
				);
				
				$this->db->insert('tx_resep_detail', $xData);
			}
		}
		
		if ($xUpdate == false)
		{
			$xHasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Simpan Sukses'
			);
			echo json_encode($xHasil);
		}
		else
		{
			$xHasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Simpan Update Sukses'
			);
			echo json_encode($xHasil);
		}
	}
	
	function CekHapus()
	{
		//change db
		$this->load->model('mMainModul');
		$this->mMainModul->ChangeDB(trim($this->session->userdata('gDB')));
		//eof change db
		
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
				$xHasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Apakah Anda yakin untuk menghapus?'
				);
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
		//change db
		$this->load->model('mMainModul');
		$this->mMainModul->ChangeDB(trim($this->session->userdata('gDB')));
		//eof change db
		
		$xKdReg = trim($this->input->post('fs_kd_reg'));
		
		// update tx_reg
		$xData = array(
			'fb_periksa' => '0'
		);
		
		$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
					AND fs_kd_reg = '".trim($xKdReg)."'";
		
		$this->db->where($xWhere);
		$this->db->update('tx_reg', $xData);
		// Eof update tx_reg
		
		// Hapus reg
		$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
					AND fs_kd_reg = '".trim($xKdReg)."'";
		
		$this->db->where($xWhere);
		$this->db->delete('tx_kartu');
		// Eof Hapus reg
		
		// Hapus resep
		$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
					AND fs_kd_reg = '".trim($xKdReg)."'";
		
		$this->db->where($xWhere);
		$this->db->delete('tx_resep_detail');
		// Eof Hapus resep
		
		$xHasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Hapus Sukses'
		);
		echo json_encode($xHasil);
	}
}
?>