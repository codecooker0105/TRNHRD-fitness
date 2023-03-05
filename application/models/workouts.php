<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Name:  Workouts Model
 *
 *
 *
 */

//  CI 2.0 Compatibility
if (!class_exists('CI_Model')) {
	class CI_Model extends Model
	{
	}
}


class Workouts extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('date');
		$this->load->library('session');
	}



	public function check_api($headers)
	{
		if (!isset($headers['mobile_api']) || $this->db->where('mobile_api', $headers['mobile_api'])->from('users')->count_all_results() == 0) {
			return false;
		} else {
			return true;
		}
	}

	public function get_progression_plans()
	{
		$this->db->select(
			array(
				'progression_plans.*'
			)
		);

		return $this->db->get('progression_plans');
	}

	public function progression_change_workouts($user_id)
	{
		$existing_workouts = $this->db->where('progression_plan_id IS NOT NULL')->where('user_id', $user_id)->where('workout_date >= CURDATE()')->where('completed', 'false')->get('user_workouts');
		foreach ($existing_workouts->result() as $workout) {
			$this->db->delete('user_workout_exercises', array('workout_id' => $workout->id));
			$this->db->delete('user_workout_sections', array('workout_id' => $workout->id));
			$this->db->delete('user_workouts', array('id' => $workout->id));
		}

		$user = $this->ion_auth->get_user($user_id);
		$weekdays = explode(',', $user->workoutdays);
		$days = array(1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday');

		foreach ($weekdays as $day) {
			$current_day = strtotime('next ' . $days[$day]);
			$end_date = strtotime('+ 90 days');
			while ($current_day < $end_date) {
				$workout_values = array('user_id' => $user_id, 'workout_date' => date("Y-m-d", $current_day), 'progression_plan_id' => $user->progression_plan_id);
				$this->db->insert('user_workouts', $workout_values);
				$current_day = strtotime(date("Y-m-d", $current_day) . " +1 week");
			}
		}

		$this->create_next_workout($user_id);
	}

	public function assign_available_exercises($user_id)
	{
		$user = $this->ion_auth->get_user($user_id);
		$available_exercises = $this->db->where('experience_id <=', $user->exp_level_id)->get('exercises');
		$this->db->delete('user_available_exercises', array('user_id' => $user->id));
		foreach ($available_exercises->result() as $exercise) {
			$input_values = array('user_id' => $user_id, 'exercise_id' => $exercise->id);
			$this->db->insert('user_available_exercises', $input_values);
		}
	}

	public function get_dashboard_stats($user_id)
	{
		$this->crud->use_table('user_stats');
		$stats = $this->crud->retrieve(array('user_id' => $this->data['user']->id), '', '', array('title' => 'desc'))->result();
		$return_stats = array();
		foreach ($stats as $stat) {
			$starting_result = mysql_query("SELECT * FROM user_stats_values WHERE stat_id = '" . $stat->id . "' AND date_taken = (SELECT MIN(date_taken) FROM user_stats_values WHERE stat_id = '" . $stat->id . "') LIMIT 1");
			if ($row = mysql_fetch_assoc($starting_result)) {
				$starting = $row['stat_value'];
			} else {
				$starting = 'na';
			}
			$current_result = mysql_query("SELECT * FROM user_stats_values WHERE stat_id = '" . $stat->id . "' AND date_taken = (SELECT MAX(date_taken) FROM user_stats_values WHERE stat_id = '" . $stat->id . "') LIMIT 1");
			if ($row = mysql_fetch_assoc($current_result)) {
				$current = $row['stat_value'];
			} else {
				$current = 'na';
			}
			$return_stats[] = array('id' => $stat->id, 'title' => $stat->title, 'measurement_type' => $stat->measurement_type, 'starting' => $starting, 'current' => $current);
		}

		return $return_stats;
	}

	public function get_exercise_counts($options = null)
	{
		$user_type = 'single';
		if (isset($options['user_id']) && is_numeric($options['user_id'])) {
			$user = $this->ion_auth->get_user($options['user_id']);
			$user_type = 'single';
		} elseif (isset($options['user_id']) && stristr($options['user_id'], 'group')) {
			$user_type = 'group';
			$id_array = explode('-', $options['user_id']);
			$this->crud->use_table('trainer_client_groups');
			$group = $this->crud->retrieve(array('id' => $id_array[1]))->row();
		}

		if ($user_type == 'single') {

			$user_session = $this->db->where('user_id', $options['user_id'])->where('progression_id', $options['progression_id'])->get('user_progressions')->row();
			if ($user_session) {
				$session_days = $this->db->where('progression_id', $options['progression_id'])->where('day <=', $user_session->session_count)->order_by('day', 'asc')->get('progression_sessions');
			} else {
				$insert_data = array('user_id' => $options['user_id'], 'progression_id' => $options['progression_id'], 'session_count' => '1');
				$this->db->insert('user_progressions', $insert_data);
				$session_days = $this->db->where('progression_id', $options['progression_id'])->where('day <=', '1')->order_by('day', 'asc')->get('progression_sessions');
			}
		}

		if (isset($options['exercise_id'])) {
			$this->crud->use_table('exercises');
			$exercise = $this->crud->retrieve(array('id' => $options['exercise_id']), 1)->row();
		}

		$weight_option = 'weighted';
		$progression = $this->db->where('id', $options['progression_id'])->get('progressions')->row();
		$sets = explode('|', $progression->default_sets);
		$reps = explode('|', $progression->default_reps);
		$time = explode('|', $progression->default_time);
		$rest = explode('|', $progression->default_rest);
		if (isset($options['weight_type'])) {
			$weight_option = $options['weight_type'];
		}

		if (in_array($options['section_type'], array('warmup', 'cool-down'))) {
			foreach ($sets as $set) {
				$weight[] = '';
			}
			$weight_option = 'bodyweight';
		} else {
			if (isset($options['exercise_id'])) {
				$last_exercise_date = $this->db->where('exercise_id', $options['exercise_id'])->where('user_id', $options['user_id'])->where('weight IS NOT NULL')->where('weight > 0')->where('progression_id', $options['progression_id'])->order_by('workout_date', 'desc')->limit(1)->get('user_workout_stats')->row();
				if ($last_exercise_date) {
					$exercise_stats = $this->db->select(array('AVG(user_workout_stats.weight) as avg_weight'))->where('exercise_id', $options['exercise_id'])->where('user_id', $options['user_id'])->where('weight IS NOT NULL')->where('weight > 0')->where('progression_id', $options['progression_id'])->where('workout_date', $last_exercise_date->workout_date)->order_by('set', 'asc')->group_by('set')->get('user_workout_stats')->result();
					foreach ($exercise_stats as $weight_stat) {
						if ($weight_stat->avg_weight % 5 == 0) {
							$weight[] = round($weight_stat->avg_weight);
						} else {
							$weight[] = round(($weight_stat->avg_weight + 5 / 2) / 5) * 5;
						}
					}
				} else {
					foreach ($sets as $set) {
						$weight[] = '';
					}
				}
			} else {
				foreach ($sets as $set) {
					$weight[] = '';
				}
			}
		}

		if ($user_type == 'single') {
			foreach ($session_days->result() as $day) {
				switch ($day->change_type) {
					case 'reps':
						foreach ($reps as $index => $set_reps) {
							$reps[$index] = $set_reps + ($day->change_amount);
						}
						break;
					case 'sets':
						$sets = $sets + ($day->change_amount);
						break;
					case 'time':
						foreach ($time as $index => $set_time) {
							$time[$index] = $set_time + ($day->change_amount);
						}
						break;
					case 'weight':
						foreach ($time as $index => $set_time) {
							if ($weight != 0 && $weight != 'bw') {
								$weight = $weight + ($day->change_amount);
							}
						}
						break;
					case 'next_exercise':
						$sets = $progression->default_sets;
						$reps = $progression->default_reps;
						$time = $progression->default_time;
						$rest = $progression->default_rest;
						add_new_exercise($options['user_id']);
						break;
				}
			}
		}

		return array('sets' => implode('|', $sets), 'reps' => implode('|', $reps), 'time' => implode('|', $time), 'rest' => implode('|', $rest), 'weight' => implode('|', $weight), 'weight_option' => $weight_option);
	}

	public function copy_workout($workout_id, $user_id, $workout_date)
	{
		$workout_array = $this->db->select(array('user_workouts.*', 'trainer_workouts.trainer_id', 'trainer_workouts.start_date', 'trainer_workouts.end_date'))
			->join('trainer_workouts', 'user_workouts.trainer_workout_id = trainer_workouts.id', 'left')
			->where('user_workouts.id', $workout_id)
			->get('user_workouts')->result_array();
		$return_workout = array();
		if (count($workout_array) == 1) {
			$workout = $workout_array[0];
			unset($workout['workout_id']);
			unset($workout['trainer_workout_id']);
			unset($workout['trainer_group_id']);
			unset($workout['start_date']);
			unset($workout['end_date']);
			$workout['workout_date'] = $workout_date;
			print_r($workout);
			die('here');
			$workout_sections = $this->db->select(array('user_workout_sections.*', 'skeleton_section_types.title'))->join('skeleton_section_types', 'skeleton_section_types.id = user_workout_sections.section_type_id')->where('workout_id', $workout->id)->order_by('display_order', 'asc')->get('user_workout_sections');
			foreach ($workout_sections->result() as $section) {
				$return_workout['sections'][$section->display_order] = array('title' => $section->title, 'section_rest' => $section->section_rest);
				$workout_exercises = $this->db->select(array('exercises.title', 'exercises.type', 'exercise_types.title as type_title', 'user_workout_exercises.*'))->join('exercise_types', 'exercise_types.id = user_workout_exercises.exercise_type_id')->join('exercises', 'exercises.id = user_workout_exercises.exercise_id')->where('workout_id', $workout->id)->where('workout_section_id', $section->id)->order_by('display_order', 'asc')->get('user_workout_exercises');
				foreach ($workout_exercises->result() as $exercise) {
					$return_workout['sections'][$section->display_order]['exercises'][$exercise->display_order] = array('type_title' => $exercise->type_title, 'id' => $exercise->exercise_id, 'uwe_id' => $exercise->id, 'title' => $exercise->title, 'sets' => $exercise->sets, 'reps' => $exercise->reps, 'time' => $exercise->time, 'rest' => $exercise->rest, 'weight' => $exercise->weight, 'set_type' => $exercise->set_type, 'weight_option' => $exercise->weight_option);
				}
			}
			return $return_workout;
		} else {
			return false;
		}
	}

	public function create_next_workout($user_id, $workout_date = false)
	{
		$user = $this->ion_auth->get_user($user_id);
		$prev_workout = $this->db->where('progression_plan_id', $user->progression_plan_id)
			->where('user_id', $user_id)
			->where('workout_date <= CURDATE()')
			->where('completed', 'true')
			->order_by('workout_date', 'desc')
			->limit(1)
			->get('user_workouts')->row();

		if ($prev_workout) { //There is a previous workout to go from
			$next_workout = $this->db->where('progression_plan_id', $user->progression_plan_id)
				->where('user_id', $user_id)
				->where('completed', 'false')
				->where('workout_date >=', $prev_workout->workout_date)
				->order_by('workout_date', 'asc')
				->limit(1)
				->get('user_workouts')->row();
		} else { //no previous workouts in this progresion plan
			$next_workout = $this->db->where('progression_plan_id', $user->progression_plan_id)
				->where('user_id', $user_id)
				->where('completed', 'false')
				->where('workout_date >= CURDATE()')
				->order_by('workout_date', 'asc')
				->limit(1)
				->get('user_workouts')->row();
		}

		if ($next_workout) {
			if ($next_workout->workout_created == 'false') {
				$progression = $this->db->select(array('progressions.*'))
					->join('progressions', 'progressions.id = progression_plan_days.progression_id')
					->where('day', $user->progression_plan_day)
					->where('plan_id', $user->progression_plan_id)
					->limit(1)
					->get('progression_plan_days')->row();
				$workout_data = array('progression_id' => $progression->id);
				$this->db->where('id', $next_workout->id)->update('user_workouts', $workout_data);

				$hybrid_workout = $this->db->select(array('skeleton_workouts.*'))->join('skeleton_focus', 'skeleton_focus.skeleton_id = skeleton_workouts.id')->where('skeleton_focus.progression_id', $progression->id)->order_by('skeleton_workouts.id', "random")->limit(1)->get('skeleton_workouts')->row();
				$workout_title = $hybrid_workout->title;

				$hybrid_workout_sections = $this->db->select(array('skeleton_section_types.title', 'skeleton_section_types.type', 'skeleton_section.*'))->join('skeleton_section_types', 'skeleton_section_types.id = skeleton_section.section_type_id')->where('skeleton_id', $hybrid_workout->id)->order_by('display_order')->get('skeleton_section');
				$section_count = 1;
				$this->db->delete('user_workout_sections', array('workout_id' => $next_workout->id));
				$this->db->delete('user_workout_exercises', array('workout_id' => $next_workout->id));
				foreach ($hybrid_workout_sections->result() as $section) {
					$section_data = array('workout_id' => $next_workout->id, 'section_type_id' => $section->section_type_id, 'display_order' => $section_count++);
					$this->db->insert('user_workout_sections', $section_data);
					$section_id = $this->db->insert_id();
					$hybrid_workout_exercise_types = $this->db->select(array('exercise_types.title', 'skeleton_category.*'))->join('exercise_types', 'exercise_types.id = skeleton_category.exercise_type_id')->where('section_id', $section->id)->order_by('display_order')->get('skeleton_category');
					$exercise_count = 1;
					foreach ($hybrid_workout_exercise_types->result() as $category) {
						$exercise = $this->get_random_exercise(array('user_id' => $user_id, 'available_equipment' => $user->available_equipment, 'exercise_type' => $category->exercise_type_id));
						//print_r($exercise);
						if ($exercise) {
							$exercise_stats = $this->get_exercise_counts(array('user_id' => $user_id, 'progression_id' => $progression->id, 'exercise_id' => $exercise->id, 'weight_type' => $exercise->weight_type, 'section_type' => $section->type));
							$exercise_data = array(
								'sets' => $exercise_stats['sets'],
								'reps' => $exercise_stats['reps'],
								'time' => $exercise_stats['time'],
								'rest' => $exercise_stats['rest'],
								'weight' => '' . $exercise_stats['weight'],
								'weight_option' => '' . $exercise_stats['weight_option'],
								'workout_id' => $next_workout->id,
								'exercise_id' => $exercise->id,
								'workout_section_id' => $section_id,
								'exercise_type_id' => $category->exercise_type_id,
								'display_order' => $exercise_count++
							);
						} else {
							$exercise_stats = $this->get_exercise_counts(array('user_id' => $user_id, 'progression_id' => $progression->id, 'section_type' => $section->type));
							$exercise_data = array(
								'sets' => $exercise_stats['sets'],
								'reps' => $exercise_stats['reps'],
								'time' => $exercise_stats['time'],
								'rest' => $exercise_stats['rest'],
								'weight' => '' . $exercise_stats['weight'],
								'weight_option' => '' . $exercise_stats['weight_option'],
								'workout_id' => $next_workout->id,
								'exercise_id' => 'NULL',
								'workout_section_id' => $section_id,
								'exercise_type_id' => $category->exercise_type_id,
								'display_order' => $exercise_count++
							);
						}
						$this->db->insert('user_workout_exercises', $exercise_data);
					}
				}
				$this->db->where('id', $next_workout->id)
					->where('user_id', $user_id)
					->update('user_workouts', array('workout_created' => 'true', 'title' => $workout_title));
				return $next_workout->id;
			}
		} else {
			return false;
		}

		//die('end');
	}

	public function get_last_workout($user_id = '')
	{
		$workout = $this->db->where('user_id', $user_id)
			->where('workout_date <', date('Y-m-d'))
			->order_by("workout_date", "desc")
			->limit(1)
			->get('user_workouts')->row();
		if (empty($workout)) {
			return FALSE;
		} else {
			return $workout;
		}
	}

	public function get_workout_tree($user_id = '', $date = '')
	{
		$workout = $this->db->where('workout_date', $date)->where('user_id', $user_id)->get('user_workouts')->row();
		$return_workout = array();
		if (count($workout) == 1) {
			$return_workout['title'] = $workout->title;
			$return_workout['created'] = $workout->workout_created;
			$workout_sections = $this->db->select(array('user_workout_sections.*', 'skeleton_section_types.title'))->join('skeleton_section_types', 'skeleton_section_types.id = user_workout_sections.section_type_id')->where('workout_id', $workout->id)->order_by('display_order', 'asc')->get('user_workout_sections');
			foreach ($workout_sections->result() as $section) {
				$return_workout['sections'][$section->display_order] = array('title' => $section->title);
				$workout_exercises = $this->db->select(array('exercises.*', 'exercise_types.title as type_title', 'user_workout_exercises.*'))->join('exercise_types', 'exercise_types.id = user_workout_exercises.exercise_type_id')->join('exercises', 'exercises.id = user_workout_exercises.exercise_id')->where('workout_id', $workout->id)->where('workout_section_id', $section->id)->order_by('display_order', 'asc')->get('user_workout_exercises');
				foreach ($workout_exercises->result() as $exercise) {
					$return_workout['sections'][$section->display_order]['exercises'][$exercise->display_order] = array('type_title' => $exercise->type_title, 'id' => $exercise->exercise_id, 'title' => $exercise->title);
				}
			}
			return $return_workout;
		} else {
			return false;
		}
	}

	public function get_logbook_workout($user_id = '', $date = '', $workout_id = '')
	{
		if ($workout_id != '') {
			$workout = $this->db->select(array('user_workouts.*', 'trainer_workouts.trainer_id', 'trainer_workouts.start_date', 'trainer_workouts.end_date'))->join('trainer_workouts', 'user_workouts.trainer_workout_id = trainer_workouts.id', 'left')->where('user_workouts.id', $workout_id)->where('user_workouts.user_id', $user_id)->get('user_workouts')->row();
		} else {
			$workout = $this->db->select(array('user_workouts.*', 'trainer_workouts.trainer_id', 'trainer_workouts.start_date', 'trainer_workouts.end_date'))->join('trainer_workouts', 'user_workouts.trainer_workout_id = trainer_workouts.id', 'left')->where('user_workouts.workout_date', $date)->where('user_workouts.user_id', $user_id)->get('user_workouts')->row();
		}
		$return_workout = array();
		if (count($workout) == 1) {
			$return_workout['title'] = $workout->title;
			$return_workout['workout_id'] = $workout->id;
			$return_workout['user_id'] = $workout->user_id;
			$return_workout['trainer_workout_id'] = $workout->trainer_workout_id;
			$return_workout['trainer_group_id'] = $workout->trainer_group_id;
			if (isset($workout->trainer_id)) {
				$return_workout['trainer_id'] = $workout->trainer_id;
			} else {
				$return_workout['trainer_id'] = '';
			}
			$return_workout['workout_date'] = $workout->workout_date;
			$return_workout['start_date'] = $workout->start_date;
			$return_workout['end_date'] = $workout->end_date;
			$return_workout['completed'] = $workout->completed;
			$return_workout['created'] = $workout->workout_created;
			$workout_sections = $this->db->select(array('user_workout_sections.*', 'skeleton_section_types.title'))->join('skeleton_section_types', 'skeleton_section_types.id = user_workout_sections.section_type_id')->where('workout_id', $workout->id)->order_by('display_order', 'asc')->get('user_workout_sections');
			foreach ($workout_sections->result() as $section) {
				$return_workout['sections'][$section->display_order] = array('title' => $section->title, 'section_rest' => $section->section_rest);
				$workout_exercises = $this->db->select(array('exercises.title', 'exercises.video', 'exercises.mobile_video', 'exercises.type', 'exercise_types.title as type_title', 'user_workout_exercises.*'))->join('exercise_types', 'exercise_types.id = user_workout_exercises.exercise_type_id')->join('exercises', 'exercises.id = user_workout_exercises.exercise_id')->where('workout_id', $workout->id)->where('workout_section_id', $section->id)->order_by('display_order', 'asc')->get('user_workout_exercises');
				foreach ($workout_exercises->result() as $exercise) {
					$return_workout['sections'][$section->display_order]['exercises'][$exercise->display_order] = array('type_title' => $exercise->type_title, 'id' => $exercise->exercise_id, 'uwe_id' => $exercise->id, 'title' => $exercise->title, 'video' => $exercise->video, 'mobile_video' => $exercise->mobile_video, 'sets' => $exercise->sets, 'reps' => $exercise->reps, 'time' => $exercise->time, 'rest' => $exercise->rest, 'weight' => $exercise->weight, 'set_type' => $exercise->set_type, 'weight_option' => $exercise->weight_option);
				}
			}
			return $return_workout;
		} else {
			return false;
		}
	}

	public function get_workout($workout_id = '')
	{
		$workout = $this->db->select(array('user_workouts.*', 'trainer_workouts.trainer_id', 'trainer_workouts.start_date', 'trainer_workouts.end_date'))
			->join('trainer_workouts', 'user_workouts.trainer_workout_id = trainer_workouts.id', 'left')
			->where('user_workouts.id', $workout_id)
			->get('user_workouts')->row();
		$return_workout = array();
		if (count($workout) == 1) {
			$return_workout['title'] = $workout->title;
			$return_workout['workout_id'] = $workout->id;
			$return_workout['user_id'] = $workout->user_id;
			$return_workout['trainer_workout_id'] = $workout->trainer_workout_id;
			$return_workout['trainer_group_id'] = $workout->trainer_group_id;
			if (isset($workout->trainer_id)) {
				$return_workout['trainer_id'] = $workout->trainer_id;
			} else {
				$return_workout['trainer_id'] = '';
			}
			$return_workout['workout_date'] = $workout->workout_date;
			$return_workout['start_date'] = $workout->start_date;
			$return_workout['end_date'] = $workout->end_date;
			$return_workout['completed'] = $workout->completed;
			$return_workout['created'] = $workout->workout_created;
			$workout_sections = $this->db->select(array('user_workout_sections.*', 'skeleton_section_types.title'))->join('skeleton_section_types', 'skeleton_section_types.id = user_workout_sections.section_type_id')->where('workout_id', $workout->id)->order_by('display_order', 'asc')->get('user_workout_sections');
			foreach ($workout_sections->result() as $section) {
				$return_workout['sections'][$section->display_order] = array('title' => $section->title, 'section_rest' => $section->section_rest);
				$workout_exercises = $this->db->select(array('exercises.title', 'exercises.type', 'exercise_types.title as type_title', 'user_workout_exercises.*'))->join('exercise_types', 'exercise_types.id = user_workout_exercises.exercise_type_id')->join('exercises', 'exercises.id = user_workout_exercises.exercise_id')->where('workout_id', $workout->id)->where('workout_section_id', $section->id)->order_by('display_order', 'asc')->get('user_workout_exercises');
				foreach ($workout_exercises->result() as $exercise) {
					$return_workout['sections'][$section->display_order]['exercises'][$exercise->display_order] = array('type_title' => $exercise->type_title, 'id' => $exercise->exercise_id, 'uwe_id' => $exercise->id, 'title' => $exercise->title, 'sets' => $exercise->sets, 'reps' => $exercise->reps, 'time' => $exercise->time, 'rest' => $exercise->rest, 'weight' => $exercise->weight, 'set_type' => $exercise->set_type, 'weight_option' => $exercise->weight_option);
				}
			}
			return $return_workout;
		} else {
			return false;
		}
	}

	public function get_user_workout($user_id = '', $expand_circuit = false)
	{
		$workout = $this->db->select(array('user_workouts.*', 'trainer_workouts.trainer_id', 'trainer_workouts.start_date', 'trainer_workouts.end_date'))
			->join('trainer_workouts', 'user_workouts.trainer_workout_id = trainer_workouts.id', 'left')
			->where('user_workouts.completed', 'false')
			->where('user_workouts.workout_created', 'true')
			->where('user_workouts.user_id', $user_id)
			->where('user_workouts.workout_date >= CURDATE() - INTERVAL 3 DAY')
			->order_by('workout_date', 'asc')->limit(1)
			->get('user_workouts')->row();
		$return_workout = array();
		if (count($workout) == 1) {
			$return_workout['title'] = $workout->title;
			$return_workout['workout_id'] = $workout->id;
			$return_workout['user_id'] = $workout->user_id;
			$return_workout['trainer_workout_id'] = $workout->trainer_workout_id;
			$return_workout['trainer_group_id'] = $workout->trainer_group_id;
			if (isset($workout->trainer_id)) {
				$return_workout['trainer_id'] = $workout->trainer_id;
			} else {
				$return_workout['trainer_id'] = '';
			}
			$return_workout['workout_date'] = $workout->workout_date;
			$return_workout['start_date'] = $workout->start_date;
			$return_workout['end_date'] = $workout->end_date;
			$return_workout['completed'] = $workout->completed;
			$return_workout['created'] = $workout->workout_created;
			$workout_sections = $this->db->select(array('user_workout_sections.*', 'skeleton_section_types.title'))
				->join('skeleton_section_types', 'skeleton_section_types.id = user_workout_sections.section_type_id')
				->where('workout_id', $workout->id)
				->order_by('display_order', 'asc')
				->get('user_workout_sections');
			foreach ($workout_sections->result() as $section) {
				$return_workout['sections'][$section->display_order] = array('title' => $section->title, 'section_rest' => $section->section_rest, 'section_type_id' => $section->section_type_id, 'display_order' => $section->display_order);
				$workout_exercises = $this->db->select(array('exercises.title', 'exercises.type', 'exercises.mobile_video', 'exercise_types.title as type_title', 'user_workout_exercises.*'))
					->join('exercise_types', 'exercise_types.id = user_workout_exercises.exercise_type_id')
					->join('exercises', 'exercises.id = user_workout_exercises.exercise_id')
					->where('workout_id', $workout->id)->where('workout_section_id', $section->id)
					->order_by('display_order', 'asc')
					->get('user_workout_exercises');
				if ($expand_circuit === true && $section->section_type_id == 2) {
					$end_circuit = false;
					$set_index = 0;
					while (!$end_circuit) {
						foreach ($workout_exercises->result() as $exercise) {
							$ex_sets = explode('|', $exercise->sets);
							$ex_reps = explode('|', $exercise->reps);
							$ex_time = explode('|', $exercise->time);
							$ex_rest = explode('|', $exercise->rest);
							$ex_weight = explode('|', $exercise->weight);
							if (isset($ex_sets[$set_index])) {
								$return_workout['sections'][$section->display_order]['exercises'][] = array(
									'type_title' => $exercise->type_title,
									'display_order' => $exercise->display_order,
									'id' => $exercise->exercise_id,
									'mobile_video' => $exercise->mobile_video,
									'uwe_id' => $exercise->id,
									'title' => $exercise->title,
									'sets' => $ex_sets[$set_index],
									'reps' => $ex_reps[$set_index],
									'time' => $ex_time[$set_index],
									'rest' => $ex_rest[$set_index],
									'weight' => $ex_weight[$set_index],
									'set_type' => $exercise->set_type,
									'weight_option' => $exercise->weight_option
								);
							} else {
								$end_circuit = true;
							}
						}
						$set_index++;
						if ($set_index > 10) {
							print_r($return_workout);
							die('here');
						}
					}
				} else {
					foreach ($workout_exercises->result() as $exercise) {
						$return_workout['sections'][$section->display_order]['exercises'][] = array(
							'type_title' => $exercise->type_title,
							'display_order' => $exercise->display_order,
							'id' => $exercise->exercise_id,
							'mobile_video' => $exercise->mobile_video,
							'uwe_id' => $exercise->id,
							'title' => $exercise->title,
							'sets' => $exercise->sets,
							'reps' => $exercise->reps,
							'time' => $exercise->time,
							'rest' => $exercise->rest,
							'weight' => $exercise->weight,
							'set_type' => $exercise->set_type,
							'weight_option' => $exercise->weight_option
						);
					}
				}
			}
			return $return_workout;
		} else {
			return false;
		}
	}

	public function get_workout_details($workout_id = '')
	{
		$workout = $this->db->select(array('user_workouts.*'))
			->where('user_workouts.id', $workout_id)
			->get('user_workouts')->row();
		if (count($workout) == 1) {
			return $workout;
		} else {
			return false;
		}
	}

	public function get_workout_exercise($ex_id = '')
	{
		$exercise = $this->db->select(array('exercises.title', 'exercises.type', 'exercise_types.title as type_title', 'user_workout_exercises.*'))
			->join('exercise_types', 'exercise_types.id = user_workout_exercises.exercise_type_id')
			->join('exercises', 'exercises.id = user_workout_exercises.exercise_id')
			->where('user_workout_exercises.id', $ex_id)
			->get('user_workout_exercises')->row();
		if (count($exercise) == 1) {
			$return_data = array(
				'ExerciseId' => $exercise->exercise_id,
				'ExDisplayOrder' => $exercise->display_order,
				'Sets' => $exercise->sets,
				'Reps' => $exercise->reps,
				'Weight' => $exercise->weight,
				'Rest' => $exercise->rest,
				'SetType' => $exercise->set_type,
				'ExerciseType' => $exercise->type,
				'Time' => $exercise->time,
				'WeightOption' => $exercise->weight_option
			);
			return $return_data;
		} else {
			return false;
		}
	}

	public function get_random_exercise($options)
	{

		$user_type = 'single';
		if (isset($options['user_id']) && is_numeric($options['user_id'])) {
			$user = $this->ion_auth->get_user($options['user_id']);
			$user_type = 'single';
		} elseif (isset($options['user_id']) && stristr($options['user_id'], 'group')) {
			$user_type = 'group';
			$id_array = explode('-', $options['user_id']);
			$this->crud->use_table('trainer_client_groups');
			$group = $this->crud->retrieve(array('id' => $id_array[1]))->row();
		}

		$this->db->select(array('exercises.*'));
		if (isset($options['user_id']) && $user_type == 'single') {
			$this->db->where("id IN (SELECT exercise_id FROM user_available_exercises WHERE user_id = '" . $options['user_id'] . "')");
		} elseif (isset($options['user_id']) && $user_type == 'group') {
			$this->db->where("experience_id", $group->exp_level_id);
		}

		if (isset($options['available_equipment']) && $options['available_equipment'] != '' && is_array($options['available_equipment'])) {
			$this->db->where("(id NOT IN(SELECT exercise_id FROM exercise_equipment GROUP BY exercise_id) OR id IN (SELECT exercise_id FROM exercise_equipment WHERE equipment_id IN (" . implode(',', $options['available_equipment']) . ")))");
		} elseif (isset($options['available_equipment']) && $options['available_equipment'] != '' && $options['available_equipment'] != 'none') {
			$this->db->where("(id NOT IN(SELECT exercise_id FROM exercise_equipment GROUP BY exercise_id) OR id IN (SELECT exercise_id FROM exercise_equipment WHERE equipment_id IN (" . $options['available_equipment'] . ")))");
		} elseif (isset($options['available_equipment']) && $options['available_equipment'] == 'none') {
			$this->db->where("id NOT IN(SELECT exercise_id FROM exercise_equipment GROUP BY exercise_id)");
		}

		if (isset($options['exercise_type'])) {
			$this->db->where("id IN (SELECT exercise_id FROM exercise_link_types WHERE type_id = '" . $options['exercise_type'] . "')");
		}

		return $this->db->order_by('title', 'random')->limit(1)->get('exercises')->row();
	}

	public function get_exercises($options)
	{


		if (isset($options['user_id'])) {
			$user = $this->ion_auth->get_user($options['user_id']);
		}

		$this->db->select(array('exercises.*'));
		if (isset($options['user_id'])) {
			$this->db->where("id IN (SELECT exercise_id FROM user_available_exercises WHERE user_id = '" . $options['user_id'] . "')");
		}

		if (isset($options['available_equipment'])) {
			$this->db->where("(id NOT IN(SELECT exercise_id FROM exercise_equipment GROUP BY exercise_id) OR id IN (SELECT exercise_id FROM exercise_equipment WHERE equipment_id IN (" . $options['available_equipment'] . ")))");
		}

		if (isset($options['exercise_type'])) {
			$this->db->where("id IN (SELECT exercise_id FROM exercise_link_types WHERE type_id = '" . $options['exercise_type'] . "')");
		}

		if (isset($options['muscle'])) {
			$this->db->where("id IN (SELECT exercise_id FROM exercise_muscles WHERE muscle_id = '" . $options['muscle'] . "')");
		}

		if (isset($options['experience_level'])) {
			$this->db->where("experience_id", $options['experience_level']);
		}

		return $this->db->order_by('title', 'asc')->get('exercises');
	}

	public function get_random_workout($options = '')
	{

		$this->db->select(array('skeleton_workouts.*'));
		return $this->db->order_by('title', 'random')->limit(1)->get('skeleton_workouts')->row();
	}

	public function get_monthly_workouts($month, $year, $user_id)
	{
		return $this->db->select(array('user_workouts.*', 'users.*', 'meta.*', 'progressions.title as pro_title'))->join('progressions', 'progressions.id = user_workouts.progression_id', 'left')->join('trainer_workouts', 'trainer_workouts.id = user_workouts.trainer_workout_id', 'left')->join('users', 'trainer_workouts.trainer_id = users.id', 'left')->join('meta', 'meta.user_id = users.id', 'left')->where('user_workouts.user_id', $user_id)->where('workout_date >=', date('Y-m-d', mktime(0, 0, 0, $month, 1, $year)))->where('workout_date <=', date('Y-m-d', strtotime('-1 second', strtotime('+1 month', strtotime($month . '/01/' . $year)))))->order_by("workout_date", "desc")->get('user_workouts');
	}

	public function get_past_workouts($user_id)
	{
		return $this->db->select(array('user_workouts.*'))->where('user_id', $user_id)->where('workout_date <', date('Y-m-d'))->order_by("workout_date", "desc")->get('user_workouts')->result();
	}

	public function get_upcoming_created_workouts($user_id)
	{
		return $this->db->select(array('user_workouts.*'))->where('user_id', $user_id)->where('workout_date >=', date('Y-m-d'))->where('completed', 'false')->where('workout_created', 'true')->order_by("workout_date", "asc")->get('user_workouts')->result();
	}

	public function get_upcoming_workouts($user_id)
	{
		return $this->db->select(array('user_workouts.*'))->where('user_id', $user_id)->where('workout_date >=', date('Y-m-d'))->where('completed', 'false')->order_by("workout_date", "asc")->get('user_workouts')->result();
	}

	public function get_exercise_library()
	{
		$experience_levels = $this->db->order_by('id', 'asc')->get('experience_level');
		$muscles = $this->db->order_by('title', 'asc')->get('muscles');
		$muscle_count = 0;
		foreach ($muscles->result() as $muscle) {
			$library['muscles'][$muscle_count] = array('title' => $muscle->title, 'no_exercises' => true);
			$level_count = 0;
			foreach ($experience_levels->result() as $level) {
				$library['muscles'][$muscle_count]['levels'][$level_count] = array('title' => $level->title, 'no_exercises' => true);
				$exercises = $this->get_exercises(array('muscle' => $muscle->id, 'experience_level' => $level->id));
				$exercise_count = 0;
				if ($exercises->num_rows() == 0) {
				} else {
					foreach ($exercises->result() as $exercise) {
						$library['muscles'][$muscle_count]['no_exercises'] = false;
						$library['muscles'][$muscle_count]['levels'][$level_count]['no_exercises'] = false;
						$library['muscles'][$muscle_count]['levels'][$level_count]['exercises'][$exercise_count] = array('title' => $exercise->title, 'id' => $exercise->id);
						$exercise_count++;
					}
				}
				$level_count++;
			}
			$muscle_count++;
		}
		return $library;
	}

	
	public function get_exercise($ex_id){
		$this->crud->use_table('exercises');
		$exercise = $this->crud->retrieve(array('id' => $ex_id))->row();
		if(count($exercise) == 1){
			return $exercise;
		}else{
			return false;
		}
	}


	public function get_exercise_library_bu(){
		$experience_levels = $this->db->order_by('id','asc')->get('experience_level');
		$exercise_types = $this->db->order_by('title','asc')->get('exercise_types');
		$type_count = 0;
		foreach($exercise_types->result() as $type){
			$library['types'][$type_count] = array('title' => $type->title,'no_exercises' => true);
			$level_count = 0;
			foreach($experience_levels->result() as $level){
				$library['types'][$type_count]['levels'][$level_count] = array('title' => $level->title,'no_exercises' => true);
				$exercises = $this->get_exercises(array('exercise_type' => $type->id,'experience_level' => $level->id));
				$exercise_count = 0;
				if($exercises->num_rows() == 0){

				}else{
					foreach($exercises->result() as $exercise){
						$library['types'][$type_count]['no_exercises'] = false;
						$library['types'][$type_count]['levels'][$level_count]['no_exercises'] = false;
						$library['types'][$type_count]['levels'][$level_count]['exercises'][$exercise_count] = array('title' => $exercise->title,'id' => $exercise->id);
						$exercise_count++;
					}
				}
				$level_count++;
			}
			$type_count++;
		}
		return $library;
	}

	function get_stats_chart($user_id){
		if($_POST['interval'] == 'daily'){
			$this->crud->use_table('user_stats');
			$stats = $this->crud->retrieve(array('user_id' => $user_id),'','',array('title' => 'asc'))->result_array();
			foreach($stats as $stat){
				$this->db->select('AVG(stat_value) as average, DATE_FORMAT(date_taken,\'%W, %M %e, %Y\') as day', FALSE);
				$this->db->from('user_stats_values');
				$this->db->join('user_stats', 'user_stats.id = user_stats_values.stat_id');
				$this->db->where('stat_id',$stat['id']);
				$this->db->group_by(array("year(date_taken)", "week(date_taken)", "day(date_taken)"));
				$return_stats[] = $this->db->get()->result_array();
			}
		}elseif($_POST['interval'] == 'weekly'){
			$this->crud->use_table('user_stats');
			$stats = $this->crud->retrieve(array('user_id' => $user_id),'','',array('title' => 'asc'))->result_array();
			foreach($stats as $stat){
				$this->db->select('title, AVG(stat_value) as average, DATE_FORMAT(DATE_ADD(date_taken, INTERVAL(1-DAYOFWEEK(date_taken)) DAY),\'%W, %M %e, %Y\') as week_start', FALSE);
				$this->db->from('user_stats_values');
				$this->db->join('user_stats', 'user_stats.id = user_stats_values.stat_id');
				$this->db->where('stat_id',$stat['id']);
				$this->db->group_by(array("year(date_taken)", "week(date_taken)"));
				$return_stats[] = $this->db->get()->result_array();
			}
		}elseif($_POST['interval'] == 'monthly'){
			$this->crud->use_table('user_stats');
			$stats = $this->crud->retrieve(array('user_id' => $user_id),'','',array('title' => 'asc'))->result_array();
			foreach($stats as $stat){
				$this->db->select('title, AVG(stat_value) as average, DATE_FORMAT(DATE_ADD(date_taken, INTERVAL(1-DAYOFMONTH(date_taken)) DAY),\'%W, %M %e, %Y\') as month_start', FALSE);
				$this->db->from('user_stats_values');
				$this->db->join('user_stats', 'user_stats.id = user_stats_values.stat_id');
				$this->db->where('stat_id',$stat['id']);
				$this->db->group_by(array("year(date_taken)", "month(date_taken)"));
				$return_stats[] = $this->db->get()->result_array();
			}
		}

		//print_r($profits);
		echo json_encode($return_stats);
	}
}