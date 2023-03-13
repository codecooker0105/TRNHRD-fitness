<?php defined('BASEPATH') or exit('No direct script access allowed');

if (!class_exists('Controller')) {
	class Controller extends CI_Controller
	{
	}
}

class Admin extends Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->database();
		$this->load->helper('url');
		if ((!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) && $this->uri->segment(2) != 'login') {
			//redirect them to the login page
			redirect('admin/login', 'refresh');
		}

	}

	//redirect if needed, otherwise display the user list
	function index()
	{
		if (!$this->ion_auth->logged_in()) {
			//redirect them to the login page
			redirect('admin/login', 'refresh');
		} elseif (!$this->ion_auth->is_admin()) {
			//redirect them to the home page because they must be an administrator to view this
			redirect($this->config->item('base_url'), 'refresh');
		} else {
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->load->view('admin/header', $this->data);
			$this->load->view('admin/index', $this->data);
			$this->load->view('admin/footer', $this->data);
		}
	}

	//redirect if needed, otherwise display the user list
	function members()
	{
		if (!$this->ion_auth->logged_in()) {
			//redirect them to the login page
			redirect('admin/login', 'refresh');
		} elseif (!$this->ion_auth->is_admin()) {
			//redirect them to the home page because they must be an administrator to view this
			redirect($this->config->item('base_url'), 'refresh');
		} else {
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			//list the users
			$this->data['users'] = $this->ion_auth->get_users_array();
			$this->load->view('admin/header', array('assets' => 'admin_members'));
			$this->load->view('admin/members', $this->data);
			$this->load->view('admin/footer', $this->data);
		}
	}

	//redirect if needed, otherwise display the user list
	function exercises()
	{
		if (!$this->ion_auth->logged_in()) {
			//redirect them to the login page
			redirect('admin/login', 'refresh');
		} elseif (!$this->ion_auth->is_admin()) {
			//redirect them to the home page because they must be an administrator to view this
			redirect($this->config->item('base_url'), 'refresh');
		} else {
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			//list the users
			$this->crud->use_table('exercises');
			$this->data['exercises'] = $this->crud->retrieve('', 0, 0, array('title' => 'asc'))->result_array();
			$this->load->view('admin/header', $this->data);
			$this->load->view('admin/exercises', $this->data);
			$this->load->view('admin/footer', $this->data);
		}
	}

	function progression_plans()
	{
		if (!$this->ion_auth->logged_in()) {
			//redirect them to the login page
			redirect('admin/login', 'refresh');
		} elseif (!$this->ion_auth->is_admin()) {
			//redirect them to the home page because they must be an administrator to view this
			redirect($this->config->item('base_url'), 'refresh');
		} else {
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			//list the users
			$this->crud->use_table('progression_plans');
			$this->data['plans'] = $this->crud->retrieve('', 0, 0, array('title' => 'asc'))->result_array();
			$this->load->view('admin/header', $this->data);
			$this->load->view('admin/progression_plans', $this->data);
			$this->load->view('admin/footer', $this->data);
		}
	}

	//create  new exercise
	function create_exercise()
	{
		$this->form_validation->set_rules('title', 'Title', 'required');

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

			$this->data['experience_id'] = $this->form_validation->set_value('experience_id');
			$this->data['experience_options'] = array();
			$this->crud->use_table('experience_level');
			$query = $this->crud->retrieve();
			foreach ($query->result() as $experience) {
				$this->data['experience_options'][$experience->id] = $experience->title;
			}

			$this->data['exercise_muscles'] = array();
			$this->data['exercise_muscles'] = $this->form_validation->set_value('exercise_muscles');
			$this->data['muscle_options'] = array();
			$this->crud->use_table('muscles');
			$query = $this->crud->retrieve();
			foreach ($query->result() as $muscle) {
				$this->data['muscle_options'][$muscle->id] = $muscle->title;
			}

			$this->data['exercise_types'] = array();
			$this->data['exercise_types'] = $this->form_validation->set_value('exercise_types');
			$this->data['equipment_options'] = array();
			$this->crud->use_table('equipment');
			$query = $this->crud->retrieve();
			foreach ($query->result() as $equipment) {
				$this->data['equipment_options'][$equipment->id] = $equipment->title;
			}

			$this->data['exercise_equipment'] = array();
			$this->data['exercise_equipment'] = $this->form_validation->set_value('exercise_equipment');
			$this->data['type_options'] = array();
			$this->crud->use_table('exercise_types');
			$query = $this->crud->retrieve();
			foreach ($query->result() as $e_type) {
				$this->data['type_options'][$e_type->id] = $e_type->title;
			}

			$this->data['type'] = $this->form_validation->set_value('type');
			$this->data['weight_type'] = $this->form_validation->set_value('weight_type');

			$description = $this->form_validation->set_value('description');
			$this->data['description'] = array(
				'name' => 'description',

				'id' => 'description',
				'value' => $description
			);

			$this->data['video'] = '';
			$this->data['mobile_video'] = '';

			//render
			$this->load->view('admin/header', $this->data);
			$this->load->view('admin/create_exercise', $this->data);
			$this->load->view('admin/footer', $this->data);
		} else {
			$insert_values = array(
				'title' => $this->input->post('title'),
				'experience_id' => $this->input->post('experience_id'),
				'description' => $this->input->post('description'),
				'type' => $this->input->post('type'),
				'weight_type' => $this->input->post('weight_type')
			);

			$config['overwrite'] = true;
			$config['upload_path'] = './video/exercises/';
			$config['allowed_types'] = 'flv';

			$this->load->library('upload', $config);
			$this->upload->initialize($config);

			if ($_FILES['video']['error'] != 4 && $this->upload->do_upload('video')) {
				$upload_data = $this->upload->data();
				$video = '/video/exercises/' . $upload_data['file_name'];
				$insert_values['video'] = $video;
			} elseif ($_FILES['video']['error'] != 4) {
				$this->session->set_flashdata('message', $this->upload->display_errors());
				redirect('admin/create_exercise', 'refresh');
			}

			$config['overwrite'] = true;
			$config['upload_path'] = './video/mobile_exercises/';
			$config['allowed_types'] = 'mp4';

			$this->load->library('upload', $config);
			$this->upload->initialize($config);

			if ($_FILES['mobile_video']['error'] != 4 && $this->upload->do_upload('mobile_video')) {
				$upload_data = $this->upload->data();
				$video = '/video/mobile_exercises/' . $upload_data['file_name'];
				$insert_values['mobile_video'] = $video;
			} elseif ($_FILES['mobile_video']['error'] != 4) {
				$this->session->set_flashdata('message', $this->upload->display_errors());
				redirect('admin/create_exercise', 'refresh');
			}



			$this->crud->use_table('exercises');
			$new_exercise = $this->crud->create($insert_values);
			$exercise_id = $this->db->insert_id();



			if ($new_exercise) { //if the password was successfully changed
				$this->crud->use_table('exercise_muscles');
				if (is_array($this->input->post('exercise_muscles'))) {
					foreach ($this->input->post('exercise_muscles') as $muscle) {
						$this->crud->create(array('muscle_id' => $muscle, 'exercise_id' => $exercise_id));
					}
				}

				$this->crud->use_table('exercise_link_types');
				if (is_array($this->input->post('exercise_types'))) {
					foreach ($this->input->post('exercise_types') as $e_type) {
						$this->crud->create(array('type_id' => $e_type, 'exercise_id' => $exercise_id));
					}
				}

				$this->crud->use_table('exercise_equipment');
				if (is_array($this->input->post('exercise_equipment'))) {
					foreach ($this->input->post('exercise_equipment') as $equipment) {
						$this->crud->create(array('equipment_id' => $equipment, 'exercise_id' => $exercise_id));
					}
				}
				$this->session->set_flashdata('message', 'New Exercise Saved');
				redirect('admin/exercises', 'refresh');
			} else {
				$this->session->set_flashdata('message', 'Exercise Failed to Save');
				redirect('admin/add_exercise', 'refresh');
			}
		}
	}

	//change password
	function edit_user()
	{
		$this->form_validation->set_rules('first_name', 'First Name', 'required');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required');

		if ($this->form_validation->run() == false) { //display the form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$user = $this->ion_auth->get_user($this->uri->segment(3));

			if ($this->input->post('submit') != '') {
				$first_name = $this->form_validation->set_value('first_name');
				$last_name = $this->form_validation->set_value('last_name');
				$email = $this->form_validation->set_value('email');
				$city = $this->form_validation->set_value('city');
				$state = $this->form_validation->set_value('state');
				$zip = $this->form_validation->set_value('zip');
				$group_id = $this->form_validation->set_value('group_id');
				$username = $this->form_validation->set_value('username');
			} else {
				$first_name = $user->first_name;
				$last_name = $user->last_name;
				$email = $user->email;
				$city = $user->city;
				$state = $user->state;
				$zip = $user->zip;
				$group_id = $user->group_id;
				$username = $user->username;
			}

			$this->data['username'] = array(
				'name' => 'username',
				'id' => 'username',
				'type' => 'text',
				'value' => $username
			);
			$this->data['first_name'] = array(
				'name' => 'first_name',
				'id' => 'title',
				'type' => 'text',
				'value' => $first_name
			);
			$this->data['last_name'] = array(
				'name' => 'last_name',
				'id' => 'title',
				'type' => 'text',
				'value' => $last_name
			);
			$this->data['email'] = array(
				'name' => 'email',
				'id' => 'title',
				'type' => 'text',
				'value' => $email
			);

			$this->data['city'] = array(
				'name' => 'city',
				'id' => 'title',
				'type' => 'text',
				'value' => $city
			);

			$this->data['zip'] = array(
				'name' => 'zip',
				'id' => 'title',
				'type' => 'text',
				'value' => $zip
			);

			$this->data['user_id'] = array(
				'name' => 'user_id',
				'type' => 'hidden',
				'value' => $user->id,
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
			$this->data['state_value'] = $state;

			if ($current_trainer = $this->ion_auth->get_trainer($user->id)) {
				$this->data['trainer_value'] = $current_trainer->id;
			} else {
				$this->data['trainer_value'] = '';
			}
			$trainers = $this->ion_auth->get_users('trainers');
			$this->data['trainer_options'] = array('' => 'Select a Trainer');
			foreach ($trainers as $trainer) {
				$this->data['trainer_options'][$trainer->id] = $trainer->first_name . ' ' . $trainer->last_name;
			}


			$groups = $this->ion_auth->get_groups();
			$this->data['group_options'] = array();
			foreach ($groups as $group) {
				$this->data['group_options'][$group->id] = $group->description;
			}
			$this->data['group_value'] = $group_id;



			//render
			$this->load->view('admin/header', $this->data);
			$this->load->view('admin/edit_user', $this->data);
			$this->load->view('admin/footer', $this->data);
		} else {
			$trainer = $this->ion_auth->get_trainer($this->input->post('user_id'));
			$user_values = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'email' => $this->input->post('email'),
				'city' => $this->input->post('city'),
				'state' => $this->input->post('state'),
				'zip' => $this->input->post('zip'),
				'group_id' => $this->input->post('group_id'),
				'username' => $this->input->post('username')
			);
			if ($this->input->post('trainer_id') != '') {
				if ($trainer = $this->ion_auth->get_trainer($this->input->post('user_id'))) {
					if ($trainer->id != $this->input->post('trainer_id')) {
						$this->db->where('status', 'confirmed')->where('client_id', $this->input->post('user_id'))->delete('trainer_clients');
						$trainer_values = array(
							'trainer_id' => $this->input->post('trainer_id'),
							'client_id' => $this->input->post('user_id'),
							'status' => 'confirmed'
						);
						$this->db->insert('trainer_clients', $trainer_values);
					}
				} else {
					$trainer_values = array(
						'trainer_id' => $this->input->post('trainer_id'),
						'client_id' => $this->input->post('user_id'),
						'status' => 'confirmed'
					);
					$this->db->insert('trainer_clients', $trainer_values);
				}
			} else {
				$this->db->where('status', 'confirmed')->where('client_id', $this->input->post('user_id'))->delete('trainer_clients');
			}
			if ($this->ion_auth->update_user($this->input->post('user_id'), $user_values)) { //if the password was successfully changed
				$this->session->set_flashdata('message', 'Member Saved');
				redirect('admin/members', 'refresh');
			} else {
				$this->session->set_flashdata('message', 'Member Failed to Save');
				redirect('admin/edit_user/' . $this->input->post('user_id'), 'refresh');
			}
		}
	}

	function edit_password()
	{
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
		$this->form_validation->set_rules('password_confirm', 'Password Confirmation', 'required');

		if ($this->form_validation->run() == false) { //display the form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$user = $this->ion_auth->get_user($this->uri->segment(3));


			$this->data['password'] = array(
				'name' => 'password',
				'id' => 'password',
				'type' => 'password',
				'value' => '',
			);
			$this->data['password_confirm'] = array(
				'name' => 'password_confirm',
				'id' => 'password_confirm',
				'type' => 'password',
				'value' => '',
			);



			//render
			$this->load->view('admin/header', $this->data);
			$this->load->view('admin/edit_password', $this->data);
			$this->load->view('admin/footer', $this->data);
		} else {

			if ($this->ion_auth->admin_change_password($this->uri->segment(3), $this->input->post('password'))) { //if the password was successfully changed
				$this->session->set_flashdata('message', 'Member Password Saved');
				redirect('admin/members', 'refresh');
			} else {
				$this->session->set_flashdata('message', 'Member Password Failed to Save');
				redirect('admin/edit_password/' . $this->uri->segment(3), 'refresh');
			}
		}
	}

	//change password
	function edit_exercise()
	{
		$this->form_validation->set_rules('title', 'Title', 'required');

		if ($this->form_validation->run() == false) { //display the form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->crud->use_table('exercises');
			$exercise = $this->crud->retrieve(array('id' => $this->uri->segment(3)))->row();

			if ($this->input->post('submit') != '') {
				$title = $this->form_validation->set_value('title');
			} else {
				$title = $exercise->title;
			}
			$this->data['title'] = array(
				'name' => 'title',
				'id' => 'title',
				'type' => 'text',
				'value' => $exercise->title
			);

			if ($this->input->post('submit') != '') {
				$this->data['experience_id'] = $this->form_validation->set_value('experience_id');
			} else {
				$this->data['experience_id'] = $exercise->experience_id;
			}

			$this->data['experience_options'] = array();
			$this->crud->use_table('experience_level');
			$query = $this->crud->retrieve();
			foreach ($query->result() as $experience) {
				$this->data['experience_options'][$experience->id] = $experience->title;
			}

			$this->data['exercise_muscles'] = array();
			if ($this->input->post('submit') != '') {
				$this->data['exercise_muscles'] = $this->form_validation->set_value('exercise_muscles');
			} else {
				$this->crud->use_table('exercise_muscles');
				$muscles = $this->crud->retrieve(array('exercise_id' => $exercise->id), '', '', '', array('muscle_id'))->result();
				foreach ($muscles as $muscle) {
					$this->data['exercise_muscles'][] = $muscle->muscle_id;
				}
			}

			$this->data['muscle_options'] = array();
			$this->crud->use_table('muscles');
			$query = $this->crud->retrieve();
			foreach ($query->result() as $muscle) {
				$this->data['muscle_options'][$muscle->id] = $muscle->title;
			}

			$this->data['exercise_types'] = array();
			if ($this->input->post('submit') != '') {
				$this->data['exercise_types'] = $this->form_validation->set_value('exercise_types');
			} else {
				$this->crud->use_table('exercise_link_types');
				$e_types = $this->crud->retrieve(array('exercise_id' => $exercise->id), '', '', '', array('type_id'))->result();
				foreach ($e_types as $e_type) {
					$this->data['exercise_types'][] = $e_type->type_id;
				}
			}

			$this->data['equipment_options'] = array();
			$this->crud->use_table('equipment');
			$query = $this->crud->retrieve();
			foreach ($query->result() as $equipment) {
				$this->data['equipment_options'][$equipment->id] = $equipment->title;
			}

			$this->data['exercise_equipment'] = array();
			if ($this->input->post('submit') != '') {
				$this->data['exercise_equipment'] = $this->form_validation->set_value('exercise_equipment');
			} else {
				$this->crud->use_table('exercise_equipment');
				$equipments = $this->crud->retrieve(array('exercise_id' => $exercise->id), '', '', '', array('equipment_id'))->result();
				foreach ($equipments as $equipment) {
					$this->data['exercise_equipment'][] = $equipment->equipment_id;
				}
			}

			$this->data['type_options'] = array();
			$this->crud->use_table('exercise_types');
			$query = $this->crud->retrieve();
			foreach ($query->result() as $e_type) {
				$this->data['type_options'][$e_type->id] = $e_type->title;
			}

			if ($this->input->post('submit') != '') {
				$this->data['type'] = $this->form_validation->set_value('type');
			} else {
				$this->data['type'] = $exercise->type;
			}

			if ($this->input->post('submit') != '') {
				$this->data['weight_type'] = $this->form_validation->set_value('weight_type');
			} else {
				$this->data['weight_type'] = $exercise->weight_type;
			}

			if ($this->input->post('submit') != '') {
				$description = $this->form_validation->set_value('description');
			} else {
				$description = $exercise->description;
			}

			$this->data['description'] = array(
				'name' => 'description',


				'id' => 'description',
				'value' => $description
			);

			$this->data['video'] = $exercise->video;
			$this->data['mobile_video'] = $exercise->mobile_video;
			$this->data['exercise_id'] = array(
				'name' => 'exercise_id',
				'id' => 'exercise_id',
				'type' => 'hidden',
				'value' => $exercise->id,
			);


			//render
			$this->load->view('admin/header', $this->data);
			$this->load->view('admin/edit_exercise', $this->data);
			$this->load->view('admin/footer', $this->data);
		} else {
			$update_values = array(
				'title' => $this->input->post('title'),
				'experience_id' => $this->input->post('experience_id'),
				'description' => $this->input->post('description'),
				'type' => $this->input->post('type'),
				'weight_type' => $this->input->post('weight_type')
			);

			$config['overwrite'] = true;
			$config['upload_path'] = './video/exercises/';
			$config['allowed_types'] = 'flv';

			$this->load->library('upload', $config);
			$this->upload->initialize($config);

			if ($_FILES['video']['error'] != 4 && $this->upload->do_upload('video')) {
				$upload_data = $this->upload->data();
				$video = '/video/exercises/' . $upload_data['file_name'];
				$update_values['video'] = $video;
			} elseif ($_FILES['video']['error'] != 4) {
				$this->session->set_flashdata('message', $this->upload->display_errors());
				redirect('admin/edit_exercise/' . $this->input->post('exercise_id'), 'refresh');
			}

			$config['overwrite'] = true;
			$config['upload_path'] = './video/mobile_exercises/';
			$config['allowed_types'] = 'mp4';

			$this->load->library('upload', $config);
			$this->upload->initialize($config);

			if ($_FILES['mobile_video']['error'] != 4 && $this->upload->do_upload('mobile_video')) {
				$upload_data = $this->upload->data();
				$video = '/video/mobile_exercises/' . $upload_data['file_name'];
				$update_values['mobile_video'] = $video;
			} elseif ($_FILES['mobile_video']['error'] != 4) {
				$this->session->set_flashdata('message', $this->upload->display_errors());
				redirect('admin/edit_exercise/' . $this->input->post('exercise_id'), 'refresh');
			}



			$this->crud->use_table('exercises');
			$update = $this->crud->update(array('id' => $this->input->post('exercise_id')), $update_values);

			$this->crud->use_table('exercise_muscles');
			$this->crud->delete(array('exercise_id' => $this->input->post('exercise_id')));
			if (is_array($this->input->post('exercise_muscles'))) {
				foreach ($this->input->post('exercise_muscles') as $muscle) {
					$this->crud->create(array('muscle_id' => $muscle, 'exercise_id' => $this->input->post('exercise_id')));
				}
			}

			$this->crud->use_table('exercise_link_types');
			$this->crud->delete(array('exercise_id' => $this->input->post('exercise_id')));
			if (is_array($this->input->post('exercise_types'))) {
				foreach ($this->input->post('exercise_types') as $e_type) {
					$this->crud->create(array('type_id' => $e_type, 'exercise_id' => $this->input->post('exercise_id')));
				}
			}

			$this->crud->use_table('exercise_equipment');
			$this->crud->delete(array('exercise_id' => $this->input->post('exercise_id')));
			$this->crud->use_table('exercise_equipment');
			if (is_array($this->input->post('exercise_equipment'))) {
				foreach ($this->input->post('exercise_equipment') as $equipment) {
					$this->crud->create(array('equipment_id' => $equipment, 'exercise_id' => $this->input->post('exercise_id')));
				}
			}

			if ($update) { //if the password was successfully changed
				$this->session->set_flashdata('message', 'Exercise Saved');
				redirect('admin/exercises', 'refresh');
			} else {
				$this->session->set_flashdata('message', 'Exercise Failed to Save');
				redirect('admin/edit_exercise/' . $this->input->post('exercise_id'), 'refresh');
			}
		}
	}





	//change password
	function edit_progression_plan()
	{
		$this->form_validation->set_rules('title', 'Title', 'required');

		if ($this->form_validation->run() == false) { //display the form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->crud->use_table('progression_plans');
			$plan = $this->crud->retrieve(array('id' => $this->uri->segment(3)))->row();

			if ($this->input->post('submit') != '') {
				$title = $this->form_validation->set_value('title');
			} else {
				$title = $plan->title;
			}
			$this->data['title'] = array(
				'name' => 'title',
				'id' => 'title',
				'type' => 'text',
				'value' => $title
			);

			if ($this->input->post('submit') != '') {
				$this->data['focus_id'] = $this->form_validation->set_value('focus_id');
			} else {
				$this->data['focus_id'] = $plan->focus_id;
			}

			$this->data['focus_options'] = array();
			$this->crud->use_table('focus');
			$query = $this->crud->retrieve();
			foreach ($query->result() as $focus) {
				$this->data['focus_options'][$focus->id] = $focus->focus;
			}

			if ($this->input->post('submit') != '') {
				$this->data['days_week'] = $this->form_validation->set_value('days_week');
			} else {
				$this->data['days_week'] = $plan->days_week;
			}

			$this->data['days_week_options'] = array();
			for ($x = 1; $x <= 7; $x++) {
				$this->data['days_week_options'][$x] = $x . ' day(s)';
			}

			$this->data['days'] = array();
			if ($this->input->post('submit') != '') {
				$this->data['days'] = $this->form_validation->set_value('days');
			} else {
				$this->crud->use_table('progression_plan_days');
				$days = $this->crud->retrieve(array('plan_id' => $plan->id))->result();
				foreach ($days as $day) {
					$this->data['days'][$day->day] = $day->progression_id;
				}
			}
			for ($x = 1; $x <= 30; $x++) {
				if (!isset($this->data['days'][$x])) {
					$this->data['days'][$x] = '';
				}
			}

			$this->data['progression_options'] = array('' => 'Select a Progression');
			$this->crud->use_table('progressions');
			$query = $this->crud->retrieve();
			foreach ($query->result() as $progression) {
				$this->data['progression_options'][$progression->id] = $progression->title;
			}

			$this->data['plan_id'] = array(
				'name' => 'plan_id',
				'id' => 'plan_id',
				'type' => 'hidden',
				'value' => $plan->id,
			);

			//render
			$this->load->view('admin/header', $this->data);
			$this->load->view('admin/edit_progression_plans', $this->data);
			$this->load->view('admin/footer', $this->data);
		} else {
			$update_values = array(
				'title' => $this->input->post('title'),
				'focus_id' => $this->input->post('focus_id'),
				'days_week' => $this->input->post('days_week')
			);


			$this->crud->use_table('progression_plans');
			$update = $this->crud->update(array('id' => $this->input->post('plan_id')), $update_values);

			$this->crud->use_table('progression_plan_days');
			$this->crud->delete(array('plan_id' => $this->input->post('plan_id')));
			if (is_array($this->input->post('days'))) {
				foreach ($this->input->post('days') as $day_number => $progression) {
					if ($progression != '') {
						$this->crud->create(array('plan_id' => $this->input->post('plan_id'), 'day' => $day_number, 'progression_id' => $progression));
					}
				}
			}

			if ($update) { //if the password was successfully changed
				$this->session->set_flashdata('message', 'Plan Saved');
				redirect('admin/progression_plans', 'refresh');
			} else {
				$this->session->set_flashdata('message', 'Plan Failed to Save');
				redirect('admin/edit_progression_plan/' . $this->input->post('plan_id'), 'refresh');
			}
		}
	}



	//redirect if needed, otherwise display the user list
	function skeleton_workouts()
	{
		if (!$this->ion_auth->logged_in()) {
			//redirect them to the login page
			redirect('admin/login', 'refresh');
		} elseif (!$this->ion_auth->is_admin()) {
			//redirect them to the home page because they must be an administrator to view this
			redirect($this->config->item('base_url'), 'refresh');
		} else {
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			//list the users
			$this->crud->use_table('skeleton_workouts');
			$this->data['workouts'] = $this->crud->retrieve('', 0, 0, array('title' => 'asc'))->result_array();
			$this->load->view('admin/header', $this->data);
			$this->load->view('admin/skeleton_workouts', $this->data);
			$this->load->view('admin/footer', $this->data);
		}
	}

	//create  new exercise
	function create_skeleton_workout()
	{
		$this->form_validation->set_rules('title', 'Title', 'required');

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

			$this->data['workout_progressions'] = array();
			$this->data['workout_progressions'] = $this->form_validation->set_value('workout_progressions');
			$this->data['progression_options'] = array();
			$this->crud->use_table('progressions');
			$query = $this->crud->retrieve();
			foreach ($query->result() as $progression) {
				$this->data['progression_options'][$progression->id] = $progression->title;
			}



			//render
			$this->data['assets'] = 'admin_skeleton_workout';
			$this->load->view('admin/header', $this->data);
			$this->load->view('admin/create_skeleton_workout', $this->data);
			$this->load->view('admin/footer', $this->data);
		} else {
			$insert_values = array(
				'title' => $this->input->post('title')
			);


			$this->crud->use_table('skeleton_workouts');
			$new_workout = $this->crud->create($insert_values);
			$workout_id = $this->db->insert_id();



			if ($new_workout) { //if the password was successfully changed
				$this->crud->use_table('skeleton_focus');
				$this->crud->delete(array('skeleton_id' => $this->input->post('workout_id')));
				if (is_array($this->input->post('workout_progressions'))) {
					foreach ($this->input->post('workout_progressions') as $progression) {
						$this->crud->create(array('progression_id' => $progression, 'skeleton_id' => $workout_id));
					}
				}

				$workout_list = explode("|", $this->input->post('workout_list'));
				$section_count = 0;
				$category_count = 0;
				$new_section = false;
				$section_type_id = '';
				$this->crud->use_table('skeleton_section');
				$this->crud->delete(array('skeleton_id' => $this->input->post('workout_id')));
				$this->crud->use_table('skeleton_category');
				$this->crud->delete(array('skeleton_id' => $this->input->post('workout_id')));

				foreach ($workout_list as $item) {
					if ($item == 's') {
						$new_section = true;
					} elseif ($new_section === true) {
						$this->crud->use_table('skeleton_section');
						$this->crud->create(array('section_type_id' => $item, 'skeleton_id' => $workout_id, 'display_order' => $section_count));
						$section_type_id = $item;
						$section_count++;
						$category_count = 0;
						$section_id = $this->db->insert_id();
						$new_section = false;
					} else {
						$this->crud->use_table('skeleton_category');
						$this->crud->create(array('exercise_type_id' => $item, 'section_type_id' => $section_type_id, 'section_id' => $section_id, 'skeleton_id' => $workout_id, 'display_order' => $category_count));
						$category_count++;
					}

				}

				$this->session->set_flashdata('message', 'New Workout Saved');
				redirect('admin/skeleton_workouts', 'refresh');
			} else {
				$this->session->set_flashdata('message', 'workout Failed to Save');
				redirect('admin/create_skeleton_workout', 'refresh');
			}
		}
	}

	//change password
	function edit_skeleton_workout()
	{
		$this->form_validation->set_rules('title', 'Title', 'required');

		if ($this->form_validation->run() == false) { //display the form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->crud->use_table('skeleton_workouts');
			$workout = $this->crud->retrieve(array('id' => $this->uri->segment(3)))->row();

			if ($this->input->post('submit') != '') {
				$title = $this->form_validation->set_value('title');
			} else {
				$title = $workout->title;
			}
			$this->data['title'] = array(
				'name' => 'title',
				'id' => 'title',
				'type' => 'text',
				'value' => $title
			);

			$this->data['workout_progressions'] = array();
			if ($this->input->post('submit') != '') {
				$this->data['workout_progressions'] = $this->form_validation->set_value('workout_progressions');
			} else {
				$this->crud->use_table('skeleton_focus');
				$progressions = $this->crud->retrieve(array('skeleton_id' => $workout->id), '', '', '', array('progression_id'))->result();
				foreach ($progressions as $progression) {
					$this->data['workout_progressions'][] = $progression->progression_id;
				}
			}

			$this->data['progression_options'] = array();
			$this->crud->use_table('progressions');
			$query = $this->crud->retrieve();
			foreach ($query->result() as $progression) {
				$this->data['progression_options'][$progression->id] = $progression->title;
			}

			$this->data['workout_sections'] = $this->db->select(array('skeleton_section_types.title', 'skeleton_section_types.type', 'skeleton_section_types.type', 'skeleton_section.*'))->join('skeleton_section_types', 'skeleton_section_types.id = skeleton_section.section_type_id')->where('skeleton_id', $workout->id)->order_by('display_order', 'asc')->get('skeleton_section')->result_array();
			foreach ($this->data['workout_sections'] as $index => $section) {
				$this->data['workout_sections'][$index]['exercises'] = $this->db->select(array('exercise_types.title', 'skeleton_category.*'))->join('exercise_types', 'exercise_types.id = skeleton_category.exercise_type_id')->where('section_id', $section['id'])->order_by('display_order', 'asc')->get('skeleton_category')->result_array();
			}
			//print_r($this->data['workout_sections']);
			//die('here');


			$this->data['workout_id'] = array(
				'name' => 'workout_id',
				'id' => 'workout_id',
				'type' => 'hidden',
				'value' => $workout->id,
			);

			$this->data['assets'] = 'admin_skeleton_workout';
			//render
			$this->load->view('admin/header', $this->data);
			$this->load->view('admin/edit_skeleton_workout', $this->data);
			$this->load->view('admin/footer', $this->data);
		} else {
			$update_values = array(
				'title' => $this->input->post('title')
			);

			$this->crud->use_table('skeleton_workouts');
			$update = $this->crud->update(array('id' => $this->input->post('workout_id')), $update_values);

			$this->crud->use_table('skeleton_focus');
			$this->crud->delete(array('skeleton_id' => $this->input->post('workout_id')));
			if (is_array($this->input->post('workout_progressions'))) {
				foreach ($this->input->post('workout_progressions') as $progression) {
					$this->crud->create(array('progression_id' => $progression, 'skeleton_id' => $this->input->post('workout_id')));
				}
			}

			$workout_list = explode("|", $this->input->post('workout_list'));
			$section_count = 0;
			$category_count = 0;
			$new_section = false;
			$section_type_id = '';
			$this->crud->use_table('skeleton_section');
			$this->crud->delete(array('skeleton_id' => $this->input->post('workout_id')));
			$this->crud->use_table('skeleton_category');
			$this->crud->delete(array('skeleton_id' => $this->input->post('workout_id')));

			foreach ($workout_list as $item) {
				if ($item == 's') {
					$new_section = true;
				} elseif ($new_section === true) {
					$this->crud->use_table('skeleton_section');
					$this->crud->create(array('section_type_id' => $item, 'skeleton_id' => $this->input->post('workout_id'), 'display_order' => $section_count));
					$section_type_id = $item;
					$section_count++;
					$category_count = 0;
					$section_id = $this->db->insert_id();
					$new_section = false;
				} else {
					$this->crud->use_table('skeleton_category');
					$this->crud->create(array('exercise_type_id' => $item, 'section_type_id' => $section_type_id, 'section_id' => $section_id, 'skeleton_id' => $this->input->post('workout_id'), 'display_order' => $category_count));
					$category_count++;
				}

			}


			if ($update) { //if the password was successfully changed
				$this->session->set_flashdata('message', 'Workout Saved');
				redirect('admin/skeleton_workouts', 'refresh');
			} else {
				$this->session->set_flashdata('message', 'Workout Failed to Save');
				redirect('admin/edit_skeleton_workout/' . $this->input->post('workout_id'), 'refresh');
			}
		}
	}



	//log the user in
	function login()
	{
		$this->data['title'] = "Login";

		//validate form input
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == true) { //check to see if the user is logging in
			//check for "remember me"
			$remember = (bool) $this->input->post('remember');

			if ($this->ion_auth->login($this->input->post('username'), $this->input->post('password'), $remember)) { //if the login is successful
				//redirect them back to the home page
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect('admin', 'refresh');
			} else { //if the login was un-successful
				//redirect them back to the login page
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				redirect('admin/login', 'refresh'); //use redirects instead of loading views for compatibility with MY_Controller libraries
			}
		} else { //the user is not logging in so display the login page
			//set the flash data error message if there is one
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
			$this->load->view('admin/header', $this->data);
			$this->load->view('admin/login', $this->data);
			$this->load->view('admin/footer', $this->data);
		}
	}

	//log the user out
	function logout()
	{
		$this->data['title'] = "Logout";

		//log the user out
		$logout = $this->ion_auth->logout();

		//redirect them back to the page they came from
		redirect('auth', 'refresh');
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

			//render
			$this->load->view('member/change_password', $this->data);
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
			$this->load->view('member/forgot_password', $this->data);
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
			redirect("admin/members", 'refresh');
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
			$this->load->view('admin/header', $this->data);
			$this->load->view('admin/deactivate_user', $this->data);
			$this->load->view('admin/footer', $this->data);
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
			redirect('admin/members', 'refresh');
		}
	}

	//create a new user
	function create_member()
	{
		//validate form input
		$this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
		$this->form_validation->set_rules('last_name', 'Last Name', 'required|xss_clean');
		$this->form_validation->set_rules('username', 'Username', 'required|xss_clean');
		$this->form_validation->set_rules('email', 'Email Address', 'required|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
		$this->form_validation->set_rules('password_confirm', 'Password Confirmation', 'required');

		if ($this->form_validation->run() == true) {
			$username = $this->input->post('username');
			$email = $this->input->post('email');
			$password = $this->input->post('password');
			$group_name = $this->input->post('group_name');

			$additional_data = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'city' => $this->input->post('city'),
				'state' => $this->input->post('state'),
				'zip' => $this->input->post('zip')
			);
		}
		if ($this->form_validation->run() == true && $user_id = $this->ion_auth->register($username, $password, $email, $additional_data, $group_name)) { //check to see if we are creating the user
			//redirect them back to the admin page
			$insert_data = array(
				'user_id' => $user_id,
				'zip' => $this->input->post('zip'),
				'default' => 'true'
			);

			$this->crud->use_table('user_weather');
			$this->crud->create($insert_data);

			if ($this->input->post('trainer') != '') {
				$insert_data = array(
					'client_id' => $user_id,
					'trainer_id' => $this->input->post('trainer'),
					'status' => 'confirmed'
				);
				$this->crud->use_table('trainer_clients');
				$this->crud->create($insert_data);
			}
			$this->session->set_flashdata('message', "The member has been created.");
			redirect("admin/members", 'refresh');
		} else { //display the create user form
			//set the flash data error message if there is one
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

			$this->data['first_name'] = array(
				'name' => 'first_name',
				'id' => 'first_name',
				'type' => 'text',
				'value' => $this->form_validation->set_value('first_name'),
				'class' => 'midsize',
				'size' => 25
			);
			$this->data['last_name'] = array(
				'name' => 'last_name',
				'id' => 'last_name',
				'type' => 'text',
				'value' => $this->form_validation->set_value('last_name'),
				'class' => 'midsize',
				'size' => 25
			);
			$this->data['city'] = array(
				'name' => 'city',
				'id' => 'city',
				'type' => 'text',
				'value' => $this->form_validation->set_value('city'),
				'class' => 'midsize',
				'size' => 25
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
			$this->data['state_value'] = $this->form_validation->set_value('city');
			$this->data['zip'] = array(
				'name' => 'zip',
				'id' => 'zip',
				'type' => 'text',
				'value' => $this->form_validation->set_value('zip'),
				'class' => 'midsize',
				'size' => 25
			);
			$this->data['username'] = array(
				'name' => 'username',
				'id' => 'username',
				'type' => 'text',
				'value' => $this->form_validation->set_value('username'),
				'class' => 'required midsize',
				'size' => 40
			);
			$this->data['email'] = array(
				'name' => 'email',
				'id' => 'email',
				'type' => 'text',
				'value' => $this->form_validation->set_value('email'),
				'class' => 'required email midsize',
				'size' => 40
			);
			$this->data['password'] = array(
				'name' => 'password',
				'id' => 'password',
				'type' => 'password',
				'value' => $this->form_validation->set_value('password'),
			);
			$this->data['password_confirm'] = array(
				'name' => 'password_confirm',
				'id' => 'password_confirm',
				'type' => 'password',
				'value' => $this->form_validation->set_value('password_confirm'),
			);

			$groups = $this->ion_auth->get_groups();
			$this->data['group_options'] = array();
			foreach ($groups as $group) {
				$this->data['group_options'][$group->name] = $group->description;
			}

			$trainers = $this->ion_auth->get_users('trainers');
			$this->data['trainer_options'] = array('' => 'Select a Trainer');
			foreach ($trainers as $trainer) {
				$this->data['trainer_options'][$trainer->id] = $trainer->last_name . ', ' . $trainer->first_name;
			}

			$this->header_data['assets'] = 'register';
			$this->load->view('admin/header', $this->header_data);
			$this->load->view('admin/create_member', $this->data);
			$this->load->view('admin/footer', $this->data);
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

	function delete_member()
	{
		$user_id = $this->input->post('user_id');
		if ($this->ion_auth->delete_user($user_id)) {
			echo json_encode(array('success' => 'true'));
		} else {
			echo json_encode(array('error' => 'Error Deleting Member'));
		}
	}

	function delete_exercise()
	{
		$exercise_id = $this->input->post('exercise_id');
		if ($this->workout_model->delete_exercise($exercise_id)) {
			echo json_encode(array('success' => 'true'));
		} else {
			echo json_encode(array('error' => 'Error Deleting Exercise'));
		}
	}

	function delete_users_workouts()
	{
		$result = mysql_query("SELECT * FROM user_workouts WHERE user_id = '" . $this->uri->segment(3) . "'");
		while ($row = mysql_fetch_assoc($result)) {
			$this->db->delete('user_workout_exercises', array('workout_id' => $row['id']));
			$this->db->delete('user_workout_sections', array('workout_id' => $row['id']));
			$this->db->delete('user_workout_stats', array('uw_id' => $row['id']));
			$this->db->delete('user_workouts', array('id' => $row['id']));
		}

	}

}