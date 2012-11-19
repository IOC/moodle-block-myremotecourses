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

    var $idprovider;
    var $pathtoajax = '/blocks/myremotecourses/ajax.php';

    /**
     * block initializations
     */
    public function init() {
        global $CFG, $DB, $OUTPUT, $USER;
        
        $remotehost = (isset($CFG->block_myremotecourses_myremotehost)?$CFG->block_myremotecourses_myremotehost:'');
        $hostid = (is_mnet_remote_user($USER)?$USER->mnethostid:$remotehost);
        if (!empty($hostid)){
            $this->idprovider = $DB->get_record('mnet_host', array('id'=>$hostid), 'id, name, wwwroot');
            if ($this->idprovider){
                if (is_mnet_remote_user($USER)){
                    $url = $this->idprovider->wwwroot;
                }else{
                    $url = $CFG->wwwroot.'/auth/mnet/jump.php?hostid='.$this->idprovider->id; 
                }
                $this->title = get_string('remotecourses', 'block_myremotecourses', html_writer::link($url,
                        $this->idprovider->name));
            }
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
        global $USER, $PAGE, $OUTPUT, $CFG;
        if($this->content !== NULL) {
            return $this->content;
        }

        if ($this->idprovider){
            $PAGE->requires->js('/blocks/myremotecourses/index.js');
            $PAGE->requires->string_for_js('nocourses', 'block_myremotecourses');
            $PAGE->requires->string_for_js('errormyremotehost', 'block_myremotecourses');
            if (!empty($CFG->block_myremotecourses_moodlepath)){
                $this->pathtoajax = $CFG->block_myremotecourses_moodlepath;
            }
            $directurl = $this->idprovider->wwwroot.$this->pathtoajax;
            if (is_mnet_remote_user($USER)){
                $remoteurl = $directurl;
            }else{
                $remoteurl = $CFG->wwwroot.'/auth/mnet/jump.php?hostid='.$this->idprovider->id.'&wantsurl='.$this->pathtoajax;
            }

            $PAGE->requires->js_init_code('getremotecourses("'.$directurl.'", "'.$remoteurl.'");', true);
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        $content = array();

        if($this->idprovider){
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

}
