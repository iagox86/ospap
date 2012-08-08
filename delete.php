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

	# delete.php
	# This script deletes images.  It first checks if the user is an admin.  If
	#  he isn't, then it checks if the user is the owner of the picture (the
	#  category that the picture is in).  If he is admin or owner, it's deleted.
	# 

	header('Pragma: no-cache');

	require('shared.php');

	# Make a connection to the database
	$db_read = get_db_read();
	$db_write = get_db_write();

	if(!$me)
		show_error_redirect_back("Please log in");

	if(isset($_GET['picture_id']) == false)
	{
		if(isset($_GET['category_id']) == false)
		{
			show_error_redirect_back("No category_id or picture_id found to delete");
		}

		# The user is deleting a category
		$category_id = $_GET['category_id'];
		if(is_numeric($category_id) == false)
			show_error_redirect_back("No category_id or picture_id found to delete");

		$result = try_mysql_query("SELECT user_id FROM categories WHERE category_id='$category_id'", $db_read);
		$assoc = mysql_fetch_assoc($result);
		mysql_free_result($result);

		# Get the owner of the category
		if($me['admin'] != '1')
			if($assoc['user_id'] != $me['user_id'])
				show_error_redirect_back("Access denied");

		$pictures_result = try_mysql_query("SELECT * FROM pictures WHERE category_id='$category_id'", $db_read);
		while($row = mysql_fetch_assoc($pictures_result))
			try_mysql_query("DELETE FROM comments WHERE picture_id='" . $pictures_result['picture_id'] . "'", $db_write);
		mysql_free_result($pictures_result);
		try_mysql_query("DELETE FROM pictures WHERE category_id='$category_id'", $db_write);
		try_mysql_query("DELETE FROM categories WHERE category_id='$category_id'", $db_write);
	
		show_message_redirect("Category deleted", "show_user.php?user_id=" . $assoc['user_id']);
	}
	else
	{
		# The user is deleting a picture
		$picture_id = $_GET['picture_id'];
		if(is_numeric($picture_id) == false)
			redirect_back();
	
		// Get the category
		$result = try_mysql_query("SELECT user_id,pictures.category_id FROM categories,pictures WHERE categories.category_id = pictures.category_id AND picture_id = $picture_id", $db_read);
		$assoc = mysql_fetch_assoc($result);
		mysql_free_result($result);
		if($me['admin'] != 1 && $assoc['user_id']  != $me['user_id'])
			show_error_redirect_back("Access denied");

		try_mysql_query("DELETE FROM pictures WHERE picture_id = '$picture_id'", $db_write);
		try_mysql_query("DELETE FROM comments WHERE picture_id = '$picture_id'", $db_write);

		show_message_redirect("Picture deleted", "show_category.php?category_id=" . $assoc['category_id']);
	}

?>
	
	
	

