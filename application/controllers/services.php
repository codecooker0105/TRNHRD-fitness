<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! class_exists('Controller'))
{
	class Controller extends CI_Controller {}
}
require APPPATH.'/libraries/REST_Controller.php';

class Services extends REST_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->library('ion_auth');
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->database();
		$this->load->helper('url');
	}
	
	function parseRequestHeaders() {
		$headers = array();
		foreach($_SERVER as $key => $value) {
			if (substr($key, 0, 5) <> 'HTTP_') {
				continue;
			}
			$header = str_replace(' ', '-', strtolower(substr($key, 5)));
			$headers[$header] = $value;
		}
		return $headers;
	}


	//redirect if needed, otherwise display the user list
	function index()
	{
		//do nothing
		$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

		$this->data['username'] = array('name' => 'username',
			'id' => 'username',
			'type' => 'text',
			'value' => $this->form_validation->set_value('username'),
		);
		$this->data['password'] = array('name' => 'password',
			'id' => 'password',
			'type' => 'password',
		);
		$this->load->view('services/login',$this->data);
	}


	//log the user in
	function login_post()
	{
		//validate form input
		$body = json_decode(file_get_contents('php://input'),true);
		
		if (isset($body['username']) && isset($body['password']))
		{ //check to see if the user is logging in
			//check for "remember me"
			echo json_encode($this->ion_auth->mobile_login($body['username'], $body['password']));
		}
		else
		{  
			//do nothing
			echo json_encode(array('error' => 'Username and Password is required'));
		}
	}
	
	function submituserworkout_post_bu()
	{
		$headers = $this->parseRequestHeaders();
		if($this->workouts->check_api($headers['api_key'])){		
			//validate form input
			if($this->input->post('workout') == ''){
				$workout_data = json_encode($_POST);
				$this->form_validation->set_rules('UserId', '', 'required');
			}else{
				$this->form_validation->set_rules('workout', 'Workout Data', 'required');
			}
			
	
			if ($this->form_validation->run() == true)
			{ //check to see if the user is logging in
				//check for "remember me"
				if($workout_data == ''){
					$workout_data = $this->input->post('workout');
				}
				$workout = json_decode($workout_data);
				$user = $this->ion_auth->get_user($workout->UserId);
				$workout_details = $this->workouts->get_workout_details($workout->WorkoutId);
				$workout_values = array('completed' => 'true','user_comments' => $workout->comments);
				foreach($workout->exercises as $exercise){
					$sets = explode('|',$exercise->Sets);
					$reps = explode('|',$exercise->Reps);
					$time = explode('|',$exercise->Time);
					$weight = explode('|',$exercise->Weight);
					$difficulty = explode('|',$exercise->Difficulty);			
					$error = false;
					foreach($sets as $index => $set){
						if(isset($reps[$index]) && $reps[$index] != ''){
							$set_rep = $reps[$index];
						}else{
							$set_rep = NULL;
						}
						if(isset($weight[$index]) && $weight[$index] != ''){
							$set_weight = $weight[$index];
						}else{
							$set_weight = NULL;
						}
						if(isset($time[$index]) && $time[$index] != ''){
							$set_time = $time[$index];
						}else{
							$set_time = NULL;
						}
						if(isset($difficulty[$index]) && $difficulty[$index] != ''){
							$set_difficulty = $difficulty[$index];
						}else{
							$set_difficulty = NULL;
						}
						$stat_values = array('uw_id' => $workout->WorkoutId,
											'exercise_id' => $workout->ExerciseId,
											'uwe_id' => $workout->UserWorkoutExerciseId,
											'user_id' => $workout->UserId,
											'workout_date' => $workout_details->workout_date,
											'progression_id' => $workout_details->progression_id,
											'set' => $set,
											'reps' => $set_rep,
											'weight' => $set_weight,
											'time' => $set_time,
											'difficulty' => $set_difficulty);
						if(!$this->db->insert('user_workout_stats',$stat_values)){
							$error = true;
						}
					}
				}
				if($error === true){
					echo json_encode(array('StatusCode' => 1, 'Message' => 'Error saving workout data'));
				}else{
					$this->db->where('id',$workout->WorkoutId)->update('user_workout_stats',$workout_values);
					echo json_encode(array('StatusCode' => 0, 'Message' => ''));
				}
			}
			else
			{  
				//do nothing
				echo json_encode(array('StatusCode' => 1, 'Message' => 'Workout Data missing'));
			}
		}else{
			echo json_encode(array('StatusCode' => 1, 'Message' => 'Invalid API Key'));
		}
	}
	
	function submituserworkout_post()
	{
		if($this->workouts->check_api($this->parseRequestHeaders())){
			$workout = json_decode(file_get_contents('php://input'),true);
			
			//validate form input
			if($workout['uw_id'] != ''){
				if($workout_details = $this->workouts->get_workout_details($workout['uw_id'])){
					
				}else{
					die(json_encode(array('StatusCode' => 1, 'Message' => 'Invalid Workout ID')));
				}
			}
			
			if($workout['user_id'] != ''){
				if($user = $this->ion_auth->get_user($workout['user_id'])){
					
				}else{
					die(json_encode(array('StatusCode' => 1, 'Message' => 'Invalid User ID')));
				}
			}
			
	
			foreach($workout['exercises'] as $exercise){
				
				$sets = explode('|',$exercise['sets']);
				$reps = explode('|',$exercise['reps']);
				$time = explode('|',$exercise['time']);
				$weight = explode('|',$exercise['weight']);
				$difficulty = explode('|',$exercise['difficulty']);
				$error = false;
				foreach($sets as $index => $set){
					if(isset($reps[$index]) && $reps[$index] != ''){
						$set_rep = $reps[$index];
					}else{
						$set_rep = NULL;
					}
					if(isset($weight[$index]) && $weight[$index] != ''){
						$set_weight = $weight[$index];
					}else{
						$set_weight = NULL;
					}
					if(isset($time[$index]) && $time[$index] != ''){
						$set_time = $time[$index];
					}else{
						$set_time = NULL;
					}
					if(isset($difficulty[$index]) && $difficulty[$index] != ''){
						$set_difficulty = $difficulty[$index];
					}else{
						$set_difficulty = NULL;
					}
					$stat_values = array('uw_id' => $workout['uw_id'],
										'exercise_id' => $exercise['exercise_id'],
										'uwe_id' => $exercise['uwe_id'],
										'user_id' => $workout['user_id'],
										'workout_date' => $workout_details->workout_date,
										'progression_id' => $workout_details->progression_id,
										'set' => $set,
										'reps' => $set_rep,
										'weight' => $set_weight,
										'time' => $set_time,
										'difficulty' => $set_difficulty);
					if(!$this->db->insert('user_workout_stats',$stat_values)){
						$error = true;
					}
				}
			}
			
			if($error === true){
				echo json_encode(array('StatusCode' => 1, 'Message' => 'Error saving workout data'));
			}else{
				if($workout['status'] == 'complete' || $workout['status'] == 'quit'){
					$this->db->where('id',$workout['uw_id'])->update('user_workouts',array('completed'=>'true'));
				}
				echo json_encode(array('StatusCode' => 0, 'Message' => 'Workout Stats Saved'));
			}
		}else{
			echo json_encode(array('StatusCode' => 1, 'Message' => 'Invalid API Key'));
		}
	}
	
	function test_get(){
		if($workout = $this->workouts->get_last_workout(2)){ //workout exists
			$workout['uw_id'] = $workout->id;
			die('there');
		}else{ //we need to generate one somehow
			$user = $this->ion_auth->get_user(2);
			$workout_values = array('user_id' => 2, 'workout_date' => date("Y-m-d"), 'progression_plan_id' => $user->progression_plan_id);
			$this->db->insert('user_workouts',$workout_values);
			if($workout_id = $this->workouts->create_next_workout(2)){ //workout was created let's assign workout_id;
				$workout['uw_id'] = $workout_id;
			}else{ //There was a problem, let's direct them to the website
				die(json_encode(array('StatusCode' => 1, 'Message' => 'We could not get a workout for you. Please go to http://hybridfitness.com and create your workout plan')));
			}
			die('here');
		}
	}
	
	function renewuserworkout_post()
	{
		if($this->workouts->check_api($this->parseRequestHeaders())){
			$workout = json_decode(file_get_contents('php://input'),true);
			$error = false;
			//validate form input
			
			if($workout['user_id'] != ''){
				if($user = $this->ion_auth->get_user($workout['user_id'])){
					
				}else{
					die(json_encode(array('StatusCode' => 1, 'Message' => 'Invalid User ID')));
				}
			}
			
			if($workout['uw_id'] != ''){
				if($workout['uw_id'] < 0){ //Need to find a workout or generate one
					if($last_workout = $this->workouts->get_last_workout($workout['user_id'])){ //workout exists
						$workout['uw_id'] = $last_workout->id;
					}else{ //we need to generate one somehow
						//Let's create a blank workout and then use create_next_workout to generate one for us
						$workout_values = array('user_id' => $workout['user_id'], 'workout_date' => date("Y-m-d"), 'progression_plan_id' => $user->progression_plan_id);
						$this->db->insert('user_workouts',$workout_values);
						if($workout_id = $this->workouts->create_next_workout($workout['user_id'])){ //workout was created let's assign workout_id;
							$workout['uw_id'] = $workout_id;
						}else{ //There was a problem, let's direct them to the website
							die(json_encode(array('StatusCode' => 2, 'Message' => 'We could not get a workout for you. Please go to http://hybridfitness.com and create your workout plan. You will be redirected to the login page now.')));
						}
					}
				}elseif($workout_details = $this->workouts->get_workout_details($workout['uw_id'])){ //we have a workout to use
					
				}else{ //no workout to go with here
					die(json_encode(array('StatusCode' => 1, 'Message' => 'Invalid Workout ID')));
				}
			}else{
				die(json_encode(array('StatusCode' => 1, 'Message' => 'Invalid Workout ID')));
			}
			if(isset($workout_details->id)){
				unset($workout_details->id);
			}
			$workout_details->workout_date = date('Y-m-d');
			$workout_details->completed = 'false';
			$this->db->insert('user_workouts',$workout_details);
			$new_id = $this->db->insert_id();
			
			$workout_sections = $this->db->where('workout_id',$workout['uw_id'])->get('user_workout_sections')->result_array();			
	
			foreach($workout_sections as $section){
				$old_section_id = $section['id'];
				unset($section['id']);
				$section['workout_id'] = $new_id;
				$this->db->insert('user_workout_sections',$section);
				$new_section_id = $this->db->insert_id();
				
				$workout_exercises = $this->db->where('workout_id',$workout['uw_id'])->where('workout_section_id',$old_section_id)->get('user_workout_exercises')->result_array();
				foreach($workout_exercises as $exercise){
					unset($exercise['id']);
					$exercise['workout_id'] = $new_id;
					$exercise['workout_section_id'] = $new_section_id;
					$this->db->insert('user_workout_exercises',$exercise);			
				}
			}
			
			if($error === true){
				echo json_encode(array('StatusCode' => 1, 'Message' => 'Error renewing workout data'));
			}else{
				echo json_encode(array('StatusCode' => 0, 'Message' => 'Workout Renewed'));
			}
		}else{
			echo json_encode(array('StatusCode' => 1, 'Message' => 'Invalid API Key'));
		}
	}
	
	function submituserworkout_form()
	{
		//validate form input
		$this->data['workout_id'] = array('name' => 'WorkoutId',
			'type' => 'text'
		);
		$this->data['user_id'] = array('name' => 'UserId',
			'type' => 'text'
		);
		$this->data['exercise_id'] = array('name' => 'ExerciseId',
			'type' => 'text'
		);
		$this->data['uwe_id'] = array('name' => 'UserWorkoutExerciseId',
			'type' => 'text'
		);
		$this->data['sets'] = array('name' => 'Sets',
			'type' => 'text'
		);
		$this->data['reps'] = array('name' => 'Reps',
			'type' => 'text'
		);
		$this->data['time'] = array('name' => 'Time',
			'type' => 'text'
		);
		$this->data['weight'] = array('name' => 'Weight',
			'type' => 'text'
		);
		$this->data['difficulty'] = array('name' => 'Difficulty',
			'type' => 'text'
		);
		$this->load->view('services/submituserworkout_form',$this->data);
	}
	
	//log the user in
	function getworkout_get()
	{
		if($this->workouts->check_api($this->parseRequestHeaders())){
			//validate form input
			$workout_id = $this->get('workoutid');
			$workout = $this->workouts->get_workout($workout_id);
			echo json_encode($workout);
		}else{
			echo json_encode(array('error' => 'Invalid API Key'));
		}
	}
	
	//log the user in
	function getuserworkout_get()
	{
		if($this->workouts->check_api($this->parseRequestHeaders())){
			//validate form input
			$user_id = $this->get('userid');
			if($user_id != '' && is_numeric($user_id)){
				$user = $this->ion_auth->get_user($user_id);
				if($user){
					$workout = $this->workouts->get_user_workout($user_id,true);
					if($workout){
						echo json_encode($workout);
					}else{
						//No workout for today let's check to see if there is a future workout, if not we should autogenerate them one
						/*$workout = $this->db->select(array('user_workouts.*','trainer_workouts.trainer_id','trainer_workouts.start_date','trainer_workouts.end_date'))
							->join('trainer_workouts','user_workouts.trainer_workout_id = trainer_workouts.id','left')
							->where('user_workouts.completed','false')
							->where('user_workouts.user_id',$user_id)
							->where('user_workouts.workout_date > CURDATE()')
							->order_by('workout_date','asc')->limit(1)
							->get('user_workouts')->row();
						if(count($workout) > 0){
							echo json_encode(array('error' => 'There are no more workouts for today'));
						}else{
							//we need to create a workout
						}*/
						echo json_encode(array('error' => 'There are no more workouts for today'));
					}
				}else{
					echo json_encode(array('error' => 'Invalid User'));
				}
			}else{
				echo json_encode(array('error' => 'Invalid Parameters'));
			}
		}else{
			echo json_encode(array('error' => 'Invalid API Key'));
		}
	}
	
	//log the user in
	function getworkoutexercises_get()
	{
		$headers = $this->parseRequestHeaders();
		if($this->workouts->check_api($headers['api_key'])){
			//validate form input
			$ex_id = $this->get('ex_id');
			$exercise = $this->workouts->get_workout_exercise($ex_id);
			echo json_encode($exercise);
		}else{
			echo json_encode(array('error' => 'Invalid API Key'));
		}
	}
	
	function getexercisevideo_get()
	{
		$headers = $this->parseRequestHeaders();
		if($this->workouts->check_api($headers['api_key'])){
			//validate form input
			$ex_id = $this->get('ex_id');
			$exercise = $this->workouts->get_exercise($ex_id);
			if($exercise === false){
				$return = array('Error' => 'That video could not be located');
			}else{
				$return = array('URL' => $exercise->mobile_video);
			}
			echo json_encode($return);
		}else{
			echo json_encode(array('error' => 'Invalid API Key'));
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
		if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
				$this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue'))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

}
