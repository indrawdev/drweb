<?php

class MSetupGantiPass extends CI_Model
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
}
?>