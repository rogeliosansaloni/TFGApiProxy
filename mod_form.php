<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
//
// This file is part of APIProxy
//
// APIProxy is a plugin developed in Catalunya that helps teacher to understand how
// students use APIs in their assessments. This project implements an activity for
// Moodle that works as an API and a middleware to integrate third party APIs that
// generates statistics of use for teachers. Moodle is a Free Open source Learning
// Management System by Martin Dougiamas.
// ProxyAPI is a project initiated and leaded by Daniel Amo at the GRETEL research
// group at La Salle Campus Barcelona, Universitat Ramon Llull.
//
// ProxyAPI is copyrighted 2020 by Daniel Amo and Oriol Pando
// of the La Salle Campus Barcelona, Universitat Ramon Llull https://www.salleurl.edu
// Contact info: Daniel Amo Filvà  danielamo @ gmail.com or daniel.amo @ salle.url.edu.

/**
 * API Proxy configuration form
 *
 * @package mod_apiproxy
 * @copyright  2020 Daniel Amo, Oriol Pando
 *  daniel.amo@salle.url.edu
 *  oriolpando@gmail.com
 * @copyright  2020 La Salle Campus Barcelona, Universitat Ramon Llull https://www.salleurl.edu
 * @author     Daniel Amo
 * @author     Oriol Pando
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/apiproxy/locallib.php');
require_once($CFG->libdir.'/filelib.php');

class mod_apiproxy_mod_form extends moodleform_mod {
    function definition() {
        global $CFG, $DB;

        $id = optional_param('update', null, PARAM_INT); // Course Module ID

        if (!$cm = get_coursemodule_from_id('apiproxy', $id)) {
           // print_error('invalidcoursemodule');
        }
        if (isset($id) && isset($cm) && $id != 0) {
            $apiproxyInfo = $DB->get_record('apiproxy', array('id'=>$cm->instance), '*', MUST_EXIST);
            $_SESSION['apiproxy'] = $apiproxyInfo;
            redirect($CFG->wwwroot . '/mod/apiproxy/view.php?id='. $cm->id);
        }
        $config = get_config('apiproxy');

        $mform = $this->_form;

        $config = get_config('apiproxy');

        //-------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'apiname', get_string('apiname', 'apiproxy'), array('size'=>'48'));
        $mform->addRule('apiname', null, 'required', null, 'client');
        $mform->addRule('apiname', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $this->standard_intro_elements();
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('apiname', PARAM_TEXT);
        } else {
            $mform->setType('apiname', PARAM_CLEANHTML);
        }
        


        

        //-----------------API PROXY-----------------------------
        //-----------------API TYPE---------------
        /*
        $mform->addElement('header', 'contentsection', get_string('contentheader', 'apiproxy'));
        $options = array(
            'intern' => 'Intern API (1st Party API)',
            'extern' => 'Extern API (3rd Party API)'
        );
        $select = $mform->addElement('select', 'apitype', get_string('contentheader', 'apiproxy'), $options);
        $select->setSelected('extern');

        $mform->disabledIf('apitype', '', 'eq', '');
        $mform->hideIf('apitype', '', 'eq', '');
        */

        $mform->addElement('hidden', 'apitype');
        $mform->setType('apitype', PARAM_TEXT);
        $mform->setDefault('apitype', 'extern');




        $mform->addElement('text', 'realurl', get_string('realapiurl', 'apiproxy'), array('size'=>'48'));
        $mform->setType('realurl', PARAM_TEXT);
        $mform->hideif('realurl', 'apitype', 'eq', 'intern');
        //Canviar maxbytes
        $maxbytes = 10485760;
        $mform->addElement('filemanager', 'attachments', get_string('apifiles', 'apiproxy'), null,
                    array('subdirs' => 0, 'maxbytes' => $maxbytes, 'areamaxbytes' => 10485760, 'maxfiles' => 50,
                          'accepted_types' => array('web_file, sourcecode,web_image,json'), 'return_types'=> FILE_INTERNAL | FILE_EXTERNAL));
        $mform->hideif('attachments', 'apitype', 'eq', 'extern');

        //-----------------API MAPPING---------------        
        //---------------------GET--------
        $mform->addElement('header', 'contentsection2', get_string('contentparametersget', 'apiproxy'));
        $repeatarray = array();
        $repeatarray[] = $mform->createElement('text', 'localparameter', get_string('localparametersget', 'apiproxy'));
        $repeatarray[] = $mform->createElement('text', 'realparameter', get_string('realparametersget', 'apiproxy'));
        $repeatarray[] = $mform->createElement('checkbox', 'requiredparameter', get_string('requiredParameter', 'apiproxy'));
        $repeatarray[] = $mform->createElement('hidden', 'optionid', 0);


        $repeatno = 1;

        $repeateloptions = array();
        $repeateloptions['localparameter']['default'] = 'parameter{no}';
        $repeateloptions['localparameter']['type'] = PARAM_TEXT;
        $repeateloptions['realparameter']['type'] = PARAM_TEXT;
        $repeateloptions['realparameter']['hideif'] = array('apitype', 'eq', 'intern');
        $repeateloptions['requiredparameter']['type'] = PARAM_BOOL;
        $repeateloptions['requiredparameter']['default'] = false;

        $mform->setType('option', PARAM_CLEANHTML);

        $mform->setType('optionid', PARAM_INT);

        $this->repeat_elements($repeatarray, $repeatno,
                    $repeateloptions, 'get_repeats', 'get_add_fields', 1, null, true);

        //---------------------POST--------
        $mform->addElement('header', 'contentsection3', get_string('contentparameterspost', 'apiproxy'));

        $repeatarraypost = array();
        $repeatarraypost[] = $mform->createElement('text', 'localparameterpost', get_string('localparameterspost', 'apiproxy'));
        $repeatarraypost[] = $mform->createElement('text', 'realparameterpost', get_string('realparameterspost', 'apiproxy'));
        $repeatarraypost[] = $mform->createElement('checkbox', 'requiredparameterpost', get_string('requiredParameter', 'apiproxy'));
        $repeatarraypost[] = $mform->createElement('hidden', 'optionidpost', 1);


        $repeatnopost = 1;

        $repeateloptionspost = array();
        $repeateloptionspost['localparameterpost']['default'] = 'parameter{no}';
        $repeateloptionspost['localparameterpost']['type'] = PARAM_TEXT;
        $repeateloptionspost['realparameterpost']['type'] = PARAM_TEXT;
        $repeateloptionspost['realparameterpost']['hideif'] = array('apitype', 'eq', 'intern');
        $repeateloptionspost['requiredparameterpost']['type'] = PARAM_BOOL;
        $repeateloptionspost['requiredparameterpost']['default'] = false;

        $mform->setType('optionpost', PARAM_CLEANHTML);

        $mform->setType('optionidpost', PARAM_INT);

        $this->repeat_elements($repeatarraypost, $repeatnopost,
                    $repeateloptionspost, 'post_repeats', 'post_add_fields', 1, null, true);

        $repeatarrayendpoint = array();
        $repeatarrayendpoint[] = $mform->createElement('text', 'endpoint', get_string('endpoint', 'apiproxy'));


        $repeatnoendpoint = 1;

        $repeateloptionsendpoint = array();
        $repeateloptionsendpoint['endpoint']['type'] = PARAM_TEXT;

        $mform->setType('optionendpoint', PARAM_CLEANHTML);

        $mform->setType('optionidendpoint', PARAM_INT);

        $this->repeat_elements($repeatarrayendpoint, $repeatnoendpoint,
                    $repeateloptionsendpoint, 'endpoint_repeats', 'endpoint_add_fields', 1, null, true);




        //-------------------------------------------------------

        //-------------------------------------------------------
        $this->standard_coursemodule_elements();

        //-------------------------------------------------------
        $this->add_action_buttons();

        //-------------------------------------------------------
        $mform->addElement('hidden', 'revision');
        $mform->setType('revision', PARAM_INT);
        $mform->setDefault('revision', 1);

    }

    /**
     * Enforce defaults here.
     *
     * @param array $defaultvalues Form defaults
     * @return void
     **/
    public function data_preprocessing(&$default_values) {
        
        if(empty($default_values['apiname'])){
            $default_values['apiname'] = null;
        }
        if(empty($default_values['apiname'])){
            $default_values['apiname'] = null;
        }
        if(empty($default_values['realurl'])){
            $default_values['apiname'] = null;
        }
        if(empty($default_values['localparameter'])){
            $default_values['apiname'] = null;
        }
        if(empty($default_values['localparameterpost'])){
            $default_values['apiname'] = null;
        }
        if(empty($default_values['realparameter'])){
            $default_values['apiname'] = null;
        }
        if(empty($default_values['realparameterpost'])){
            $default_values['apiname'] = null;
        }
        if(empty($default_values['attachments'])){
            $default_values['attachments'] = null;
        }

    }
}

