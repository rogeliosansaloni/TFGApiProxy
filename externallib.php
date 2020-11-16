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
 * APIProxy external API
 *
 * @package mod_apiproxy
 * @category   external
 * @copyright  2020 Daniel Amo, Oriol Pando
 *  daniel.amo@salle.url.edu
 *  oriolpando@gmail.com
 * @copyright  2020 La Salle Campus Barcelona, Universitat Ramon Llull https://www.salleurl.edu
 * @author     Daniel Amo
 * @author     Oriol Pando
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * apiproxy external functions
 *
 * @package     mod_apiproxy
 * @category    external
 * @copyright   2019-2020 Oriol Pando, Daniel Amo
 * @author      Oriol Pando <oriol.pando@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_apiproxy_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function view_apiproxy_parameters() {
        return new external_function_parameters(
            array(
                'apiproxyid' => new external_value(PARAM_INT, 'apiproxy instance id')
            )
        );
    }

    /**
     * Simulate the apiproxy/view.php web interface apiproxy: trigger events, completion, etc...
     *
     * @param int $apiproxyid the apiproxy instance id
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function view_apiproxy($apiproxyid) {
        global $DB, $CFG;
        require_once($CFG->dirroot . "/mod/apiproxy/lib.php");

        $params = self::validate_parameters(self::view_apiproxy_parameters(),
                                            array(
                                                'apiproxyid' => $apiproxyid
                                            ));
        $warnings = array();

        // Request and permission validation.
        $apiproxy = $DB->get_record('apiproxy', array('id' => $params['apiproxyid']), '*', MUST_EXIST);
        list($course, $cm) = get_course_and_cm_from_instance($apiproxy, 'apiproxy');

        $context = context_module::instance($cm->id);
        self::validate_context($context);

        require_capability('mod/apiproxy:view', $context);

        // Call the apiproxy/lib API.
        apiproxy_view($apiproxy, $course, $cm, $context);

        $result = array();
        $result['status'] = true;
        $result['warnings'] = $warnings;
        return $result;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function view_apiproxy_returns() {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_BOOL, 'status: true if success'),
                'warnings' => new external_warnings()
            )
        );
    }

    /**
     * Describes the parameters for get_apiproxy_by_courses.
     *
     * @return external_function_parameters
     * @since Moodle 3.3
     */
    public static function get_apiproxy_by_courses_parameters() {
        return new external_function_parameters (
            array(
                'courseids' => new external_multiple_structure(
                    new external_value(PARAM_INT, 'Course id'), 'Array of course ids', VALUE_DEFAULT, array()
                ),
            )
        );
    }

    /**
     * Returns a list of apiproxys in a provided list of courses.
     * If no list is provided all apiproxys that the user can view will be returned.
     *
     * @param array $courseids course ids
     * @return array of warnings and apiproxys
     * @since Moodle 3.3
     */
    public static function get_apiproxy_by_courses($courseids = array()) {

        $warnings = array();
        $returnedapiproxys = array();

        $params = array(
            'courseids' => $courseids,
        );
        $params = self::validate_parameters(self::_by_courses_parameters(), $params);

        $mycourses = array();
        if (empty($params['courseids'])) {
            $mycourses = enrol_get_my_courses();
            $params['courseids'] = array_keys($mycourses);
        }

        // Ensure there are courseids to loop through.
        if (!empty($params['courseids'])) {

            list($courses, $warnings) = external_util::validate_courses($params['courseids'], $mycourses);

            // Get the apiproxys in this course, this function checks users visibility permissions.
            // We can avoid then additional validate_context calls.
            $apiproxys = get_all_instances_in_courses("apiproxy", $courses);
            foreach ($apiproxys as $apiproxy) {
                $context = context_module::instance($apiproxy->coursemodule);
                // Entry to return.
                $apiproxy->name = external_format_string($apiproxy->name, $context->id);

                $options = array('noclean' => true);
                list($apiproxy->intro, $apiproxy->introformat) =
                    external_format_text($apiproxy->intro, $apiproxy->introformat, $context->id, 'mod_apiproxy', 'intro', null, $options);
                $apiproxy->introfiles = external_util::get_area_files($context->id, 'mod_apiproxy', 'intro', false, false);

                $options = array('noclean' => true);
                list($apiproxy->content, $apiproxy->contentformat) = external_format_text($apiproxy->content, $apiproxy->contentformat,
                                                                $context->id, 'mod_apiproxy', 'content', $apiproxy->revision, $options);
                $apiproxy->contentfiles = external_util::get_area_files($context->id, 'mod_apiproxy', 'content');

                $returnedapiproxys[] = $apiproxy;
            }
        }

        $result = array(
            'apiproxys' => $returnedapiproxys,
            'warnings' => $warnings
        );
        return $result;
    }

    /**
     * Describes the get_apiproxy_by_courses return value.
     *
     * @return external_single_structure
     * @since Moodle 3.3
     */
    public static function get_apiproxy_by_courses_returns() {
        return new external_single_structure(
            array(
                'apiproxys' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Module id'),
                            'coursemodule' => new external_value(PARAM_INT, 'Course module id'),
                            'course' => new external_value(PARAM_INT, 'Course id'),
                            'name' => new external_value(PARAM_RAW, 'apiproxy name'),
                            'intro' => new external_value(PARAM_RAW, 'Summary'),
                            'introformat' => new external_format_value('intro', 'Summary format'),
                            'introfiles' => new external_files('Files in the introduction text'),
                            'content' => new external_value(PARAM_RAW, 'apiproxy content'),
                            'contentformat' => new external_format_value('content', 'Content format'),
                            'contentfiles' => new external_files('Files in the content'),
                            'legacyfiles' => new external_value(PARAM_INT, 'Legacy files flag'),
                            'legacyfileslast' => new external_value(PARAM_INT, 'Legacy files last control flag'),
                            'display' => new external_value(PARAM_INT, 'How to display the apiproxy'),
                            'displayoptions' => new external_value(PARAM_RAW, 'Display options (width, height)'),
                            'revision' => new external_value(PARAM_INT, 'Incremented when after each file changes, to avoid cache'),
                            'timemodified' => new external_value(PARAM_INT, 'Last time the apiproxy was modified'),
                            'section' => new external_value(PARAM_INT, 'Course section id'),
                            'visible' => new external_value(PARAM_INT, 'Module visibility'),
                            'groupmode' => new external_value(PARAM_INT, 'Group mode'),
                            'groupingid' => new external_value(PARAM_INT, 'Grouping id'),
                        )
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }
}
