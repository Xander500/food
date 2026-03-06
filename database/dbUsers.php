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
include_once(dirname(__FILE__).'/../domain/User.php');

function make_a_user($result_row) {
    //takes sql query results
    $thePerson = new User(
    @$result_row['id'],
    @$result_row['start_date'],
    @$result_row['first_name'],
    @$result_row['last_name'],
    @$result_row['email'],
    @$result_row['password'],
    @$result_row['role'],
    @$result_row['semester']
);
    return $thePerson;
}

function get_user_full_name_from_id($id) {
    $con=connect();
    $query = 'SELECT first_name,last_name,username FROM dbusers WHERE id = "' . $id . '"';
    $result = mysqli_query($con,$query);
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_close($con);
        return false;
    }
    $result_row = mysqli_fetch_assoc($result);
    mysqli_close($con);
    return $result_row['first_name'] . " " . $result_row['last_name'];
}
