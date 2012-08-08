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

	# show_category.php
	# This shows a list of the pictures in a category.  Hopefully, eventually, with
	# thumbnails.  
	# 

	header('Pragma: no-cache');

	require('shared.php');

	$db = get_db_read();
	$_SESSION['back'] = $_SERVER['REQUEST_URI'];

	if(isset($_GET['category_id']) == false || is_numeric($_GET['category_id']) == false)
		show_error_redirect_back("No category_id specified");

	$category_id = $_GET['category_id'];

	$category_information = get_category_by_category_id($category_id, $db);
	if(!$category_information || (!$me && $category_information['private'] != 0))
		show_error_redirect_back("invalid category_id");
	$user_information = get_user_by_user_id($category_information['user_id'], $db);

	# Check if the category is private
	$pictures = get_pictures_by_category_id($category_id, $db);

	# Display the table of pictures
	$new_pictures = array();
	foreach($pictures as $picture)
	{
		$picture['url'] = "show_picture.php?picture_id=" . $picture['picture_id'];
		$picture['picture_url'] = "picture.php?picture_id=" . $picture['picture_id'];
		$picture['tn_url'] = "picture.php?tn=true&picture_id=" . $picture['picture_id'];
		$picture['num_comments'] = count(get_comments_by_picture_id($picture['picture_id'], $db));
		array_push($new_pictures, $picture);
	}

	template_display_picture_list($me, $user_information, $category_information, $new_pictures, $thumbnail_height, $thumbnail_width);
?>
	
	
	

