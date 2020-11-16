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
 * API Proxy javascript
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

window.onload = function() {  
    document.getElementById("id_gotologs").setAttribute("onclick", "checkForm()");
    document.getElementById("id_cancel").setAttribute("onclick", "checkFormCancel()");
    document.getElementById("id_submitbutton").setAttribute("onclick", "checkForm()");

};

function updatePostParams() {
    document.getElementById("updatePost").style.display = "block";
    document.getElementsByTagName("form")[0].action = '';
};

function updateGetParams() {
    document.getElementById("updateGet").style.display = "block";
    document.getElementsByTagName("form")[0].action = '';
};

function updateEndpointParams() {
    document.getElementById("updateEndpoint").style.display = "block";
    document.getElementsByTagName("form")[0].action = '';
};

function checkForm() {
    document.getElementsByTagName("form")[0].action = 'update.php';
    return true;
}

function checkFormCancel(){
    //Parche
    document.getElementsByTagName("form")[0].action = 'update.php';
    skipClientValidation = true;
    return true;

} 
