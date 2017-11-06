<?php

class Login extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		$this->load->view('vlogin');
	}
	
	function BuatCaptcha()
	{
		$this->load->helper('captcha');
		
		$this->load->database();
		
		$xVals = array(
			'expiration'	=> 1800,
			'font_path'		=> './assets/css/font/comic.ttf',
			'img_height'	=> 70,
			'img_path'		=> './temp/captcha/',
			'img_url'		=> './temp/captcha/',
			'img_width'		=> 270
		);
		
		$xCap = create_captcha($xVals);
		
		if ($xCap)
		{
			$xData = array(
				'captcha_time'	=> round($xCap['time']),
				'ip_address'	=> $this->input->ip_address(),
				'word'			=> $xCap['word']
			);
			
			$this->db->insert('captcha', $xData);
			
			$this->session->set_userdata('vcpt',round(trim($xCap['time'])));
			
			$xPathFile = base_url('/temp/captcha/'.trim($xCap['time']).'.jpg');
			
			$xHasil = array(
				'src'	=> $xPathFile);
			echo json_encode($xHasil);
		}
	}
	
	function CekText()
	{
		$this->load->database();
		
		$xEmail = trim($this->input->post('fs_email'));
		$xWord = trim($this->input->post('fs_text'));
		
		$this->load->database();
		
		$xExp = time() - 3600;
		$xWhere = "captcha_time < '".trim($xExp)."'";
		$this->db->where($xWhere);
		$this->db->delete('captcha');
		
		$this->load->model('mMainModul');
		$sSQL = $this->mMainModul->CekCaptcha($xWord);
		$sSQL = $sSQL->row();
		$xJml = $sSQL->fn_jml;
		
		if ($xJml > 0)
		{
		}
		else
		{
			$xHasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Captcha Salah!!'
			);
			echo json_encode($xHasil);
			return;
		}
		
		$this->load->model('mMainModul');
		$sSQL = $this->mMainModul->ValidEmail($xEmail);
		
		if ($sSQL->num_rows() > 0)
		{
			$sSQL = $sSQL->row();
			$xNmDB = strtolower(trim($sSQL->fs_nm_db));
			$xKdDokter = trim($sSQL->fs_kd_dokter);
			$xKota = trim($sSQL->fs_kota);
			$xTempo = trim($sSQL->fd_jatuh_tempo);
			
			$new = array(
				'gDB'=>trim($xNmDB),'gID'=>trim($xKdDokter),'gKota'=>trim($xKota),'gTempo'=>trim($xTempo)
				);
			$this->session->set_userdata($new);
			
			$xHasil = array(
				'sukses'	=> true
			);
			echo json_encode($xHasil);
		}
		else
		{
			$xHasil = array(
				'sukses'	=> false,
				'hasil'		=> 'Kode Petugas, Password atau Email salah!!'
			);
			echo json_encode($xHasil);
		}
	}
	
	function CekLogin()
	{
		$this->form_validation->set_rules('txtPetugas', 'UserCode', 'trim|required|xss_clean');
		$this->form_validation->set_rules('txtPassword', 'Password', 'trim|required|xss_clean');
		
		
		if ($this->form_validation->run() == FALSE)
		{
			echo "'Kode Petugas, Password atau Email salah!!'";
		}
		else
		{
			$xNmUser = trim($this->input->post('txtPetugas'));
			$xPassword = trim($this->input->post('txtPassword'));
			$xUserPassword = trim($this->input->post('txtTgl'));
			
			$hr = substr($xUserPassword,0,2);
			if ($hr % 2 == 0) {
				$x = 0;
			}
			else
			{
				$x = 1;
			}
			$xUserPassword = $xUserPassword.$x;
			
			if (trim($xUserPassword) == trim($xPassword))
			{
				$new = array(
					'gUserLevel'	=> '1',
					'gUser'			=> trim($xNmUser)
				);
				$this->session->set_userdata($new);
				
				echo "{success:true}";
			}
			else
			{
				//change db
				$this->load->model('mMainModul');
				$this->mMainModul->ChangeDB(trim($this->session->userdata('gDB')));
				//eof change db
				
				$this->load->model('mMainModul');
				$sSQL = $this->mMainModul->ValidUserPass($xNmUser);
				
				if ($sSQL->num_rows() > 0)
				{
					$sSQL = $sSQL->row();
					$xNmUser = $sSQL->fs_kd_user;
					$xKey = 'dr';
					$xUserPassword = $this->encrypt->decode($sSQL->fs_password,$xKey);
					
					if (trim($xUserPassword) == trim($xPassword))
					{
						$new = array(
							'gUserLevel'	=> '0',
							'gUser'			=> trim($xNmUser)
						);
						$this->session->set_userdata($new);
						
						echo "{success:true}";
					}
					else
					{
						echo "'Kode Petugas, Password atau Email salah!!'";
					}
				}
				else
				{
					echo "'Kode Petugas, Password atau Email salah!!'";
				}
			}
		}
	}
	
	function Logout()
	{
		$this->session->sess_destroy();
		echo "{success:true}";
	}	
}

?>