<?php

class MainMenu extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	function index()
	{
		if (trim($this->session->userdata('gUserLevel')) <> '')
		{
			$this->load->view('vmainmenu');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function AmbilDefa()
	{
		$xCari = trim($this->session->userdata('gID'));
		$xKdPetugas = trim($this->session->userdata('gUser'));
		$xTglTempo = trim($this->session->userdata('gTempo'));
		
		$this->load->model('mMainMenu');
		$sSQL = $this->mMainMenu->AmbilTempo($xCari);
		
		if ($sSQL->num_rows() > 0)
		{
			$sSQL = $sSQL->row();
			$xTempo = $sSQL->fs_status;
		}
		else
		{
			$xTempo = 'EXPIRED';
		}
		
		$xHasil = array(
			'sukses'	=> true,
			'petugas'	=> $xKdPetugas,
			'tgltempo'	=> $xTglTempo,
			'tempo'		=> $xTempo
		);
		echo json_encode($xHasil);
	}
	
	function AmbilNodes()
	{
		$this->load->model('mMainMenu');
		$sSQL = $this->mMainMenu->CekKodePaket();
		
		if ($sSQL->num_rows() > 0)
		{
			$sSQL = $sSQL->row();
			$xKdPaket = $sSQL->fs_kd_paket;
			$xKdAkses = $sSQL->fs_kd_akses;
		}
		
		if ($this->session->userdata('gUserLevel') == '1')
		{
			$this->load->model('mMainMenu');
			$sSQL = $this->mMainMenu->LoadMenu();
		}
		else if ($this->session->userdata('gUserLevel') == '0')
		{
			$this->load->model('mMainMenu');
			$sSQL = $this->mMainMenu->LoadMenu2($xKdPaket,$xKdAkses);
		}
		
		$xArr0 = array();
		$xArr1 = array();
		$xArr2 = array();
		$xArr3 = array();
		$xArr4 = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow0)
			{
				if (trim($xRow0->fs_kd_induk) == '00')
				{
					
					$i = 0;
					foreach ($sSQL->result() as $xRow1)
					{
						if (strlen(trim($xRow1->fs_kd_induk)) == strlen(trim($xRow0->fs_kd_menu))
							and trim($xRow1->fs_kd_induk) == trim($xRow0->fs_kd_menu))
						{
							++$i;
						}
					}
					
					if ($i == 0)
					{
						$xArr0[] = array(
							'id'	=> $xRow0->fs_kd_menu.'|'.$xRow0->fs_nm_form,
							'text'	=> $xRow0->fs_nm_menu,
							'leaf'	=> true
						);
					}
					else
					{
						$xArr1 = array();
						foreach ($sSQL->result() as $xRow1)
						{
							if (strlen(trim($xRow1->fs_kd_induk)) == strlen(trim($xRow0->fs_kd_menu))
								and trim($xRow1->fs_kd_induk) == trim($xRow0->fs_kd_menu))
							{
								
								$i = 0;
								foreach ($sSQL->result() as $xRow2)
								{
									if (strlen(trim($xRow2->fs_kd_induk)) == strlen(trim($xRow1->fs_kd_menu))
										and trim($xRow2->fs_kd_induk) == trim($xRow1->fs_kd_menu))
									{
										++$i;
									}
								}
								
								if ($i == 0)
								{
									$xArr1[] = array(
										'id'	=> $xRow1->fs_kd_menu.'|'.$xRow1->fs_nm_form,
										'text'	=> $xRow1->fs_nm_menu,
										'leaf'	=> true
									);
								}
								else
								{
									$xArr2 = array();
									foreach ($sSQL->result() as $xRow2)
									{
										if (strlen(trim($xRow2->fs_kd_induk)) == strlen(trim($xRow1->fs_kd_menu))
											and trim($xRow2->fs_kd_induk) == trim($xRow1->fs_kd_menu))
										{
											
											$i = 0;
											foreach ($sSQL->result() as $xRow3)
											{
												if (strlen(trim($xRow3->fs_kd_induk)) == strlen(trim($xRow2->fs_kd_menu))
													and trim($xRow3->fs_kd_induk) == trim($xRow2->fs_kd_menu))
												{
													++$i;
												}
											}
											
											if ($i == 0)
											{
												$xArr2[] = array(
													'id'	=> $xRow2->fs_kd_menu.'|'.$xRow2->fs_nm_form,
													'text'	=> $xRow2->fs_nm_menu,
													'leaf'	=> true
												);
											}
											else
											{
												$xArr3 = array();
												foreach ($sSQL->result() as $xRow3)
												{
													if (strlen(trim($xRow3->fs_kd_induk)) == strlen(trim($xRow2->fs_kd_menu))
														and trim($xRow3->fs_kd_induk) == trim($xRow2->fs_kd_menu))
													{
														
														$i = 0;
														foreach ($sSQL->result() as $xRow4)
														{
															if (strlen(trim($xRow4->fs_kd_induk)) == strlen(trim($xRow3->fs_kd_menu))
																and trim($xRow4->fs_kd_induk) == trim($xRow3->fs_kd_menu))
															{
																++$i;
															}
														}
														
														if ($i == 0)
														{
															$xArr3[] = array(
																'id'	=> $xRow3->fs_kd_menu.'|'.$xRow3->fs_nm_form,
																'text'	=> $xRow3->fs_nm_menu,
																'leaf'	=> true
															);
														}
														else
														{
															$xArr4 = array();
															foreach ($sSQL->result() as $xRow4)
															{
																if (strlen(trim($xRow4->fs_kd_induk)) == strlen(trim($xRow3->fs_kd_menu))
																	and trim($xRow4->fs_kd_induk) == trim($xRow3->fs_kd_menu))
																{
																	$xArr4[] = array(
																		'id'	=> $xRow4->fs_kd_menu.'|'.$xRow4->fs_nm_form,
																		'text'	=> $xRow4->fs_nm_menu,
																		'leaf'	=> true
																	);
																}
															}
															$xArr3[] = array(
																'id'		=> $xRow3->fs_kd_menu,
																'text'		=> $xRow3->fs_nm_menu,
																'expanded'	=> true,
																'leaf'		=> false,
																'children'	=> $xArr4
															);
														}
													}
												}
												$xArr2[] = array(
													'id'		=> $xRow2->fs_kd_menu,
													'text'		=> $xRow2->fs_nm_menu,
													'expanded'	=> true,
													'leaf'		=> false,
													'children'	=> $xArr3
												);
											}
										}
									}
									$xArr1[] = array(
										'id'		=> $xRow1->fs_kd_menu,
										'text'		=> $xRow1->fs_nm_menu,
										'expanded'	=> true,
										'leaf'		=> false,
										'children'	=> $xArr2
									);
								}
							}
						}
						$xArr0[] = array(
							'id'		=> $xRow0->fs_kd_menu,
							'text'		=> $xRow0->fs_nm_menu,
							'expanded'	=> true,
							'leaf'		=> false,
							'children'	=> $xArr1
						);
					}
				}
			}
		}
		echo json_encode($xArr0);
	}
}
?>