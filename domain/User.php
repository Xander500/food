<?php
/*
 * Copyright 2013 by Allen Tucker.
 * This program is part of RMHC-Homebase, which is free software.  It comes with
 * absolutely no warranty. You can redistribute and/or modify it under the terms
 * of the GNU General Public License as published by the Free Software Foundation
 * (see <http://www.gnu.org/licenses/ for more information).
 *
 */

/*
 * Created on Mar 28, 2008
 * @author Oliver Radwan <oradwan@bowdoin.edu>, Sam Roberts, Allen Tucker
 * @version 3/28/2008, revised 7/1/2015
 */


class User {

	private $id; // (username)
	private $start_date; // (date of account creation)
	private $first_name;
	private $last_name;
	private $email;
	private $password;
    private $role; //from type //student or instructor
    private $semester;
	private $archived;

	function __construct(
        $id, $start_date, $first_name, $last_name, $email, $password, $role, $semester, $archived) {
        $this->id = $id;
		$this->start_date = $start_date;
		$this->first_name = $first_name;
		$this->last_name = $last_name;
		$this->email = $email;
		$this->password = $password;
		$this->role = $role;
		$this->semester = $semester;
		$this->archived = $archived;
    }


	function get_id() {
		return $this->id;
	}

	function get_start_date() {
		return $this->start_date;
	}

	function get_first_name() {
		return $this->first_name;
	}

	function get_last_name() {
		return $this->last_name;
	}

	function get_email() {
		return $this->email;
	}

	function get_role() {
		return $this->role;
	}

	function get_password() {
		return $this->password;
	}

    function get_semester() {
		return $this->semester;
	}

	function is_archived() {
		return $this->archived;
	}

    //! check out
	function get_access_level() {
        if ($this->role == "Student") {
            return 1;
        }
        else if ($this->role == "Instructor") {
            return 3;
        }
        else if ($this->id == 'vmsroot') { // ideally remove this once we have vmsroot as a Instructor, its currently null in the database
            return 3;
        }
        return 0;
	}

}
