-- Query for: refactor team members tables
update team_member set createdDtm = updatedDtm where createdDtm like "%0000%";
SELECT team_id as id, parent_id, role_id, name, mobile, email, venue_name, status, createdDtm as created_at, createdDtm as updated_at FROM `team_member`; -- Select the data using this query and export and import into new table

-- Query for: deleting old data from leads table
delete from `leads` where create_date < "2023-03-01" or mobile like "%998877%" or mobile is null;
update `leads` set lead_create_by = concat(SUBSTRING_INDEX(lead_create_by, '#', 1));
update `leads` set created_at = concat(create_date, " ", create_time);
update `leads` set lead_create_by = null where not exists(select * from team_member where team_id = leads.lead_create_by);
update `leads` set updatedDtm = created_at where updatedDtm like "%0000%";

SELECT l.leads_id as id, l.lead_create_by as created_by, l.created_at as lead_datetime, l.name, l.email, l.mobile, l.alternate_phone_no as alternate_mobile, c.name as source, l.locality, status_cat.name as status, concat(l.event_date, ' ', CURRENT_TIME) as event_datetime, l.no_of_guest as pax, l.re_enuery_count as enquiry_count, l.created_at, l.updatedDtm as updated_at FROM `leads` as l join categories as c on l.lead_source = c.id join categories as status_cat on l.status = status_cat.id; -- Select the data using this query and run below function to set up leads into new database tables.

-- Run: set_leads_refactor(). function;

delete FROM `rm_msg` where not exists (select * from leads where leads.leads_id = rm_msg.lead_id);

SELECT lead_id, team_id as created_by, msg_title as title, msg as message, created_at as created_at, created_at as updated_at FROM `rm_msg`; -- Select the data using this query and export it and upload onto new database table.

delete FROM `event` where not exists (select * from leads where leads.leads_id = event.leads_id);
delete FROM `event` where not exists (select * from team_member where team_id = event.team_id);
-- update event set budget_in_lacs = concat("0.", SUBSTRING_INDEX(budget_in_lacs, '0', 1)) where budget_in_lacs like "%,%"; // no need to this query because we have updated new database migrations for budget column in necessary tables.
select leads_id as lead_id, team_id as created_by, event_name, concat(event_date, " ", CURRENT_TIME) as event_datetime, no_of_guest as pax, budget_in_lacs as budget, 
CASE
    WHEN food_perference = 1 THEN "Veg"
    WHEN food_perference = 2 THEN "Non Veg"
    WHEN food_perference = 3 THEN "Both"
	ELSE null
END as food_preference, 
CASE
    WHEN event_slot = 1 THEN "Morning"
    WHEN event_slot = 2 THEN "Full Day"
    WHEN event_slot = 3 THEN "Evening"
	ELSE null
END as event_slot,
concat(event_date, " ", CURRENT_TIME) as created_at,
concat(event_date, " ", CURRENT_TIME) as updated_at
from event; -- Select the data using this query and export it and upload onto new database table.

delete FROM `tasks` where not exists (select * from leads where leads.leads_id = tasks.leads_id) or tasks.leads_id is null;
delete from `tasks` where not exists (select * from team_member where team_member.team_id = tasks.team_id) or tasks.team_id is null;
UPDATE `tasks` set updatedDtm = concat(create_date, " ", create_time) where updatedDtm like "%0000%";
SELECT tasks_id as id, leads_id as lead_id, team_id as created_by, concat(task_due_date, " ", task_due_time) as task_schedule_datetime, 
case
 	when task_follow_up = 1 then "Call"
 	when task_follow_up = 2 then "SMS"
 	when task_follow_up = 3 then "Mail"
 	when task_follow_up = 3 then "WhatsApp"
	else null
end as follow_up,
message,
case
 	when donw_with = 1 then "Call"
 	when donw_with = 2 then "SMS"
 	when donw_with = 3 then "Mail"
 	when donw_with = 3 then "WhatsApp"
	else null
end as done_with,
notes as done_message, concat(task_done_date, " ", task_done_time) as done_datetime, concat(create_date, " ", create_time) as created_at, updatedDtm as updated_at
FROM `tasks`; -- Select the data using this query and export it and upload onto new database table.

delete FROM `lead_notes` where not exists (select * from leads where leads.leads_id = lead_notes.lead_id) or lead_id is null;
delete FROM `lead_notes` where not exists(select * from team_member where team_id = lead_notes.note_by_id) or note_by_id is null;
SELECT lead_id, note_by_id as created_by, note as message, create_at as created_at, create_at as updated_at FROM `lead_notes`; -- Select the data using this query and export it and upload onto new database table.

-- Run: set_unique_team_id_in_leads(). function;

delete FROM `leads_action` where not EXISTS(select * from team_member where team_id = leads_action.team_id) or team_id is null;
delete FROM `leads_action` where not EXISTS(select * from leads where leads_id = leads_action.leads_id) or leads_id is null;

-- Run: set_lead_forwards_for_venue_crm(). function;

delete from lead_forwards where not exists(select * from team_members where id = lead_forwards.forward_to); -- Run this query in new database
delete from lead_forward_infos where not exists(select * from team_members where id = lead_forward_infos.forward_to); -- Run this query in new database


-- Queries for: refactoring non venue crm data
delete from `nvleads` where createdDtm < "2023-03-01" or mobile is null;
SELECT nv_id as id, create_by as created_by, createdDtm as lead_datetime, name, email, mobile, alternate_number as alternate_mobile, address, "Active" as status, concat(event_date, " ", CURRENT_TIME) as event_datetime, createdDtm as created_at, createdDtm as updated_at FROM `nvleads`; -- Select the data using this query and export it and upload onto new database table.

delete from `nv_rmmsg` where not EXISTS(select * from nvleads where nvleads.nv_id = nv_rmmsg.lead_id) or lead_id is null;
delete FROM `nv_rmmsg` where service_category is null;
update `nv_rmmsg` set budget = REPLACE(budget, ",", "");
update `nv_rmmsg` set budget = concat(SUBSTRING_INDEX(budget, '-', 1)) where budget like "%-%";
SELECT lead_id, service_category as vendor_category_id, team_id as created_by, msg_title as title, msg as message, budget, createdDtm as created_at, createdDtm as updated_at FROM `nv_rmmsg`; -- Select the data using this query and export it and upload onto new database table.

delete FROM `nvrm_event` where not EXISTS (select * from nvleads where nvleads.nv_id = nvrm_event.leads_id) or leads_id is null;
delete FROM `nvrm_event` where not exists(select * from team_member where team_member.team_id = nvrm_event.vendor_id) or vendor_id is null;
SELECT * FROM `nvrm_event` WHERE no_of_guest like "%+%"; -- Select data where this condition and calculate no_of_guest and update this column.
SELECT leads_id as lead_id, vendor_id as created_by, event_name, concat(event_date, " ", create_time) as event_datetime, no_of_guest as pax, event_slot, select_venue as venue_name, concat(create_date, " ", create_time) as created_at, updatedDtm as updated_at FROM `nvrm_event`; -- Select the data using this query and export it and upload onto new database table.

delete FROM `vendor_tasks` where not exists(select * from nvleads where nvleads.nv_id = vendor_tasks.leads_id) or leads_id is null;
delete FROM `vendor_tasks` where not exists(select * from user_register where user_register.id = vendor_tasks.vendor_id) or vendor_id is null;
SELECT tasks_id as id, leads_id as lead_id, vendor_id as created_by, concat(task_due_date, " ", task_due_time) as task_schedule_datetime,
CASE
	WHEN task_follow_up = 1 THEN "Call"
	WHEN task_follow_up = 2 THEN "SMS"
	WHEN task_follow_up = 3 THEN "Mail"
	WHEN task_follow_up = 4 THEN "WhatsApp"
    ELSE null
END as task_follow_up,
message, concat(create_date, " ", create_time) as created_at, updatedDtm as updated_at   
FROM `vendor_tasks`; -- Select the data using this query and export it and upload onto new database table.

delete FROM `vendor_visit` where not exists(select * from nvleads where nvleads.nv_id = vendor_visit.leads_id) or leads_id is null;
delete FROM `vendor_visit` where not exists(select * from user_register where user_register.id = vendor_visit.vendor_id) or vendor_id is null;
-- if visits exist then we need to create refactoring process of it;

SELECT id, category as category_id, first_name as name, office_name as business_name, mobile, email, status, CURRENT_TIMESTAMP as created_at, CURRENT_TIMESTAMP as updated_at FROM `user_register`; -- Select the data using this query and export it and upload onto new database table.

delete FROM `nv_fwd_leads_new` where not EXISTS(select * from nvleads where nvleads.nv_id = nv_fwd_leads_new.lead_id) or lead_id is null;
delete FROM `nv_fwd_leads_new` where not EXISTS(select * from user_register where user_register.id = nv_fwd_leads_new.vendor_id) or vendor_id is null;





-- ver 2.2.0 refactoring
delete FROM `lead_forwards` where forward_to in(11,20,39);
delete FROM `lead_forward_infos` where forward_to in(11,20,39);
update leads set lead_color = "#4bff0033" where created_by is null;
update leads set lead_color = "#0066ff33" where created_by is not null;
update `leads` set lead_color = "#ff00001f" where exists(select * from lead_forwards as fwd where fwd.lead_id = leads.lead_id);
update `leads` set lead_color = "#ff000066" where status = "Done";




-- for database refactoring
SELECT fwd.* FROM `lead_forwards` as fwd where not exists(select * from lead_forward_infos as fwd_info where fwd_info.lead_id = fwd.lead_id);


SELECT fwd.* FROM `lead_forwards` as fwd join team_members as tm on tm.id = fwd.forward_to where tm.role_id = 4 and fwd.lead_status = "Done" group by fwd.lead_id order by fwd.lead_id;


-- actual working queries
update leads set read_status = 1;

SELECT fwd.* FROM `lead_forwards` as fwd join team_members as tm on tm.id = fwd.forward_to where tm.role_id = 4 and fwd.task_id is not null; -- and add task id into leads table repactively



update lead_forwards set task_id = (select id from tasks where tasks.lead_id = lead_forwards.lead_id and tasks.created_by = lead_forwards.forward_to order by tasks.id desc limit 1);