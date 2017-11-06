<?php

class MInfoAntrian extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function KodeRegAll($xTgl)
	{
		$xSQL = ("
			SELECT	a.fs_kd_pesan, a.fs_kd_reg, a.fs_jam_masuk fs_jam,
					b.fs_kd_mr, b.fs_nm_pasien, IFNULL(b.fs_alamat, '') fs_alamat,
					CASE a.fb_periksa WHEN '0' THEN '' ELSE 'SUDAH' END fb_periksa
			FROM	tx_reg a
			LEFT JOIN tm_mr b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_mr = b.fs_kd_mr
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	a.fd_tgl_masuk <= '".trim($xTgl)."'
				AND	a.fd_tgl_masuk >= DATE_ADD('".trim($xTgl)."', INTERVAL -1 DAY)
			ORDER BY a.fs_kd_reg DESC, b.fs_nm_pasien, b.fs_kd_mr
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeReg($xTgl,$nStart,$nLimit)
	{
		$xSQL = ("
			SELECT	a.fs_kd_pesan, a.fs_kd_reg, a.fs_jam_masuk fs_jam,
					b.fs_kd_mr, b.fs_nm_pasien, IFNULL(b.fs_alamat, '') fs_alamat,
					CASE a.fb_periksa WHEN '0' THEN '' ELSE 'SUDAH' END fb_periksa
			FROM	tx_reg a
			LEFT JOIN tm_mr b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_mr = b.fs_kd_mr
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	a.fd_tgl_masuk <= '".trim($xTgl)."'
				AND	a.fd_tgl_masuk >= DATE_ADD('".trim($xTgl)."', INTERVAL -1 DAY)
			ORDER BY a.fs_kd_reg DESC, b.fs_nm_pasien, b.fs_kd_mr LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}	
}
?>