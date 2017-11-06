<?php

class InfoKunjungan extends CI_Controller
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
			$this->load->view('vinfokunjungan');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function GridDetil()
	{
		$xTgl = trim($this->input->post('fd_tgl'));
		$xTgl2 = trim($this->input->post('fd_tgl2'));
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mInfoKunjungan');
		$sSQL = $this->mInfoKunjungan->KunjunganAll($xTgl,$xTgl2);
		$xTotal = $sSQL->num_rows();
		
		$sSQL = $this->mInfoKunjungan->Kunjungan($xTgl,$xTgl2,$nStart,$nLimit);
		
		$xArr = array();
		$xKet = '';
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xKet = '';
				$this->load->model('mTrsKartu');
				$xsSQL = $this->mTrsKartu->KetObat(trim($xRow->fd_tgl),trim($xRow->fs_kd_mr));
				if ($xsSQL->num_rows() > 0)
				{
					foreach ($xsSQL->result() as $xRow1)
					{
						if (trim($xKet) == '')
						{
							if (trim($xRow1->fs_ket) <> '')
							{
								$xKet = trim($xRow1->fs_nm_barang).' ( '.trim($xRow1->fs_ket).' )';
							}
							else
							{
								$xKet = trim($xRow1->fs_nm_barang);
							}
						}
						else
						{
							if (trim($xRow1->fs_ket) <> '')
							{
								$xKet = $xKet."</br>".trim($xRow1->fs_nm_barang).' ( '.trim($xRow1->fs_ket).' )';
							}
							else
							{
								$xKet = $xKet."</br>".trim($xRow1->fs_nm_barang);
							}
						}
					}
				}
				
				$xArr[] = array(
					'fs_nm_pasien'		=> ascii_to_entities(trim($xRow->fs_nm_pasien)),
					'fs_kd_reg'			=> ascii_to_entities(trim($xRow->fs_kd_reg)."</br>".trim($xRow->fs_kd_mr)."</br>".trim($xRow->fd_tgl_periksa)),
					'fs_umur'			=> ascii_to_entities(trim($xRow->fs_umur)."</br>".trim($xRow->fs_nm_jk)),
					'fs_anamnesa'		=> ascii_to_entities(trim($xRow->fs_anamnesa)),
					'fs_diagnosa'		=> ascii_to_entities(trim($xRow->fs_diagnosa)),
					'fs_tindakan'		=> ascii_to_entities(trim($xRow->fs_tindakan)),
					'fs_ket'			=> ascii_to_entities(trim($xKet))
				);
			}
		}
		
		echo json_encode($xArr);
	}
}
?>