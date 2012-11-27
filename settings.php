<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('block_myremotecourses_moodlepath', get_string('moodlepath', 'block_myremotecourses'),
                   get_string('moodlepath', 'block_myremotecourses'), '', PARAM_URL, 50));
}
