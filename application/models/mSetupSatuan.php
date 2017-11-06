<?php

class MSetupSatuan extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function CekKodeSatuan($xKdSatuan)
	{
		$xSQL = ("
			SELECT	fs_kd_satuan
			FROM	tm_satuan
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	fs_kd_satuan = '".$xKdSatuan."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function list_satuan_all()
	{
		$xSQL = ("
			SELECT	fs_kd_satuan, fs_nm_satuan, fb_aktif
			FROM 	tm_satuan
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
			ORDER BY fs_kd_satuan, fs_nm_satuan
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function list_satuan($nStart,$nLimit)
	{
		$xSQL = ("
			SELECT	fs_kd_satuan, fs_nm_satuan, fb_aktif
			FROM 	tm_satuan
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
			ORDER BY fs_kd_satuan, fs_nm_satuan LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>