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

function add_organization($org) {
    $con = connect();
    $query = "SELECT * FROM dborganizations WHERE name = '" . $org->get_name() . "'";
    $result = mysqli_query($con, $query);

    // If the result is empty, it means the org doesn't exist, so we can add the org
    if (mysqli_num_rows($result) == 0) {
        // Prepare the insert query
        $insert_query = 'INSERT INTO dborganizations (name, email, description, location) 
            VALUES ("' .
            $org->get_name() . '","' .
            $org->get_email() . '","' .
            $org->get_description() . '","' .
            $org->get_location() . '");';  
    
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

function find_organizations($name, $location) {
    if ($name && $location) {
        $query = 'select * from dborganizations where name like "%' . $name . '%" and location like "%' . $location . '%";';
    } else if ($name) {
        $query = 'select * from dborganizations where name like "%' . $name . '%";';
    } else if ($location) {
        $query = 'select * from dborganizations where location like "%' . $location . '%";';
    } else {
        return [];
    }
    // echo $query;
    $connection = connect();
    $result = mysqli_query($connection, $query);
    if (!$result) {
        mysqli_close($connection);
        return [];
    }
    $raw = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $orgs = [];
    foreach ($raw as $row) {
        $orgs []= make_an_organization($row);
    }
    mysqli_close($connection);
    return $orgs;
}

function update_organization($id, $details) {
    $connection = connect();
    
    $id = $details['id'];
    $name = $details['name'];
    $email = $details['email'];
    $description = $details['description'];
    $location = $details['location'];

    $query = "
        UPDATE dborganizations
        SET name='$name', email='$email', description='$description', location='$location'
        WHERE id='$id'
    ";
    $result = mysqli_query($connection, $query);
    mysqli_commit($connection);
    mysqli_close($connection);
    return $result;
}

function fetch_organization_by_id($id) {
    $connection = connect();
    $id = mysqli_real_escape_string($connection, $id);
    $query = $connection->prepare("select * from dborganizations where id = ?");
    $query->bind_param("i", $id); //protect from sql injection
    $query->execute();

    $result = $query->get_result();
    $org = mysqli_fetch_assoc($result);
    if ($org) {
        require_once('include/output.php');
        $org = hsc($org);
        mysqli_close($connection);
        return $org;
    }
    mysqli_close($connection);
    return null;
}

function delete_organization($id) {
    $con=connect();
    $query = "DELETE FROM dborganizations WHERE id = '$id'";
    $result = mysqli_query($con, $query);
    $result = boolval($result);
    mysqli_close($con);
    return $result;
}