<?php
namespace WeDevs\ERP\HRM;

use WeDevs\ERP\Framework\Traits\Ajax;
use WeDevs\ERP\Framework\Traits\Hooker;
use WeDevs\ERP\HRM\Models\Dependents;
use WeDevs\ERP\HRM\Models\Education;
use WeDevs\ERP\HRM\Models\Work_Experience;

/**
 * Ajax handler
 *
 * @package WP-ERP
 */
class Ajax_Handler {

    use Ajax;
    use Hooker;

    /**
     * Bind all the ajax event for HRM
     *
     * @since 0.1
     *
     * @return void
     */
    public function __construct() {

        // Department
        $this->action( 'wp_ajax_erp-hr-new-dept', 'department_create' );
        $this->action( 'wp_ajax_erp-hr-del-dept', 'department_delete' );
        $this->action( 'wp_ajax_erp-hr-get-dept', 'department_get' );
        $this->action( 'wp_ajax_erp-hr-update-dept', 'department_create' );

        // Designation
        $this->action( 'wp_ajax_erp-hr-new-desig', 'designation_create' );
        $this->action( 'wp_ajax_erp-hr-get-desig', 'designation_get' );
        $this->action( 'wp_ajax_erp-hr-update-desig', 'designation_create' );
        $this->action( 'wp_ajax_erp-hr-del-desig', 'designation_delete' );

        // Employee
        $this->action( 'wp_ajax_erp-hr-employee-new', 'employee_create' );
        $this->action( 'wp_ajax_erp-hr-emp-get', 'employee_get' );
        $this->action( 'wp_ajax_erp-hr-emp-delete', 'employee_remove' );
        $this->action( 'wp_ajax_erp-hr-emp-restore', 'employee_restore' );
        $this->action( 'wp_ajax_erp-hr-emp-update-status', 'employee_update_employment' );
        $this->action( 'wp_ajax_erp-hr-emp-update-comp', 'employee_update_compensation' );
        $this->action( 'wp_ajax_erp-hr-emp-delete-history', 'employee_remove_history' );
        $this->action( 'wp_ajax_erp-hr-emp-update-jobinfo', 'employee_update_job_info' );
        $this->action( 'wp_ajax_erp-hr-empl-leave-history', 'get_employee_leave_history' );
        $this->action( 'wp_ajax_erp-hr-employee-new-note', 'employee_add_note' );
        $this->action( 'wp_ajax_erp-load-more-notes', 'employee_load_note' );
        $this->action( 'wp_ajax_erp-delete-employee-note', 'employee_delete_note' );
        $this->action( 'wp_ajax_erp-hr-emp-update-terminate-reason', 'employee_terminate' );
        $this->action( 'wp_ajax_erp-hr-emp-activate', 'employee_termination_reactive' );
        $this->action( 'wp_ajax_erp-hr-convert-wp-to-employee', 'employee_create_from_wp_user' );
        $this->action( 'wp_ajax_erp_hr_check_user_exist', 'check_user' );

        // Dashaboard
        $this->action ( 'wp_ajax_erp_hr_announcement_mark_read', 'mark_read_announcement' );
        $this->action ( 'wp_ajax_erp_hr_announcement_view', 'view_announcement' );

        // Performance
        $this->action( 'wp_ajax_erp-hr-emp-update-performance-reviews', 'employee_update_performance' );
        $this->action( 'wp_ajax_erp-hr-emp-update-performance-comments', 'employee_update_performance' );
        $this->action( 'wp_ajax_erp-hr-emp-update-performance-goals', 'employee_update_performance' );
        $this->action( 'wp_ajax_erp-hr-emp-delete-performance', 'employee_delete_performance' );

        // work experience
        $this->action( 'wp_ajax_erp-hr-create-work-exp', 'employee_work_experience_create' );
        $this->action( 'wp_ajax_erp-hr-emp-delete-exp', 'employee_work_experience_delete' );

        // education
        $this->action( 'wp_ajax_erp-hr-create-education', 'employee_education_create' );
        $this->action( 'wp_ajax_erp-hr-emp-delete-education', 'employee_education_delete' );

        // dependents
        $this->action( 'wp_ajax_erp-hr-create-dependent', 'employee_dependent_create' );
        $this->action( 'wp_ajax_erp-hr-emp-delete-dependent', 'employee_dependent_delete' );

        // leave policy
        $this->action( 'wp_ajax_erp-hr-leave-policy-create', 'leave_policy_create' );
        $this->action( 'wp_ajax_erp-hr-leave-policy-delete', 'leave_policy_delete' );
        $this->action( 'wp_ajax_erp-hr-leave-request-req-date', 'leave_request_dates' );
        $this->action( 'wp_ajax_erp-hr-leave-employee-assign-policies', 'leave_assign_employee_policy' );
        $this->action( 'wp_ajax_erp-hr-leave-policies-availablity', 'leave_available_days' );
        $this->action( 'wp_ajax_erp-hr-leave-req-new', 'leave_request' );

        //leave holiday
        $this->action( 'wp_ajax_erp_hr_holiday_create', 'holiday_create' );
        $this->action( 'wp_ajax_erp-hr-get-holiday', 'get_holiday' );
        $this->action( 'wp_ajax_erp-hr-import-ical', 'import_ical' );

        //leave entitlement
        $this->action( 'wp_ajax_erp-hr-leave-entitlement-delete', 'remove_entitlement' );

        //leave rejected
        $this->action( 'wp_ajax_erp_hr_leave_reject', 'leave_reject' );

        // script reload
        $this->action( 'wp_ajax_erp_hr_script_reload', 'employee_template_refresh' );
        $this->action( 'wp_ajax_erp_hr_new_dept_tmp_reload', 'new_dept_tmp_reload' );
        $this->action( 'wp_ajax_erp-hr-holiday-delete', 'holiday_remove' );
    }

    function leave_reject() {
        $this->verify_nonce( 'wp-erp-hr-nonce' );

        // Check permission
        if ( ! current_user_can( 'erp_leave_manage' ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        $request_id = isset( $_POST['leave_request_id'] ) ? intval( $_POST['leave_request_id'] ) : 0;
        $comments   = isset( $_POST['reason'] ) ? $_POST['reason'] : '';
        erp_hr_leave_request_update_status( $request_id, 3 );

        global $wpdb;

        $update = $wpdb->update( $wpdb->prefix . 'erp_hr_leave_requests',
            array( 'comments' => $comments ),
            array( 'id' => $request_id )
        );

        if ( $update ) {
            $this->send_success();
        }
    }

    /**
     * Remove Holiday
     *
     * @since 0.1
     *
     * @return json
     */
    function holiday_remove() {
        $this->verify_nonce( 'wp-erp-hr-nonce' );

        // Check permission
        if ( ! current_user_can( 'erp_leave_manage' ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        $holiday = erp_hr_delete_holidays( array( 'id' => intval( $_POST['id'] ) ) );
        $this->send_success();
    }

    /**
     * Get Holiday
     *
     * @since 0.1
     *
     * @return json
     */
    function get_holiday() {
        $this->verify_nonce( 'wp-erp-hr-nonce' );

        $holiday = erp_hr_get_holidays( [
            'id'     => absint( $_POST['id'] ),
            'number' => -1
        ] );

        $this->send_success( array( 'holiday' => $holiday ) );
    }

    /**
     * Import ICal files
     *
     * @since 0.1
     *
     * @return json
     */
    function import_ical() {
        $this->verify_nonce( 'wp-erp-hr-nonce' );

        if ( empty( $_FILES['ics']['tmp_name'] ) ) {
            $this->send_error( __( 'File upload error!', 'erp' ) );
        }

        /*
         * An iCal may contain events from previous and future years.
         * We'll import only events from current year
         */
        $first_day_of_year = strtotime( date( 'Y-01-01 00:00:00' ) );
        $last_day_of_year = strtotime( date( 'Y-12-31 23:59:59' ) );


        /*
         * We'll ignore duplicate entries with the same title and
         * start date in the foreach loop when inserting an entry
         */
        $holiday_model = new \WeDevs\ERP\HRM\Models\Leave_Holiday();

        // create the ical parser object
        $ical = new \ICal( $_FILES['ics']['tmp_name'] );
        $events = $ical->events();

        foreach ($events as $event) {
            $start = strtotime( $event['DTSTART'] );
            $end = strtotime( $event['DTEND'] );

            if ( ( $start >= $first_day_of_year ) && ( $end <= $last_day_of_year ) ) {
                $title = sanitize_text_field( $event['SUMMARY'] );
                $start = date( 'Y-m-d H:i:s', $start );
                $end = date( 'Y-m-d H:i:s', $end );
                $description = ( ! empty( $event['DESCRIPTION'] ) ) ? $event['DESCRIPTION'] : $event['SUMMARY'];

                // check for duplicate entries
                $holiday = $holiday_model->where( 'title', '=', $title )
                                         ->where( 'start', '=', $start );

                // insert only unique one
                if ( ! $holiday->count() ) {
                    erp_hr_leave_insert_holiday( array(
                        'id'          => 0,
                        'title'       => $title,
                        'start'       => $start,
                        'end'         => $end,
                        'description' => sanitize_text_field( $description ),
                    ) );
                }
            }
        }

        $this->send_success();
    }

    /**
     * Remove entitlement
     *
     * @since 0.1
     *
     * @return json
     */
    public function remove_entitlement() {
        $this->verify_nonce( 'wp-erp-hr-nonce' );

        // Check permission
        if ( ! current_user_can( 'erp_leave_manage' ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        $id        = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
        $user_id   = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
        $policy_id = isset( $_POST['policy_id'] ) ? intval( $_POST['policy_id'] ) : 0;

        if ( $id && $user_id && $policy_id ) {
            erp_hr_delete_entitlement( $id, $user_id, $policy_id );
            $this->send_success();
        } else {
            $this->send_error( __( 'Somthing wrong !', 'erp' ) );
        }
    }

    /**
     * Get employee template
     *
     * @since 0.1
     *
     * @return void
     */
    public function employee_template_refresh() {
        ob_start();
        include WPERP_HRM_JS_TMPL . '/new-employee.php';
        $this->send_success( array( 'content' => ob_get_clean() ) );
    }

    /**
     * Get department template
     *
     * @since 0.1
     *
     * @return void
     */
    public function new_dept_tmp_reload() {
        ob_start();
        include WPERP_HRM_JS_TMPL . '/new-dept.php';
        $this->send_success( array( 'content' => ob_get_clean() ) );
    }

    /**
     * Get a department
     *
     * @since 0.1
     *
     * @return void
     */
    public function department_get() {
        $this->verify_nonce( 'wp-erp-hr-nonce' );

        $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;

        if ( $id ) {
            $department = new Department( $id );
            $this->send_success( $department );
        }

        $this->send_success( __( 'Something went wrong!', 'erp' ) );
    }

    /**
     * Create a new department
     *
     * @since 0.1
     *
     * @return void
     */
    public function department_create() {
        $this->verify_nonce( 'erp-new-dept' );

        //check permission
        if ( ! current_user_can( 'erp_manage_department' ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        $title   = isset( $_POST['title'] ) ? trim( strip_tags( $_POST['title'] ) ) : '';
        $desc    = isset( $_POST['dept-desc'] ) ? trim( strip_tags( $_POST['dept-desc'] ) ) : '';
        $dept_id = isset( $_POST['dept_id'] ) ? intval( $_POST['dept_id'] ) : 0;
        $lead    = isset( $_POST['lead'] ) ? intval( $_POST['lead'] ) : 0;
        $parent  = isset( $_POST['parent'] ) ? intval( $_POST['parent'] ) : 0;

        // on update, ensure $parent != $dept_id
        if ( $dept_id == $parent ) {
            $parent = 0;
        }

        $dept_id = erp_hr_create_department( array(
            'id'          => $dept_id,
            'title'       => $title,
            'description' => $desc,
            'lead'        => $lead,
            'parent'      => $parent
        ) );

        if ( is_wp_error( $dept_id ) ) {
            $this->send_error( $dept_id->get_error_message() );
        }

        $this->send_success( array(
            'id'       => $dept_id,
            'title'    => $title,
            'lead'     => $lead,
            'parent'   => $parent,
            'employee' => 0
        ) );
    }

    /**
     * Delete a department
     *
     * @return void
     */
    public function department_delete() {
        $this->verify_nonce( 'wp-erp-hr-nonce' );

        //check permission
        if ( ! current_user_can( 'erp_manage_department' ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
        if ( $id ) {
            $deleted = erp_hr_delete_department( $id );

            if ( is_wp_error( $deleted ) ) {
                $this->send_error( $deleted->get_error_message() );
            }

            $this->send_success( __( 'Department has been deleted', 'erp' ) );
        }

        $this->send_error( __( 'Something went worng!', 'erp' ) );
    }

    /**
     * Create a new designnation
     *
     * @return void
     */
    function designation_create() {
        $this->verify_nonce( 'erp-new-desig' );

        //check permission
        if ( ! current_user_can( 'erp_manage_designation' ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        $title    = isset( $_POST['title'] ) ? trim( strip_tags( $_POST['title'] ) ) : '';
        $desc     = isset( $_POST['desig-desc'] ) ? trim( strip_tags( $_POST['desig-desc'] ) ) : '';
        $desig_id = isset( $_POST['desig_id'] ) ? intval( $_POST['desig_id'] ) : 0;

        $desig_id = erp_hr_create_designation( array(
            'id'          => $desig_id,
            'title'       => $title,
            'description' => $desc
        ) );

        if ( is_wp_error( $desig_id ) ) {
            $this->send_error( $desig_id->get_error_message() );
        }

        $this->send_success( array(
            'id'       => $desig_id,
            'title'    => $title,
            'employee' => 0
        ) );
    }

    /**
     * Get a department
     *
     * @return void
     */
    public function designation_get() {
        $this->verify_nonce( 'wp-erp-hr-nonce' );

        $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;

        if ( $id ) {
            $designation = new Designation( $id );
            $this->send_success( $designation );
        }

        $this->send_error( __( 'Something went wrong!', 'erp' ) );
    }

    /**
     * Delete a department
     *
     * @return void
     */
    public function designation_delete() {
        $this->verify_nonce( 'wp-erp-hr-nonce' );

        //check permission
        if ( ! current_user_can( 'erp_manage_designation' ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
        if ( $id ) {
            // @TODO: check permission
            $deleted = erp_hr_delete_designation( $id );

            if ( is_wp_error( $deleted ) ) {
                $this->send_error( $deleted->get_error_message() );
            }

            $this->send_success( __( 'Designation has been deleted', 'erp' ) );
        }

        $this->send_error( __( 'Something went wrong!', 'erp' ) );
    }

    /**
     * Create/update an employee
     *
     * @return void
     */
    public function employee_create() {
        $this->verify_nonce( 'wp-erp-hr-employee-nonce' );

        unset( $_POST['_wp_http_referer'] );
        unset( $_POST['_wpnonce'] );
        unset( $_POST['action'] );

        $posted               = array_map( 'strip_tags_deep', $_POST );
        $posted['type']       = 'customer';

        // Check permission for editing and adding new employee
        if ( isset( $posted['user_id'] ) && $posted['user_id'] ) {
            if ( ! current_user_can( 'erp_edit_employee', $posted['user_id'] ) ) {
                $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
            }
        } else {
            if ( ! current_user_can( 'erp_create_employee' ) ) {
                $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
            }
        }

        $employee_id  = erp_hr_employee_create( $posted );

        if ( is_wp_error( $employee_id ) ) {
            $this->send_error( $employee_id->get_error_message() );
        }

        $employee               = new Employee( $employee_id );
        $data                   = $employee->to_array();
        $data['work']['joined'] = $employee->get_joined_date();
        $data['work']['type']   = $employee->get_type();
        $data['url']            = $employee->get_details_url();

        // user notification email
        if ( isset( $posted['user_notification'] ) && $posted['user_notification'] == 'on' ) {
            $emailer    = wperp()->emailer->get_email( 'New_Employee_Welcome' );
            $send_login = isset( $posted['login_info'] ) ? true : false;

            if ( is_a( $emailer, '\WeDevs\ERP\Email') ) {
                $emailer->trigger( $employee_id, $send_login );
            }
        }

        $this->send_success( $data );
    }

    /**
     * Get an employee for ajax
     *
     * @return void
     */
    public function employee_get() {
        $this->verify_nonce( 'wp-erp-hr-nonce' );

        $employee_id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;
        $user        = get_user_by( 'id', $employee_id );

        if ( ! $user ) {
            $this->send_error( __( 'Employee does not exists.', 'erp' ) );
        }

        $employee = new Employee( $user );
        $this->send_success( $employee->to_array() );
    }

    /**
     * Remove an employee from the company
     *
     * @return void
     */
    public function employee_remove() {
        global $wpdb;

        $this->verify_nonce( 'wp-erp-hr-nonce' );

        // Check permission
        if ( ! current_user_can( 'erp_delete_employee' ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        $employee_id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;
        $hard        = isset( $_REQUEST['hard'] ) ? intval( $_REQUEST['hard'] ) : 0;
        $user        = get_user_by( 'id', $employee_id );

        if ( ! $user ) {
            $this->send_error( __( 'No employee found', 'erp' ) );
        }

        if ( in_array( 'employee', $user->roles ) ) {
            $hard = apply_filters( 'erp_employee_delete_hard', $hard );
            erp_employee_delete( $employee_id, $hard );
        }

        $this->send_success( __( 'Employee has been removed successfully', 'erp' ) );
    }

    /**
     * Restore an employee from the company
     *
     * @since 1.1.1
     *
     * @return void
     */
    public function employee_restore() {
        $this->verify_nonce( 'wp-erp-hr-nonce' );

        global $wpdb;

        $employee_id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;
        $user        = get_user_by( 'id', $employee_id );

        if ( ! $user ) {
            $this->send_error( __( 'No employee found', 'erp' ) );
        }

        if ( in_array( 'employee', $user->roles ) ) {
            erp_employee_restore( $employee_id );
        }

        $this->send_success( __( 'Employee has been restore successfully', 'erp' ) );
    }


    /**
     * Update employment status
     *
     * @return void
     */
    public function employee_update_employment() {
        $this->verify_nonce( 'employee_update_employment' );

        $employee_id = isset( $_REQUEST['employee_id'] ) ? intval( $_REQUEST['employee_id'] ) : 0;

        // Check permission
        if ( ! current_user_can( 'erp_edit_employee', $employee_id ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        $date        = ( empty( $_POST['date'] ) ) ? current_time( 'mysql' ) : $_POST['date'];
        $comment     = strip_tags( $_POST['comment'] );
        $status      = strip_tags( $_POST['status'] );
        $types       = erp_hr_get_employee_types();

        if ( ! array_key_exists( $status, $types ) ) {
            $this->send_error( __( 'Status error', 'erp' ) );
        }

        $employee = new Employee( $employee_id );

        if ( $employee->id ) {
            do_action( 'erp_hr_employee_employment_status_create', $employee->id );
            $employee->update_employment_status( $status, $date, $comment );
            $this->send_success();
        }

        $this->send_error( __( 'Something went wrong!', 'erp' ) );
    }

    /**
     * Update employee compensation
     *
     * @return void
     */
    public function employee_update_compensation() {
        $this->verify_nonce( 'employee_update_compensation' );

        $employee_id = isset( $_REQUEST['employee_id'] ) ? intval( $_REQUEST['employee_id'] ) : 0;

        // Check permission
        if ( ! current_user_can( 'erp_edit_employee', $employee_id ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        $date        = ( empty( $_POST['date'] ) ) ? current_time( 'mysql' ) : $_POST['date'];
        $comment     = strip_tags( $_POST['comment'] );
        $pay_rate    = intval( $_POST['pay_rate'] );
        $pay_type    = strip_tags( $_POST['pay_type'] );
        $reason      = strip_tags( $_POST['change-reason'] );

        $types       = erp_hr_get_pay_type();
        $reasons     = erp_hr_get_pay_change_reasons();

        if ( ! $pay_rate ) {
            $this->send_error( __( 'Enter a valid pay rate.', 'erp' ) );
        }

        if ( ! array_key_exists( $pay_type, $types ) ) {
            $this->send_error( __( 'Pay Type does not exists.', 'erp' ) );
        }

        if ( ! array_key_exists( $reason, $reasons ) ) {
            $this->send_error( __( 'Reason does not exists.', 'erp' ) );
        }

        $employee = new Employee( $employee_id );

        if ( $employee->id ) {
            do_action( 'erp_hr_employee_compensation_create', $employee->id );
            $employee->update_compensation( $pay_rate, $pay_type, $reason, $date, $comment );
            $this->send_success();
        }

        $this->send_error( __( 'Something went wrong!', 'erp' ) );
    }

    /**
     * Remove an history
     *
     * @return void
     */
    public function employee_remove_history() {
        global $wpdb;

        $this->verify_nonce( 'wp-erp-hr-nonce' );

        $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
        $query = "SELECT module, user_id FROM {$wpdb->prefix}erp_hr_employee_history WHERE id=" . $id;
        $get_module = $wpdb->get_row($query);

        // Check permission
        if ( ! current_user_can( 'erp_edit_employee', $get_module->user_id ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        if ( $get_module->module == 'employment' ) {
            do_action( 'erp_hr_employee_employment_status_delete', $id );
        } elseif ( $get_module->module == 'compensation' ) {
            do_action( 'erp_hr_employee_compensation_delete', $id );
        } elseif ( $get_module->module == 'job' ) {
            do_action( 'erp_hr_employee_job_info_delete', $id );
        }

        erp_hr_employee_remove_history( $id );

        $this->send_success();
    }

    /**
     * Update job information
     *
     * @return void
     */
    public function employee_update_job_info() {
        $this->verify_nonce( 'employee_update_jobinfo' );

        $employee_id  = isset( $_POST['employee_id'] ) ? intval( $_POST['employee_id'] ) : 0;

        $location     = isset( $_POST['location'] ) ? intval( $_POST['location'] ) : 0;
        $department   = isset( $_POST['department'] ) ? intval( $_POST['department'] ) : 0;
        $designation  = isset( $_POST['designation'] ) ? intval( $_POST['designation'] ) : 0;
        $reporting_to = isset( $_POST['reporting_to'] ) ? intval( $_POST['reporting_to'] ) : 0;
        $date         = ( empty( $_POST['date'] ) ) ? current_time( 'mysql' ) : $_POST['date'];

        $employee = new Employee( $employee_id );

        if ( $employee->id ) {
            // Check permission
            if ( ! current_user_can( 'erp_edit_employee', $employee->id ) ) {
                $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
            }

            do_action( 'erp_hr_employee_job_info_create', $employee->id );
            $employee->update_job_info( $department, $designation, $reporting_to, $location, $date );
            $this->send_success();
        }

        $this->send_error( __( 'Something went wrong!', 'erp' ) );
    }

    /**
     * Add a new note
     *
     * @return void
     */
    public function employee_add_note() {
        $this->verify_nonce( 'wp-erp-hr-employee-nonce' );

        $employee_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
        $note        = isset( $_POST['note'] ) ? strip_tags( $_POST['note'] ) : 0;
        $note_by     = get_current_user_id();

        $employee = new Employee( $employee_id );

        if ( $employee->id ) {
            // Check permission
            if ( ! current_user_can( 'erp_edit_employee', $employee_id ) ) {
                $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
            }

            $employee->add_note( $note, $note_by );
        }

        $this->send_success();
    }

    /**
     * Employee Load more note
     *
     * @return json
     */
    public function employee_load_note() {
        $employee_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
        $total_no = isset( $_POST['total_no'] ) ? intval( $_POST['total_no'] ) : 0;
        $offset_no = isset( $_POST['offset_no'] ) ? intval( $_POST['offset_no'] ) : 0;

        $employee = new Employee( $employee_id );

        $notes = $employee->get_notes( $total_no, $offset_no );

        ob_start();
        include WPERP_HRM_VIEWS . '/employee/tab-notes-row.php';
        $content = ob_get_clean();

        $this->send_success( array( 'content' => $content ) );
    }

    /**
     * Delete Note
     *
     * @return json
     */
    public function employee_delete_note() {
        check_admin_referer( 'wp-erp-hr-nonce' );

        $note_id     = isset( $_POST['note_id'] ) ? intval( $_POST['note_id'] ) : 0;
        $employee = new Employee();

        // Check permission
        if ( ! current_user_can( 'erp_edit_employee', $employee->id ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        if ( $employee->delete_note( $note_id ) ) {
            $this->send_success();
        } else {
            $this->send_error();
        }
    }

    /**
     * Employee Termination
     *
     * @since 0.1
     *
     * @return json
     */
    public function employee_terminate() {
        $this->verify_nonce( 'employee_update_terminate' );

        $employee_id         = isset( $_POST['employee_id'] ) ? intval( $_POST['employee_id'] ) : 0;
        $terminate_date      = ( empty( $_POST['terminate_date'] ) ) ? current_time( 'mysql' ) : $_POST['terminate_date'];
        $termination_type    = isset( $_POST['termination_type'] ) ?  $_POST['termination_type'] : '';
        $termination_reason  = isset( $_POST['termination_reason'] ) ?  $_POST['termination_reason'] : '';
        $eligible_for_rehire = isset( $_POST['eligible_for_rehire'] ) ? $_POST['eligible_for_rehire'] : '';

        $fields = [
            'employee_id'         => $employee_id,
            'terminate_date'      => $terminate_date,
            'termination_type'    => $termination_type,
            'termination_reason'  => $termination_reason,
            'eligible_for_rehire' => $eligible_for_rehire
        ];

        // Check permission
        if ( ! current_user_can( 'erp_edit_employee', $employee_id ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        $result = erp_hr_employee_terminate( $fields );

        if ( is_wp_error( $result ) ) {
            $this->send_error( $result->get_error_message() );
        }

        $this->send_success();

    }

    /**
     * Reactive terminate employees
     *
     * @since 0.1
     *
     * @return json
     */
    public function employee_termination_reactive() {
        $this->verify_nonce( 'wp-erp-hr-nonce' );

        $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;

        if ( ! $id ) {
            $this->send_error( __( 'Something wrong', 'erp' ) );
        }

        // Check permission
        if ( ! current_user_can( 'erp_edit_employee', $id ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        \WeDevs\ERP\HRM\Models\Employee::where( 'user_id', $id )->update( ['status'=>'active'] );

        delete_user_meta( $id, '_erp_hr_termination' );

        $this->send_success();
    }

    /**
     * Check for created an employee
     *
     * @since 1.0
     *
     * @return json
     */
    public function check_user() {
        $email = isset( $_REQUEST['email'] ) ? sanitize_text_field( $_REQUEST['email'] ) : false;

        if ( ! $email ) {
            $this->send_error( __( 'No email address provided', 'erp' ) );
        }

        $user = get_user_by( 'email', $email );

        // we didn't found any user with this email address
        if ( false === $user ) {
            $this->send_success();
        }

        if ( null != \WeDevs\ERP\HRM\Models\Employee::withTrashed()->whereUserId( $user->ID )->first() ) {
            $employee = new \WeDevs\ERP\HRM\Employee( intval( $user->ID ) );
            $this->send_error( [ 'type' => 'employee', 'data' => $employee->to_array() ] );
        }

        // seems like we found one
        $this->send_error( [ 'type' => 'wp_user', 'data' => $user ] );
    }

    /**
     * Create wp user to emplyee
     *
     * @since 1.0
     *
     * @return json
     */
    public function employee_create_from_wp_user() {
        $this->verify_nonce( 'wp-erp-hr-nonce' );

        $id = isset( $_POST['user_id'] ) ? $_POST['user_id'] : 0;

        if ( ! $id ) {
            $this->send_error( __( 'User not found', 'erp' ) );
        }

        $user = get_user_by( 'id', intval( $id ) );

        $user->add_role('employee');

        $employee = new \WeDevs\ERP\HRM\Models\Employee();
        $exists = $employee->where( 'user_id', '=', $user->ID )->first();

        if ( null === $exists ) {
            $employee = $employee->create([
                'user_id'     => $user->ID,
                'designation' => 0,
                'department'  => 0,
                'status'      => 'active'
            ]);

            $this->send_success( $employee );

        } else {
            $this->send_error( __( 'Employee already exist.', 'erp' ) );
        }
    }

    /**
     * Mark Read Announcement
     *
     * @since 0.1
     *
     * @return json|boolean
     */
    public function mark_read_announcement() {
        $this->verify_nonce( 'wp-erp-hr-nonce' );
        $row_id = intval( $_POST['id'] );

        \WeDevs\ERP\HRM\Models\Announcement::find( $row_id )->update( ['status' => 'read' ] );

        return $this->send_success();
    }

    /**
     * View single announcment
     *
     * @since 0.1
     *
     * @return json [post array]
     */
    public function view_announcement() {
        global $post;

        $this->verify_nonce( 'wp-erp-hr-nonce' );
        $post_id = intval( $_POST['id'] );
        if ( ! $post_id ) {
            $this->send_error();
        }

        \WeDevs\ERP\HRM\Models\Announcement::where( 'post_id', $post_id )->update( ['status' => 'read' ] );

        $post = get_post( $post_id );
        setup_postdata( $post );

        $post_data = [
            'title' => get_the_title(),
            'content' => wpautop( get_the_content() )
        ];

        wp_reset_postdata();

        $this->send_success( $post_data );
    }

    /**
     * Employee Update Performance Reviews
     *
     * @since 0.1
     *
     * @return json
     */
    public function employee_update_performance() {

        // check permission for adding performance
        if ( isset( $_POST['employee_id'] ) && $_POST['employee_id'] && ! current_user_can( 'erp_edit_employee', $_POST['employee_id'] ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        $type = isset( $_POST['type'] ) ? $_POST['type'] : '';

        if ( $type && $type == 'reviews' ) {
            $employee_id      = isset( $_POST['employee_id'] ) ? intval( $_POST['employee_id'] ) : 0;
            $review_id        = isset( $_POST['review_id'] ) ? intval( $_POST['review_id'] ) : 0;
            $reporting_to     = isset( $_POST['reporting_to'] ) ? intval( $_POST['reporting_to'] ) : 0;
            $job_knowledge    = isset( $_POST['job_knowledge'] ) ? intval( $_POST['job_knowledge'] ) : 0;
            $work_quality     = isset( $_POST['work_quality'] ) ? intval( $_POST['work_quality'] ) : 0;
            $attendance       = isset( $_POST['attendance'] ) ? intval( $_POST['attendance'] ) : 0;
            $communication    = isset( $_POST['communication'] ) ? intval( $_POST['communication'] ) : 0;
            $dependablity     = isset( $_POST['dependablity'] ) ? intval( $_POST['dependablity'] ) : 0;
            $performance_date = ( empty( $_POST['performance_date'] ) ) ? current_time( 'mysql' ) : $_POST['performance_date'];

            // some basic validations
            $requires = [
                'performance_date' => __( 'Review Date', 'erp' ),
                'reporting_to'     => __( 'Reporting To', 'erp' ),
            ];

            $fields = [
                'employee_id'      => $employee_id,
                'reporting_to'     => $reporting_to,
                'job_knowledge'    => $job_knowledge,
                'work_quality'     => $work_quality,
                'attendance'       => $attendance,
                'communication'    => $communication,
                'dependablity'     => $dependablity,
                'type'             => $type,
                'performance_date' => $performance_date
            ];
        }

        if ( $type && $type == 'comments' ) {

            $employee_id      = isset( $_POST['employee_id'] ) ? intval( $_POST['employee_id'] ) : 0;
            $review_id        = isset( $_POST['review_id'] ) ? intval( $_POST['review_id'] ) : 0;
            $reviewer         = isset( $_POST['reviewer'] ) ? intval( $_POST['reviewer'] ) : 0;
            $comments         = isset( $_POST['comments'] ) ? esc_textarea( $_POST['comments'] ) : '';
            $performance_date = ( empty( $_POST['performance_date'] ) ) ? current_time( 'mysql' ) : $_POST['performance_date'];

            // some basic validations
            $requires = [
                'performance_date' => __( 'Reference Date', 'erp' ),
                'reviewer'         => __( 'Reviewer', 'erp' ),
            ];

            $fields = [
                'employee_id'      => $employee_id,
                'reviewer'         => $reviewer,
                'comments'         => $comments,
                'type'             => $type,
                'performance_date' => $performance_date
            ];
        }

        if ( $type && $type == 'goals' ) {

            $employee_id           = isset( $_POST['employee_id'] ) ? intval( $_POST['employee_id'] ) : 0;
            $review_id             = isset( $_POST['review_id'] ) ? intval( $_POST['review_id'] ) : 0;
            $completion_date       = ( empty( $_POST['completion_date'] ) ) ? current_time( 'mysql' ) : $_POST['completion_date'];
            $goal_description      = isset( $_POST['goal_description'] ) ? esc_textarea( $_POST['goal_description'] ) : '';
            $employee_assessment   = isset( $_POST['employee_assessment'] ) ? esc_textarea( $_POST['employee_assessment'] ) : '';
            $supervisor            = isset( $_POST['supervisor'] ) ? intval( $_POST['supervisor'] ) : 0;
            $supervisor_assessment = isset( $_POST['supervisor_assessment'] ) ? esc_textarea( $_POST['supervisor_assessment'] ) : '';
            $performance_date      = ( empty( $_POST['performance_date'] ) ) ? current_time( 'mysql' ) : $_POST['performance_date'];

            // some basic validations
            $requires = [
                'performance_date' => __( 'Reference Date', 'erp' ),
                'completion_date' => __( 'Completion Date', 'erp' ),
            ];

            $fields = [
                'employee_id'           => $employee_id,
                'completion_date'       => $completion_date,
                'goal_description'      => $goal_description,
                'employee_assessment'   => $employee_assessment,
                'supervisor'            => $supervisor,
                'supervisor_assessment' => $supervisor_assessment,
                'type'                  => $type,
                'performance_date'      => $performance_date
            ];
        }


        foreach ( $requires as $var_name => $label ) {
            if ( ! $$var_name ) {
                $this->send_error( sprintf( __( '%s is required', 'erp' ), $label ) );
            }
        }

        if ( ! $review_id ) {
            \WeDevs\ERP\HRM\Models\Performance::create( $fields );
        } else {
            \WeDevs\ERP\HRM\Models\Performance::find( $review_id )->update( $fields );
        }

        $this->send_success();
    }

    /**
     * Remove an Prformance
     *
     * @return void
     */
    public function employee_delete_performance() {
        $this->verify_nonce( 'wp-erp-hr-nonce' );

        $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;

        if ( ! current_user_can( 'erp_delete_review' ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        \WeDevs\ERP\HRM\Models\Performance::find( $id )->delete();

        $this->send_success();
    }

    /**
     * Add/edit work experience
     *
     * @return void
     */
    public function employee_work_experience_create() {
        $this->verify_nonce( 'erp-work-exp-form' );

        $employee_id  = isset( $_POST['employee_id'] ) ? intval( $_POST['employee_id'] ) : 0;

        // Check permission
        if ( ! current_user_can( 'erp_edit_employee', $employee_id ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        $exp_id       = isset( $_POST['exp_id'] ) ? intval( $_POST['exp_id'] ) : 0;
        $company_name = isset( $_POST['company_name'] ) ? strip_tags( $_POST['company_name'] ) : '';
        $job_title    = isset( $_POST['job_title'] ) ? strip_tags( $_POST['job_title'] ) : '';
        $from         = isset( $_POST['from'] ) ? strip_tags( $_POST['from'] ) : '';
        $to           = isset( $_POST['to'] ) ? strip_tags( $_POST['to'] ) : '';
        $description  = isset( $_POST['description'] ) ? strip_tags( $_POST['description'] ) : '';

        // some basic validations
        $requires = [
            'company_name' => __( 'Company Name', 'erp' ),
            'job_title'    => __( 'Job Title', 'erp' ),
            'from'         => __( 'From date', 'erp' ),
            'to'           => __( 'To date', 'erp' ),
        ];

        foreach ($requires as $var_name => $label) {
            if ( ! $$var_name ) {
                $this->send_error( sprintf( __( '%s is required', 'erp' ), $label ) );
            }
        }

        $fields = [
            'employee_id'  => $employee_id,
            'company_name' => $company_name,
            'job_title'    => $job_title,
            'from'         => $from,
            'to'           => $to,
            'description'  => $description
        ];

        if ( ! $exp_id ) {
            do_action( 'erp_hr_employee_experience_new', $fields );
            Work_Experience::create( $fields );
        } else {
            Work_Experience::find( $exp_id )->update( $fields );
        }

        $this->send_success();
    }

    /**
     * Delete a work experience
     *
     * @return void
     */
    public function employee_work_experience_delete() {
        $this->verify_nonce( 'wp-erp-hr-nonce' );

        $id          = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
        $employee_id = isset( $_POST['employee_id'] ) ? intval( $_POST['employee_id'] ) : 0;

        if ( ! $employee_id ) {
            $this->send_error( __( 'No employee found', 'erp' ) );
        }

        // Check permission
        if ( ! current_user_can( 'erp_edit_employee', $employee_id ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        if ( $id ) {
            do_action( 'erp_hr_employee_experience_delete', $id );
            Work_Experience::find( $id )->delete();
        }

        $this->send_success();
    }

    /**
     * Create/edit educational experiences
     *
     * @return void
     */
    public function employee_education_create() {
        $this->verify_nonce( 'erp-hr-education-form' );

        $employee_id = isset( $_POST['employee_id'] ) ? intval( $_POST['employee_id'] ) : 0;

        // Check permission
        if ( ! current_user_can( 'erp_edit_employee', $employee_id ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        $edu_id      = isset( $_POST['edu_id'] ) ? intval( $_POST['edu_id'] ) : 0;
        $school      = isset( $_POST['school'] ) ? strip_tags( $_POST['school'] ) : '';
        $degree      = isset( $_POST['degree'] ) ? strip_tags( $_POST['degree'] ) : '';
        $field       = isset( $_POST['field'] ) ? strip_tags( $_POST['field'] ) : '';
        $finished    = isset( $_POST['finished'] ) ? intval( $_POST['finished'] ) : '';
        $notes       = isset( $_POST['notes'] ) ? strip_tags( $_POST['notes'] ) : '';
        $interest    = isset( $_POST['interest'] ) ? strip_tags( $_POST['interest'] ) : '';

        // some basic validations
        $requires = [
            'school'   => __( 'School Name', 'erp' ),
            'degree'   => __( 'Degree', 'erp' ),
            'field'    => __( 'Field', 'erp' ),
            'finished' => __( 'Completion date', 'erp' ),
        ];

        foreach ($requires as $var_name => $label) {
            if ( ! $$var_name ) {
                $this->send_error( sprintf( __( '%s is required', 'erp' ), $label ) );
            }
        }

        $fields = [
            'employee_id' => $employee_id,
            'school'      => $school,
            'degree'      => $degree,
            'field'       => $field,
            'finished'    => $finished,
            'notes'       => $notes,
            'interest'    => $interest
        ];

        if ( ! $edu_id ) {
            do_action( 'erp_hr_employee_education_create', $fields );
            Education::create( $fields );
        } else {
            Education::find( $edu_id )->update( $fields );
        }

        $this->send_success();
    }

    /**
     * Delete a work experience
     *
     * @return void
     */
    public function employee_education_delete() {
        $this->verify_nonce( 'wp-erp-hr-nonce' );

        $id          = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
        $employee_id = isset( $_POST['employee_id'] ) ? intval( $_POST['employee_id'] ) : 0;

        if ( ! $employee_id ) {
            $this->send_error( __( 'No employee found', 'erp' ) );
        }

        // Check permission
        if ( ! current_user_can( 'erp_edit_employee', $employee_id ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        if ( $id ) {
            do_action( 'erp_hr_employee_education_delete', $id );
            Education::find( $id )->delete();
        }

        $this->send_success();
    }

    /**
     * Create/edit dependents
     *
     * @return void
     */
    public function employee_dependent_create() {
        $this->verify_nonce( 'erp-hr-dependent-form' );
        $employee_id = isset( $_POST['employee_id'] ) ? intval( $_POST['employee_id'] ) : 0;

        // Check permission
        if ( ! current_user_can( 'erp_edit_employee', $employee_id ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        $dep_id      = isset( $_POST['dep_id'] ) ? intval( $_POST['dep_id'] ) : 0;
        $name        = isset( $_POST['name'] ) ? strip_tags( $_POST['name'] ) : '';
        $relation    = isset( $_POST['relation'] ) ? strip_tags( $_POST['relation'] ) : '';
        $dob         = isset( $_POST['dob'] ) ? strip_tags( $_POST['dob'] ) : '';

        // some basic validations
        $requires = [
            'name'     => __( 'Name', 'erp' ),
            'relation' => __( 'Relation', 'erp' ),
        ];

        foreach ($requires as $var_name => $label) {
            if ( ! $$var_name ) {
                $this->send_error( sprintf( __( '%s is required', 'erp' ), $label ) );
            }
        }

        $fields = [
            'employee_id' => $employee_id,
            'name'        => $name,
            'relation'    => $relation,
            'dob'         => $dob,
        ];

        if ( ! $dep_id ) {
            do_action( 'erp_hr_employee_dependents_create', $fields );
            Dependents::create( $fields );
        } else {
            Dependents::find( $dep_id )->update( $fields );
        }

        $this->send_success();
    }

    /**
     * Delete a dependent
     *
     * @return void
     */
    public function employee_dependent_delete() {
        $this->verify_nonce( 'wp-erp-hr-nonce' );

        $id          = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
        $employee_id = isset( $_POST['employee_id'] ) ? intval( $_POST['employee_id'] ) : 0;

        if ( ! $employee_id ) {
            $this->send_error( __( 'No employee found', 'erp' ) );
        }

        // Check permission
        if ( ! current_user_can( 'erp_edit_employee', $employee_id ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        if ( $id ) {
            do_action( 'erp_hr_employee_dependents_delete', $id );
            Dependents::find( $id )->delete();
        }

        $this->send_success();
    }

    /**
     * Create or update a leave policy
     *
     * @since 0.1
     *
     * @return void
     */
    public function leave_policy_create() {
        $this->verify_nonce( 'erp-leave-policy' );

        if ( ! current_user_can( 'erp_leave_create_request' ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        $policy_id      = isset( $_POST['policy-id'] ) ? intval( $_POST['policy-id'] ) : 0;
        $name           = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
        $days           = isset( $_POST['days'] ) ? intval( $_POST['days'] ) : '';
        $color          = isset( $_POST['color'] ) ? sanitize_text_field( $_POST['color'] ) : '';
        $department     = isset( $_POST['department'] ) ? intval( $_POST['department'] ) : 0;
        $designation    = isset( $_POST['designation'] ) ? intval( $_POST['designation'] ) : 0;
        $gender         = isset( $_POST['gender'] ) ?  $_POST['gender']  : 0;
        $marital_status = isset( $_POST['maritial'] ) ? $_POST['maritial'] : 0;
        $activate       = isset( $_POST['rateTransitions'] ) ? intval( $_POST['rateTransitions'] ) : 1;
        $description    = isset( $_POST['description'] ) ? $_POST['description'] : '';
        $after_x_day    = isset( $_POST['no_of_days'] ) ? intval( $_POST['no_of_days'] ) : '';
        $effective_date = isset( $_POST['effective_date'] ) ? $_POST['effective_date'] : '';
        $location       = isset( $_POST['location'] ) ? $_POST['location'] : '';
        $instant_apply  = ( isset( $_POST['apply'] ) ) && ( $_POST['apply'] == 'on' ) ? true : false;

        $policy_id = erp_hr_leave_insert_policy( array(
            'id'             => $policy_id,
            'name'           => $name,
            'description'    => $description,
            'value'          => $days,
            'color'          => $color,
            'department'     => $department,
            'designation'    => $designation,
            'gender'         => $gender,
            'marital'        => $marital_status,
            'activate'       => $activate,
            'execute_day'    => $after_x_day,
            'effective_date' => $effective_date,
            'location'       => $location,
            'instant_apply'  => $instant_apply
        ) );

        if ( is_wp_error( $policy_id ) ) {
            $this->send_error( $policy_id->get_error_message() );
        }

        $this->send_success();
    }

    /**
     * Create or update a holiday
     *
     * @since 0.1
     *
     * @return void
     */
    public function holiday_create() {
        $this->verify_nonce( 'erp-leave-holiday' );

        if ( ! current_user_can( 'erp_leave_manage' ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        $holiday_id   = isset( $_POST['holiday_id'] ) ? intval( $_POST['holiday_id'] ) : 0;
        $title        = isset( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
        $start_date   = isset( $_POST['start_date'] ) ? $_POST['start_date']  : '';
        $end_date     = isset( $_POST['end_date'] ) && ! empty( $_POST['end_date'] ) ? $_POST['end_date'] : $start_date;
        $end_date     = date( 'Y-m-d H:i:s', strtotime( $end_date . ' +1 day' )  );
        $description  = isset( $_POST['description'] ) ? $_POST['description'] : '';
        $range_status = isset( $_POST['range'] ) ? $_POST['range'] : 'off';
        $error        = true;

        $holidays = erp_hr_get_holidays( array( 'number' => '-1' ) );

        if ( $holidays ) {
            foreach ( $holidays as $holiday ) {
                $prev_start = date( 'Y-m-d', strtotime( $holiday->start ) );
                $prev_end   = date( 'Y-m-d', strtotime( $holiday->end ) );

                if ( erp_check_date_range_in_range_exist( $prev_start, $prev_end, $start_date, $end_date ) && ( $holiday->id != $holiday_id ) ) {
                    $error = new \WP_Error( 'msg', __( 'Holiday exist in your selected date', 'erp' ) );
                }

                if ( erp_check_date_range_in_range_exist( $start_date, $end_date, $prev_start, $prev_end ) && ( $holiday->id != $holiday_id ) ) {
                    $error = new \WP_Error( 'msg', __( 'Holiday exist in your selected date', 'erp' ) );
                }
            }
        }

        if ( $range_status == 'off' ) {
            $end_date = date( 'Y-m-d H:i:s', strtotime( $start_date . ' +1 day' )  );
        }

        if ( is_wp_error( $error ) ) {
            $this->send_error( $error->get_error_message() );
        }

        $holiday_id = erp_hr_leave_insert_holiday( array(
            'id'          => $holiday_id,
            'title'       => $title,
            'start'       => $start_date,
            'end'         => $end_date,
            'description' => $description,
        ) );

        if ( is_wp_error( $holiday_id ) ) {
            $this->send_error( $holiday_id->get_error_message() );
        }

        $this->send_success();
    }

    /**
     * Delete a leave policy
     *
     * @since 0.1
     *
     * @return void
     */
    public function leave_policy_delete() {
        $this->verify_nonce( 'wp-erp-hr-nonce' );

        if ( ! current_user_can( 'erp_leave_manage' ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        $id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
        if ( $id ) {
            $deleted = erp_hr_leave_policy_delete( $id );

            if ( is_wp_error( $deleted ) ) {
                $this->send_error( $deleted->get_error_message() );
            }

            $this->send_success( __( 'Policy has been deleted', 'erp' ) );
        }

        $this->send_error( __( 'Something went worng!', 'erp' ) );
    }

    /**
     * Gets the leave dates
     *
     * Returns the date list between the start and end date of the
     * two dates
     *
     * @since 0.1
     *
     * @return void
     */
    public function leave_request_dates() {

        $this->verify_nonce( 'wp-erp-hr-nonce' );

        $id = isset( $_POST['employee_id'] ) && $_POST['employee_id'] ? intval( $_POST['employee_id'] ) : false;

        if ( ! $id ) {
           $this->send_error( __( 'Please select an employee', 'erp' ) );
        }

        $policy_id = isset( $_POST['type'] ) && $_POST['type'] ? $_POST['type'] : false;

        if ( ! $policy_id ) {
            $this->send_error( __( 'Please select a policy', 'erp' ) );
        }

        $start_date           = isset( $_POST['from'] ) ? sanitize_text_field( $_POST['from'] ) : date_i18n( 'Y-m-d' );
        $end_date             = isset( $_POST['to'] ) ? sanitize_text_field( $_POST['to'] ) : date_i18n( 'Y-m-d' );
        $valid_date_range     = erp_hrm_is_valid_leave_date_range_within_financial_date_range( $start_date, $end_date );
        $financial_start_date = date( 'Y-m-d', strtotime( erp_financial_start_date() ) );
        $financial_end_date   = date( 'Y-m-d', strtotime( erp_financial_end_date() ) );

        if ( $start_date >  $end_date ) {
            $this->send_error( __( 'Invalid date range', 'erp' ) );
        }

        if ( ! $valid_date_range ) {
            $this->send_error( sprintf( __( 'Date range must be within %s to %s', 'erp' ), erp_format_date( $financial_start_date ), erp_format_date( $financial_end_date ) ) );
        }

        $leave_record_exisst = erp_hrm_is_leave_recored_exist_between_date( $start_date, $end_date, $id );

        if ( $leave_record_exisst ) {
            $this->send_error( __( 'Existing Leave Record found within selected range!', 'erp' ) );
        }

        $is_policy_valid = erp_hrm_is_valid_leave_duration( $start_date, $end_date, $policy_id, $id );

        if ( ! $is_policy_valid ) {
            $this->send_error( __( 'Sorry! You do not have any leave left under this leave policy', 'erp' ) );
        }

        $days = erp_hr_get_work_days_between_dates( $start_date, $end_date );

        if ( is_wp_error( $days ) ) {
            $this->send_error( $days->get_error_message() );
        }

        // just a bit more readable date format
        foreach ( $days['days'] as &$date ) {

            $date['date'] = erp_format_date( $date['date'], 'D, M d' );
        }

        $leave_count = $days['total'];
        $days['total'] = sprintf( '%d %s', $days['total'], _n( 'day', 'days', $days['total'], 'erp' ) );

        $this->send_success( array( 'print' => $days, 'leave_count' => $leave_count ) );
    }

    /**
     * Fetch assigning policy dropdown html
     * according to employee id
     *
     * @since 0.1
     *
     * @return html|json
     */
    public function leave_assign_employee_policy() {
        $this->verify_nonce( 'wp-erp-hr-nonce' );
        $employee_id = isset( $_POST['employee_id'] ) && $_POST['employee_id'] ? intval( $_POST['employee_id'] ) : false;

        if ( ! $employee_id ) {
           $this->send_error( __( 'Please select an employee', 'erp' ) );
        }

        $policies = erp_hr_get_assign_policy_from_entitlement( $employee_id );

        if ( $policies ) {
            ob_start();
            erp_html_form_input( array(
                'label'    => __( 'Leave Type', 'erp' ),
                'name'     => 'leave_policy',
                'id'       => 'erp-hr-leave-req-leave-policy',
                'value'    => '',
                'required' => true,
                'type'     => 'select',
                'options'  => array( '' => __( '- Select -', 'erp' ) ) + $policies
            ) );
            $content = ob_get_clean();

            return $this->send_success( $content );
        }

        return $this->send_error( __( 'Selected user is not entitled to any leave policy. Set leave entitlement to apply for leave', 'erp' ) );
    }

    /**
     * Get available day for users leave policy
     *
     * @since 0.1
     *
     * @return json
     */
    public function leave_available_days() {

        $this->verify_nonce( 'wp-erp-hr-nonce' );

        $employee_id = isset( $_POST['employee_id'] ) && $_POST['employee_id'] ? intval( $_POST['employee_id'] ) : false;
        $policy_id   = isset( $_POST['policy_id'] ) && $_POST['policy_id'] ? intval( $_POST['policy_id'] ) : false;
        $available   = 0;

        if ( ! $employee_id ) {
           $this->send_error( __( 'Please select an employee', 'erp' ) );
        }

        if ( ! $policy_id ) {
           $this->send_error( __( 'Please select a policy', 'erp' ) );
        }

        $balance = erp_hr_leave_get_balance( $employee_id );

        if ( array_key_exists( $policy_id, $balance ) ) {
            $available = $balance[ $policy_id ]['entitlement'] - $balance[ $policy_id ]['total'];
        }

        if ( $available < 0 ) {
            $content = sprintf( '<span class="description red">%d %s</span>', number_format_i18n( $available ), __( 'days are available', 'erp' ) );
        } elseif ( $available > 0 ) {
            $content = sprintf( '<span class="description green">%d %s</span>', number_format_i18n( $available ), __( 'days are available', 'erp' ) );
        } else {
            $leave_policy_day = \WeDevs\ERP\HRM\Models\Leave_Policies::select( 'value' )->where( 'id', $policy_id )->pluck('value');
            $content = sprintf( '<span class="description">%d %s</span>', number_format_i18n( $leave_policy_day ), __( 'days are available', 'erp' ) );
        }

        $this->send_success( $content );
    }

    /**
     * Insert leave request for users
     *
     * Save leave request data from employee dashboard
     * overview area
     *
     * @since 0.1
     *
     * @return json
     */
    public function leave_request() {

        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'erp-leave-req-new' ) ) {
            $this->send_error( __( 'Something went wrong!', 'erp' ) );
        }

        if ( ! current_user_can( 'erp_leave_create_request' ) ) {
            $this->send_error( __( 'You do not have sufficient permissions to do this action', 'erp' ) );
        }

        $employee_id  = isset( $_POST['employee_id'] ) ? intval( $_POST['employee_id'] ) : 0;
        $leave_policy = isset( $_POST['leave_policy'] ) ? intval( $_POST['leave_policy'] ) : 0;
        $start_date   = isset( $_POST['leave_from'] ) ? sanitize_text_field( $_POST['leave_from'] ) : date_i18n( 'Y-m-d' );
        $end_date     = isset( $_POST['leave_to'] ) ? sanitize_text_field( $_POST['leave_to'] ) : date_i18n( 'Y-m-d' );
        $leave_reason = isset( $_POST['leave_reason'] ) ? strip_tags( $_POST['leave_reason'] ) : '';

        $request_id = erp_hr_leave_insert_request( array(
            'user_id'      => $employee_id,
            'leave_policy' => $leave_policy,
            'start_date'   => $start_date,
            'end_date'     => $end_date,
            'reason'       => $leave_reason
        ) );

        if ( ! is_wp_error( $request_id ) ) {

            // notification email
            $emailer = wperp()->emailer->get_email( 'New_Leave_Request' );

            if ( is_a( $emailer, '\WeDevs\ERP\Email') ) {
                $emailer->trigger( $request_id );
            }

            $this->send_success( __( 'Leave request has been submitted successfully!', 'erp' ) );
        } else {
            $this->send_error( __( 'Something went wrong, please try again.', 'erp' ) );
        }
    }

    /**
     * Get employee leave history
     *
     * @since 0.1
     *
     * @return void
     */
    public function get_employee_leave_history() {
        $this->verify_nonce( 'erp-hr-empl-leave-history' );

        $year        = isset( $_POST['year'] ) ? intval( $_POST['year'] ) : date( 'Y' );
        $employee_id = isset( $_POST['employee_id'] ) ? intval( $_POST['employee_id'] ) : 0;
        $policy      = isset( $_POST['leave_policy'] ) ? intval( $_POST['leave_policy'] ) : 'all';

        $args = array(
            'year'      => $year,
            'user_id'   => $employee_id,
            'status'    => 1,
            'orderby'   => 'req.start_date'
        );

        if ( $policy != 'all' ) {
            $args['policy_id'] = $policy;
        }

        $requests = erp_hr_get_leave_requests( $args );

        ob_start();
        include WPERP_HRM_VIEWS . '/employee/tab-leave-history.php';
        $content = ob_get_clean();

        $this->send_success( $content );
    }
}
