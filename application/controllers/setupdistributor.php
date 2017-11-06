<?php

class SetupDistributor extends CI_Controller
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
			$this->load->view('vsetupdistributor');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function KodeDistributor()
	{
		$xKdDistributor = trim($this->input->post('fs_kd_dist'));
		$xNmDistributor = trim($this->input->post('fs_nm_dist'));
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodeDistributorAll($xKdDistributor,$xNmDistributor);
		$xTotal = $sSQL->num_rows();
		
		$sSQL = $this->mSearch->KodeDistributor($xKdDistributor,$xNmDistributor,$nStart,$nLimit);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_dist'	=> ascii_to_entities(trim($xRow->fs_kd_dist)),
					'fs_nm_dist'	=> ascii_to_entities(trim($xRow->fs_nm_dist)),
					'fs_alamat'		=> ascii_to_entities(trim($xRow->fs_alamat)),
					'fs_kota'		=> ascii_to_entities(trim($xRow->fs_kota)),
					'fs_tlp'		=> ascii_to_entities(trim($xRow->fs_tlp)),
					
					'fs_kontak'		=> ascii_to_entities(trim($xRow->fs_kontak)),
					'fb_aktif'		=> ascii_to_entities(trim($xRow->fb_aktif)),
					'fs_status'		=> ascii_to_entities(trim($xRow->fs_status))
				);
			}
		}
		echo '({"total":"'.$xTotal.'","hasil":'.json_encode($xArr).'})';
	}
	
	function KodeDistributorAktif()
	{
		$xKdDistributor = trim($this->input->post('fs_kd_dist'));
		$xNmDistributor = trim($this->input->post('fs_nm_dist'));
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mSearch');
		$sSQL = $this->mSearch->KodeDistributorAktifAll($xKdDistributor,$xNmDistributor);
		$xTotal = $sSQL->num_rows();
		
		$sSQL = $this->mSearch->KodeDistributorAktif($xKdDistributor,$xNmDistributor,$nStart,$nLimit);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_dist'	=> ascii_to_entities(trim($xRow->fs_kd_dist)),
					'fs_nm_dist'	=> ascii_to_entities(trim($xRow->fs_nm_dist))
				);
			}
		}
		echo '({"total":"'.$xTotal.'","hasil":'.json_encode($xArr).'})';
	}
	
	function GridDetil()
	{
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mSetupDistributor');
		$sSQL = $this->mSetupDistributor->ListDistributorAll();
		$xTotal = $sSQL->num_rows();
		
		$sSQL = $this->mSetupDistributor->ListDistributor($nStart,$nLimit);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_dist'	=> ascii_to_entities(trim($xRow->fs_kd_dist)),
					'fs_nm_dist'	=> ascii_to_entities(trim($xRow->fs_nm_dist)),
					'fs_alamat'		=> ascii_to_entities(trim($xRow->fs_alamat)),
					'fs_kota'		=> ascii_to_entities(trim($xRow->fs_kota)),
					'fs_tlp'		=> ascii_to_entities(trim($xRow->fs_tlp)),
					
					'fs_kontak'		=> ascii_to_entities(trim($xRow->fs_kontak)),
					'fb_aktif'		=> ascii_to_entities(trim($xRow->fb_aktif))
				);
			}
		}
		echo '({"total":"'.$xTotal.'","hasil":'.json_encode($xArr).'})';
	}
	
	function CekSimpan()
	{
		$xKdDistributor = trim($this->input->post('fs_kd_dist'));
		
		if (trim($xKdDistributor) == '')
		{
			$xHasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Simpan Gagal, Kode Distributor kosong!!<br>silakan isi kode distributor terlebih dahulu...'
			);
			echo json_encode($xHasil);
		}
		else
		{
			$this->load->model('mSetupDistributor');
			$sSQL = $this->mSetupDistributor->CekKodeDistributor($xKdDistributor);
			
			if ($sSQL->num_rows() > 0)
			{
				$xHasil = array(
					'sukses'	=> true,
					'hasil'		=> 'Kode Distributor sudah ada, Apakah Anda ingin meng-update?'
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
		$xKdDistributor = trim($this->input->post('fs_kd_dist'));
		$xKdAktif = trim($this->input->post('fb_aktif'));
		$xNmDistributor = trim($this->input->post('fs_nm_dist'));
		$xAlamat = trim($this->input->post('fs_alamat'));
		$xKota = trim($this->input->post('fs_kota'));
		$xTelp = trim($this->input->post('fs_tlp'));
		$xKontak = trim($this->input->post('fs_kontak'));
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
		$this->load->model('mSetupDistributor');
		$sSQL = $this->mSetupDistributor->CekKodeDistributor($xKdDistributor);
		
		if ($sSQL->num_rows() > 0)
		{
			$xUpdate = true;
		}
		
		$xDt = array(
			'fs_kd_dokter'	=> trim($this->session->userdata('gID')),
			'fs_kd_dist'	=> trim($xKdDistributor),
			'fb_aktif'		=> trim($xKdAktif),
			'fs_nm_dist'	=> trim($xNmDistributor),
			'fs_alamat'		=> trim($xAlamat),
			
			'fs_kota'		=> trim($xKota),
			'fs_tlp'		=> trim($xTelp),
			'fs_kontak'		=> trim($xKontak)
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
				
				$this->db->insert('tm_distributor', $xData);
				
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
					'hasil'		=> 'Simpan Gagal, Kode Distributor tidak diketahui!!<br>Silakan coba lagi kemudian...'
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
					AND	fs_kd_dist = '".trim($xKdDistributor)."'";
			
			$this->db->where($xWhere);
			$this->db->update('tm_distributor', $xData);
			
			$xHasil = array(
				'sukses'	=> true,
				'hasil'		=> 'Simpan Update Sukses'
			);
			echo json_encode($xHasil);
		}
	}
	
	function CekHapus()
	{
		$xKdDistributor = trim($this->input->post('fs_kd_dist'));
		
		if (trim($xKdDistributor) == '')
		{
			$xHasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Hapus Gagal, Kode Distributor kosong!!<br>silakan isi kode distributor terlebih dahulu...'
			);
			echo json_encode($xHasil);
		}
		else
		{
			$this->load->model('mSetupBarang');
			$sSQL = $this->mSetupBarang->CekKodeDistributor($xKdDistributor);
			
			if ($sSQL->num_rows() > 0)
			{
				$sSQL = $sSQL->row();
				$xNmBarang = $sSQL->fs_nm_barang;
				
				$xHasil = array(
					'sukses'	=> false,
					'hasil'		=> 'Hapus Gagal, Kode distributor ini digunakan pada barang "'.trim($xNmBarang).'"!!'
				);
				echo json_encode($xHasil);
				return;
			}
			
			$this->load->model('mSetupDistributor');
			$sSQL = $this->mSetupDistributor->CekKodeDistributor($xKdDistributor);
			
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
					'hasil'		=> 'Hapus Gagal, Kode Distributor tidak diketahui!!'
				);
				echo json_encode($xHasil);
			}
		}
	}
	
	function Hapus()
	{
		$xKdDistributor = trim($this->input->post('fs_kd_dist'));
		
		$xWhere = "fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
					AND	fs_kd_dist = '".trim($xKdDistributor)."'";
		
		$this->db->where($xWhere);
		$this->db->delete('tm_distributor');
		
		$xHasil = array(
			'sukses'	=> true,
			'hasil'		=> 'Hapus Sukses'
		);
		echo json_encode($xHasil);
	}
}
?>