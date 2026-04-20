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
    @$result_row['archived']
    );
    return $theOrg;
}

function add_organization($org) {
    $con = connect();

    $name = $org->get_name();
    $email = $org->get_email();
    $description = $org->get_description();
    $location = $org->get_location();

    $sql = 'SELECT * FROM dborganizations WHERE name = ?';
    $query = $con->prepare($sql);
    $query->bind_param("s", $name);
    $query->execute();
    $result = $query->get_result();

    // If the result is empty, it means the org doesn't exist, so we can add the org
    if (mysqli_num_rows($result) == 0) {
        // Prepare the insert query
        $insert_query = 'INSERT INTO dborganizations (name, email, description, location) VALUES (?, ?, ?, ?)';  

        // Check if the query is properly built
        if (empty($insert_query)) {
            die("Error: insert query is empty");
        }

        // Perform the insert
        $insert = $con->prepare($insert_query);
        $insert->bind_param("ssss", $name, $email, $description, $location);
        if ($insert->execute()) {
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
    $sql = 'SELECT name FROM dborganizations WHERE id = ?';
    $query = $con->prepare($sql);
    $query->bind_param("s", $id);
    $query->execute();
    $result = $query->get_result();
    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_close($con);
        return false;
    }
    $result_row = mysqli_fetch_assoc($result);
    mysqli_close($con);
    return $result_row['name'];
}

//used to get the full list of organizations of their id and name, used for dropdown in addEvent.php
function get_organizations_id_name($want_archived = false) { 
    $con=connect();
    $query = "SELECT id, name FROM dborganizations WHERE archived = 0";
    if ($want_archived) {
        //default to showing active orgs, but allow archived orgs to be shown if $want_archived is true
        $query .= " or archived = 1";
    }
    $result = mysqli_query($con,$query);

    if ($result == null || mysqli_num_rows($result) == 0) {
        mysqli_close($con);
        return false;
    }
    mysqli_close($con);
    return $result;
}

function find_organizations($name, $location) {
    $connection = connect();

if ($name && $location) {
    $sql = 'SELECT * FROM dborganizations WHERE name LIKE ? AND location LIKE ?';
    $query = $connection->prepare($sql);

    $nameLike = "%$name%";
    $locationLike = "%$location%";

    $query->bind_param("ss", $nameLike, $locationLike);

} else if ($name) {
    $sql = 'SELECT * FROM dborganizations WHERE name LIKE ?';
    $query = $connection->prepare($sql);

    $nameLike = "%$name%";
    $query->bind_param("s", $nameLike);

} else if ($location) {
    $sql = 'SELECT * FROM dborganizations WHERE location LIKE ?';
    $query = $connection->prepare($sql);

    $locationLike = "%$location%";
    $query->bind_param("s", $locationLike);

} else {
    return [];
}

$query->execute();
$result = $query->get_result();

    // echo $query;
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
    $archived = $details['archived'];

    $sql = 'UPDATE dborganizations SET name=?, email=?, description=?, location=?, archived=? WHERE id=?';

    $query = $connection->prepare($sql);
    $query->bind_param("ssssss", $name, $email, $description, $location, $archived, $id);

    if ($query->execute()) {
        mysqli_commit($connection);
        mysqli_close($connection);
        return true;
    } 
        mysqli_close($connection);
    return false;

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
    $sql = "DELETE FROM dborganizations WHERE id = ?";
    $query = $con->prepare($sql);
    $query->bind_param("s", $id);

    if (!$query->execute()) {
        mysqli_close($con);
        return false;
    }

    $affected = $query->affected_rows;
    mysqli_close($con);

    return $affected > 0; 
}

// used to fetch organizaton information for exporting.
function fetch_organizations($archived = '0') {
    $con=connect();
    $sql = "SELECT * FROM dborganizations WHERE (dborganizations.archived = 0 OR ? = 1)";
    $query = $con->prepare($sql);
    $query->bind_param("s", $archived);
    $query->execute();
    $result = $query->get_result();
    mysqli_close($con);

    if ($result == null || mysqli_num_rows($result) == 0) {
        return false;
    }
    return $result;
}