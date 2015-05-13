(
  _id smallint unsigned NOT NULL auto_increment,
  uid varchar(11)NOT NULL default '-1',
  type tinyint NOT NULL default '0',
  name varchar(15) NOT NULL default '',
  gender char(1) NOT NULL default 'm',
  number smallint unsigned NOT NULL default '0',
  icon varchar(50) NOT NULL default 'img/question.gif',
  club tinyint unsigned NOT NULL default '0',
  `motto` varchar(30) NOT NULL DEFAULT '',
  `killmsg` varchar(30) NOT NULL DEFAULT '',
  `lastword` varchar(30) NOT NULL DEFAULT '',
  skill varchar(255) NOT NULL default '{}',
  daemontime int(10) unsigned NOT NULL default '0',
  deathtime int(10) unsigned NOT NULL default '0',
  killer varchar(255) NOT NULL default '[]',
  deathreason varchar(64) NOT NULL default '',
  cooldowntime char(255)  NOT NULL default '{}',
  buff varchar(1000) NOT NULL default '[]',
  action varchar(255) NOT NULL default '{}',
  pose tinyint(1) unsigned NOT NULL default '0',
  tactic tinyint(1) unsigned NOT NULL default '0',
  hp int unsigned NOT NULL default '0',
  mhp int unsigned NOT NULL default '0',
  sp int unsigned NOT NULL default '0',
  msp int unsigned NOT NULL default '0',
  att smallint unsigned NOT NULL default '0',
  def smallint unsigned NOT NULL default '0',
  baseatt smallint unsigned NOT NULL default '0',
  basedef smallint unsigned NOT NULL default '0',
  region tinyint unsigned NOT NULL default '0',
  area tinyint unsigned NOT NULL default '0',
  lvl tinyint unsigned NOT NULL default '0',
  `exp` smallint unsigned NOT NULL default '0',
  `upexp` smallint unsigned NOT NULL default '0',
  money mediumint unsigned NOT NULL default '0',
  killnum smallint unsigned NOT NULL default '0',
  proficiency varchar(255) not null default '{}',
  `teamID` varchar(24) not null default '-1',
  collecting varchar(255) NOT NULL default '{}',
  capacity smallint unsigned not null default '5',
  package varchar(1000) NOT NULL default '[]',
  equipment varchar(1000) NOT NULL default '{}',
  
  rage int unsigned NOT NULL default '0',

  PRIMARY KEY  (_id),
  INDEX TYPE (type, number),
  INDEX NAME (name, type)
	
)