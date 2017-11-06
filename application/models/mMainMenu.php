<?php

class MMainMenu extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	function CekKodePaket()
	{
		$xKdDokter = $this->session->userdata('gID');
		$xKdPetugas = $this->session->userdata('gUser');
		
		$xSQL = ("
			SELECT	IFNULL(fs_kd_paket, '') fs_kd_paket,
					IFNULL(fs_kd_akses, '') fs_kd_akses
			FROM	tm_dokter
			WHERE	fs_kd_dokter = '".trim($xKdDokter)."'
				AND	fs_kd_user = '".trim($xKdPetugas)."'
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function LoadMenu()
	{
		$xSQL = ("
			SELECT	fs_kd_menu, fs_kd_induk, fs_nm_menu,
					fs_nm_form
			FROM	tm_menu_dokter
		");
		
		$xSQL = $xSQL.("
			ORDER BY fs_kd_menu, fs_kd_induk
		");
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function LoadMenu2($xKdPaket,$xKdAkses)
	{
		$xSQL = ("
			SELECT	a.fs_kd_menu, c.fs_kd_induk, c.fs_nm_menu,
					c.fs_nm_form
			FROM	tm_paketlevel a
			INNER JOIN tm_paketlevel b ON a.fs_kd_menu = b.fs_kd_menu
			INNER JOIN tm_menu_dokter c ON a.fs_kd_menu = c.fs_kd_menu
		");
		
		if (trim($xKdPaket) <> '')
		{
			$xSQL = $xSQL.("
				WHERE	a.fs_kd_level = '".trim($xKdPaket)."'
					AND	b.fs_kd_level = '".trim($xKdAkses)."'
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_menu, c.fs_kd_induk
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
	
	function AmbilTempo($xCari)
	{
		$xSQL = ("
			SELECT	a.fs_kd_dokter, a.fs_nm_dokter,
					a.fs_kd_user, a.fs_password, a.fs_alamat,
					a.fs_kota, a.fs_tlp, a.fs_email,
					a.fs_kd_paket, IFNULL(b.fs_nm_paket, '') fs_nm_paket,
					a.fs_kd_akses, IFNULL(c.fs_nm_paket, '') fs_nm_akses,
					DATE_FORMAT(a.fd_jatuh_tempo,'%d-%m-%Y') fd_jatuh_tempo, a.fs_nm_db,
					CASE
						WHEN CURDATE() >= a.fd_jatuh_tempo THEN 'EXPIRED'
						WHEN DATE_ADD(CURDATE(), INTERVAL 1 DAY) = a.fd_jatuh_tempo THEN 'KUNING'
						WHEN DATE_ADD(CURDATE(), INTERVAL 2 DAY) = a.fd_jatuh_tempo THEN 'KUNING'
						WHEN DATE_ADD(CURDATE(), INTERVAL 3 DAY) = a.fd_jatuh_tempo THEN 'KUNING'
						WHEN DATE_ADD(CURDATE(), INTERVAL 4 DAY) = a.fd_jatuh_tempo THEN 'KUNING'
						WHEN DATE_ADD(CURDATE(), INTERVAL 5 DAY) = a.fd_jatuh_tempo THEN 'KUNING'
						WHEN DATE_ADD(CURDATE(), INTERVAL 6 DAY) = a.fd_jatuh_tempo THEN 'KUNING'
						WHEN DATE_ADD(CURDATE(), INTERVAL 7 DAY) = a.fd_jatuh_tempo THEN 'KUNING'
						ELSE 'GO'
					END fs_status
			FROM 	tm_dokter a
			LEFT JOIN tm_paket b ON a.fs_kd_paket = b.fs_kd_paket
			LEFT JOIN tm_paket c ON a.fs_kd_akses = c.fs_kd_paket
		");
		
		if (trim($xCari) <> '')
		{
			$xSQL = $xSQL.("
				WHERE a.fs_kd_dokter = '".trim($xCari)."'
			");
		}
		
		$xSQL = $xSQL.("
			ORDER BY a.fs_kd_dokter, a.fs_nm_dokter
		");
		
		$sSQL = $this->db->query($xSQL);
		return $sSQL;
	}
}

?>