<?php

class MTrsReg extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function CekKodeMR($xKdMR)
	{
		$xSQL = ("
			SELECT	fs_kd_mr
			FROM	tm_mr
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	fs_kd_mr = '".$xKdMR."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function CekKodeReg($xKdReg)
	{
		$xSQL = ("
			SELECT	fs_kd_reg, fb_periksa
			FROM	tx_reg
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	fs_kd_reg = '".$xKdReg."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>