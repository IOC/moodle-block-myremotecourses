<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('block_myremotecourses_url', get_string('myremoteurl', 'block_myremotecourses'),
                   get_string('myremoteurl_desc', 'block_myremotecourses'), '', PARAM_URL, 50));
}
