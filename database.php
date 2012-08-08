<?php

	# Copyright (c) 2005, Ron Bowes
	# All rights reserved.
	#
	# Redistribution and use in source and binary forms, with or without modification, 
	# are permitted provided that the following conditions are met:
	#
	#	  * Redistributions of source code must retain the above copyright notice, this 
	#	list of conditions and the following disclaimer.
	#	  * Redistributions in binary form must reproduce the above copyright notice, 
	#	this list of conditions and the following disclaimer in the documentation 
	#	and/or other materials provided with the distribution.
	#	  * Neither the name of the organization nor the names of its contributors 
	#	may be used to endorse or promote products derived from this software 
	#	without specific prior written permission.
	#
	# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
	# AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
	# IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
	# ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE 
	# LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
	# CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
	# SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
	# INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN 
	# CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
	# ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
	# POSSIBILITY OF SUCH DAMAGE.

	# database.php
	# This is a wrapper script for Database functions to help simplify code. 
	#

	# Gets a read-connection to the database (use this for most queries)
	function get_db_read()
	{
		global $db_persistent; 
		global $db_host; 
		global $db_read_user; 
		global $db_read_pass; 
		global $db_name; 

		static $db = null;

		if($db == null)
		{
			# Make a connection to the database
			if($db_persistent == 1)
				$db = mysql_pconnect($db_host, $db_read_user, $db_read_pass);
			else
				$db = mysql_connect($db_host, $db_read_user, $db_read_pass);
	  
			# Make sure we have a database
			if(!$db)
				show_mysql_error('Connecting to database', mysql_error());
	
			# Select the database
			mysql_select_db($db_name) or show_mysql_error('Selecting database', mysql_error());
		}

		return $db;
	}

	# Gets a write-connection to the database (only use if you absolutely need to write)
	function get_db_write()
	{
		global $db_persistent; 
		global $db_host; 
		global $db_write_user; 
		global $db_write_pass; 
		global $db_name; 

		static $db = null;

		if($db == null)
		{
			# Make a connection to the database
			if($db_persistent == 1)
				$db = mysql_pconnect($db_host, $db_write_user, $db_write_pass);
			else
				$db = mysql_connect($db_host, $db_write_user, $db_write_pass);
	  
			# Make sure we have a database
			if(!$db)
				show_mysql_error('Connecting to database', mysql_error());
	
			# Select the database
			mysql_select_db($db_name) or show_mysql_error('Selecting database', mysql_error());
		}

		return $db;
	}

		
	# Attempt to execute a mysql query, or die if it was unsuccessful
	function try_mysql_query($query, $db)
	{
		$result = mysql_query($query, $db) or show_mysql_error($query, mysql_error());
		return $result;
	}

	# NOTE: USERNAME HAS TO BE SANITIZED BEFORE ENTERING!
	# If only a username is specified (null password), returns the assoc. array of the user's information (or 
	# null if the user wasn't found).  If a password (hashed with the salt) is given, it checks the password and
	# returns null if it failed.  
	function get_user_info($db, $username, $password = null)
	{
		$result = try_mysql_query("SELECT * FROM users WHERE username = '$username'", $db);

		if(!$result)
		{
			return null;
		}

		$ret = mysql_fetch_assoc($result);

		if($password != null && $ret['password'] != $password)
		{
			$ret = null;
		}

		mysql_free_result($result);

		return $ret;
	}

	function get_all_users($show_private, $db)
	{
		if($show_private)
			$result = try_mysql_query("SELECT * FROM users ORDER BY last_updated DESC", $db);
		else
			$result = try_mysql_query("SELECT * FROM users ORDER BY last_updated_public DESC", $db);

		$ret = array();

		while($this_result = mysql_fetch_assoc($result))
			array_push($ret, $this_result);
		mysql_free_result($result);

		return $ret;
	}

	function get_category_from_picture_id($picture_id, $db)
	{
		if(is_numeric($picture_id) == false)
			return null;

		$result = try_mysql_query("SELECT * FROM pictures WHERE picture_id='$picture_id'", $db);
		$assoc = mysql_fetch_assoc($result);
		mysql_free_result($result);

		return $assoc;
	}

	function get_user_from_picture_id($picture_id, $db)
	{
		if(is_numeric($picture_id) == false)
			return null;

		$result = try_mysql_query("SELECT * FROM users, categories, pictures WHERE pictures.picture_id='$picture_id' AND pictures.category_id=categories.category_id AND categories.user_id=users.user_id", $db);
		$assoc = mysql_fetch_assoc($result);
		mysql_free_result($result);

		return $assoc;
	}

	function get_picture_from_picture_id($picture_id, $db)
	{
		if(is_numeric($picture_id) == false)
			return null;

		$result = try_mysql_query("SELECT * FROM pictures WHERE picture_id='$picture_id'", $db);
		$assoc = mysql_fetch_assoc($result);
		mysql_free_result($result);

		return $assoc;
	}

	function get_user_from_category_id($category_id, $db)
	{
		if(is_numeric($category_id) == false)
			return null;

		$result = try_mysql_query("SELECT * FROM categories WHERE category_id='$category_id'", $db);
		$assoc = mysql_fetch_assoc($result);
		mysql_free_result($result);

		return $assoc;
	}

	function get_pictures_by_user_id($user_id, $show_private, $db)
	{
		if(is_numeric($user_id) == false)
			return null;

		$ret = array();

		if($show_private)
			$result = try_mysql_query("SELECT * FROM pictures, categories WHERE categories.user_id='$user_id' AND pictures.category_id=categories.category_id", $db);
		else
			$result = try_mysql_query("SELECT * FROM pictures, categories WHERE categories.private='0' AND categories.user_id='$user_id' AND pictures.category_id=categories.category_id", $db);

		while($this_result = mysql_fetch_assoc($result))
			array_push($ret, $this_result);
		mysql_free_result($result);

		return $ret;
	}

	function get_categories_by_user_id($user_id, $show_private, $db)
	{
		if(is_numeric($user_id) == false)
			return null;

		if($show_private)
			$result = try_mysql_query("SELECT * FROM categories WHERE user_id='$user_id' ORDER BY last_updated DESC", $db);
		else
			$result = try_mysql_query("SELECT * FROM categories WHERE private='0' AND user_id='$user_id' ORDER BY last_updated_public DESC", $db);

		$ret = array();
		while($this_result = mysql_fetch_assoc($result))
			array_push($ret, $this_result);
		mysql_free_result($result);

		return $ret;
	}

	function get_all_categories($show_private, $db)
	{
		if($show_private)
			$result = try_mysql_query("SELECT * FROM categories", $db);
		else
			$result = try_mysql_query("SELECT * FROM categories WHERE private='0'", $db);

		$ret = array();
		while($this_result = mysql_fetch_assoc($result))
			array_push($ret, $this_result);
		mysql_free_result($result);

		return $ret;
	}

	function get_user_by_user_id($user_id, $db)
	{
		if(is_numeric($user_id) == false)
			return null;

		$result = try_mysql_query("SELECT * FROM users WHERE user_id='$user_id'", $db);
		$user_information = mysql_fetch_assoc($result);
		mysql_free_result($result);

		return $user_information;
	}

	function get_category_by_category_id($category_id, $db)
	{
		if(is_numeric($category_id) == false)
			return null;

		$result = try_mysql_query("SELECT * FROM categories WHERE category_id='$category_id'", $db);
		$category_information = mysql_fetch_assoc($result);
		mysql_free_result($result);

		return $category_information;
	}

	function get_pictures_by_category_id($category_id, $db)
	{
		if(is_numeric($category_id) == false)
			return null;

		$result = try_mysql_query("SELECT * FROM pictures WHERE category_id='$category_id' ORDER BY date_added", $db);

		$ret = array();
		while($this_result = mysql_fetch_assoc($result))
			array_push($ret, $this_result);
		mysql_free_result($result);

		return $ret;
	}

	function get_comments_by_picture_id($picture_id, $db)
	{
		if(is_numeric($picture_id) == false)
			return null;

		$result = try_mysql_query("SELECT * FROM comments WHERE picture_id='$picture_id' ORDER BY date_added", $db);

		$ret = array();
		while($this_result = mysql_fetch_assoc($result))
			array_push($ret, $this_result);
		mysql_free_result($result);

		return $ret;
	}

	function get_comments_by_poster_id($poster_id, $db)
	{
		if(is_numeric($picture_id) == false)
			return null;

		$result = try_mysql_query("SELECT * FROM comments WHERE user_id='$poster_id'", $db);

		$ret = array();
		while($this_result = mysql_fetch_assoc($result))
			array_push($ret, $this_result);
		mysql_free_result($result);

		return $ret;
	}

	function get_emails_notify_pictures($db)
	{
		$result = try_mysql_query("SELECT email FROM users WHERE notify_pictures='1'", $db);

		$ret = array();
		while($this_result = mysql_fetch_assoc($result))
			array_push($ret, $this_result['email']);
		mysql_free_result($result);

		return $ret;
	}

	function get_unauthorized_users($db)
	{
		$result = try_mysql_query("SELECT * FROM users WHERE authorized='0'", $db);
		$ret = array();
		while($this_result = mysql_fetch_assoc($result))
			array_push($ret, $this_result);
		mysql_free_result($result);

		return $ret;
	}


// NO NEWINE at bottom! It screws up pictures!
?>
