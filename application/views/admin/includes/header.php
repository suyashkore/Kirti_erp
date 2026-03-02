<?php defined('BASEPATH') or exit('No direct script access allowed');

ob_start();

?>
<?php
if (!isset($sidebar_menu) || !is_array($sidebar_menu)) {
    $sidebar_menu = [];
}
?>

<style>

    #table-company_select tr:hover {

        background-color: #ccc;

    }

    

    #table-company_select td:hover {

        cursor: pointer;

    }

    /* Mobile setup menu toggle styles */
    .mobile-menu-btn { display: none; }
    @media (max-width: 768px) {
        .mobile-menu-btn { display: inline-block !important; margin: 6px 8px; }
        #setup-menu-wrapper { display: none; position: fixed; z-index: 1050; background: #fff; width: 260px; height: 100%; overflow: auto; top: 0; left: 0; }
        #setup-menu-wrapper.display-block { display: block; }
        body.setup-menu-open { overflow: hidden; }
        /* Header dropdowns show inline on small screens */
        #header .nav .dropdown-menu { position: static !important; display: none !important; float: none !important; width: auto !important; box-shadow: none !important; }
        #header .nav li.open > .dropdown-menu { display: block !important; }
    }

</style>

<li id="top_search" class="dropdown" data-toggle="tooltip" data-placement="bottom" data-title="search by name...">

   <input type="search" id="search_input" class="form-control" placeholder="<?php echo _l('top_search_placeholder'); ?>">

   <div id="search_results">

   </div>

   <ul class="dropdown-menu search-results animated fadeIn no-mtop search-history" id="search-history">

   </ul>

</li>

<li id="top_search_button">

   <button class="btn"><i class="fa fa-search"></i></button>

</li>

<?php

$top_search_area = ob_get_contents();

ob_end_clean();

?>



<?php

$staff_permission = get_staff_permission($current_user->staffid);

    /*echo "<pre>";

    print_r($current_user);

    print_r($staff_permission);

    die;*/

        if($this->session->userdata('root_company')){

            

        }else {

        ?>

        <div class="row">

            <div class="modal company_selection" id="company_selection" tabindex="-1" role="dialog" style="display: block;">

                <div class="modal-dialog">

                  <div class="modal-content">

                    <div class="modal-header" style="padding:0px;">

                      <h4 style="text-align:center;">Select Your Plant and Year</h4>

                   </div>

                   

                   <div class="modal-body" style="padding-bottom: 2px;padding-top: 0px;">

                       <?php //echo form_open('admin/dashboard/set_root_company1',array()); ?>

                    <div class="row">

                    <div class="table-company_select" style="overflow: auto;max-height: 40vh;width:100%;position:relative;top: 0px;border-collapse: collapse;">

                        <table class="tree table table-striped table-bordered table-company_select" id="table-company_select" width="100%" style="margin-top: 0px;">

                        <thead>

                            <!--<th style="padding: 1px 5px !important;position: sticky; top: 0; z-index: 1;background:#50607b;color:#fff;">Tag</th>-->

                            <th style="padding: 1px 5px !important;position: sticky; top: 0; z-index: 1;background:#50607b;color:#fff;">PlantID</th>

                            <th style="padding: 1px 5px !important;position: sticky; top: 0; z-index: 1;background:#50607b;color:#fff;">FY</th>

                            <th style="padding: 1px 5px !important;position: sticky; top: 0; z-index: 1;background:#50607b;color:#fff;">FirmName</th>

                            <th style="padding: 1px 5px !important;position: sticky; top: 0; z-index: 1;background:#50607b;color:#fff;">YearFrom</th>

                            <th style="padding: 1px 5px !important;position: sticky; top: 0; z-index: 1;background:#50607b;color:#fff;">YearTo</th>

                        </thead>  

                        

                        <?php

                                    $staff_details = get_staff($current_user->staffid);

                                    $staff_company = unserialize($staff_details->staff_comp);

                        

                        //echo "<pre>";

                        //print_r($staff_permission);

                        $i = 1;

                        if(empty($staff_permission)){

                            ?>

                            <tr>

                                <td colspan="6"><span>Access denied. Please contact to administrator...</span></td>

                            </tr>

                            <?php

                        }

                        foreach ($staff_permission as $key1 => $value1) {

                            $Url = admin_url().'dashboard/SetCompanySession/'.substr($value1['YEARFROM'],2,2)."-".$value1['PlantID'];

                        ?>

                           <tr onclick="window.open('<?php echo $Url;?>','_self')">

                           <!--<td style="padding: 1px 5px !important;border:1px solid !important;font-size:11px;text-align:center;position: sticky; left: 0;">

                               <input type="radio"  name="company_id" id="company_id"  value="<?php echo substr($value1['YEARFROM'],2,2)."-".$value1['PlantID'];?>">

                           </td> -->

                           <td style="padding: 1px 5px !important;border:1px solid !important;font-size:11px;text-align:center;position: sticky; left: 0;"><?php echo $value1['PlantID']; ?></td>

                            <td style="padding: 1px 5px !important;border:1px solid !important;font-size:11px;text-align:center;position: sticky; left: 0;"><?php echo substr($value1['YEARFROM'],2,2); ?></td>

                            <td style="padding: 1px 5px !important;border:1px solid !important;font-size:11px;text-align:left;position: sticky; left: 0;">

                                <span><?php echo $value1['FIRMNAME']; ?></span>

                            </td>

                            <td style="padding: 1px 5px !important;border:1px solid !important;font-size:11px;text-align:center;position: sticky; left: 0;">

                                <span><?php echo date("d/m/Y", strtotime(substr($value1['YEARFROM'],0,10))); ?></span>

                            </td>

                            <td style="padding: 1px 5px !important;border:1px solid !important;font-size:11px;text-align:center;position: sticky; left: 0;">

                                <span><?php echo date("d/m/Y", strtotime(substr($value1['YEARTO'],0,10))); ?></span>

                            </td>

                                

                        </tr>

                    <?php

                            $i++;

                        }

                    ?>

                        </table>

                    </div>

                        

                    </div>

                    <div class="modal-footer" style="padding:2px;">

                      <!--<input type="submit" class="btn btn-info save_company1" data-dismiss="modal" value="select">-->

                    </div>

                    <!--</form>-->

                    <?php //echo form_close(); ?>

                  </div>

                </div>

      </div>

        </div>

        </div>

        <?php } ?>

        

<div id="header">

    <!--<button type="button"

        class="hide-menu tw-inline-flex tw-bg-transparent tw-border-0 tw-p-1 tw-mt-4 hover:tw-bg-neutral-600/10 tw-text-neutral-600 hover:tw-text-neutral-800 focus:tw-text-neutral-800 focus:tw-outline-none tw-rounded-md tw-mx-4 ltr:md:tw-ml-4 rtl:md:tw-mr-4 ltr:tw-float-left  rtl:tw-float-right">

        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="tw-h-4 tw-w-4 tw-text-current">

            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"

                d="M2.25 18.003h19.5m-19.5-6h19.5m-19.5-6h19.5"></path>

        </svg>

    </button>-->

    <nav>

        <ul class="nav navbar-nav navbar-left">
            <li class="mobile-menu-toggle-li visible-xs">
                <button class="btn btn-default mobile-menu-btn" onclick="$('#setup-menu-wrapper').toggleClass('display-block'); $('body').toggleClass('setup-menu-open');">
                    <i class="fa fa-bars"></i> Menu
                </button>
            </li>

            <li class="icon header-company-select" data-toggle="tooltip" title="<?php echo get_root_company_name($this->session->userdata('root_company')); ?>" data-placement="bottom">

              <a href="#" class="dropdown-toggle company_select" data-toggle="dropdown" aria-expanded="false">

                <?php echo get_root_company_name($this->session->userdata('root_company')); ?>

              </a>

              <?php $root_company = get_all_root_company();

                    $staff_details = get_staff($current_user->staffid);

                    $staff_company = unserialize($staff_details->staff_comp);

              ?>

      <ul class="dropdown-menu animated fadeIn">

        <?php

        //print_r($staff_permission);

            /*foreach ($root_company as $key => $value) {

                # code...

            if(in_array($value['id'], $staff_company)){*/

            foreach($staff_permission as $key2 => $value2) {

            ?>

            <li class="header-my-company_select"><a href="<?php echo admin_url('dashboard/change_company/').substr($value2['YEARFROM'],2,2)."-".$value2['PlantID']; ?>" style="font-size:10px;"><?php echo $value2['FIRMNAME']." ( ".substr($value2['YEARFROM'],2,2)." )"; ?></a></li>

            <?php } //} ?>

         

         

      </ul>

   </li>

   <?php

         hooks()->do_action('before_render_aside_menu');

         ?>

         <?php foreach($sidebar_menu as $key => $item){

         if(isset($item['collapse']) && count($item['children']) === 0) {

           continue;

         }

         ?>

      <li class="icon header-company-select" data-toggle="tooltip" 

         <?php echo _attributes_to_string(isset($item['li_attributes']) ? $item['li_attributes'] : []); ?>>

         <a href="<?php echo count($item['children']) > 0 ? '#' : $item['href']; ?>"

          aria-expanded="false" data-toggle="dropdown"

          <?php echo _attributes_to_string(isset($item['href_attributes']) ? $item['href_attributes'] : []); ?>>

             <!--<i class="<?php echo $item['icon']; ?> menu-icon"></i>-->

             <span class="menu-text">

             <?php echo _l($item['name'],'', false); ?>

             </span>

             <!--<?php if(count($item['children']) > 0){ ?>

             <span class="fa arrow"></span>

             <?php } ?>-->

         </a>

         <?php if(count($item['children']) > 0){ ?>

         <ul class="dropdown-menu animated fadeIn" aria-expanded="false">

            <?php foreach($item['children'] as $submenu){

               ?>

            <li class="header-my-company_select"

              <?php echo _attributes_to_string(isset($submenu['li_attributes']) ? $submenu['li_attributes'] : []); ?>>

              <a href="<?php echo $submenu['href']; ?>"

               <?php echo _attributes_to_string(isset($submenu['href_attributes']) ? $submenu['href_attributes'] : []); ?>>

               <!--<?php if(!empty($submenu['icon'])){ ?>

               <i class="<?php echo $submenu['icon']; ?> menu-icon"></i>

               <?php } ?>-->

               <span class="sub-menu-text">

                  <?php echo _l($submenu['name'],'',false); ?>

               </span>

               </a>

            </li>

            <?php } ?>

         </ul>

         <?php } ?>

      </li>

      <?php hooks()->do_action('after_render_single_aside_menu', $item); ?>

      <?php } ?>

   </ul>

        <div class="tw-flex tw-justify-between">

            <!--<div class="tw-overflow-hidden tw-shrink-0">

                <div id="logo"

                    class="tw-h-[57px] tw-hidden md:tw-flex tw-items-center [&_img]:tw-h-9 [&_img]:tw-w-auto">

                    <?php $logo = get_admin_header_logo_url(); ?>

                    <?php if (! $logo) { ?>

                    <a class="logo logo-text tw-text-2xl tw-font-semibold"

                        href="<?= hooks()->apply_filters('admin_header_logo_href', admin_url()); ?>">

                        <?= e(get_option('companyname')); ?>

                    </a>

                    <?php } else { ?>

                    <a class="logo"

                        href="<?= hooks()->apply_filters('admin_header_logo_href', admin_url()); ?>">

                        <img src="<?= e($logo); ?>"

                            class="img-responsive"

                            alt="<?= e(get_option('companyname')); ?>" />

                    </a>

                    <?php } ?>

                </div>

            </div>-->

            <div class="tw-flex tw-flex-1 sm:tw-flex-initial">

                <!--<div id="top_search"

                    class="tw-inline-flex tw-relative dropdown sm:tw-ml-1.5 sm:tw-mr-3 tw-max-w-xl tw-flex-auto tw-group/top-search"

                    data-toggle="tooltip" data-placement="bottom"

                    data-title="<?= _l('search_by_tags'); ?>">

                    <input type="search" id="search_input"

                        class="ltr:tw-pr-4 ltr:tw-pl-9 rtl:tw-pr-9 rtl:tw-pl-4 tw-ml-1 tw-mt-2 focus:!tw-ring-0 tw-w-full !tw-placeholder-neutral-500 !tw-shadow-none tw-text-neutral-800 focus:!tw-placeholder-neutral-600 hover:!tw-placeholder-neutral-600 sm:tw-w-[350px] tw-h-[38px] tw-border-0 tw-border-solid !tw-border-white !tw-bg-neutral-100 !tw-rounded-lg"

                        placeholder="<?= _l('top_search_placeholder'); ?>"

                        autocomplete="off">

                    <div id="top_search_button" class="tw-absolute rtl:tw-right-2 ltr:tw-left-2 tw-top-2.5">

                        <button

                            class="tw-outline-none tw-border-0 tw-p-2 tw-text-neutral-400 group-focus-within/top-search:tw-text-neutral-600 tw-bg-transparent">

                            <i class="fa fa-search"></i>

                        </button>

                    </div>

                    <div id="search_results">

                    </div>

                    <ul class="dropdown-menu search-results animated fadeIn search-history" id="search-history">

                    </ul>



                </div>-->

                <ul class="nav navbar-nav visible-md visible-lg">

                    <?php $quickActions = collect($this->app->get_quick_actions_links())->reject(function ($action) {

                        return isset($action['permission']) && staff_cant('create', $action['permission']);

                    }); ?>

                    <?php if ($quickActions->isNotEmpty()) { ?>

                    <!--<li class="icon tw-relative ltr:tw-mr-1.5 rtl:tw-ml-1.5 -tw-mt-1"

                        title="<?= _l('quick_create'); ?>"

                        data-toggle="tooltip" data-placement="bottom">

                        <a href="#" class="!tw-px-0 tw-group !tw-text-white" data-toggle="dropdown">

                            <span

                                class="tw-rounded-full tw-bg-primary-600 tw-text-white tw-inline-flex tw-items-center tw-justify-center tw-h-7 tw-w-7 -tw-mt-1 group-hover:!tw-bg-primary-700">

                                <i class="fa-regular fa-plus fa-lg"></i>

                            </span>

                        </a>

                        <ul class="dropdown-menu dropdown-menu-right animated fadeIn tw-text-base">

                            <li class="dropdown-header tw-mb-1">

                                <?= _l('quick_create'); ?>

                            </li>

                            <?php foreach ($quickActions as $key => $item) {

                                $url = '';

                                if (isset($item['permission'])) {

                                    if (staff_cant('create', $item['permission'])) {

                                        continue;

                                    }

                                }

                                if (isset($item['custom_url'])) {

                                    $url = $item['url'];

                                } else {

                                    $url = admin_url('' . $item['url']);

                                }

                                $href_attributes = '';

                                if (isset($item['href_attributes'])) {

                                    foreach ($item['href_attributes'] as $key => $val) {

                                        $href_attributes .= $key . '="' . $val . '"';

                                    }

                                } ?>

                            <li>

                                <a href="<?= e($url); ?>"

                                    <?= $href_attributes; ?>

                                    class="tw-group tw-inline-flex tw-space-x-0.5 tw-text-neutral-700">

                                    <?php if (isset($item['icon'])) { ?>

                                    <i

                                        class="<?= e($item['icon']); ?> tw-text-neutral-400 group-hover:tw-text-neutral-600 tw-h-5 tw-w-5"></i>

                                    <?php } ?>

                                    <span>

                                        <?= e($item['name']); ?>

                                    </span>

                                </a>

                            </li>

                            <?php

                            } ?>

                        </ul>

                    </li>-->

                    <?php } ?>

                </ul>

            </div>



            <div class="mobile-menu tw-shrink-0 ltr:tw-ml-4 rtl:tw-mr-4">

                <button type="button"

                    class="navbar-toggle visible-md visible-sm visible-xs mobile-menu-toggle collapsed tw-ml-1.5 tw-text-neutral-600 hover:tw-text-neutral-800"

                    data-toggle="collapse" data-target="#mobile-collapse" aria-expanded="false">

                    <i class="fa fa-chevron-down fa-lg"></i>

                </button>

                <ul class="mobile-icon-menu tw-inline-flex tw-mt-5">

                    <?php

               // To prevent not loading the timers twice

            if (is_mobile()) { ?>

                    <li class="dropdown notifications-wrapper header-notifications tw-block ltr:tw-mr-3 rtl:tw-ml-3">

                        <?php $this->load->view('admin/includes/notifications'); ?>

                    </li>

                    <li class="header-timers ltr:tw-mr-1.5 rtl:tw-ml-1.5">

                        <a href="#" id="top-timers" class="dropdown-toggle top-timers tw-block tw-h-5 tw-w-5"

                            data-toggle="dropdown">

                            <i

                                class="fa-regular fa-clock fa-lg tw-text-neutral-500 group-hover:tw-text-neutral-800 tw-shrink-0<?= count($startedTimers) > 0 ? ' tw-animate-spin-slow' : ''; ?>"></i>

                            <span

                                class="tw-leading-none tw-px-1 tw-py-0.5 tw-text-xs bg-success tw-z-10 tw-absolute tw-rounded-full -tw-right-3 -tw-top-2 tw-min-w-[18px] tw-min-h-[18px] tw-inline-flex tw-items-center tw-justify-center icon-started-timers<?= $totalTimers = count($startedTimers) == 0 ? ' hide' : ''; ?>"><?= count($startedTimers); ?></span>

                        </a>

                        <ul class="dropdown-menu animated fadeIn started-timers-top width300" id="started-timers-top">

                            <?php $this->load->view('admin/tasks/started_timers', ['startedTimers' => $startedTimers]); ?>

                        </ul>

                    </li>

                    <?php } ?>

                </ul>

                <div class="mobile-navbar collapse" id="mobile-collapse" aria-expanded="false" style="height: 0px;"

                    role="navigation">

                    <ul class="nav navbar-nav">

                        <li class="header-my-profile"><a

                                href="<?= admin_url('profile'); ?>">

                                <?= _l('nav_my_profile'); ?>

                            </a>

                        </li>

                        <li class="header-my-timesheets"><a

                                href="<?= admin_url('staff/timesheets'); ?>">

                                <?= _l('my_timesheets'); ?>

                            </a>

                        </li>

                        <li class="header-edit-profile"><a

                                href="<?= admin_url('staff/edit_profile'); ?>">

                                <?= _l('nav_edit_profile'); ?>

                            </a>

                        </li>

                        <?php if (is_staff_member()) { ?>

                        <li class="header-newsfeed">

                            <a href="#" class="open_newsfeed mobile">

                                <?= _l('whats_on_your_mind'); ?>

                            </a>

                        </li>

                        <?php } ?>

                        <li class="header-logout">

                            <a href="#" onclick="logout(); return false;">

                                <?= _l('nav_logout'); ?>

                            </a>

                        </li>

                    </ul>

                </div>

            </div>



            <ul class="nav navbar-nav navbar-right -tw-mt-px">

                <?php do_action_deprecated('after_render_top_search', [], '3.0.0', 'admin_navbar_start'); ?>

                <?php hooks()->do_action('admin_navbar_start'); ?>

                <?php if (staff_can('view', 'settings')) { ?>

                <!--<li>

                    <a

                        href="<?= admin_url('settings'); ?>">

                        <?= _l('settings'); ?>

                    </a>

                </li>-->

                <?php } ?>

                <?php if (is_staff_member()) { ?>

                <!--<li class="icon header-newsfeed -tw-mr-1.5">

                    <a href="#" class="open_newsfeed desktop" data-toggle="tooltip"

                        title="<?= _l('whats_on_your_mind'); ?>"

                        data-placement="bottom">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"

                            stroke="currentColor"

                            class="tw-w-[calc(theme(spacing.5)-1px)] tw-h-[calc(theme(spacing.5)-1px)]">

                            <path stroke-linecap="round" stroke-linejoin="round"

                                d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />

                        </svg>

                    </a>

                </li>-->

                <?php } ?>



                <li class="icon header-todo">

                    <a href="<?= admin_url('todo'); ?>"

                        data-toggle="tooltip"

                        title="<?= _l('nav_todo_items'); ?>"

                        data-placement="bottom" class="">

                        <i class="fa-regular fa-square-check fa-lg tw-shrink-0"></i>

                        <span

                            class="tw-leading-none tw-px-1 tw-py-0.5 tw-text-xs bg-warning tw-z-10 tw-absolute tw-rounded-full -tw-right-0.5 tw-top-2 tw-min-w-[18px] tw-min-h-[18px] tw-inline-flex tw-items-center tw-justify-center nav-total-todos<?= $current_user->total_unfinished_todos == 0 ? ' hide' : ''; ?>">

                            <?= e($current_user->total_unfinished_todos); ?>

                        </span>

                    </a>

                </li>



                <li class="icon header-timers timer-button tw-relative ltr:tw-mr-1.5 rtl:tw-ml-1.5"

                    data-placement="bottom" data-toggle="tooltip"

                    data-title="<?= _l('my_timesheets'); ?>">

                    <a href="#" id="top-timers" class="top-timers !tw-px-0 tw-group" data-toggle="dropdown">

                        <span class="tw-inline-flex tw-items-center tw-justify-center tw-h-8 tw-w-9 -tw-mt-1.5">

                            <i

                                class="fa-regular fa-clock fa-lg tw-text-neutral-500 group-hover:tw-text-neutral-800 tw-shrink-0<?= count($startedTimers) > 0 ? ' tw-animate-spin-slow' : ''; ?>"></i>

                        </span>

                        <span

                            class="tw-leading-none tw-px-1 tw-py-0.5 tw-text-xs bg-success tw-z-10 tw-absolute tw-rounded-full -tw-right-1.5 tw-top-2 tw-min-w-[18px] tw-min-h-[18px] tw-inline-flex tw-items-center tw-justify-center icon-started-timers<?= $totalTimers = count($startedTimers) == 0 ? ' hide' : ''; ?>">

                            <?= count($startedTimers); ?>

                        </span>

                    </a>

                    <ul class="dropdown-menu animated fadeIn started-timers-top width300" id="started-timers-top">

                        <?php $this->load->view('admin/tasks/started_timers', ['startedTimers' => $startedTimers]); ?>

                    </ul>

                </li>



                <li class="icon dropdown tw-relative tw-block notifications-wrapper header-notifications rtl:tw-ml-3"

                    data-toggle="tooltip"

                    title="<?= _l('nav_notifications'); ?>"

                    data-placement="bottom">

                    <?php $this->load->view('admin/includes/notifications'); ?>

                </li>



                <?php hooks()->do_action('admin_navbar_end'); ?>

            </ul>

        </div>

    </nav>

</div>
    <script>
        (function($){
            function mobileDropdownInit(){
                if($(window).width() <= 768){
                    $('#header .nav.navbar-nav.navbar-left > li > a[data-toggle="dropdown"]').off('click.mobile').on('click.mobile', function(e){
                        e.preventDefault();
                        var $li = $(this).parent();
                        $li.toggleClass('open');
                    });
                    $(document).off('click.mobile').on('click.mobile', function(e){
                        if(!$(e.target).closest('#header .nav').length){
                            $('#header .nav li.open').removeClass('open');
                        }
                    });
                } else {
                    $('#header .nav.navbar-nav.navbar-left > li').removeClass('open');
                    $('#header .nav.navbar-nav.navbar-left > li > a[data-toggle="dropdown"]').off('click.mobile');
                    $(document).off('click.mobile');
                }
            }
            $(function(){
                mobileDropdownInit();
                $(window).on('resize', mobileDropdownInit);
            });
        })(jQuery);
    </script>