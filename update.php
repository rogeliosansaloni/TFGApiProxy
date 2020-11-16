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
 * API proxy module version information
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

if (strcmp($_SERVER['REQUEST_METHOD'],'POST') != 0) {
    redirect($CFG->wwwroot);
}

if (isset($_POST['gotologs'])) {
    redirect($CFG->wwwroot . '/mod/apiproxy/logs.php?id='. $_POST['cm']);
}

$url = $_POST['apiurlf'];

if (isset($_POST['cancel'])) {
    redirect($url);
    exit();
}else{

    if (strcmp($_POST['apitype'], 'intern')==0) {
        $apiproxy = array('id'=>$_POST['apiid'], 'name'=>$_POST['apiname'],'realurl'=>'-','intro'=>$_POST['intro']);
    }else{
        $apiproxy = array('id'=>$_POST['apiid'], 'name'=>$_POST['apiname'],'realurl'=>$_POST['realurl'], 'intro'=>$_POST['intro'], 
            'localparameter'=>$_POST['localparameter'],'realparameter'=>$_POST['realparameter'],'localparameterpost'=>$_POST['localparameterpost'],
                'realparameterpost'=>$_POST['realparameterpost'], 'requiredparameter'=>$_POST['requiredparameter'], 
                    'requiredparameterpost'=>$_POST['requiredparameterpost'], 'endpoint'=>$_POST['endpoint']);
    }
    if(apiproxy_update_instance($apiproxy)){
        $msg = 'APIProxy correctly updated!';
    }else{
        $msg = 'Error at updating apiproxy!';
    };
    
    redirect($url, $msg);
    exit();
}

