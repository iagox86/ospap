# Copyright (c) 2005, Ron Bowes
# All rights reserved.
#
# Redistribution and use in source and binary forms, with or without modification, 
# are permitted provided that the following conditions are met:
#
#       * Redistributions of source code must retain the above copyright notice, this 
# list of conditions and the following disclaimer.
#       * Redistributions in binary form must reproduce the above copyright notice, 
# this list of conditions and the following disclaimer in the documentation 
# and/or other materials provided with the distribution.
#       * Neither the name of the organization nor the names of its contributors 
# may be used to endorse or promote products derived from this software 
# without specific prior written permission.
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

DROP TABLE IF EXISTS users;
CREATE TABLE users
(
	user_id                   INTEGER                NOT NULL AUTO_INCREMENT,
	username                  VARCHAR(255)           NOT NULL,
	password                  VARCHAR(255)           NOT NULL,
	salt                      VARCHAR(255)           NOT NULL,
	email                     VARCHAR(255)           NOT NULL,
	notify_comments           TINYINT(4) UNSIGNED    NOT NULL,
	notify_pictures           TINYINT(4) UNSIGNED    NOT NULL,
	date_registered           DATETIME               NOT NULL,
	authorized                TINYINT(4) UNSIGNED    NOT NULL,
	admin                     TINYINT(4) UNSIGNED    NOT NULL,
	last_category             INTEGER                NOT NULL,
	last_updated_public       DATETIME               NOT NULL,
	last_updated              DATETIME               NOT NULL,

	PRIMARY KEY (user_id)
);

DROP TABLE IF EXISTS categories;
CREATE TABLE categories
(
	category_id               INTEGER                NOT NULL AUTO_INCREMENT,
	user_id                   INTEGER                NOT NULL,
	name                      VARCHAR(255)           NOT NULL,
	private                   TINYINT(4) UNSIGNED    NOT NULL,
	date_created              DATE                   NOT NULL,
	last_updated_public       DATETIME               NOT NULL,
	last_updated              DATETIME               NOT NULL,
	
	PRIMARY KEY (category_id),
	FOREIGN KEY (user_id) REFERENCES users
);



DROP TABLE IF EXISTS pictures;
CREATE TABLE pictures
(
	picture_id                INTEGER                NOT NULL AUTO_INCREMENT,
	category_id               INTEGER                NOT NULL,
	title                     VARCHAR(255)           NOT NULL,
	filename                  VARCHAR(255)           NOT NULL,
	caption                   TEXT                   NOT NULL,
	date_added                DATE                   NOT NULL,

	PRIMARY KEY (picture_id),
	FOREIGN KEY (category_id) REFERENCES category
);

DROP TABLE IF EXISTS comments;
CREATE TABLE comments
(
	comment_id                INTEGER                NOT NULL AUTO_INCREMENT,
	user_id                   INTEGER                NOT NULL,
	picture_id                INTEGER                NOT NULL,
	text                      TEXT                   NOT NULL,
	date_added                DATETIME               NOT NULL,
	poster_name               VARCHAR(255)           NOT NULL,

	PRIMARY KEY (comment_id),
	FOREIGN KEY (user_id) REFERENCES users,
	FOREIGN KEY (picture_id) REFERENCES pictures
);


