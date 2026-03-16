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
    $theUser = new User(
    @$result_row['id'],
    @$result_row['start_date'],
    @$result_row['first_name'],
    @$result_row['last_name'],
    @$result_row['email'],
    @$result_row['password'],
    @$result_row['role'],
    @$result_row['semester']
);
    return $theUser;
}

function get_user_full_name_from_id($id) {
    $con=connect();
    $query = 'SELECT first_name,last_name,id FROM dbusers WHERE id = "' . $id . '"';
    $result = mysqli_query($con,$query);
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_close($con);
        return false;
    }
    $result_row = mysqli_fetch_assoc($result);
    mysqli_close($con);
    return $result_row['first_name'] . " " . $result_row['last_name'];
}

  function get_semesters_in_users() {
    $con=connect();
    $query = "SELECT DISTINCT semester FROM dbusers" .
        " ORDER BY semester asc";
    $result = mysqli_query($con,$query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    return $rows;
 }

 function add_user($user) {
    $con = connect();
    $query = "SELECT * FROM dbusers WHERE id = '" . $user->get_id() . "'";
    $result = mysqli_query($con, $query);
    // if (!$user instanceof user) {
    //     die("Error: add_user type mismatch");
    // }

    

    // If the result is empty, it means the user doesn't exist, so we can add the user
    if (mysqli_num_rows($result) == 0) {
        // Prepare the insert query
        $insert_query = 'INSERT INTO dbusers (id, start_date, first_name, last_name, email, password, role, semester) 
            VALUES ("' .
            $user->get_id() . '","' .
            $user->get_start_date() . '","' .
            $user->get_first_name() . '","' .
            $user->get_last_name() . '","' .
            $user->get_email() . '","' .
            $user->get_password() . '","' .
            $user->get_role() . '","' . 
            $user->get_semester() . '");';  
    
        // Check if the query is properly built
        if (empty($insert_query)) {
            die("Error: insert query is empty");
        }

        // Perform the insert
        if (mysqli_query($con, $insert_query)) {
            mysqli_close($con);
            return true;
        } else {
            die("Error: " . mysqli_error($con)); // Debugging MySQL error
        }
    }

    mysqli_close($con);
    return false;
}

/*
 * @return a User from dbUsers table matching a particular id.
 * if not in table, return false
 */

function retrieve_user($id) { // (username! not id)
    $con=connect();
    $query = "SELECT * FROM dbusers WHERE id = '" . $id . "'";
    $result = mysqli_query($con,$query);
    if (mysqli_num_rows($result) !== 1) {
        mysqli_close($con);
        return false;
    }
    $result_row = mysqli_fetch_assoc($result);
    // var_dump($result_row);
    $theUser = make_a_user($result_row);
//    mysqli_close($con);
    return $theUser;
}