<?php
	# Copyright (c) 2005, Ron Bowes
	# All rights reserved.
	#
	# Redistribution and use in source and binary forms, with or without modification, 
	# are permitted provided that the following conditions are met:
	#
	#	* Redistributions of source code must retain the above copyright notice, this 
	#	  list of conditions and the following disclaimer.
	#	* Redistributions in binary form must reproduce the above copyright notice, 
	#	  this list of conditions and the following disclaimer in the documentation 
	#	  and/or other materials provided with the distribution.
	#	* Neither the name of the organization nor the names of its contributors 
	#	  may be used to endorse or promote products derived from this software 
	#	  without specific prior written permission.
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
 	#

	# create_category.php
	# This script simply creates a category, if it doesn't already exist.  
	# 

	header('Pragma: no-cache');

	require_once('shared.php');

	# Make a connection to the database
	$db_read = get_db_read();
	$db_write = get_db_write();

	if(!$me)
		redirect("index.php");

	if(isset($_POST['category']) == false)
		redirect("index.php");

	$category = mysql_escape_string(htmlentities(trim($_POST['category'])));
	$private = isset($_POST['private']) ? '1' : '0';

	if(validate_category($category) == false)
		show_error_redirect_back("Please enter a valid category name (between 3 and $max_length_category characters)");

	$result = try_mysql_query("SELECT * FROM categories WHERE name = '$category' AND user_id = '" . $me['user_id'] . "'" , $db_read);

	if(mysql_num_rows($result) > 0)
		show_error_redirect_back('Error: you already have a category with that name!');

	try_mysql_query("INSERT INTO categories (user_id, name, private, date_created, last_updated, last_updated_public) VALUES (" . $me['user_id'] . ", '$category', '$private', NOW(), 0, 0)", $db_write);
	$category_id = mysql_insert_id($db_write);
	try_mysql_query("UPDATE users SET last_category='$category_id' WHERE user_id='" . $me['user_id'] . "'", $db_write);

	show_message_redirect_back("Category successfully created!");

?>
	
	
	

