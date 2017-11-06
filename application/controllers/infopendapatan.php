<?php

class InfoPendapatan extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		//change db
		$this->load->model('mMainModul');
		$this->mMainModul->ChangeDB(trim($this->session->userdata('gDB')));
		//eof change db
	}
	
	function index()
	{
		if (trim($this->session->userdata('gUserLevel')) <> '')
		{
			$this->load->view('vinfopendapatan');
		}
		else
		{
			redirect('','refresh');
		}
	}
	
	function GridDetil()
	{
		$xTgl = trim($this->input->post('fd_tgl'));
		$xTgl2 = trim($this->input->post('fd_tgl2'));
		$nStart = trim($this->input->post('start'));
		$nLimit = trim($this->input->post('limit'));
		
		$this->load->model('mInfoPendapatan');
		$sSQL = $this->mInfoPendapatan->PendapatanAll($xTgl,$xTgl2);
		$xTotal = $sSQL->num_rows();
		
		$xBiaya = 0;
		$xObat = 0;
		$xTotal = 0;
		
		if ($nStart > 0)
		{
			$this->load->model('mInfoPendapatan');
			$sSQL = $this->mInfoPendapatan->Pendapatan($xTgl,$xTgl2,0,$nLimit);
			
			if ($sSQL->num_rows() > 0)
			{
				foreach ($sSQL->result() as $xRow)
				{
					$xBiaya = $xBiaya + trim($xRow->fn_biaya);
					$xObat = $xObat + trim($xRow->fn_obat);
					$xTotal = $xTotal + trim($xRow->fn_total);
				}
			}
		}
		
		$this->load->model('mInfoPendapatan');
		$sSQL = $this->mInfoPendapatan->Pendapatan($xTgl,$xTgl2,$nStart,$nLimit);
		
		$xArr = array();
		if ($sSQL->num_rows() > 0)
		{
			foreach ($sSQL->result() as $xRow)
			{
				$xArr[] = array(
					'fs_kd_reg'			=> trim($xRow->fs_kd_reg),
					'fd_tgl_periksa'	=> trim($xRow->fd_tgl_periksa),
					'fs_nm_pasien'		=> trim($xRow->fs_nm_pasien),
					'fs_alamat'			=> trim($xRow->fs_alamat),
					'fn_biaya'			=> trim($xRow->fn_biaya),
					
					'fn_obat'			=> trim($xRow->fn_obat),
					'fn_total'			=> trim($xRow->fn_total)
				);
				$xBiaya = $xBiaya + trim($xRow->fn_biaya);
				$xObat = $xObat + trim($xRow->fn_obat);
				$xTotal = $xTotal + trim($xRow->fn_total);
			}
		}
		$xArr[] = array(
			'fs_kd_reg'			=> '',
			'fd_tgl_periksa'	=> '',
			'fs_nm_pasien'		=> '',
			'fs_alamat'			=> 'T O T A L',
			'fn_biaya'			=> trim($xBiaya),
			
			'fn_obat'			=> trim($xObat),
			'fn_total'			=> trim($xTotal)
		);
		
		echo json_encode($xArr);
	}
	
	function PrintInfo()
	{
		ini_set('memory_limit', '-1');
		
		$this->load->model('mMainModul');
		$xJamSkg = $this->mMainModul->MicrotimeFloat();
		
		$xNmFileTemp = 'tinfopendapatan';
		$xNmFile = 'infopendapatan'.trim($this->session->userdata('gID')).'-'.$xJamSkg;
		
		$xoReader = PHPExcel_IOFactory::createReader('Excel5');
		
		$xTPath = APPPATH.'../assets/docs/';
		$xPath = APPPATH.'../temp/';
		
		$xoExcel = $xoReader->load($xTPath.$xNmFileTemp.'.xls');
		
		$xoExcel->getActiveSheet()->setTitle('InfoPendapatan');
		$xoExcel->getActiveSheet()->setShowGridlines(false);
		
		$xoExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$xoExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		
		$xoExcel->getActiveSheet()->getPageMargins()->setTop(0.2);
		$xoExcel->getActiveSheet()->getPageMargins()->setBottom(0.2);
		$xoExcel->getActiveSheet()->getPageMargins()->setLeft(0.2);
		$xoExcel->getActiveSheet()->getPageMargins()->setRight(0);
		// $xoExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . '&RPage &P of &N');
		$xoExcel->getDefaultStyle()->getFont()->setName('Calibri');
		$xoExcel->getDefaultStyle()->getFont()->setSize(10);
		
		$xSel = $xoExcel->getActiveSheet();
		
		$xTgl = trim($this->input->post('fd_tgl'));
		$xTgl2 = trim($this->input->post('fd_tgl2'));
		
		if (trim($xTgl)  == '' or trim($xTgl2) == '')
		{
			$xHasil = array(
				'sukses'	=> false,
				'hasil'		=> 'No record!!'
			);
			echo json_encode($xHasil);
			return;
		}
		
		$this->load->model('mInfoPendapatan');
		$sSQL = $this->mInfoPendapatan->PendapatanAll($xTgl,$xTgl2);
		
		$pjghal = 38;
		$i = 1;
		$l = 0; // no urut
		
		$k = 1; // pjg hal
		$p = 1; // awal hal
		$xSel->setCellValue('A'.$i, 'LAPORAN INFO PENDAPATAN');
		$xoExcel->getActiveSheet()->mergeCells('A'.$i.':H'.$i);
		$xoExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$i++;
		$k++;
		
		if (trim($xTgl) == trim($xTgl2))
		{
			$xSel->setCellValue('A'.$i, 'Tanggal : '.$xTgl);
		}
		else
		{
			$xSel->setCellValue('A'.$i, 'Tanggal: '. substr($xTgl,8,2) .'-'. substr($xTgl,5,2) .'-'. substr($xTgl,0,4) .' s/d '. substr($xTgl2,8,2) .'-'. substr($xTgl2,5,2) .'-'. substr($xTgl2,0,4));
		}
		$xoExcel->getActiveSheet()->mergeCells('A'.$i.':H'.$i);
		$xoExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$i++;
		$k++;
		
		$xSel->setCellValue('A'.$i, ' ');
		$i++;
		$k++;
		
		if ($sSQL->num_rows() > 0)
		{
			$l++;
			$xSel->setCellValue('A'.$i, 'No.');
			$xSel->setCellValue('B'.$i, 'Registrasi');
			$xSel->setCellValue('C'.$i, 'Tanggal');
			$xSel->setCellValue('D'.$i, 'Nama');
			$xSel->setCellValue('E'.$i, 'Alamat');
			$xSel->setCellValue('F'.$i, 'Biaya');
			$xSel->setCellValue('G'.$i, 'Obat');
			$xSel->setCellValue('H'.$i, 'Total');
			$xoExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$xoExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$xoExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$xoExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$xoExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$xoExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$xoExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$xoExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$xoExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray(
				array(
					'borders' => array(
						'top' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$xoExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			$p = $i;
			$i++;
			$k++;
			
			
			$xTotalBiaya = 0;
			$xTotalObat = 0;
			$grandTotal = 0;
			
			//loop
			foreach ($sSQL->result() as $xRow)
			{
				if ($k > $pjghal)
				{
					$j = $i - 1;
					$xoExcel->getActiveSheet()->setBreak('A'.$j, PHPExcel_Worksheet::BREAK_ROW);
					
					$xoExcel->getActiveSheet()->getStyle('A'.$j.':H'.$j)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					
					$k = 1;
					$xSel->setCellValue('A'.$i, 'LAPORAN INFO PENDAPATAN');
					$xoExcel->getActiveSheet()->mergeCells('A'.$i.':H'.$i);
					$xoExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$i++;
					$k++;
					
					if (trim($xTgl) == trim($xTgl2))
					{
						$xSel->setCellValue('A'.$i, 'Tanggal : '.$xTgl);
					}
					else
					{
						$xSel->setCellValue('A'.$i, 'Tanggal: '. substr($xTgl,8,2) .'-'. substr($xTgl,5,2) .'-'. substr($xTgl,0,4) .' s/d '. substr($xTgl2,8,2) .'-'. substr($xTgl2,5,2) .'-'. substr($xTgl2,0,4));
					}
					$xoExcel->getActiveSheet()->mergeCells('A'.$i.':H'.$i);
					$xoExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$i++;
					$k++;
					
					$xSel->setCellValue('A'.$i, ' ');
					$i++;
					$k++;
					
					$xSel->setCellValue('A'.$i, 'No.');
					$xSel->setCellValue('B'.$i, 'Registrasi');
					$xSel->setCellValue('C'.$i, 'Tanggal');
					$xSel->setCellValue('D'.$i, 'Nama');
					$xSel->setCellValue('E'.$i, 'Alamat');
					$xSel->setCellValue('F'.$i, 'Biaya');
					$xSel->setCellValue('G'.$i, 'Obat');
					$xSel->setCellValue('H'.$i, 'Total');
					$xoExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$xoExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$xoExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$xoExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$xoExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$xoExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$xoExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$xoExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					$xoExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray(
						array(
							'borders' => array(
								'top' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$xoExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray(
						array(
							'borders' => array(
								'bottom' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					
					$q = $p;
					$xArr = array(
						'borders' => array(
							'left' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN
							)
						)
					);
					$xoExcel->getActiveSheet()->getStyle('A'.$q.':A'.$j)->applyFromArray($xArr);
					$xoExcel->getActiveSheet()->getStyle('B'.$q.':B'.$j)->applyFromArray($xArr);
					$xoExcel->getActiveSheet()->getStyle('C'.$q.':C'.$j)->applyFromArray($xArr);
					$xoExcel->getActiveSheet()->getStyle('D'.$q.':D'.$j)->applyFromArray($xArr);
					$xoExcel->getActiveSheet()->getStyle('E'.$q.':E'.$j)->applyFromArray($xArr);
					$xoExcel->getActiveSheet()->getStyle('F'.$q.':F'.$j)->applyFromArray($xArr);
					$xoExcel->getActiveSheet()->getStyle('G'.$q.':G'.$j)->applyFromArray($xArr);
					$xoExcel->getActiveSheet()->getStyle('H'.$q.':H'.$j)->applyFromArray($xArr);
					$xoExcel->getActiveSheet()->getStyle('H'.$q.':H'.$j)->applyFromArray(
						array(
							'borders' => array(
								'right' => array(
									'style' => PHPExcel_Style_Border::BORDER_THIN
								)
							)
						)
					);
					$p = $i;
					$i++;
					$k++;
				}
				
				$xoExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$xoExcel->getActiveSheet()->getStyle('A'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
				$xSel->setCellValue('A'.$i, $l.'.');
				
				$xSel->setCellValue('B'.$i, trim($xRow->fs_kd_reg));
				$xSel->setCellValue('C'.$i, trim($xRow->fd_tgl_periksa));
				$xSel->setCellValue('D'.$i, trim($xRow->fs_nm_pasien));
				$xSel->setCellValue('E'.$i, trim($xRow->fs_alamat));
				$xSel->setCellValue('F'.$i, trim($xRow->fn_biaya));
				$xSel->setCellValue('G'.$i, trim($xRow->fn_obat));
				$xSel->setCellValue('H'.$i, trim($xRow->fn_total));
				
				$xoExcel->getActiveSheet()->getStyle('F'.$i)->getNumberFormat()->setFormatCode('#,##0');
				$xoExcel->getActiveSheet()->getStyle('G'.$i)->getNumberFormat()->setFormatCode('#,##0');
				$xoExcel->getActiveSheet()->getStyle('H'.$i)->getNumberFormat()->setFormatCode('#,##0');
				
				$xoExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$xoExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$xoExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				
				$xTotalBiaya = $xTotalBiaya + trim($xRow->fn_biaya);
				$xTotalObat = $xTotalObat + trim($xRow->fn_obat);
				$grandTotal = $grandTotal + trim($xRow->fn_total);
				
				$i++;
				$k++;
				$l++;
			}
			//eof loop
			
			$j = $i - 1;
			$xoExcel->getActiveSheet()->getStyle('A'.$j.':H'.$j)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			
			$q = $p;
			$xArr = array(
				'borders' => array(
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$xoExcel->getActiveSheet()->getStyle('A'.$q.':A'.$j)->applyFromArray($xArr);
			$xoExcel->getActiveSheet()->getStyle('B'.$q.':B'.$j)->applyFromArray($xArr);
			$xoExcel->getActiveSheet()->getStyle('C'.$q.':C'.$j)->applyFromArray($xArr);
			$xoExcel->getActiveSheet()->getStyle('D'.$q.':D'.$j)->applyFromArray($xArr);
			$xoExcel->getActiveSheet()->getStyle('E'.$q.':E'.$j)->applyFromArray($xArr);
			$xoExcel->getActiveSheet()->getStyle('F'.$q.':F'.$j)->applyFromArray($xArr);
			$xoExcel->getActiveSheet()->getStyle('G'.$q.':G'.$j)->applyFromArray($xArr);
			$xoExcel->getActiveSheet()->getStyle('H'.$q.':H'.$j)->applyFromArray($xArr);
			$xoExcel->getActiveSheet()->getStyle('H'.$q.':H'.$j)->applyFromArray(
				array(
					'borders' => array(
						'right' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			
			$xSel->setCellValue('E'.$i, 'T o t a l');
			$xSel->setCellValue('F'.$i, $xTotalBiaya);
			$xSel->setCellValue('G'.$i, $xTotalObat);
			$xSel->setCellValue('H'.$i, $grandTotal);
			
			$xoExcel->getActiveSheet()->getStyle('F'.$i)->getNumberFormat()->setFormatCode('#,##0');
			$xoExcel->getActiveSheet()->getStyle('G'.$i)->getNumberFormat()->setFormatCode('#,##0');
			$xoExcel->getActiveSheet()->getStyle('H'.$i)->getNumberFormat()->setFormatCode('#,##0');
			$xoExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$xoExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$xoExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$xoExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
			$xArr = array(
				'borders' => array(
					'left' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);
			$xoExcel->getActiveSheet()->getStyle('E'.$q.':E'.$i)->applyFromArray($xArr);
			$xoExcel->getActiveSheet()->getStyle('F'.$q.':F'.$i)->applyFromArray($xArr);
			$xoExcel->getActiveSheet()->getStyle('G'.$q.':G'.$i)->applyFromArray($xArr);
			$xoExcel->getActiveSheet()->getStyle('H'.$q.':H'.$i)->applyFromArray($xArr);
			$xoExcel->getActiveSheet()->getStyle('H'.$q.':H'.$i)->applyFromArray(
				array(
					'borders' => array(
						'right' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			
			$xoExcel->getActiveSheet()->getStyle('E'.$i.':H'.$i)->applyFromArray(
				array(
					'borders' => array(
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN
						)
					)
				)
			);
			
			$xoWriter = PHPExcel_IOFactory::createWriter($xoExcel, 'Excel5');
			$xoWriter->save($xPath.$xNmFile.'.xls');
			
			//Hapus file lama
			$this->load->model('mMainModul');
			$xJamSkg = $this->mMainModul->MicrotimeFloat();
			$xExp = 1800;
			
			$current_dir = @opendir($xPath);
			while ($xNamaFile = @readdir($current_dir))
			{
				if ($xNamaFile != '.' and $xNamaFile != '..' and $xNamaFile != 'captcha')
				{
					$xNamaFile2 = str_replace('.xls', '', $xNamaFile);
					$xNamaFile2 = str_replace('.pdf', '', $xNamaFile2);
					
					$xLen = strlen($xNamaFile2);
					$xMulai = stripos($xNamaFile2,'-') + 1;
					$xNamaFile2 = substr($xNamaFile2,$xMulai,$xLen);
					
					if (($xNamaFile2 + $xExp) < $xJamSkg)
					{
						@unlink($xPath.$xNamaFile);
					}
				}
			}
			@closedir($current_dir);
			//eof Hapus file lama
			
			$this->load->model('mMainModul');
			$this->mMainModul->BuatPDF($xNmFile);
			
			$xHasil = array(
				'sukses'	=> true,
				'hasil'		=> '"'.$xNmFile.'.pdf" has been created!!',
				'nmfile'	=> $xPath.$xNmFile.'.pdf',
				'nmfilexls'	=> $xPath.$xNmFile.'.xls'
			);
			echo json_encode($xHasil);
		}
		else
		{
			$xHasil = array(
				'sukses'	=> false,
				'hasil'		=> 'No record!!'
			);
			echo json_encode($xHasil);
		}
	}
}
?>