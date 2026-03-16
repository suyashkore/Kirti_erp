<?php

defined('BASEPATH') or exit('No direct script access allowed');
$aColumns = [
	'1',
	'training_id',
	'subject',
	'training_type',
	'(SELECT count(questionid) FROM ' . db_prefix() . 'hr_position_training_question_form WHERE ' . db_prefix() . 'hr_position_training_question_form.rel_id = ' . db_prefix() . 'hr_position_training.training_id AND rel_type="position_training")',
	'(SELECT count(resultsetid) FROM ' . db_prefix() . 'hr_p_t_surveyresultsets WHERE ' . db_prefix() . 'hr_p_t_surveyresultsets.trainingid = ' . db_prefix() . 'hr_position_training.training_id)',
	'datecreated',
	'2',
];
$sIndexColumn = 'training_id';
$sTable       = db_prefix() . 'hr_position_training';
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['hash',db_prefix() . 'hr_position_training.training_id']);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {

	$row = [];
	for ($i = 0; $i < count($aColumns); $i++) {
		$_data = $aRow[$aColumns[$i]];
		if($aColumns[$i] == '1') {
			$_data = '<div class=""></div>';

		}elseif ($aColumns[$i] == 'subject') {
			$_data = '<a href="' . site_url('hr_profile/participate/index/' . $aRow['training_id'] . '/' . $aRow['hash']) . '" target="_blank">' . $_data . '</a>';

		/*	$_data .= '<div class="row-options">';


			if (is_admin() || has_permission('staffmanage_training', '', 'edit')) {
				$_data .= ' <a href="' . admin_url('hr_profile/position_training/' . $aRow['training_id']) . '">' . _l('hr_edit') . '</a>';
			}    

			if (is_admin() || has_permission('staffmanage_training', '', 'delete')) {
				$_data .= ' | <a href="' . admin_url('hr_profile/delete_position_training/' . $aRow['training_id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
			}

			$_data .= '</div>';*/
		}elseif($aColumns[$i] == 'training_type'){
			$training_text ='';
			switch ($_data) {
				case 1:
					$training_text = _l('hr_basic_training');
					break;
				case 2:
					$training_text = _l('hr_professiona_training');
					break;
				case 3:
					$training_text = _l('hr_skill_training');
					break;
				case 4:
					$training_text = _l('hr_management_training');
					break;
				
				default:
					# code...
					break;
			}

			$_data = $training_text;
		} elseif ($aColumns[$i] == 'datecreated') {
			$_data = _dt($_data);
		} elseif ($aColumns[$i] == '2') {
			$action .= '<a href="' . admin_url('hr_profile/position_training/' . $aRow['training_id']) . '"><i class="fa fa-pencil-square-o"></i></a>';
	    $row[] = $action;
		}


		$row[] = $_data;
		 
	    
	}
	$row['DT_RowClass'] = 'has-row-options';
	$output['aaData'][] = $row;
}
