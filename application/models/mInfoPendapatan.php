<?php

class MInfoPendapatan extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function PendapatanAll($xTgl,$xTgl2)
	{
		$xSQL = ("
			SELECT  a.fs_kd_reg, DATE_FORMAT(a.fd_tgl_periksa,'%d-%m-%Y') fd_tgl_periksa,
					IFNULL(c.fs_nm_pasien, '') fs_nm_pasien, IFNULL(c.fs_alamat, '') fs_alamat,
					a.fn_biaya, a.fn_obat, a.fn_total
			FROM  	tx_kartu a
			INNER JOIN tx_reg b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_reg = b.fs_kd_reg
			INNER JOIN tm_mr c ON a.fs_kd_dokter = c.fs_kd_dokter
				AND	b.fs_kd_mr = c.fs_kd_mr
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	a.fd_tgl_periksa >= '".trim($xTgl)."'
				AND	a.fd_tgl_periksa <= '".trim($xTgl2)."'
			ORDER BY c.fs_nm_pasien
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function Pendapatan($xTgl,$xTgl2,$nStart,$nLimit)
	{
		$xSQL = ("
			SELECT  a.fs_kd_reg, DATE_FORMAT(a.fd_tgl_periksa,'%d-%m-%Y') fd_tgl_periksa,
					IFNULL(c.fs_nm_pasien, '') fs_nm_pasien, IFNULL(c.fs_alamat, '') fs_alamat,
					a.fn_biaya, a.fn_obat, a.fn_total
			FROM  	tx_kartu a
			INNER JOIN tx_reg b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_reg = b.fs_kd_reg
			INNER JOIN tm_mr c ON a.fs_kd_dokter = c.fs_kd_dokter
				AND	b.fs_kd_mr = c.fs_kd_mr
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	a.fd_tgl_periksa >= '".trim($xTgl)."'
				AND	a.fd_tgl_periksa <= '".trim($xTgl2)."'
			ORDER BY c.fs_nm_pasien LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>