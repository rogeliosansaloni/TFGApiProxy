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
 * API proxy call reciever
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
require_once($CFG->dirroot . '/webservice/lib.php');


$id      = optional_param('id', 0, PARAM_INT); // Course Module ID
$token     = optional_param('token', 0, PARAM_TEXT); // user token
$endpoint     = optional_param('endpoint', 0, PARAM_TEXT); // endpoint
$p       = optional_param('y', 0, PARAM_INT);  // APIProxy instance ID
$inpopup = optional_param('inpopup', 0, PARAM_BOOL);
$type      = optional_param('type', 0, PARAM_TEXT); // Course Module ID


if(!empty($token)){

    $api = new webservice();
    try {
        //Check login
        $user = $api->authenticate_user($token);
        
        if(!apiproxy_get_instance($id)){
            $now = new DateTime("now", core_date::get_server_timezone_object());
            $log = array('apiid' => $id,
                'userid' => $user['user']->id,
                'type' => '-',
                'comment' => 'Fail - APIProxy not found',
                'logtime' => $now->getTimestamp());
            apiproxy_add_log($log);
            echo 'Invalid APIProxy - apiproxy not found';
            exit();
        }else{

            //Check api type and data
            $realurl = apiproxy_get_type($id);
        
            if (strcmp($realurl, '-') == 0) {
                $apitype = true;
            }else{
                $apitype = false;
            }

            $data = apiproxy_get_info($id);

            //Check call type
            switch ($type) {
                case 'post':
                    $params = $data->postparameterslocal;
                    $realparams = $data->postparametersreal;
                    $required = $data->postparametersrequired;
                    $typeBool = true;
                    break;

                case 'get':
                    $params = $data->getparameterslocal;
                    $realparams = $data->getparametersreal;
                    $required = $data->getparametersrequired;
                    $typeBool = false;
                    break;
                
                default:
                    $now = new DateTime("now", core_date::get_server_timezone_object());
                    $log = array('apiid' => $id,
                        'userid' => $user['user']->id,
                        'type' => '-',
                        'comment' => 'Fail - Invalid call type',
                        'logtime' => $now->getTimestamp());
                    apiproxy_add_log($log);
                    echo 'Invalid call type';
                    exit();
                    break;
            }

            $forbidden = array("type", "token", "id", "y", "impopup", "endpoint");
            $finalparams = "";
            $finalparamsOP2 = "";


            //Check and replace (if necessary) parameters
            $paramsPost = array();
            foreach ($_GET as $key => $value) {
                if(!in_array($key, $forbidden)){
                    if (!in_array($key, $params)) {
                        $now = new DateTime("now", core_date::get_server_timezone_object());
                        $log = array('apiid' => $id,
                            'userid' => $user['user']->id,
                            'type' => '-',
                            'comment' => 'Fail - Incorrect parameters',
                            'logtime' => $now->getTimestamp());
                        apiproxy_add_log($log);
                        echo 'Invalid parameters - parmeter not found';
                        exit();
                    }else{
                        if (empty($value) && $required[array_search($key, $params)] == '1') {
                            $now = new DateTime("now", core_date::get_server_timezone_object());
                            $log = array('apiid' => $id,
                                'userid' => $user['user']->id,
                                'type' => '-',
                                'comment' => 'Fail - Empty value',
                                'logtime' => $now->getTimestamp());
                            apiproxy_add_log($log);
                            echo 'Invalid value - empty value';
                            exit();
                        }
                        if (empty($value)){
                            if ($apitype){
                                //$finalparams[$params[array_search($key, $params)]] =  $value;
                                $finalparams .= $params[array_search($key, $params)] . "=&";
    
                            }else{
                                //$finalparams[$realparams[array_search($key, $params)]] =  $value;
                                $finalparams .= $realparams[array_search($key, $params)] . "=&";
                                $finalparamsOP2 .= $realparams[array_search($key, $params)] . "/";
                            }
                            $paramsPost[$realparams[array_search($key, $params)]] = '';
                        }else {
                            if ($apitype){
                                //$finalparams[$params[array_search($key, $params)]] =  $value;
                                $finalparams .= $params[array_search($key, $params)] . "=" .  $value . "&";
    
                            }else{
                                //$finalparams[$realparams[array_search($key, $params)]] =  $value;
                                $finalparams .= $realparams[array_search($key, $params)] . "=" .  $value . "&";
                                $finalparamsOP2 .= $realparams[array_search($key, $params)] . "/" . $value . "/";
                            }
                            $paramsPost[$realparams[array_search($key, $params)]] = $value;
                        }
                        
                    }
                } 
            }
            $finalparams = substr($finalparams, 0, -1); // take the last "&"

            //Control d'Endpoints
            
            if ($typeBool) {
                //post
                $checkEnpoints = explode( '/', $endpoint );
                $finalEndpoint = '';

                foreach ($checkEnpoints as $key => $value) {
                    if (!in_array($value, $data->endpoints)) {
                        $now = new DateTime("now", core_date::get_server_timezone_object());
                        $log = array('apiid' => $id,
                            'userid' => $user['user']->id,
                            'type' => '-',
                            'comment' => 'Fail - Incorrect endpoint',
                            'logtime' => $now->getTimestamp());
                        apiproxy_add_log($log);
                        echo 'Invalid endpoint - endpoint not found';
                        exit();
                    }else{
                        $finalEndpoint .= $value . '/';
                    }
                }
                $finalEndpoint = substr($finalEndpoint, 0, -1);
            }
           
            

            if (!$apitype) {
                if ($typeBool) {
                    //try
                    $finalparams = "title=foo&body=bar&userId=1";
                    if (strcmp("/", substr($realurl, -1)) == 0){    
                        $realurl = substr($realurl, 0, -1);
                    }
                    $realurl .= "/".$finalEndpoint;
                    $paramsPost = json_encode($paramsPost);
                    //POST
                    $now = new DateTime("now", core_date::get_server_timezone_object());
                    $log = array('apiid' => $id,
                        'userid' => $user['user']->id,
                        'type' => 'POST',
                        'comment' => 'Success',
                        'logtime' => $now->getTimestamp());
                    apiproxy_add_log($log);
                    echo apiRedirectPost($realurl, $paramsPost, $id, $user);
                }else{
                    //GET
                    /*
                    //OP1
                    if (strcmp("?", substr($realurl, -1)) == 0){
                        $realurl = substr($realurl, 0, -1);
                    }
                    $url = $realurl . "?" . $finalparams;
                    */
                    //OP2
                    if (strcmp("/", substr($realurl, -1)) == 0){    
                        $realurl = substr($realurl, 0, -1);
                    }
                    $url = $realurl . "/" . $finalparamsOP2;
                    $now = new DateTime("now", core_date::get_server_timezone_object());
                    $log = array('apiid' => $id,
                        'userid' => $user['user']->id,
                        'type' => 'GET',
                        'comment' => 'Success',
                        'logtime' => $now->getTimestamp());
                    apiproxy_add_log($log);
                    echo apiRedirect($url, $id, $user);
                }
            }

            exit();
        }
    } catch (\Throwable $th) {
        $now = new DateTime("now", core_date::get_server_timezone_object());
        $log = array('apiid' => $id,
            'userid' => 0,
            'type' => '-',
            'comment' => 'Error - Token not found',
            'logtime' => $now->getTimestamp());
        apiproxy_add_log($log);
        echo 'Invalid token - token not found';
        exit();
    }
}else{
    $now = new DateTime("now", core_date::get_server_timezone_object());
        $log = array('apiid' => $id,
            'userid' => 0,
            'type' => '-',
            'comment' => 'Error - Token not found',
            'logtime' => $now->getTimestamp());
    apiproxy_add_log($log);
    echo 'Invalid token - token not found';
    exit();
}

function apiRedirect($url, $id, $user) {
    $options = array(
        CURLOPT_RETURNTRANSFER => true,   // return web page
        CURLOPT_HEADER         => false,  // don't return headers
        CURLOPT_FOLLOWLOCATION => true,   // follow redirects
        CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
        CURLOPT_ENCODING       => "",     // handle compressed
        CURLOPT_USERAGENT      => "test", // name of client
        CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
        CURLOPT_TIMEOUT        => 120,    // time-out on response
    ); 
    try {
        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION,
            function($curl, $header) use (&$headers)
            {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                return $len;

                $headers[strtolower(trim($header[0]))][] = trim($header[1]);

                return $len;
            }
            );
        $content  = curl_exec($ch);
        $err = curl_error($ch);

        if ($err) {
            echo "cURL Error #:" . $err;
            $now = new DateTime("now", core_date::get_server_timezone_object());
            $log = array('apiid' => $id,
                'userid' => $user['user']->id,
                'type' => '-',
                'comment' => 'Fail - External API internal Error',
                'logtime' => $now->getTimestamp());
            apiproxy_add_log($log);
        } else {

            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            

            if (isset($headers['date'])) {
                header('date:'. $headers['date'][0]);
            }
            if (isset($headers['content-type'])) {
                header('content-type:'. $headers['content-type'][0]);
            }
            if (isset($headers['connection'])) {
                header('connection:'. $headers['connection'][0]);
            }

            http_response_code($http_code);
            return $content;
        }

    } catch (\Throwable $th) {

        $now = new DateTime("now", core_date::get_server_timezone_object());
        $log = array('apiid' => $id,
            'userid' => $user['user']->id,
            'type' => '-',
            'comment' => 'Fail - External API internal Error',
            'logtime' => $now->getTimestamp());
        apiproxy_add_log($log);
        return 'Failed call';
    }
}

function apiRedirectPost($url, $finalparams, $id, $user) {

    $options = array(
        CURLOPT_POST           => 1,
        CURLOPT_RETURNTRANSFER => true,   // return web page
        CURLOPT_HEADER	       => "Content-Type: application/json",
        CURLOPT_POSTFIELDS     => $finalparams,
    ); 

    try {
        
        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION,
            function($curl, $header) use (&$headers)
            {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                return $len;

                $headers[strtolower(trim($header[0]))][] = trim($header[1]);

                return $len;
            }
            );
        $content  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        

        if (isset($headers['date'])) {
            header('date:'. $headers['date'][0]);
        }
        if (isset($headers['content-type'])) {
            header('content-type:'. $headers['content-type'][0]);
        }
        if (isset($headers['connection'])) {
            header('connection:'. $headers['connection'][0]);
        }

        http_response_code($http_code);
        
        return $content;

    } catch (\Exception $e) {

        $now = new DateTime("now", core_date::get_server_timezone_object());
        $log = array('apiid' => $id,
            'userid' => $user['user']->id,
            'type' => '-',
            'comment' => 'Fail - External API internal Error',
            'logtime' => $now->getTimestamp());
        apiproxy_add_log($log);
        return 'Failed call';
    }
}