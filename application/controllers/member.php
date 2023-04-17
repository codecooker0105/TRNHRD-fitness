<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!class_exists('Controller')) {
	class Controller extends CI_Controller
	{
	}
}

class Member extends Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->database();
		$this->load->helper('url');


		if (!$this->ion_auth->logged_in() && !in_array($this->uri->segment(2), array('login', 'register', 'forgot_password', 'reset_password'))) {
			//redirect them to the login page
			redirect('member/login', 'refresh');
		}
	}

	//redirect if needed, otherwise display the user list
	function index()
	{
		if (!$this->ion_auth->logged_in()) {
			//redirect them to the login page
			redirect('member/login', 'refresh');
		} else {
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');


			$this->data['user'] = $this->ion_auth->get_user();
			$this->crud->use_table('trainer_clients');
			if ($this->data['user']->group_id == '2') {
				$trainer = $this->crud->retrieve(array('email' => $this->data['user']->email))->row();
				if (!$trainer) {
					redirect('member/select_trainer');
				}
			}
			if ($this->data['user']->group_id == '3' && $trainer_request = $this->crud->retrieve(array('email' => $this->data['user']->email, 'status' => 'requested'))->row()) {
				redirect('member/confirm_trainer_request/' . $trainer_request->id, 'refresh');
			}
			$trainer_client = $this->crud->retrieve(array('client_id' => $this->data['user']->id, 'status' => 'confirmed'))->row();

			// if ($this->data['user']->tos_agreement == 'false') {
			// 	redirect('member/accept_tos', 'refresh');
			// }
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
				// $this->data['get_past_workouts_testimonial'] = $this->workouts->get_past_workouts_testimonial($this->data['user']->id);
				$this->data['get_past_workouts_testimonial'] = $this->workouts->get_past_workouts_testimonial(121);
				if($this->data['clients']) {
					// $this->data['workouts'] = $this->workouts->get_past_5_exercise($this->data['user']->id, $this->data['clients'][0]->id);
					$this->data['workouts'] = $this->workouts->get_past_5_exercise(121, 122);
				}
				$this->data['exercise_group'] = $this->workouts->get_exercise_group(1081);
				// $this->data['exercise_group'] = $this->workouts->get_exercise_group($this->data['user']->id);
				// var_dump($this->data);die;
			} else {
				$this->data['member_group'] = 'member';
				if ($trainer_client) {
					$this->data['trainer'] = $this->ion_auth->get_user($trainer_client->trainer_id);
				} else {
					$this->data['trainer'] = false;
				}
				$this->data['past_5_workouts'] = $this->workouts->get_past_5_workouts($this->data['user']->id);
			}
			$this->header_data['meta_keywords'] = 'TRNHRD';
			$this->header_data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';

			$this->crud->use_table('user_weather');
			$zipcodes = $this->crud->retrieve(array('user_id' => $this->data['user']->id), '', '', array('default' => 'desc'))->result_array();
			$this->load->library('weather');
			$this->data['weathers'] = array();
			// foreach ($zipcodes as $locations) {
			//$this->data['weathers'][$locations['zip']] = $this->weather->get_weather($locations['zip']);
			// }

			// $this->data['stats'] = $this->workouts->get_dashboard_stats($this->data['user']->id);
			// var_dump($this->data);die;
			$this->header_data['assets'] = 'dashboard';
			// 			$this->load->view('base/header', $this->header_data);
			// 			$this->load->view('base/integration_start');
			// 			$this->load->view('member/dashboard', $this->data);
			// 			$this->load->view('base/integration_end');
			// 			$this->load->view('base/footer');
			$this->load->view('base/header', $this->header_data);
			$this->load->view('base/dashboard', $this->data);
			$this->load->view('base/footer', $this->data);
		}
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
		if (!$this->ion_auth->logged_in()) {
			//redirect them to the login page
			redirect('member/login', 'refresh');
		} elseif (!$this->ion_auth->is_group('trainers')) {
			redirect('member', 'refresh');
		} else {
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			//list the users
			$this->data['user'] = $this->ion_auth->get_user();
			$this->data['clients'] = $this->ion_auth->get_clients(false, NULL, NULL, $this->session->userdata('user_id'), NULL);

			$this->crud->use_table('trainer_client_groups');
			$this->data['trainer_groups'] = $this->crud->retrieve(array('trainer_id' => $this->session->userdata('user_id')))->result_array();
			$this->header_data['meta_keywords'] = 'TRNHRD';
			$this->header_data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';

			$this->header_data['assets'] = 'clients';
			$this->load->view('base/header', $this->header_data);
			$this->load->view('base/integration_start');
			$this->load->view('member/clients', $this->data);
			$this->load->view('base/integration_end');
			$this->load->view('base/footer');
		}
	}

	//create  new exercise
	function create_trainer_group()
	{
		$this->form_validation->set_rules('title', 'Title', 'required');
		$this->form_validation->set_rules('exp_level_id', 'Experience Level', 'required');

		if ($this->form_validation->run() == false) { //display the form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$title = $this->form_validation->set_value('title');
			$this->data['title'] = array(
				'name' => 'title',
				'id' => 'title',
				'type' => 'text',
				'value' => $title
			);

			$this->data['experience_value'] = $this->form_validation->set_value('exp_level_id');
			$this->data['experience_options'] = array('' => 'Choose Experience Level');
			$this->crud->use_table('experience_level');
			$query = $this->crud->retrieve();
			foreach ($query->result() as $level) {
				$this->data['experience_options'][$level->id] = $level->title;
			}

			$user_equipment = $this->input->post('available_equipment');

			$this->crud->use_table('equipment');
			$query = $this->crud->retrieve();
			foreach ($query->result() as $equipment) {
				$this->data['equipment'][$equipment->id] = $equipment->title;
				if (is_array($user_equipment) && in_array($equipment->id, $user_equipment)) {
					$this->data['available_equipment'][$equipment->id] = array(
						'name' => 'available_equipment[]',
						'checked' => TRUE,
						'value' => $equipment->id
					);
				} else {
					$this->data['available_equipment'][$equipment->id] = array(
						'name' => 'available_equipment[]',
						'checked' => FALSE,
						'value' => $equipment->id
					);
				}
			}

			$this->data['user'] = $this->ion_auth->get_user();
			$this->data['clients'] = $this->ion_auth->get_clients(false, NULL, NULL, $this->session->userdata('user_id'), 'confirmed');
			$this->data['meta_keywords'] = 'TRNHRD';
			$this->data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';

			//render
			$this->load->view('base/header', $this->data);
			$this->load->view('base/integration_start');
			$this->load->view('member/create_trainer_group', $this->data);
			$this->load->view('base/integration_end');
			$this->load->view('base/footer', $this->data);
		} else {
			$insert_values = array(
				'title' => $this->input->post('title'),
				'trainer_id' => $this->session->userdata('user_id'),
				'exp_level_id' => $this->input->post('exp_level_id'),
				'available_equipment' => implode(',', $this->input->post('available_equipment'))
			);


			$this->crud->use_table('trainer_client_groups');
			$new_group = $this->crud->create($insert_values);
			$group_id = $this->db->insert_id();



			if ($new_group) { //if the password was successfully changed
				$this->crud->use_table('trainer_clients');

				if (is_array($this->input->post('clients'))) {
					foreach ($this->input->post('clients') as $client) {
						$this->crud->update(array('client_id' => $client, 'trainer_id' => $this->session->userdata('user_id')), array('trainer_group_id' => $group_id));
					}
				}

				$this->session->set_flashdata('message', 'New Group Saved');
				redirect('member/clients', 'refresh');
			} else {
				$this->session->set_flashdata('message', 'Group Failed to Save');
				redirect('member/create_trainer_group', 'refresh');
			}
		}
	}


	function edit_trainer_group()
	{
		$this->form_validation->set_rules('title', 'Title', 'required');
		$this->form_validation->set_rules('exp_level_id', 'Experience Level', 'required');

		if ($this->form_validation->run() == false) { //display the form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->crud->use_table('trainer_client_groups');
			$group = $this->crud->retrieve(array('id' => $this->uri->segment(3)))->row();

			if ($this->input->post('submit') != '') {
				$title = $this->form_validation->set_value('title');
			} else {
				$title = $group->title;
			}
			$this->data['title'] = array(
				'name' => 'title',
				'id' => 'title',
				'type' => 'text',
				'value' => $title
			);

			$user_equipment = explode(",", $group->available_equipment);
			$this->crud->use_table('equipment');
			$query = $this->crud->retrieve();
			foreach ($query->result() as $equipment) {
				$this->data['equipment'][$equipment->id] = $equipment->title;
				if (is_array($user_equipment) && in_array($equipment->id, $user_equipment)) {
					$this->data['available_equipment'][$equipment->id] = array(
						'name' => 'available_equipment[]',
						'checked' => TRUE,
						'value' => $equipment->id
					);
				} else {
					$this->data['available_equipment'][$equipment->id] = array(
						'name' => 'available_equipment[]',
						'checked' => FALSE,
						'value' => $equipment->id
					);
				}
			}

			if ($this->input->post('submit') != '') {
				$this->data['experience_id'] = $this->form_validation->set_value('experience_id');
			} else {
				$this->data['experience_id'] = $group->exp_level_id;
			}

			$this->data['experience_options'] = array();
			$this->crud->use_table('experience_level');
			$query = $this->crud->retrieve();
			foreach ($query->result() as $experience) {
				$this->data['experience_options'][$experience->id] = $experience->title;
			}

			$this->data['clients'] = array();
			if ($this->input->post('submit') != '') {
				$this->data['clients'] = $this->form_validation->set_value('clients');
			} else {
				$this->crud->use_table('trainer_clients');
				$clients = $this->crud->retrieve(array('trainer_group_id' => $group->id), '', '', '', '')->result();
				foreach ($clients as $client) {
					$this->data['group_clients'][] = $client->client_id;
				}
			}


			$this->data['group_id'] = array(
				'name' => 'group_id',
				'id' => 'group_id',
				'type' => 'hidden',
				'value' => $group->id,
			);

			$this->data['user'] = $this->ion_auth->get_user();
			$this->data['clients'] = $this->ion_auth->get_clients(false, NULL, NULL, $this->session->userdata('user_id'), 'confirmed');
			$this->data['meta_keywords'] = 'TRNHRD';
			$this->data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';

			//render
			$this->load->view('base/header', $this->data);
			$this->load->view('base/integration_start');
			$this->load->view('member/edit_trainer_group', $this->data);
			$this->load->view('base/integration_end');
			$this->load->view('base/footer', $this->data);
		} else {
			$update_values = array(
				'title' => $this->input->post('title'),
				'trainer_id' => $this->session->userdata('user_id'),
				'exp_level_id' => $this->input->post('exp_level_id'),
				'available_equipment' => implode(',', $this->input->post('available_equipment'))
			);

			$this->crud->use_table('trainer_client_groups');
			$update = $this->crud->update(array('id' => $this->input->post('group_id')), $update_values);

			$this->crud->use_table('trainer_clients');
			$this->crud->update(array('trainer_group_id' => $this->input->post('group_id')), array('trainer_group_id' => 'NULL'));
			if (is_array($this->input->post('clients'))) {
				foreach ($this->input->post('clients') as $client) {
					$this->crud->update(array('client_id' => $client, 'trainer_id' => $this->session->userdata('user_id')), array('trainer_group_id' => $this->input->post('group_id')));
				}
			}

			if ($update) { //if the password was successfully changed
				$this->session->set_flashdata('message', 'Group Saved');
				redirect('member/clients', 'refresh');
			} else {
				$this->session->set_flashdata('message', 'Group Failed to Save');
				redirect('member/edit_trainer_group/' . $this->input->post('group_id'), 'refresh');
			}
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
			$this->data['meta_keywords'] = 'TRNHRD';
			$this->data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';

			$this->header_data['assets'] = 'edit_stats';
			$this->load->view('base/header', $this->header_data);
			$this->load->view('base/integration_start');
			$this->load->view('member/edit_stats', $this->data);
			$this->load->view('base/integration_end');
			$this->load->view('base/footer');
		}
	}

	function add_stat()
	{
		if (!$this->ion_auth->logged_in()) {
			redirect('member/login', 'refresh');
		}
		$error_message = '';

		//validate form input
		$this->form_validation->set_rules('title', 'Title', 'required|xss_clean');

		if ($this->form_validation->run() == true) {

			$insert_data = array(
				'user_id' => $this->session->userdata('user_id'),
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

	function add_featured_exercise_to_workout()
	{
		if (!$this->ion_auth->logged_in()) {
			redirect('member/login', 'refresh');
		}
		$error_message = '';

		//validate form input
		$this->form_validation->set_rules('workout_id', 'Workout', 'required|xss_clean');
		$this->form_validation->set_rules('choice', 'Choice', 'required|xss_clean');

		if ($this->form_validation->run() == true) {
			$exercise_id = $this->input->post('exercise');
			$workout_id = $this->input->post('workout_id');
			$choice = $this->input->post('choice');
			if ($choice == "add_to_section") {
				$uws_id = $this->input->post('uws');
				$exercise = $this->db->join('exercise_link_types elt', 'elt.exercise_id = exercises.id', 'left')->where('id', $exercise_id)->limit(1)->get('exercises')->result_array();
				$current_section_exercise = $this->db->where('workout_section_id', $uws_id)->order_by('display_order', 'desc')->limit(1)->get('user_workout_exercises')->result_array();
				$new_exercise = $current_section_exercise[0];
				unset($new_exercise['id']);
				$new_exercise['exercise_id'] = $exercise_id;
				$new_exercise['exercise_type_id'] = $exercise[0]['type_id'];
				$new_exercise['set_type'] = $exercise[0]['type'];
				$new_exercise['weight_option'] = $exercise[0]['weight_type'];
				$new_exercise['display_order'] = $new_exercise['display_order'] + 1;
				$this->db->insert('user_workout_exercises', $new_exercise);
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
		} else {
		}
	}

	function get_similiar_workout_exercises()
	{
		if (!$this->ion_auth->logged_in()) {
			redirect('member/login', 'refresh');
		}
		$error_message = '';

		//validate form input
		$this->form_validation->set_rules('exercise', 'Exercise', 'required|xss_clean');
		$this->form_validation->set_rules('workout_id', 'Workout', 'required|xss_clean');

		if ($this->form_validation->run() == true) {

			$exercise_id = $this->input->post('exercise');
			$workout_id = $this->input->post('workout_id');
			$exercise_types_result = $this->db->select('exercise_link_types.type_id')->where('exercise_id', $exercise_id)->get('exercise_link_types')->result_array();
			if (count($exercise_types_result) > 0) {
				$exercise_types = '';
				$types = count($exercise_types_result);
				foreach ($exercise_types_result as $field => $value) {
					$types--;
					$exercise_types .= $value['type_id'];
					if ($types) {
						$exercise_types .= ',';
					}
				}

				$workout_exercises = $this->db->select('uwe.id,uwe.exercise_id,e.title as exercise_title,sst.title as section_title')
					->join('exercises e', 'uwe.exercise_id = e.id')
					->join('user_workout_sections uws', 'uwe.workout_section_id = uws.id')
					->join('skeleton_section_types sst', 'uws.section_type_id = sst.id')
					->where('uwe.workout_id', $workout_id)
					->where("uwe.exercise_type_id IN (" . $exercise_types . ")")
					->order_by("uws.display_order", "asc")
					->order_by("uwe.display_order", "asc")
					->get('user_workout_exercises uwe')->result_array();
				if (count($workout_exercises) > 0) {
					$this->data['exercises'] = $workout_exercises;
				} else {
					$this->data['exercises'] = 'none';
				}
			} else {
				$this->data['exercises'] = 'none';
			}

			$workout_sections = $this->db->select('uws.id,sst.title as section_title')
				->join('skeleton_section_types sst', 'uws.section_type_id = sst.id')
				->where('uws.workout_id', $workout_id)
				->order_by("display_order", "asc")
				->get('user_workout_sections uws')->result_array();
			$this->data['sections'] = $workout_sections;

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
	function add_current_stat()
	{
		if (!$this->ion_auth->logged_in()) {
			redirect('member/login', 'refresh');
		}
		$error_message = '';

		//validate form input
		$this->form_validation->set_rules('id', 'Stat', 'required|xss_clean');
		$this->form_validation->set_rules('current_value', 'Current Stat Value', 'required|xss_clean');
		$this->form_validation->set_rules('date_taken', 'Date stat was taken', 'required|xss_clean');

		if ($this->form_validation->run() == true) {

			$insert_data = array(
				'stat_id' => $this->input->post('id'),
				'date_taken' => date('Y/m/d', strtotime($this->input->post('date_taken'))),
				'stat_value' => $this->input->post('current_value')
			);
			$select_data = array(
				'user_id' => $this->session->userdata('user_id'),
				'id' => $this->input->post('id')
			);

			$this->crud->use_table('user_stats');
			if ($stat = $this->crud->retrieve($select_data, 1)->row()) {
				$insert_data = array(
					'stat_id' => $this->input->post('id'),
					'date_taken' => date('Y/m/d', strtotime($this->input->post('date_taken'))),
					'stat_value' => $this->input->post('current_value')
				);
				$this->crud->use_table('user_stats_values');
				$this->crud->create($insert_data);
			}

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

	function remove_stat()
	{
		if (!$this->ion_auth->logged_in()) {
			redirect('member/login', 'refresh');
		}
		if ($this->uri->segment(3) != '') {
			$this->crud->use_table('user_stats');
			if ($this->crud->delete(array('user_id' => $this->session->userdata('user_id'), 'id' => $this->uri->segment(3)))) {
				$this->crud->use_table('user_stats_values');
				$this->crud->delete(array('stat_id' => $this->uri->segment(3)));
			}
		}

		redirect('member/edit_stats', 'refresh');
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
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_group('trainers')) {
			redirect('member/login', 'refresh');
		}
		$error_message = '';

		//validate form input
		$this->form_validation->set_rules('name', 'Name', 'required|xss_clean');
		$this->form_validation->set_rules('email', 'Email', 'required|xss_clean');
		$this->form_validation->set_rules('email_message', 'Message', 'xss_clean');

		if ($this->form_validation->run() == true) {

			$insert_data = array(
				'name' => $this->input->post('name'),
				'trainer_id' => $this->session->userdata('user_id'),
				'email' => $this->input->post('email'),
				'email_message' => $this->input->post('email_message')
			);

			$this->crud->use_table('trainer_clients');
			if ($current_clients = $this->crud->retrieve(array('email' => $this->input->post('email'), 'trainer_id' => $this->session->userdata('user_id')), 1)->row()) {
				$error_message = "You have already requested to train this client";
			}
		}

		if ($error_message == '' && $this->form_validation->run() == true) { //check to see if we are creating the user
			//redirect them back to the admin page
			$this->crud->create($insert_data);
			$user = $this->ion_auth->get_user();
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

			if ($this->email->send()) {
				$this->session->set_flashdata('message', "Your message has been sent to the client");
				redirect("member/request_client", 'refresh');
			} else {
				$this->session->set_flashdata('message', "Your message failed to send. Please check the email you entered and try again. If you continue to get this message, please contact support.");
				redirect("member/request_client", 'refresh');
			}
		} else {
			//display the edit user form
			//set the flash data error message if there is one
			if ($error_message != '') {
				$this->data['message'] = $error_message;
			} else {
				$this->data['message'] = (validation_errors()) ? ($this->ion_auth->errors() ? $this->ion_auth->errors() : validation_errors()) : $this->session->flashdata('message');
			}

			$this->data['name'] = array(
				'name' => 'name',
				'id' => 'name',
				'type' => 'text',
				'value' => $this->form_validation->set_value('name'),
			);
			$this->data['email'] = array(
				'name' => 'email',
				'id' => 'email',
				'type' => 'text',
				'value' => $this->form_validation->set_value('email'),
			);
			$this->data['email_message'] = array(
				'name' => 'email_message',
				'id' => 'email_message',
				'type' => 'textarea',
				'value' => $this->form_validation->set_value('email_message'),
				'cols' => 50,
				'rows' => 10
			);

			$this->load->view('popup_header', array('assets' => 'request_client'));

			$this->load->view('member/request_client', $this->data);

			$this->load->view('popup_footer');
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
			$this->data['meta_keywords'] = 'TRNHRD';
			$this->data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';

			$this->load->view('base/header', $this->data);
			$this->load->view('base/integration_start');
			$this->load->view('member/accept_tos', $this->data);
			$this->load->view('base/integration_end');
			$this->load->view('base/footer');
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
			$this->header_data['meta_keywords'] = 'TRNHRD';
			$this->header_data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';

			$this->header_data['assets'] = 'workout_generator';
			$this->load->view('base/header', $this->header_data);
			$this->load->view('base/integration_start');
			$this->load->view('member/workout_generator', $this->data);
			$this->load->view('base/integration_end');
			$this->load->view('base/footer');
		}
	}

	function process_generator()
	{
		$date = $this->input->post('date');
		$dates = split(' - ', $date);
		if (count($dates) > 1) {
			$start_date = $dates[0];
			$end_date = $dates[1];
			$weekdays = $this->input->post('days');
		} else {
			$single_date = $date;
		}

		$user_type = 'single';

		if ($this->input->post('client') != '' && stristr($this->input->post('client'), 'group')) {
			$user_type = 'group';
			$id_array = explode('-', $this->input->post('client'));
			$users = $this->db->select(array('trainer_clients.client_id as id'))->where('trainer_group_id', $id_array[1])->get('trainer_clients')->result();
			$group = $this->db->where('id', $id_array[1])->where('trainer_id', $this->session->userdata('user_id'))->limit(1)->get('trainer_client_groups')->row();
		} elseif ($this->input->post('client') != '') {
			$user = $this->ion_auth->get_user($this->input->post('client'));
			$users[] = $user;
		} else {
			$user = $this->ion_auth->get_user();
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
				$trainer_workout_values = array('trainer_id' => $this->session->userdata('user_id'), 'trainer_group_id' => $id_array[1], 'start_date' => date("Y-m-d", strtotime($start_date)), 'end_date' => date("Y-m-d", $end_date), 'days' => implode(',', $weekdays));
			} else {
				$trainer_workout_values = array('trainer_id' => $this->session->userdata('user_id'), 'user_id' => $user->id, 'start_date' => date("Y-m-d", strtotime($start_date)), 'end_date' => date("Y-m-d", $end_date), 'days' => implode(',', $weekdays));
			}
			if ($this->input->post('trainer_workout_id') != '') {
				$trainer_workout_id = $this->input->post('trainer_workout_id');
				$this->db->where('id', $trainer_workout_id)->where('trainer_id', $this->session->userdata('user_id'))->update('trainer_workouts', $trainer_workout_values);
			} elseif ($this->input->post('trainer_group_workout_id') != '') {
				$trainer_workout_id = $this->input->post('trainer_group_workout_id');
				$this->db->where('id', $trainer_workout_id)->where('trainer_id', $this->session->userdata('user_id'))->update('trainer_workouts', $trainer_workout_values);
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
								$s_value = split("-", $section[0]);
								$section_values = array('workout_id' => $workout_id, 'section_type_id' => $s_value[0], 'display_order' => $index);
								if (isset($s_value[1]) && $s_value[1] != 'undefined') {
									$section_values['section_rest'] = $s_value[1];
								}
								$this->db->insert('user_workout_sections', $section_values);
								$workout_section_id = $this->db->insert_id();
								foreach ($section as $index2 => $cat_exercise) {
									if (is_array($cat_exercise)) {
										$value = split("-", $cat_exercise[$index2 - 1]);
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
				$trainer_workout_values = array('trainer_id' => $this->session->userdata('user_id'), 'trainer_group_id' => $id_array[1], 'start_date' => date("Y-m-d", strtotime($single_date)));
			} else {
				$trainer_workout_values = array('trainer_id' => $this->session->userdata('user_id'), 'user_id' => $user->id, 'start_date' => date("Y-m-d", strtotime($single_date)));
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
						$s_value = split("-", $section[0]);
						$section_values = array('workout_id' => $workout_id, 'section_type_id' => $s_value[0], 'display_order' => $index);
						if (isset($s_value[1])) {
							$section_values['section_rest'] = $s_value[1];
						}
						$this->db->insert('user_workout_sections', $section_values);
						$workout_section_id = $this->db->insert_id();
						foreach ($section as $index2 => $cat_exercise) {
							if (is_array($cat_exercise)) {
								$value = split("-", $cat_exercise[$index2 - 1]);
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

		?><p>The new workout(s) have been added to
						<?php if ($user_type == 'group') { ?>	
								<?= $group->title ?> 		<?php } else { ?>				<?= $user->first_name ?>
									<?= $user->last_name ?>'s<?php } ?> workout log. You may close this dialog and add this workout to another date or
						client or choose a destination below.</p>
					<ul>
						<li><a href="/member">Dashboard</a></li>
						<?php if ($user_type != 'group') { ?>
								<li><a href="/member/client_log_book/<?= $user->id ?>"><?= $user->first_name ?>																					 			<?= $user->last_name ?>'s Workout
										Log</a></li>
						<?php } ?>
						<li><a href="/member/workout_generator">Reset Workout Generator</a></li>
					</ul><?php
	}

	function save_logbook_stats()
	{
		$workout_date = $this->input->post('workout_date');
		$uw_id = $this->input->post('uw_id');
		$uw_row = $this->db->where('id', $uw_id)->where('user_id', $this->session->userdata('user_id'))->get('user_workouts')->row();
		if ($uw_row) {

			foreach ($this->input->post('uwe') as $uwe) {
				$this->db->where('user_id', $this->session->userdata('user_id'))->where('uw_id', $uw_id)->where('uwe_id', $uwe['uwe_id'])->delete('user_workout_stats');
				$uwe_row = $this->db->where('workout_id', $uw_id)->where('id', $uwe['uwe_id'])->get('user_workout_exercises')->row();
				if ($uwe_row) {
					foreach ($uwe['sets'] as $index => $set) {
						unset($insert_data);
						$insert_data = array(
							'uw_id' => $uw_id,
							'uwe_id' => $uwe['uwe_id'],
							'workout_date' => $workout_date,
							'user_id' => $this->session->userdata('user_id'),
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
				$current_progression = $this->db->where('progression_id', $uw_row->progression_id)->where('user_id', $this->session->userdata('user_id'))->get('user_progressions')->row();
				if ($current_progression) {
					$update_data = array('session_count' => ($current_progression->session_count + 1));
					$this->db->where('progression_id', $uw_row->progression_id)->where('user_id', $this->session->userdata('user_id'))->update('user_progressions', $update_data);
				} else {
					$insert_data = array('user_id' => $this->session->userdata('user_id'), 'progression_id' => $uw_row->progression_id, 'session_count' => 1);
					$this->db->insert('user_progressions', $insert_data);
				}
			}

			$this->db->where('id', $uw_id)->where('user_id', $this->session->userdata('user_id'))->update('user_workouts', array('completed' => 'true'));


			if ($uw_row->progression_plan_id != '') {
				//going to generate next workout here
				$this->workouts->create_next_workout($this->session->userdata('user_id'));
			}


			$this->session->set_flashdata('message', "Your workout stats have been saved and your next workout has been generated based on these stats");
			redirect('/member/log_book/' . date('Y/m/d', strtotime($uw_row->workout_date)), 'refresh');
		}

		$this->session->set_flashdata('message', "Your workout stats failed to save");
		redirect('member/log_book/' . date('Y/m/d', strtotime($workout_date)), 'refresh');
	}

	function save_log_stats()
	{
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

		$this->db->where('user_id', $this->session->userdata('user_id'))->where('uw_id', $uw_id)->where('uwe_id', $uwe_id)->delete('user_workout_stats');

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
				'user_id' => $this->session->userdata('user_id'),
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

	function get_video_url()
	{
		if ($this->input->post('exercise_id') != '') {
			$exercise_id = $this->input->post('exercise_id');
		} else {
			echo 'false';
		}
		$exercise = $this->db->where('id', $exercise_id)->get('exercises')->row();
		echo json_encode($exercise->mobile_video);
	}

	function get_section()
	{
		if ($this->input->post('id') != '') {
			$id = $this->input->post('id');
		} else {
			echo 'false';
		}
		$this->crud->use_table('skeleton_section_types');
		$section = $this->crud->retrieve(array('id' => $id))->row();
		if ($section) { ?>
						<li class="section">
							<div class="ui-widget ui-helper-clearfix ui-state-default ui-corner-all move">
								<span class="ui-icon ui-icon-arrow-4 move"></span></div>
							<input type="hidden" value="<?= $section->id ?>" name="section_id" class="section_id" />
							<?php if ($section->type == 'rest' || $section->type == 'active-rest') { ?>
									<span class="rest"><?= $section->title ?> - <?= secToMinute($section->rest) ?></span>
							<?php } else { ?>
									<a href="#" class="section_title off"><?= $section->title ?></a>
							<?php } ?>
							<div class="remove_section ui-widget ui-helper-clearfix ui-state-default ui-corner-all remove"><span
									class="ui-icon ui-icon-closethick remove"></span>Remove Section</div>
							<div class="add_exercise ui-widget ui-helper-clearfix ui-state-default ui-corner-all pointer"><span
									class="ui-icon ui-icon-plus pointer"></span>Add Exercise Type</div>
							<ul class="workout_categories"></ul>
						<?php
		}
	}

	function get_admin_exercise_type()
	{
		if ($this->input->post('id') != '') {
			$id = $this->input->post('id');
		} else {
			echo 'false';
		}
		$this->crud->use_table('exercise_types');
		$e_type = $this->crud->retrieve(array('id' => $id))->row();
		if ($e_type) { ?>
						<li class="category">
							<div class="ui-state-default ui-corner-all move"><span class="ui-icon ui-icon-arrow-4 move"></span></div>
							<input type="hidden" value="<?= $e_type->id ?>" name="category_id" class="category_id" /><a href="#"
								class="workout_category_title"><?= $e_type->title ?></a>
						<?php
		}
	}

	function get_exercise_type()
	{
		if ($this->input->post('exercise_id') != '') {
			$exercise_id = $this->input->post('exercise_id');
			$this->crud->use_table('exercises');
			$exercise = $this->crud->retrieve(array('id' => $exercise_id))->row();
			$category = $this->db->select(array('exercise_types.*'))->join('exercise_link_types', 'exercise_link_types.type_id = exercise_types.id')->where('exercise_link_types.exercise_id', $exercise_id)->limit(1)->get('exercise_types')->row();
			if (isset($category->id)) {
				$id = $category->id;
			} else {
				$id = 1;
			}
		} elseif ($this->input->post('id') != '') {
			$id = $this->input->post('id');
		} else {
			echo 'false';
		}
		$this->crud->use_table('exercise_types');
		$e_type = $this->crud->retrieve(array('id' => $id))->row();
		if ($e_type) { ?>
					<li class="category">
						<div class="ui-state-default ui-corner-all move"><span class="ui-icon ui-icon-arrow-4 move"></span></div><a href="#"
							class="workout_category_title"><?= $e_type->title ?></a>
						<?php
						?>
						<ul class="workout_exercises">
							<li class="exercise_type">
								<input type="hidden" value="<?= $e_type->id ?>" name="category_id" class="category_id" />
								<input type="hidden" name="exercise_id"
									value="<?php if (isset($exercise->id)) { ?><?= $exercise->id ?><?php } ?>" class="exercise_id" />
								<input type="hidden" name="ex_type" value="<?= $e_type->title ?>" class="ex_type" />

								<table width="100%" cellspacing="0" cellpadding="0">
									<thead>
										<tr>
											<th class="left"><a
													href="/member/popup_video/<?php if (isset($exercise->id)) { ?><?= $exercise->id ?><?php } ?>"
													class="play-exercise"><?php if (isset($exercise->id)) { ?><?= $exercise->title ?><?php } ?></a>
											</th>
											<th>Set</th>
											<th>Reps/Time</th>
											<th>Rest</th>
											<th class="right">Weight</th>
										</tr>
									</thead>
									<tbody>
										<?php $set = 1;
										while ($set <= 3) { ?>
												<tr <?php if ($set == 3) { ?> class="bottom" <?php } ?>>
													<?php if ($set == 1) { ?>
															<td class="ex_options left bottom" rowspan="3">
																<strong>Set Options:</strong><br />
																<select name="set_type[]" class="set_type">
																	<option value="sets_reps" selected="selected">Sets x Reps</option>
																	<option value="sets_time">Sets x Time</option>
																</select><br />
																<strong>Weight Options:</strong><br />
																<select name="weight_option[]" class="weight_option">
																	<option value="weighted" selected="selected">Weighted</option>
																	<option value="bodyweight"> Bodyweight only</option>
																</select>
															</td><?php } ?>
													<td><span class="set_number"><?= $set ?></span><input name="sets[]" type="hidden"
															value="<?= $set ?>" /></td>
													<td><select name="reps[]" class="reps">
															<?php for ($x = 1; $x <= 30; $x++) { ?>
																	<option value="<?= $x ?>" <?php if (10 == $x) { ?> selected="selected" <?php } ?>>
																		<?= $x ?></option>
															<?php } ?>
														</select>
														<select name="time[]" class="time">
															<?php for ($x = 15; $x <= 300; $x += 15) { ?>
																	<option value="<?= $x ?>" <?php if (30 == $x) { ?> selected="selected" <?php } ?>>
																		<?= secToMinute($x) ?></option>
															<?php } ?>
														</select>
													</td>
													<td class="right">
														<select name="rest[]" class="rest">
															<?php for ($x = 0; $x <= 300; $x += 15) { ?>
																	<option value="<?= $x ?>" <?php if (30 == $x) { ?> selected="selected" <?php } ?>>
																		<?= $x ?></option>
															<?php } ?>
														</select>
													</td>
													<td class="right">
														<span class="weight_input_box"><input type="text" name="weight[]" class="weight" value="" />
															lbs.</span>
														<span class="bodyweight">Body Weight Only</span>
													</td>
												</tr>
												<?php $set++;
										} ?>
									<tbody class="spacer">
										<tr>
											<td colspan="6">&nbsp;</td>
										</tr>
									</tbody>
									<tbody class="footer">
										<tr>
											<td colspan="6" class="left">
												<strong>OPTIONS</strong>
												<a href="#" class="add_set">Add Set</a> | <a href="#" class="remove_set">Remove Set</a> | <a
													href="#" class="select_exercise<?= $e_type->id ?>" id="exercise_10000">Select
													Exercise</a> | <a href="#" class="remove_exercise">Remove Exercise</a>
											</td>
										</tr>
									</tbody>
								</table>
							</li>
						</ul><?php
		}
	}

	function skeleton_json()
	{
		if ($this->input->post('id') != '') {
			$id = $this->input->post('id');
		} else {
			return false;
		}

		if ($this->input->post('user_id') != '') {
			$user_id = $this->input->post('user_id');
		} else {
			$user_id = $this->session->userdata('user_id');
		}

		if ($this->input->post('progression_id') != '') {
			$progression_id = $this->input->post('progression_id');
		} else {
			$progression_id = '';
		}

		if ($this->input->post('available_equipment') != '') {
			$available_equipment = $this->input->post('available_equipment');
		} else {
			$available_equipment = '';
		}
		return $this->workouts->get_skeleton_generator(array('id' => $id, 'progression_id' => $progression_id, 'user_id' => $user_id, 'available_equipment' => $available_equipment));
	}

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
		$guid =
			substr($s, 0, 8) . '-' .
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

				$this->header_data['meta_keywords'] = 'TRNHRD';
				$this->header_data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';
				$this->load->view('base/header', $this->header_data);
				$this->load->view('base/integration_start');
				$this->load->view('member/edit_photo', $this->data);
				$this->load->view('base/integration_end');
				$this->load->view('base/footer');
			}
		} else {
			$this->data['error'] = $this->upload->display_errors();
			$this->data['judge'] = $this->ion_auth->get_user($this->uri->segment(3));
			$this->header_data['meta_keywords'] = 'TRNHRD';
			$this->header_data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';
			$this->header_data['main_menu'] = 'admin';
			$this->load->view('base/header', $this->header_data);
			$this->load->view('base/integration_start');
			$this->load->view('member/edit_photo', $this->data);
			$this->load->view('base/integration_end');
			$this->load->view('base/footer');
		}
	}

	//log the user in
	function login()
	{
		$this->data['page_title'] = "Login";

		//validate form input
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == true) {
			//check to see if the user is logging in
			//check for "remember me"
			$remember = (bool) $this->input->post('remember');

			if ($this->ion_auth->login($this->input->post('username'), $this->input->post('password'), $remember)) {
				//if the login is successful
				//redirect them back to the home page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect('member', 'refresh');
			} else {
				//if the login was un-successful
				//redirect them back to the login page
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('member/login', 'refresh'); //use redirects instead of loading views for compatibility with MY_Controller libraries
			}
		} else {
			//the user is not logging in so display the login page
			//set the flash data error message if there is one
			$this->data['meta_keywords'] = 'TRNHRD';
			$this->data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';
			$this->data['url_segment'] = 'login';
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->data['username'] = array(
				'name' => 'username',
				'id' => 'username',
				'type' => 'text',
				'value' => $this->form_validation->set_value('username'),
			);
			$this->data['password'] = array(
				'name' => 'password',
				'id' => 'password',
				'type' => 'password',
			);
			$this->data['meta_keywords'] = 'TRNHRD';
			$this->data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';

			$this->data['footer_visibility'] = 'no';
			$this->load->view('base/header', $this->data);
			$this->load->view('base/login', $this->data);
			$this->load->view('base/footer', $this->data);
		}
	}

	function first_run()
	{
		if (!$this->ion_auth->logged_in()) {
			//redirect them to the login page
			redirect('member/login', 'refresh');
		}
		$other_error = false;
		//validate form input
		$this->form_validation->set_rules('exp_level_id', 'Experience Level', 'required');
		$this->data['user'] = $this->ion_auth->get_user();
		$this->crud->use_table('trainer_clients');
		$trainer_client = $this->crud->retrieve(array('client_id' => $this->data['user']->id, 'status' => 'confirmed'))->row();
		if (!$trainer_client) {
			$this->form_validation->set_rules('progression_plan_id', 'Progression Plan', 'required');
		}


		if ($this->form_validation->run() == true) { //check to see if the user is logging in
			//check for "remember me"
			if ($this->input->post('progression_plan_id') != '') {
				$this->crud->use_table('progression_plans');
				$progression_plan = $this->crud->retrieve(array('id' => $this->input->post('progression_plan_id')), 1)->row();

				if ($progression_plan->days_week != count($this->input->post('workoutdays'))) {
					$error_message = 'You must select ' . $progression_plan->days_week . ' days a week for the ' . $progression_plan->title . ' Plan';
					$other_error = true;
				} else {


					$user = $this->ion_auth->get_user();
					$user_values = array(
						'exp_level_id' => $this->input->post('exp_level_id'),
						'progression_plan_id' => $this->input->post('progression_plan_id'),
						'available_equipment' => implode(',', $this->input->post('available_equipment')),
						'workoutdays' => implode(',', $this->input->post('workoutdays'))
					);
					if ($this->ion_auth->update_user($user->id, $user_values)) { //if the login is successful
						//redirect them back to the home page
						$this->workouts->assign_available_exercises($user->id);
						$this->workouts->progression_change_workouts($user->id);
						$this->session->set_flashdata('message', $this->ion_auth->messages());
						redirect('member', 'refresh');
					} else { //if the login was un-successful
						//redirect them back to the login page
						$this->session->set_flashdata('message', $this->ion_auth->errors());
						redirect('member/first_run', 'refresh'); //use redirects instead of loading views for compatibility with MY_Controller libraries
					}
				}
			} else {
				$user = $this->ion_auth->get_user();
				$user_values = array(
					'exp_level_id' => $this->input->post('exp_level_id'),
					'available_equipment' => implode(',', $this->input->post('available_equipment'))
				);
				if ($this->ion_auth->update_user($user->id, $user_values)) { //if the login is successful
					//redirect them back to the home page
					$this->workouts->assign_available_exercises($user->id);
					$this->session->set_flashdata('message', $this->ion_auth->messages());
					redirect('member', 'refresh');
				} else { //if the login was un-successful
					//redirect them back to the login page
					$this->session->set_flashdata('message', $this->ion_auth->errors());
					redirect('member/first_run', 'refresh'); //use redirects instead of loading views for compatibility with MY_Controller libraries
				}
			}
		}

		if ($this->form_validation->run() == false || $other_error == true) { //the user is not logging in so display the login page
			//set the flash data error message if there is one
			if ($other_error) {
				$this->data['message'] = $error_message;
			} else {
				$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			}

			$this->data['experience_value'] = $this->form_validation->set_value('exp_level_id');
			$this->data['experience_options'] = array('' => 'Choose Experience Level');
			$this->crud->use_table('experience_level');
			$query = $this->crud->retrieve();
			foreach ($query->result() as $level) {
				$this->data['experience_options'][$level->id] = $level->title;
			}

			$this->data['progression_plan_value'] = $this->form_validation->set_value('progression_plan_id');
			$this->data['progression_plan_options'] = array('' => 'Choose Progression Plan');
			$this->crud->use_table('progression_plans');
			$query = $this->crud->retrieve();
			foreach ($query->result() as $plan) {
				$this->data['progression_plan_options'][$plan->id] = $plan->title . ' (' . $plan->days_week . ' a week)';
			}

			$user_equipment = $this->input->post('available_equipment');

			$this->crud->use_table('equipment');
			$query = $this->crud->retrieve();
			foreach ($query->result() as $equipment) {
				$this->data['equipment'][$equipment->id] = $equipment->title;
				if (is_array($user_equipment) && in_array($equipment->id, $user_equipment)) {
					$this->data['available_equipment'][$equipment->id] = array(
						'name' => 'available_equipment[]',
						'checked' => TRUE,
						'value' => $equipment->id
					);
				} else {
					$this->data['available_equipment'][$equipment->id] = array(
						'name' => 'available_equipment[]',
						'checked' => FALSE,
						'value' => $equipment->id
					);
				}
			}

			$user_workoutdays = $this->input->post('workoutdays');

			$weekdays = array(1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday');
			foreach ($weekdays as $day => $title) {
				$this->data['weekday_title'][$day] = $title;
				if (is_array($user_workoutdays) && in_array($day, $user_workoutdays)) {
					$this->data['weekdays'][$day] = array(
						'name' => 'workoutdays[]',
						'checked' => TRUE,
						'value' => $day
					);
				} else {
					$this->data['weekdays'][$day] = array(
						'name' => 'workoutdays[]',
						'checked' => FALSE,
						'value' => $day
					);
				}
			}

			$this->data['user'] = $this->ion_auth->get_user();
			$this->crud->use_table('trainer_clients');
			if ($trainer_client = $this->crud->retrieve(array('client_id' => $this->data['user']->id, 'status' => 'confirmed'))->row()) {
				$this->data['trainer'] = $this->ion_auth->get_user($trainer_client->trainer_id);
			} else {
				$this->data['trainer'] = false;
			}
			$this->data['meta_keywords'] = 'TRNHRD';
			$this->data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';

			$this->load->view('base/header', $this->data);
			$this->load->view('base/integration_start');
			$this->load->view('member/first_run', $this->data);
			$this->load->view('base/integration_end');
			$this->load->view('base/footer', $this->data);
		}
	}

	function select_trainer()
	{
		if (!$this->ion_auth->logged_in()) {
			//redirect them to the login page
			redirect('member/login', 'refresh');
		}
		$error_message = '';
		$this->form_validation->set_rules('selected_trainer_id', 'Selected Trainer', 'required');
		if ($this->form_validation->run() == true) {
			$selected_trainer_id = (int) $this->input->post('selected_trainer_id');
			$user = $this->ion_auth->get_user();
			$insert_data = array(
				'name' => $user->first_name . ' ' . $user->last_name,
				'client_id' => $this->session->userdata('user_id'),
				'trainer_id' => $selected_trainer_id,
				'email' => $user->email,
				'email_message' => "Client wants to train with you"
			);
			$this->crud->use_table('trainer_clients');
			$this->crud->create($insert_data);
			$trainer_data = $this->db->select(array('users.*', 'meta.first_name', 'meta.last_name'))
								->join('meta', 'users.id = meta.user_id', 'left')
								->where('users.id', $selected_trainer_id)
								->get('users')
								->result();
			$data['name'] = $user->first_name . ' ' . $user->last_name;
			$data['email'] = $user->email;
			$data['email_message'] = 'Client wants to train with you';
			$data['trainer_name'] = $trainer_data[0]->first_name . ' ' . $trainer_data[0]->last_name;
			$data['trainer_request_code'] = 'TEST';
			$message = $this->load->view('member/email/request_trainer.tpl.php', $data, true);
			$this->email->clear();
			$config['mailtype'] = $this->config->item('email_type', 'ion_auth');
			$this->email->initialize($config);
			$this->email->set_newline("\r\n");
			$this->email->from($this->config->item('admin_email', 'ion_auth'), 'Trnhrd on behalf of ' . $data['trainer_name']);
			$this->email->to($trainer_data[0]->email);
			$this->email->subject('Trnhrd - Client Request');
			$this->email->message($message);
			redirect("member/");

			// if ($this->email->send()) {
			// 	redirect("member/");
			// } else {
			// 	$this->session->set_flashdata('message', "Your message failed to send. Please check the email you entered and try again. If you continue to get this message, please contact support.");
			// 	redirect("member/select_trainer", 'refresh');
			// }
		} else {
			if ($error_message != '') {
				$this->data['message'] = $error_message;
			} else {
				$this->data['message'] = (validation_errors()) ? ($this->ion_auth->errors() ? $this->ion_auth->errors() : validation_errors()) : $this->session->flashdata('message');
			}
			$this->data['all_trainers'] = $this->db->select(array('users.*', 'meta.*'))
				->join('meta', 'users.id = meta.user_id', 'left')
				->where('group_id', '3')
				->where('active', '1')
				->where('tos_agreement', 'true')
				->order_by('created_on', 'desc')
				->limit(8)
				->get('users')
				->result();
			$this->header_data['meta_keywords'] = 'TRNHRD';
			$this->header_data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';
			$this->header_data['assets'] = 'select_trainer';

			// var_dump($this->data['all_trainers']);die;
			$this->load->view('base/header', $this->header_data);
			$this->load->view('member/select_trainer', $this->data);
			$this->load->view('base/footer', $this->data);
		}
		
	}

	function confirm_trainer_request()
	{
		if (!$this->ion_auth->logged_in()) {
			//redirect them to the login page
			redirect('member/login', 'refresh');
		} elseif ($this->uri->segment(3) == '') {
			redirect('member', 'refresh');
		}
		$other_error = false;
		//validate form input
		$this->form_validation->set_rules('decision', 'Decision', 'required');

		if ($this->form_validation->run() == true) { //check to see if the user is logging in
			//check for "remember me"
			if ($this->input->post('decision') == 'true') {
				$this->crud->use_table('trainer_clients');
				$this->crud->update(array('id' => $this->input->post('request_id')), array('client_id' => $this->session->userdata('user_id'), 'status' => 'confirmed'));
				redirect('member', 'refresh');
			} else {
				$this->crud->use_table('trainer_clients');
				$this->crud->update(array('id' => $this->input->post('request_id')), array('client_id' => $this->session->userdata('user_id'), 'status' => 'denied'));
				redirect('member', 'refresh');
			}
		}

		if ($this->form_validation->run() == false || $other_error == true) { //the user is not logging in so display the login page
			//set the flash data error message if there is one
			if ($other_error) {
				$this->data['message'] = $other_error;
			} else {
				$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			}

			$this->crud->use_table('trainer_clients');
			$trainer_client = $this->crud->retrieve(array('id' => $this->uri->segment(3)))->row();
			$this->data['trainer'] = $this->ion_auth->get_user($trainer_client->trainer_id);
			$this->data['decision'] = $this->form_validation->set_value('decision');
			$this->data['decision_options'] = array('' => 'Select a decision', 'true' => 'Yes he/she is my trainer', 'false' => 'No he/she is NOT my trainer');
			$this->data['request_id'] = array(
				'name' => 'request_id',
				'id' => 'request_id',
				'type' => 'hidden',
				'value' => $this->uri->segment(3),
			);
			$this->data['meta_keywords'] = 'TRNHRD';
			$this->data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';

			$this->load->view('base/header', $this->data);
			$this->load->view('base/integration_start');
			$this->load->view('member/confirm_trainer_request', $this->data);
			$this->load->view('base/integration_end');
			$this->load->view('base/footer', $this->data);
		}
	}

	function edit_progression_plan()
	{
		if (!$this->ion_auth->logged_in()) {
			//redirect them to the login page
			redirect('member/login', 'refresh');
		}
		$other_error = false;
		//validate form input
		$this->form_validation->set_rules('progression_plan_id', 'Progression Plan', 'required');

		if ($this->form_validation->run() == true) { //check to see if the user is logging in
			//check for "remember me"
			$this->crud->use_table('progression_plans');
			$progression_plan = $this->crud->retrieve(array('id' => $this->input->post('progression_plan_id')), 1)->row();

			if ($progression_plan->days_week != count($this->input->post('workoutdays'))) {
				$error_message = 'You must select ' . $progression_plan->days_week . ' days a week for the ' . $progression_plan->title . ' Plan';
				$other_error = true;
			} else {


				$user = $this->ion_auth->get_user();
				$user_values = array(
					'progression_plan_id' => $this->input->post('progression_plan_id'),
					'workoutdays' => implode(',', $this->input->post('workoutdays'))
				);
				if ($this->ion_auth->update_user($user->id, $user_values)) { //if the login is successful
					//redirect them back to the home page
					$this->workouts->progression_change_workouts($user->id);
					$this->session->set_flashdata('message', $this->ion_auth->messages());
					redirect('member', 'refresh');
				} else { //if the login was un-successful
					//redirect them back to the login page
					$this->session->set_flashdata('message', $this->ion_auth->errors());
					redirect('member/edit_progression_plan', 'refresh'); //use redirects instead of loading views for compatibility with MY_Controller libraries
				}
			}
		}

		if ($this->form_validation->run() == false || $other_error == true) { //the user is not logging in so display the login page
			//set the flash data error message if there is one
			if ($other_error) {
				$this->data['message'] = $error_message;
			} else {
				$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			}

			if ($this->input->post('submit') != '') {
				$this->data['progression_plan_value'] = $this->form_validation->set_value('progression_plan_id');
				$user_workoutdays = $this->input->post('workoutdays');
			} else {
				$user = $this->ion_auth->get_user();
				$this->data['progression_plan_value'] = $user->progression_plan_id;
				$user_workoutdays = explode(',', $user->workoutdays);
			}


			$this->data['progression_plan_options'] = array('' => 'Choose Progression Plan');
			$this->crud->use_table('progression_plans');
			$query = $this->crud->retrieve();
			foreach ($query->result() as $plan) {
				$this->data['progression_plan_options'][$plan->id] = $plan->title . ' (' . $plan->days_week . ' a week)';
			}



			$weekdays = array(1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday');
			foreach ($weekdays as $day => $title) {
				$this->data['weekday_title'][$day] = $title;
				if (is_array($user_workoutdays) && in_array($day, $user_workoutdays)) {
					$this->data['weekdays'][$day] = array(
						'name' => 'workoutdays[]',
						'checked' => TRUE,
						'value' => $day
					);
				} else {
					$this->data['weekdays'][$day] = array(
						'name' => 'workoutdays[]',
						'checked' => FALSE,
						'value' => $day
					);
				}
			}
			$this->data['meta_keywords'] = 'TRNHRD';
			$this->data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';

			$this->load->view('base/header', $this->data);
			$this->load->view('base/integration_start');
			$this->load->view('member/edit_progression_plan', $this->data);
			$this->load->view('base/integration_end');
			$this->load->view('base/footer', $this->data);
		}
	}

	function calendar()
	{
		$prefs = array(
			'show_next_prev' => TRUE,
			'next_prev_url' => '/member/calendar/'
		);

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
		
		   {cal_cell_no_content}<div class="date">{day}</div>{/cal_cell_no_content}
		   {cal_cell_no_content_today}<div class="date">{day}</div>{/cal_cell_no_content_today}
		
		   {cal_cell_blank}&nbsp;{/cal_cell_blank}
		
		   {cal_cell_end}</td>{/cal_cell_end}
		   {cal_row_end}</tr>{/cal_row_end}
		
		   {table_close}</tbody></table>{/table_close}
		';
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

		$workouts = $this->workouts->get_monthly_workouts($this->data['month'], $this->data['year'], $this->session->userdata('user_id'));
		$this->data['workouts'] = array();
		foreach ($workouts->result() as $workout) {
			if ($workout->title == '') {
				$title = 'Workout';
			} else {
				$title = $workout->title;
			}

			if ($workout->workout_created == 'true') {
				$url = date('Y/m/d', strtotime($workout->workout_date));
				$this->data['workouts'][date('j', strtotime($workout->workout_date))] = '<a href="/member/log_book/' . $url . '">' . $title . '</a>';
				if ($workout->trainer_workout_id != '') {
					$this->data['workouts'][date('j', strtotime($workout->workout_date))] .= ' created by ' . $workout->first_name . ' ' . $workout->last_name;
				} elseif ($workout->pro_title != '') {
					$this->data['workouts'][date('j', strtotime($workout->workout_date))] .= ' ' . $workout->pro_title . ' Progression Day';
				}
			} else {
				$this->data['workouts'][date('j', strtotime($workout->workout_date))] = $title . ' not generated yet, complete previous workouts first';
			}
		}
		$this->header_data['meta_keywords'] = 'TRNHRD';
		$this->header_data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';

		$this->load->library('calendar', $prefs);
		$this->load->view('base/header', $this->header_data);
		$this->load->view('base/integration_start');
		$this->load->view('member/calendar', $this->data);
		$this->load->view('base/integration_end');
		$this->load->view('base/footer');
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
		$this->header_data['meta_keywords'] = 'TRNHRD';
		$this->header_data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';
		$this->header_data['assets'] = 'logbook';

		$this->load->library('calendar', $prefs);
		$this->load->view('base/header', $this->header_data);
		$this->load->view('base/integration_start');
		$this->load->view('member/client_calendar', $this->data);
		$this->load->view('base/integration_end');
		$this->load->view('base/footer');
	}

	function log_book()
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
		$this->header_data['meta_keywords'] = 'TRNHRD';
		$this->header_data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';
		$this->header_data['assets'] = 'logbook';

		$this->load->view('base/header', $this->header_data);
		$this->load->view('base/integration_start');
		$this->load->view('member/log_book', $this->data);
		$this->load->view('base/integration_end');
		$this->load->view('base/footer');
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
		$this->header_data['meta_keywords'] = 'TRNHRD';
		$this->header_data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';
		$this->header_data['assets'] = 'logbook';
		$this->load->view('base/header', $this->header_data);
		$this->load->view('base/integration_start');
		$this->load->view('member/client_log_book', $this->data);
		$this->load->view('base/integration_end');
		$this->load->view('base/footer');
	}

	//log the user out
	function logout()
	{
		$this->data['title'] = "Logout";

		//log the user out
		$logout = $this->ion_auth->logout();

		//redirect them back to the page they came from
		redirect('/', 'refresh');
	}

	//change password
	function change_password()
	{
		$this->form_validation->set_rules('old', 'Old password', 'required');
		$this->form_validation->set_rules('new', 'New Password', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
		$this->form_validation->set_rules('new_confirm', 'Confirm New Password', 'required');

		if (!$this->ion_auth->logged_in()) {
			redirect('member/login', 'refresh');
		}
		$user = $this->ion_auth->get_user($this->session->userdata('user_id'));

		if ($this->form_validation->run() == false) { //display the form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->data['old_password'] = array(
				'name' => 'old',
				'id' => 'old',
				'type' => 'password',
			);
			$this->data['new_password'] = array(
				'name' => 'new',
				'id' => 'new',
				'type' => 'password',
			);
			$this->data['new_password_confirm'] = array(
				'name' => 'new_confirm',
				'id' => 'new_confirm',
				'type' => 'password',
			);
			$this->data['user_id'] = array(
				'name' => 'user_id',
				'id' => 'user_id',
				'type' => 'hidden',
				'value' => $user->id,
			);
			$this->data['meta_keywords'] = 'TRNHRD';
			$this->data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';
			//render
			$this->load->view('base/header', $this->data);
			$this->load->view('base/integration_start');
			$this->load->view('member/change_password', $this->data);
			$this->load->view('base/integration_end');
			$this->load->view('base/footer');
		} else {
			$identity = $this->session->userdata($this->config->item('identity', 'ion_auth'));

			$change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

			if ($change) { //if the password was successfully changed
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				$this->logout();
			} else {
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('member/change_password', 'refresh');
			}
		}
	}

	//change password
	function edit_account()
	{
		$this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
		$this->form_validation->set_rules('email', 'Email Address', 'required|valid_email');

		if (!$this->ion_auth->logged_in()) {
			redirect('member/login', 'refresh');
		}
		$user = $this->ion_auth->get_user($this->session->userdata('user_id'));

		if ($this->form_validation->run() == false) { //display the form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->data['first_name'] = array(
				'name' => 'first_name',
				'id' => 'first_name',
				'type' => 'text',
				'value' => $user->first_name
			);
			$this->data['last_name'] = array(
				'name' => 'last_name',
				'id' => 'last_name',
				'type' => 'text',
				'value' => $user->last_name
			);
			$this->data['city'] = array(
				'name' => 'city',
				'id' => 'city',
				'type' => 'text',
				'value' => $user->city
			);
			$this->data['state_options'] = array(
				'AL' => "Alabama",
				'AK' => "Alaska",
				'AZ' => "Arizona",
				'AR' => "Arkansas",
				'CA' => "California",
				'CO' => "Colorado",
				'CT' => "Connecticut",
				'DE' => "Delaware",
				'DC' => "District Of Columbia",
				'FL' => "Florida",
				'GA' => "Georgia",
				'HI' => "Hawaii",
				'ID' => "Idaho",
				'IL' => "Illinois",
				'IN' => "Indiana",
				'IA' => "Iowa",
				'KS' => "Kansas",
				'KY' => "Kentucky",
				'LA' => "Louisiana",
				'ME' => "Maine",
				'MD' => "Maryland",
				'MA' => "Massachusetts",
				'MI' => "Michigan",
				'MN' => "Minnesota",
				'MS' => "Mississippi",
				'MO' => "Missouri",
				'MT' => "Montana",
				'NE' => "Nebraska",
				'NV' => "Nevada",
				'NH' => "New Hampshire",
				'NJ' => "New Jersey",
				'NM' => "New Mexico",
				'NY' => "New York",
				'NC' => "North Carolina",
				'ND' => "North Dakota",
				'OH' => "Ohio",
				'OK' => "Oklahoma",
				'OR' => "Oregon",
				'PA' => "Pennsylvania",
				'RI' => "Rhode Island",
				'SC' => "South Carolina",
				'SD' => "South Dakota",
				'TN' => "Tennessee",
				'TX' => "Texas",
				'UT' => "Utah",
				'VT' => "Vermont",
				'VA' => "Virginia",
				'WA' => "Washington",
				'WV' => "West Virginia",
				'WI' => "Wisconsin",
				'WY' => "Wyoming"
			);
			$this->data['state_value'] = $user->state;
			$this->data['zip'] = array(
				'name' => 'zip',
				'id' => 'zip',
				'type' => 'text',
				'value' => $user->zip,
				'class' => 'midsize',
				'size' => 25
			);

			$this->data['email'] = array(
				'name' => 'email',
				'id' => 'email',
				'type' => 'text',
				'value' => $user->email,
				'class' => 'midsize',
				'size' => 25
			);
			$this->data['meta_keywords'] = 'TRNHRD';
			$this->data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';

			//render
			$this->load->view('base/header', $this->data);
			$this->load->view('base/integration_start');
			$this->load->view('member/edit_account', $this->data);
			$this->load->view('base/integration_end');
			$this->load->view('base/footer');
		} else {
			$update_data = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'city' => $this->input->post('city'),
				'state' => $this->input->post('state'),
				'zip' => $this->input->post('zip'),
				'email' => $this->input->post('email')
			);

			$change = $this->ion_auth->update_user($this->session->userdata('user_id'), $update_data);

			if ($change) { //if the password was successfully changed
				redirect('member', 'refresh');
			} else {
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('member/edit_account', 'refresh');
			}
		}
	}

	//forgot password
	function forgot_password()
	{
		$this->form_validation->set_rules('email', 'Email Address', 'required');
		if ($this->form_validation->run() == false) {
			//setup the input
			$this->data['email'] = array(
				'name' => 'email',
				'id' => 'email',
			);
			//set any errors and display the form
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
			$this->data['meta_keywords'] = 'TRNHRD';
			$this->data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';
			$this->load->view('base/header', $this->data);
			$this->load->view('base/integration_start');
			$this->load->view('member/forgot_password', $this->data);
			$this->load->view('base/integration_end');
			$this->load->view('base/footer');
		} else {
			//run the forgotten password method to email an activation code to the user
			$forgotten = $this->ion_auth->forgotten_password($this->input->post('email'));

			if ($forgotten) { //if there were no errors
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect("member/login", 'refresh'); //we should display a confirmation page here instead of the login page
			} else {
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect("member/forgot_password", 'refresh');
			}
		}
	}

	//reset password - final step for forgotten password
	public function reset_password($code)
	{
		$reset = $this->ion_auth->forgotten_password_complete($code);

		if ($reset) { //if the reset worked then send them to the login page
			$this->session->set_flashdata('message', $this->ion_auth->messages());
			redirect("member/login", 'refresh');
		} else { //if the reset didnt work then send them back to the forgot password page
			$this->session->set_flashdata('message', $this->ion_auth->errors());
			redirect("member/forgot_password", 'refresh');
		}
	}

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

		if ($this->ion_auth->logged_in()) {
			redirect('member', 'refresh');
		}

		$this->load->helper('captcha');
		$captcha_error = false;

		//validate form input
		$this->form_validation->set_rules('member_type', 'Member Type', 'required|xss_clean');
		$this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
		$this->form_validation->set_rules('username', 'Username', 'required|xss_clean');
		$this->form_validation->set_rules('terms_accept', 'Terms of Use', 'required|xss_clean');
		$this->form_validation->set_rules('email', 'Email Address', 'required|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
		$this->form_validation->set_rules('password_confirm', 'Password Confirmation', 'required');

		if ($this->form_validation->run() == true) {
			$group = $this->input->post('member_type');
			if (!in_array($group, array('members', 'trainers'))) {
				$group = 'members';
			}

			$username = $this->input->post('username');
			$email = $this->input->post('email');
			$password = $this->input->post('password');

			$additional_data = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'city' => $this->input->post('city'),
				'state' => $this->input->post('state'),
				'zip' => $this->input->post('zip')
			);

			if ($_POST['hpot'] != '') {
				$captcha_error = true;
			}
		}

		if (!$captcha_error && $this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data, $group)) { //check to see if we are creating the user
			//redirect them back to the admin page
			// $this->session->set_flashdata('message', "Your account has been created, please login now");
			// redirect("member/login", 'refresh');
			if ($this->ion_auth->login($username, $password)) {
				//if the login is successful
				//redirect them back to the home page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect('member', 'refresh');
			}
		} else { //display the create user form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

			if ($captcha_error) {
				$this->data['message'] = "Sorry we do not accept registrations from bots";
			}


			$this->data['hpot'] = array(
				'name' => 'hpot',
				'id' => 'hpot',
				'type' => 'hidden',
				'value' => ''
			);
			$this->data['first_name'] = array(
				'name' => 'first_name',
				'id' => 'first_name',
				'type' => 'text',
				'value' => $this->form_validation->set_value('first_name'),
				'class' => 'form-control',
				'required' => 'required',
				'placeholder' => 'First name',
				'size' => 25
			);
			$this->data['last_name'] = array(
				'name' => 'last_name',
				'id' => 'last_name',
				'type' => 'text',
				'value' => $this->form_validation->set_value('last_name'),
				'class' => 'form-control',
				'required' => 'required',
				'placeholder' => 'Last name',
				'size' => 25
			);
			$this->data['city'] = array(
				'name' => 'city',
				'id' => 'city',
				'type' => 'text',
				'value' => $this->form_validation->set_value('city'),
				'class' => 'form-control',
				'required' => 'required',
				'placeholder' => 'City',
				'style' => 'padding-inline-start: 35px;',
				'size' => 25
			);
			$this->data['state_options'] = array(
				'' => "State",
				'AL' => "Alabama",
				'AK' => "Alaska",
				'AZ' => "Arizona",
				'AR' => "Arkansas",
				'CA' => "California",
				'CO' => "Colorado",
				'CT' => "Connecticut",
				'DE' => "Delaware",
				'DC' => "District Of Columbia",
				'FL' => "Florida",
				'GA' => "Georgia",
				'HI' => "Hawaii",
				'ID' => "Idaho",
				'IL' => "Illinois",
				'IN' => "Indiana",
				'IA' => "Iowa",
				'KS' => "Kansas",
				'KY' => "Kentucky",
				'LA' => "Louisiana",
				'ME' => "Maine",
				'MD' => "Maryland",
				'MA' => "Massachusetts",
				'MI' => "Michigan",
				'MN' => "Minnesota",
				'MS' => "Mississippi",
				'MO' => "Missouri",
				'MT' => "Montana",
				'NE' => "Nebraska",
				'NV' => "Nevada",
				'NH' => "New Hampshire",
				'NJ' => "New Jersey",
				'NM' => "New Mexico",
				'NY' => "New York",
				'NC' => "North Carolina",
				'ND' => "North Dakota",
				'OH' => "Ohio",
				'OK' => "Oklahoma",
				'OR' => "Oregon",
				'PA' => "Pennsylvania",
				'RI' => "Rhode Island",
				'SC' => "South Carolina",
				'SD' => "South Dakota",
				'TN' => "Tennessee",
				'TX' => "Texas",
				'UT' => "Utah",
				'VT' => "Vermont",
				'VA' => "Virginia",
				'WA' => "Washington",
				'WV' => "West Virginia",
				'WI' => "Wisconsin",
				'WY' => "Wyoming"
			);
			$this->data['state_value'] = $this->form_validation->set_value('state');
			$this->data['zip'] = array(
				'name' => 'zip',
				'id' => 'zip',
				'type' => 'text',
				'value' => $this->form_validation->set_value('zip'),
				'class' => 'form-control',
				'required' => 'required',
				'placeholder' => 'Zip',
				'style' => 'padding-inline-start: 35px;',
				'size' => 25
			);
			$this->data['username'] = array(
				'name' => 'username',
				'id' => 'username',
				'type' => 'text',
				'value' => $this->form_validation->set_value('username'),
				'class' => 'form-control',
				'required' => 'required',
				'placeholder' => 'Username',
				'style' => 'padding-inline-start: 35px;',
				'size' => 40
			);
			$this->data['email'] = array(
				'name' => 'email',
				'id' => 'email',
				'type' => 'text',
				'value' => $this->form_validation->set_value('email'),
				'class' => 'form-control',
				'required' => 'required',
				'placeholder' => 'Email',
				'style' => 'padding-inline-start: 35px;',
				'size' => 40
			);
			$this->data['password'] = array(
				'name' => 'password',
				'id' => 'password',
				'type' => 'password',
				'value' => $this->form_validation->set_value('password'),
				'class' => 'form-control',
				'placeholder' => 'Password',
				'style' => 'padding-inline-start: 35px;',
				'required' => 'required',
			);
			$this->data['password_confirm'] = array(
				'name' => 'password_confirm',
				'id' => 'password_confirm',
				'type' => 'password',
				'value' => $this->form_validation->set_value('password_confirm'),
				'class' => 'form-control',
				'placeholder' => 'Confirm Password',
				'style' => 'padding-inline-start: 35px;',
				'required' => 'required',
			);

			$this->data['terms_accept'] = array(
				'name' => 'terms_accept',
				'id' => 'terms_accept',
				'value' => 'accept',
				'type' => "checkbox",
			);
			$this->header_data['url_segment'] = 'register';
			$this->header_data['meta_keywords'] = 'TRNHRD';
			$this->header_data['page_title'] = 'TRNHRD';
			$this->header_data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';

			$this->header_data['assets'] = 'register';
			$this->load->view('base/header', $this->header_data);
			// $this->load->view('base/integration_start');
			// $this->load->view('member/register', $this->data);
			// $this->load->view('base/integration_end');
			$this->data['footer_visibility'] = 'no';

			$this->load->view('base/register', $this->data);

			$this->load->view('base/footer', $this->data);
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
}