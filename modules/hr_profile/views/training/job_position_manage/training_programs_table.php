<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
	'training_process_id',
	'training_name',
	'training_type',
	'description',
	'mint_point',
	'date_add',
];


$sIndexColumn = 'training_process_id';
$sTable       = db_prefix() . 'hr_jp_interview_training';

$join =[];
$where =[];

$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['job_position_id', 'position_training_id']);


$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

	$row = [];
		$row[] = '<div class=""></div>';
		$row[] = $aRow['training_process_id'];

			$subject = '<a href="#" target="_blank">' . $aRow['training_name'] . '</a>';
			$subject .= '<div class="row-options">';

			if (has_permission('staffmanage_training', '', 'edit')) {
				$subject .= ' <a href="#" onclick="edit_training_process(this,' . $aRow['training_process_id'] . ', '.$aRow['training_process_id'].');return false;" data-id_training= "'.$aRow['training_process_id'].'" data-training_name= "'.$aRow['training_name'].'"  data-job_position_training_type= "'.$aRow['training_type'].'" data-job_position_mint_point= "'.$aRow['mint_point'].'"  data-job_position_training_id= "'.$aRow['position_training_id'].'" data-job_position_id= "'.$aRow['job_position_id'].'" >' . _l('hr_edit') . '</a>';
			}    

			if (has_permission('staffmanage_training', '', 'delete')) {
				$subject .= ' | <a href="' . admin_url('hr_profile/delete_job_position_training_process/' . $aRow['training_process_id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
			}
			$subject .= '</div>';

		$row[] = $subject;

		$training_text ='';
		switch ($aRow['training_type']) {
			case 1:
				$training_text .= _l('hr_basic_training');
				break;
			case 2:
				$training_text .= _l('hr_professiona_training');
				break;
			case 3:
				$training_text .= _l('hr_skill_training');
				break;
			case 4:
				$training_text .= _l('hr_management_training');
				break;
			
			default:
				# code...
				break;
		}

		$row[] = $training_text;
		$row[] = $aRow['description'];
		$row[] = $aRow['mint_point'];
		$row[] = _dt($aRow['date_add']);

	$row['DT_RowClass'] = 'has-row-options';
	$output['aaData'][] = $row;
}
