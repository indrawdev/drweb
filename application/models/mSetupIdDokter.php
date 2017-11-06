<?php

class mSetupIdDokter extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function KodeDokter()
	{
		$xSQL = ("
			SELECT	fs_nm_dokter, fs_alamat, fs_kota,
					fs_tlp, fs_email
			FROM	tm_dokter
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>