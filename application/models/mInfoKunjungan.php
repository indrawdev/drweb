<?php

class MInfoKunjungan extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function KunjunganAll($xTgl,$xTgl2)
	{
		$xSQL = ("
			SELECT  DISTINCT DATE_FORMAT(a.fd_tgl_periksa,'%d-%m-%Y') fd_tgl_periksa,
					a.fd_tgl_periksa fd_tgl, a.fs_kd_reg,
					IFNULL(c.fs_nm_pasien, '') fs_nm_pasien,
					IFNULL(c.fs_kd_mr, '') fs_kd_mr,
					IFNULL(b.fs_ket_umur, '') fs_umur,
					c.fb_sex fs_kd_jk, CASE c.fb_sex WHEN '0' THEN 'LAKI - LAKI' ELSE 'PEREMPUAN' END fs_nm_jk,
					REPLACE(REPLACE(REPLACE(a.fs_anamnesa, '\n', '</br>'), '\r', '</br>'), '</br></br>', '</br>') fs_anamnesa,
					REPLACE(REPLACE(REPLACE(a.fs_diagnosa, '\n', '</br>'), '\r', '</br>'), '</br></br>', '</br>') fs_diagnosa,
					REPLACE(REPLACE(REPLACE(a.fs_tindakan, '\n', '</br>'), '\r', '</br>'), '</br></br>', '</br>') fs_tindakan
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
	
	function Kunjungan($xTgl,$xTgl2,$nStart,$nLimit)
	{
		$xSQL = ("
			SELECT  DISTINCT DATE_FORMAT(a.fd_tgl_periksa,'%d-%m-%Y') fd_tgl_periksa,
					a.fd_tgl_periksa fd_tgl, a.fs_kd_reg,
					IFNULL(c.fs_nm_pasien, '') fs_nm_pasien,
					IFNULL(c.fs_kd_mr, '') fs_kd_mr,
					IFNULL(b.fs_ket_umur, '') fs_umur,
					c.fb_sex fs_kd_jk, CASE c.fb_sex WHEN '0' THEN 'LAKI - LAKI' ELSE 'PEREMPUAN' END fs_nm_jk,
					REPLACE(REPLACE(REPLACE(a.fs_anamnesa, '\n', '</br>'), '\r', '</br>'), '</br></br>', '</br>') fs_anamnesa,
					REPLACE(REPLACE(REPLACE(a.fs_diagnosa, '\n', '</br>'), '\r', '</br>'), '</br></br>', '</br>') fs_diagnosa,
					REPLACE(REPLACE(REPLACE(a.fs_tindakan, '\n', '</br>'), '\r', '</br>'), '</br></br>', '</br>') fs_tindakan
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