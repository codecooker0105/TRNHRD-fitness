<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Base extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	function home()
	{
		$data['page_title'] = 'Home';
		$data['meta_keywords'] = 'TRNHRD';
		$data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';
		$data['assets'] = 'homepage';
		$this->load->view('base/header', $data);
		$this->load->view('base/intro', $data);
		$this->load->view('base/home', $data);
		$this->load->view('base/footer', $data);
	}

	function index()
	{
		$data['page_title'] = 'TRNHRD';
		$data['meta_keywords'] = 'TRNHRD';
		$data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';
		$data['url_segment'] = 'index';
		$this->load->view('base/header', $data);
		$this->load->view('base/landing', $data);
		$this->load->view('base/footer', $data);
	}

	function contact()
	{
		$data['page_title'] = 'TRNHRD';
		$data['meta_keywords'] = 'TRNHRD';
		$data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';
		$data['url_segment'] = 'contact';
		$this->load->view('base/header', $data);
		$this->load->view('base/contact', $data);
		$this->load->view('base/footer', $data);
	}

	function about()
	{
		$data['page_title'] = 'TRNHRD';
		$data['meta_keywords'] = 'TRNHRD';
		$data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';
		$data['url_segment'] = 'about';
		$this->load->view('base/header', $data);
		$this->load->view('base/about', $data);
		$this->load->view('base/footer', $data);
	}

	function testimonial()
	{
		$data['page_title'] = 'TRNHRD';
		$data['meta_keywords'] = 'TRNHRD';
		$data['meta_description'] = 'Accomplish your fitness goals with our affordable, certified personal trainer in New York. Find out how Trnhrd helps you create challenging workouts more easily.';
		$data['url_segment'] = 'testimonial';
		$this->load->view('base/header', $data);
		$this->load->view('base/testimonial', $data);
		$this->load->view('base/footer', $data);
	}

	function subscriber()
	{
		$data['page_title'] = 'Fitness Training Programs';
		$data['meta_description'] = 'Our fitness training programs are aimed to bring about a lifestyle change in the individuals life. Check out our fitness programs, become a member today. ';
		$this->load->view('base/header', $data);
		$this->load->view('base/intro', $data);
		$this->load->view('base/subscriber', $data);
		$this->load->view('base/footer', $data);
	}

	function professional()
	{
		$data['page_title'] = 'Personal Trainer';
		$data['meta_description'] = 'Looking for a training program to improve your fitness? Our certified personal trainer provides you with an exercise program and individualised support. ';
		$this->load->view('base/header', $data);
		$this->load->view('base/intro', $data);
		$this->load->view('base/professional', $data);
		$this->load->view('base/footer', $data);
	}

	function what()
	{
		$data['page_title'] = 'What';
		$data['meta_description'] = 'Get personalised training from fitness professional at Trnhrd. Christopher Cosentino is the founder and developer of Trnhrd. Find out more about our fitness trainer right here!';
		$this->load->view('base/header', $data);
		$this->load->view('base/intro', $data);
		$this->load->view('base/what', $data);
		$this->load->view('base/footer', $data);
	}

	function how()
	{
		$data['page_title'] = 'How it Works';
		$this->load->view('base/header', $data);
		$this->load->view('base/intro', $data);
		$this->load->view('base/how', $data);
		$this->load->view('base/footer', $data);
	}

	function why()
	{
		$data['page_title'] = 'Why We Like It';
		$this->load->view('base/header', $data);
		$this->load->view('base/intro', $data);
		$this->load->view('base/why', $data);
		$this->load->view('base/footer', $data);
	}

	function clear()
	{
		if ($this->uri->segment(3) == 'joshua') {
			$result = mysql_query("SELECT * FROM user_workouts WHERE user_id = '496'");
			while ($row = mysql_fetch_assoc($result)) {
				$this->db->delete('user_workout_exercises', array('workout_id' => $row['id']));
				$this->db->delete('user_workout_sections', array('workout_id' => $row['id']));
				$this->db->delete('user_workout_stats', array('uw_id' => $row['id']));
				$this->db->delete('user_workouts', array('id' => $row['id']));
			}
			echo 'Your workouts and stats have been deleted';
		} else {
			echo 'invalid';
		}
	}

	function expire()
	{
		if ($this->uri->segment(3) == 'joshua') {
			$result = mysql_query("SELECT * FROM user_workouts WHERE user_id = '496'");
			while ($row = mysql_fetch_assoc($result)) {
				$new_date = strtotime("-3 months", strtotime($row['workout_date']));
				$new_date = date('Y-m-d', $new_date);
				die($new_date);
				$this->db->where('id', $row['id'])->update('user_workouts');
			}
			echo 'Your workouts have been expired';
		} else {
			echo 'invalid';
		}
	}

/*
function landingnew()
{
$this->load->view('base/landingnew', $data);
} */

/*
function test_copy(){
$this->workouts->copy_workout(8422,2,date('Y-m-d'));
} */

/*
function renewuserworkout()
{
//if($this->workouts->check_api($this->parseRequestHeaders())){
//$workout = json_decode(file_get_contents('php://input'),true);
$workout['uw_id'] = 8422;
$workout['user_id'] = 2;
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
unset($workout_details->id);
$workout_details->workout_date = date('Y-m-d');
$this->db->insert('user_workouts',$workout_details);
$new_id = $this->db->insert_id();
$workout_sections = $this->db->where('workout_id',$workout['uw_id'])->get('user_workout_sections')->result_array();			
foreach($workout_sections as $section){
$old_section_id = $section['id'];
print_r($section);
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
//print_r($exercise);
//die('here');				
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
}*/
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */