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

/**
 * My remote courses block
 *
 *
 * @package   blocks
 * @author    Marc Català <mcatala@ioc.cat>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/lib/weblib.php');
require_once($CFG->dirroot . '/lib/formslib.php');

class block_myremotecourses extends block_base {

    /**
     * block initializations
     */
    public function init() {
        global $CFG;

        if (!empty($CFG->block_myremotecourses_url)){
            $this->title = get_string('remotecourses', 'block_myremotecourses', html_writer::link($CFG->block_myremotecourses_url,
                        ''));
        }else{
            $this->title = get_string('noremotehost', 'block_myremotecourses');
        }
    }

    /**
     * block contents
     *
     * @return object
     */
    public function get_content() {
        global $PAGE, $OUTPUT, $CFG;
        if($this->content !== NULL) {
            return $this->content;
        }

        if (!empty($CFG->block_myremotecourses_url)){
            $PAGE->requires->js('/blocks/myremotecourses/index.js');
            $PAGE->requires->string_for_js('errormyremotehost', 'block_myremotecourses');
            $PAGE->requires->js_init_code('getremotecourses("'.$CFG->block_myremotecourses_url.'");', true);
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        $content = array();

        if(!empty($CFG->block_myremotecourses_url)){
             $this->content->text .= html_writer::start_tag('div', array('class' => 'categorybox'));
             $this->content->text .= html_writer::start_tag('div', array('id' => 'rcourse-list'));
             $this->content->text .= html_writer::empty_tag('img', array('class'=>'roverview-loading','src'=>$OUTPUT->pix_url('i/ajaxloader'),'style'=>'display: none','alt'=>''));
             $this->content->text .= html_writer::end_tag('div');
             $this->content->text .= html_writer::end_tag('div');
        }

        return $this->content;
    }

    /**
     * allow the block to have a configuration page
     *
     * @return boolean
     */
    public function has_config() {
        return true;
    }

    /**
     * locations where block can be displayed
     *
     * @return array
     */
    public function applicable_formats() {
        return array('my-index'=>true);
    }

    public function html_attributes() {
        $attributes = parent::html_attributes(); // Get default values
        $attributes['class'] .= ' remote-hidden'; // Append our class to class attribute
        return $attributes;
    }

}
