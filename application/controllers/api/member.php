<?php

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . "libraries/sendgrid-php/sendgrid-php.php";

if (!class_exists('Controller')) {

    class Controller extends CI_Controller
    {

    }

}

class Member extends Controller
{

    protected $ci;

    function __construct()
    {
        parent::__construct();
        $this->ci = &get_instance();
        $this->load->library('ion_auth');
        $this->ci->load->model('ion_auth_model');
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->database();
        $this->load->helper('url');
        $this->load->model('api_model');
        $this->load->model('workouts_api');
    }

    //redirect if needed, otherwise display the user list
    function index()
    {
        //set the flash data error message if there is one
        $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');


        $this->data['user'] = $this->ion_auth->get_user();
        $this->crud->use_table('trainer_clients');
        if ($trainer_request = $this->crud->retrieve(array('email' => $this->data['user']->email, 'status' => 'requested'))->row()) {
            redirect('member/confirm_trainer_request/' . $trainer_request->id, 'refresh');
        }
        $trainer_client = $this->crud->retrieve(array('client_id' => $this->data['user']->id, 'status' => 'confirmed'))->row();

        if ($this->data['user']->tos_agreement == 'false') {
            redirect('member/accept_tos', 'refresh');
        }

        if ($this->data['user']->progression_plan_id == '' && $this->ion_auth->is_group('members') && !$trainer_client) {
            redirect('member/first_run', 'refresh');
        }
        $this->crud->use_table('progression_plans');
        $this->data['current_progression_plan'] = $this->crud->retrieve(array('id' => $this->data['user']->progression_plan_id), 1)->row();

        $this->data['todays_workout'] = $this->workouts->get_workout_tree($this->session->userdata('user_id'), date('Y-m-d'));
        $this->data['featured_exercise'] = $this->workouts->get_random_exercise(array('user_id' => $this->data['user']->id, 'available_equipment' => $this->data['user']->available_equipment));
        $this->data['featured_workout'] = $this->workouts->get_random_workout();
        $this->data['upcoming_workouts'] = $this->workouts->get_upcoming_created_workouts($this->session->userdata('user_id'));

        if ($this->ion_auth->is_group('trainers')) {
            $this->data['member_group'] = 'trainer';
            $this->data['clients'] = $this->ion_auth->get_clients(false, NULL, NULL, $this->session->userdata('user_id'), 'confirmed');
        } else {
            $this->data['member_group'] = 'member';
            if ($trainer_client) {
                $this->data['trainer'] = $this->ion_auth->get_user($trainer_client->trainer_id);
            } else {
                $this->data['trainer'] = false;
            }
        }

        $this->crud->use_table('user_weather');
        $zipcodes = $this->crud->retrieve(array('user_id' => $this->data['user']->id), '', '', array('default' => 'desc'))->result_array();
        $this->load->library('weather');
        $this->data['weathers'] = array();
        foreach ($zipcodes as $locations) {
            //$this->data['weathers'][$locations['zip']] = $this->weather->get_weather($locations['zip']);
        }

        $this->data['stats'] = $this->workouts->get_dashboard_stats($this->data['user']->id);

        $this->header_data['assets'] = 'dashboard';
        $this->load->view('header', $this->header_data);
        $this->load->view('member/dashboard', $this->data);
        $this->load->view('footer');
    }

    function get_weather_ajax()
    {
        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('member/login', 'refresh');
        } else {
            $this->data['user'] = $this->ion_auth->get_user();

            $this->crud->use_table('user_weather');
            $zipcodes = $this->crud->retrieve(array('user_id' => $this->data['user']->id), '', '', array('default' => 'desc'))->result_array();
            $this->load->library('weather');

            foreach ($zipcodes as $locations) {
                //$this->data['weathers'][$locations['zip']] = $this->weather->get_weather($locations['zip']);
            }

            $this->load->view('member/get_weather_ajax', $this->data);
        }
    }

    function stats()
    {
        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('members/login', 'refresh');
        } else {
            $this->load->view('popup_header', array('assets' => 'stats'));

            $this->load->view('member/stats');

            $this->load->view('popup_footer');
        }
    }

    function get_stats_chart()
    {
        $chart = $this->workouts->get_stats_chart($this->session->userdata('user_id'));
        //$this->load->view('dashboard/profit_numbers',$profits);
    }

    function clients()
    {

        $data = $_POST;
        $mandatory_fields = array('user_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);

        if ($user && $user->group_id == 3) {

            //set the flash data error message if there is one
//            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            //list the users
//            $this->data['user'] = $this->ion_auth->get_user($data['user_id']);
            $data['clients'] = $this->api_model->get_clients($data['user_id']);
            $data['trainer_groups'] = $this->api_model->get_groups($data['user_id']);

            if ($data) {
                $this->api_model->wd_result(array('status' => 1, 'data' => $data));
            } else {
                $this->api_model->wd_result(array('status' => 0, 'message' => 'There Are No Clients'));
            }

            //            $this->crud->use_table('trainer_client_groups');
//            $this->data['trainer_groups'] = $this->crud->retrieve(array('trainer_id' => $data['user_id']))->result_array();
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Trainers does not exist with given ID'));
        }
    }

    //create  new exercise
    function create_trainer_group()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'title', 'exp_level_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);

        if ($user && $user->group_id == 3) {

            $this->form_validation->set_rules('title', 'Title', 'required');
            $this->form_validation->set_rules('exp_level_id', 'Experience Level', 'required');

            if ($this->form_validation->run() == false) { //display the form
                //set the flash data error message if there is one
                $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags((validation_errors()) ? validation_errors() : $this->session->flashdata('message'))));
            } else {
                $insert_values = array(
                    'title' => $this->input->post('title'),
                    'trainer_id' => $data['user_id'],
                    'exp_level_id' => $this->input->post('exp_level_id'),
                    'available_equipment' => $this->input->post('available_equipment')
                );

                $this->crud->use_table('trainer_client_groups');
                $new_group = $this->crud->create($insert_values);
                $group_id = $this->db->insert_id();

                if ($new_group) { //if the password was successfully changed
                    $this->crud->use_table('trainer_clients');

                    if (is_array(explode(',', $this->input->post('clients')))) {
                        foreach (explode(',', $this->input->post('clients')) as $client) {
                            $this->crud->update(array('client_id' => $client, 'trainer_id' => $data['user_id']), array('trainer_group_id' => $group_id));
                        }
                    }
                    //                    $data['clients'] = $this->api_model->get_clients($data['user_id']);
//                    $data['trainer_groups'] = $this->api_model->get_groups($data['user_id']);
                    $this->api_model->wd_result(array('status' => 1, 'message' => 'New group saved'));
                } else {
                    $this->api_model->wd_result(array('status' => 0, 'message' => 'Group failed to save'));
                }
            }
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Trainers does not exist with given ID'));
        }
    }

    function edit_trainer_group()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'group_id', 'title', 'exp_level_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);

        if ($user && $user->group_id == 3) {

            $this->form_validation->set_rules('title', 'Title', 'required');
            $this->form_validation->set_rules('exp_level_id', 'Experience Level', 'required');

            if ($this->form_validation->run() == false) { //display the form
                //set the flash data error message if there is one
                $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags((validation_errors()) ? validation_errors() : $this->session->flashdata('message'))));
            } else {
                $update_values = array(
                    'title' => $this->input->post('title'),
                    'trainer_id' => $data['user_id'],
                    'exp_level_id' => $this->input->post('exp_level_id'),
                    'available_equipment' => $this->input->post('available_equipment')
                );

                $this->crud->use_table('trainer_client_groups');
                $update = $this->crud->update(array('id' => $this->input->post('group_id')), $update_values);

                $this->crud->use_table('trainer_clients');
                $this->crud->update(array('trainer_group_id' => $this->input->post('group_id')), array('trainer_group_id' => 'NULL'));
                if (is_array(explode(',', $this->input->post('clients')))) {
                    foreach (explode(',', $this->input->post('clients')) as $client) {
                        $this->crud->update(array('client_id' => $client, 'trainer_id' => $data['user_id']), array('trainer_group_id' => $this->input->post('group_id')));
                    }
                }

                $data['clients'] = $this->api_model->get_clients($data['user_id']);
                $data['trainer_groups'] = $this->api_model->get_groups($data['user_id']);

                if ($update) { //if the password was successfully changed
                    $this->api_model->wd_result(array('status' => 1, 'message' => 'Group saved', 'data' => $data));
                } else {
                    $this->api_model->wd_result(array('status' => 0, 'message' => 'Group failed to save', 'data' => $data));
                }
            }
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Trainers does not exist with given ID'));
        }
    }

    function edit_stats()
    {
        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('member/login', 'refresh');
        } else {
            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            //list the users
            $this->data['user'] = $this->ion_auth->get_user();
            $this->crud->use_table('user_stats');
            $this->data['stats'] = $this->crud->retrieve(array('user_id' => $this->data['user']->id), '', '', array('title' => 'desc'))->result();

            $this->header_data['assets'] = 'edit_stats';
            $this->load->view('header', $this->header_data);
            $this->load->view('member/edit_stats', $this->data);
            $this->load->view('footer');
        }
    }

    function add_stat()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'title', 'measurement_type');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);
        if ($user) {
            $error_message = '';

            //validate form input
            $this->form_validation->set_rules('title', 'Title', 'required|xss_clean');

            if ($this->form_validation->run() == true) {

                $insert_data = array(
                    'user_id' => $data['user_id'],
                    'title' => $this->input->post('title'),
                    'measurement_type' => $this->input->post('measurement_type')
                );

                $this->crud->use_table('user_stats');
                $this->crud->create($insert_data);

                $stat = $this->crud->retrieve($insert_data, 1)->row();

                if ($this->input->post('starting') != '') {
                    $insert_data = array(
                        'stat_id' => $stat->id,
                        'stat_value' => $this->input->post('starting')
                    );

                    $this->db->set('date_taken', 'NOW()', false);
                    $this->db->set($insert_data);
                    $this->db->insert('user_stats_values');
                }
                $response = $this->api_model->stat_list($data['user_id']);
                $this->api_model->wd_result(array('status' => 1, 'message' => 'Stat created successfully', 'data' => $response));
            } else {
                //display the edit user form
                //set the flash data error message if there is one
                if ($error_message != '') {
                    $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags($error_message)));
                } else {
                    $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags((validation_errors()) ? ($this->ion_auth->errors() ? $this->ion_auth->errors() : validation_errors()) : $this->session->flashdata('message'))));
                }
            }
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'User does not exist'));
        }
    }

    function my_stat()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);
        if ($user) {
            $response = $this->api_model->stat_list($data['user_id']);
            if ($response) {
                $this->api_model->wd_result(array('status' => 1, 'data' => $response));
            } else {
                $this->api_model->wd_result(array('status' => 0, 'message' => 'No stats'));
            }
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'User does not exist'));
        }
    }

    function view_stat()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'stat_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);
        if ($user) {
            if (isset($data['interval']) && !empty($data['interval'])) {
                if (strtolower($data['interval']) == 'weekly') {
                    $response = $this->api_model->view_stat_weekly($data['stat_id']);
                } elseif (strtolower($data['interval']) == 'monthly') {
                    $response = $this->api_model->view_stat_monthly($data['stat_id']);
                } else {
                    $response = $this->api_model->view_stat($data['stat_id']);
                }
            } else {
                $response = $this->api_model->view_stat($data['stat_id']);
            }
            if ($response) {
                $this->api_model->wd_result(array('status' => 1, 'data' => $response));
            } else {
                $this->api_model->wd_result(array('status' => 0, 'message' => 'No Stats'));
            }
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'User does not exist'));
        }
    }

    function add_featured_exercise_to_workout()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'exercise', 'workout_id', 'choice');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);
        if ($user && $user->group_id == 2) {

            $exercise_id = $this->input->post('exercise');
            $workout_id = $this->input->post('workout_id');
            $choice = $this->input->post('choice');
            if ($choice == "add_to_section") {
                $uws_id = $this->input->post('uws');
                $exercise = $this->db->join('exercise_link_types elt', 'elt.exercise_id = exercises.id', 'left')->where('id', $exercise_id)->limit(1)->get('exercises')->result_array();
                $current_section_exercise = $this->db->where('workout_section_id', $uws_id)->order_by('display_order', 'desc')->limit(1)->get('user_workout_exercises')->result_array();
                if ($current_section_exercise) {
                    $new_exercise = $current_section_exercise[0];
                    unset($new_exercise['id']);
                    $new_exercise['exercise_id'] = $exercise_id;
                    $new_exercise['exercise_type_id'] = $exercise[0]['type_id'];
                    $new_exercise['set_type'] = $exercise[0]['type'];
                    $new_exercise['weight_option'] = $exercise[0]['weight_type'];
                    $new_exercise['display_order'] = $new_exercise['display_order'] + 1;
                    $this->db->insert('user_workout_exercises', $new_exercise);
                }
                $this->api_model->wd_result(array('status' => 1, 'message' => 'Exercise added to workout successfully'));
            } elseif ($choice == "replace") {
                $uwe_id = $this->input->post('uwe');
                $exercise = $this->db->join('exercise_link_types elt', 'elt.exercise_id = exercises.id', 'left')->where('id', $exercise_id)->limit(1)->get('exercises')->result_array();
                $current_section_exercise = $this->db->where('id', $uwe_id)->order_by('display_order', 'desc')->limit(1)->get('user_workout_exercises')->result_array();
                $new_exercise = $current_section_exercise[0];
                $new_exercise['exercise_id'] = $exercise_id;
                $new_exercise['exercise_type_id'] = $exercise[0]['type_id'];
                $new_exercise['set_type'] = $exercise[0]['type'];
                $new_exercise['weight_option'] = $exercise[0]['weight_type'];
                $new_exercise['display_order'] = $new_exercise['display_order'] + 1;
                $this->db->where('id', $uwe_id)->update('user_workout_exercises', $new_exercise);
            }
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Unable to add to workout'));
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Member does not exist with given ID'));
        }
    }

    function get_similiar_workout_exercises()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'exercise', 'workout_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);
        if ($user && $user->group_id == 2) {

            $error_message = '';

            //validate form input
            $this->form_validation->set_rules('exercise', 'Exercise', 'required|xss_clean');
            $this->form_validation->set_rules('workout_id', 'Workout', 'required|xss_clean');

            if ($this->form_validation->run() == true) {

                $exercise_id = $this->input->post('exercise');
                $workout_id = $this->input->post('workout_id');
                //                $exercise_types_result = $this->db->select('exercise_link_types.type_id')->where('exercise_id', $exercise_id)->get('exercise_link_types')->result_array();
//                if (count($exercise_types_result) > 0) {
//                    $exercise_types = '';
//                    $types = count($exercise_types_result);
//                    foreach ($exercise_types_result as $field => $value) {
//                        $types--;
//                        $exercise_types .= $value['type_id'];
//                        if ($types) {
//                            $exercise_types .= ',';
//                        }
//                    }
//
//                    $workout_exercises = $this->db->select('uwe.id,uwe.exercise_id,e.title as exercise_title,sst.title as section_title')
//                                    ->join('exercises e', 'uwe.exercise_id = e.id')
//                                    ->join('user_workout_sections uws', 'uwe.workout_section_id = uws.id')
//                                    ->join('skeleton_section_types sst', 'uws.section_type_id = sst.id')
//                                    ->where('uwe.workout_id', $workout_id)
//                                    ->where("uwe.exercise_type_id IN (" . $exercise_types . ")")
//                                    ->order_by("uws.display_order", "asc")
//                                    ->order_by("uwe.display_order", "asc")
//                                    ->get('user_workout_exercises uwe')->result_array();
//                    if (count($workout_exercises) > 0) {
//                        $this->data['exercises'] = $workout_exercises;
//                    } else {
//                        $this->data['exercises'] = 'none';
//                    }
//                } else {
//                    $this->data['exercises'] = 'none';
//                }

                $workout_sections = $this->db->select('uws.id,sst.title as section_title')
                    ->join('skeleton_section_types sst', 'uws.section_type_id = sst.id')
                    ->where('uws.workout_id', $workout_id)
                    ->order_by("display_order", "asc")
                    ->get('user_workout_sections uws')->result_array();
                $this->data['sections'] = $workout_sections;

                $this->api_model->wd_result(array('status' => 1, 'data' => $this->data));
            } else {
                //display the edit user form
                //set the flash data error message if there is one
                if ($error_message != '') {
                    $this->data['error'] = $error_message;
                } else {
                    $this->data['error'] = (validation_errors()) ? ($this->ion_auth->errors() ? $this->ion_auth->errors() : validation_errors()) : $this->session->flashdata('message');
                }

                $this->api_model->wd_result(array('status' => 0, 'data' => $this->data['error']));
            }
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Member does not exist with given ID'));
        }
    }

    function add_current_stat()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'stat_id', 'date_taken', 'stat_value');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);
        if ($user) {

            $error_message = '';

            //validate form input
            $this->form_validation->set_rules('stat_id', 'Stat Id', 'required|xss_clean');
            $this->form_validation->set_rules('stat_value', 'Current Stat Value', 'required|xss_clean');
            $this->form_validation->set_rules('date_taken', 'Date stat was taken', 'required|xss_clean');

            if ($this->form_validation->run() == true) {

                $select_data = array(
                    'user_id' => $data['user_id'],
                    'id' => $data['stat_id']
                );

                $this->crud->use_table('user_stats');
                if ($stat = $this->crud->retrieve($select_data, 1)->row()) {
                    $insert_data = array(
                        'stat_id' => $this->input->post('stat_id'),
                        'date_taken' => date('Y-m-d', strtotime($this->input->post('date_taken'))),
                        'stat_value' => $this->input->post('stat_value')
                    );
                    $this->api_model->update_new_stat($insert_data);
                }

                $response = $this->api_model->view_stat($data['stat_id']);
                $this->api_model->wd_result(array('status' => 1, 'data' => $response));
            } else {
                //display the edit user form
                //set the flash data error message if there is one
                if ($error_message != '') {
                    $this->api_model->wd_result(array('status' => 0, 'message' => $error_message));
                } else {
                    $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags((validation_errors()) ? ($this->ion_auth->errors() ? $this->ion_auth->errors() : validation_errors()) : $this->session->flashdata('message'))));
                }
            }
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'User does not exist'));
        }
    }

    function remove_stat()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'stat_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);
        if ($user) {
            if ($data['stat_id'] != '') {
                $this->crud->use_table('user_stats');
                if ($this->crud->delete(array('user_id' => $data['user_id'], 'id' => $data['stat_id']))) {
                    $this->crud->use_table('user_stats_values');
                    $this->crud->delete(array('stat_id' => $data['stat_id']));
                }
                $response = $this->api_model->view_stat($data['stat_id']);
                $this->api_model->wd_result(array('status' => 1, 'data' => $response));
            }
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'User does not exist'));
        }
    }

    function add_weather()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('member/login', 'refresh');
        }
        $error_message = '';

        //validate form input
        $this->form_validation->set_rules('zip', 'Zip Code', 'required|xss_clean');

        if ($this->form_validation->run() == true) {
            if ($this->input->post('default') == 'true') {
                $this->crud->use_table('user_weather');
                $this->crud->update(array('user_id' => $this->session->userdata('user_id')), array('default' => 'false'));
                $default = 'true';
            } else {
                $default = 'false';
            }

            $insert_data = array(
                'user_id' => $this->session->userdata('user_id'),
                'zip' => $this->input->post('zip'),
                'default' => $default
            );

            $this->crud->use_table('user_weather');
            $this->crud->create($insert_data);

            $this->data['success'] = 'true';
            echo json_encode($this->data);
        } else {
            //display the edit user form
            //set the flash data error message if there is one
            if ($error_message != '') {
                $this->data['error'] = $error_message;
            } else {
                $this->data['error'] = (validation_errors()) ? ($this->ion_auth->errors() ? $this->ion_auth->errors() : validation_errors()) : $this->session->flashdata('message');
            }

            echo json_encode($this->data);
        }
    }

    function remove_weather()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('member/login', 'refresh');
        }
        $error_message = '';

        //validate form input
        $this->form_validation->set_rules('zip', 'Zip Code', 'required|xss_clean');

        if ($this->form_validation->run() == true) {
            $this->crud->use_table('user_weather');
            $this->crud->delete(array('user_id' => $this->session->userdata('user_id'), 'zip' => $this->input->post('zip')));

            $data['success'] = 'true';
            echo json_encode($data);
        } else {
            $data['success'] = 'false';
            echo json_encode($data);
        }
    }

    function remove_workout()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('member/login', 'refresh');
        }
        $error_message = '';

        //validate form input
        $this->form_validation->set_rules('id', 'Workout', 'required|xss_clean');

        if ($this->form_validation->run() == true) {
            $this->db->delete('user_workout_exercises', array('workout_id' => $this->input->post('id')));
            $this->db->delete('user_workout_sections', array('workout_id' => $this->input->post('id')));
            $this->db->delete('user_workouts', array('id' => $this->input->post('id')));

            $data['success'] = 'true';
            echo json_encode($data);
        } else {
            $data['success'] = 'false';
            echo json_encode($data);
        }
    }

    function remove_group_workout()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('member/login', 'refresh');
        }
        $error_message = '';

        //validate form input
        $this->form_validation->set_rules('id', 'Workout', 'required|xss_clean');

        if ($this->form_validation->run() == true) {
            $user_workout = $this->db->where('id', $this->input->post('id'))->limit(1)->get('user_workouts')->row();
            $user_workouts = $this->db->where('trainer_workout_id', $user_workout->trainer_workout_id)->where('workout_date', $user_workout->workout_date)->get('user_workouts')->result_array();
            foreach ($user_workouts as $group_member_workout) {
                $this->db->delete('user_workout_exercises', array('workout_id' => $group_member_workout['id']));
                $this->db->delete('user_workout_sections', array('workout_id' => $group_member_workout['id']));
                $this->db->delete('user_workouts', array('id' => $group_member_workout['id']));
            }

            $data['success'] = 'true';
            echo json_encode($data);
        } else {
            $data['success'] = 'false';
            echo json_encode($data);
        }
    }

    function remove_trainer_workout()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('member/login', 'refresh');
        }
        $error_message = '';

        //validate form input
        $this->form_validation->set_rules('id', 'Workout', 'required|xss_clean');

        if ($this->form_validation->run() == true) {
            $id_array = explode('-', $this->input->post('id'));
            $trainer_workouts = $this->db->where('trainer_workout_id', $id_array[1])->where('user_id', $id_array[0])->where('workout_date >=', date('Y-m-d'))->where('completed', 'false')->get('user_workouts')->result_array();
            foreach ($trainer_workouts as $client_workout) {
                $this->db->delete('user_workout_exercises', array('workout_id' => $client_workout['id']));
                $this->db->delete('user_workout_sections', array('workout_id' => $client_workout['id']));
                $this->db->delete('user_workouts', array('id' => $client_workout['id']));
            }
            $this->db->where('id', $this->input->post('id'))->delete('trainer_workouts');

            $data['success'] = 'true';
            echo json_encode($data);
        } else {
            $data['success'] = 'false';
            echo json_encode($data);
        }
    }

    function remove_trainer_group_workout()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('member/login', 'refresh');
        }
        $error_message = '';

        //validate form input
        $this->form_validation->set_rules('id', 'Workout', 'required|xss_clean');

        if ($this->form_validation->run() == true) {
            $trainer_workouts = $this->db->where('trainer_workout_id', $this->input->post('id'))->where('workout_date >=', date('Y-m-d'))->where('completed', 'false')->get('user_workouts')->result_array();
            foreach ($trainer_workouts as $client_workout) {
                $this->db->delete('user_workout_exercises', array('workout_id' => $client_workout['id']));
                $this->db->delete('user_workout_sections', array('workout_id' => $client_workout['id']));
                $this->db->delete('user_workouts', array('id' => $client_workout['id']));
            }
            $this->db->where('id', $this->input->post('id'))->delete('trainer_workouts');

            $data['success'] = 'true';
            echo json_encode($data);
        } else {
            $data['success'] = 'false';
            echo json_encode($data);
        }
    }

    function request_client()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'name', 'email');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);

        if ($user && $user->group_id == 3) {

            $mailDetail = $this->api_model->user_table_detail_through_email($data['email']);

            if ($mailDetail && $mailDetail['group_id'] == 2) {

                $error_message = '';

                //validate form input
                $this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
                $this->form_validation->set_rules('email', 'Email', 'required|xss_clean');
                $this->form_validation->set_rules('email_message', 'Message', 'xss_clean');

                if ($this->form_validation->run() == true) {

                    $this->crud->use_table('trainer_clients');
                    if ($current_clients = $this->crud->retrieve(array('email' => $this->input->post('email'), 'trainer_id' => $data['user_id'], 'status' => 'denied'), 1)->row()) {
                        $this->db->update('trainer_clients', ['status' => 'requested'], ['id' => $current_clients->id]);
                    } elseif ($current_clients = $this->crud->retrieve(array('email' => $this->input->post('email'), 'trainer_id' => $data['user_id']), 1)->row()) {
                        $error_message = "You have already requested to train this client";
                    } else {
                        $insert_data = array(
                            'name' => $this->input->post('name'),
                            'trainer_id' => $data['user_id'],
                            'email' => $this->input->post('email'),
                            'email_message' => $this->input->post('email_message')
                        );
                    }
                }

                if ($error_message == '' && $this->form_validation->run() == true) { //check to see if we are creating the user
                    //redirect them back to the admin page
                    if ($insert_data) {
                        $this->crud->create($insert_data);
                    }
                    $data['name'] = $this->input->post('name');
                    $data['email'] = $this->input->post('email');
                    $data['email_message'] = $this->input->post('email_message');
                    $data['trainer_name'] = $user->first_name . ' ' . $user->last_name;
                    $data['trainer_request_code'] = 'TEST';
                    $message = $this->load->view('member/email/request_client.tpl.php', $data, true);
                    $this->email->clear();
                    $config['mailtype'] = $this->config->item('email_type', 'ion_auth');
                    $this->email->initialize($config);
                    $this->email->set_newline("\r\n");
                    $this->email->from($this->config->item('admin_email', 'ion_auth'), 'Trnhrd on behalf of ' . $data['trainer_name']);
                    $this->email->to($this->input->post('email'));
                    $this->email->subject('Trnhrd - Trainer Request');
                    $this->email->message($message);

                    $data['clients'] = $this->api_model->get_clients($data['user_id']);
                    $data['trainer_groups'] = $this->api_model->get_groups($data['user_id']);

                    if ($this->email->send()) {
                        $this->api_model->wd_result(array('status' => 1, 'message' => "Your message has been sent to the client", 'data' => $data));
                    } else {
                        $this->api_model->wd_result(array('status' => 0, 'message' => "Your message failed to send. Please check the email you entered and try again. If you continue to get this message, please contact support.", 'data' => $data));
                    }
                    $this->send_notifications( $data['clients']['device_token'], "Trnhrd - Trainer Request");
                } else {
                    //display the edit user form
                    //set the flash data error message if there is one
                    if ($error_message != '') {
                        $this->api_model->wd_result(array('status' => 0, 'message' => $error_message));
                    } else {
                        $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags((validation_errors()) ? ($this->ion_auth->errors() ? $this->ion_auth->errors() : validation_errors()) : $this->session->flashdata('message'))));
                    }
                }
            } else {
                $this->api_model->wd_result(array('status' => 0, 'message' => 'You Can Only Send Request To Members'));
            }
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Trainers does not exist with given ID'));
        }
    }

    function accept_tos()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('member/login', 'refresh');
        }

        //validate form input
        $this->form_validation->set_rules('terms_accept', 'Terms of Use', 'required|xss_clean');

        if ($this->form_validation->run() == true) { //check to see if we are creating the user
            //redirect them back to the admin page
            $update_data = array('tos_agreement' => 'true');
            $this->crud->use_table('meta');
            $this->crud->update(array('user_id' => $this->session->userdata('user_id')), $update_data);
            redirect("member", 'refresh');
        } else {
            //display the edit user form
            //set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            $this->data['terms_accept'] = array(
                'name' => 'terms_accept',
                'id' => 'terms_accept',
                'value' => 'accept',
                'class' => 'required'
            );

            $this->load->view('header');
            $this->load->view('member/accept_tos', $this->data);
            $this->load->view('footer');
        }
    }

    function workout_generator()
    {
        if (!$this->ion_auth->logged_in()) {
            //redirect them to the login page
            redirect('member/login', 'refresh');
        } elseif (!$this->ion_auth->is_group('trainers')) {
            redirect('member', 'refresh');
        } else {

            if ($this->uri->segment(3) == 'new' && $this->uri->segment(4) != '' && $this->uri->segment(5) != '') {
                $this->data['workout_date'] = date('n/j/Y', strtotime($this->uri->segment(4)));
                $this->data['client_id'] = $this->uri->segment(5);
            }

            if ($this->uri->segment(3) == 'workout' && $this->uri->segment(4) != '') {
                $this->data['workout_id'] = $this->uri->segment(4);
            }

            if ($this->uri->segment(3) == 'group_workout' && $this->uri->segment(4) != '') {
                $this->data['group_workout_id'] = $this->uri->segment(4);
            }

            if ($this->uri->segment(3) == 'trainer_workout' && $this->uri->segment(4) != '') {
                $this->data['trainer_workout_id'] = $this->uri->segment(4);
            }

            if ($this->uri->segment(3) == 'trainer_group_workout' && $this->uri->segment(4) != '') {
                $this->data['trainer_group_workout_id'] = $this->uri->segment(4);
            }

            $user = $this->ion_auth->get_user();
            $this->data['exercise_library'] = $this->workouts->get_exercise_library();
            $this->data['clients'] = $this->ion_auth->get_clients(false, NULL, NULL, $this->session->userdata('user_id'), 'confirmed');

            $this->crud->use_table('trainer_client_groups');
            $this->data['trainer_groups'] = $this->crud->retrieve(array('trainer_id' => $this->session->userdata('user_id')))->result_array();

            $user_workoutdays = $this->input->post('workoutdays');

            $weekdays = array(1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday');
            foreach ($weekdays as $day => $title) {
                $this->data['weekday_title'][$day] = $title;
                if (is_array($user_workoutdays) && in_array($day, $user_workoutdays)) {
                    $this->data['weekdays'][$day] = array(
                        'name' => 'workoutdays[]',
                        'checked' => TRUE,
                        'value' => $day,
                        'class' => 'days'
                    );
                } else {
                    $this->data['weekdays'][$day] = array(
                        'name' => 'workoutdays[]',
                        'checked' => FALSE,
                        'value' => $day,
                        'class' => 'days'
                    );
                }
            }

            $user_equipment = $user->available_equipment;
            $this->crud->use_table('equipment');
            $query = $this->crud->retrieve();
            foreach ($query->result() as $equipment) {
                $this->data['equipment'][$equipment->id] = $equipment->title;
                if (is_array($user_equipment) && in_array($equipment->id, $user_equipment)) {
                    $this->data['available_equipment'][$equipment->id] = array(
                        'name' => 'available_equipment[]',
                        'checked' => TRUE,
                        'value' => $equipment->id,
                        'class' => 'equipment',
                        'id' => 'equipment' . $equipment->id
                    );
                } else {
                    $this->data['available_equipment'][$equipment->id] = array(
                        'name' => 'available_equipment[]',
                        'checked' => FALSE,
                        'value' => $equipment->id,
                        'class' => 'equipment',
                        'id' => 'equipment' . $equipment->id
                    );
                }
            }

            $this->data['skeleton_workout_id'] = $this->form_validation->set_value('skeleton_workouts');
            $this->data['skeleton_workouts'] = array('' => 'Choose Workout Type');
            $this->crud->use_table('skeleton_workouts');
            $query = $this->crud->retrieve();
            foreach ($query->result() as $workout) {
                $this->data['skeleton_workouts'][$workout->id] = $workout->title;
            }

            $this->data['progression_id'] = $this->form_validation->set_value('progressions');
            $this->data['progressions'] = array('' => 'Choose Progression');
            $this->crud->use_table('progressions');
            $query = $this->crud->retrieve();
            foreach ($query->result() as $progression) {
                $this->data['progressions'][$progression->id] = $progression->title;
            }

            $this->header_data['assets'] = 'workout_generator';
            $this->load->view('header', $this->header_data);
            $this->load->view('member/workout_generator', $this->data);
            $this->load->view('footer');
        }
    }

    function process_generator()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);

        if ($user && $user->group_id == 3) {
            $date = $this->input->post('date');
            $dates = explode(' - ', $date);
            if (count($dates) > 1) {
                $start_date = $dates[0];
                $end_date = $dates[1];
                $weekdays = explode(',', $this->input->post('days'));
            } else {
                $single_date = $date;
            }

            $user_type = 'single';

            if ($this->input->post('client') != '' && stristr($this->input->post('client'), 'group')) {
                $user_type = 'group';
                $id_array = explode('-', $this->input->post('client'));
                $users = $this->db->select(array('trainer_clients.client_id as id'))->where('trainer_group_id', $id_array[1])->get('trainer_clients')->result();
                $group = $this->db->where('id', $id_array[1])->where('trainer_id', $data['user_id'])->limit(1)->get('trainer_client_groups')->row();
            } elseif ($this->input->post('client') != '') {
                $user = $this->ion_auth->get_user($this->input->post('client'));
                $users[] = $user;
            } else {
                $user = $this->ion_auth->get_user($data['user_id']);
                $users[] = $user;
            }

            if ($this->input->post('group_workout_id') != '') {
                $user_workout = $this->db->where('id', $this->input->post('group_workout_id'))->limit(1)->get('user_workouts')->row();
                $user_workouts = $this->db->where('trainer_workout_id', $user_workout->trainer_workout_id)->where('workout_date', $user_workout->workout_date)->get('user_workouts')->result_array();
                foreach ($user_workouts as $group_member_workout) {
                    $this->db->delete('user_workout_exercises', array('workout_id' => $group_member_workout['id']));
                    $this->db->delete('user_workout_sections', array('workout_id' => $group_member_workout['id']));
                }
                //$this->db->delete('user_workouts', array('id' => $this->input->post('workout_id')));
            } elseif ($this->input->post('workout_id') != '') {
                $this->db->delete('user_workout_exercises', array('workout_id' => $this->input->post('workout_id')));
                $this->db->delete('user_workout_sections', array('workout_id' => $this->input->post('workout_id')));
                //$this->db->delete('user_workouts', array('id' => $this->input->post('workout_id')));
            }

            if ($this->input->post('trainer_workout_id') != '') {
                $trainer_workouts = $this->db->where('trainer_workout_id', $this->input->post('trainer_workout_id'))->where('workout_date >=', date('Y-m-d'))->get('user_workouts')->result_array();
                foreach ($trainer_workouts as $client_workout) {
                    $this->db->delete('user_workout_exercises', array('workout_id' => $client_workout['id']));
                    $this->db->delete('user_workout_sections', array('workout_id' => $client_workout['id']));
                    $this->db->delete('user_workouts', array('id' => $client_workout['id']));
                }
            } elseif ($this->input->post('trainer_group_workout_id') != '') {
                $trainer_workouts = $this->db->where('trainer_workout_id', $this->input->post('trainer_group_workout_id'))->where('workout_date >=', date('Y-m-d'))->get('user_workouts')->result_array();
                foreach ($trainer_workouts as $client_workout) {
                    $this->db->delete('user_workout_exercises', array('workout_id' => $client_workout['id']));
                    $this->db->delete('user_workout_sections', array('workout_id' => $client_workout['id']));
                    $this->db->delete('user_workouts', array('id' => $client_workout['id']));
                }
            }

            if ($this->input->post('workout_title') != '') {
                $workout_title = $this->input->post('workout_title');
            } elseif ($this->input->post('skeleton_workout_id') != '') {
                $this->crud->use_table('skeleton_workouts');
                $skeleton_workout = $this->crud->retrieve(array('id' => $this->input->post('skeleton_workout_id')), 1)->row();
                $workout_title = $skeleton_workout->title;
            } else {
                $workout_title = 'Workout';
            }

            if ($this->input->post('progression_id') != '') {
                $progression_id = $this->input->post('progression_id');
            } else {
                $progression_id = 'NULL';
            }


            if (isset($start_date)) {
                $end_date = strtotime($end_date);
                if ($user_type == 'group') {
                    $trainer_workout_values = array('trainer_id' => $data['user_id'], 'trainer_group_id' => $id_array[1], 'start_date' => date("Y-m-d", strtotime($start_date)), 'end_date' => date("Y-m-d", $end_date), 'days' => implode(',', $weekdays));
                } else {
                    $trainer_workout_values = array('trainer_id' => $data['user_id'], 'user_id' => $user->id, 'start_date' => date("Y-m-d", strtotime($start_date)), 'end_date' => date("Y-m-d", $end_date), 'days' => implode(',', $weekdays));
                }
                if ($this->input->post('trainer_workout_id') != '') {
                    $trainer_workout_id = $this->input->post('trainer_workout_id');
                    $this->db->where('id', $trainer_workout_id)->where('trainer_id', $data['user_id'])->update('trainer_workouts', $trainer_workout_values);
                } elseif ($this->input->post('trainer_group_workout_id') != '') {
                    $trainer_workout_id = $this->input->post('trainer_group_workout_id');
                    $this->db->where('id', $trainer_workout_id)->where('trainer_id', $data['user_id'])->update('trainer_workouts', $trainer_workout_values);
                } else {
                    $this->db->insert('trainer_workouts', $trainer_workout_values);
                    $trainer_workout_id = $this->db->insert_id();
                }

                foreach ($users as $user) {
                    $days = array(1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday');
                    foreach ($weekdays as $day) {
                        $current_day = '';
                        $current_day = strtotime('next ' . $days[$day]);

                        while ($current_day < $end_date) {

                            $workout_values = array('trainer_workout_id' => $trainer_workout_id, 'progression_id' => $progression_id, 'user_id' => $user->id, 'workout_date' => date("Y-m-d", $current_day), 'title' => $workout_title);
                            if ($user_type == 'group') {
                                $workout_values['trainer_group_id'] = $id_array[1];
                            }
                            $this->db->insert('user_workouts', $workout_values);
                            $workout_id = $this->db->insert_id();

                            $current_day = strtotime(date("Y-m-d", $current_day) . " +1 week");

                            if (isset($workout_id)) {
                                foreach ($this->input->post('workout') as $index => $section) {
                                    $section = explode(';;', $section);
                                    $s_value = explode("-", $section[0]);
                                    $section_values = array('workout_id' => $workout_id, 'section_type_id' => $s_value[0], 'display_order' => $index);
                                    if (isset($s_value[1]) && $s_value[1] != 'undefined') {
                                        $section_values['section_rest'] = $s_value[1];
                                    }
                                    $this->db->insert('user_workout_sections', $section_values);
                                    $workout_section_id = $this->db->insert_id();
                                    foreach ($section as $index2 => $cat_exercise) {
                                        if ($index2 != 0) {
                                            $value = explode("-", $cat_exercise);
                                            $sets = substr($value[2], 0, (strlen($value[2]) - 1));
                                            $reps = substr($value[3], 0, (strlen($value[3]) - 1));
                                            $rest = substr($value[4], 0, (strlen($value[4]) - 1));
                                            $weight = substr($value[5], 0, (strlen($value[5]) - 1));
                                            $time = substr($value[6], 0, (strlen($value[6]) - 1));
                                            $exercise_values = array(
                                                'exercise_id' => $value[1],
                                                'workout_id' => $workout_id,
                                                'display_order' => $index2,
                                                'exercise_type_id' => $value[0],
                                                'workout_section_id' => $workout_section_id,
                                                'sets' => $sets,
                                                'reps' => $reps,
                                                'rest' => $rest,
                                                'weight' => $weight,
                                                'time' => $time,
                                                'set_type' => $value[7],
                                                'weight_option' => $value[8]
                                            );
                                            $this->db->insert('user_workout_exercises', $exercise_values);
                                        }
                                    }
                                }
                                $this->db->where('id', $workout_id)->where('user_id', $user->id)->update('user_workouts', array('workout_created' => 'true'));
                            }
                        }
                    }
                }
            } else {
                if ($user_type == 'group') {
                    $trainer_workout_values = array('trainer_id' => $data['user_id'], 'trainer_group_id' => $id_array[1], 'start_date' => date("Y-m-d", strtotime($single_date)));
                } else {
                    $trainer_workout_values = array('trainer_id' => $data['user_id'], 'user_id' => $user->id, 'start_date' => date("Y-m-d", strtotime($single_date)));
                }
                $this->db->insert('trainer_workouts', $trainer_workout_values);
                $trainer_workout_id = $this->db->insert_id();
                if ($this->input->post('group_workout_id') != '') {
                    $orig_user_workout = $this->db->where('id', $this->input->post('group_workout_id'))->limit(1)->get('user_workouts')->row();
                }
                foreach ($users as $user) {

                    $workout_values = array('user_id' => $user->id, 'trainer_workout_id' => $trainer_workout_id, 'progression_id' => $progression_id, 'title' => $workout_title, 'workout_date' => date('Y-m-d', strtotime($date)));
                    if ($user_type == 'group') {
                        $workout_values['trainer_group_id'] = $id_array[1];
                    }
                    if ($this->input->post('group_workout_id') != '') {
                        $user_workout = $this->db->where('user_id', $user->id)->where('trainer_workout_id', $orig_user_workout->trainer_workout_id)->where('workout_date', $orig_user_workout->workout_date)->limit(1)->get('user_workouts')->row();
                        $workout_id = $user_workout->id;
                        $this->db->where('id', $workout_id)->where('user_id', $user->id)->update('user_workouts', $workout_values);
                    } elseif ($this->input->post('workout_id') != '') {
                        $workout_id = $this->input->post('workout_id');
                        $this->db->where('id', $workout_id)->where('user_id', $user->id)->update('user_workouts', $workout_values);
                    } else {
                        $this->db->insert('user_workouts', $workout_values);
                        $workout_id = $this->db->insert_id();
                    }

                    if (isset($workout_id)) {
                        foreach ($this->input->post('workout') as $index => $section) {
                            $section = explode(';;', $section);
                            $s_value = explode("-", $section[0]);
                            $section_values = array('workout_id' => $workout_id, 'section_type_id' => $s_value[0], 'display_order' => $index);
                            if (isset($s_value[1])) {
                                $section_values['section_rest'] = $s_value[1];
                            }
                            $this->db->insert('user_workout_sections', $section_values);
                            $workout_section_id = $this->db->insert_id();
                            foreach ($section as $index2 => $cat_exercise) {
                                if ($index2 != 0) {
                                    $value = explode("-", $cat_exercise);
                                    $sets = substr($value[2], 0, (strlen($value[2]) - 1));
                                    $reps = substr($value[3], 0, (strlen($value[3]) - 1));
                                    $rest = substr($value[4], 0, (strlen($value[4]) - 1));
                                    $weight = substr($value[5], 0, (strlen($value[5]) - 1));
                                    $time = substr($value[6], 0, (strlen($value[6]) - 1));
                                    $exercise_values = array(
                                        'exercise_id' => $value[1],
                                        'workout_id' => $workout_id,
                                        'display_order' => $index2,
                                        'exercise_type_id' => $value[0],
                                        'workout_section_id' => $workout_section_id,
                                        'sets' => $sets,
                                        'reps' => $reps,
                                        'rest' => $rest,
                                        'weight' => $weight,
                                        'time' => $time,
                                        'set_type' => $value[7],
                                        'weight_option' => $value[8]
                                    );
                                    $this->db->insert('user_workout_exercises', $exercise_values);
                                }
                            }
                        }
                        $this->db->where('id', $workout_id)->where('user_id', $user->id)->update('user_workouts', array('workout_created' => 'true'));
                    }
                }
            }
            if ($user_type == 'group') {
                $this->api_model->wd_result(array('status' => 1, 'message' => "The new workout(s) have been added to " . $group->title . "\'s workout log."));
            } else {
                $this->api_model->wd_result(array('status' => 1, 'message' => "The new workout(s) have been added to " . $user->first_name . " " . $user->last_name . " 's workout log."));
            }
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Trainers does not exist with given ID'));
        }
    }

    function save_logbook_stats()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'date', 'workout_id');
        $this->api_model->validate($mandatory_fields, $data);

        $workout_date = $this->input->post('workout_date');
        $uw_id = $this->input->post('workout_id');
        $uw_row = $this->db->where('id', $uw_id)->where('user_id', $data['user_id'])->get('user_workouts')->row();
        if ($uw_row) {

            $_POST['uwe'] = unserialize(base64_decode($_POST['uwe']));
            foreach ($_POST['uwe'] as $uwe) {
                $this->db->where('user_id', $data['user_id'])->where('uw_id', $uw_id)->where('uwe_id', $uwe['uwe_id'])->delete('user_workout_stats');
                $uwe_row = $this->db->where('workout_id', $uw_id)->where('id', $uwe['uwe_id'])->get('user_workout_exercises')->row();
                if ($uwe_row) {
                    foreach ($uwe['sets'] as $index => $set) {
                        unset($insert_data);
                        $insert_data = array(
                            'uw_id' => $uw_id,
                            'uwe_id' => $uwe['uwe_id'],
                            'workout_date' => $workout_date,
                            'user_id' => $data['user_id'],
                            'progression_id' => $uw_row->progression_id,
                            'exercise_id' => $uwe_row->exercise_id,
                            'difficulty' => $uwe['difficulty'],
                            'set' => $set
                        );

                        if (isset($uwe['reps'][$index])) {
                            $insert_data['reps'] = $uwe['reps'][$index];
                        } elseif (isset($uwe['time'][$index])) {
                            $insert_data['time'] = $uwe['time'][$index];
                        }

                        if (isset($uwe['weight'][$index]) && $uwe['weight'][$index] > 0) {
                            $insert_data['weight'] = $uwe['weight'][$index];
                        }

                        $this->db->insert('user_workout_stats', $insert_data);
                    }
                }
            }

            if ($uw_row->completed != 'true' && $uw_row->progression_id != '') {
                $current_progression = $this->db->where('progression_id', $uw_row->progression_id)->where('user_id', $data['user_id'])->get('user_progressions')->row();
                if ($current_progression) {
                    $update_data = array('session_count' => ($current_progression->session_count + 1));
                    $this->db->where('progression_id', $uw_row->progression_id)->where('user_id', $data['user_id'])->update('user_progressions', $update_data);
                } else {
                    $insert_data = array('user_id' => $data['user_id'], 'progression_id' => $uw_row->progression_id, 'session_count' => 1);
                    $this->db->insert('user_progressions', $insert_data);
                }
            }

            $this->db->where('id', $uw_id)->where('user_id', $data['user_id'])->update('user_workouts', array('completed' => 'true'));


            if ($uw_row->progression_plan_id != '') {
                //going to generate next workout here
                $this->workouts->create_next_workout($data['user_id']);
            }
            $this->api_model->wd_result(array('status' => 1, 'message' => "Your workout stats have been saved and your next workout has been generated based on these stats"));
        }
        $this->api_model->wd_result(array('status' => 0, 'message' => 'Your workout stats failed to save'));
    }

    function save_log_stats()
    {
        $user_id = $this->input->post('user_id');
        $workout_date = $this->input->post('workout_date');
        $exercise_id = $this->input->post('exercise_id');
        $uw_id = $this->input->post('uw_id');
        $uwe_id = $this->input->post('uwe_id');
        $sets = $this->input->post('sets');
        $reps = $this->input->post('reps');
        $weight = $this->input->post('weight');
        $time = $this->input->post('time');
        $sets = $this->input->post('sets');
        $difficulty = $this->input->post('difficulty');

        $this->db->where('user_id', $user_id)->where('uw_id', $uw_id)->where('uwe_id', $uwe_id)->delete('user_workout_stats');

        foreach ($sets as $set) {
            if (isset($weight[$set])) {
                $set_weight = $weight[$set];
            } else {
                $set_weight = null;
            }
            echo $set;
            if (isset($time[$set])) {
                $set_time = $time[$set];
            } else {
                $set_time = null;
            }
            $data = array(
                'uw_id' => $uw_id,
                'uwe_id' => $uwe_id,
                'workout_date' => $workout_date,
                'user_id' => $user_id,
                'exercise_id' => $exercise_id,
                'difficulty' => $difficulty,
                'set' => $set,
                'weight' => $set_weight,
                'time' => $set_time,
                'reps' => $reps[$set]
            );
            $this->db->insert('user_workout_stats', $data);
        }
    }

    function get_client()
    {
        if ($this->input->post('id') != '') {
            $id = $this->input->post('id');
        } else {
            echo 'false';
        }

        $client = $this->ion_auth->get_user_array($id);
        echo json_encode($client);
    }

    function generator_get_client()
    {
        if ($this->input->post('id') != '') {
            $id = $this->input->post('id');
        } else {
            echo 'false';
        }

        if (stristr($id, 'group')) {
            $id_array = explode('-', $id);
            $group = $this->db->where('id', $id_array[1])->get('trainer_client_groups')->row_array();
            $this->db->select(array('meta.first_name', 'meta.last_name'));
            $this->db->join('meta', 'meta.user_id = trainer_clients.client_id');
            $group['clients'] = $this->db->where('trainer_clients.trainer_group_id', $id_array[1])->get('trainer_clients')->result_array();
            $group['type'] = 'group';
            echo json_encode($group);
        } else {
            $client = $this->ion_auth->get_user_array($id);
            $client['type'] = 'client';
            echo json_encode($client);
        }
    }

    //        function skeleton_json() {
//            if ($this->input->post('id') != '') {
//                $id = $this->input->post('id');
//            } else {
//                return false;
//            }
//
//            if ($this->input->post('user_id') != '') {
//                $user_id = $this->input->post('user_id');
//            } else {
//                $user_id = $this->session->userdata('user_id');
//            }
//
//            if ($this->input->post('progression_id') != '') {
//                $progression_id = $this->input->post('progression_id');
//            } else {
//                $progression_id = '';
//            }
//
//            if ($this->input->post('available_equipment') != '') {
//                $available_equipment = $this->input->post('available_equipment');
//            } else {
//                $available_equipment = '';
//            }
//            return $this->workouts->get_skeleton_generator(array('id' => $id, 'progression_id' => $progression_id, 'user_id' => $user_id, 'available_equipment' => $available_equipment));
//        }

    function get_workout_for_generator()
    {
        if ($this->input->post('group_id') != '') {
            $id = $this->input->post('group_id');
        } elseif ($this->input->post('id') != '') {
            $id = $this->input->post('id');
        } else {
            return false;
        }

        return $this->workouts->get_workout_for_generator(array('id' => $id));
    }

    function get_workout_details_for_generator()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('member/login', 'refresh');
        }

        if ($this->input->post('id') != '') {
            $id = $this->input->post('id');
            $this->crud->use_table('user_workouts');
            $this->data['details'] = $this->crud->retrieve(array('id' => $id), 1)->row();

            $this->data['success'] = 'true';
            echo json_encode($this->data);
        } elseif ($this->input->post('group_id') != '') {

            $id = $this->input->post('group_id');
            $this->crud->use_table('user_workouts');
            $this->data['details'] = $this->crud->retrieve(array('id' => $id), 1)->row();

            $this->data['success'] = 'true';
            echo json_encode($this->data);
        } elseif ($this->input->post('trainer_workout_id') != '') {
            $id = $this->input->post('trainer_workout_id');
            $this->data['details'] = $this->db->select(array('user_workouts.*', 'user_workouts.id as workout_id', 'trainer_workouts.*'))->join('user_workouts', 'user_workouts.trainer_workout_id = trainer_workouts.id')->where('trainer_workouts.id', $id)->limit(1)->get('trainer_workouts')->row();

            $this->data['success'] = 'true';
            echo json_encode($this->data);
        } elseif ($this->input->post('trainer_group_workout_id') != '') {
            $id = $this->input->post('trainer_group_workout_id');
            $this->data['details'] = $this->db->select(array('user_workouts.*', 'user_workouts.id as workout_id', 'trainer_workouts.*'))->join('user_workouts', 'user_workouts.trainer_workout_id = trainer_workouts.id')->where('trainer_workouts.id', $id)->limit(1)->get('trainer_workouts')->row();

            $this->data['success'] = 'true';
            echo json_encode($this->data);
        } else {
            $this->data['success'] = 'false';
            echo json_encode($this->data);
        }
    }

    function popup_video()
    {
        $this->crud->use_table('exercises');
        $this->data['exercise'] = $this->crud->retrieve(array('id' => $this->uri->segment(3)), 1)->row();
        $this->load->view('member/video_player', $this->data);
    }

    function edit_photo()
    {
        $user = $this->ion_auth->get_user();
        $this->data['user'] = $user;
        $s = strtoupper(md5(uniqid(rand(), true)));
        $guid = substr($s, 0, 8) . '-' .
            substr($s, 8, 4) . '-' .
            substr($s, 12, 4) . '-' .
            substr($s, 16, 4) . '-' .
            substr($s, 20);
        $config['file_name'] = $guid . '.jpg';
        $config['overwrite'] = true;

        $config['upload_path'] = './images/member_photos/';
        $config['allowed_types'] = 'gif|jpg|png';



        $this->load->library('upload', $config);

        if ($this->input->post('upload') != '' && $this->upload->do_upload('photo')) {
            $upload_data = $this->upload->data();

            $this->load->library('image_lib');
            $orig_image = $_SERVER['DOCUMENT_ROOT'] . '/images/member_photos/' . $upload_data['file_name'];
            $sm_image = $_SERVER['DOCUMENT_ROOT'] . '/images/member_photos/sm_' . $upload_data['file_name'];

            $config['image_library'] = 'gd2';
            $config['source_image'] = $orig_image;
            $config['maintain_ratio'] = TRUE;
            $config['width'] = 160;
            $config['height'] = 180;

            $this->image_lib->clear();
            $this->image_lib->initialize($config);
            $this->image_lib->resize();


            $config['image_library'] = 'gd2';
            $config['source_image'] = $orig_image;
            $config['new_image'] = $sm_image;
            $config['thumb_marker'] = '';
            $config['create_thumb'] = TRUE;
            $config['maintain_ratio'] = TRUE;
            $config['width'] = 80;
            $config['height'] = 80;

            $this->image_lib->clear();
            $this->image_lib->initialize($config);

            if ($this->image_lib->resize()) {
                $this->ion_auth_model->update_user($user->id, array('photo' => $upload_data['file_name']));
                $this->session->set_flashdata('message', "Photo successfully uploaded");
                redirect("member", 'refresh');
            } else {
                $this->data['error'] = $this->upload->display_errors();

                $this->load->view('header');

                $this->load->view('member/edit_photo', $this->data);

                $this->load->view('footer');
            }
        } else {
            $this->data['error'] = $this->upload->display_errors();
            $this->data['judge'] = $this->ion_auth->get_user($this->uri->segment(3));
            $this->load->view('header', array('main_menu' => 'admin'));

            $this->load->view('member/edit_photo', $this->data);

            $this->load->view('footer');
        }
    }

    //log the user in
    function login()
    {
        $data = $_POST;
        $mandatory_fields = array('username', 'password');
        $this->api_model->validate($mandatory_fields, $data);

        //validate form input
//        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == true) { //check to see if the user is logging in
            //check for "remember me"
            $remember = (bool) $this->input->post('remember');
            if ($this->api_model->login($this->input->post('username'), $this->input->post('password'), $remember)) { //if the login is successful
                $loggedin_user = $this->api_model->user_table_detail_through_email($this->input->post('username'));
                if (isset($data['device_type']) && !empty($data['device_type'])) {
                    $deviceUpdate['device_type'] = $data['device_type'];
                }
                if (isset($data['device_token']) && !empty($data['device_token'])) {
                    $deviceUpdate['device_token'] = $data['device_token'];
                }
                if (isset($deviceUpdate) && !empty($deviceUpdate)) {
                    $this->api_model->updateDeviceField($loggedin_user['id'], $deviceUpdate);
                }
                $user = $this->api_model->user_detail_by_user_id($loggedin_user['id']);
                //                $user = $this->ion_auth->get_user($loggedin_user['id']);
                $this->api_model->wd_result(array('status' => 1, 'message' => "You Are Login Successfully", 'data' => $user));
                //                redirect('member', 'refresh');
            } else {
                $this->api_model->wd_result(array('status' => 0, 'message' => 'In-Correct Login'));
            }
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags((validation_errors()) ? validation_errors() : $this->session->flashdata('message'))));
        }
    }

    function first_run()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'progression_plan_id', 'workoutdays', 'exp_level_id', 'available_equipment');
        $this->api_model->validate($mandatory_fields, $data);

        //validate form input
        $this->form_validation->set_rules('exp_level_id', 'Experience Level', 'required');
        //        $this->data['user'] = $this->ion_auth->get_user();
//        $this->crud->use_table('trainer_clients');
//        $trainer_client = $this->crud->retrieve(array('client_id' => $this->data['user']->id, 'status' => 'confirmed'))->row();
//        if (!$trainer_client) {
//            $this->form_validation->set_rules('progression_plan_id', 'Progression Plan', 'required');
//        }


        if ($this->form_validation->run() == true) { //check to see if the user is logging in
            //check for "remember me"
            if ($this->input->post('progression_plan_id') != '') {
                $this->crud->use_table('progression_plans');
                $progression_plan = $this->crud->retrieve(array('id' => $this->input->post('progression_plan_id')), 1)->row();

                if ($progression_plan->days_week != count(explode(',', $this->input->post('workoutdays')))) {
                    $error_message = 'You must select ' . $progression_plan->days_week . ' days a week for the ' . $progression_plan->title . ' Plan';
                    $other_error = true;
                } else {

                    $user_values = array(
                        'exp_level_id' => $this->input->post('exp_level_id'),
                        'progression_plan_id' => $this->input->post('progression_plan_id'),
                        'available_equipment' => $this->input->post('available_equipment'),
                        'workoutdays' => $this->input->post('workoutdays')
                    );
                    if ($this->ion_auth->update_user($data['user_id'], $user_values)) { //if the login is successful
                        //redirect them back to the home page
                        $this->workouts->assign_available_exercises($data['user_id']);
                        $this->workouts->progression_change_workouts($data['user_id']);
                        $user = $this->api_model->user_detail_by_user_id($data['user_id']);
                        //                        $user = $this->ion_auth->get_user($data['user_id']);
                        $this->api_model->wd_result(array('status' => 1, 'message' => strip_tags($this->ion_auth->messages()), 'data' => $user));
                    } else { //if the login was un-successful
                        //redirect them back to the login page
                        $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags($this->ion_auth->errors())));
                    }
                }
            } else {
                //                $user = $this->ion_auth->get_user();
                $user = $this->api_model->user_detail_by_user_id($data['user_id']);
                $user_values = array(
                    'exp_level_id' => $this->input->post('exp_level_id'),
                    'available_equipment' => $this->input->post('available_equipment')
                );
                if ($this->ion_auth->update_user($data['user_id'], $user_values)) { //if the login is successful
                    //redirect them back to the home page
                    $this->workouts->assign_available_exercises($data['user_id']);
                    $this->api_model->wd_result(array('status' => 1, 'message' => strip_tags($this->ion_auth->messages()), 'data' => $user));
                } else { //if the login was un-successful
                    //redirect them back to the login page
                    $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags($this->ion_auth->errors())));
                }
            }
        }

        if ($this->form_validation->run() == false || $other_error == true) { //the user is not logging in so display the login page
            //set the flash data error message if there is one
            if ($other_error) {
                $this->api_model->wd_result(array('status' => 0, 'message' => $error_message));
            } else {
                $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags((validation_errors()) ? validation_errors() : $this->session->flashdata('message'))));
            }
        }
    }

    function confirm_trainer_request()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'request_id', 'decision');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);

        if ($user && $user->group_id == 2) {

            $other_error = false;
            //validate form input
            $this->form_validation->set_rules('decision', 'Decision', 'required');

            if ($this->form_validation->run() == true) { //check to see if the user is logging in
                //check for "remember me"
                if ($this->input->post('decision') == 'true') {
                    $this->crud->use_table('trainer_clients');
                    $this->crud->update(array('id' => $this->input->post('request_id')), array('client_id' => $data['user_id'], 'status' => 'confirmed'));
                    $this->api_model->wd_result(array('status' => 1, 'message' => "Request confirmed successfully"));
                } else {
                    $this->crud->use_table('trainer_clients');
                    $this->crud->update(array('id' => $this->input->post('request_id')), array('client_id' => $data['user_id'], 'status' => 'denied'));
                    $this->api_model->wd_result(array('status' => 1, 'message' => "Request denied successfully"));
                }
            }

            if ($this->form_validation->run() == false || $other_error == true) { //the user is not logging in so display the login page
                //set the flash data error message if there is one
                if ($other_error) {
                    $this->api_model->wd_result(array('status' => 0, 'message' => $error_message));
                } else {
                    $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags((validation_errors()) ? validation_errors() : $this->session->flashdata('message'))));
                }
            }
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Memeber does not exist with given ID'));
        }
    }

    function edit_progression_plan()
    {

        $data = $_POST;
        $mandatory_fields = array('user_id', 'progression_plan_id', 'workoutdays');
        $this->api_model->validate($mandatory_fields, $data);

        $other_error = false;
        //validate form input
        $this->form_validation->set_rules('progression_plan_id', 'Progression Plan', 'required');

        if ($this->form_validation->run() == true) { //check to see if the user is logging in
            //check for "remember me"
            $this->crud->use_table('progression_plans');
            $progression_plan = $this->crud->retrieve(array('id' => $this->input->post('progression_plan_id')), 1)->row();

            if ($progression_plan->days_week != count(explode(',', $this->input->post('workoutdays')))) {
                $error_message = 'You must select ' . $progression_plan->days_week . ' days a week for the ' . $progression_plan->title . ' Plan';
                $other_error = true;
            } else {

                $user = $this->ion_auth->get_user($data['user_id']);
                $user_values = array(
                    'progression_plan_id' => $this->input->post('progression_plan_id'),
                    'workoutdays' => $this->input->post('workoutdays')
                );
                if ($this->ion_auth->update_user($user->id, $user_values)) { //if the login is successful
                    //redirect them back to the home page
                    $user = $this->api_model->user_detail_by_user_id($data['user_id']);
                    $this->workouts->progression_change_workouts($user->id);
                    $this->api_model->wd_result(array('status' => 1, 'message' => strip_tags($this->ion_auth->messages()), 'data' => $user));
                } else { //if the login was un-successful
                    //redirect them back to the login page
                    $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags($this->ion_auth->errors())));
                }
            }
        }

        if ($this->form_validation->run() == false || $other_error == true) { //the user is not logging in so display the login page
            //set the flash data error message if there is one
            if ($other_error) {
                $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags($error_message)));
            } else {
                $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags((validation_errors()) ? validation_errors() : $this->session->flashdata('message'))));
            }
        }
    }

    //        function calendar() {
//            $prefs = array(
//                'show_next_prev' => TRUE,
//                'next_prev_url' => '/member/calendar/'
//            );
//
//            $prefs['template'] = '
//
//		   {table_open}<table id="calendar_outer">{/table_open}
//
//		   {heading_row_start}<tr id="month_year">{/heading_row_start}
//
//		   {heading_previous_cell}<th colspan="2"><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
//		   {heading_title_cell}<th colspan="3">{heading}</th>{/heading_title_cell}
//		   {heading_next_cell}<th colspan="2"><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}
//
//		   {heading_row_end}</tr><tbody>
//            <tr>
//               <td colspan="7">
//                  <div class="wrap">
//                  <table id="calendar_inner" cellspacing="2">
//                     <thead>{/heading_row_end}
//
//		   {week_row_start}<tr id="days_of_week">{/week_row_start}
//		   {week_day_cell}<th class="day_of_week">{week_day}</th>{/week_day_cell}
//		   {week_row_end}</tr></thead>
//                     <tbody>{/week_row_end}
//
//		   {cal_row_start}<tr>{/cal_row_start}
//		   {cal_cell_start}<td class="day_cell" valign="top">{/cal_cell_start}
//
//		   {cal_cell_content}<div class="date">{day}</div>{content}{/cal_cell_content}
//		   {cal_cell_content_today}<div class="date">{day}</div>{content}{/cal_cell_content_today}
//
//		   {cal_cell_no_content}<div class="date">{day}</div>{/cal_cell_no_content}
//		   {cal_cell_no_content_today}<div class="date">{day}</div>{/cal_cell_no_content_today}
//
//		   {cal_cell_blank}&nbsp;{/cal_cell_blank}
//
//		   {cal_cell_end}</td>{/cal_cell_end}
//		   {cal_row_end}</tr>{/cal_row_end}
//
//		   {table_close}</tbody></table>{/table_close}
//		';
//            if ($this->uri->segment(4) == '') {
//                $this->data['month'] = date('m');
//            } else {
//                $this->data['month'] = $this->uri->segment(4);
//            }
//            if ($this->uri->segment(3) == '') {
//                $this->data['year'] = date('Y');
//            } else {
//                $this->data['year'] = $this->uri->segment(3);
//            }
//
//            $workouts = $this->workouts->get_monthly_workouts($this->data['month'], $this->data['year'], $this->session->userdata('user_id'));
//            $this->data['workouts'] = array();
//            foreach ($workouts->result() as $workout) {
//                if ($workout->title == '') {
//                    $title = 'Workout';
//                } else {
//                    $title = $workout->title;
//                }
//
//                if ($workout->workout_created == 'true') {
//                    $url = date('Y/m/d', strtotime($workout->workout_date));
//                    $this->data['workouts'][date('j', strtotime($workout->workout_date))] = '<a href="/member/log_book/' . $url . '">' . $title . '</a>';
//                    if ($workout->trainer_workout_id != '') {
//                        $this->data['workouts'][date('j', strtotime($workout->workout_date))] .= ' created by ' . $workout->first_name . ' ' . $workout->last_name;
//                    } elseif ($workout->pro_title != '') {
//                        $this->data['workouts'][date('j', strtotime($workout->workout_date))] .= ' ' . $workout->pro_title . ' Progression Day';
//                    }
//                } else {
//                    $this->data['workouts'][date('j', strtotime($workout->workout_date))] = $title . ' not generated yet, complete previous workouts first';
//                }
//            }
//
//            $this->load->library('calendar', $prefs);
//            $this->load->view('header');
//            $this->load->view('member/calendar', $this->data);
//            $this->load->view('footer');
//        }

    function calendar()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->basic_user_detail_by_user_id($data['user_id']);

        $response['user'] = $user;
        $response['workouts'] = array();
        $response['progression_plan'] = array();

        $workouts = $this->workouts_api->overall_workouts($data['user_id']);

        $unique_workout = array();
        foreach ($workouts->result_array() as $workout) {
            if ($workout['title'] == '') {
                $title = 'Workout';
            } else {
                $title = $workout['title'];
            }
            if (!empty($workout['trainer_workout_id'])) {
                if (!array_key_exists($workout['workout_date'], $unique_workout)) {
                    $unique_workout[$workout['workout_date']] = $workout;
                    $response['workouts'][] = $workout;
                }
            } else {
                $response['progression_plan'][] = $workout;
            }
        }
        $this->api_model->wd_result(array('status' => 1, 'data' => $response));
    }

    function calendar_per_month()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->basic_user_detail_by_user_id($data['user_id']);

        $response['user'] = $user;

        if (isset($data['month']) && !empty($data['month'])) {
            $this->data['month'] = $data['month'];
        } else {
            $this->data['month'] = date('m');
        }
        if (isset($data['year']) && !empty($data['year'])) {
            $this->data['year'] = $data['year'];
        } else {
            $this->data['year'] = date('Y');
        }

        $workouts = $this->workouts_api->get_monthly_workouts($this->data['month'], $this->data['year'], $data['user_id']);
        $unique_workout = array();

        foreach ($workouts->result_array() as $workout) {
            if ($workout['title'] == '') {
                $title = 'Workout';
            } else {
                $title = $workout['title'];
            }
            if (!empty($workout['trainer_workout_id'])) {
                if (!array_key_exists($workout['workout_date'], $unique_workout)) {
                    $unique_workout[$workout['workout_date']] = $workout;
                    $response['workouts'][] = $workout;
                }
            } else {
                $response['progression_plan'][] = $workout;
            }
        }
        $this->api_model->wd_result(array('status' => 1, 'data' => $response));
    }

    function client_calendar()
    {
        if ($this->uri->segment(3) != '') {
            $this->data['user'] = $this->ion_auth->get_user();
            $this->crud->use_table('trainer_clients');
            if ($trainer_client = $this->crud->retrieve(array('trainer_id' => $this->data['user']->id, 'client_id' => $this->uri->segment(3), 'status' => 'confirmed'))->row()) {
                $this->data['current_client'] = $this->ion_auth->get_user($trainer_client->client_id);
            } else {
                $this->data['trainer'] = false;
                redirect('member', 'refresh');
            }
        }
        $this->data['clients'] = $this->ion_auth->get_clients(false, NULL, NULL, $this->session->userdata('user_id'), 'confirmed');

        $prefs = array(
            'show_next_prev' => TRUE,
            'next_prev_url' => '/member/client_calendar/' . $this->uri->segment(3) . '/'
        );
        if ($this->uri->segment(5) == '') {
            $this->data['month'] = date('m');
        } else {
            $this->data['month'] = $this->uri->segment(5);
        }
        if ($this->uri->segment(4) == '') {
            $this->data['year'] = date('Y');
        } else {
            $this->data['year'] = $this->uri->segment(4);
        }

        $prefs['template'] = '

		   {table_open}<table id="calendar_outer">{/table_open}

		   {heading_row_start}<tr id="month_year">{/heading_row_start}

		   {heading_previous_cell}<th colspan="2"><a href="{previous_url}">&lt;&lt;</a></th>{/heading_previous_cell}
		   {heading_title_cell}<th colspan="3">{heading}</th>{/heading_title_cell}
		   {heading_next_cell}<th colspan="2"><a href="{next_url}">&gt;&gt;</a></th>{/heading_next_cell}

		   {heading_row_end}</tr><tbody>
            <tr>
               <td colspan="7">
                  <div class="wrap">
                  <table id="calendar_inner" cellspacing="2">
                     <thead>{/heading_row_end}

		   {week_row_start}<tr id="days_of_week">{/week_row_start}
		   {week_day_cell}<th class="day_of_week">{week_day}</th>{/week_day_cell}
		   {week_row_end}</tr></thead>
                     <tbody>{/week_row_end}

		   {cal_row_start}<tr>{/cal_row_start}
		   {cal_cell_start}<td class="day_cell" valign="top">{/cal_cell_start}

		   {cal_cell_content}<div class="date">{day}</div>{content}{/cal_cell_content}
		   {cal_cell_content_today}<div class="date">{day}</div>{content}{/cal_cell_content_today}

		   {cal_cell_no_content}<div class="date">{day}</div><a href="/member/workout_generator/new/' . $this->data['year'] . '-' . $this->data['month'] . '-{day}/' . $this->data['current_client']->id . '">Create Workout</a>{/cal_cell_no_content}
		   {cal_cell_no_content_today}<div class="date">{day}</div>{/cal_cell_no_content_today}

		   {cal_cell_blank}&nbsp;{/cal_cell_blank}

		   {cal_cell_end}</td>{/cal_cell_end}
		   {cal_row_end}</tr>{/cal_row_end}

		   {table_close}</tbody></table>{/table_close}
		';


        $workouts = $this->workouts->get_monthly_workouts($this->data['month'], $this->data['year'], $this->data['current_client']->id);
        $this->data['workouts'] = array();
        foreach ($workouts->result() as $workout) {
            if ($workout->title == '') {
                $title = 'Workout';
            } else {
                $title = $workout->title;
            }

            if ($workout->workout_created == 'true') {
                $url = date('Y/m/d', strtotime($workout->workout_date));
                $this->data['workouts'][date('j', strtotime($workout->workout_date))] = '<a href="/member/client_log_book/' . $this->data['current_client']->id . '/' . $url . '">' . $title . '</a>';
                if ($workout->trainer_workout_id != '') {
                    $this->data['workouts'][date('j', strtotime($workout->workout_date))] .= ' created by ' . $workout->first_name . ' ' . $workout->last_name;
                }
            } else {
                $this->data['workouts'][date('j', strtotime($workout->workout_date))] = $title . ' not generated yet, client created workout';
            }
        }

        $this->load->library('calendar', $prefs);
        $this->load->view('header', array('assets' => 'calendar'));
        $this->load->view('member/client_calendar', $this->data);
        $this->load->view('footer');
    }

    function log_book()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id');
        $this->api_model->validate($mandatory_fields, $data);

        $this->data['user'] = $this->ion_auth->get_user($data['user_id']);
        if (isset($data['date']) && !empty($data['date'])) {
            $this->data['date'] = $data['date'];
        } else {
            $this->data['date'] = date('Y-m-d');
        }
        if (isset($data['workout_id']) && !empty($data['workout_id'])) {
            $workout = $this->workouts_api->get_logbook_workout($this->data['user']->id, '', $data['workout_id']);
        } else {
            $workout = $this->workouts_api->get_logbook_workout($this->data['user']->id, date('Y-m-d', strtotime($this->data['date'])));
        }
        //            $response['title'] = date('l F jS', strtotime($this->data['year'] . '-' . $this->data['month'] . '-' . $this->data['date']));
//            $this->data['past_workouts'] = $this->workouts_api->get_past_workouts($this->data['user']->id);
        if ($workout) {

            if (isset($data['device_type']) && !empty($data['device_type'])) {
                $deviceUpdate['device_type'] = $data['device_type'];
            }
            if (isset($data['device_token']) && !empty($data['device_token'])) {
                $deviceUpdate['device_token'] = $data['device_token'];
            }
            if (isset($deviceUpdate) && !empty($deviceUpdate)) {
                $this->api_model->updateDeviceField($data['user_id'], $deviceUpdate);
            }


            $uwe = array();
            if ($workout['created'] == 'true') {
                foreach ($workout['sections'] as $section) {
                    if (isset($section['exercises'])) {
                        foreach ($section['exercises'] as $exercise) {
                            $uwe[$exercise['uwe_id']]['uwe_id'] = $exercise['uwe_id'];
                            $ex_sets = explode('|', $exercise['sets']);
                            $ex_weight = explode('|', $exercise['weight']);
                            foreach ($ex_sets as $index => $set) {
                                $save_stats = $this->db->query("SELECT * FROM user_workout_stats WHERE uwe_id = '" . $exercise['uwe_id'] . "' AND user_workout_stats.set = '" . $set . "'");
                                if ($previous_stats = $save_stats->row_array()) {
                                    $weight = $previous_stats['weight'];
                                } else {
                                    if (isset($ex_weight[$index])) {
                                        $weight = $ex_weight[$index];
                                    }
                                }
                                $uwe[$exercise['uwe_id']]['sets'][] = $set;
                                if ($exercise['set_type'] == 'sets_reps') {
                                    $uwe[$exercise['uwe_id']]['reps'][] = $exercise['reps'];
                                } elseif ($exercise['set_type'] == 'sets_time') {
                                    $uwe[$exercise['uwe_id']]['time'][] = $exercise['time'];
                                }
                                if ($exercise['weight_option'] == 'weighted') {
                                    $uwe[$exercise['uwe_id']]['weight'][] = $weight;
                                }
                            }
                            $uwe[$exercise['uwe_id']]['difficulty'] = 2;
                        }
                    }
                }
                $workout['uwe'] = base64_encode(serialize($uwe));
            }
            $this->api_model->wd_result(array('status' => 1, 'data' => $workout));
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'No workout found'));
        }
    }

    function workout_playlist()
    {
        $this->data['user'] = $this->ion_auth->get_user();
        $this->data['workout'] = $this->workouts->get_logbook_workout($this->data['user']->id, '', $this->uri->segment(3));

        $this->load->view('member/workout_playlist', $this->data);
    }

    function print_log_book()
    {
        $this->data['user'] = $this->ion_auth->get_user();
        if ($this->uri->segment(4) == '') {
            $this->data['month'] = date('m');
        } else {
            $this->data['month'] = $this->uri->segment(4);
        }
        if ($this->uri->segment(3) == '') {
            $this->data['year'] = date('Y');
        } else {
            $this->data['year'] = $this->uri->segment(3);
        }
        if ($this->uri->segment(5) == '') {
            $this->data['day'] = date('d');
        } else {
            $this->data['day'] = $this->uri->segment(5);
        }

        $this->data['title'] = date('l F jS', strtotime($this->data['year'] . '-' . $this->data['month'] . '-' . $this->data['day']));
        $this->data['workout'] = $this->workouts->get_logbook_workout($this->data['user']->id, date('Y-m-d', strtotime($this->data['year'] . '-' . $this->data['month'] . '-' . $this->data['day'])));
        $this->data['past_workouts'] = $this->workouts->get_past_workouts($this->data['user']->id);

        $this->load->view('print_header', array('assets' => 'print_logbook'));
        $this->load->view('member/print_log_book', $this->data);
        $this->load->view('print_footer');
    }

    function client_log_book()
    {
        if ($this->uri->segment(3) != '') {
            $this->data['user'] = $this->ion_auth->get_user();
            $this->crud->use_table('trainer_clients');
            if ($trainer_client = $this->crud->retrieve(array('trainer_id' => $this->data['user']->id, 'client_id' => $this->uri->segment(3), 'status' => 'confirmed'))->row()) {
                $this->data['current_client'] = $this->ion_auth->get_user($trainer_client->client_id);
            } else {
                $this->data['trainer'] = false;
                redirect('member', 'refresh');
            }
        }
        $this->data['clients'] = $this->ion_auth->get_clients(false, NULL, NULL, $this->session->userdata('user_id'), 'confirmed');

        $this->data['user'] = $this->ion_auth->get_user();
        if ($this->uri->segment(5) == '') {
            $this->data['month'] = date('m');
        } else {
            $this->data['month'] = $this->uri->segment(5);
        }
        if ($this->uri->segment(4) == '') {
            $this->data['year'] = date('Y');
        } else {
            $this->data['year'] = $this->uri->segment(4);
        }
        if ($this->uri->segment(6) == '') {
            $this->data['day'] = date('d');
        } else {
            $this->data['day'] = $this->uri->segment(6);
        }

        $this->data['title'] = date('l F jS', strtotime($this->data['year'] . '-' . $this->data['month'] . '-' . $this->data['day']));
        $this->data['workout'] = $this->workouts->get_logbook_workout($this->data['current_client']->id, date('Y-m-d', strtotime($this->data['year'] . '-' . $this->data['month'] . '-' . $this->data['day'])));
        $this->data['past_workouts'] = $this->workouts->get_past_workouts($this->data['current_client']->id);

        $this->load->view('header', array('assets' => 'logbook'));
        $this->load->view('member/client_log_book', $this->data);
        $this->load->view('footer');
    }

    //log the user out
    function logout()
    {
        $this->data['title'] = "Logout";

        //log the user out
        $logout = $this->ion_auth->logout();

        //redirect them back to the page they came from
        redirect('member', 'refresh');
    }

    //change password
    function change_password()
    {

        $data = $_POST;
        $mandatory_fields = array('user_id', 'old_password', 'new_password');
        $this->api_model->validate($mandatory_fields, $data);

        $this->form_validation->set_rules('old_password', 'Old password', 'required');
        $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']');
        //        $this->form_validation->set_rules('new_confirm', 'Confirm New Password', 'required');

        $user = $this->ion_auth->get_user($data['user_id']);

        if (!$user) {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'User does not exist with given ID'));
        }

        if ($this->form_validation->run() == false) { //display the form
            //set the flash data error message if there is one
            $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags((validation_errors()) ? validation_errors() : $this->session->flashdata('message'))));
        } else {

            $identity = $user->username;

            $change = $this->ion_auth->change_password($identity, $this->input->post('old_password'), $this->input->post('new_password'));

            if ($change) { //if the password was successfully changed
                $this->api_model->wd_result(array('status' => 1, 'message' => strip_tags($this->ion_auth->messages())));
                //                $this->logout();
            } else {
                $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags($this->ion_auth->errors())));
            }
        }
    }

    //change password
    function edit_account()
    {
        //        $this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
//        $this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
        $data = $_POST;
        $mandatory_fields = array('user_id');
        $this->api_model->validate($mandatory_fields, $data);

        $this->form_validation->set_rules('user_id', 'User Id', 'required');

        if ($this->form_validation->run() == false) { //display the form
            //set the flash data error message if there is one
            $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags((validation_errors()) ? validation_errors() : $this->session->flashdata('message'))));
        } else {
            $update_data = array();
            if (isset($data['first_name'])) {
                $update_data['first_name'] = $data['first_name'];
            }
            if (isset($data['last_name'])) {
                $update_data['last_name'] = $data['last_name'];
            }
            if (isset($data['city'])) {
                $update_data['city'] = $data['city'];
            }
            if (isset($data['state'])) {
                $update_data['state'] = $data['state'];
            }
            if (isset($data['zip'])) {
                $update_data['zip'] = $data['zip'];
            }

            if (isset($_FILES['photo']['name']) && !empty($_FILES['photo']['name'])) {
                $s = strtoupper(md5(uniqid(rand(), true)));
                $guid = substr($s, 0, 8) . '-' .
                    substr($s, 8, 4) . '-' .
                    substr($s, 12, 4) . '-' .
                    substr($s, 16, 4) . '-' .
                    substr($s, 20);
                $config['file_name'] = $guid . '.jpg';
                $config['overwrite'] = true;

                $config['upload_path'] = './images/member_photos/';
                $config['allowed_types'] = '*';



                $this->load->library('upload', $config);

                if ($this->upload->do_upload('photo')) {
                    $upload_data = $this->upload->data();

                    //                    $this->load->library('image_lib');
//                    $orig_image = $_SERVER['DOCUMENT_ROOT'] . '/hybrid_fitness/images/member_photos/' . $upload_data['file_name'];
//                    $sm_image = $_SERVER['DOCUMENT_ROOT'] . '/hybrid_fitness/images/member_photos/sm_' . $upload_data['file_name'];
//
//                    $config['image_library'] = 'gd2';
//                    $config['source_image'] = $orig_image;
//                    $config['maintain_ratio'] = TRUE;
//                    $config['width'] = 160;
//                    $config['height'] = 180;
//
//                    $this->image_lib->clear();
//                    $this->image_lib->initialize($config);
//                    $this->image_lib->resize();
//
//
//                    $config['image_library'] = 'gd2';
//                    $config['source_image'] = $orig_image;
//                    $config['new_image'] = $sm_image;
//                    $config['thumb_marker'] = '';
//                    $config['create_thumb'] = TRUE;
//                    $config['maintain_ratio'] = TRUE;
//                    $config['width'] = 80;
//                    $config['height'] = 80;
//
//                    $this->image_lib->clear();
//                    $this->image_lib->initialize($config);

                    //                    if ($this->image_lib->resize()) {
                    $update_data['photo'] = $upload_data['file_name'];
                    //                    } else {
//                        $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags($this->upload->display_errors())));
//                    }
                } else {
                    $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags($this->upload->display_errors())));
                }
            }

            $change = $this->ion_auth->update_user($data['user_id'], $update_data);

            if ($change) { //if the password was successfully changed
                $user = $this->api_model->user_detail_by_user_id($data['user_id']);
                //                $user = $this->ion_auth->get_user($data['user_id']);
                $this->api_model->wd_result(array('status' => 1, 'message' => 'User Edited Successfully', 'data' => $user));
            } else {
                $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags($this->ion_auth->errors())));
            }
        }
    }

    //forgot password
    function forgot_password()
    {
        $data = $_POST;
        $mandatory_fields = array('email');
        $this->api_model->validate($mandatory_fields, $data);

        $this->form_validation->set_rules('email', 'Email Address', 'required');
        if ($this->form_validation->run() == false) {

            //set any errors and display the form
            $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags((validation_errors()) ? validation_errors() : $this->session->flashdata('message'))));
        } else {

            //           if ($this->ci->ion_auth_model->forgotten_password($this->input->post('email'))) {   //changed
            // Get user information
            $user = $this->ion_auth->get_user_by_email($this->input->post('email')); //changed to get_user_by_identity from email

            if ($user) {
                $generatedOTP = $this->api_model->generate_otp($user->id);
                $basicUserDetail = $this->api_model->basic_user_detail_by_user_id($user->id);

                //                $message = $this->ci->load->view('member/email/forgot_password_otp.tpl.php', $data, true);
//                $this->ci->email->clear();
//                $config['mailtype'] = $this->ci->config->item('email_type', 'ion_auth');
//                $this->ci->email->initialize($config);
//                $this->ci->email->set_newline("\r\n");
//                $this->ci->email->from($this->ci->config->item('admin_email', 'ion_auth'), $this->ci->config->item('site_title', 'ion_auth'));
//                $this->ci->email->to($user->email);
//                $this->ci->email->subject($this->ci->config->item('site_title', 'ion_auth') . ' - Forgotten Password Verification');
//                $this->ci->email->message($message);

                $email = new \SendGrid\Mail\Mail();
                $email->setFrom("no-reply@hybridfitness.com", "Trnhrd");
                $email->setSubject("Password Reset Confirmation");
                $email->addTo($this->input->post('email'));

                $generatedOTP = $this->api_model->generate_otp($user->id);

                $data = array(
                    'identity' => $user->{$this->ci->config->item('identity', 'ion_auth')},
                    'forgotten_password_code' => $user->forgotten_password_code,
                    'generatedOTP' => $generatedOTP,
                    'name' => $basicUserDetail->first_name . ' ' . $basicUserDetail->last_name,
                );

                $body = $this->load->view('member/email/forgot_password_template.php', $data, true);
                $body = preg_replace('/\\\\/', '', $body); //Strip backslashes

                $email->addContent("text/html", $body);
                $sendgrid = new \SendGrid('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');
                try {
                    $response = $sendgrid->send($email);
                    if ($response->statusCode() >= 200 && $response->statusCode() <= 202) {
                        $this->api_model->wd_result(array('status' => 1, 'message' => 'OTP sent to your E-Mail or check spam too'));
                    } else {
                        $this->api_model->wd_result(array('status' => 0, 'message' => "Mail was unable to send", 'data' => array('status_code' => $response->statusCode())));
                    }
                } catch (Exception $e) {
                    $this->api_model->wd_result(array('status' => 0, 'message' => 'Caught exception: ' . $e->getMessage()));
                }
            } else {
                $this->api_model->wd_result(array('status' => 0, 'message' => 'Mail Not Exist'));
            }
        }
    }

    function match_otp()
    {
        $data = $_POST;
        $mandatory_fields = array('otp');
        $this->api_model->validate($mandatory_fields, $data);

        $otp_user = $this->api_model->validate_otp($data['otp']);
        if ($otp_user) {
            $this->api_model->wd_result(array('status' => 1, 'message' => 'OTP Is Valid', 'data' => $otp_user->id));
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Invalid OTP'));
        }
    }

    function reset_password()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'new_password');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->db->get_where('users', ['id' => $data['user_id']])->row();

        $password = $this->ion_auth_model->hash_password($data['new_password'], $user->salt);

        $update = $this->api_model->update_password($data['user_id'], $password);
        if ($update) {
            $this->api_model->wd_result(array('status' => 1, 'message' => 'Password Changed Successfully'));
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Unable To Change Password'));
        }
    }

    //reset password - final step for forgotten password
//    public function reset_password($code) {
//        $reset = $this->ion_auth->forgotten_password_complete($code);
//
//        if ($reset) {  //if the reset worked then send them to the login page
//            $this->session->set_flashdata('message', $this->ion_auth->messages());
//            redirect("member/login", 'refresh');
//        } else { //if the reset didnt work then send them back to the forgot password page
//            $this->session->set_flashdata('message', $this->ion_auth->errors());
//            redirect("member/forgot_password", 'refresh');
//        }
//    }
    //activate the user
    function activate($id, $code = false)
    {
        if ($code !== false)
            $activation = $this->ion_auth->activate($id, $code);
        else if ($this->ion_auth->is_admin())
            $activation = $this->ion_auth->activate($id);


        if ($activation) {
            //redirect them to the auth page
            $this->session->set_flashdata('message', $this->ion_auth->messages());
            redirect("auth", 'refresh');
        } else {
            //redirect them to the forgot password page
            $this->session->set_flashdata('message', $this->ion_auth->errors());
            redirect("member/forgot_password", 'refresh');
        }
    }

    //deactivate the user
    function deactivate($id = NULL)
    {
        // no funny business, force to integer
        $id = (int) $id;

        $this->load->library('form_validation');
        $this->form_validation->set_rules('confirm', 'confirmation', 'required');
        $this->form_validation->set_rules('id', 'user ID', 'required|is_natural');

        if ($this->form_validation->run() == FALSE) {
            // insert csrf check
            $this->data['csrf'] = $this->_get_csrf_nonce();
            $this->data['user'] = $this->ion_auth->get_user_array($id);
            $this->load->view('member/deactivate_user', $this->data);
        } else {
            // do we really want to deactivate?
            if ($this->input->post('confirm') == 'yes') {
                // do we have a valid request?
                if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id')) {
                    show_404();
                }

                // do we have the right userlevel?
                if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
                    $this->ion_auth->deactivate($id);
                }
            }

            //redirect them back to the auth page
            redirect('auth', 'refresh');
        }
    }

    //create a new user
    function register()
    {
        $data = $_POST;

        //validate form input
        $this->form_validation->set_rules('member_type', 'Member Type', 'required|xss_clean');
        $this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
        $this->form_validation->set_rules('terms_accept', 'Terms of Use', 'required|xss_clean');
        $this->form_validation->set_rules('email', 'Email Address', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']');
        //        $this->form_validation->set_rules('password_confirm', 'Password Confirmation', 'required');

        if ($this->form_validation->run() == true) {
            $group = $this->input->post('member_type');
            if (!in_array($group, array('members', 'trainers'))) {
                $group = 'members';
            }

            $username = $this->input->post('email');
            $email = $this->input->post('email');
            $password = $this->input->post('password');

            $additional_data = array(
                'first_name' => $this->input->post('first_name'),
                'last_name' => $this->input->post('last_name'),
                //                'city' => $this->input->post('city'),
//                'state' => $this->input->post('state'),
//                'zip' => $this->input->post('zip')
            );
        }


        if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data, $group)) { //check to see if we are creating the user
            $loggedin_user = $this->api_model->user_table_detail_through_email($this->input->post('email'));
            if (isset($data['device_type']) && !empty($data['device_type'])) {
                $deviceUpdate['device_type'] = $data['device_type'];
            }
            if (isset($data['device_token']) && !empty($data['device_token'])) {
                $deviceUpdate['device_token'] = $data['device_token'];
            }
            if (isset($deviceUpdate) && !empty($deviceUpdate)) {
                $this->api_model->updateDeviceField($loggedin_user['id'], $deviceUpdate);
            }
            $user = $this->api_model->user_detail_by_user_id($loggedin_user['id']);
            //            $user = $this->ion_auth->get_user($loggedin_user['id']);
            $this->api_model->wd_result(array('status' => 1, 'message' => "Your account has been created", 'data' => $user));
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags((validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message'))))));
        }
    }

    function _get_csrf_nonce()
    {
        $this->load->helper('string');
        $key = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);

        return array($key => $value);
    }

    function _valid_csrf_nonce()
    {
        if (
            $this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
            $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue')
        ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function first_run_get_array()
    {
        $this->crud->use_table('experience_level');
        $query = $this->crud->retrieve();
        $this->data['exp_level_id'] = $query->result();
        //        foreach ($query->result() as $level) {
//            $this->data['exp_level_id'][$level->id] = $level->title;
//        }

        $this->crud->use_table('progression_plans');
        $query = $this->crud->retrieve();
        $this->data['progression_plan_id'] = $query->result();
        //        foreach ($query->result() as $plan) {
//            $this->data['progression_plan_id'][$plan->id] = $plan->title . ' (' . $plan->days_week . ' a week)';
//        }

        $this->crud->use_table('equipment');
        $query = $this->crud->retrieve();
        $this->data['available_equipment'] = $query->result();
        //        foreach ($query->result() as $equipment) {
//            $this->data['available_equipment'][$equipment->id] = $equipment->title;
//        }

        $workoutdays = array(1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday');
        $count = 0;
        foreach ($workoutdays as $key => $value) {
            $workoutdaysObject[$count] = new stdClass();
            $workoutdaysObject[$count]->id = $key;
            $workoutdaysObject[$count]->value = $value;
            $count++;
        }
        $this->data['workoutdays'] = $workoutdaysObject;

        $this->api_model->wd_result($this->data);
    }

    function state_array()
    {
        $state = $this->api_model->state;
        $count = 0;
        foreach ($state as $key => $value) {
            $stateObject[$count] = new stdClass();
            $stateObject[$count]->id = $key;
            $stateObject[$count]->title = $value;
            $count++;
        }
        $this->api_model->wd_result(array('state' => $stateObject));
    }

    function view_profile()
    {
        $data = $_GET;
        $mandatory_fields = array('user_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);

        if ($user) {
            $this->api_model->wd_result(array('status' => 1, 'data' => $user));
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'No Such User Exist'));
        }
    }

    function trainers()
    {

        $data = $_POST;
        $mandatory_fields = array('user_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);

        if ($user && $user->group_id == 2) {

            //set the flash data error message if there is one
//            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
            //list the users
//            $this->data['user'] = $this->ion_auth->get_user($data['user_id']);
            $data['trainers'] = $this->api_model->get_trainers($user->email);

            if ($data) {
                $this->api_model->wd_result(array('status' => 1, 'data' => $data));
            } else {
                $this->api_model->wd_result(array('status' => 0, 'message' => 'There Are No Trainers'));
            }

            //            $this->crud->use_table('trainer_client_groups');
//            $this->data['trainer_groups'] = $this->crud->retrieve(array('trainer_id' => $data['user_id']))->result_array();
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Member does not exist with given ID'));
        }
    }

    function view_trainer_client_group()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'group_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);

        if ($user) {
            $group = $this->api_model->view_group($data['group_id']);
            if ($group) {
                $this->api_model->wd_result(array('status' => 1, 'data' => $group));
            } else {
                $this->api_model->wd_result(array('status' => 0, 'message' => 'No Such Group Exist'));
            }
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'User does not exist with given ID'));
        }
    }

    function remove_group()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'group_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);

        if ($user) {
            $group = $this->api_model->view_group($data['group_id']);
            if ($group) {
                $this->api_model->delete_group($data['group_id']);
                $data['clients'] = $this->api_model->get_clients($data['user_id']);
                $data['trainer_groups'] = $this->api_model->get_groups($data['user_id']);
                $this->api_model->wd_result(array('status' => 1, 'message' => 'Group Successfully Deleted', 'data' => $data));
            } else {
                $this->api_model->wd_result(array('status' => 0, 'message' => 'No Such Group Exist'));
            }
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'User does not exist with given ID'));
        }
    }

    function remove_client()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'client_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);

        if ($user && $user->group_id == 3) {
            $this->api_model->remove_client($data['user_id'], $data['client_id']);
            $data['clients'] = $this->api_model->get_clients($data['user_id']);
            $data['trainer_groups'] = $this->api_model->get_groups($data['user_id']);
            $this->api_model->wd_result(array('status' => 1, 'message' => 'Client removed successfully', 'data' => $data));
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Trainer does not exist with given ID'));
        }
    }

    function remove_trainer()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'trainer_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);

        if ($user && $user->group_id == 2) {
            $this->api_model->remove_trainer($data['user_id'], $data['trainer_id']);
            $data['clients'] = $this->api_model->get_clients($data['user_id']);
            $data['trainer_groups'] = $this->api_model->get_groups($data['user_id']);
            $this->api_model->wd_result(array('status' => 1, 'message' => 'Trainer removed successfully', 'data' => $data));
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Member does not exist with given ID'));
        }
    }

    function workout_generator_array()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);

        if ($user && $user->group_id == 3) {
            $data['status'] = 1;
            $data['message'] = 'List of array for workout';

            $this->crud->use_table('progressions');
            $query = $this->crud->retrieve();
            $data['progression_id'] = $query->result();

            $this->crud->use_table('skeleton_workouts');
            $query = $this->crud->retrieve();
            $data['skeleton_workout_id'] = $query->result();

            $data['client']['clients'] = $this->api_model->get_clients($data['user_id']);
            $data['client']['groups'] = $this->api_model->get_groups_for_workout($data['user_id']);

            $this->crud->use_table('equipment');
            $query = $this->crud->retrieve();
            $data['available_equipment'] = $query->result();

            $workoutdays = array(1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday');
            $count = 0;
            foreach ($workoutdays as $key => $value) {
                $workoutdaysObject[$count] = new stdClass();
                $workoutdaysObject[$count]->id = $key;
                $workoutdaysObject[$count]->value = $value;
                $count++;
            }
            $data['workoutdays'] = $workoutdaysObject;

            $this->api_model->wd_result($data);
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Trainer does not exist with given ID'));
        }
    }

    function skeleton_json()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);

        if ($user && $user->group_id == 3) {
            if ($this->input->post('skeleton_workout_id') != '') {
                $skeleton_workout_id = $this->input->post('skeleton_workout_id');
            }

            if ($this->input->post('client') != '') {
                $user_id = $this->input->post('client');
            } else {
                $user_id = $data['user_id'];
            }

            if ($this->input->post('progression_id') != '') {
                // $progression_id = $this->input->post('progression_id');

                $progression = $this->db->select(array('progressions.*'))
                    ->join('progressions', 'progressions.id = progression_plan_days.progression_id')
                    ->where('day', $user->progression_plan_day)
                    ->where('plan_id', $user->progression_plan_id)
                    ->limit(1)
                    ->get('progression_plan_days')->row();
                $hybrid_workout = $this->db->select(array('skeleton_workouts.*'))->join('skeleton_focus', 'skeleton_focus.skeleton_id = skeleton_workouts.id')->where('skeleton_focus.progression_id', $progression->id)->order_by('skeleton_workouts.id', "random")->limit(1)->get('skeleton_workouts')->row();

                $progression_id = $progression->id;
                $skeleton_workout_id = $hybrid_workout->id;
            } else {
                $progression_id = '';
            }

            if ($this->input->post('available_equipment') != '' && $this->input->post('available_equipment') != 'none') {
                $available_equipment = explode(',', $this->input->post('available_equipment'));
            } else if ($this->input->post('available_equipment') == 'none') {
                $available_equipment = $this->input->post('available_equipment');
            } else {
                $available_equipment = '';
            }
            $workout_array = $this->workouts_api->get_skeleton_generator(array('id' => $skeleton_workout_id, 'progression_id' => $progression_id, 'user_id' => $user_id, 'available_equipment' => $available_equipment));
            $this->api_model->wd_result(array('status' => 1, 'data' => $workout_array));
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Trainer does not exist with given ID'));
        }
    }

    function skeleton_section_types_array()
    {
        $section_array = $this->workouts_api->skeleton_section_types();
        if ($section_array) {
            $this->api_model->wd_result(array('status' => 1, 'data' => $section_array));
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'No section found'));
        }
    }

    function exercise_types_array()
    {
        $section_array = $this->workouts_api->exercise_types();
        if ($section_array) {
            $this->api_model->wd_result(array('status' => 1, 'data' => $section_array));
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'No section found'));
        }
    }

    function exercises()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'exercise_type_id', 'section_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);

        if ($user && $user->group_id == 3) {

            if ($this->input->post('client') != '') {
                $user_id = $this->input->post('client');
            } else {
                $user_id = $data['user_id'];
            }

            //            $section_record = $this->workouts_api->skeleton_section_types_by_Id($data['section_id']);

            $exercise_type_list = $this->workouts_api->exercisesByExerciseTypeId($data['exercise_type_id'], $data['user_id']);
            if ($exercise_type_list) {
                //                foreach ($exercise_type_list as $exercise_type_list_key => $exercise_type_list_value) {
//                    $exercise_type_list[$exercise_type_list_key]->exercise_stats = $this->workouts_api->get_exercise_counts(array('user_id' => $user_id, 'progression_id' => $data['progression_id'], 'exercise_id' => $exercise_type_list_value->id, 'weight_type' => $exercise_type_list_value->weight_type, 'section_type' => $section_record->type));
//                }
                $this->api_model->wd_result(array('status' => 1, 'data' => $exercise_type_list));
            } else {
                $this->api_model->wd_result(array('status' => 0, 'message' => 'No exercise found'));
            }
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Trainer does not exist with given ID'));
        }
    }

    public function featured_exercise()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);
        if ($user && $user->group_id == 2) {
            $response['featured_exercise'] = $this->workouts_api->get_random_exercise(array('user_id' => $user->id, 'available_equipment' => $user->available_equipment));
            $response['upcoming_workouts'] = $this->workouts_api->get_upcoming_created_workouts($user->id);
            $this->api_model->wd_result(array('status' => 1, 'data' => $response));
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Member does not exist with given ID'));
        }
    }

    public function add_additional_video()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'exercise_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);
        if ($user && $user->group_id == 3) {
            if (isset($_FILES['video']['name']) && !empty($_FILES['video']['name'])) {
                $check_video_existance = $this->api_model->check_exercise_video_existance($data['user_id'], $data['exercise_id']);
                $config['file_name'] = time() . '.mp4';

                $config['overwrite'] = true;

                $config['upload_path'] = './video/trainer_exercise/';
                $config['allowed_types'] = '*';
                $this->load->library('upload', $config);

                if ($this->upload->do_upload('video')) {
                    $this->api_model->insert_exercise_video(array('trainer_id' => $data['user_id'], 'exercise_id' => $data['exercise_id'], 'video' => '/video/trainer_exercise/' . $this->upload->data()['file_name']));
                    $this->api_model->wd_result(array('status' => 1, 'message' => 'Video added successfully', 'data' => array('trainer_video' => '/video/trainer_exercise/' . $this->upload->data()['file_name'])));
                } else {
                    $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags($this->upload->display_errors())));
                }
            }
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Trainer does not exist with given ID'));
        }
    }

    public function delete_additional_video()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'exercise_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);
        if ($user && $user->group_id == 3) {
            $check_video_existance = $this->api_model->check_exercise_video_existance($data['user_id'], $data['exercise_id']);
            if ($check_video_existance) {
                unlink('./video/trainer_exercise/' . $check_video_existance->video);
                $this->api_model->delete_exercise_video($data['user_id'], $data['exercise_id']);
                $this->api_model->wd_result(array('status' => 1, 'message' => 'Video removed successfully'));
            } else {
                $this->api_model->wd_result(array('status' => 0, 'message' => 'No Video Exist for that Trainer'));
            }
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Trainer does not exist with given ID'));
        }
    }

    public function add_custom_exercise()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'exercise_type', 'exercise_name');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);
        if ($user && $user->group_id == 3) {
            if (isset($_FILES['video']['name']) && !empty($_FILES['video']['name'])) {
                if (!is_dir('./video/trainer_exercise/')) {
                    mkdir('./video/trainer_exercise/', 0777, TRUE);
                }
                $config['file_name'] = time() . '.mp4';
                $config['overwrite'] = true;
                $config['upload_path'] = './video/trainer_exercise/';
                $config['allowed_types'] = '*';
                $this->load->library('upload', $config);

                if ($this->upload->do_upload('video')) {
                    $this->db->insert('exercises', ['title' => $data['exercise_name'], 'experience_id' => 1, 'description' => '', 'video' => '/video/trainer_exercise/' . $this->upload->data()['file_name'], 'mobile_video' => '/video/trainer_exercise/' . $this->upload->data()['file_name'], 'inserted_as' => 'custom']);
                    $exercise_id = $this->db->insert_id();
                    $this->db->insert('exercise_types', ['title' => $data['exercise_type'], 'inserted_as' => 'custom']);
                    $exercise_type_id = $this->db->insert_id();
                    $this->db->insert('exercise_link_types', ['exercise_id' => $exercise_id, 'type_id' => $exercise_type_id]);
                    $this->api_model->wd_result(array('status' => 1, 'message' => 'Exercise added successfully', 'data' => array('exercise_type_id' => $exercise_type_id, 'exercise_type' => $data['exercise_type'])));
                } else {
                    $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags($this->upload->display_errors())));
                }
            } elseif (isset($data['trainer_exercise']) && !empty($data['trainer_exercise'])) {
                $this->db->insert('exercises', ['title' => $data['exercise_name'], 'experience_id' => 1, 'description' => '', 'video' => $data['trainer_exercise'], 'mobile_video' => $data['trainer_exercise'], 'inserted_as' => 'custom']);
                $exercise_id = $this->db->insert_id();
                $this->db->insert('exercise_types', ['title' => $data['exercise_type'], 'inserted_as' => 'custom']);
                $exercise_type_id = $this->db->insert_id();
                $this->db->insert('exercise_link_types', ['exercise_id' => $exercise_id, 'type_id' => $exercise_type_id]);
                $this->api_model->wd_result(array('status' => 1, 'message' => 'Exercise added successfully', 'data' => array('exercise_type_id' => $exercise_type_id, 'exercise_type' => $data['exercise_type'])));
            } else {
                $this->api_model->wd_result(array('status' => 0, 'message' => 'Must upload a video'));
            }
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Trainer does not exist with given ID'));
        }
    }

    public function prebuild_videos_list()
    {
        $prebuild_videos = $this->api_model->prebuild_videos_list();
        $this->api_model->wd_result(array('status' => 1, 'data' => $prebuild_videos));
    }

    public function add_additional_exercise_video()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'title', 'exercise_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);
        if ($user && $user->group_id == 3) {
            if (isset($data['mobile_video']) && !empty($data['mobile_video'])) {
                $checkExerciseTable = $this->db->get_where('exercises', ['mobile_video' => $data['mobile_video'], 'id' => $data['exercise_id']])->row_array();
                $this->db->update('additional_exercise_videos', ['priority' => 0], ['exercise_id' => $data['exercise_id'], 'trainer_id' => $data['user_id']]);
                if (empty($checkExerciseTable)) {
                    $checkAdditionalExerciseTable = $this->db->get_where('additional_exercise_videos', ['trainer_id' => $data['user_id'], 'mobile_video' => $data['mobile_video'], 'exercise_id' => $data['exercise_id']])->row_array();
                    if (!empty($checkAdditionalExerciseTable)) {
                        $this->db->update('additional_exercise_videos', ['priority' => 1], ['id' => $checkAdditionalExerciseTable['id']]);
                    } else {
                        $this->db->insert('additional_exercise_videos', ['title' => $data['title'], 'mobile_video' => $data['mobile_video'], 'trainer_id' => $data['user_id'], 'priority' => '1', 'exercise_id' => $data['exercise_id']]);
                    }
                }
                $this->api_model->wd_result(array('status' => 1, 'message' => 'Exercise updated successfully', 'data' => array('id' => $data['exercise_id'], 'title' => $data['title'], 'mobile_video' => $data['mobile_video'], 'trainer_id' => $data['user_id'])));
            } else {
                if (isset($_FILES['video']['name']) && !empty($_FILES['video']['name'])) {
                    if (!is_dir('./video/trainer_exercise/')) {
                        mkdir('./video/trainer_exercise/', 0777, TRUE);
                    }
                    $config['file_name'] = time() . '.mp4';
                    $config['overwrite'] = true;
                    $config['upload_path'] = './video/trainer_exercise/';
                    $config['allowed_types'] = '*';
                    $this->load->library('upload', $config);

                    if ($this->upload->do_upload('video')) {
                        $this->db->update('additional_exercise_videos', ['priority' => 0], ['exercise_id' => $data['exercise_id'], 'trainer_id' => $data['user_id']]);
                        $this->db->insert('additional_exercise_videos', ['title' => $data['title'], 'mobile_video' => '/video/trainer_exercise/' . $this->upload->data()['file_name'], 'trainer_id' => $data['user_id'], 'priority' => '1', 'exercise_id' => $data['exercise_id']]);
                        $exercise_id = $this->db->insert_id();
                        $this->api_model->wd_result(array('status' => 1, 'message' => 'Video added successfully', 'data' => array('id' => $exercise_id, 'title' => $data['title'], 'mobile_video' => '/video/trainer_exercise/' . $this->upload->data()['file_name'], 'trainer_id' => $data['user_id'])));
                    } else {
                        $this->api_model->wd_result(array('status' => 0, 'message' => strip_tags($this->upload->display_errors())));
                    }
                } else {
                    $this->api_model->wd_result(array('status' => 0, 'message' => 'Must upload a video'));
                }
            }
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Trainer does not exist with given ID'));
        }
    }

    public function list_of_videos()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id');
        $this->api_model->validate($mandatory_fields, $data);

        $user = $this->api_model->user_detail_by_user_id($data['user_id']);
        if ($user && $user->group_id == 3) {
            $exercise_videos = $this->db->select('id as exercise_id, title, mobile_video')->get('exercises')->result_array();
            $additional_exercise_videos = $this->db->select('exercise_id, title, mobile_video')->group_by('mobile_video')->get('additional_exercise_videos')->result_array();
            $all_exercise = array_merge($exercise_videos, $additional_exercise_videos);
            $this->api_model->wd_result(array('status' => 1, 'data' => $all_exercise));
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Trainer does not exist with given ID'));
        }
    }

    public function make_priority_to_video()
    {
        $data = $_POST;
        $mandatory_fields = array('user_id', 'mobile_video', 'exercise_id', 'title');
        $this->api_model->validate($mandatory_fields, $data);
        $user = $this->api_model->user_detail_by_user_id($data['user_id']);
        if ($user && $user->group_id == 3) {
            $checkExerciseTable = $this->db->get_where('exercises', ['mobile_video' => $data['mobile_video'], 'id' => $data['exercise_id']])->row_array();
            $this->db->update('additional_exercise_videos', ['priority' => 0], ['exercise_id' => $data['exercise_id'], 'trainer_id' => $data['user_id']]);
            if (empty($checkExerciseTable)) {
                $checkAdditionalExerciseTable = $this->db->get_where('additional_exercise_videos', ['trainer_id' => $data['user_id'], 'mobile_video' => $data['mobile_video'], 'exercise_id' => $data['exercise_id']])->row_array();
                if (!empty($checkAdditionalExerciseTable)) {
                    $this->db->update('additional_exercise_videos', ['priority' => 1], ['id' => $checkAdditionalExerciseTable['id']]);
                } else {
                    $this->db->insert('additional_exercise_videos', ['title' => $data['title'], 'mobile_video' => $data['mobile_video'], 'trainer_id' => $data['user_id'], 'priority' => '1', 'exercise_id' => data['exercise_id']]);
                }
            }
            $this->api_model->wd_result(array('status' => 1, 'message' => 'Video added for this exercise successfully'));
        } else {
            $this->api_model->wd_result(array('status' => 0, 'message' => 'Trainer does not exist with given ID'));
        }
    }


    function send_notifications($device_token, $message)
    {
        $this->load->library('apn');
        $this->apn->connectToPush();

        $send_result = $this->apn->sendMessage($device_token, $message, 1,  'default'  );
            
        if($send_result)
            log_message('debug','Отправлено успешно');
        else
            log_message('error',$this->apn->error);

        
        $this->apn->disconnectPush();
    }

    public function generateJWT()
    {
        $this->load->library('Jwt_creator');
        $payload = array(
            "iss" => "KMTJ5J4KWU",
            "aud" => "audience",
            "iat" => time(), // Issued at time
            "exp" => time() + 3600, // Expiration time (1 hour from now)
        );

        $jwt = $this->jwt_creator->createToken($payload);
        return $jwt;
    }

    function sendAPNSPushNotification($deviceToken, $jwtToken) {
        $url = 'https://api.sandbox.push.apple.com/3/device/' . $deviceToken;
    
        $headers = array(
            'apns-topic: com.hybrid.fitness',
            'apns-push-type: alert',
            'authorization: bearer ' . $jwtToken
        );
    
        $data = array(
            'aps' => array(
                'alert' => 'test'
            )
        );
    
        $dataString = json_encode($data);
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
    
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        
        curl_close($ch);
    
        return array(
            'response' => $response,
            'info' => $info
        );
    }

    public function testAPN()
    {
        $device_token = '8f7f60f609aba189b8bc216875e9537f95b999267ee760c04cb710039dd8fdab';
        $jwtToken = $this->generateJWT();
        $result = $this->sendAPNSPushNotification($device_token, $jwtToken);
        
        echo "Response: " . $result['response'] . "\n";
        echo "HTTP Status Code: " . $result['info']['http_code'] . "\n";
    }
}