 <?php

defined('BASEPATH') or exit('No direct script access allowed');

$base_currency = get_base_currency();

$aColumns = [
    'position_id',
    'position_code',
    'position_name',
    'job_position_description',
    'department_id',
    'job_p_id',
    
    ];

$sIndexColumn = 'position_id';
$sTable       = db_prefix() . 'hr_job_position';

$join = [];

$where  = [];
$filter = [];


$department_ids = $this->ci->input->post('department_id');
$job_p_ids = $this->ci->input->post('job_p_id');

if(isset($department_ids)){

    $department_where = '';
    foreach ($department_ids as $department_id) {
        if ($department_id != '') {

            if ($department_where == '') {
                $department_where .= ' AND (find_in_set('.$department_id.', '.db_prefix().'hr_job_position.department_id) ';

            } else {
                $department_where .= ' OR find_in_set('.$department_id.', '.db_prefix().'hr_job_position.department_id) ';
            }

        }
    }

    if ($department_where != '') {
        $department_where .= ')';

        $where[] = $department_where;
    }

}

if(isset($job_p_ids)){
    $job_p_where = '';
    foreach ($job_p_ids as $job_p_id) {
        if ($job_p_id != '') {

            if ($job_p_where == '') {
                $job_p_where .= ' AND (( '.db_prefix().'hr_job_position.job_p_id) = '.$job_p_id;

            } else {
                $job_p_where .= ' OR ('.db_prefix().'hr_job_position.job_p_id = '.$job_p_id.') ';
            }

        }
    }

    if ($job_p_where != '') {
        $job_p_where .= ')';

        $where[] = $job_p_where;
    }


}



$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['position_id', 'department_id']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = '<div class=""><label></label></div>';
    $row[] = $aRow['position_id'];
    
    $subjectOutput ='';

     if (has_permission('staffmanage_job_position', '', 'view') || has_permission('staffmanage_job_position', '', 'view_own' ) || is_admin()){ 

         $subjectOutput .= '<a href="'.admin_url('hr_profile/job_position_view_edit/'.$aRow['position_id']).'">'. $aRow['position_code'].'</a>';
     }


    $subjectOutput .= '<div class="row-options">';

    if (has_permission('staffmanage_job_position', '', 'view') || has_permission('staffmanage_job_position', '', 'view_own' ) || is_admin()){
         $subjectOutput .= '<a href="'.admin_url('hr_profile/job_position_view_edit/'.$aRow['position_id']).'">'. _l('hr_view').'</a> |';
    }

    if (has_permission('staffmanage_job_position', '', 'edit') || is_admin()){
         $subjectOutput .='<a href="#" onclick="edit_job_position(this,'.$aRow['position_id'].'); return false" data-name="'. $aRow['position_name'].'" data-position_code="'. $aRow['position_code'].'" data-department_id="'. $aRow['department_id'].'" data-job_p_id="'. $aRow['job_p_id'].'"  data-toggle="sidebar-right" >'._l('hr_edit').'</a> |';
    }

    if (has_permission('staffmanage_job_position', '', 'delete') || is_admin()){
        $subjectOutput .='<a href="'.admin_url('hr_profile/delete_job_position/'.$aRow['position_id']).'" class="text-danger _delete" >'. _l('delete').'</a>';

    }

    $subjectOutput .= '</div>';
    //$row[] = $subjectOutput;
    $row[] = $aRow['position_code'];

    $row[] = $aRow['position_name'];

    /*get frist 100 character */
     if(strlen($aRow['job_position_description']) > 200){
        $pos=strpos($aRow['job_position_description'], ' ', 200);
        $description_sub = substr($aRow['job_position_description'],0,$pos ); 
      }else{
        $description_sub = $aRow['job_position_description'];
      }

    $row[] = $description_sub;

	// get department
    if($aRow['department_id'] != null && $aRow['department_id'] != ''){
              $members       = explode(',', $aRow['department_id']);
              $str = '';
              $j = 0;
              foreach ($members as $key => $member_id) {
                $member   = $this->ci->hr_profile_model->hr_profile_get_department_name($member_id);

                $j++;
                $str .= '<span class="label label-tag tag-id-1"><span class="tag">'.$member->name.'</span><span class="hide">, </span></span>&nbsp';

                /*if($j%2 == 0){
                 $str .= '<br><br/>';
               }*/

             }
             $_data = $str;
           }
           else{
            $_data = '';
          }

    $row[] = $_data;

    //get parent name
    $job_p = $this->ci->hr_profile_model->get_job_p($aRow['job_p_id']) ; 
    //$row[] = isset($job_p) ? $job_p->job_name :'';
    
    //$action = '<a href="'.admin_url('hr_profile/job_position_view_edit/'.$aRow['position_id']).'"><i class="fa fa-eye"></i></a> | ';
    $action = '<a href="#" onclick="edit_job_position(this,'.$aRow['position_id'].'); return false" data-name="'. $aRow['position_name'].'" data-position_code="'. $aRow['position_code'].'" data-department_id="'. $aRow['department_id'].'" data-job_p_id="'. $aRow['job_p_id'].'"  data-toggle="sidebar-right" ><i class="fa fa-pencil-square-o"></i></a>';
    //$action .= '<a href="'.admin_url('hr_profile/delete_job_position/'.$aRow['position_id']).'" class="text-danger _delete" ><i class="fa fa-remove"></i></a>';
    $row[] = $action;
    
    $output['aaData'][] = $row;
}
