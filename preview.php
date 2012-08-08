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

	# upload_iamge.php
	# * Image is uploaded to this
	# * Image is moved and named appropriately
	# * 
	# 

	header('Pragma: no-cache');

	require('shared.php');

	# Make a connection to the database
	$db = get_db_read();

	if(!$me)
		show_error_redirect_back("Please log in first");


	if(isset($_FILES['file']) == false)
		show_error_redirect_back("Error uploading file!  The filename wasn't found.");

	$ext = get_extension(strtolower($_FILES['file']['name']));

	if(!in_array($ext, array("jpeg", "jpg", "png", "gif", "bmp", "tif", "tiff")))
		show_error_redirect_back("Sorry, $ext isn't an allowed file type.  Allowed extensions are JPEG, JPG, GIF, PNG, BMP, TIF, and TIFF<BR>");

	# Generate the new filename
	$rand = generate_salt();
	$i = 0;
	do
	{
		$newname = $me['username'] . "-" . $rand . "-$i.jpeg";
	}
	while(file_exists("$upload_directory/$newname"));

	# Copy it into the production folder, however, don't link it to the database yet.  
	resize_and_compress($max_width, $max_height, $jpeg_quality, $_FILES['file']['tmp_name'], "$upload_directory/$newname");
	resize_and_compress($thumbnail_width, $thumbnail_height, $jpeg_quality, $_FILES['file']['tmp_name'], "$upload_directory/tn-$newname");

	# Copy it into the preview folder
	resize_and_compress($preview_width, $preview_height, 40, $_FILES['file']['tmp_name'], "$preview_directory/$newname");

	# Set the filename in the session, which is used after confirmation
	$_SESSION['image_filename'] = $newname;

	# Get the list of categories, for displaying the combobox
	$categories = get_categories_by_user_id($me['user_id'], true, $db);

	if(count($categories) == 0)
		show_error_redirect_back("You need to create a category first");

	# Display
	template_display_preview("$preview_directory/$newname", htmlentities($_FILES['file']['name']), $categories, $me['last_category']);

?>
	
	
	

