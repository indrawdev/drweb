<?php

class MSetupPetugas extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function CekKodePetugas($xKdUser)
	{
		$xSQL = ("
			SELECT	fs_kd_user, fs_nm_user, fs_password
			FROM 	tm_user
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	fs_kd_user = '".trim($xKdUser)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function CekKodePetugas2($xKdUser)
	{
		$xSQL = ("
			SELECT	fs_kd_user, fs_password
			FROM 	tm_dokter
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	fs_kd_user = '".trim($xKdUser)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeDokter()
	{
		$xSQL = ("
			SELECT	fs_kd_dokter, fs_nm_dokter, fs_alamat,
					fs_tlp, fs_email, fs_nm_db,
					fs_kd_user, fs_password, fs_kota,
					fs_kd_paket, fs_kd_akses,
					fd_reg, fd_jatuh_tempo
			FROM 	tm_dokter
			WHERE 	fs_kd_dokter = '".trim($this->session->userdata('gID'))."' LIMIT 1
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>