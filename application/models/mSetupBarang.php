<?php

class MSetupBarang extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function CekKodeBarang($xKdBarang)
	{
		$xSQL = ("
			SELECT	fs_kd_barang
			FROM	tm_barang
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	fs_kd_barang = '".$xKdBarang."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function cek_kartu($xKdBarang)
	{
		$xSQL = ("
			SELECT	IFNULL(d.fs_kd_mr, '') fs_kd_mr, IFNULL(d.fs_nm_pasien, '') fs_nm_pasien,
					DATE_FORMAT(b.fd_tgl_periksa,'%d-%m-%Y') fd_tgl_periksa, b.fs_jam_periksa
			FROM 	tx_resep_detail a
			INNER JOIN tx_kartu b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_reg = b.fs_kd_reg
			INNER JOIN tx_reg c ON a.fs_kd_dokter = c.fs_kd_dokter
				AND	a.fs_kd_reg = c.fs_kd_reg
			INNER JOIN tm_mr d ON a.fs_kd_dokter = d.fs_kd_dokter
				AND	c.fs_kd_mr = d.fs_kd_mr
			WHERE  a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	a.fs_kd_barang = '".$xKdBarang."'
			ORDER BY b.fd_tgl_periksa DESC, b.fs_jam_periksa DESC LIMIT 1
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function CekKodeDistributor($xKdDist)
	{
		$xSQL = ("
			SELECT	fs_nm_barang, fs_kd_satuan, fs_kd_dist
			FROM	tm_barang
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	fs_kd_dist = '".$xKdDist."'
			ORDER BY fs_kd_barang, fs_nm_barang LIMIT 1
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function CekKodeSatuan($xKdSatuan)
	{
		$xSQL = ("
			SELECT	fs_nm_barang, fs_kd_satuan, fs_kd_dist
			FROM	tm_barang
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	fs_kd_satuan = '".$xKdSatuan."'
			ORDER BY fs_kd_barang, fs_nm_barang LIMIT 1
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function ListBarangAll()
	{
		$xSQL = ("
			SELECT	a.fs_kd_barang, a.fs_nm_barang,
					a.fs_kd_satuan, IFNULL(b.fs_nm_satuan, '') fs_nm_satuan,
					a.fs_kd_dist, IFNULL(c.fs_nm_dist, '') fs_nm_dist,
					a.fs_ket, a.fb_aktif
			FROM 	tm_barang a
			INNER JOIN tm_satuan b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_satuan = b.fs_kd_satuan
			INNER JOIN tm_distributor c ON a.fs_kd_dokter = c.fs_kd_dokter
				AND	a.fs_kd_dist = c.fs_kd_dist
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
			ORDER BY a.fs_kd_barang, a.fs_nm_barang
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function ListBarang($nStart,$nLimit)
	{
		$xSQL = ("
			SELECT	a.fs_kd_barang, a.fs_nm_barang,
					a.fs_kd_satuan, IFNULL(b.fs_nm_satuan, '') fs_nm_satuan,
					a.fs_kd_dist, IFNULL(c.fs_nm_dist, '') fs_nm_dist,
					a.fs_ket, a.fb_aktif
			FROM 	tm_barang a
			INNER JOIN tm_satuan b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_satuan = b.fs_kd_satuan
			INNER JOIN tm_distributor c ON a.fs_kd_dokter = c.fs_kd_dokter
				AND	a.fs_kd_dist = c.fs_kd_dist
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
			ORDER BY a.fs_kd_barang, a.fs_nm_barang LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>