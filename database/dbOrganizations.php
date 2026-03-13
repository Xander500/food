<?php
/*
 * Copyright 2013 by Jerrick Hoang, Ivy Xing, Sam Roberts, James Cook,
 * Johnny Coster, Judy Yang, Jackson Moniaga, Oliver Radwan,
 * Maxwell Palmer, Nolan McNair, Taylor Talmage, and Allen Tucker.
 * This program is part of RMH Homebase, which is free software.  It comes with
 * absolutely no warranty. You can redistribute and/or modify it under the terms
 * of the GNU General Public License as published by the Free Software Foundation
 * (see <http://www.gnu.org/licenses/ for more information).
 *
 */

include_once('dbinfo.php');
include_once(dirname(__FILE__).'/../domain/Organization.php');

function make_an_organization($result_row) {
    //takes sql query results
    $theOrg = new Organization(
    @$result_row['id'],
    @$result_row['name'],
    @$result_row['email'],
    @$result_row['location'],
    @$result_row['description'],
);
    return $theOrg;
}

function get_organization_name_from_id($id) {
    $con=connect();
    $query = 'SELECT name FROM dborganizations WHERE id = "' . $id . '"';
    $result = mysqli_query($con,$query);
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_close($con);
        return false;
    }
    $result_row = mysqli_fetch_assoc($result);
    mysqli_close($con);
    return $result_row['name'];
}

//used to get the full list of organizations of their id and name, used for dropdown in addEvent.php
function get_organizations_id_name() {
    $con=connect();
    $query = "SELECT id, name FROM dborganizations";
    $result = mysqli_query($con,$query);

    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_close($con);
        return false;
    }
    mysqli_close($con);
    return $result;
}
