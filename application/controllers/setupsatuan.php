<?php

class SetupSatuan extends CI_Controller
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
			$this->load->view('vsetupsatuan');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function KodeSatuan()
	{
		$xKdSatuan = trim($this->input->post('fs_kd_satuan'));
		$xNmSatuan = trim($this->input->post('fs_nm_satuan'));
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodeSatuanAll($xKdSatuan,$xNmSatuan);
		$xTotal = $sSQL->num_rows();
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodeSatuan($xKdSatuan,$xNmSatuan,$nStart,$nLimit);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_satuan'	=> ascii_to_entities(trim($xRow->fs_kd_satuan)),
					'fs_nm_satuan'	=> ascii_to_entities(trim($xRow->fs_nm_satuan)),
					'fb_aktif'	=> ascii_to_entities(trim($xRow->fb_aktif)),
					'fs_status'	=> ascii_to_entities(trim($xRow->fs_status))
				);
			}
		}
		echo '({"total":"'.$xTotal.'","hasil":'.json_encode($xArr).'})';
	}
	
	function KodeSatuanAktif()
	{
		$xKdSatuan = trim($this->input->post('fs_kd_satuan'));
		$xNmSatuan = trim($this->input->post('fs_nm_satuan'));
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodeSatuanAktifAll($xKdSatuan,$xNmSatuan);
		$xTotal = $sSQL->num_rows();
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodeBarangSatuan($xKdSatuan,$xNmSatuan,$nStart,$nLimit);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_satuan'	=> ascii_to_entities(trim($xRow->fs_kd_satuan)),
					'fs_nm_satuan'	=> ascii_to_entities(trim($xRow->fs_nm_satuan))
				);
			}
		}
		echo '({"total":"'.$xTotal.'","hasil":'.json_encode($xArr).'})';
	}
	
	function GridDetil()
	{
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mSetupSatuan');
		$sSQL = $this->mSetupSatuan->list_satuan_all();
		$xTotal = $sSQL->num_rows();
		
		$this->load->model('mSetupSatuan');
		$sSQL = $this->mSetupSatuan->list_satuan($nStart,$nLimit);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_satuan'	=> ascii_to_entities(trim($xRow->fs_kd_satuan)),
					'fs_nm_satuan'	=> ascii_to_entities(trim($xRow->fs_nm_satuan)),
					'fb_aktif'		=> ascii_to_entities(trim($xRow->fb_aktif))
				);
			}
		}
		echo '({"total":"'.$xTotal.'","hasil":'.json_encode($xArr).'})';
	}
	
	function CekSimpan()
	{
		$kdsat = trim($this->input->post('fs_kd_satuan'));
		
		if (trim($kdsat) == '')
		{
			$xHasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Simpan Gagal, Kode Satuan kosong!!<br>silakan isi kode satuan terlebih dahulu...'
			);
			echo json_encode($xHasil);
		}
		else
		{
			$this->load->model('mSetupSatuan');
			$sSQL = $this->mSetupSatuan->CekKodeSatuan($kdsat);
			
			if ($sSQL->num_rows() > 0)
			{
				$xHasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Kode Satuan sudah ada, Apakah Anda ingin meng-update?'
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
		$xKdSatuan = trim($this->input->post('fs_kd_satuan'));
		$xKdAktif = trim($this->input->post('fb_aktif'));
		$xNmSatuan = trim($this->input->post('fs_nm_satuan'));
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
		$this->load->model('mSetupSatuan');
		$sSQL = $this->mSetupSatuan->CekKodeSatuan($xKdSatuan);
		
		if ($sSQL->num_rows() > 0)
		{
			$xUpdate = true;
		}
		
		$xDt = array(
			'fs_kd_dokter'	=> trim($this->session->userdata('gID')),
			'fs_kd_satuan'	=> trim($xKdSatuan),
			'fb_aktif'		=> trim($xKdAktif),
			'fs_nm_satuan'	=> trim($xNmSatuan)
		);
		
		if ($xUpdate == false)
		{
			if (trim($xKdSatuan) <> '')
			{
				$xDt2 = array(
					'fs_usr'	=> trim($this->session->userdata('gUser')),
					'fd_usr'	=> trim($xTglSimpan),
					'fs_upd'	=> trim($this->session->userdata('gUser')),
					'fd_upd'	=> trim($xTglSimpan)
				);
				$xData = array_merge($xDt,$xDt2);
				
				$this->db->insert('tm_satuan', $xData);
				
				$xHasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Saving Success'
				);
				echo json_encode($xHasil);
			}
			else
			{
				$xHasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Simpan Gagal, Kode Satuan tidak diketahui!!<br>Silakan coba lagi kemudian...'
				);
				echo json_encode($xHasil);
			}
		}
		else
		{
			$xDt2 = array(
				'fs_upd' => trim($this->session->userdata('gUser')),
				'fd_upd' => trim($xTglSimpan)
			);
			$xData = array_merge($xDt,$xDt2);
			
			$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
					AND	fs_kd_satuan = '".trim($xKdSatuan)."'";
			
			$this->db->where($xWhere);
			$this->db->update('tm_satuan', $xData);
			
			$xHasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Simpan Update Sukses'
			);
			echo json_encode($xHasil);
		}
	}
	
	function CekHapus()
	{
		$kdsat = trim($this->input->post('fs_kd_satuan'));
		
		if (trim($kdsat) == '')
		{
			$xHasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Hapus Gagal, Kode Satuan kosong!!<br>silakan isi kode satuan terlebih dahulu...'
			);
			echo json_encode($xHasil);
		}
		else
		{
			$this->load->model('mSetupBarang');
			$sSQL = $this->mSetupBarang->CekKodeSatuan($kdsat);
			
			if ($sSQL->num_rows() > 0)
			{
				$sSQL = $sSQL->row();
				$xNmBarang = $sSQL->fs_nm_barang;
				
				$xHasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Hapus Gagal, Kode Satuan ini digunakan pada barang "'.trim($xNmBarang).'"!!'
				);
				echo json_encode($xHasil);
				return;
			}
			
			$this->load->model('mSetupSatuan');
			$sSQL = $this->mSetupSatuan->CekKodeSatuan($kdsat);
			
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
					'hasil'		=> 'Hapus Gagal, Kode Satuan tidak diketahui!!'
				);
				echo json_encode($xHasil);
			}
		}
	}
	
	function Hapus()
	{
		$xKdSatuan = trim($this->input->post('fs_kd_satuan'));
		
		$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
					AND	fs_kd_satuan = '".trim($xKdSatuan)."'";
		
		$this->db->where($xWhere);
		$this->db->delete('tm_satuan');
		
		$xHasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Hapus Sukses'
		);
		echo json_encode($xHasil);
	}
}
?>