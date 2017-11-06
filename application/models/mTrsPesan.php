<?php

class MTrsPesan extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function CekKodePesan($xKdPesan)
	{
		$xSQL = ("
			SELECT	fs_kd_pesan
			FROM	tx_pesan
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	fs_kd_pesan = '".$xKdPesan."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_nama($xNama,$xAlamat)
	{
		$xSQL = ("
			SELECT	fs_kd_mr, fs_nm_pasien, fs_alamat
			FROM	tm_mr
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	fs_nm_pasien = '".trim($xNama)."'
				AND	fs_alamat = '".trim($xAlamat)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>