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

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/mod/apiproxy/locallib.php');
require_once($CFG->dirroot.'/mod/apiproxy/lib.php');



class mod_apiproxy_log_form extends moodleform{
    //Add elements to form
    public function definition() {
        global $CFG;

        $config = get_config('apiproxy');

        $cm = context_module::instance($_SESSION['cmid']);

        $users = get_enrolled_users($cm);

        $mform = $this->_form; // Don't forget the underscore! 

        //$mform->addElement('submit', 'search', 'Search');

        /*
        $selection = array();
        array_push($selection, "All");
        foreach ($users as $element) {
            array_push($selection, $element->firstname . ' ' . $element->lastname);
        }
        $select = $mform->addElement('select', 'colors', 'Users', $selection, $attributes);
        $select->setMultiple(true);
        */ 

        $info = apiproxy_get_log($_SESSION['apid'], 0);
        $logs = array();

        $htmlString = '<div id="example-table"></div>';

        
        $htmlString = '<div id="example-table" class=\'info\' ><table class=\'main\'><tr><th>User</th><th>Call Type</th><th>Content</th><th>Log Time</th></tr>';
        
        foreach ($info as $element) { 

            $user = apiproxy_get_username($element->userid);
            
            if(!$user){
                $username = '-'; 
            }else{
                $username = $user->firstname . ' ' . $user->lastname;
            }

            $time = date("F j, Y, g:i a", $element->logtime);
            $htmlString .= '<tr><td>' . $username . '</td><td>' . $element->type . '</td>
                <td>' . $element->comment . '</td><td>' . $time . '</td></tr>';

            
            $log = new stdClass();
            $log->user = $username;
            $log->type = $element->type;
            $log->comment = $element->comment;
            $log->time = $time;

            $logs[] = $log;
        }
        
        
        $mform->addElement('html', $htmlString . '</table>');

        $totalReq = apiproxy_get_stat($_SESSION['apid'], true, 10);

        $totalSucces = apiproxy_get_stat($_SESSION['apid'], true, 0);
        $successGet = apiproxy_get_stat($_SESSION['apid'], true, 1);
        $successPost = apiproxy_get_stat($_SESSION['apid'], true, 2);

        
        $htmlString = '';
        $htmlString = '<table class=\'success\'><tr><th>Success Type</th><th>Nº</th></tr>';
        $htmlString .= '<tr><td>Total Requests</td><td>' . $totalReq . '</td></tr>';
        $htmlString .= '<tr><td>Total Successes</td><td>' . $totalSucces . '</td></tr>';
        $htmlString .= '<tr><td>GET Successes</td><td>' . $successGet . '</td></tr>';
        $htmlString .= '<tr><td>POST Successes</td><td>' . $successPost . '</td></tr>';

        $mform->addElement('html', $htmlString . '</table>');

        $totalFails = apiproxy_get_stat($_SESSION['apid'], false, 0);
        $incorrectParams = apiproxy_get_stat($_SESSION['apid'], false, 1);
        $incorrectValues = apiproxy_get_stat($_SESSION['apid'], false, 2);
        $internalError = apiproxy_get_stat($_SESSION['apid'], false, 3);


        $htmlString = '';
        $htmlString = '<table class=\'fail\'><tr><th>Error Type</th><th>Nº</th></tr>';
        $htmlString .= '<tr><td>Total Requests</td><td>' . $totalReq . '</td></tr>';
        $htmlString .= '<tr><td>Total Fails</td><td>' . $totalFails . '</td></tr>';
        $htmlString .= '<tr><td>Malformed Strings</td><td>' . $incorrectValues . '</td></tr>';
        $htmlString .= '<tr><td>Incorrect Parameters</td><td>' . $incorrectParams . '</td></tr>';
        $htmlString .= '<tr><td>Incorrect Values</td><td>' . $incorrectValues . '</td></tr>';
        $htmlString .= '<tr><td>External API Internal Errors</td><td>' . $internalError . '</td></tr>';

        $mform->addElement('html', $htmlString . '</table></div>');

        $mform->addElement('html', '<a class="qheader">');

        $_SESSION['student_logs'] = $logs;
        //$mform->addElement('submit', 'download', get_string('downloadLogs','apiproxy'));

    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
