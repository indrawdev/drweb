<?php

class MSearch extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function KodeAgama()
	{
		$xSQL = ("
			SELECT	fs_kd_agama, fs_nm_agama
			FROM	tm_agama
			ORDER BY fs_kd_agama
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeBarangAll($xKdBarang,$xNmBarang)
	{
		$xSQL = ("
			SELECT	a.fs_kd_barang, a.fs_nm_barang,
					a.fs_kd_satuan, IFNULL(b.fs_nm_satuan, '') fs_nm_satuan,
					a.fs_kd_dist, IFNULL(c.fs_nm_dist, '') fs_nm_dist,
					a.fs_ket,
					CASE a.fb_aktif WHEN '1' THEN true ELSE false END fb_aktif,
					CASE a.fb_aktif WHEN '1' THEN 'AKTIF' ELSE 'NON AKTIF' END fs_status
			FROM 	tm_barang a
			INNER JOIN tm_satuan b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_satuan = b.fs_kd_satuan
			INNER JOIN tm_distributor c ON a.fs_kd_dokter = c.fs_kd_dokter
				AND	a.fs_kd_dist = c.fs_kd_dist
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
		");
		
		if (trim($xKdBarang) <> '' or trim($xNmBarang) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_barang LIKE '%".trim($xKdBarang)."%'
				OR a.fs_nm_barang LIKE '%".trim($xNmBarang)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_barang, a.fs_nm_barang
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeBarang($xKdBarang,$xNmBarang,$nStart,$nLimit)
	{
		$xSQL = ("
			SELECT	a.fs_kd_barang, a.fs_nm_barang,
					a.fs_kd_satuan, IFNULL(b.fs_nm_satuan, '') fs_nm_satuan,
					a.fs_kd_dist, IFNULL(c.fs_nm_dist, '') fs_nm_dist,
					a.fs_ket,
					CASE a.fb_aktif WHEN '1' THEN true ELSE false END fb_aktif,
					CASE a.fb_aktif WHEN '1' THEN 'AKTIF' ELSE 'NON AKTIF' END fs_status
			FROM 	tm_barang a
			INNER JOIN tm_satuan b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_satuan = b.fs_kd_satuan
			INNER JOIN tm_distributor c ON a.fs_kd_dokter = c.fs_kd_dokter
				AND	a.fs_kd_dist = c.fs_kd_dist
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
		");
		
		if (trim($xKdBarang) <> '' or trim($xNmBarang) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_barang LIKE '%".trim($xKdBarang)."%'
				OR a.fs_nm_barang LIKE '%".trim($xNmBarang)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_barang, a.fs_nm_barang LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeBarangAktifAll($xKdBarang,$xNmBarang)
	{
		$xSQL = ("
			SELECT	a.fs_kd_barang, a.fs_nm_barang
			FROM 	tm_barang a
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	a.fb_aktif = '1'
		");
		
		if (trim($xKdBarang) <> '' or trim($xNmBarang) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_barang LIKE '%".trim($xKdBarang)."%'
				OR a.fs_nm_barang LIKE '%".trim($xNmBarang)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_barang, a.fs_nm_barang
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeBarangAktif($xKdBarang,$xNmBarang,$nStart,$nLimit)
	{
		$xSQL = ("
			SELECT	a.fs_kd_barang, a.fs_nm_barang
			FROM 	tm_barang a
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	a.fb_aktif = '1'
		");
		
		if (trim($xKdBarang) <> '' or trim($xNmBarang) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_barang LIKE '%".trim($xKdBarang)."%'
				OR a.fs_nm_barang LIKE '%".trim($xNmBarang)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_barang, a.fs_nm_barang LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeDistributorAktifAll($xKdDist,$xNmDist)
	{
		$xSQL = ("
			SELECT	fs_kd_dist, fs_nm_dist
			FROM 	tm_distributor
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	fb_aktif = '1'
		");
		
		if (trim($xKdDist) <> '' or trim($xNmDist) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_dist LIKE '%".trim($xKdDist)."%'
				OR fs_nm_dist LIKE '%".trim($xNmDist)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_dist, fs_nm_dist
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeDistributorAktif($xKdDist,$xNmDist,$nStart,$nLimit)
	{
		$xSQL = ("
			SELECT	fs_kd_dist, fs_nm_dist
			FROM 	tm_distributor
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	fb_aktif = '1'
		");
		
		if (trim($xKdDist) <> '' or trim($xNmDist) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_dist LIKE '%".trim($xKdDist)."%'
				OR fs_nm_dist LIKE '%".trim($xNmDist)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_dist, fs_nm_dist LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeBarangSatuan($xKdSatuan,$xNmSatuan,$nStart,$nLimit)
	{
		$xSQL = ("
			SELECT	fs_kd_satuan, fs_nm_satuan
			FROM 	tm_satuan
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	fb_aktif = '1'
		");
		
		if (trim($xKdSatuan) <> '' or trim($xNmSatuan) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_satuan LIKE '%".trim($xKdSatuan)."%'
				OR fs_nm_satuan LIKE '%".trim($xNmSatuan)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_satuan, fs_nm_satuan LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeDistributorAll($xKdDist,$xNmDist)
	{
		$xSQL = ("
			SELECT	fs_kd_dist, fs_nm_dist, fs_alamat,
					fs_kota, fs_tlp, fs_kontak,
					CASE fb_aktif WHEN '1' THEN true ELSE false END fb_aktif,
					CASE fb_aktif WHEN '1' THEN 'AKTIF' ELSE 'NON AKTIF' END fs_status
			FROM 	tm_distributor
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
		");
		
		if (trim($xKdDist) <> '' or trim($xNmDist) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_dist LIKE '%".trim($xKdDist)."%'
				OR fs_nm_dist LIKE '%".trim($xNmDist)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_dist, fs_nm_dist
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeDistributor($xKdDist,$xNmDist,$nStart,$nLimit)
	{
		$xSQL = ("
			SELECT	fs_kd_dist, fs_nm_dist, fs_alamat,
					fs_kota, fs_tlp, fs_kontak,
					CASE fb_aktif WHEN '1' THEN true ELSE false END fb_aktif,
					CASE fb_aktif WHEN '1' THEN 'AKTIF' ELSE 'NON AKTIF' END fs_status
			FROM 	tm_distributor
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
		");
		
		if (trim($xKdDist) <> '' or trim($xNmDist) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_dist LIKE '%".trim($xKdDist)."%'
				OR fs_nm_dist LIKE '%".trim($xNmDist)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_dist, fs_nm_dist LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeGolDarah()
	{
		$xSQL = ("
			SELECT	fs_gol_darah
			FROM	tm_gol_darah
			ORDER BY fs_gol_darah
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeIcdAll($xCari)
	{
		$xSQL = ("
			SELECT	fs_kd_icd, fs_nm_icd
			FROM 	tm_icd
		");
		
		if (trim($xCari) <> '')
		{
			$xSQL = $xSQL.("
				WHERE (fs_kd_icd LIKE '%".trim($xCari)."%'
				OR fs_nm_icd LIKE '%".trim($xCari)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_icd, fs_nm_icd
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeIcd($xCari,$nStart,$nLimit)
	{
		$xSQL = ("
			SELECT	fs_kd_icd, fs_nm_icd
			FROM 	tm_icd
		");
		
		if (trim($xCari) <> '')
		{
			$xSQL = $xSQL.("
				WHERE (fs_kd_icd LIKE '%".trim($xCari)."%'
				OR fs_nm_icd LIKE '%".trim($xCari)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_icd, fs_nm_icd LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeKartuRegAll($xKdReg,$xKdMR,$xNama,$xAlamat,$xTgl)
	{
		$xSQL = ("
			SELECT	a.fs_kd_reg, DATE_FORMAT(IFNULL(c.fd_tgl_periksa, a.fd_tgl_masuk),'%d-%m-%Y') fd_tgl_periksa,
					IFNULL(b.fs_kd_mr, '') fs_kd_mr, IFNULL(b.fs_nm_pasien, '') fs_nm_pasien,
					IFNULL(b.fs_alamat, '') fs_alamat, IFNULL(b.fs_tlp, '') fs_tlp,
					DATE_FORMAT(b.fd_tgl_lahir,'%d-%m-%Y') fd_tgl_lahir,
					IFNULL(b.fn_tinggi, 0) fn_tinggi, IFNULL(b.fn_berat, 0) fn_berat,
					IFNULL(c.fs_anamnesa, '') fs_anamnesa, IFNULL(c.fs_diagnosa, '') fs_diagnosa,
					IFNULL(c.fs_tindakan, '') fs_tindakan, IFNULL(c.fs_kd_icd, '') fs_kd_icd,
					IFNULL(c.fs_nm_icd, '') fs_nm_icd,
					IFNULL(c.fn_biaya, 0) fn_biaya, IFNULL(c.fn_obat, 0) fn_obat
			FROM	tx_reg a
			LEFT JOIN tm_mr b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_mr = b.fs_kd_mr
			LEFT JOIN tx_kartu c ON a.fs_kd_dokter = c.fs_kd_dokter
				AND	a.fs_kd_reg = c.fs_kd_reg
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	a.fd_tgl_masuk <= '".trim($xTgl)."'
				AND	a.fd_tgl_masuk >= DATE_ADD('".trim($xTgl)."', INTERVAL -1 DAY)
		");
		
		if (trim($xKdReg) <> '' or trim($xKdMR) <> '' or trim($xNama) <> '' or trim($xAlamat) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_reg LIKE '%".trim($xKdReg)."%'
				OR b.fs_kd_mr LIKE '%".trim($xKdMR)."%'
				OR b.fs_nm_pasien LIKE '%".trim($xNama)."%'
				OR b.fs_alamat LIKE '%".trim($xAlamat)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_reg DESC, b.fs_nm_pasien, b.fs_kd_mr
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeKartuReg($xKdReg,$xKdMR,$xNama,$xAlamat,$xTgl,$nStart,$nLimit)
	{
		$xSQL = ("
			SELECT	a.fs_kd_reg, DATE_FORMAT(IFNULL(c.fd_tgl_periksa, a.fd_tgl_masuk),'%d-%m-%Y') fd_tgl_periksa,
					IFNULL(b.fs_kd_mr, '') fs_kd_mr, IFNULL(b.fs_nm_pasien, '') fs_nm_pasien,
					IFNULL(b.fs_alamat, '') fs_alamat, IFNULL(b.fs_tlp, '') fs_tlp,
					DATE_FORMAT(b.fd_tgl_lahir,'%d-%m-%Y') fd_tgl_lahir,
					IFNULL(b.fn_tinggi, 0) fn_tinggi, IFNULL(b.fn_berat, 0) fn_berat,
					IFNULL(c.fs_anamnesa, '') fs_anamnesa, IFNULL(c.fs_diagnosa, '') fs_diagnosa,
					IFNULL(c.fs_tindakan, '') fs_tindakan, IFNULL(c.fs_kd_icd, '') fs_kd_icd,
					IFNULL(c.fs_nm_icd, '') fs_nm_icd,
					IFNULL(c.fn_biaya, 0) fn_biaya, IFNULL(c.fn_obat, 0) fn_obat
			FROM	tx_reg a
			LEFT JOIN tm_mr b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_mr = b.fs_kd_mr
			LEFT JOIN tx_kartu c ON a.fs_kd_dokter = c.fs_kd_dokter
				AND	a.fs_kd_reg = c.fs_kd_reg
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	a.fd_tgl_masuk <= '".trim($xTgl)."'
				AND	a.fd_tgl_masuk >= DATE_ADD('".trim($xTgl)."', INTERVAL -1 DAY)
		");
		
		if (trim($xKdReg) <> '' or trim($xKdMR) <> '' or trim($xNama) <> '' or trim($xAlamat) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_reg LIKE '%".trim($xKdReg)."%'
				OR b.fs_kd_mr LIKE '%".trim($xKdMR)."%'
				OR b.fs_nm_pasien LIKE '%".trim($xNama)."%'
				OR b.fs_alamat LIKE '%".trim($xAlamat)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_reg DESC, b.fs_nm_pasien, b.fs_kd_mr LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeMRAll($xKdMR,$xNama,$xAlamat)
	{
		$xSQL = ("
			SELECT	a.fs_kd_mr, a.fs_nm_pasien, a.fs_alamat,
					a.fs_tlp, a.fb_sex fs_kd_jk, CASE a.fb_sex WHEN '0' THEN 'LAKI - LAKI' ELSE 'PEREMPUAN' END fs_nm_jk,
					a.fs_gol_darah, DATE_FORMAT(a.fd_tgl_lahir,'%d-%m-%Y') fd_tgl_lahir,
					a.fn_tinggi, a.fn_berat,
					a.fs_kd_agama, IFNULL(b.fs_nm_agama, '') fs_nm_agama,
					a.fs_kd_pendidikan, IFNULL(c.fs_nm_pendidikan, '' ) fs_nm_pendidikan,
					a.fs_kd_pekerjaan, IFNULL(d.fs_nm_pekerjaan, '') fs_nm_pekerjaan,
					a.fs_nm_kerabat, a.fs_alm_kerabat, a.fs_hubungan,
					a.fs_tlp_kerabat
			FROM 	tm_mr a
			LEFT JOIN tm_agama b ON a.fs_kd_agama = b.fs_kd_agama
			LEFT JOIN tm_pendidikan c ON a.fs_kd_pendidikan = c.fs_kd_pendidikan
			LEFT JOIN tm_pekerjaan d ON a.fs_kd_pekerjaan = d.fs_kd_pekerjaan
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
		");
		
		if (trim($xKdMR) <> '' or trim($xNama) <> '' or trim($xAlamat) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_mr LIKE '%".trim($xKdMR)."'
				OR a.fs_nm_pasien LIKE '%".trim($xNama)."%'
				OR a.fs_alamat LIKE '%".trim($xAlamat)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_nm_pasien, a.fs_kd_mr
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeMR($xKdMR,$xNama,$xAlamat,$nStart,$nLimit)
	{
		$xSQL = ("
			SELECT	a.fs_kd_mr, a.fs_nm_pasien, a.fs_alamat,
					a.fs_tlp, a.fb_sex fs_kd_jk, CASE a.fb_sex WHEN '0' THEN 'LAKI - LAKI' ELSE 'PEREMPUAN' END fs_nm_jk,
					a.fs_gol_darah, DATE_FORMAT(a.fd_tgl_lahir,'%d-%m-%Y') fd_tgl_lahir,
					a.fn_tinggi, a.fn_berat,
					a.fs_kd_agama, IFNULL(b.fs_nm_agama, '') fs_nm_agama,
					a.fs_kd_pendidikan, IFNULL(c.fs_nm_pendidikan, '' ) fs_nm_pendidikan,
					a.fs_kd_pekerjaan, IFNULL(d.fs_nm_pekerjaan, '') fs_nm_pekerjaan,
					a.fs_nm_kerabat, a.fs_alm_kerabat, a.fs_hubungan,
					a.fs_tlp_kerabat
			FROM 	tm_mr a
			LEFT JOIN tm_agama b ON a.fs_kd_agama = b.fs_kd_agama
			LEFT JOIN tm_pendidikan c ON a.fs_kd_pendidikan = c.fs_kd_pendidikan
			LEFT JOIN tm_pekerjaan d ON a.fs_kd_pekerjaan = d.fs_kd_pekerjaan
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
		");
		
		if (trim($xKdMR) <> '' or trim($xNama) <> '' or trim($xAlamat) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_mr LIKE '%".trim($xKdMR)."'
				OR a.fs_nm_pasien LIKE '%".trim($xNama)."%'
				OR a.fs_alamat LIKE '%".trim($xAlamat)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_nm_pasien, a.fs_kd_mr LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodePaketAksesAll($xKdPaket,$xNmPaket)
	{
		$xSQL = ("
			SELECT	fs_kd_paket, fs_nm_paket
			FROM 	tm_paket
			WHERE	fs_kd_paket LIKE '%AKSES%'
		");
			
		if (trim($xKdPaket) <> '' or trim($xNmPaket) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_paket LIKE '%".trim($xKdPaket)."%'
				OR fs_nm_paket LIKE '%".trim($xNmPaket)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_paket, fs_nm_paket
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodePekerjaan($xKdPekerjaan,$xNmPekerjaan)
	{
		$xSQL = ("
			SELECT	fs_kd_pekerjaan, fs_nm_pekerjaan
			FROM 	tm_pekerjaan
		");
		
		if (trim($xKdPekerjaan) <> '' or trim($xNmPekerjaan) <> '')
		{
			$xSQL = $xSQL.("
				WHERE (fs_kd_pekerjaan LIKE '%".trim($xKdPekerjaan)."%'
				OR fs_nm_pekerjaan LIKE '%".trim($xNmPekerjaan)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_pekerjaan
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodePaketAkses($xKdPaket,$xNmPaket,$nStart,$nLimit)
	{
		$xSQL = ("
			SELECT	fs_kd_paket, fs_nm_paket
			FROM 	tm_paket
			WHERE	fs_kd_paket LIKE '%AKSES%'
		");
		
		if (trim($xKdPaket) <> '' or trim($xNmPaket) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_paket LIKE '%".trim($xKdPaket)."%'
				OR fs_nm_paket LIKE '%".trim($xNmPaket)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_paket, fs_nm_paket LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodePendidikan($xKdPendidikan,$xNmPendidikan)
	{
		$xSQL = ("
			SELECT	fs_kd_pendidikan, fs_nm_pendidikan
			FROM 	tm_pendidikan
		");
		
		if (trim($xKdPendidikan) <> '' or trim($xNmPendidikan) <> '')
		{
			$xSQL = $xSQL.("
				WHERE (fs_kd_pendidikan LIKE '%".trim($xKdPendidikan)."%'
				OR fs_nm_pendidikan LIKE '%".trim($xNmPendidikan)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_nm_pendidikan
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodePesanAll($xKdPesan,$xNama,$xAlamat,$xTgl)
	{
		$xSQL = ("
			SELECT	a.fs_kd_pesan, DATE_FORMAT(a.fd_periksa,'%d-%m-%Y') fd_tgl_periksa,
					DATE_FORMAT(a.fd_pesan,'%d-%m-%Y') fd_tgl_pesan,
					b.fs_kd_mr, b.fs_nm_pasien, b.fs_alamat,
					b.fs_tlp, b.fb_sex fs_kd_jk, CASE b.fb_sex WHEN '0' THEN 'LAKI - LAKI' ELSE 'PEREMPUAN' END fs_nm_jk,
					b.fs_gol_darah, DATE_FORMAT(b.fd_tgl_lahir,'%d-%m-%Y') fd_tgl_lahir,
					b.fn_tinggi, b.fn_berat,
					b.fs_kd_agama, IFNULL(c.fs_nm_agama, '') fs_nm_agama,
					b.fs_kd_pendidikan, IFNULL(d.fs_nm_pendidikan, '' ) fs_nm_pendidikan,
					b.fs_kd_pekerjaan, IFNULL(e.fs_nm_pekerjaan, '') fs_nm_pekerjaan
			FROM	tx_pesan a
			LEFT JOIN tm_mr b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_mr = b.fs_kd_mr
			LEFT JOIN tm_agama c ON b.fs_kd_agama = c.fs_kd_agama
			LEFT JOIN tm_pendidikan d ON b.fs_kd_pendidikan = d.fs_kd_pendidikan
			LEFT JOIN tm_pekerjaan e ON b.fs_kd_pekerjaan = e.fs_kd_pekerjaan
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	a.fd_periksa <= '".trim($xTgl)."'
				AND	a.fd_periksa >= DATE_ADD('".trim($xTgl)."', INTERVAL -1 DAY)
				AND	a.fb_reg = '0'
		");
		
		if (trim($xKdPesan) <> '' or trim($xNama) <> '' or trim($xAlamat) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_pesan LIKE '%".trim($xKdPesan)."'
				OR b.fs_nm_pasien LIKE '%".trim($xNama)."%'
				OR b.fs_alamat LIKE '%".trim($xAlamat)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_pesan DESC, b.fs_nm_pasien
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodePesan($xKdPesan,$xNama,$xAlamat,$xTgl,$nStart,$nLimit)
	{
		$xSQL = ("
			SELECT	a.fs_kd_pesan, DATE_FORMAT(a.fd_periksa,'%d-%m-%Y') fd_tgl_periksa,
					DATE_FORMAT(a.fd_pesan,'%d-%m-%Y') fd_tgl_pesan,
					b.fs_kd_mr, b.fs_nm_pasien, b.fs_alamat,
					b.fs_tlp, b.fb_sex fs_kd_jk, CASE b.fb_sex WHEN '0' THEN 'LAKI - LAKI' ELSE 'PEREMPUAN' END fs_nm_jk,
					b.fs_gol_darah, DATE_FORMAT(b.fd_tgl_lahir,'%d-%m-%Y') fd_tgl_lahir,
					b.fn_tinggi, b.fn_berat,
					b.fs_kd_agama, IFNULL(c.fs_nm_agama, '') fs_nm_agama,
					b.fs_kd_pendidikan, IFNULL(d.fs_nm_pendidikan, '' ) fs_nm_pendidikan,
					b.fs_kd_pekerjaan, IFNULL(e.fs_nm_pekerjaan, '') fs_nm_pekerjaan
			FROM	tx_pesan a
			LEFT JOIN tm_mr b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_mr = b.fs_kd_mr
			LEFT JOIN tm_agama c ON b.fs_kd_agama = c.fs_kd_agama
			LEFT JOIN tm_pendidikan d ON b.fs_kd_pendidikan = d.fs_kd_pendidikan
			LEFT JOIN tm_pekerjaan e ON b.fs_kd_pekerjaan = e.fs_kd_pekerjaan
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	a.fd_periksa <= '".trim($xTgl)."'
				AND	a.fd_periksa >= DATE_ADD('".trim($xTgl)."', INTERVAL -1 DAY)
				AND	a.fb_reg = '0'
		");
		
		if (trim($xKdPesan) <> '' or trim($xNama) <> '' or trim($xAlamat) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_pesan LIKE '%".trim($xKdPesan)."'
				OR b.fs_nm_pasien LIKE '%".trim($xNama)."%'
				OR b.fs_alamat LIKE '%".trim($xAlamat)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_pesan DESC, b.fs_nm_pasien LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodePesanRegAll($xKdPesan,$xNama,$xAlamat,$xTgl)
	{
		$xSQL = ("
			SELECT	a.fs_kd_pesan, DATE_FORMAT(a.fd_periksa,'%d-%m-%Y') fd_tgl_masuk,
					b.fs_kd_mr, b.fs_nm_pasien, b.fs_alamat,
					b.fs_tlp, b.fb_sex fs_kd_jk, CASE b.fb_sex WHEN '0' THEN 'LAKI - LAKI' ELSE 'PEREMPUAN' END fs_nm_jk,
					b.fs_gol_darah, DATE_FORMAT(b.fd_tgl_lahir,'%d-%m-%Y') fd_tgl_lahir,
					b.fn_tinggi, b.fn_berat,
					b.fs_kd_agama, IFNULL(c.fs_nm_agama, '') fs_nm_agama,
					b.fs_kd_pendidikan, IFNULL(d.fs_nm_pendidikan, '' ) fs_nm_pendidikan,
					b.fs_kd_pekerjaan, IFNULL(e.fs_nm_pekerjaan, '') fs_nm_pekerjaan,
					b.fs_nm_kerabat, b.fs_alm_kerabat, b.fs_hubungan,
					b.fs_tlp_kerabat
			FROM	tx_pesan a
			LEFT JOIN tm_mr b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_mr = b.fs_kd_mr
			LEFT JOIN tm_agama c ON b.fs_kd_agama = c.fs_kd_agama
			LEFT JOIN tm_pendidikan d ON b.fs_kd_pendidikan = d.fs_kd_pendidikan
			LEFT JOIN tm_pekerjaan e ON b.fs_kd_pekerjaan = e.fs_kd_pekerjaan
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	a.fd_periksa = '".trim($xTgl)."'
				AND	a.fb_reg = '0'
		");
		
		if (trim($xKdPesan) <> '' or trim($xNama) <> '' or trim($xAlamat) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_pesan LIKE '%".trim($xKdPesan)."'
				OR b.fs_nm_pasien LIKE '%".trim($xNama)."%'
				OR b.fs_alamat LIKE '%".trim($xAlamat)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_pesan DESC, b.fs_nm_pasien
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodePetugasAll($xKdUser,$xNmUser)
	{
		$xSQL = ("
			SELECT	fs_kd_user, fs_nm_user, fs_password,
					fs_kd_akses, fs_nm_akses
			FROM 	tm_user
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
		");
		
		if (trim($xKdUser) <> '' or trim($xNmUser) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_user LIKE '%".trim($xKdUser)."%'
				OR fs_nm_user LIKE '%".trim($xNmUser)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_user, fs_nm_user
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodePetugas($xKdUser,$xNmUser,$nStart,$nLimit)
	{
		$xSQL = ("
			SELECT	fs_kd_user, fs_nm_user, fs_password,
					fs_kd_akses, fs_nm_akses
			FROM 	tm_user
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
		");
		
		if (trim($xKdUser) <> '' or trim($xNmUser) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_user LIKE '%".trim($xKdUser)."%'
				OR fs_nm_user LIKE '%".trim($xNmUser)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_user, fs_nm_user LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodePesanReg($xKdPesan,$xNama,$xAlamat,$xTgl,$nStart,$nLimit)
	{
		$xSQL = ("
			SELECT	a.fs_kd_pesan, DATE_FORMAT(a.fd_periksa,'%d-%m-%Y') fd_tgl_masuk,
					b.fs_kd_mr, b.fs_nm_pasien, b.fs_alamat,
					b.fs_tlp, b.fb_sex fs_kd_jk, CASE b.fb_sex WHEN '0' THEN 'LAKI - LAKI' ELSE 'PEREMPUAN' END fs_nm_jk,
					b.fs_gol_darah, DATE_FORMAT(b.fd_tgl_lahir,'%d-%m-%Y') fd_tgl_lahir,
					b.fn_tinggi, b.fn_berat,
					b.fs_kd_agama, IFNULL(c.fs_nm_agama, '') fs_nm_agama,
					b.fs_kd_pendidikan, IFNULL(d.fs_nm_pendidikan, '' ) fs_nm_pendidikan,
					b.fs_kd_pekerjaan, IFNULL(e.fs_nm_pekerjaan, '') fs_nm_pekerjaan,
					b.fs_nm_kerabat, b.fs_alm_kerabat, b.fs_hubungan,
					b.fs_tlp_kerabat
			FROM	tx_pesan a
			LEFT JOIN tm_mr b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_mr = b.fs_kd_mr
			LEFT JOIN tm_agama c ON b.fs_kd_agama = c.fs_kd_agama
			LEFT JOIN tm_pendidikan d ON b.fs_kd_pendidikan = d.fs_kd_pendidikan
			LEFT JOIN tm_pekerjaan e ON b.fs_kd_pekerjaan = e.fs_kd_pekerjaan
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	a.fd_periksa = '".trim($xTgl)."'
				AND	a.fb_reg = '0'
		");
		
		if (trim($xKdPesan) <> '' or trim($xNama) <> '' or trim($xAlamat) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_pesan LIKE '%".trim($xKdPesan)."'
				OR b.fs_nm_pasien LIKE '%".trim($xNama)."%'
				OR b.fs_alamat LIKE '%".trim($xAlamat)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_pesan DESC, b.fs_nm_pasien LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeRegAll($xKdReg,$xKdMR,$xNama,$xAlamat,$xTgl)
	{
		$xSQL = ("
			SELECT	a.fs_kd_reg, a.fs_kd_pesan, DATE_FORMAT(a.fd_tgl_masuk,'%d-%m-%Y') fd_tgl_masuk,
					b.fs_kd_mr, b.fs_nm_pasien, b.fs_alamat,
					b.fs_tlp, b.fb_sex fs_kd_jk, CASE b.fb_sex WHEN '0' THEN 'LAKI - LAKI' ELSE 'PEREMPUAN' END fs_nm_jk,
					b.fs_gol_darah, DATE_FORMAT(b.fd_tgl_lahir,'%d-%m-%Y') fd_tgl_lahir,
					b.fn_tinggi, b.fn_berat,
					b.fs_kd_agama, IFNULL(c.fs_nm_agama, '') fs_nm_agama,
					b.fs_kd_pendidikan, IFNULL(d.fs_nm_pendidikan, '' ) fs_nm_pendidikan,
					b.fs_kd_pekerjaan, IFNULL(e.fs_nm_pekerjaan, '') fs_nm_pekerjaan,
					b.fs_nm_kerabat, b.fs_alm_kerabat, b.fs_hubungan,
					b.fs_tlp_kerabat
			FROM	tx_reg a
			LEFT JOIN tm_mr b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_mr = b.fs_kd_mr
			LEFT JOIN tm_agama c ON b.fs_kd_agama = c.fs_kd_agama
			LEFT JOIN tm_pendidikan d ON b.fs_kd_pendidikan = d.fs_kd_pendidikan
			LEFT JOIN tm_pekerjaan e ON b.fs_kd_pekerjaan = e.fs_kd_pekerjaan
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	a.fd_tgl_masuk <= '".trim($xTgl)."'
				AND	a.fd_tgl_masuk >= DATE_ADD('".trim($xTgl)."', INTERVAL -1 DAY)
		");
		
		if (trim($xKdReg) <> '' or trim($xKdMR) <> '' or trim($xNama) <> '' or trim($xAlamat) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_reg LIKE '%".trim($xKdReg)."'
				OR b.fs_kd_mr LIKE '%".trim($xKdMR)."%'
				OR b.fs_nm_pasien LIKE '%".trim($xNama)."%'
				OR b.fs_alamat LIKE '%".trim($xAlamat)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_reg DESC, b.fs_nm_pasien, b.fs_kd_mr
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeReg($xKdReg,$xKdMR,$xNama,$xAlamat,$xTgl,$nStart,$nLimit)
	{
		$xSQL = ("
			SELECT	a.fs_kd_reg, a.fs_kd_pesan, DATE_FORMAT(a.fd_tgl_masuk,'%d-%m-%Y') fd_tgl_masuk,
					b.fs_kd_mr, b.fs_nm_pasien, b.fs_alamat,
					b.fs_tlp, b.fb_sex fs_kd_jk, CASE b.fb_sex WHEN '0' THEN 'LAKI - LAKI' ELSE 'PEREMPUAN' END fs_nm_jk,
					b.fs_gol_darah, DATE_FORMAT(b.fd_tgl_lahir,'%d-%m-%Y') fd_tgl_lahir,
					b.fn_tinggi, b.fn_berat,
					b.fs_kd_agama, IFNULL(c.fs_nm_agama, '') fs_nm_agama,
					b.fs_kd_pendidikan, IFNULL(d.fs_nm_pendidikan, '' ) fs_nm_pendidikan,
					b.fs_kd_pekerjaan, IFNULL(e.fs_nm_pekerjaan, '') fs_nm_pekerjaan,
					b.fs_nm_kerabat, b.fs_alm_kerabat, b.fs_hubungan,
					b.fs_tlp_kerabat
			FROM	tx_reg a
			LEFT JOIN tm_mr b ON a.fs_kd_dokter = b.fs_kd_dokter
				AND	a.fs_kd_mr = b.fs_kd_mr
			LEFT JOIN tm_agama c ON b.fs_kd_agama = c.fs_kd_agama
			LEFT JOIN tm_pendidikan d ON b.fs_kd_pendidikan = d.fs_kd_pendidikan
			LEFT JOIN tm_pekerjaan e ON b.fs_kd_pekerjaan = e.fs_kd_pekerjaan
			WHERE	a.fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	a.fd_tgl_masuk <= '".trim($xTgl)."'
				AND	a.fd_tgl_masuk >= DATE_ADD('".trim($xTgl)."', INTERVAL -1 DAY)
		");
		
		if (trim($xKdReg) <> '' or trim($xKdMR) <> '' or trim($xNama) <> '' or trim($xAlamat) <> '')
		{
			$xSQL = $xSQL.("
				AND (a.fs_kd_reg LIKE '%".trim($xKdReg)."'
				OR b.fs_kd_mr LIKE '%".trim($xKdMR)."%'
				OR b.fs_nm_pasien LIKE '%".trim($xNama)."%'
				OR b.fs_alamat LIKE '%".trim($xAlamat)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_reg DESC, b.fs_nm_pasien, b.fs_kd_mr LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeSatuanAll($xKdSatuan,$xNmSatuan)
	{
		$xSQL = ("
			SELECT	fs_kd_satuan, fs_nm_satuan,
					CASE fb_aktif WHEN '1' THEN true ELSE false END fb_aktif,
					CASE fb_aktif WHEN '1' THEN 'AKTIF' ELSE 'NON AKTIF' END fs_status
			FROM 	tm_satuan
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
		");
		
		if (trim($xKdSatuan) <> '' or trim($xNmSatuan) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_satuan LIKE '%".trim($xKdSatuan)."%'
				OR fs_nm_satuan LIKE '%".trim($xNmSatuan)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_satuan, fs_nm_satuan
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeSatuan($xKdSatuan,$xNmSatuan,$nStart,$nLimit)
	{
		$xSQL = ("
			SELECT	fs_kd_satuan, fs_nm_satuan,
					CASE fb_aktif WHEN '1' THEN true ELSE false END fb_aktif,
					CASE fb_aktif WHEN '1' THEN 'AKTIF' ELSE 'NON AKTIF' END fs_status
			FROM 	tm_satuan
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
		");
		
		if (trim($xKdSatuan) <> '' or trim($xNmSatuan) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_satuan LIKE '%".trim($xKdSatuan)."%'
				OR fs_nm_satuan LIKE '%".trim($xNmSatuan)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_satuan, fs_nm_satuan LIMIT ".$nStart.",".$nLimit."
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function KodeSatuanAktifAll($xKdSatuan,$xNmSatuan)
	{
		$xSQL = ("
			SELECT	fs_kd_satuan, fs_nm_satuan
			FROM 	tm_satuan
			WHERE	fs_kd_dokter = '".trim($this->session->userdata('gID'))."'
				AND	fb_aktif = '1'
		");
		
		if (trim($xKdSatuan) <> '' or trim($xNmSatuan) <> '')
		{
			$xSQL = $xSQL.("
				AND (fs_kd_satuan LIKE '%".trim($xKdSatuan)."%'
				OR fs_nm_satuan LIKE '%".trim($xNmSatuan)."%')
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_satuan, fs_nm_satuan
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}
?>