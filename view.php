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


require('../../config.php');
require_once($CFG->dirroot.'/mod/apiproxy/lib.php');
require_once($CFG->dirroot.'/mod/apiproxy/locallib.php');
require_once($CFG->libdir.'/completionlib.php');
require_once($CFG->dirroot.'/mod/apiproxy/view_form.php');


$id      = optional_param('id', 0, PARAM_INT); // Course Module ID
$p       = optional_param('y', 0, PARAM_INT);  // APIProxy instance ID
$inpopup = optional_param('inpopup', 0, PARAM_BOOL);

if ($p) {
    if (!$apiproxy = $DB->get_record('apiproxy', array('id'=>$p))) {
        print_error('invalidaccessparameter');
    }
    $cm = get_coursemodule_from_instance('apiproxy', $apiproxy->id, $apiproxy->course, false, MUST_EXIST);

} else {
    if (!$cm = get_coursemodule_from_id('apiproxy', $id)) {
        print_error('invalidcoursemodule');
    }
    $apiproxy = $DB->get_record('apiproxy', array('id'=>$cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);


require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/apiproxy:view', $context);

// Completion and trigger events.
apiproxy_view($apiproxy, $course, $cm, $context);

$PAGE->set_url('/mod/apiproxy/view.php', array('id' => $cm->id));

$options = empty($apiproxy->displayoptions) ? array() : unserialize($apiproxy->displayoptions);

if ($inpopup and $apiproxy->display == RESOURCELIB_DISPLAY_POPUP) {
    $PAGE->set_pagelayout('popup');
    $PAGE->set_title($course->shortname.': '.$apiproxy->name);
    $PAGE->set_heading($course->fullname);
} else {
    $PAGE->set_title($course->shortname.': '.$apiproxy->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($apiproxy);
}
echo $OUTPUT->header();
if (!isset($options['printheading']) || !empty($options['printheading'])) {
    echo $OUTPUT->heading(format_string($apiproxy->name), 2);
}
if (!empty($options['printintro'])) {
    if (trim(strip_tags($apiproxy->intro))) {
        echo $OUTPUT->box_start('mod_introbox', 'pageintro');
        echo format_module_intro('apiproxy', $apiproxy, $cm->id);
        echo $OUTPUT->box_end();
    }
}

$content = file_rewrite_pluginfile_urls($apiproxy->intro, 'pluginfile.php', $context->id, 'mod_apiproxy', 'content', $apiproxy->revision);
$formatoptions = new stdClass;
$formatoptions->noclean = true;
$formatoptions->overflowdiv = true;
$formatoptions->context = $context;
$content = format_text($content, $formatoptions);
echo $OUTPUT->box($content, "generalbox center clearfix");

$apiproxy->cm = $cm;
$_SESSION['apiproxy'] = $apiproxy;
$mform = new mod_apiproxy_view_form('update.php');
$mform->display();

$PAGE->requires->js('/mod/apiproxy/js/script.js');


if (!isset($options['printlastmodified']) || !empty($options['printlastmodified'])) {
    $strlastmodified = get_string("lastmodified");
    echo html_writer::div("$strlastmodified: " . userdate($apiproxy->timemodified), 'modified');
}




echo $OUTPUT->footer();
