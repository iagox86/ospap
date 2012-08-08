<?php

	# Copyright (c) 2005, Ron Bowes
	# All rights reserved.
	#
	# Redistribution and use in source and binary forms, with or without modification, 
	# are permitted provided that the following conditions are met:
	#
	#       * Redistributions of source code must retain the above copyright notice, this 
	#	 list of conditions and the following disclaimer.
	#       * Redistributions in binary form must reproduce the above copyright notice, 
	#	 this list of conditions and the following disclaimer in the documentation 
	#	 and/or other materials provided with the distribution.
	#       * Neither the name of the organization nor the names of its contributors 
	#	 may be used to endorse or promote products derived from this software 
	#	 without specific prior written permission.
	#
	# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS 'AS IS' 
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

	# The host for the database
	$db_host = 'localhost';

	# The name of the database to use.
	$db_name = 'ospap';

	# You can optionally use separate accounts for read and write operations
	# in the database.  This is purely a security thing: if somebody manages
	# to find an SQL-Injection exploit, they might only have access to the read
	# user, which would prevent any kind of damage.  
	$db_read_user = 'ospap_read';
	$db_read_pass = '';
	$db_write_user = 'ospap_write';
	$db_write_pass = '';

	# Use a persistent connection to the database (use this if the database is
	# local)
	$db_persistent = 1;

	# The name of the skin to use.  The skins are "skins/<name>/skin.php"
	$skin = "dark";

	# The size of the thumb nails.  By default, 64x64.  The aspect ratio will be kept.
	$thumbnail_width = 64; 
	$thumbnail_height = 64;

	# The number of thumbnails to put on each row
	$thumbnails_per_row = 6;

	# The maximum size of uploaded pictures, in pixels (biggers ones will be 
	# resized, maintaining their aspect ratio)
	$max_width = 640;
	$max_height = 480;

	# If this is set, an admin must authorize a new account.  
	$require_authorization = 1;

	# If this is set, a user has to register before he can view anything.
	$require_registration = 0;

	# Whether or not to even allow email
	$smtp_allow = 1;
	# The SMTP server to use to send notifications.
	$smtp_server = 'smtp.mts.net';
	# The SMTP port.  The default should always work.  
	$smtp_port = 25;
	# The SMTP username/password (this usually isn't required)
	$smtp_username = '';
	$smtp_password = '';
	# The user to send the email from.
	$smtp_email = 'iago@valhallalegends.com';

	# The maximum length of an email address.
	$max_length_email = 1024;

	# The maximum length of a category name.
	$max_length_category = 256;

	# The maximum length of a title name.
	$max_length_title = 256;

	# The maximum length of a caption/comment.
	$max_length_comment = 10240;

	# The quality of jpeg images (higher = bigger end images, but not as nice looking).
	$jpeg_quality = 85;

	# The directory that pictures are stored in.  This shouldn't be on the 
	# public http path if you want to require registration.  They will be
	# loaded and displayed by a .php script.   No trailing slash.
	$upload_directory = '/tmp/php';
	
	# The directory that the preview is stored in.  This has to be on the 
	# shared path somewhere, and relative to these .php scripts.  Images
	# from here only last for a limited time.  No trailing slash.  
	$preview_directory = 'previews';

	# The time, in minutes, until a preview images expires and is deleted.  
	# I'm deleting them fast, by default, since once it's loaded the first time
	# it isn't necessary for anybody to see it again.  But if you want previews
	# to be persistant, then by all means raise this.  
	$preview_timeout = 1;

	# The size of previews.
	$preview_width = '320';
	$preview_height = '240';

	# I've had problems with resizing images with gd, so I figured I'd define a function here
	# so it could easily be changed if you don't have the proper software.  This implementation
	# requires the 'convert' program, which comes with imagemagick, www.imagemagick.org.
	# width = new width in pixels
	# height = new height in pixels
	# compression = jpeg compression value
	# old_filename = the original filename, in gif, jpeg, or png format
	# new_filename = the new filename, has to be .jpeg
	function resize_image($width, $height, $compression, $old_filename, $new_filename)
	{
		$ret = 0;
		system("convert -quality $compression -resize " . $width . "x" . $height . " $old_filename $new_filename", $ret);

		if(is_file($old_filename) == false)
		{
			show_error_die('image failed to upload');
		}
		else if($ret == 127)
		{
			show_error_die('image conversion failed, please install imagemagick or edit the resize_image() function in configuration.php');
		}
		else if($ret != 0)
		{
			show_error_die('image conversion failed, picture was invalid or corrupt');
		}
	}

// NO NEWLINE at end! It screws up images!
?>
