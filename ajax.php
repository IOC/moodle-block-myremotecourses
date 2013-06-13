<?php

//  My remote courses block for Moodle
//  Copyright © 2012  Institut Obert de Catalunya
//
//  This program is free software: you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation, either version 3 of the License, or
//  (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with this program.  If not, see <http://www.gnu.org/licenses/>.

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/coursecatlib.php');

require_login(null, false);

@header('Content-type: application/json; charset=utf-8');

function print_ioc_overview($courses) {
    global $CFG, $DB, $OUTPUT, $PAGE, $USER;

    $PAGE = new moodle_page();
    $context = get_context_instance(CONTEXT_SYSTEM);
    $PAGE->set_context($context);

    $visible_courses = array();
    foreach ($courses as $id => $course) {
        if ($course->visible) {
            $visible_courses[$id] = $course;
        }
    }

    $htmlarray = array();
    $outhtml = '';
    if ($modules = $DB->get_records('modules')) {
        foreach ($modules as $mod) {
            if (file_exists($CFG->dirroot.'/mod/'.$mod->name.'/lib.php')) {
                include_once($CFG->dirroot.'/mod/'.$mod->name.'/lib.php');
                $fname = $mod->name.'_print_overview';
                if (function_exists($fname)) {
                    $fname($visible_courses,$htmlarray);
                }
            }
        }
    }

    $cat_names = coursecat::make_categories_list();
    $cat_courses = array();
    foreach ($courses as $course) {
        $parent = $course->category;
        while ($aux = coursecat::get($parent)->get_parents()) {
            $parent = $aux[0];
        }
        $cat_courses[$parent][$course->id] = $course;
    }

    foreach ($cat_courses as $category => $courses) {
        $outhtml .= html_writer::start_tag('div', array('class' => 'categorybox'));
        $outhtml .= html_writer::start_tag('h3');
        $outhtml .= $cat_names[$category];
        $outhtml .= html_writer::end_tag('h3');
        foreach ($courses as $course) {
            $PAGE->set_context(get_context_instance(CONTEXT_COURSE, $course->id));
            $fullname = format_string($course->fullname, true, array('context' => get_context_instance(CONTEXT_COURSE, $course->id)));
            $attributes = array('title' => s($fullname));
            if (empty($course->visible)) {
                $attributes['class'] = 'dimmed';
            }

            $show_overview = '';
            if ($course->visible && array_key_exists($course->id, $htmlarray)) {
                if (count($htmlarray[$course->id]) > 0) {
                    foreach (array_keys($htmlarray[$course->id]) as $mod) {
                        $modname = get_string('modulenameplural', $mod);
                        $show_overview .= html_writer::start_tag('a', array('title' => $modname,
                                'id' => 'roverview-'. $course->id .'-'.$mod.'-link',
                                'class' => 'roverview-link',
                                'href' => '#'));
                        $show_overview .= html_writer::empty_tag('img', array('title' => $modname,
                                'class' => 'icon',
                                'src' => $OUTPUT->pix_url('icon',$mod)));
                        $show_overview .= html_writer::end_tag('a');
                    }
                }
            }
            $outhtml .= $OUTPUT->box_start('coursebox');
            $outhtml .= $OUTPUT->heading(html_writer::link(
                     new moodle_url('/course/view.php', array('id' => $course->id)), $fullname, $attributes).$show_overview, 3);
            if (array_key_exists($course->id,$htmlarray)) {
                foreach ($htmlarray[$course->id] as $modname => $html) {
                    $outhtml .= html_writer::start_tag('div', array('id' => 'roverview-'. $course->id .'-'.$modname,
                            'class' => 'rcourse-overview'));
                    $outhtml .= $html;
                    $outhtml .= html_writer::end_tag('div');
                }
            }
            $outhtml .= $OUTPUT->box_end();
        }
        $outhtml .= html_writer::end_tag('div');
    }
    return $outhtml;
}

$content = '';
$sitetitle = '';
$siteurl = '';

if (!empty($USER->id)) {
    $courses = enrol_get_my_courses('modinfo, sectioncache');
    foreach ($courses as $c) {
        if (isset($USER->lastcourseaccess[$c->id])) {
            $courses[$c->id]->lastaccess = $USER->lastcourseaccess[$c->id];
        } else {
            $courses[$c->id]->lastaccess = 0;
        }
    }
    $content = print_ioc_overview($courses);
    if (empty($content)) {
        $content = '<!--KO-->';
    }
    $sitetitle = $SITE->fullname;
    $siteurl = $CFG->wwwroot;
}

echo json_encode(array(
    'html' => $content,
    'title' => $sitetitle,
    'url' => $siteurl
));

