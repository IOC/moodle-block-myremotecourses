<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('block_myremotecourses_moodlepath', get_string('moodleoldpath', 'block_myremotecourses'),
                   get_string('moodleoldpath', 'block_myremotecourses'), '', PARAM_URL, 20));
    
    $sql = "SELECT h.id, h.name, h.wwwroot
    FROM {mnet_host} h
    JOIN {mnet_application} a ON h.applicationid = a.id
    AND h.deleted = 0
    WHERE h.id <> ?";
    
    $hosts = $DB->get_records_sql($sql, array($CFG->mnet_localhost_id));
    
    $select[] = '';
    foreach ($hosts as $host){
        if ($host->id != $CFG->mnet_all_hosts_id){
            $select[$host->id] = $host->name;
        }
    }
    
    $settings->add(new admin_setting_configselect('block_myremotecourses_myremotehost',
            get_string('myremotehost', 'block_myremotecourses'),
            get_string('myremotehostdesc', 'block_myremotecourses'), '0', $select)
    );
}
