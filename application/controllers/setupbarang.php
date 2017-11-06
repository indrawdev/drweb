<?php

class SetupBarang extends CI_Controller
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
			$this->load->view('vsetupbarang');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function KodeBarang()
	{
		$xKdBarang = trim($this->input->post('fs_kd_barang'));
		$xNmBarang = trim($this->input->post('fs_nm_barang'));
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodeBarangAll($xKdBarang,$xNmBarang);
		$xTotal = $sSQL->num_rows();
		
		$sSQL = $this->mSearch->KodeBarang($xKdBarang,$xNmBarang,$nStart,$nLimit);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_barang'	=> ascii_to_entities(trim($xRow->fs_kd_barang)),
					'fs_nm_barang'	=> ascii_to_entities(trim($xRow->fs_nm_barang)),
					'fs_kd_satuan'	=> ascii_to_entities(trim($xRow->fs_kd_satuan)),
					'fs_nm_satuan'	=> ascii_to_entities(trim($xRow->fs_nm_satuan)),
					'fs_kd_dist'	=> ascii_to_entities(trim($xRow->fs_kd_dist)),
					
					'fs_nm_dist'	=> ascii_to_entities(trim($xRow->fs_nm_dist)),
					'fs_ket'		=> ascii_to_entities(trim($xRow->fs_ket)),
					'fb_aktif'		=> ascii_to_entities(trim($xRow->fb_aktif)),
					'fs_status'		=> ascii_to_entities(trim($xRow->fs_status))
				);
			}
		}
		echo '({"total":"'.$xTotal.'","hasil":'.json_encode($xArr).'})';
	}
	
	function KodeBarangAktif()
	{
		$xKdBarang = trim($this->input->post('fs_kd_barang'));
		$xNmBarang = trim($this->input->post('fs_nm_barang'));
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodeBarangAktifAll($xKdBarang,$xNmBarang);
		$xTotal = $sSQL->num_rows();
		
		$sSQL = $this->mSearch->KodeBarangAktif($xKdBarang,$xNmBarang,$nStart,$nLimit);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_barang'	=> ascii_to_entities(trim($xRow->fs_kd_barang)),
					'fs_nm_barang'	=> ascii_to_entities(trim($xRow->fs_nm_barang))
				);
			}
		}
		echo '({"total":"'.$xTotal.'","hasil":'.json_encode($xArr).'})';
	}
	
	function GridDetil()
	{
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mSetupBarang');
		$sSQL = $this->mSetupBarang->ListBarangAll();
		$xTotal = $sSQL->num_rows();
		
		$sSQL = $this->mSetupBarang->ListBarang($nStart,$nLimit);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_barang'	=> ascii_to_entities(trim($xRow->fs_kd_barang)),
					'fs_nm_barang'	=> ascii_to_entities(trim($xRow->fs_nm_barang)),
					'fs_nm_satuan'	=> ascii_to_entities(trim($xRow->fs_nm_satuan)),
					'fs_nm_dist'	=> ascii_to_entities(trim($xRow->fs_nm_dist)),
					'fs_ket'		=> ascii_to_entities(trim($xRow->fs_ket)),
					
					'fb_aktif'		=> ascii_to_entities(trim($xRow->fb_aktif))
				);
			}
		}
		echo '({"total":"'.$xTotal.'","hasil":'.json_encode($xArr).'})';
	}
	
	function CekSimpan()
	{
		$xKdBarang = trim($this->input->post('fs_kd_barang'));
		
		if (trim($xKdBarang) == '')
		{
			$xHasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Simpan Gagal, Kode Barang kosong!!<br>silakan isi kode barang terlebih dahulu...'
			);
			echo json_encode($xHasil);
		}
		else
		{
			$this->load->model('mSetupBarang');
			$sSQL = $this->mSetupBarang->CekKodeBarang($xKdBarang);
			
			if ($sSQL->num_rows() > 0)
			{
				$xHasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Kode Barang sudah ada, Apakah Anda ingin meng-update?'
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
		$xKdBarang = trim($this->input->post('fs_kd_barang'));
		$xKdAktif = trim($this->input->post('fb_aktif'));
		$xNmBarang = trim($this->input->post('fs_nm_barang'));
		$xKdSatuan = trim($this->input->post('fs_kd_satuan'));
		$xKdDistributor = trim($this->input->post('fs_kd_dist'));
		$xKet = trim($this->input->post('fs_ket'));
		$xTglSimpan = trim($this->input->post('fd_simpan'));
		
		if (trim($xKdAktif) == 'true')
		{
			$xKdAktif = 1;
		}
		else
		{
			$xKdAktif = 0;
		}
		
		$xUpdate = false;
		$this->load->model('mSetupBarang');
		$sSQL = $this->mSetupBarang->CekKodeBarang($xKdBarang);
		
		if ($sSQL->num_rows() > 0)
		{
			$xUpdate = true;
		}
		
		$xDt = array(
			'fs_kd_dokter'	=> trim($this->session->userdata('gID')),
			'fs_kd_barang'	=> trim($xKdBarang),
			'fb_aktif'		=> trim($xKdAktif),
			'fs_nm_barang'	=> trim($xNmBarang),
			'fs_kd_satuan'	=> trim($xKdSatuan),
			
			'fs_kd_dist'	=> trim($xKdDistributor),
			'fs_ket'		=> trim($xKet)
		);
		
		if ($xUpdate == false)
		{
			if (trim($xKdDistributor) <> '')
			{
				$xDt2 = array(
					'fs_usr'	=> trim($this->session->userdata('gUser')),
					'fd_usr'	=> trim($xTglSimpan),
					'fs_upd'	=> trim($this->session->userdata('gUser')),
					'fd_upd'	=> trim($xTglSimpan)
				);
				$xData = array_merge($xDt,$xDt2);
				
				$this->db->insert('tm_barang', $xData);
				
				$xHasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Simpan Sukses'
				);
				echo json_encode($xHasil);
			}
			else
			{
				$xHasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Simpan Gagal, Kode Barang tidak diketahui!!<br>Silakan coba lagi kemudian...'
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
					AND	fs_kd_barang = '".trim($xKdBarang)."'";
			
			$this->db->where($xWhere);
			$this->db->update('tm_barang', $xData);
			
			$xHasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Simpan Update Sukses'
			);
			echo json_encode($xHasil);
		}
	}
	
	function CekHapus()
	{
		$xKdBarang = trim($this->input->post('fs_kd_barang'));
		
		if (trim($xKdBarang) == '')
		{
			$xHasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Hapus Gagal, Kode Barang kosong!!<br>silakan isi kode barang terlebih dahulu...'
			);
			echo json_encode($xHasil);
		}
		else
		{
			$this->load->model('mSetupBarang');
			$sSQL = $this->mSetupBarang->cek_kartu($xKdBarang);
			
			if ($sSQL->num_rows() > 0)
			{
				$sSQL = $sSQL->row();
				$xKdMR = $sSQL->fs_kd_mr;
				$xNmPasien = $sSQL->fs_nm_pasien;
				$xTgl = $sSQL->fd_tgl_periksa;
				$xJam = $sSQL->fs_jam_periksa;
				
				$xHasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Hapus Gagal, Kode Barang ini digunakan pada pemeriksaan pasien : <br>'
							.trim($xKdMR).' : '.trim($xNmPasien).'<br> tgl : '.trim($xTgl).', jam : '.trim($xJam)
				);
				echo json_encode($xHasil);
				return;
			}
			
			$this->load->model('mSetupBarang');
			$sSQL = $this->mSetupBarang->CekKodeBarang($xKdBarang);
			
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
					'hasil'		=> 'Hapus Gagal, Kode Barang tidak diketahui!!'
				);
				echo json_encode($xHasil);
			}
		}
	}
	
	function Hapus()
	{
		$xKdBarang = trim($this->input->post('fs_kd_satuan'));
		$xTglSimpan = trim($this->input->post('fd_simpan'));
		
		$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
					AND	fs_kd_barang = '".trim($xKdBarang)."'";
		
		$this->db->where($xWhere);
		$this->db->delete('tm_barang');
		
		$xHasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Hapus Sukses'
		);
		echo json_encode($xHasil);
	}
}
?>