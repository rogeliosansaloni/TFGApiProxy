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
 * API Proxy module admin settings and defaults
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

if ($ADMIN->fulltree) {
    require_once("$CFG->libdir/resourcelib.php");

    $displayoptions = resourcelib_get_displayoptions(array(RESOURCELIB_DISPLAY_OPEN, RESOURCELIB_DISPLAY_POPUP));
    $defaultdisplayoptions = array(RESOURCELIB_DISPLAY_OPEN);

    //--- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_configmultiselect('apiproxy/displayoptions',
        get_string('displayoptions', 'apiproxy'), get_string('configdisplayoptions', 'apiproxy'),
        $defaultdisplayoptions, $displayoptions));

    //--- modedit defaults -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('apiproxymodeditdefaults', get_string('modeditdefaults', 'admin'), get_string('condifmodeditdefaults', 'admin')));

    $settings->add(new admin_setting_configcheckbox('apiproxy/printheading',
        get_string('printheading', 'apiproxy'), get_string('printheadingexplain', 'apiproxy'), 1));
    $settings->add(new admin_setting_configcheckbox('apiproxy/printintro',
        get_string('printintro', 'apiproxy'), get_string('printintroexplain', 'apiproxy'), 0));
    $settings->add(new admin_setting_configcheckbox('apiproxy/printlastmodified',
        get_string('printlastmodified', 'apiproxy'), get_string('printlastmodifiedexplain', 'apiproxy'), 1));
    $settings->add(new admin_setting_configselect('apiproxy/display',
        get_string('displayselect', 'apiproxy'), get_string('displayselectexplain', 'apiproxy'), RESOURCELIB_DISPLAY_OPEN, $displayoptions));
    $settings->add(new admin_setting_configtext('apiproxy/popupwidth',
        get_string('popupwidth', 'apiproxy'), get_string('popupwidthexplain', 'apiproxy'), 620, PARAM_INT, 7));
    $settings->add(new admin_setting_configtext('apiproxy/popupheight',
        get_string('popupheight', 'apiproxy'), get_string('popupheightexplain', 'apiproxy'), 450, PARAM_INT, 7));
}
