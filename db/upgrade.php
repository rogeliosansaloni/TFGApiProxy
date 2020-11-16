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
 * APIProxy module upgrade code
 *
 * This file keeps track of upgrades to
 * the resource module
 *
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 *
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installation to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the methods of database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish. 
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

function xmldb_apiproxy_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2020051004) {

        // Define table apiproxy to be dropped.
        $table = new xmldb_table('apiproxy');

        // Conditionally launch drop table for apiproxy.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table apiproxy to be created.
        $table = new xmldb_table('apiproxy');

        // Adding fields to table apiproxy.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('realurl', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('intro', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('revision', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table apiproxy.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table apiproxy.
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, ['course']);

        // Conditionally launch create table for apiproxy.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table apiproxy_parameters to be dropped.
        $table = new xmldb_table('apiproxy_parameters');

        // Conditionally launch drop table for apiproxy_parameters.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table apiproxy_parameters to be created.
        $table = new xmldb_table('apiproxy_parameters');

        // Adding fields to table apiproxy_parameters.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('apiid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
        $table->add_field('localparameter', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('realparameter', XMLDB_TYPE_CHAR, '255', null, null, null, 'null');
        $table->add_field('type', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, 'get');
        $table->add_field('required', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '1');


        // Adding keys to table apiproxy_parameters.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('apiid', XMLDB_KEY_FOREIGN, ['apiid'], 'apiproxy', ['id']);

        // Conditionally launch create table for apiproxy_parameters.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table apiproxy_logs to be dropped.
        $table = new xmldb_table('apiproxy_logs');

        // Conditionally launch drop table for apiproxy_logs.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table apiproxy_logs to be created.
        $table = new xmldb_table('apiproxy_logs');

        // Adding fields to table apiproxy_logs.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('apiid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
        $table->add_field('type', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, '-');
        $table->add_field('comment', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, '-');
        $table->add_field('logtime', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table apiproxy_logs.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('apiid', XMLDB_KEY_FOREIGN, ['apiid'], 'apiproxy', ['id']);


        // Conditionally launch create table for apiproxy_logs.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }


        // Define table apiproxy_endpoints to be dropped.
        $table = new xmldb_table('apiproxy_endpoints');

        // Conditionally launch drop table for apiproxy_endpoints.
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Define table apiproxy_endpoints to be created.
        $table = new xmldb_table('apiproxy_endpoints');

        // Adding fields to table apiproxy_endpoints.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('apiid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, 0);
        $table->add_field('endpoint', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, '-');

        // Adding keys to table apiproxy_endpoints.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('apiid', XMLDB_KEY_FOREIGN, ['apiid'], 'apiproxy', ['id']);


        // Conditionally launch create table for apiproxy_endpoints.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // APIProxy savepoint reached.
        upgrade_mod_savepoint(true, 2020051004, 'apiproxy');
    }


    return true;
}
