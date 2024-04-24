<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Refactor extends MY_Controller {
	private $current_timestamp;
	private $wb_laravel_db;
	public function __construct() {
		parent::__construct();
		date_default_timezone_set("Asia/Kolkata");
		$this->current_timestamp = date('Y-m-d H:i:s');
		$this->wb_laravel_db = $this->load->database('wb_laravel_crm', true);
	}

	private function print_data(array $data) {
		echo json_encode($data);
	}

	public function set_leads_refactor() {
		$leads = $this->db->query("SELECT l.leads_id as id, l.lead_create_by as created_by, l.created_at as lead_datetime, l.name, l.email, l.mobile, l.alternate_phone_no as alternate_mobile, c.name as source, l.locality, status_cat.name as status, concat(l.event_date, ' ', CURRENT_TIME) as event_datetime, l.no_of_guest as pax, l.re_enuery_count as enquiry_count FROM `leads` as l join categories as c on l.lead_source = c.id join categories as status_cat on l.status = status_cat.id")->result();

		$this->print_data($leads);
		die;

		foreach ($leads as $lead) {
			$col_val = [
				'id' => $lead->id,
				'created_by' => $lead->created_by,
				'lead_datetime' => $lead->lead_datetime,
				'name' => $lead->name,
				'email' => $lead->email,
				'mobile' => $lead->mobile,
				'alternate_mobile' => $lead->alternate_mobile,
				'source' => $lead->source,
				'locality' => $lead->locality,
				'status' => $lead->status,
				'event_datetime' => $lead->event_datetime,
				'pax' => $lead->pax,
				'enquiry_count' => $lead->enquiry_count,
			];
			$this->wb_laravel_db->insert('leads', $col_val);
		}
		echo "Leads uploaded done.";
	}

	public function set_unique_team_id_in_leads() {
		$leads = $this->db->query("SELECT leads_id, name, team_id, lead_read_status, mobile from leads limit 2")->result();
		foreach ($leads as $lead) {
			$team_ids = explode(",", $lead->team_id);
			$unique_team_id = implode(",", array_unique($team_ids));
			$this->db->query("UPDATE leads set team_id = '$unique_team_id' where leads_id = $lead->leads_id");
		}
	}

	// public function get_duplicate_team_id_in_leads() {
	// 	$leads = $this->db->query("SELECT leads_id, name, team_id, lead_read_status, mobile from leads limit 2")->result();
	// 	$duplicate_data = [];
	// 	foreach ($leads as $lead) {
	// 		$team_ids = explode(",", $lead->team_id);
	// 		$unique_team_id = array_unique($team_ids);
	// 		if (sizeof($team_ids) !== sizeof($unique_team_id)) {
	// 			array_push($duplicate_data, $lead);
	// 		}
	// 	}
	// 	$this->print_data($duplicate_data);
	// }
	//insert into nvrm_lead_forwards (lead_id, forward_to, lead_datetime, name, email, mobile, alternate_mobile, address, lead_status, event_datetime, )

	public function set_lead_forwards_for_venue_crm() {
		//devide lead forwards into two rms account for the lead forwards info: using limit and offset options in query.
		$leads = $this->db->query("SELECT l.leads_id, l.team_id, l.created_at, l.name, l.email, l.mobile, l.alternate_phone_no, source.name as lead_source, l.locality, status.name as lead_status FROM `leads` as l join categories as source on l.lead_source = source.id join categories as status on l.status = status.id limit 2")->result();
		foreach ($leads as $lead) {
			$team_ids = explode(",", $lead->team_id);
			foreach ($team_ids as $team_id) {
				$col_val_for_lead_forwards = array(
					'lead_id' => $lead->leads_id,
					'forward_to' => $team_id,
					'lead_datetime' => $lead->created_at,
					'name' => $lead->name,
					'email' => $lead->email,
					'mobile' => $lead->mobile,
					'alternate_mobile' => $lead->alternate_phone_no,
					'source' => $lead->lead_source,
					'locality' => $lead->locality,
					'created_at' => $lead->created_at,
					'updated_at' => $lead->created_at,
				);

				$lead_action = $this->db->query("SELECT id, contacted, team_id, leads_id, close_msg, close_reason from leads_action where team_id = $team_id and leads_id = $lead->leads_id order by id desc")->row();
				if($lead_action){
					$col_val_for_lead_forwards['service_status'] = $lead_action->contacted;
					if($lead_action->close_reason){
						$col_val_for_lead_forwards['done_title'] = $lead_action->close_reason;
						$col_val_for_lead_forwards['done_message'] = $lead_action->close_msg;
						$col_val_for_lead_forwards['lead_status'] = "Done";
						$col_val_for_lead_forwards['read_status'] = 1;
					}else{
						$col_val_for_lead_forwards['lead_status'] = $lead->lead_status;
					}
				}else{
					$col_val_for_lead_forwards['lead_status'] = $lead->lead_status;
				}

				$task = $this->db->query("SELECT * FROM tasks where team_id = $team_id and leads_id = $lead->leads_id order by tasks_id desc")->row();
				if($task){
					$col_val_for_lead_forwards['task_id'] = $task->tasks_id;
				}

				$this->wb_laravel_db->insert("lead_forwards", $col_val_for_lead_forwards);

				//setup for lead_forward_infos
				$col_val_for_lead_forward_infos = array(
					'lead_id' => $lead->leads_id,
					'forward_from' => 20,
					'forward_to' => $team_id,
					'created_at' => $lead->created_at,
					'updated_at' => $lead->created_at,
				);
				$this->wb_laravel_db->insert("lead_forward_infos", $col_val_for_lead_forward_infos);
			}
		}
		echo "Lead forwards and lead forward infos are done";
	}

	public function set_vm_lead_forwards(){
		$nv_fwd_leads_new = $this->db->query("SELECT l.nv_id as lead_id, fwd.vendor_id as forward_to, l.createdDtm as lead_datetime, l.name, l.email, l.mobile, l.alternate_number as alternate_mobile, l.address, 'Active' as lead_status, concat(l.event_date, ' ', CURRENT_TIME) as event_datetime  FROM `nv_fwd_leads_new` as fwd join nvleads as l on l.nv_id = fwd.lead_id");
	}

	public function set_nvrm_forward_leads(){
		$leads = $this->wb_laravel_db->query("SELECT * from nv_leads")->result();
		foreach($leads as $lead){
			$col_val = [
				'lead_id' => $lead->id,
				'forward_to' => $lead->created_by,
				'lead_datetime' => $lead->lead_datetime,
				'name' => $lead->name,
				'email' => $lead->email,
				'mobile' => $lead->mobile,
				'alternate_mobile' => $lead->alternate_mobile,
				'address' => $lead->address,
				'lead_status' => $lead->status,
				'event_datetime' => $lead->event_datetime,
			];
		}
	}

	
	//End of refactoring provision

	public function copy_rm_events_for_vm() {
		$event_slot_arr = array('1' => 'Lunch', '2' => 'Lunch', '3' => 'Dinner');
		$food_preference_arr = array('1' => 'Veg', '2' => 'Non Veg', '3' => 'Both');
		$events = $this->db->query("SELECT e.event_id, e.leads_id, e.team_id, e.event_name, e.event_date, e.no_of_guest, e.budget_in_lacs, e.food_perference, e.event_slot, l.team_id as lead_shared_with from event as e join leads as l on e.leads_id = l.leads_id limit 1")->result();
		foreach ($events as $event) {
			$team_ids = explode(",", $event->lead_shared_with);
			foreach ($team_ids as $shared_with) {
				$col_val = [
					'lead_id' => $event->leads_id,
					'created_by' => $shared_with,
					'event_name' => $event->event_name,
					'event_datetime' => $event->event_date . " " . date('H:i:s'),
					'pax' => $event->no_of_guest,
					'budget' => $event->budget_in_lacs,
					'food_preference' => $food_preference_arr[$event->food_perference],
					'event_slot' => $event_slot_arr[$event->event_slot],
					'event_id' => $event->event_id,
					'created_at' => $this->current_timestamp,
					'updated_at' => $this->current_timestamp,
				];
				$this->db->insert('up_vm_events', $col_val);
			}
		}
		echo "Copy RM events to VM events table are done";
	}

	public function vm_events_refactor() {
		$event_slot_arr = array('1' => 'Lunch', '2' => 'Lunch', '3' => 'Dinner');
		$food_preference_arr = array('1' => 'Veg', '2' => 'Non Veg', '3' => 'Both');
		$events = $this->db->query("SELECT * from vm_event")->result();

		foreach ($events as $event) {
			$col_val = [
				'lead_id' => $event->leads_id,
				'created_by' => $event->team_id,
				'event_name' => $event->event_name,
				'event_datetime' => $event->event_date . " " . date('H:i:s'),
				'pax' => $event->no_of_guest,
				'budget' => $event->budget_in_lacs,
				'food_preference' => $food_preference_arr[$event->food_perference],
				'event_slot' => $event_slot_arr[$event->event_slot],
				'created_at' => $this->current_timestamp,
				'updated_at' => $this->current_timestamp,
			];
			$this->db->insert('up_vm_events', $col_val);
		}
		echo "VM events refactored are done";
	}

	public function set_tasks_refactor() {
		$tasks = $this->db->query("SELECT tasks_id, leads_id, team_id, task_due_date, task_due_time, task_follow_up, message, donw_with, notes, task_done_date, task_done_time FROM `tasks` limit 10")->result();
		$task_followup_arr = ['1' => 'Call', '2' => 'SMS', '3' => 'Mail', '4' => 'WhatsApp'];

		foreach ($tasks as $task) {
			$col_val = [
				'lead_id' => $task->leads_id,
				'created_by' => $task->team_id,
				'task_schedule_datetime' => date('Y-m-d H:i:s', strtotime($task->task_due_date . " " . $task->task_due_time)),
				'follow_up' => $task_followup_arr[$task->task_follow_up],
				'message' => $task->message,
				'done_with' => $task->donw_with != null ? $task_followup_arr[$task->donw_with] : null,
				'done_message' => $task->notes,
				'done_datetime' => $task->task_done_date != null ? date('Y-m-d H:i:s', strtotime($task->task_done_date . " " . $task->task_done_time)) : null,
				'created_at' => $this->current_timestamp,
				'updated_at' => $this->current_timestamp,
			];
			$this->db->insert('up_tasks', $col_val);
		}
		echo "Tasks refactored successfully.";
	}
	// public function set_visits_refactor() {
	// 	$tasks = $this->db->query("SELECT visit_id, leads_id, team_id, visit_due_date, visit_due_time, task_follow_up, message, donw_with, notes, task_done_date, task_done_time FROM `tasks` limit 10")->result();
	// 	$task_followup_arr = ['1' => 'Call', '2' => 'SMS', '3' => 'Mail', '4' => 'WhatsApp'];

	// 	foreach ($tasks as $task) {
	// 		$col_val = [
	// 			'lead_id' => $task->leads_id,
	// 			'created_by' => $task->team_id,
	// 			'task_schedule_datetime' => date('Y-m-d H:i:s', strtotime($task->task_due_date . " " . $task->task_due_time)),
	// 			'follow_up' => $task_followup_arr[$task->task_follow_up],
	// 			'message' => $task->message,
	// 			'done_with' => $task->donw_with != null ? $task_followup_arr[$task->donw_with] : null,
	// 			'done_message' => $task->notes,
	// 			'done_datetime' => $task->task_done_date != null ? date('Y-m-d H:i:s', strtotime($task->task_done_date . " " . $task->task_done_time)) : null,
	// 			'created_at' => $this->current_timestamp,
	// 			'updated_at' => $this->current_timestamp,
	// 		];
	// 		$this->db->insert('up_tasks', $col_val);
	// 	}
	// 	echo "Tasks refactored successfully.";
	// }




}
