#
# Table structure for table 'tx_phinewsletter_domain_model_config'
#
CREATE TABLE tx_phinewsletter_domain_model_config (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	emailfrom varchar(255) DEFAULT '' NOT NULL,
	namefrom varchar(255) DEFAULT '' NOT NULL,
	subject0 varchar(255) DEFAULT '' NOT NULL,
	subject1 varchar(255) DEFAULT '' NOT NULL,
	subject2 varchar(255) DEFAULT '' NOT NULL,
	image0 int(11) DEFAULT '0' NOT NULL,
	image1 int(11) DEFAULT '0' NOT NULL,
	image2 int(11) DEFAULT '0' NOT NULL,
	replytoemail varchar(255) DEFAULT '' NOT NULL,
	replytoname varchar(255) DEFAULT '' NOT NULL,
	statuspageid varchar(255) DEFAULT '' NOT NULL,
	filestorage varchar(255) DEFAULT '' NOT NULL,
	configuration text NOT NULL,
	prefix0 text NOT NULL,
	prefix1 text NOT NULL,
	prefix2 text NOT NULL,
	url varchar(255) DEFAULT '' NOT NULL,
	tosendtime varchar(255) DEFAULT '' NOT NULL,
	issent tinyint(1) unsigned DEFAULT '0' NOT NULL,
	selected tinyint(1) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)

);

#
# Table structure for table 'tx_phinewsletter_domain_model_emails'
#
CREATE TABLE tx_phinewsletter_domain_model_emails (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	newsids varchar(255) DEFAULT '' NOT NULL,
	userid varchar(255) DEFAULT '' NOT NULL,
	groupid varchar(255) DEFAULT '' NOT NULL,
	senttime varchar(255) DEFAULT '' NOT NULL,
	tosendtime int(11) unsigned DEFAULT '0' NOT NULL,
	edition int(11) unsigned DEFAULT '0',
	config int(11) unsigned DEFAULT '0',
	uniqueid varchar(255) DEFAULT '' NOT NULL,
	#additionals text DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)

);


#
# Table structure for table 'tx_phinewsletter_domain_model_openrate'
#
CREATE TABLE tx_phinewsletter_domain_model_openrate (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	user int(11) DEFAULT 0 NOT NULL,
	ip varchar(255) DEFAULT '' NOT NULL,
	config int(11) DEFAULT 0 NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)

);

#
# Table structure for table 'tx_phinewsletter_domain_model_stats'
#
CREATE TABLE tx_phinewsletter_domain_model_clickrate (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	user int(11) DEFAULT 0 NOT NULL,
	ip varchar(255) DEFAULT '' NOT NULL,
	config int(11) DEFAULT 0 NOT NULL,
	itemid int(11) DEFAULT 0 NOT NULL,
	filelink varchar(255) DEFAULT 0 NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)

);

#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (
	doubleopthash varchar(255) DEFAULT '' NOT NULL,
	language tinyint(1) DEFAULT '0' NOT NULL,
	gender varchar(1) DEFAULT '' NOT NULL,
	birthday int(11) unsigned DEFAULT '0' NOT NULL,
);
