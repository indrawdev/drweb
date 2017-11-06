<?php

class MSetupDistributor extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function CekKodeDistributor($xKdDist)
	{
		$xSQL = ("
			SELECT	fs_kd_dist
			FROM	tm_distributor
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	fs_kd_dist = '".$xKdDist."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function ListDistributorAll()
	{
		$xSQL = ("
			SELECT	fs_kd_dist, fs_nm_dist,
					fs_alamat, fs_kota, fs_tlp,
					fs_kontak, fb_aktif
			FROM 	tm_distributor
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
			ORDER BY fs_kd_dist, fs_nm_dist
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function ListDistributor($nStart,$nLimit)
	{
		$xSQL = ("
			SELECT	fs_kd_dist, fs_nm_dist,
					fs_alamat, fs_kota, fs_tlp,
					fs_kontak, fb_aktif
			FROM 	tm_distributor
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
			ORDER BY fs_kd_dist, fs_nm_dist LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>