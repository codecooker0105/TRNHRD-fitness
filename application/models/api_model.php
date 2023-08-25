<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Api_model extends CI_Model
{

    public $state = array(
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
    public $workoutdays = array(1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday');

    function __construct()
    {
        // Call the parent Model
        parent::__construct();

        // Load the Database Module REQUIRED for this to work.
        $this->load->database();
    }

    function validate($mandatory_fields = [], $post = [])
    {
        if (sizeof($mandatory_fields)) {
            foreach ($mandatory_fields as $mandatory_field) {
                if (!isset($post[$mandatory_field]) || empty($post[$mandatory_field])) {
                    $response['status'] = 0;
                    $response['message'] = str_replace('_', ' ', $mandatory_field) . ' is required';
                    $this->wd_result($response);
                }
            }
        } else {
            return true;
        }
    }

    public function wd_result($options = [])
    {
        echo json_encode($options);
        exit;
    }

    public function user_detail_by_email($email)
    {
        $this->db->select('meta.*,groups.name as group_name, groups.description as group_description,users.group_id,users.username,users.email');
        $this->db->from('users');
        $this->db->join('meta', 'users.id = meta.user_id');
        $this->db->join('groups', 'users.group_id = groups.id');
        $this->db->where(['users.email' => $email]);
        $user = $this->db->get()->row();
        return $user;
    }

    public function user_table_detail_through_email($email)
    {
        $user = $this->db->get_where('users', ['users.email' => $email])->row_array();
        return $user;
    }

    public function user_table_detail_through_username($username)
    {
        $user = $this->db->get_where('users', ['users.username' => $username])->row_array();
        return $user;
    }

    public function updateDeviceField($user_id, $update)
    {
        $this->db->where('id', $user_id);
        $this->db->update('users', $update);
    }

    public function user_detail_by_user_id($user_id)
    {
        $this->db->select('users.id,users.group_id,users.username,users.email,users.active,users.device_type,users.device_token,groups.name as group_name, groups.description as group_description,meta.first_name,meta.last_name,meta.phone_number,meta.city,meta.state,meta.zip,meta.photo,meta.progression_plan_id,meta.progression_plan_day,meta.exp_level_id,meta.available_equipment,meta.workoutdays,meta.tos_agreement');
        $this->db->from('users');
        $this->db->join('meta', 'users.id = meta.user_id');
        $this->db->join('groups', 'users.group_id = groups.id');
        $this->db->where(['users.id' => $user_id]);
        $user = $this->db->get()->row();
        if ($user) {
            $user->state_name = "";
            if (isset($user->state) && !empty($user->state)) {
                if (isset($this->state[$user->state])) {
                    $user->state_name = $this->state[$user->state];
                }
            }
            if (isset($user->exp_level_id)) {

                if (!empty($user->exp_level_id)) {
                    $user->exp_level_name = $this->exp_level_name_from_id($user->exp_level_id);
                }
            }
            if (isset($user->available_equipment)) {
                $user->available_equipment_name = "";
                if (!empty($user->available_equipment)) {
                    $user->available_equipment_name = $this->available_equipment_array_from_id($user->available_equipment);
                }
            }
            if (isset($user->progression_plan_id)) {
                $user->progression_plan_name = "";
                if (!empty($user->progression_plan_id)) {
                    $user->progression_plan_name = $this->progression_plan_array_from_id($user->progression_plan_id);
                }
            }
            if (isset($user->workoutdays)) {
                $user->workoutdays_name = "";
                if (!empty($user->workoutdays)) {
                    $user->workoutdays_name = $this->workout_array_from_id($user->workoutdays);
                }
            }
        }
        return $user;
    }

    public function basic_user_detail_by_user_id($user_id)
    {
        $this->db->select('users.id,users.group_id,users.username,users.email,users.active,users.device_type,users.device_token,groups.name as group_name, groups.description as group_description,meta.first_name,meta.last_name,meta.city,meta.state,meta.zip,meta.photo,meta.progression_plan_id,meta.progression_plan_day,meta.exp_level_id,meta.available_equipment,meta.workoutdays,meta.tos_agreement');
        $this->db->from('users');
        $this->db->join('meta', 'users.id = meta.user_id');
        $this->db->join('groups', 'users.group_id = groups.id');
        $this->db->where(['users.id' => $user_id]);
        return $this->db->get()->row();
    }

    public function generate_otp($user_id)
    {
        $userOTP = $this->db->get_where('users', ['id' => $user_id])->row();
        if (!empty($userOTP->forgot_password_otp)) {
            $random = $userOTP->forgot_password_otp;
        } else {
            $random = mt_rand(1000, 9999);
            $record = $this->db->get_where('users', ['forgot_password_otp' => $random])->row();
            if ($record) {
                $this->generate_otp($user_id);
            } else {
                $this->db->where('id', $user_id);
                $this->db->update('users', array('forgot_password_otp' => $random));
            }
        }
        return $random;
    }

    public function update_password($user_id, $password)
    {
        return $this->db->update('users', array('forgot_password_otp' => '', 'password' => $password), ['id' => $user_id]);
    }

    function exp_level_name_from_id($id)
    {
        $data = $this->db->get_where('experience_level', ['id' => $id])->row();
        return $data;
    }

    function available_equipment_array_from_id($id)
    {
        $this->db->where_in('id', explode(',', $id));
        $data = $this->db->get('equipment')->result();
        return $data;
    }

    function progression_plan_array_from_id($id)
    {
        $data = $this->db->get_where('progression_plans', ['id' => $id])->row();
        return $data;
    }

    function workout_array_from_id($id)
    {
        $count = 0;
        foreach (explode(',', $id) as $key => $value) {
            $workoutdaysObject[$count] = new stdClass();
            $workoutdaysObject[$count]->id = $value;
            $workoutdaysObject[$count]->value = $this->workoutdays[$value];
            $count++;
        }
        return $workoutdaysObject;
    }

    function validate_otp($otp)
    {
        return $this->db->get_where('users', ['forgot_password_otp' => $otp])->row();
    }

    function get_clients($trainer_id)
    {
        $this->db->select('trainer_clients.id,users.email,users.device_token,trainer_clients.status,meta.first_name,meta.last_name,meta.photo,meta.user_id,meta.available_equipment');
        $this->db->from('users');
        $this->db->join('trainer_clients', 'users.email = trainer_clients.email');
        $this->db->join('meta', 'users.id = meta.user_id');
        $this->db->where(['trainer_clients.trainer_id' => $trainer_id, 'trainer_clients.status !=' => 'denied']);
        $client = $this->db->get()->result();
        return $client;
    }

    function get_all_clients($trainer_id, $page, $limit)
    {
        $query = 'SELECT `users`.`id` As user_id, `users`.`username`, `users`.`email`, IFNULL(`trainer_clients`.`status`, NULL) AS status, `meta`.`first_name`, `meta`.`last_name`, `meta`.`phone_number`, `meta`.`photo`, `meta`.`available_equipment` FROM (`users`) LEFT JOIN `trainer_clients` ON `users`.`id` = `trainer_clients`.`client_id` AND `trainer_clients`.`trainer_id` = ' . $trainer_id . ' JOIN `meta` ON `users`.`id` = `meta`.`user_id` WHERE `users`.`group_id` = 2 ORDER BY `users`.`created_on` desc LIMIT ' . $limit . ' OFFSET ' . ($page-1) * $limit;
        $result = $this->db->query($query)->result();
        return $result;
    }

    function get_trainers($member_email)
    {
        $this->db->select('trainer_clients.id,users.email,trainer_clients.status,meta.first_name,meta.last_name,meta.photo,meta.user_id');
        $this->db->from('users');
        $this->db->join('trainer_clients', 'users.id = trainer_clients.trainer_id');
        $this->db->join('meta', 'users.id = meta.user_id');
        $this->db->where(['trainer_clients.email' => $member_email, 'trainer_clients.status !=' => 'denied']);
        $trainers = $this->db->get()->result();
        return $trainers;
    }

    function get_groups($trainer_id)
    {
        $groups = $this->db->get_where('trainer_client_groups', ['trainer_id' => $trainer_id])->result_array();
        if ($groups) {
            foreach ($groups as $key => $group) {
                if (isset($group['exp_level_id'])) {
                    $groups[$key]['exp_level_name'] = "";
                    if (!empty($group['exp_level_id'])) {
                        $groups[$key]['exp_level_name'] = $this->exp_level_name_from_id($group['exp_level_id']);
                    }
                }
                if (isset($group['available_equipment'])) {
                    $groups[$key]['available_equipment_name'] = "";
                    if (!empty($group['available_equipment'])) {
                        $groups[$key]['available_equipment_name'] = $this->available_equipment_array_from_id($group['available_equipment']);
                    }
                }
                $this->db->where('trainer_group_id', $group['id']);
                $groups[$key]['members_count'] = $this->db->count_all_results('trainer_clients');
            }
        }
        return $groups;
    }

    function get_all_groups($trainer_id, $page, $limit)
    {
        $this->db->from('trainer_client_groups');
        $this->db->where(['trainer_id' => $trainer_id]);
        $this->db->limit($limit, ($page-1) * $limit);
        $groups = $this->db->get()->result_array();
        if ($groups) {
            foreach ($groups as $key => $group) {
                if (isset($group['exp_level_id'])) {
                    $groups[$key]['exp_level_name'] = "";
                    if (!empty($group['exp_level_id'])) {
                        $groups[$key]['exp_level_name'] = $this->exp_level_name_from_id($group['exp_level_id']);
                    }
                }
                if (isset($group['available_equipment'])) {
                    $groups[$key]['available_equipment_name'] = "";
                    if (!empty($group['available_equipment'])) {
                        $groups[$key]['available_equipment_name'] = $this->available_equipment_array_from_id($group['available_equipment']);
                    }
                }
                $this->db->where('trainer_group_id', $group['id']);
                $groups[$key]['members_count'] = $this->db->count_all_results('trainer_clients');
            }
        }
        return $groups;
    }

    function get_groups_for_workout($trainer_id)
    {
        $this->db->select("CONCAT('group-',trainer_client_groups.id) as group_id,trainer_client_groups.*", FALSE);
        $groups = $this->db->get_where('trainer_client_groups', ['trainer_id' => $trainer_id])->result_array();
        if ($groups) {
            foreach ($groups as $key => $group) {
                if (isset($group['exp_level_id'])) {
                    $groups[$key]['exp_level_name'] = "";
                    if (!empty($group['exp_level_id'])) {
                        $groups[$key]['exp_level_name'] = $this->exp_level_name_from_id($group['exp_level_id']);
                    }
                }
                if (isset($group['available_equipment'])) {
                    $groups[$key]['available_equipment_name'] = "";
                    if (!empty($group['available_equipment'])) {
                        $groups[$key]['available_equipment_name'] = $this->available_equipment_array_from_id($group['available_equipment']);
                    }
                }
                $this->db->where('trainer_group_id', $group['id']);
                $groups[$key]['members_count'] = $this->db->count_all_results('trainer_clients');
            }
        }
        return $groups;
    }

    function view_group($group_id)
    {
        $group = $this->db->get_where('trainer_client_groups', ['id' => $group_id])->row_array();
        if ($group) {
            if (isset($group['exp_level_id'])) {
                $group['exp_level_name'] = "";
                if (!empty($group['exp_level_id'])) {
                    $group['exp_level_name'] = $this->exp_level_name_from_id($group['exp_level_id']);
                }
            }
            if (isset($group['available_equipment'])) {
                $group['available_equipment_name'] = "";
                if (!empty($group['available_equipment'])) {
                    $group['available_equipment_name'] = $this->available_equipment_array_from_id($group['available_equipment']);
                }
            }

            $this->db->select('trainer_clients.id,users.email,trainer_clients.status,meta.first_name,meta.last_name,meta.photo,meta.user_id');
            $this->db->from('users');
            $this->db->join('trainer_clients', 'users.id = trainer_clients.client_id');
            $this->db->join('meta', 'users.id = meta.user_id');
            $this->db->where('trainer_group_id', $group_id);
            $group['members'] = $this->db->get()->result_array();
            $group['clients'] = implode(",", array_column($group['members'], 'user_id'));
        }
        return $group;
    }

    function delete_group($group_id)
    {
        $this->db->update('trainer_clients', ['trainer_group_id' => 'NULL'], ['trainer_group_id' => $group_id]);
        $this->db->delete('trainer_client_groups', array('id' => $group_id));
        return TRUE;
    }

    function remove_client($trainer_id, $client_id)
    {
        $this->db->delete('trainer_clients', ['trainer_id' => $trainer_id, 'client_id' => $client_id]);
    }

    function remove_trainer($client_id, $trainer_id)
    {
        $this->db->delete('trainer_clients', ['trainer_id' => $trainer_id, 'client_id' => $client_id]);
    }

    /*    List Of Stat    */

    function stat_list($user_id)
    {
        $stats = $this->db->get_where('user_stats', ['user_id' => $user_id])->result();

        if ($stats) {
            foreach ($stats as $key => $stat) {
                $this->db->select('stat_value');
                $this->db->order_by('date_taken');
                $starting_stat = $this->db->get_where('user_stats_values', ['stat_id' => $stat->id])->row();
                if ($starting_stat) {
                    $stats[$key]->starting_stat = $starting_stat->stat_value;
                } else {
                    $stats[$key]->starting_stat = "0";
                }

                $this->db->select('stat_value');
                $this->db->order_by('date_taken desc');
                $current_stat = $this->db->get_where('user_stats_values', ['stat_id' => $stat->id])->row();
                if ($current_stat) {
                    $stats[$key]->current_stat = $current_stat->stat_value;
                } else {
                    $stats[$key]->current_stat = "0";
                }
                //                $this->db->order_by('date_taken');
//                $stats[$key]->user_stats_values = $this->db->get_where('user_stats_values',['stat_id' => $stat->id])->result();
            }
        }
        return $stats;
    }

    /*    View Stat    */

    function view_stat($stat_id)
    {
        $stats = $this->db->get_where('user_stats', ['id' => $stat_id])->row();
        if ($stats) {
            $this->db->order_by('stat_value');
            $starting_stat = $this->db->get_where('user_stats_values', ['stat_id' => $stats->id])->row();
            if ($starting_stat) {
                $stats->starting_stat = $starting_stat->stat_value;
            } else {
                $stats->starting_stat = "";
            }
            $this->db->order_by('stat_value desc');
            $current_stat = $this->db->get_where('user_stats_values', ['stat_id' => $stats->id])->row();
            if ($current_stat) {
                $stats->current_stat = $current_stat->stat_value;
            } else {
                $stats->current_stat = "";
            }
            $this->db->order_by('date_taken');
            $stats->user_stats_values = $this->db->get_where('user_stats_values', ['stat_id' => $stats->id])->result();
        }
        return $stats;
    }

    /*    View Stat of this Week   */

    function view_stat_weekly($stat_id)
    {
        $stats = $this->db->get_where('user_stats', ['id' => $stat_id])->row();
        if ($stats) {
            $this->db->order_by('stat_value');
            $starting_stat = $this->db->get_where('user_stats_values', ['stat_id' => $stats->id, 'date_taken >=' => date('Y-m-d', strtotime('this week Monday')), 'date_taken <=' => date('Y-m-d', strtotime('this week Sunday'))])->row();
            if ($starting_stat) {
                $stats->starting_stat = $starting_stat->stat_value;
            } else {
                $stats->starting_stat = "";
            }
            $this->db->order_by('stat_value desc');
            $current_stat = $this->db->get_where('user_stats_values', ['stat_id' => $stats->id, 'date_taken >=' => date('Y-m-d', strtotime('this week Monday')), 'date_taken <=' => date('Y-m-d', strtotime('this week Sunday'))])->row();
            if ($current_stat) {
                $stats->current_stat = $current_stat->stat_value;
            } else {
                $stats->current_stat = "";
            }
            $this->db->order_by('date_taken');
            $stats->user_stats_values = $this->db->get_where('user_stats_values', ['stat_id' => $stats->id, 'date_taken >=' => date('Y-m-d', strtotime('this week Monday')), 'date_taken <=' => date('Y-m-d', strtotime('this week Sunday'))])->result();
        }
        return $stats;
    }

    /*    View Stat of this Month   */

    function view_stat_monthly($stat_id)
    {
        $stats = $this->db->get_where('user_stats', ['id' => $stat_id])->row();
        if ($stats) {
            $this->db->order_by('stat_value');
            $starting_stat = $this->db->get_where('user_stats_values', ['stat_id' => $stats->id, 'date_taken >=' => date('Y-m-01'), 'date_taken <=' => date('Y-m-t')])->row();
            if ($starting_stat) {
                $stats->starting_stat = $starting_stat->stat_value;
            } else {
                $stats->starting_stat = "";
            }
            $this->db->order_by('stat_value desc');
            $current_stat = $this->db->get_where('user_stats_values', ['stat_id' => $stats->id, 'date_taken >=' => date('Y-m-01'), 'date_taken <=' => date('Y-m-t')])->row();
            if ($current_stat) {
                $stats->current_stat = $current_stat->stat_value;
            } else {
                $stats->current_stat = "";
            }
            $this->db->order_by('date_taken');
            $stats->user_stats_values = $this->db->get_where('user_stats_values', ['stat_id' => $stats->id, 'date_taken >=' => date('Y-m-01'), 'date_taken <=' => date('Y-m-t')])->result();
        }
        return $stats;
    }

    /*    Update New Stat    */

    function update_new_stat($data)
    {
        $stats = $this->db->get_where('user_stats_values', ['stat_id' => $data['stat_id'], 'Date(date_taken)' => $data['date_taken']])->row();
        if ($stats) {
            $this->db->update('user_stats_values', ['stat_value' => $data['stat_value']], ['id' => $stats->id]);
        } else {
            $this->db->insert('user_stats_values', $data);
        }
        return true;
    }

    function check_exercise_video_existance($trainer_id, $exercise_id)
    {
        return $this->db->get_where('exercise_video', ['trainer_id' => $trainer_id, 'exercise_id' => $exercise_id])->row();
    }

    function insert_exercise_video($data)
    {
        return $this->db->insert('exercise_video', $data);
    }

    function delete_exercise_video($trainer_id, $exercise_id)
    {
        $this->db->delete('exercise_video', ['trainer_id' => $trainer_id, 'exercise_id' => $exercise_id]);
    }

    public function login($identity, $password, $remember = FALSE)
    {
        if (empty($identity) || empty($password)) {
            return FALSE;
        }

        $query = $this->db->select('email, id, password, group_id')
            ->where('email', $identity)
            ->where('active', 1)
            ->limit(1)
            ->get('users');

        $result = $query->row();

        if ($query->num_rows() == 1) {
            $query1 = $this->db->select('password')
                ->select('salt')
                ->where('email', $identity)
                ->limit(1)
                ->get('users');

            $result1 = $query1->row();

            $salt = substr($result1->password, 0, 10);

            $password = $salt . substr(sha1($salt . $password), 0, -10);

            //            $password = $this->hash_password_db($identity, $password);

            if ($result1->password === $password) {
                $this->db->update('users', array('last_login' => now()), array('id' => $result->id));

                $group_row = $this->db->select('name')->where('id', $result->group_id)->get('groups')->row();

                return TRUE;
            }
        }

        return FALSE;
    }

    public function prebuild_videos_list()
    {
        $this->db->select('id, title, mobile_video');
        return $this->db->get('exercises', 4)->result_array();
    }

}