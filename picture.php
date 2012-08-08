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

	# picture.php
	# This will show the picture to the user, assuming they're allowed to see it. 
	# Hot-linking should also work for users who are logged in, if logging in is
	# required (per the settings).  
	# 

	//header('Pragma: no-cache');

	require("shared.php");

	if(isset($_GET['picture_id']) == false || is_numeric($_GET['picture_id']) == false)
		show_error_image("Couldn't find picture");

	if(isset($_GET['tn']) && $_GET['tn'] == "true")
		$tn = true;
	else
		$tn = false;

	$picture_id = $_GET['picture_id'];

	$db = get_db_read();

	# Check if this board requires registration
	if(!$me && $require_registration)
		show_error_image("Login required");

	# Get the information on the picture
	$picture = get_picture_from_picture_id($picture_id, $db);
	# Get information ont he category
	$category = get_category_by_category_id($picture['category_id'], $db);

	# Die if the picture doesn't exist
	if(!($picture))
		show_error_image("Couldn't find picture");

	# If they aren't logged in, make sure they have access
	if(!$me && $category['private'] == '1')
		if($category['private'] == 1)
			show_error_image("Couldn't find picture");

	$file = $picture['filename'];

	if($tn == true)
		show_image("$upload_directory/tn-$file");
	else
		show_image("$upload_directory/$file");
	


	function show_image($image)
	{
		header('Content-type: image/' . get_extension($image));

		$handle = fopen($image, "rb");

		while(!feof($handle))
		{
			$buffer = fgets($handle, 4096);
			echo $buffer;
		}
		fclose($handle);
	}

	function show_error_image($message, $fatal = true)
	{
		header("Content-type: image/png");
		$im = @imagecreatetruecolor(200, 30);
    
		$text_color = imagecolorallocate($im, 233, 14, 91);
		imagestring($im, 9, 5, 5,  $message, $text_color);
		imagepng($im);
		imagedestroy($im);

		if($fatal == true)
			exit(1);
	}


?>
	
	
	

