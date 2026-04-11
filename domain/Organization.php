<?php
/*
 * Copyright 2026 by Laura Rider. 
 * This program is part of Majid Volunteer Impact Tracking System, which is free software.  It comes with 
 * absolutely no warranty. You can redistribute and/or modify it under the terms 
 * of the GNU General Public License as published by the Free Software Foundation
 * (see <http://www.gnu.org/licenses/ for more information).
 * 
 */

class Organization {

	private $id;
	private $name;
	private $email;
	private $location;
    private $description;
    private $archived;


	function __construct($id, $name, $email, $location, $description, $archived='0') {
        $this->id = $id;
		$this->name = $name;
		$this->email = $email;
		$this->location = $location;
		$this->description = $description;
		$this->archived = $archived;
    }


	function get_id() {
		return $this->id;
	}

	function get_name() {
		return $this->name;
	}

	function get_email() {
		return $this->email;
	}

	function get_location() {
		return $this->location;
	}

    function get_description() {
		return $this->description;
	}

    function is_archived() {
        return $this->archived;
    }
}