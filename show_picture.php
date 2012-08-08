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

	# show_picture.php
	# This shows the selected picture, with information about it. 
	# 

	header('Pragma: no-cache');

	require('shared.php');

	# Make a connection to the database
	$db = get_db_read();
	$_SESSION['back'] = $_SERVER['REQUEST_URI'];

	if(isset($_GET['picture_id']) == false || is_numeric($_GET['picture_id']) == false)
		show_error_redirect_back("Invalid picture");

	$picture_id = $_GET['picture_id'];
	# Get the current picture 
	$picture = get_picture_from_picture_id($picture_id, $db) or show_error_redirect_back("Invalid picture");
	# Get the category
	$category = get_category_by_category_id($picture['category_id'], $db) or show_error_redirect_back("Invalid picture");
	# Get the user
	$user = get_user_by_user_id($category['user_id'], $db) or show_error_redirect_back("Invalid picture");

	# Check if the category is private
	if(!$me && $category['private'] == '1')
		show_error_redirect_back("Invalid picture");

	# Get the images in the category
	$pictures = get_pictures_by_category_id($category['category_id'], $db);
	$prev_picture = null;
	$next_picture = null;

	# Find the next and previous picture
	$done = false;
	while(!$done && $this_picture = array_shift($pictures))
	{
		if($this_picture['picture_id'] == $picture_id)
		{
			if($this_picture = array_shift($pictures))
				$next_picture = $this_picture;
			$done = true;
		}
		else
		{
			$prev_picture = $this_picture;
		}
	}

	if($next_picture)
	{
		$next_picture['url'] = "show_picture.php?picture_id=" . $next_picture['picture_id'];
		$next_picture['picture_url'] = "picture.php?picture_id=" . $next_picture['picture_id'];
		$next_picture['tn_url'] = "picture.php?tn=true&picture_id=" . $next_picture['picture_id'];
	}
	if($prev_picture)
	{
		$prev_picture['url'] = "show_picture.php?picture_id=" . $prev_picture['picture_id'];
		$prev_picture['picture_url'] = "picture.php?picture_id=" . $prev_picture['picture_id'];
		$prev_picture['tn_url'] = "picture.php?tn=true&picture_id=" . $prev_picture['picture_id'];
	}

	$this_picture['url'] = "show_picture.php?picture_id=" . $picture_id;
	$this_picture['picture_url'] = "picture.php?picture_id=" . $picture_id;
	$this_picture['tn_url'] = "picture.php?tn=true&picture_id=" . $picture_id;


	# Get the list of comments
	$comments = get_comments_by_picture_id($picture_id, $db);


	$new_comments = array();
	while($current_result = array_shift($comments))
	{
		# Get information on the poster
		$current_result['comment_poster'] = get_user_by_user_id($current_result['user_id'], $db);
		array_push($new_comments, $current_result);
	}

	template_display_picture($me, $user, $category, $picture, $next_picture, $prev_picture, $new_comments, $max_height, $max_width);

?>
	
	
	

