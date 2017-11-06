<?php

class MTrsKartu extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function CekKodeReg($xKdReg)
	{
		$xSQL = ("
			SELECT	fs_kd_reg
			FROM	tx_reg
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	fs_kd_reg = '".$xKdReg."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_kdkartu($xKdReg)
	{
		$xSQL = ("
			SELECT	fs_kd_reg
			FROM	tx_kartu
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	fs_kd_reg = '".$xKdReg."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function ListBarang($xKdReg)
	{
		$xSQL = ("
			SELECT	a.fs_kd_barang, IFNULL(b.fs_nm_barang, '') fs_nm_barang,
					a.fs_ket
			FROM 	tx_resep_detail a
			LEFT JOIN tm_barang b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_barang = b.fs_kd_barang
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	a.fs_kd_reg = '".trim($xKdReg)."'
			ORDER BY a.fs_kd_barang, b.fs_nm_barang
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function ListHistori($xKdMR,$nStart,$nLimit)
	{
		$xSQL = ("
			SELECT  DATE_FORMAT(a.fd_tgl_periksa,'%d-%m-%Y') fd_tgl_periksa, a.fs_kd_reg,
					REPLACE(REPLACE(REPLACE(a.fs_anamnesa, '\n', '</br>'), '\r', '</br>'), '</br></br>', '</br>') fs_anamnesa,
					REPLACE(REPLACE(REPLACE(a.fs_diagnosa, '\n', '</br>'), '\r', '</br>'), '</br></br>', '</br>') fs_diagnosa,
					REPLACE(REPLACE(REPLACE(a.fs_tindakan, '\n', '</br>'), '\r', '</br>'), '</br></br>', '</br>') fs_tindakan,
					CONCAT(IFNULL(e.fs_nm_barang, ''), e.fs_ket) fs_ket
			FROM  	tx_kartu a
			INNER JOIN tx_reg b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_reg = b.fs_kd_reg
			INNER JOIN tm_mr c ON a.fs_kd_dokter = c.fs_kd_dokter
				AND	b.fs_kd_mr = c.fs_kd_mr
			INNER JOIN tx_resep_detail d ON a.fs_kd_dokter = d.fs_kd_dokter
				AND	a.fs_kd_reg = d.fs_kd_reg
			LEFT JOIN tm_barang e ON a.fs_kd_dokter = e.fs_kd_dokter
				AND	d.fs_kd_barang = e.fs_kd_barang
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	c.fs_kd_mr = '".trim($xKdMR)."'
			ORDER BY a.fd_tgl_periksa DESC LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function HistoriAll($xKdMR)
	{
		$xSQL = ("
			SELECT  DISTINCT DATE_FORMAT(a.fd_tgl_periksa,'%d-%m-%Y') fd_tgl_periksa,
					a.fd_tgl_periksa fd_tgl, a.fs_kd_reg,
					REPLACE(REPLACE(REPLACE(a.fs_anamnesa, '\n', '</br>'), '\r', '</br>'), '</br></br>', '</br>') fs_anamnesa,
					REPLACE(REPLACE(REPLACE(a.fs_diagnosa, '\n', '</br>'), '\r', '</br>'), '</br></br>', '</br>') fs_diagnosa,
					REPLACE(REPLACE(REPLACE(a.fs_tindakan, '\n', '</br>'), '\r', '</br>'), '</br></br>', '</br>') fs_tindakan,
					a.fs_kd_icd, a.fs_nm_icd
			FROM  	tx_kartu a
			INNER JOIN tx_reg b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_reg = b.fs_kd_reg
			INNER JOIN tm_mr c ON a.fs_kd_dokter = c.fs_kd_dokter
				AND	b.fs_kd_mr = c.fs_kd_mr
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	c.fs_kd_mr = '".trim($xKdMR)."'
			ORDER BY a.fd_tgl_periksa DESC
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function Histori($xKdMR,$nStart,$nLimit)
	{
		$xSQL = ("
			SELECT  DISTINCT DATE_FORMAT(a.fd_tgl_periksa,'%d-%m-%Y') fd_tgl_periksa,
					a.fd_tgl_periksa fd_tgl, a.fs_kd_reg,
					REPLACE(REPLACE(REPLACE(a.fs_anamnesa, '\n', '</br>'), '\r', '</br>'), '</br></br>', '</br>') fs_anamnesa,
					REPLACE(REPLACE(REPLACE(a.fs_diagnosa, '\n', '</br>'), '\r', '</br>'), '</br></br>', '</br>') fs_diagnosa,
					REPLACE(REPLACE(REPLACE(a.fs_tindakan, '\n', '</br>'), '\r', '</br>'), '</br></br>', '</br>') fs_tindakan,
					a.fs_kd_icd, a.fs_nm_icd
			FROM  	tx_kartu a
			INNER JOIN tx_reg b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_reg = b.fs_kd_reg
			INNER JOIN tm_mr c ON a.fs_kd_dokter = c.fs_kd_dokter
				AND	b.fs_kd_mr = c.fs_kd_mr
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	c.fs_kd_mr = '".trim($xKdMR)."'
			ORDER BY a.fd_tgl_periksa DESC LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KetObat($xTgl,$xKdMR)
	{
		$xSQL = ("
			SELECT  IFNULL(e.fs_nm_barang, '') fs_nm_barang,
					IFNULL(d.fs_ket, '') fs_ket
			FROM  	tx_kartu a
			INNER JOIN tx_reg b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_reg = b.fs_kd_reg
			INNER JOIN tm_mr c ON a.fs_kd_dokter = c.fs_kd_dokter
				AND	b.fs_kd_mr = c.fs_kd_mr
			INNER JOIN tx_resep_detail d ON a.fs_kd_dokter = d.fs_kd_dokter
				AND	a.fs_kd_reg = d.fs_kd_reg
			LEFT JOIN tm_barang e ON a.fs_kd_dokter = e.fs_kd_dokter
				AND	d.fs_kd_barang = e.fs_kd_barang
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	a.fd_tgl_periksa = '".trim($xTgl)."'
				AND	c.fs_kd_mr = '".trim($xKdMR)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>