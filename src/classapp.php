<?php
$ehcpversion="20.04.1.b";
$known_to_work="Ubuntu-20.04";
$emailenable=True;

/*
EASY HOSTING CONTROL PANEL MAIN CLASS FILE - www.ehcp.net
mail: info@ehcp.net
support&paypal: support@ehcp.net

# last modified by ehcpdeveloper on 6.9.2020 (d-m-y)

About ssl: 
We currently try to use ssl automatically. 
Necessary info will be taken from admin/user and CSR will be built. 
All will be webbased. 


ChangeLog (Nearest upper):

2020-04:
* Rewriting nginx and apache2 configuration parts.
* ssl/https fixings
* Carry webserver config files into relevant subdirectories
* Code Cleanup
* timeout for emails and domains.
* First Ubuntu 20.04 version ready

2018-06:
* Ubuntu 18.04 minor modifications done
* Two Security issue identified & resolved. "This vulnerability was discovered by Zhouyuan Yang of Fortinet's FortiGuard Labs"
* Fixed some minor issues

2018-01:
* small problem in listallemails fixed
* Ubuntu 17.10 related mail issues fixed. Currently, Only Ubuntu 16.04 fully supported. Later versions are being tested. 

2017-11:
* letsencrypt certbot
* some other fixes. 

2016-07..2016-08:
* Many changes related to Ubuntu 16.04 LTS, especially related to php7 changes, so, 
* many php5 references fixed according to php7
* server ip detection fixed (not so important)
* Using python based ftp, instead of vsftp, this layer is new.. should be tested throughly
* default number of processes are reduced for some daemons, such as courier-imapd etc. ; to save server resources.. 
* adodb upgraded to latest version.. which supports php7
* new options in options page, to take more control
* similarfunctions improved by better css based display
* minor fixes


2016-01:
* login brute force attack detection, disallow password retry for more than 5 times from same ip

2015-05:
* similar functions & some text moved to language files for easier localization
* Initial plans for more modular design with separate classes.

2015-03:
* new module functions added, 
* ssl module, vps module, vpn module designed (pro version)

2014-07:
 * Switched to mariadb as sql server,
 * deprecated mysql_connect functions and other deprecated php code removed
 * Ubuntu 14.04 related issues solved.
 * Other code cleanup and improvements

2013-06:
 * Light, normal, extra installation modes added. light is for only crucial parts of softwares that comes with ehcp.
 * unattended install added, suppress many inputs for fast installation, for testing purposes
 * will do: ehcp testing suite, for testing an ehcp installation automatically, such as ftp, domain etc.

2013-05:
* added new Easy Install Scripts
* fixed vsftpd bug for some Ubuntu versions, thanks to enrolmartin
* added manual slave dns functionality (earnolmartin)


2013-03:
* Added custom permissions for dirs.
* account activation for new panelusers.
* page rewrite testing for nginx, not working yet.
* file upload code inside ehcp, for ssl etc.
* adjust ssl functions, testing, not working yet.
* freedns.afraid.org integration
* passworded dirs for nginx too, not tested fully, please test
* improved nginx install a little for newer systems
* "multiple Ip" bug resolved. more than one IP can be used in a server, to host different domains.

2012-12:
* fixapacheconfig2 added,
* fixed some small bugs
* worked a few new features, do not remember all now.

2013-1:
* added failed login indicator
*
2012-12:
* fixed some bugs.
*
2012-4:
* fixed subdomain configuration for IP based configs
* fixed backup bug

2012-3:
* gziping backup option added
* fix db add with "-" in name, (create database db-dene: not work, create database `db-dene`: works)
* fix subdomain issue
* minor fixes (usage&security)
* new options (such as wildcard domains)
* default template is sky

2011-11:
* file ownerships changed to: vsftpd:www-data
* file ownerships are more flexible and adjustable. all ftp/web configs follow those ownerships, when changed in classapp config section.
* one quota bug fixed
* one security fix

2011-08: (ver 0.30)
* new "easy install scripts" added, sqlbuddy etc.
* a clean, "sky" template added, by 7skyhost, thanks
* auto check for ehcp version, if upgrade needed..
* logrotate for all domain done, autom.
* different custom http for different webservers in dhcp db, so, switching webserver while having custom http is not a problem anymore
* code fixes, cleanup
* chive (mysql admin tool) added
* template_reseller added
* password reset improved/fixed
* ftp active/passive bug fixed

2011-05:
* fixed an ftp insert, security
* do not remember all
*
2011-01:
* Roundcube added,
* add domain to different IP on same server.
*
*
2010-11:
* multiserver, apache2+nginx on remote servers,
* some bugfix,
* ported ehcp dir to a new location: /var/www/new/ehcp
*

2010-09:
* nginx is supported directly in ehcp, automatically.
* multiserver concept improved. now, can produce config files for multiple webservers, it will be controlling multiple webservers soon.
* ssl is handled better now, i hope,

2010-03:
* a few fixes, I dont remember all,
* fixmailconfig does fix a few things too,
* ubuntu theme added by razvan@nrj.ro
* more stable "fixapacheconfig" command
* bulk add email (bulk add domain/hosting already exist)

2010-01:
* Added webmail, pass change plugin in webmail (squirrelmail), by cdbhushan@gmail.com

2009-11:
* Added autoreply using only php,ehcp, postfix transport maps, without 3rd party software
* added: function adjustEmailAutoreply
*
2009-09:
* fixmailconfiguration,
* fixapachewithssl # ssl support..
* fixapachenonssl
* default charset for new databases...

2009-06-08:
deleteFtpUserDirect() added

2009-06-07:
$this->showSimilarFunctions($func); function added
function addFtpToThisPaneluser() and similars added,
*


security issues: (now handled by beforeinputcontrols and afterinputcontrols)

steps to control when doing something:
1- is user active?
2- is user limit exceeded for that action?
3- is user permitted to do that?
4- is user owner of that or authorized?
5- is target entity already exists ? that is, is domain exists, or is panel users exists ?


*/
# include_once("config/dbutil.php"); # some old routines.. being ported to this class...
error_reporting (E_ALL ^ E_NOTICE ^ E_WARNING);
ini_set('display_errors', '1');

@date_default_timezone_set('Europa/Istanbul'); # this is mandatory in php 5.3 and up for date commands

include_once(dirname(__FILE__).'/adodb5/adodb.inc.php');  # bu dirname muhabbeti: classapp, ba??ka biyerden bile include edilse, bunlar??n cal??smas?? icin.. fix for this: http://www.ehcp.net/?q=comment/2921#comment-2921 ; with this code, no chdir should be required in api

include_once(dirname(__FILE__).'/localutils.php');
@include_once(dirname(__FILE__).'/modules/module_index.php'); # Modules exist for Professional version, extends basic functionality of Ehcp. Core is mostly same between Basic and Pro version. 
include_once(dirname(__FILE__).'/config/randomstring.php');
include_once(dirname(__FILE__)."/install_lib.php");

# bu harici fonksiyonlar, localutils icine tasinacak..


class Application
{
	var $appname = '',$sitename='ehcp', $headers="From: info@ehcp.net";
	var $output='', $requirePassword=True, $checkinstall=True,$miscconfig=null;
	var $queries=[], $selecteddomain='',$isreseller=false;
	var $check_webserver_enabled=True; # disable this to overcome false switchover of webservers... false alarms.. 

	var $wwwuser="www-data";  # these vars should be used are "user related" places, to unify all user settings..  #equivalent: var $wwwowner="vsftpd:www-data";
	var $wwwgroup="www-data";
	var $ftpuser="vsftpd";    # equivalent: var $ftpowner="vsftpd:www-data";
	var $ftpgroup="www-data"; # with this config, ftp user is able to see/delete files written by webserver.
	var $ftp_server_progname="ftp_server2.py";

	# debuglevel: 4: shows some functions, 3: shows queries
	var $debuglevel=0;


	var $myversion='';
	var $dbhost;
	var $dbname;
	var $dbusername;
	var $dbpass;
	var $conn;

	var $defaultlanguage="en",$clientip,$referer;
	var $currentlanguage='en';

	var $status_active="active",$status_passive="passive",$statusActivePassive,$passivefilt,$activefilt,$isDemo=false;
	var $emailfrom='info@ehcp.net';
	# language strings will be defined in $lang['en']['error1']   in language/en.php or so on...

	var $usertable,$dnsemail,$template,$templatefile,$op,$userinfo;
	var $dnszonetemplate="dnszonetemplate";
	var $dnsnamedconftemplate="dnsnamedconftemplate";  # aynen apache gibi oluturulacak...
	var $dnsnamedconftemplate_slave="dnsnamedconftemplate_slave";  # for slave DNS replication

	var $activeuser,$isloggedin,$globalfilter,$commandline=false,$erroroccured=false;
	var $connected_mysql_servers=[];
	var $ehcpdir='';
	var $template_dir='';
	var $tr="<tr class='list'>", $td="<td class='list'>", $th="<th class='list'>"; # used in some html buildup
	var $sslkeyfile="/etc/ehcp/server.key";


	var $conf=[
		# config section
		# this is like configuration of many system settings, tablenames etc. by this, changing something is easier, without need to change code..

		'adminname'=>'ehcpdeveloper',
		'adminemail'=>'ehcpdeveloper@gmail.com',
		'wwwbase'=>'/var/www',
		'ehcpdir'=>'',
		'vhosts'=>'/var/www/vhosts',
		'namedbase'=>'/etc/bind',
		'dnsip' => '10.0.0.10',
		'dnsemail'  =>  'your.email.here',

		# mysql definitions
		'mysqlrootuser'=>'root', # this is for db creation.
		'mysqlrootpass'=>'12345',

		# ehcp db table definitions, this is to make code more db-independent...
		'logintable'=>[
			'tablename'=>'panelusers',
			'passwordfunction'=>'md5',
			'usernamefield'=>'panelusername',
			'passwordfield'=>'password',
		],

		'roles'=> [
			'tablename'=>'roles',
			#'baslik'=>['id','rolename','ilgili proje','ilgili kullan??c??'),
			'listfields'=>['id','rolename','panelusername'],
			'linkimages'=>['images/edit.gif','images/delete1.jpg'],
			'linkfiles'=>['?op=roleditor&rolsec=1','?op=roleditor&rolsil=1'],
			'linkfield'=>'id',
			'createtable'=>
				'CREATE TABLE `roles` (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `rolename` varchar(30) COLLATE utf8_turkish_ci DEFAULT NULL,
				  `panelusername` varchar(30) COLLATE utf8_turkish_ci DEFAULT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci',
		],

		'settingstable2'=>[
			'tablename'=>'settings',
			'createtable'=>
				"CREATE TABLE IF NOT EXISTS `settings` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `group` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT '',
				  `reseller` varchar(30) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT '',
				  `panelusername` varchar(30) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT '',
				  `name` varchar(40) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT '',
				  `value` varchar(200) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT '',
				  `longvalue` text CHARACTER SET utf8 COLLATE utf8_turkish_ci,
				  `comment` varchar(100) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT '',
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `group` (`group`,`reseller`,`panelusername`,`name`,`value`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci COMMENT='ehcp db - Table for settings of ehcp'",
		],

		'vpstable'=>[
			'tablename'=>'vps',
			'listfields'=>['vpsname','ip','ip2','ram','description','reseller','panelusername','hostip','state','image_template'],
			'linkimages'=>['images/incele.jpg','images/poweron.gif','images/poweroff.gif','images/pause.gif','images/edit2.gif','images/delete1.jpg'],
			'linkfiles'=>['?op=vps&op2=select','?op=vps&op2=start','?op=vps&op2=shutoff','?op=vps&op2=pause','?op=vps&op2=edit','?op=vps&op2=delete'],
			'linkfield'=>'vpsname',
			'checkfields'=>[
				'addvpscmd'=>'text',
				'ip2'=>'varchar(20)',
				'cdimage'=>'varchar(100)',
			],
			'createtable'=>
				"CREATE TABLE IF NOT EXISTS `vps` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `reseller` varchar(30) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
				  `panelusername` varchar(30) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
				  `status` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
				  `vpsname` varchar(30) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
				  `description` varchar(100) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
				  `hostip` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
				  `ip` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
				  `ip2` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
				  `netmask` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
				  `broadcast` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
				  `gateway` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
				  `image_template` varchar(100) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
				  `cdimage` varchar(100) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
				  `ram` int(11) DEFAULT NULL,
				  `cpu` int(11) DEFAULT NULL,
				  `state` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
				  `ping` varchar(10) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
				  `hdimage` varchar(200) DEFAULT NULL,
				  `vncpassword` varchar(20) DEFAULT NULL,
				  `addvpscmd` text default null,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='ehcp db - list of domains and their properties'",
		],

		'domainstable'=>[
			'tablename'=>'domains',
			'ownerfield'=>'panelusername',
			'resellerfield'=>'reseller',
			'domainfields'=>['id','reseller','panelusername','domainname','status','comment'],
			'listfields'=>['id','reseller','panelusername','domainname','webserverips','status','diskquotaused','diskquota'],
			'linkimages'=>[],
			'checkfields'=>[
				'ssl_certificate_file'=>'varchar(100)',
				'ssl_certificate_key_file'=>'varchar(100)',
				'serverip'=>'varchar(30)',
				'dnsserverips'=>'varchar(200)',
				'webserverips'=>'varchar(200)',
				'mailserverips'=>'varchar(200)',
				'mysqlserverips'=>'varchar(200)',
				'host'=>'varchar(30)',
				'apachetemplate'=>'text',
				'apache2template'=>'text',
				'nginxtemplate'=>'text',
				'dnstemplate'=>'text',
				'aliases'=>'text',
				'diskquotaused'=>'int(4)',
				'diskquota'=>'int(4)',
				'diskquotaovernotified'=>'int(4)',
				'diskquotaoversince'=>'date',
				'graceperiod'=>'int(4) default 7',
				'theorder'=>'int(11)',
				'dnsmaster'=>'varchar(15) default NULL',
				'timeout' => 'datetime default NULL',
			],
		],

		'domainstable2'=>[
			'tablename'=>'domains',
			'listfields'=>['domainname'],
			'linkimages'=>[],
		],

		'subdomainstable'=>[
			'tablename'=>'subdomains',
			'listfields'=>['reseller','panelusername','subdomain','domainname','homedir','ftpusername','comment'],
			'linkimages'=>['images/delete1.jpg'],
			'linkfiles'=>['?op=delsubdomain'],
			'linkfield'=>'id',
			'checkfields'=>[
				'ftpusername'=>'varchar(100)',
				'password'=>'varchar(20)',
				'email'=>'varchar(50)',
				'webserverips'=>'varchar(200)'
			],
		 ],

		'paneluserstable'=> [
			'tablename'=>'panelusers',
			'resellerfield'=>'reseller',
			'usernamefield'=>'panelusername',
			'passwordfield'=>'password',
			'listfields'=>['id','reseller','panelusername','maxdomains','maxemails','quota','maxpanelusers','maxftpusers','maxdbs','name','email'],
			
			'linkimages'=>['images/edit.gif','images/delete1.jpg'],
			'linkfiles'=>['?op=editpaneluser','?op=deletepaneluser'],
			
			'clickimages'=>['images/edit.gif','images/delete1.jpg'],
			'clickfiles'=>['?op=editpaneluser','?op=deletepaneluser'],
			
			'insertfields'=>['panelusername',['password','password'],['maxdomains','default'=>5],['maxemails','default'=>20],['quota','default'=>500],['maxpanelusers','default'=>5],['maxftpusers','default'=>5],['maxdbs','default'=>10],'name','email'],
			'insertfieldlabels'=>['panelusername','Password','maxdomains','maxemails','Quota (Mb)','maxpanelusers','maxftpusers','Max Mysql Databases','Name','Email'],
			'mandatoryinsertfields'=>['panelusername'], # zorunlu insert alanlari
			'editfields'=>['maxdomains','maxemails','quota','maxpanelusers','maxftpusers','maxdbs','name','email'], # edit edildigi zaman g??r??necek alanlar..
			'editlabels'=>['maxdomains','maxemails','quota (MB)','maxpanelusers','maxftpusers','maxdbs','name','email'], # edit edildigi zaman g??r??necek alanlar..
			'viewfields'=>['id','panelusername','maxdomains','maxemails','quota','maxpanelusers','maxdbs','name','email'],
			'linkfield'=>'id',
			'checkfields'=>[
				'comment'=>'varchar(100)'
			],
			'help'=>'description of this table... ehcp control panel users... ',
		],

		'customstable'=>[ # custom dns and http settings
			'tablename'=>'customsettings',
			'listfields'=>['id','domainname','name','comment','value','value2','webservertype'],
			'linkimages'=>['images/delete1.jpg'],
			'linkfiles'=>['?op=deletecustom'],
			'orderby'=>'id',
			'linkfield'=>'id',
			'checkfields'=>[
				'reseller'=>'varchar(100)',
				'panelusername'=>'varchar(100)',
				'domainname'=>'varchar(100)',
				'webservertype'=>'varchar(30)',
				'value2'=>'text',
			],
		],

		'emailuserstable'=>[
			'tablename'=>'emailusers',  # going to be array as above..
			'listfields'=>['email','quota','domainname'],
			'linkimages'=>['images/delete1.jpg','images/edit.gif'],
			'linkfiles'=>['?op=userop&action=emailuserdelete','?op=editemailuser'],
			'linkfield'=>'id',
			'ownerfield'=>'panelusername',
			'resellerfield'=>'reseller',
			# for use with email user logins
			'passwordfunction'=>'encrypt',
			'usernamefield'=>'email',
			'passwordfield'=>'password',

			'checkfields'=>[
				'reseller'=>'varchar(100)',
				'panelusername'=>'varchar(100)',
				'domainname'=>'varchar(100)',
				'status'=>'varchar(10)',
				'autoreplysubject'=>'varchar(100)',
				'autoreplymessage'=>'text',
				'timeout' => 'datetime default NULL',
			],
		],

		'ftpuserstable'=>[
			'tablename'=>'ftpaccounts',  # going to be array as above..
			'ownerfield'=>'panelusername',
			'resellerfield'=>'reseller',
			'listfields'=>['reseller', 'domainname','ftpusername','status','homedir','type'],
			'linkimages'=>[],
			'checkfields'=>[
				'type'=>'varchar(10)',
				'reseller'=>'varchar(100)',
				'panelusername'=>'varchar(100)',
				'domainname'=>'varchar(100)',
				'homedir'=>'varchar(150)',
				'datetime'=>'datetime',
				'expire_date'=>'datetime',
			],
		],

		'operations_table'=>[
			'tablename'=>'operations',  # going to be array as above..
			'listfields'=>['id','user','ip','op','status','tarih','try','info','info2','info3','action'],
			'linkimages'=>[],
			'checkfields'=>[
				'info'=>'varchar(200)',
				'info2'=>'varchar(200)',
				'info3'=>'varchar(200)',
				'user'=>'varchar(30)',
				'ip'=>'varchar(30)',
			]
		],

		'backups_table'=>[
			'tablename'=>'backups',
			'listfields'=>['id','domainname','backupname','filename','date','size','status'],
			'linkimages'=>['images/delete1.gif'],
			'linkfiles'=>['?op=backups&op2=delete&filename='],
			'linkfield'=>'filename',
			'checkfields'=>[
				'status'=>'varchar(100)',
				'domainname'=>'varchar(100)',
				'filename'=>'varchar(200)',
			],
		],

		'mysqldbstable'=>[
			'tablename'=>'mysqldb',
			'listfields'=>['domainname','dbname','host'],
			'linkimages'=>['images/delete1.jpg'],
			'linkfiles'=>['?op=domainop&action=deletedb'],
			'linkfield'=>'id',
			'checkfields'=>[
				'host'=>'varchar(30)',
				'reseller'=>'varchar(100)',
				'panelusername'=>'varchar(100)',
				'domainname'=>'varchar(100)',
			],
		],

		'mysqldbuserstable'=>[
			'tablename'=>'mysqlusers',
			'listfields'=>['domainname','dbname','dbusername','host'],
			'linkimages'=>['images/edit.gif'],
			'linkfiles'=>['?op=dbedituser'],
			'linkfield'=>'id',
			'checkfields'=>[
				'host'=>'varchar(30)',
				'reseller'=>'varchar(100)',
				'panelusername'=>'varchar(100)',
				'domainname'=>'varchar(100)',
			],
		],

		'serverstable'=>[
			'tablename'=>'servers',
			'listfields'=>['servertype','ip','accessip','mandatory','location'],
			'linkimages'=>['images/edit.gif'],
			'linkfiles'=>['?op=editserver'],
			'linkfield'=>'id',
			'checkfields'=>['accessip'=>'varchar(30)'],
		],

		'passwddirectoriestable'=>[
			'tablename'=>'directories',
			'listfields'=>['domainname','username','directory','expire'],
			'linkimages'=>['images/delete1.jpg'],
			'linkfiles'=>['?op=deletedirectory'],
			'linkfield'=>'id',
			'checkfields'=>[
				'reseller'=>'varchar(100)',
				'panelusername'=>'varchar(100)',
				'domainname'=>'varchar(100)',
			],
		],
		'emailforwardingstable'=>[
			'tablename'=>'forwardings',
			'listfields'=>['panelusername','domainname','source','destination'],
			'linkimages'=>['images/delete1.jpg'],
			'linkfiles'=>['?op=delemailforwarding'],
			'linkfield'=>'id',
			'checkfields'=>[
				'reseller'=>'varchar(100)',
				'panelusername'=>'varchar(100)',
				'domainname'=>'varchar(100)',
			],
		],
		'transporttable'=>[ # for email autoreply
			'tablename'=>'transport',
			'createtable'=>
				"CREATE TABLE transport (
					domainname varchar(100) NOT NULL default '',
					transport varchar(128) NOT NULL default '',
					UNIQUE KEY domainname (domainname)
				) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_turkish_ci comment='ehcp db - email transport maps'
				",
		],
		'settingstable'=>[
			'tablename'=>'misc',
			'checkfields'=>[
				'name'=>'varchar(40)',
				'panelusername'=>'varchar(100)',
			],
		],
		'scriptstable'=>[
			'tablename'=>'scripts',
			'linkimages'=>[],
			'checkfields'=>[
				'homepage'=>'varchar(100)',
				'description'=>'text',
				'customfileownerships'=>'text'
			],
		],
		'daemonopstable'=>'operations',
		'hashtable'=>[
			'tablename'=>'hash',
			'createtable'=>
				"CREATE TABLE IF NOT EXISTS `hash` (
				  `email` varchar(100) COLLATE utf8_turkish_ci NOT NULL DEFAULT 'NULL',
				  `hash` varchar(100) COLLATE utf8_turkish_ci DEFAULT NULL,
				  KEY `email_index` (`email`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci COMMENT='to store password remind hash'",
		],
		'logtable'=>[
			'tablename'=>'log',
			'listfields'=>['tarih','panelusername','notified','ip','log'],
			'linkimages'=>[],
			'checkfields'=>[
				'panelusername'=>'varchar(100)',
				'notified'=>'varchar(5)',
			],
		],

		'hostingplans'=>[
			'tablename'=>'hostingplans',
			'listfields'=>['shortname','maxpanelusers','maxdomains','maxemails','maxftpusers','maxmysqldbs'],
			'linkimages'=>[],
			'createtable'=>
				"CREATE TABLE IF NOT EXISTS `hostingplans` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `shortname` varchar(30) COLLATE utf8_turkish_ci DEFAULT NULL,
				  `longname` varchar(100) COLLATE utf8_turkish_ci DEFAULT NULL,
				  `details` text COLLATE utf8_turkish_ci,
				  `maxpanelusers` int(11) DEFAULT NULL,
				  `maxdomains` int(11) DEFAULT NULL,
				  `maxsubdomains` int(11) DEFAULT NULL,
				  `maxemails` int(11) DEFAULT NULL,
				  `maxftpusers` int(11) DEFAULT NULL,
				  `maxmysqldbs` int(11) DEFAULT NULL,
				  `maxmysqlusers` int(11) DEFAULT NULL,
				  `diskquota` int(11) DEFAULT NULL,
				  `bwquota` int(11) DEFAULT NULL COMMENT 'MB, per month, has no affect yet',
				  `maxdirectories` int(11) DEFAULT NULL COMMENT 'password protected dir',
				  `pricepermonth` int(11) DEFAULT NULL,
				  `priceperyear` int(11) DEFAULT NULL,
				  PRIMARY KEY (`id`),
				  KEY `shortname` (`shortname`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci AUTO_INCREMENT=1",
		],
	]; # end config

/*
for almost every record:
panelusername: who is the owner of that, who setuped that. cannot be empty.
resellername: who is the reseller of that panelusername. cannot be empty.
domainname: to which domain is that related. empty if not related to domainname
*/

function set_ehcp_dir($dirname){
	# extra variables will be removed later, only one should be used.
	$this->ehcpdir=$dirname;
	$this->template_dir=$this->ehcpdir."/etc/current_webserver_templates";
	$this->config_dir=$this->ehcpdir."/etc/current_webserver_configs";
	$this->mydir=$dirname;
	$this->conf['ehcpdir']=$dirname;
}

public function __construct() {
	global $skipUpdateWebstats,$debuglevel;

	include('config.php');
	$this->set_ehcp_dir(dirname(__FILE__));
	$this->vhostsdir=$this->conf['vhosts'];
	$this->skipupdatediskquota=$skipupdatediskquota;



	$this->dbhost=$dbhost;
	$this->dbusername=$dbusername;
	$this->dbpass=$dbpass;
	$this->dbname=$dbname;
	$this->conf['mysqlrootpass']=$dbrootpass;
	$this->defaultlanguage=$defaultlanguage;
	$this->isDemo=$isdemo;
	if(!(isset($this->isDemo))) $this->isDemo=False;
	$this->statusActivePassive=["active"=>"active","passive"=>"passive"];

	if(isset($this->debuglevel)){
		#....
	}
	$debuglevel=$this->debuglevel; # for outer functions using debuglevel

	$this->wwwowner=$this->wwwuser.':'.$this->wwwgroup;
	$this->ftpowner=$this->ftpuser.':'.$this->ftpgroup;

}


function run() {
	global $commandline, $argv, $argc, $op;
	
	#echo "\n\nPid:".$this->get_pid("progname")."\n\n";
	$this->initialize1();

	$commandline=false;
	$user = $_SERVER['HTTP_USER_AGENT'];
	if($argv and $argc and (is_array($argv))and (!$user)) {
	        $commandline=true;
	        $op=$argv[1];
	        print_r($argv);
	        echo "Commandline active, argc: $argc \n op:($op)\n argv:".print_r($argv);

	} else {
	        session_start();
	        $this->getVariable("op");
	}

	$this->debugecho("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__."  Op:($op) ",4,false);
	$this->debugecho("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__."<br>Get: ".print_r2($_GET)."<br>Post:".print_r2($_POST),4,false);

	#$this->serverPlan=new serverPlan();

	$this->commandline=$commandline;
	$op=htmlspecialchars($op);
	$op=substr($op, 0, 40);
	$this->op=$op;
	$this->initialize2();

	# this is actual application runner, maps urls to functions.. a kind of controller..
	$this->runOp($this->op);
	$this->show(); # like view layer. 
}


function check_php_version(){
	global $php_service_name; # used also in install_lib.php
	# same code exists in install_lib also. sync both code !
	# used in php, fpm etc installations. 
	$ver=substr(phpversion(),0,3); #7.2
	// $this->println("Php version:".phpversion()."  ver variable: $ver");

	if(version_compare(phpversion(), '7.0.0', '<')) {  # make compatible install in many versions of Ubuntu/Debian, Ubuntu 16.04 comes with php7, while olders are different
		$this->php_version=5;
		$this->php_version_tag="5";
		$php_service_name="php5-fpm";
		$this->php_etc_dir="/etc/php5";
		$this->php_fpm="php5-fpm";
		$this->php_sock="127.0.0.1:9000";
	} else {
		$this->php_version=7;
		$this->php_version_tag="";		
		$this->php_etc_dir="/etc/php/".$ver;
		$this->php_fpm="php{$ver}-fpm";
		$php_service_name="php{$ver}-fpm";
		$this->php_sock="unix:/run/php/php{$ver}-fpm.sock"; # fastcgi_pass    unix:/run/php/php7.2-fpm.sock;   # buna gerek kalmad??, zira php_etc_dir do??ru hesaplayacak ve listen = 9000 yapacak. nginx configler de ona g??re ayarl??. 
	}

}


function table_exists($tb) {
	$q="show tables like '$tb'";
	$res=$this->query($q);
	return count($res)>0;
}

function check_database(){
	$tables=["domains", "emailusers", "ftpaccounts"];
	$res=False;
	foreach ($tables as $t) {
		$res=$res or $this->table_exists($t);
	}

	if($res) { # lots of tables not there
		include("inc/database_problem.php");
		die();
	}
}

function initialize1(){
	# DB stuff, so that escape can work... 
	$this->connectTodb();
	$this->check_database();
	
	if(@file_exists("/etc/init.d/bind9")) { # in Gui, this will not work. only for Commandline 
		$this->named="bind9";
	} elseif (@file_exists("/etc/init.d/named")) {
		$this->named="named";
	} else {
		$this->named="no_dns___"; # Error/unsupported or bind somehow not installed.
	}


	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {  # Suggestion by admin@e-speak24.pl
		$ipproxy = $_SERVER['HTTP_X_FORWARDED_FOR']; 
	} else { 
		$ipproxy=$_SERVER["REMOTE_ADDR"]; 
	}

    $this->clientip = $this->escape2($ipproxy);
	$this->referer = $this->escape(getenv("HTTP_REFERER"));
}

function initialize2(){
	# burda herhangi class initialization yapilacak.. basta yapilacak isler..
	global $commandline,$ehcpversion;
	#if(!$commandline)$this->output.="<font size=+2>".$this->appname."<br><br></font>";
	$this->myversion=$ehcpversion;



	$this->check_php_version();
	$this->debugecho("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__,4,false);
	$this->syncConfigs();

	$this->passivefilt="status<>'".$this->status_active."' or status is null";
	$this->activefilt="status='".$this->status_active."'";
	$this->loadLanguage(); # load default en to handle errors in loadconfig,
	$this->checkInstall();
	$this->loadConfig();

	if(!$this->isNoPassOp() and $this->requirePassword ) $this->securitycheck();
	$this->loadLanguage(); # load again to activate actual lang in config.

	#functions that has to be after securitycheck
	#$this->output.=print_r3($this->userconfig);

	if($this->isadmin()) {
		$this->globalfilter=''; # burasi, securitycheck den sonra olmali. isadmin yoksa calismaz.
	} else $this->globalfilter="(reseller='".$this->activeuser."' or panelusername='".$this->activeuser."')";

	if(!$this->isadmin()) {
		$userinfo=$this->query("select * from ".$this->conf['paneluserstable']['tablename']." where panelusername='$this->activeuser'", "dologin2");
		$this->userinfo=$userinfo[0];
		if($this->userinfo['maxpanelusers']>0) $this->isreseller=True;
	}

	$this->loadServerPlan();
	$this->url='http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	$this->output.=$this->check_failed_logins();
	if($commandline) {
		$this->restart_webserver(); # Starting with Ubuntu 16.04, apache config needs to be reloaded after server restart.. strange situation for U16.04, apache config is not loaded after server restart.. !
		$this->restart_ftp_daemon();
		$this->echoln("Finished initialize");
	}
	#$this->check_mysql_connection();
}

function check_webserver($retry=0){
	# checks webserver in regular intervals, try to restart it with different configs.. 
	if($this->miscconfig['switchtoapacheonerror']=='') {
		print __FUNCTION__.": not checking, as switchtoapacheonerror is disabled \n";
		return;
	}

	print __FUNCTION__.": checking webserver: \n";
	$f=file_get_contents("http://localhost");
	if(strlen($f)>0) {
		print __FUNCTION__.": webserver seems normal. continuing normally.\n";
	} else {
		if($retry<1) {
			sleep(5);
			print __FUNCTION__.": webserver seems DOWN, checking again.. \n";
			$this->check_webserver($retry+1);			
		}
		if($this->miscconfig['switchtoapacheonerror']=='') {
			print __FUNCTION__.": webserver seems DOWN, however, switchtoapacheonerror disabled, so, doing nothing.. \n";
		} else {
			print __FUNCTION__.": webserver seems DOWN, fixing apache configuration by some operations such as disabling custom http & ssl.. \n";
			$this->fixApacheConfigNonSsl2();
		}
	}
	return True;
}

function counter_reached($counter,$count){
	# can be used to count something...
	if(intval($this->miscconfig[$counter])>0) {
		$nextval=intval($this->miscconfig[$counter])-1;
		$this->setConfigValue($counter,$nextval);
		return False; # check sometime,
	}
	$this->setConfigValue($counter,$count);
	return True;
}

function check_ehcp_version(){
	global $ehcpversion;
	if($this->latest_version<>'') return; # check once
	if(!$this->counter_reached('versionwarningcounter',20)) return False; # check 20 login later again.


	$this->latest_version=trim(@file_get_contents("http://www.ehcp.net/latest_version.php?ip=".$this->dnsip));
	if($this->latest_version<>'' and $this->latest_version<>$ehcpversion) {
		$str="Your ehcp version is different($ehcpversion) than latest($this->latest_version) version. Either your ehcp is old, or you are using a new beta/test version. Look at <a target=_blank href='http://ehcp.net/?q=node/153'>here for download and upgrade</a>";
		$this->warnings.=$str;
		$this->infotoadminemail($str,"ehcp version warning for ".$this->dnsip,false);
	}

}

function loadServerPlan(){
	$this->singleserverip=$this->conf['dnsip']; # if there is only one server...
	#$this->output.=__FUNCTION__.":".$this->singleserverip."<br>";
	# more servers will be coded here...

}

function load_module($name){
	if(!defined($name.'_file_loaded')) {
		$this->echoln2(__FUNCTION__.": Sory, that module file (".$name."_file_loaded) not loaded yet. check code. (php autoload does not work with CLI)");
		return False;
	}
	if(gettype($this->$name)<>'object') $this->$name=new $name($this,$name); # initialize new module, only if not done before.
	return True;
}

function call_func_in_module($name,$func,$params=Null){
	if(!$this->load_module($name)) return True; # not returning False, because, if False, other parts of classapp retries the operation a few times. this is not needed now.

	if($params==Null) return $this->$name->$func(); # a function with no args
	else return $this->$name->$func($params); # a func with named arguments (named array), as used in many parts of this file
}

function check_module_tables(){
	# to be coded later.
}


function assign_role(){
	$alanlar=['rolename','panelusername'];
	foreach($alanlar as $al) global ${$al};
	$degerler=$this->getVariable($alanlar);

	if($panelusername and $rolename) {
		$this->executequery("insert into roles (rolename,panelusername) values ('$rolename','$panelusername')");
		$this->output.="Added new role";
	} else {
		$inputparams=['rolename','panelusername'];
		$this->output.=inputform5($inputparams);
	}
}

function list_roles(){
	$this->requireAdmin();
	$this->listTable('','roles');
}

function diognise(){
	$this->requireAdmin();

	$this->output.="To be coded.. will copy/show necessary files, for checking server's configs.. for letting admin check webbased";

}

function docertbot(){
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$this->requireAdmin(); 
	$this->requireNoDemo(); 
	return $this->addDaemonOp('certbot','', $domainname,'','certbot');
}


function runOp($op){ # these are like url to function mappers...  maps op variable to some functions in ehcp; This also can be seen as a controller in MVC model.
	global $id,$domainname,$op2,$_insert;
	$this->getVariable(['id','domainname','op2','_insert']);
	$this->debugecho("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__."  Op:($op) Op2:($op2)",4,false);
	$op=strtolower($op);
	$otheroperations=['advancedsettings'];

	if ($this->commandline) {
		print "\n-----> Commandline: Op: $op \n";
	}


	switch ($op) {

		case "change_domainname"		: return $this->change_domainname(); break;
		case 'docertbot'				: return $this->docertbot(); break;
		case 'diognise'					: return $this->diognise();break;
		
		case 'list_roles'				: return $this->list_roles();break;
		case 'assign_role'				: return $this->assign_role();break;
		case 'failedlogins'				: return $this->failedlogins();break;

		#ssl related:
		case 'adjust_ssl'				: return $this->call_func_in_module('ssl_module','adjust_ssl'); break;
		case 'generate_ssl_config1'		: return $this->call_func_in_module('ssl_module','generate_ssl_config1'); break;

		case 'pagerewrite'				: return $this->pagerewrite();break;

		# vpn related : todo: we need to move these function mappings inside modules, if function is actuallin in module... 
		case 'list_vpn'					: return $this->call_func_in_module('vpn_module','list_vpn'); break;
		case 'add_vpn'					: return $this->call_func_in_module('vpn_module','add_vpn'); break;
		case 'del_vpn'					: return $this->call_func_in_module('vpn_module','del_vpn'); break;
		
		# other
		case 'activate'					: return $this->activate();break;
		case 'settings'					: return $this->settings();break;
		case 'adjust_system'			: return $this->adjust_system();break;
		case 'redirect_domain'			: return $this->redirect_domain();break;
		case 'information'				: return $this->information($id);break;

		#multi-server operations:
		case 'multiserver_add_domain'	: return $this->multiserver_add_domain();break;

		case 'new_sync_all'				: return $this->new_sync_all();break;
		case 'new_sync_domains'			: return $this->new_sync_domains();break;
		case 'new_sync_dns'				: return $this->new_sync_dns();break;
		case 'multiserver_add_ftp_user_direct': return $this->gui_multiserver_add_ftp_user_direct();break;

		#single-server operations:
		case 'bulkaddemail'				: return $this->bulkAddEmail();break;
		case 'whitelist'				: return $this->whitelist();break;
		case 'fixmailconfiguration'		: return $this->fixMailConfiguration();break;
		case 'dofixmailconfiguration'	: $this->requireAdmin(); $this->requireNoDemo(); return $this->addDaemonOp('fixmailconfiguration','','','','fix mail configuration');break;
		case 'dofixapacheconfigssl'		: $this->requireAdmin(); $this->requireNoDemo(); return $this->addDaemonOp('fixApacheConfigSsl','','','','fixApacheConfigSsl');break;
		case 'dofixapacheconfigsslonly'	: $this->requireAdmin(); $this->requireNoDemo(); return $this->addDaemonOp('fixApacheConfigSslOnly','','','','fixApacheConfigSslOnly');break;
		case 'dofixapacheconfignonssl'	: $this->requireAdmin(); $this->requireNoDemo(); return $this->addDaemonOp('fixApacheConfigNonSsl','','','','fixApacheConfigNonSsl');break;
		case 'dofixapacheconfignonssl2'	: $this->requireAdmin(); $this->requireNoDemo(); return $this->addDaemonOp('fixApacheConfigNonSsl2','','','','fixApacheConfigNonSsl2');break;
		
		case 'rebuild_webserver_configs': return $this->rebuild_webserver_configs();break;

		case 'updatediskquota'			: return $this->updateDiskQuota();break;
		case 'doupdatediskquota' 		: $this->addDaemonOp('updatediskquota','',$domainname,'','update disk quota');return $this->displayHome();break;

		#editing of dns/apache templates for domains, on ehcp db
		case 'editdnstemplate'			: return $this->editDnsTemplate();break;
		case 'editwebservertemplate'		:
		case 'edit_webserver_template'		: return $this->edit_webserver_template();break;
		case 'editdomainaliases'		: return $this->editDomainAliases();break;

		case 'changedomainserverip'		: return $this->changedomainserverip();break;
		case 'warnings'					: break; # this will be written just before show..
		case 'bulkadddomain'			: return $this->bulkaddDomain();break ;
		case 'bulkdeletedomain' 		: return $this->bulkDeleteDomain();break ;
		case 'exportdomain'				: return $this->exportDomain();break;

		case 'adddnsonlydomain' 		: return $this->addDnsOnlyDomain();break;

		case 'addslavedns'				: return $this->addSlaveDNS();break;
		case 'removeslavedns'			: return $this->removeSlaveDNS();break;
		case 'addcustomftp'				: return $this->addCustomFTP();break;
		case 'adddnsonlydomainwithpaneluser': return $this->addDnsOnlyDomainWithPaneluser();break;

		case 'getselfftpaccount'		: return $this->getSelfFtpAccount();break;
		case 'adddomaintothispaneluser'	: return $this->addDomainToThisPaneluser();break;
		case 'add_domain_install_wordpress'	: return $this->add_domain_install_wordpress();break;

		case 'dodownloadallscripts'		: return $this->doDownloadAllscripts();break;
		case 'choosedomaingonextop'		: return $this->chooseDomainGoNextOp();break;

		case 'getmysqlserver'			: return $this->getMysqlServer();break;

		case 'emailforwardingsself'		: return $this->emailForwardingsSelf();break;
		case 'addemailforwardingself'	: return $this->addEmailForwardingSelf();break;

		case 'cmseditpages'				: return $this->cmsEditPages();break;
		case 'listservers' 				: return $this->listServers();break;
		case 'addserver'				: return $this->addServer();break;
		case 'addiptothisserver'		: return $this->add_ip_to_this_server();break;
		case 'setactiveserverip'		: return $this->set_active_server_ip();break;


		case 'advancedsettings'			: return $this->advancedsettings();break;
		case 'delemailforwarding'		: return $this->delEmailForwarding();break;
		case 'addemailforwarding'		: return $this->addEmailForwarding();break;
		case 'emailforwardings' 		: return $this->emailForwardings();break;
		case 'addscript'				: return $this->addScript();break;
		case 'addnewscript'	 			: return $this->addNewScript();break;

		case 'suggestnewscript' 		: return $this->suggestnewscript();break;
		case 'downloadfile'				: return $this->downloadfile();break;
		case 'listselectdomain' 		: return $this->listselectdomain();break;
		case 'selectdomain'	 			: return $this->selectdomain($id);break;
		case 'deselectdomain'   		: return $this->deselectdomain();break;
		case 'otheroperations'  		: return $this->otheroperations();break;


		case 'loadconfig'	   			: return $this->loadConfig();break;
		#case 'showconf'					: return $this->showConfig();break;
		case 'changemypass'				: return $this->changeMyPass();break;

		# for mysql, stop and start is meaningless, because if mysql cannot run, then, panel also cannot be accessible or this functions do not work.
		case 'dorestartmysql'			: $this->requireAdmin(); $this->requireNoDemo(); return $this->add_daemon_op(['op'=>'service','info'=>'mysql','info2'=>'restart']); break;

		case 'dostopapache2'			: $this->requireAdmin(); $this->requireNoDemo(); return $this->add_daemon_op(['op'=>'service','info'=>'apache2','info2'=>'stop']); break;
		case 'dostartapache2'			: $this->requireAdmin(); $this->requireNoDemo(); return $this->add_daemon_op(['op'=>'service','info'=>'apache2','info2'=>'start']); break;
		case 'dorestartapache2'			: $this->requireAdmin(); $this->requireNoDemo(); return $this->add_daemon_op(['op'=>'service','info'=>'apache2','info2'=>'restart']); break;

		case 'dostopvsftpd'				: $this->requireAdmin(); $this->requireNoDemo(); return $this->add_daemon_op(['op'=>'service','info'=>'vsftpd','info2'=>'stop']); break;
		case 'dostartvsftpd'			: $this->requireAdmin(); $this->requireNoDemo(); return $this->add_daemon_op(['op'=>'service','info'=>'vsftpd','info2'=>'start']); break;
		case 'dorestartvsftpd'			: $this->requireAdmin(); $this->requireNoDemo(); return $this->add_daemon_op(['op'=>'service','info'=>'vsftpd','info2'=>'restart']); break;

		case 'dostopbind'				: $this->requireAdmin(); $this->requireNoDemo(); return $this->add_daemon_op(['op'=>'service','info'=>'bind9','info2'=>'stop']); break;
		case 'dostartbind'				: $this->requireAdmin(); $this->requireNoDemo(); return $this->add_daemon_op(['op'=>'service','info'=>'bind9','info2'=>'start']); break;
		case 'dorestartbind'			: $this->requireAdmin(); $this->requireNoDemo(); return $this->add_daemon_op(['op'=>'service','info'=>'bind9','info2'=>'restart']); break;

		case 'dostoppostfix'			: $this->requireAdmin(); $this->requireNoDemo(); return $this->add_daemon_op(['op'=>'service','info'=>'postfix','info2'=>'stop']); break;
		case 'dostartpostfix'			: $this->requireAdmin(); $this->requireNoDemo(); return $this->add_daemon_op(['op'=>'service','info'=>'postfix','info2'=>'start']); break;
		case 'dorestartpostfix'			: $this->requireAdmin(); $this->requireNoDemo(); return $this->add_daemon_op(['op'=>'service','info'=>'postfix','info2'=>'restart']); break;


		case 'donewsyncdomains'			: $this->requireAdmin(); return $this->add_daemon_op(['op'=>'new_sync_domains']); break;
		case 'donewsyncdns'				: $this->requireAdmin(); return $this->add_daemon_op(['op'=>'new_sync_dns']); break;

		case 'dosyncdomains'			: return $this->addDaemonOp('syncdomains','','','','sync domains');break;
		case 'dosyncdns'				: return $this->addDaemonOp('syncdns','','','','sync dns');break;
		case 'dosyncftp' 				: return $this->addDaemonOp('syncftp','','','','sync ftp for nonstandard homes');break;
		case 'dosyncapacheauth'			: return $this->addDaemonOp('syncapacheauth','','','','sync apache auth');break;
		case 'options'		  			: return $this->options();
		case 'dump_config'	  			: return $this->dump_config();


		case 'backups'					: return $this->backups();break;
		case 'dobackup'					: return $this->doBackup();break;
		case 'dorestore'				: return $this->doRestore();break;
		case 'listbackups'				: return $this->listBackups();break;

		# these sync functions are executed in daemon mode.
		case 'updatehostsfile'			: return $this->updateHostsFile();break;
		case 'syncdomains'				: return $this->syncDomains();break;
		case 'syncftp'					: return $this->syncFtp();break;
		case 'syncdns'					: return $this->syncDns();break;
		case 'syncall'					: return $this->syncAll();break;
		case 'syncapacheauth'			: return $this->syncApacheAuth();break;
		case 'restart_webserver'		: return $this->restart_webserver();break;
		case 'fixapacheconfigssl'		: return $this->fixApacheConfigSsl();break;
		case 'fixapacheconfigsslonly'	: return $this->fixApacheConfigSslOnly();break;
		case 'fixapacheconfignonssl'	: return $this->fixApacheConfigNonSsl();break;
		case 'fixapacheconfignonssl2'	: return $this->fixApacheConfigNonSsl2();break;


		#case 'syncallnew'	: return $this->syncallnew();break;
		case 'listdomains'				: return $this->listDomains();break;  # ayni zamanda domain email userlarini da listeler.
		case 'subdomains'	   			: return $this->subDomains();	break;
		case 'addsubdomain'	 			: return $this->addSubDomain();  break;
		case 'addsubdomainwithftp'		: return $this->addSubDomainWithFtp();  break;
		case 'addsubdirectorywithftp'	: return $this->addSubDirectoryWithFtp();  break;
		case 'sync_directories'			: return $this->sync_directories(); break;


		case 'delsubdomain'	 			: return $this->delSubDomain();  break;


		case 'editdomain'				: return $this->editdomain();
		case 'listpassivedomains'		: return $this->listDomains('',$this->passivefilt);break;
		case 'phpinfo'					: return $this->phpinfo();break;
		case 'help'						: return $this->help();break;
		case 'syncpostfix'				: return $this->syncpostfix();break;
		case 'listemailusers'			: return $this->listemailusers();break;
		case 'listallemailusers'		: return $this->listallemailusers();break;
		case 'listpanelusers'   		: return $this->listpanelusers();break;
		case 'resellers'				: return $this->resellers();break;

		case 'deletepaneluser'  		: return $this->deletepaneluser();break;

		case 'operations'	   			: $this->requireAdmin();$this->listTable('operations','operations_table','');break;

		case 'listallftpusers'  		: return $this->listAllFtpUsers();break;
		case 'listftpusersrelatedtodomains': return $this->listAllFtpUsers("domainname<>''");break;
		case 'listftpuserswithoutdomain': return $this->listAllFtpUsers("domainname='' or domainname is null");break;
		case 'listftpusers'	 			: return $this->listftpusers();break;
		case 'sifrehatirlat'			: return $this->sifreHatirlat();break;
		case 'todolist'					: return $this->todolist();break;
		case 'adddomain'				: return $this->addDomain();break;
		case 'add_domain_db'			: return $this->add_domain_db();break;
		case 'adddomaineasy'			: return $this->addDomainEasy();break;
		case 'adddomaineasyip'			: return $this->addDomainEasyip();break;
		case 'transferdomain'	   		: return $this->transferDomain(); break;
		case 'deletedomain'				: return $this->deleteDomain();break;
		case 'addemailuser'				: return $this->addEmailUser();break;
		case 'addftpuser'				: return $this->addFtpUser();break;
		case 'addftptothispaneluser'	: return $this->addFtpToThisPaneluser();break;# added in 7.6.2009
		case 'add_ftp_special'			: return $this->add_ftp_special();break;

		case 'userop'		   			: return $this->userop();break;
		case 'domainop'		 			: return $this->domainop();break;
		case 'addmysqldb'	   			: return $this->addMysqlDb();   break;
		case 'addmysqldbtouser' 		: return $this->addMysqlDbtoUser();   break;
		case 'addpaneluser'				: return $this->addPanelUser();break;
		case 'editpaneluser'			: return $this->editPanelUser();break;
		case 'editftpuser'				: return $this->editFtpUser();break;
		case 'domainsettings'			: return $this->domainSettings();break;

		case 'logout'					: return $this->logout();break;
		case 'daemon'					: return $this->daemon();break;
		case 'test'						: return $this->test();	break;
		case 'aboutcontactus'   		: return $this->aboutcontactus();break;
		case 'applyforaccount'  		: return $this->applyforaccount();break;
		case 'applyfordomainaccount'	: return $this->applyfordomainaccount();break;
		case 'applyforftpaccount'		: return $this->applyforftpaccount();break;
		case 'setconfigvalue2'  		: return $this->setConfigValue2($id);break;
		case 'customhttp'				: return $this->customHttpSettings();break;
		case 'addcustomhttp'			: return $this->addCustomHttp();break;
		case 'deletecustom'				: return $this->deleteCustomSetting();break;
		case 'customdns'				: return $this->customDnsSettings();break;
		case 'addcustomdns'				: return $this->addCustomDns();break;
		case 'dbedituser'	   			: return $this->dbEditUser();break;
		case 'dbadduser'				: return $this->dbAddUser();break;

		case 'custompermissions'		: return $this->custompermissions();break;
		case 'addcustompermission'		: return $this->addcustompermission();break;

		case 'editemailuser'			: # same as below
		case 'editemailuserself'		: return $this->editEmailUser();break;

		case 'editemailuserautoreplyself':
		case 'editemailuserautoreply'	: return $this->editEmailUserAutoreply();break;

		case 'editemailuserpasswordself':
		case 'editemailuserpassword'	: return $this->editEmailUserPassword();break;

		case 'directories'	  			: return $this->directories();break;
		case 'listmyalldirectories'		: return $this->listMyAllDirectories();break;
		case 'adddirectory'	 			: return $this->addDirectory();break;
		case 'deletedirectory'  		: return $this->deleteDirectory();break;
		case 'changetemplate'   		: return $this->changetemplate();break;
		case 'addredirect'				: return $this->addRedirect();break;
		case 'serverstatus'				: return $this->serverStatus();break;
		case 'setlanguage'				: $this->setLanguage($id);$this->displayHome();break;
		case 'setdefaultdomain'			: $this->setDefaultDomain();$this->displayHome();break;

		case 'dologin'					: # default anasayfa, same as below:
		case ''							: $this->displayHome();break;

		# virtual machine (vps) opcodes:
		case 'vps_home'					: return $this->call_func_in_module('vps_module','vps_home'); break;
		case 'vps'						: return $this->call_func_in_module('vps_module','vps'); break;
		case 'vps_mountimage'			: return $this->call_func_in_module('vps_module','vps_mountimage'); break;
		case 'vps_dismountimage'		: return $this->call_func_in_module('vps_module','vps_dismountimage'); break;
		case 'add_vps'					: return $this->call_func_in_module('vps_module','add_vps'); break;
		case 'add_vps_hdd'				: return $this->call_func_in_module('vps_module','add_vps_hdd'); break;
		case 'copy_vps_image'			: return $this->call_func_in_module('vps_module','copy_vps_image'); break;


		default							: return $this->errorText("(runop) internal ehcp error: Undefined operation: $op <br> This feature may not be complete");break;

	}# switch
	return True;

}# func runop

function activate(){
	$alanlar=['panelusername','code','newpass'];
	foreach($alanlar as $al) global ${$al};
	$degerler=$this->getVariable($alanlar);

	if($panelusername) {
		$info=$this->getPanelUserInfo('',$panelusername);
		$email=$info['email'];
		if($email=='') return;
	}


	if(!$panelusername){
		$this->output.=inputform5('panelusername');
	} elseif($panelusername and !$code) {
		$hash=get_rand_id(10);
		$r=$this->executequery("insert into  hash (email,hash)values('$email','$hash')");
		$msg="Your activation code: $hash";
		mail($email,"ehcp activation code",$msg,"From: ".$this->conf['adminemail']);
		$this->output.="Your activation is sent to your email. check it. ".inputform5(['panelusername','code','newpass']);
	} elseif($panelusername and $code) {
		$filt3="email='$email' and hash='$hash'";
		$sayi=$this->recordcount("hash",$filt3);
		if($sayi==0) $this->errorTextExit("Wrong activation, verify the activation code in your email");

		if($this->conf['logintable']['passwordfunction']==''){
			$set="'$newpass'";
		} else {
			$set=$this->conf['logintable']['passwordfunction']."('$newpass')";
		}

		$this->executeQuery("update panelusers set status='active',{$this->conf['logintable']['passwordfield']}=$set where status='passive' and panelusername='$panelusername'");
	}

}


function ssl_certbot($domainname){
	/*
		Uses certbot / letsencrypt to build cert files.. 
	*/
	$this->requireCommandLine();
	$domainhome=$this->get_domain_home($domainname);
	$cmd="certbot certonly -n --agree-tos --email {$this->conf['adminemail']} --webroot -w $domainhome -d $domainname";
	passthru2($cmd);

	/* 
	Bundan sonra, ba??ar??l?? ise, ssl configin y??klenmesi ve keylerin yerine konulmas?? yap??lacak.. 
	/etc/letsencrypt/live/xxx.com alt??nda oluyor.. 
	
	burada, domain baz??nda ssle ge??me yapmam laz??m. 
	domain tablosuna alan ekleyip, ssl enable olan domainleri webserver_template_ssl den y??klemem laz??m. 
	Ayr??ca, php-fpm in hangi porttan dinledi??ini bulmak laz??m. 9000 veya unix socket:

netstat -anp | grep php | grep sock
unix  2      [ ACC ]     STREAM     LISTENING     663470820 446/php-fpm: pool w /run/php/php7.0-fpm.sock

fastcgi_pass    unix:/run/php/php7.0-fpm.sock;
veya:
fastcgi_pass   127.0.0.1:9000;

??lgili ayar:
listen = /run/php/php7.0-fpm.sock
dosya:
/etc/php/7.0/fpm/pool.d/www.conf
Bu ayara ??imdilik dokunmad??m, install_lib i??inde var. 9000 olarak kald??. 


	ssl_certificate /etc/letsencrypt/live/xxxxx.com.tr/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/xxxxx.com.tr/privkey.pem;


    /etc/letsencrypt/live/ehcp.net/fullchain.pem
    /etc/letsencrypt/live/ehcp.net/privkey.pem

 cert.pem -> ../../archive/xxx.com/cert1.pem
 chain.pem -> ../../archive/xxx.com/chain1.pem
 fullchain.pem -> ../../archive/xxx.com/fullchain1.pem
 privkey.pem -> ../../archive/xxx.com/privkey1.pem


	*/

	return True;
}


function pagerewrite(){
	global $op2,$_insert;
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$this->output.="This function is being tested with nginx only <br>";

	$alanlar=['frompage','topage','redirecttype'];
	foreach($alanlar as $al) global ${$al};
	$degerler=$this->getVariable($alanlar);

	$table=$this->conf['customstable'];


	if($op2=='add') {

		if($_insert){
			switch($this->miscconfig['webservertype']) {
				case 'nginx':
					if($redirecttype=='exactmatch') $val="rewrite ^($frompage)\$ $topage break; \n";
					else $val="rewrite $frompage $topage break; \n";
				break;

				#case 'apache2:
				default: $this->errorTextExit("Your webserver is not supported for rewrite using ehcp gui:".$this->miscconfig['webservertype']);
			}

			$this->addCustomHttpDirect($domainname,$val,"pagerewrite");

		}else{
			$alanlar=[['frompage','righttext'=>''],['topage'],['redirecttype','radio','secenekler'=>['exactmatch'=>'Exact Match Ex: frompage: /basvuru topage: /en/basvuru.html','partialmatch'=>'Partial Match, nginx style, <a href=\'http://wiki.nginx.org/NginxHttpRewriteModule#rewrite\'>examples</a>']]];
			$this->output.=inputform5($alanlar);
		}
		$this->output.="nginx, For partial, examples: <br>
		frompage: ^(/download/.*)/media/(.*)\..*$ <br>
		topage: $1/mp3/$2.mp3 <br>
		<br>
		frompage: ^/users/(.*)$ <br>
		topage: /showuser.php?uid=$1 <br>";

	} else
	$this->listTable("Page redirects:","customstable","domainname='$domainname' and comment='pagerewrite'");

	$this->showSimilarFunctions("pagerewrite");
}

function upload_file($srcfile,$dstfile){
	$srcfilename=$_FILES[$srcfile]['name'];

	$this->output.= "Copy (".$_FILES[$srcfile]['tmp_name'].") -> ".$dstfile;

	if(copy($_FILES[$srcfile]['tmp_name'], $dstfile)) {
		$this->output.= "<br>Dosya y??kleme ba??ar??l??<BR/>";
		$this->output.= "File Name :".$_FILES[$srcfile]['name']."<BR/>";
		$this->output.= "File Size :".$_FILES[$srcfile]['size']."<BR/>";
		$this->output.= "File Type :".$_FILES[$srcfile]['type']."<BR/>";
		return True;
	}
	else {
		$this->output.="<br><big><b>Dosya y??klerken Hata olu??tu ($path)</b></big><br>".print_r2($_FILES);
		return False;
	}
}

#############################
# SSL related functions.. 

function ssl_fix_template($dom,$config){
	if(!file_exists($this->ehcpdir."/upload/SSLCertificateFile_$dom.crt") or !file_exists($this->ehcpdir."/upload/SSLCertificateChainFile_$dom.crt")) return $config; # bu domaine has bir crt (sertifika dosyasi) yoksa, degistirme.

$new="
SSLCertificateFile ".$this->ehcpdir."/upload/SSLCertificateFile_$dom.crt
SSLCertificateChainFile ".$this->ehcpdir."/upload/SSLCertificateChainFile_$dom.crt

";
	$ret=str_replace("SSLCertificateFile /etc/ssl/certs/server.crt", $new, $config);
	return $ret;
}

function generate_server_key_file(){
	if(!file_exists($this->sslkeyfile)) {
		passthru2("openssl genrsa -out $this->sslkeyfile 2048"); # prepare server keyfile
		passthru2("cp -vf $this->sslkeyfile /etc/ssl/private/");
	}
}

# Verdigi hata: SSL Library Error: error:0B080074:x509 certificate routines:X509_check_private_key:key values mismatch
# 

function generateSslFiles(){
	$nowDate = time();
	$certFile = "/etc/ssl/certs/server.crt";

	// If the certificate file does not exist or the last modification date of the certificate is greater than 365 days
	// Generate a new self signed one that lasts another 365 days
	if(!file_exists($certFile) || !file_exists($this->sslkeyfile) || ($nowDate - filemtime($certFile) >= (86400 * 365))) {
		$findarray=['webserverip'];
		$replacearray=[$this->getWebServer()];
		$this->replaceArrayPutInFile("$this->ehcpdir/LocalServer.cnf","$this->ehcpdir/LocalServerIP.cnf",$findarray,$replacearray);
		$this->generate_server_key_file();
		passthru2("openssl req -new -key $this->sslkeyfile -out $this->ehcpdir/server.csr -config $this->ehcpdir/LocalServerIP.cnf"); # prepare certificate signing request (csr)
		passthru2("openssl x509 -req -days 365 -in $this->ehcpdir/server.csr -signkey $this->sslkeyfile -out $this->ehcpdir/server.crt"); # sign my own cert. this is self signed one. 

		passthru2("cp -vf $this->ehcpdir/server.crt /etc/ssl/certs/");
		passthru2("cp -vf $this->sslkeyfile /etc/ssl/private/");
	}
}


#include_once('modules/module_index.php'); # verdigi hata: Parse error: syntax error, unexpected T_INCLUDE_ONCE, expecting T_FUNCTION in /var/www/new/ehcp/classapp.php on line 1044

function information($id,$link=false){
	if($link) return " - <a href=index.php?op=information&id=$id>?</a>";

	switch($id){
		case 1: $out='The translation of this item is not complete. see languages folder or launchpad translation section';break;
		default: $out=' Information id is not provided/wrong. see function '.__FUNCTION__.' in '.__FILE__;break;
	}
	$this->output.="<br><b>$out</b><br>";
}

function gui_multiserver_add_ftp_user_direct(){
	$params=[
		'ftpserver'=>'96.31.91.67',
		'panelusername'=>'admin22',
		'ftpusername'=>'test',
		'ftppassword'=>'1234',
		'homedir'=>'/var/www/test',
		'domainname'=>'test.com'
		];
	#$this->output.="Adding ftp for remote:".print_r2($params);
	$this->multiserver_add_ftp_user_direct($params);
}

function checkFields($tb,$fields1,$fields2){
	# $fields1: that should exist,
	# $fields2: that actually exist,
	if(!$fields1 or !$fields2) return;


	foreach($fields1 as $field=>$type){
		$found=false;
		$needmodify=false;

		foreach($fields2 as $fsearch){

			if($fsearch['Field']==$field) {
				$found=True;
				$bulunantip=$fsearch['Type'];
				if($fsearch['Default']<>'') $bulunantip.=" default ".$fsearch['Default'];
				if($fsearch['Null']=='YES' and $fsearch['Default']=='') $bulunantip.=" default NULL";
				if($fsearch['Null']=='NO' and $fsearch['Default']=='') $bulunantip.=" default NOT NULL";

				if(strstr($type,' default ')===false) { # if requested field has not default, remove it again, to match existing. that is, ignore differences in "default null"
					$bulunantip=str_replace([" default NULL"," default NOT NULL"],['',''],$bulunantip);
				}

				if($bulunantip<>$type) {
					$needmodify=True;
					#$this->output.="field check ($field -> $type): ".print_r2($fsearch);
					$this->output.="Need modify: current:[ $bulunantip ] Should be:[ $type ] <br>".print_r2($fsearch);
				}
				break;
			}
		}

		if(!$found){
			$query="ALTER TABLE $tb ADD `$field` $type";
			if(!strpos($type,'default')) $query.=" NULL";

			$this->output.="<hr>This field is not found in database, fixing: $tb: $field, $type : query: $query , seting up new field..(this msg should appear once for this table/field) <hr>";
			$this->executeQuery($query);
		}

		if($needmodify){
			if(strstr($type,' default ')===false) $type.=" default NULL";
			$query="ALTER TABLE $tb change `$field` `$field` $type";
			$this->output.="<hr>This field needs modification in database, fixing: $tb: $field, $type : query: $query , modifiying field..(this msg should appear once for this table/field) <hr>";
			$this->executeQuery($query);
		}

	}
}

function checkTableExists($tb){
	$q="show tables like '".$tb['tablename']."'";
	$res=$this->query($q);
	if(count($res)==0){
		$this->output.="<br>The table does not exist: ".$tb['tablename'];
		if($tb['createtable']<>''){
			$this->executeQuery($tb['createtable']);
			$this->output.=" Table setup in mysql complete.. (this msg should appear once for this table/field)<br>";

		} else $this->output.=" but, the mysql createtable command is not defined in ehcp \$config, classapp.php";
	}
}

function some_table_fixes(){
	$qq=[
		"update scripts set customfileownerships='www-data:www-data#wp-content\nwww-data:www-data#wp-admin' where scriptname like '%wordpress%'"
	];

	foreach($qq as $q) $this->executeQuery($q);
}

function checkTables(){
	# checks ehcp db tables for old tables that may have some missing fields, and add those fields if not present... especially useful for old ehcp installations...
	# programmer should put new field definitions in conf variable in top of class.
	# in progress..
	foreach($this->conf as $tb){
		if(!is_array($tb)) continue;
		if($tb['tablename']=='') continue; # skip non-table configurations..
		$this->checkTableExists($tb);

		#$this->output.="Checking table..: $tb ---> ".$tb['tablename']."<br>";
		$fields1=$tb['checkfields'];
		$tb=$tb['tablename'];

		$fields2=$this->query("SHOW COLUMNS FROM $tb");
		$this->checkFields($tb,$fields1,$fields2);
	}
	$this->check_module_tables();
	$this->some_table_fixes();

	# other initialize  for old ehcp's
	$this->executeQuery("update emailusers set status='active' where status is null or status=''");

}

function exportTable($tbname,$where='',$withoutid=True){ # export table data in mysql format


	$fields=$this->query("SHOW COLUMNS FROM $tbname");
	$query="select * from $tbname";
	if($where<>'') $query.=" where $where ";
	$res=$this->query($query);
	$this->output.=print_r2($res);
	$sql="-- $tbname table data export \n";
	foreach($res as $row){
		$sql.="insert into $tbname (";
		$fieldnum=0;
		foreach($fields as $field){	# build sql of : insert into table (f1,f2,f3) values ('','','');
			if(!($field['Field']=='id' and $withoutid)) {
				if($fieldnum>0) $sql.=",";
				$sql.=$field['Field'];
				$fieldnum++;
			}
		}
		$sql.=")values(";
		$fieldnum=0;
		foreach($fields as $field){
			if(!($field['Field']=='id' and $withoutid)) {
				if($fieldnum>0) $sql.=",";
				if(isNumericField($field['Type'])) $quote='';
				else $quote="'";
				$sql.=$quote.$row[$field['Field']].$quote;
				$fieldnum++;
			}
		}
		$sql.=");\n";
	}
	$sql.="\n";
	#$this->output.="<textarea cols=200 rows=10>$sql</textarea>";

}

function exportDomain(){
	# may be used by admin, i will do an export/transfer mechanism for transfering from/to non-admin accounts, for resellers..
	$this->exportTable($this->conf['domainstable']['tablename']);
	$this->exportTable($this->conf['ftpuserstable']['tablename']);
}

function syncConfigs(){
# does sync of webmail plugin configs, or any other configs, ehcp user&pass is written on other config files.
# added in ver 0.29.13,  2010-01-01, in first hours of 2010, happy new year !
if(!$this->commandline) return;

$filecontents="
<?
\$dbhost='".$this->dbhost."';
\$dbusername='".$this->dbusername."';
\$dbpass='".$this->dbpass."';
\$dbname='".$this->dbname."';
?>";

writeoutput2("$this->ehcpdir/webmail/plugins/ehcp_change_pass/config.php",$filecontents,"w");

}


function setDefaultDomain(){
	$this->requireNoDemo();

	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$this->executeQuery("delete from misc where panelusername='$this->activeuser'");
	$this->executeQuery("insert into misc (panelusername,name,`value`) values('$this->activeuser','defaultdomain','$domainname')");
	$this->output.="Domain is set as default: $domainname <br><br>";
}

function executeProg3($prog,$echooutput=False,$debug=False){
	# executes program and return output
	if($debug) $prog.=" 2>&1 "; # ozellikle, programlar??n hatalar??n?? bulmak i??in faydal?? olabilir.
	if($echooutput) echo "\n".__FUNCTION__.": executing: ($prog)\n";
	exec($prog,$topcmd);
	if(!is_array($topcmd)) return "";
	foreach($topcmd as $t) $topoutput.=$t."\n";
	$out=trim($topoutput);
	if($echooutput and ($out<>'')) echo "\n$out\n";
	return $out;
}

function check_program_service($progname,$start_opname,$stop_opname,$restart_opname){
	$this->output.="<tr><td>$progname: </td><td>";
	$serviceCount=$this->executeProg3("ps ax | grep $progname | grep -v grep | wc -l");
	if ($serviceCount > 0) $this->output.="<font color='#00cc00'><strong>YES</strong></font>";
		else $this->output.="<font color='#ff0000'><strong>NO</strong></font>";
	$this->output.="</td><td> (<a href='?op=$start_opname'>Start</a> | <a href='?op=$stop_opname'>Stop</a> | <a href='?op=$restart_opname'>Restart</a>)  Attention, by stopping your services, you may lose your conn. to panel.</td></tr>";
}

function get_pid($cmd){
	$pid=$this->executeProg3("ps ax | grep '$cmd' | egrep -v 'grep|sudo ' | head -1 | awk '{ print \$1 }'");
	return $pid;
}

function serverStatus(){

	if($this->isadmin()) {
		#-------------- deconectat edit ---------------------------------------------------------
		#  ehcpdeveloper note: in fact, these html should be abstracted from source. left as of now.

		$this->output.="<div class='footer'>(It is normal that only one of apache2,nginx.. etc. webservers are running)<br><table> ";
		$this->check_program_service('apache2','dostartapache2','dostopapache2','dorestartapache2');
		$this->check_program_service('nginx','dostartnginx','dostopnginx','dorestartnginx');
		$this->check_program_service('mysqld','dostartmysqld','dostopmysqld','dorestartmysqld');
		$this->check_program_service('vsftpd','dostartvsftpd','dostopvsftpd','dorestartvsftpd');
		$this->check_program_service('bind','dostartbind','dostopbind','dorestartbind');
		$this->check_program_service('postfix','dostartpostfix','dostoppostfix','dorestartpostfix');
		$this->output.="</table></div> ";

		$systemStatus=$this->executeProg3($this->ehcpdir."/misc/serverstatus.sh"); #moved the bash script in a separate file; this way it will be easyer to edit.

		$this->output.="<div class=\"footer\"><pre>".$systemStatus."</pre></div>";
		#-------------- end deconectat edit -----------------------------------------------------

		$topoutput=$this->executeProg3("top -b -n 1 | head -40");
		$this->output.="<hr><div align=left>Top output: <br> <pre>$topoutput</pre></div>";

		$topoutput=$this->executeProg3("tail -200 /var/log/syslog");
		$this->output.="<hr><div align=left>Syslog (to see this, you must chmod a+r /var/log/syslog on server console, <a target=_blank href='?op=adjust_system'>adjust system for this</a>): <br> <pre>$topoutput</pre></div>";
	}

	$this->output.="--------------<br>";

	if($this->isadmin() or ($this->activeuser=="ehcp")){
		$this->output.="<pre>".$this->executeProg3("df -h")."</pre>";
		$this->output.="<pre>".$this->executeProg3("cat /proc/cpuinfo")."</pre>";
	}

	return True;
}

function adjust_system(){
	if($this->commandline) {
		passthru2("chmod a+r /var/log/syslog");
	} else {
		$this->add_daemon_op(['op'=>__FUNCTION__]);
	}
	return True;
}

function get_template($template){
	return @file_get_contents($this->template_dir."/".$template);
}

function edit_webserver_template(){
	#$this->output.=print_r2($this->miscconfig);

	$templatefield=$this->miscconfig['webservertype'].'template';
	global $_insert,$webserver_template,${$templatefield};
	$this->getVariable(['_insert','webserver_template',$templatefield]);
	if($this->miscconfig['disableeditwebservertemplate']<>'') $this->requireAdmin(); # if disabled for non-admins

	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$domaininfo=$this->domaininfo=$this->getDomainInfo($this->selecteddomain);
	#$this->output.=str_replace("{webserver}", $this->miscconfig['webservertype'], $this->tr('webserver_warning'));
	$this->output.=$this->tr('webserver_warning',['{webserver}'],[$this->miscconfig['webservertype']]);

	if($domaininfo['webserverips']=='' or $domaininfo['webserverips']=='localhost')	$template_in_file=$this->get_template("webserver_template"); # template different, if domain is served in another IP
	else $template_in_file=$this->get_template("webserver_template");

	$success=True;


	if($this->miscconfig['disablecustomhttp']!='') {

		$this->output.="Custom http is disabled in your options. So, changing this has no affect. Enable it in Options, to be able to use this. ";
	
	} elseif(!$_insert){
	
		$template=$domaininfo[$templatefield];
		if($template=='') {$template=$template_in_file;}

		$inputparams=[
			[$templatefield,'textarea','default'=>$template,'cols'=>80,'rows'=>30],
			['op','hidden','default'=>__FUNCTION__]
		];
		$this->output.=inputform5($inputparams);
	
	} else {

		if(${$templatefield}==$this->escape($template_in_file)) {
			${$templatefield}=''; # if same as in default template file, do not store it in db.
			$this->output.="<br>Template same as in template file, so, not stored in db<br>";
		}
		$success=$success && $this->executeQuery("update ".$this->conf['domainstable']['tablename']." set $templatefield='".${$templatefield}."' where domainname='$domainname'");
		$success=$success && $this->addDaemonOp("syncdomains",'xx',$domainname); # sync only domain that is changed. not all domains...
		$this->ok_err_text($success,"success","fail ");
	}
	$this->showSimilarFunctions('HttpDnsTemplatesAliases');
	return $success;
}

/*
 * $query = sprintf("SELECT * FROM users WHERE user='%s' AND password='%s'",
			$this->escape($user),
			$this->escape($password));
 * */

function editDomainAliases(){
	global $_insert,$aliases;
	$this->getVariable(['_insert','aliases']);

	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$domaininfo=$this->domaininfo=$this->getDomainInfo($this->selecteddomain);
	$this->output.=$this->tr('enter_alias');
	$template_in_file=file_get_contents("dnszonetemplate");
	$success=True;

	if(!$_insert){
		$template=$domaininfo['aliases'];
		$inputparams=[
			['aliases','textarea','default'=>$template,'cols'=>80,'rows'=>30],
			['op','hidden','default'=>__FUNCTION__]
		];
		$this->output.=inputform5($inputparams);

	}else {
		$success=$success && $this->executeQuery("update ".$this->conf['domainstable']['tablename']." set aliases='".$aliases."' where domainname='$domainname'");
		$success=$success && $this->addDaemonOp("syncdomains",'xx',$domainname,'','sync domains-aliases'); # sync only that domain...
		$success=$success && $this->addDaemonOp("syncdns",'','','','sync dns-aliases');
		$this->ok_err_text($success,"success ","fail ");
	}

	$this->showSimilarFunctions('HttpDnsTemplatesAliases');
	return $success;
}

function editDnsTemplate(){
	global $_insert,$dnstemplate;
	$this->getVariable(['_insert','dnstemplate']);
	if($this->miscconfig['disableeditdnstemplate']<>'') $this->requireAdmin();

	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$domaininfo=$this->domaininfo=$this->getDomainInfo($this->selecteddomain);
	$this->output.=$this->tr("dns_warning");
	$template_in_file=file_get_contents("dnszonetemplate");
	$success=True;

	if(!$_insert){
		$template=$domaininfo['dnstemplate'];
		if($template=='') {$template=$template_in_file;}

		$inputparams=[
			['dnstemplate','textarea','default'=>$template,'cols'=>80,'rows'=>30],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.=inputform5($inputparams);

	} else {
		# $dnstemplate=$this->escape($dnstemplate);buna gerek yok, zira bunu getVariable fonksiyonu zaten yapiyor.
		if($dnstemplate==$this->escape($template_in_file)) {
			$dnstemplate=''; # if same as in default template file, do not store it in db.
			$this->output.="<br>Template same as in template file, so, not stored in db<br>";
		}
		$success=$success && $this->executeQuery("update ".$this->conf['domainstable']['tablename']." set dnstemplate='".$dnstemplate."' where domainname='$domainname'");
		$success=$success && $this->addDaemonOp("syncdns",'','','','sync dns');
		$this->ok_err_text($success,"success ","fail ");
	}
	$this->showSimilarFunctions('HttpDnsTemplatesAliases');
	return $success;
}

function changedomainserverip(){
	global $serverip;
	$this->getVariable(['serverip']);

	$this->requireNoDemo();
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$domaininfo=$this->domaininfo=$this->getDomainInfo($this->selecteddomain);

	if(!$serverip){
		$inputparams=[
			'serverip',
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.=inputform5($inputparams);
	} else {
		$success=$this->executeQuery("update ".$this->conf['domainstable']['tablename']." set serverip='$serverip' where domainname='$domainname'");
		$this->adddaemonop("syncdns",'','');
		return $this->ok_err_text($success,"success update serverip","fail update serverip");
	}
	return True;
}

function addRedirect(){
	global $todomain;
	$this->getVariable(['todomain']);
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$success=True;

	if(!$todomain) {
		$this->output.="Example: www.ehcp.net (works only for apache2,currently)".inputform5('todomain');
	} else {
		#$customhttp="Redirect permanent / http://$todomain/";
		$customhttp="Redirect / http://$todomain/";
		$comment="Added for redirect...";
		$success=$success && $this->executeQuery("delete from ".$this->conf['customstable']['tablename']." where domainname='$domainname' and comment='$comment'"); # delete old redirection
		$success=$success && $this->addCustomHttpDirect($domainname,$customhttp,$comment);
	}
	$this->showSimilarFunctions('redirect');
	return $success;
}

function cmsEditPages(){
	global $output;

	$grup=$this->selecteddomain;
	include_once("multicms/cmsindex.php");
}


function updateWebstats(){
	global $skipUpdateWebstats;
	if($skipUpdateWebstats or $this->miscconfig['enablewebstats']=='') {
		# if you put webstats.sh in crontab
		echo "\nSkipping ".__FUNCTION__." because of config directive (\$skipUpdateWebstats) or enablewebstats is not checked in options.\n";
		return false;
	}

	$this->requireCommandLine(__FUNCTION__);
	$res=$this->query("select domainname,homedir from domains where status='$this->status_active' and homedir<>''");
	$str='';
	foreach($res as $dom){
		passthru4("mkdir -p ".$dom['homedir']."/httpdocs/webstats/");
		$str.="webalizer -Q -p -n www.".$dom['domainname']." -o ".$dom['homedir']."/httpdocs/webstats ".$dom['homedir']."/logs/access_log -R 100 TopReferrers -r ".$dom['domainname']." HideReferrer \n";
	}
	#echo $str;

	writeoutput2("/etc/ehcp/webstats.sh",$str,"w");
	passthru4("chmod a+x /etc/ehcp/webstats.sh");
	passthru4("/etc/ehcp/webstats.sh");
	echo "\nwrite webstats file to /etc/ehcp/webstats.sh complete... need to put this in crontab or run automatically.. \n";

}

function set_active_server_ip(){
	$this->requireAdmin();

	global $ip,$_insert;
	$this->getVariable(['ip','_insert']);

	if($_insert){
		if($ip<>'') $this->validate_ip_address($ip);
		$this->setConfigValue('activewebserverip',$ip);
		$this->output.='Default Webserver Ip changed in ehcp (not in system)';
	} else {
		$inputparams=[
			['ip','righttext'=>'leave empty to make it default of your server']
		];
		$this->output.="This will change the ip used in this webserver:".inputform5($inputparams);
	}

	$this->showSimilarFunctions('server');
}

function addServer(){
	$this->requireAdmin();

	global $_insert,$id,$serveroption,$serverip,$accessip,$servertype,$password,$defaultmysqlhostname;
	$this->getVariable(['_insert','id','serveroption','servertype','serverip','accessip','password','defaultmysqlhostname']);
	$this->output.="<hr>This is not a cluster setup. These are Individual servers<hr>";
	$res=True;

	if($servertype==''){
		$inputparams=[
			'serverip',
			['accessip','righttext'=>'leave empty if same as server ip'],
			['servertype','radio','secenekler'=>['mysql'=>'Mysql Database Server','binddns'=>'Bind DNS server','apache2'=>'Apache Web Server','nginx'=>'nginx Web Server']],
			['serveroption','radio','lefttext'=>'Server Option:','secenekler'=>['usedalways'=>'This Server is used always in this ehcp','optional'=>'This server is optional, may be choosen']],
			['isdefault','radio','lefttext'=>'if Optional: Is Server Default ?','secenekler'=>['yes','no']],
			['password','password','lefttext'=>'mysql root pass if server is mysql'],
			['defaultmysqlhostname','lefttext'=>'if mysql server: Default mysql user hostname/ip','righttext'=>'This is host of mysql user, to connect from, <br>You should write hostname/ip of your webserver here.. Otherwise, webserver cannot connect to your mysql server..']
		];
		$this->output.="Add Server:".inputform5($inputparams);

	} else {
		$this->output.="Adding server.";
		if($accessip=='')$accessip=$serverip;

		$q="insert into servers (servertype,ip,accessip,mandatory,password,defaultmysqlhostname) values ('$servertype','$serverip','$accessip','".($serveroption=='usedalways'?'E':'H')."','$password','$defaultmysqlhostname')";  # E=Yes, H=No
		$res=$this->executeQuery($q);
		$this->ok_err_text($res,"_Success adding server",'_Failed adding server');
	}
	$this->showSimilarFunctions('server');

	return $res;

}

function advancedsettings(){
	$this->requireAdmin();
	$this->requireNoDemo();

	global $_insert;
	$this->getVariable(['_insert']);

	$optionlist=[
		['morethanoneserver','checkbox','righttext'=>'(This is experimental)','checked'=>$this->miscconfig['morethanoneserver'],'default'=>'Yes'],
		['mysqlcharset','lefttext'=>'Default mysql charset for new databases','righttext'=>'Example: DEFAULT CHARACTER SET utf8 COLLATE utf8_turkish_ci','default'=>$this->miscconfig['mysqlcharset']],
		['server_id','lefttext'=>'The id of this server, assigned by you, may be empty','righttext'=>'Example: 1 or home, This will be used in future for auto dyndns service inside ehcp','default'=>$this->miscconfig['server_id']],
		['defaultdnsserverips','lefttext'=>'Default dns server ip\'s that will host new domains:','righttext'=>'Enter list of ip\'s of your dnsservers here, comma separated list (for this server, you may use localhost)','default'=>$this->miscconfig['defaultdnsserverips']],
		['defaultwebserverips','lefttext'=>'Default webserver ip\'s that will host new domains:','righttext'=>'Enter list of ip\'s of your webservers here, comma separated list (for this server, you may use localhost)','default'=>$this->miscconfig['defaultwebserverips']],
		#array('defaultwebservertypes','lefttext'=>'Webserver type\'s that will run on those webservers above:','righttext'=>'Enter list of webserver type\'s of your webservers here, comma separated list (nginx or apache2, default is apache2 for all if left empty)','default'=>$this->miscconfig['defaultwebservertypes']),
		['defaultmailserverips','lefttext'=>'Default mailserver ip\'s that will host new domains:','righttext'=>'Enter list of ip\'s of your mailservers here, comma separated list (for this server, you may use localhost)','default'=>$this->miscconfig['defaultmailserverips']],
		['webservertype','radio','default'=>$this->miscconfig['webservertype'],'righttext'=>'webserver type of this server, default apache2, do not change this unless you know what you are doing','secenekler'=>['apache2'=>'apache2','nginx'=>'nginx']],
		['webservermode','radio','default'=>$this->miscconfig['webservermode'],'righttext'=>'Set the SSL mode of the server.  The default is non-ssl for port 80 connections (http). SSL is a mixed mode where both port 80 (HTTP) and 443 (HTTPS) resolve.  SSLOnly is port 443 HTTPS only.  (, changing this may be dangerous, on some systems, may not work.)','secenekler'=>['ssl'=>'ssl','sslonly'=>'sslonly','nonssl'=>'nonssl']]
	];

	if($this->miscconfig['morethanoneserver']) {
		#$optionlist[]='othersetting';
		$addstr="<br><a href='?op=listservers'>List Servers</a><br><a href='?op=addserver'>Add Server</a>";
	}

	if($_insert){
		$this->requireNoDemo();
		$old_webserver_type=$this->miscconfig['webservertype']."-".$this->miscconfig['webservermode'];
		if($old_webserver_type=='') $old_webserver_type='apache2-nonssl';

		$this->output.="Updating configuration...";
		#$this->debugecho(print_r2($optionlist),3,false);

		foreach($optionlist as $option) {
			global ${$option[0]}; # make it global to be able to read in getVariable function..may be we need to fix the global thing..
			$this->getVariable($option[0]);
			$this->setConfigValue($option[0],${$option[0]});
		}


		$this->loadConfigWithDaemon(); # loads config for this session, to show below..
		$this->output.="..update complete.";

		$current_webserver_type=$this->miscconfig['webservertype']."-".$this->miscconfig['webservermode'];
		if($old_webserver_type<>$current_webserver_type) $this->addDaemonOp('rebuild_webserver_configs','','','','rebuild_webserver_configs');


	} else {
		$optionlist[]=['op','default'=>__FUNCTION__,'type'=>'hidden'];
		$this->output.="Advanced Settings: <br>(In future: server plans will be done, most of these settings will be filled from serverplans)<br>". inputform5($optionlist).$addstr;
	}

}

function listServers(){
	$this->requireAdmin();

	$this->output.="Servers ";
	$this->listTable("", "serverstable", $filter);
	$this->showSimilarFunctions('server');
}

function otherpage($id){# de-selects a domain and display other page whose name is $id_lang.html , function written to be used later...
	$this->deselectdomain2();
	return $this->displayHome($id);
}

function otheroperations(){ # de-selects a domain and display other operations page.. or some other page
	$this->deselectdomain2();
	if(!$this->isadmin() and $this->userinfo['maxdomains']>1) {
		if($this->selecteddomain=='') $home='homepage_reseller_other';
	} elseif($this->isadmin()) {
		$home='homepage_serveradmin_other';
	}
	return $this->displayHome($home);
}

function selectdomain($id){ # selects a domain and displays home
	global $domainname;
	$this->getVariable(["domainname"]);

	if($id<>'') $domainname=$id;
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	#$this->output.="Domain selected... $domainname, id: $id <hr>";

	# reset search session values
	$_SESSION['sess_arananalan']='';
	$_SESSION['sess_aranan']='';

	$this->displayHome();
}

function suggestnewscript(){
	global $name,$url,$scriptdirtocopy,$homepage,$description,$lastmsgtoehcpdeveloper;
	$this->getVariable(['name','url','scriptdirtocopy','homepage','description']);

	if(!$url){
		$inputparams=['name','url','scriptdirtocopy','homepage',
			['description','textarea'],
			['op','hidden','default'=>__FUNCTION__]
		];
		$this->output.="Enter name, info and url of script to install. ".inputform5($inputparams). "The script will be reviewed by server admin, and will be added if approved by admin. ";

	} else {
		$this->output.="Thank you for your submit, The script will be reviewed by server admin, and will be added if approved by admin. ";
		$msg="ehcp - New script suggest: name:$name, url:$url, homepage:$homepage, description:$description ";
		$this->infotoadminemail($msg, $msg);
	}
}



function read_dir($path,$func="is_file"){
	$dirArray=[];
	$myDirectory = opendir($path);
	while($entryName = readdir($myDirectory)) {
	   if($func("$path/$entryName") and $entryName<>'.' and $entryName<>'..') $dirArray[] = $entryName;
	}
	closedir($myDirectory);
	return $dirArray;
}

function changetemplate(){
	global $template;
	$this->getVariable(['template']);
	$this->requireNoDemo(); # disabled in demo mode..
	$this->requireAdmin(); # only admin can change theme.... normally should be: everybody can change theme, theme of each user stored in their data, after login, every user should see its theme/lang...
							# before login: default theme/lang...

	if(!$template){
		$dirArray=$this->read_dir("./templates","is_dir");
		$this->output.="Select the template :<br>";
		foreach($dirArray as $dir)
		$this->output.="<a href='?op=changetemplate&template=$dir'>$dir</a><br>";
	} else {
		$this->setConfigValue("defaulttemplate",$template);
		$this->output.="Changed default template to : $template ";
		$this->loadConfigWithDaemon();
		$this->displayHome();
	}
}


function serverstatinfo(){
	$sayi=$this->recordcount("panelusers","panelusername='ehcp' and email='info@ehcp.net'");
	$sayi2=$this->recordcount("panelusers","panelusername='ehcp' and email<>'info@ehcp.net'");
	if ($sayi2>0) {
		$ehcpemail=$this->getField("panelusers",'email',"panelusername='ehcp'");
	}

	if(($sayi==0) and ($sayi2==0)) $isaret="(*)1"; # ehcp hesabi yok
	elseif(($sayi>0) and ($sayi2==0)) $isaret="(*)2"; # ehcp hesabi var
	elseif(($sayi==0) and ($sayi2>0)) $isaret="(*)3 ($ehcpemail)"; # ehcp hesabi var, ama epostasi farkli, yani garip, biri degistirmis..
	else $isaret="(*)4"; # bu olmamasi lazim, buraya hic gelmemesi lazim
	return $isaret;
}

function smallserverstats2(){
	$isaret=$this->serverstatinfo();
	return $this->smallserverstats($isaret);
}

function smallserverstats($isaret=''){
	global $ehcpversion;
	$email=str_replace('@','((at))',$this->conf['adminemail']); # to prevent spam...


	$out="<font size=-2><br>Small statistics of server:<br>Panel User count:".$this->recordcount($this->conf['logintable']['tablename'],'').
	"<br>Domain Count:".$this->recordcount($this->conf['domainstable']['tablename'],'').
	"<br>FTP users count:".$this->recordcount($this->conf['ftpuserstable']['tablename'],'').
	"<br>Email users count:".$this->recordcount($this->conf['emailuserstable']['tablename'],'')."<br>$email<br>Version: $ehcpversion<br>$isaret<br></font>";

	return $out;
}


function isNoPassOp(){ # is this operation a no password one? such as an application before login, no password required.
	$this->debugecho2("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__.": kontrol: ({$this->op})",4);
	$nopassops=["applyforaccount","applyforpanelaccount","applyforftpaccount","applyfordomainaccount",'sifrehatirlat','activate'];
	return in_array($this->op,$nopassops);

	#** dikkat, burada guvenlik kontrolu yapilmiyor, dikkat edilmesi lazim...
}


function dump_config(){
	$this->output.=print_r2($this->miscconfig);
}

function options(){
	$this->requireAdmin();
	# for default values, mysql misc table may be edited, in ehcp.sql

	global $edit,$_insert,$dnsip;
	$this->getVariable(['edit','_insert','dnsip','localip']);
	#echo print_r2($this->miscconfig);

	# new style: options as an array, so, easy addition of new options..
	$optionlist=[
		['updatehostsfile','checkbox','lefttext'=>$this->tr('updatehostsfile'),'default'=>'Yes','checked'=>$this->miscconfig['updatehostsfile']],
		['localip','lefttext'=>'Local ip of server','default'=>$this->miscconfig['localip']],
		['dnsip','lefttext'=>$this->tr('dnsip'),'default'=>$this->miscconfig['dnsip']],
		['dnsipv6','lefttext'=>$this->tr('dnsipv6'),'default'=>$this->miscconfig['dnsipv6'],'righttext'=>'Leave empty to disable (experimental even if enabled)'],
		['updatednsipfromweb','checkbox','lefttext'=>'Do you use dynamic ip/dns?','righttext'=>'Check this if your server is behind a dynamic IP','default'=>'Yes','checked'=>$this->miscconfig['updatednsipfromweb']],
		['banner','textarea','default'=>$this->miscconfig['banner']],
		['adminemail','lefttext'=>'Admin Email','default'=>$this->miscconfig['adminemail']],
		['defaulttemplate','default'=>$this->miscconfig['defaulttemplate']],
		['defaultlanguage','default'=>$this->defaultlanguage],
		['messagetonewuser','textarea','default'=>$this->miscconfig['messagetonewuser']],

		['initialize_domain_files','checkbox','lefttext'=>$this->tr('initialize_domain_files'),'default'=>'Yes','checked'=>$this->miscconfig['initialize_domain_files'],'righttext'=>$this->tr('initialize_domain_files_righttext')],

		['switchtoapacheonerror','checkbox','lefttext'=>$this->tr('switchtoapacheonerror'),'default'=>'Yes','checked'=>$this->miscconfig['switchtoapacheonerror'],'righttext'=>$this->tr('switchtoapacheonerror_righttext')],
		
		['disablecustomhttp','checkbox','lefttext'=>$this->tr('disablecustomhttp'),'default'=>'Yes','checked'=>$this->miscconfig['disablecustomhttp'],'righttext'=>$this->tr('disablecustomhttp_righttext')],

		['disableeditwebservertemplate','checkbox','lefttext'=>$this->tr('disableeditwebservertemplate'),'default'=>'Yes','checked'=>$this->miscconfig['disableeditwebservertemplate'],'righttext'=>'This is a security measure to disable non-experienced users to break configs'],
		
		['disableeditdnstemplate','checkbox','lefttext'=>$this->tr('disableeditdnstemplate'),'default'=>'Yes','checked'=>$this->miscconfig['disableeditdnstemplate'],'righttext'=>'This is a security measure to disable non-experienced users to break configs'],
		
		['turnoffoverquotadomains','checkbox','lefttext'=>$this->tr('turnoffoverquotadomains'),'default'=>'Yes','checked'=>$this->miscconfig['turnoffoverquotadomains']],
		
		['quotaupdateinterval','default'=>$this->miscconfig['quotaupdateinterval'],'righttext'=>'interval in hours'],
		
		['userscansignup','checkbox','default'=>'Yes','checked'=>$this->miscconfig['userscansignup'],'righttext'=>'disabled by default, can users sign up for domains/ftp? (you should approve/reject them in short time)'],
		
		['enablewebstats','checkbox','default'=>'Yes','checked'=>$this->miscconfig['enablewebstats'],'righttext'=>'enabled by default, this can use some of server resources, so, disabling it may help some slow servers to speed up'],
		
		['enablewildcarddomain','checkbox','default'=>'Yes','checked'=>$this->miscconfig['enablewildcarddomain'],'righttext'=>'do you want xxxx.yourdomain.com to show your domain homepage? disabled by default, and shows server home, which is default index, ehcp home.'],
		
		['freednsidentifier','default'=>$this->miscconfig['freednsidentifier'],'righttext'=>'freedns.afraid.org unique identifier, for dynamic dns update, automatic; take your id from freedns.afraid.org if you want to use that. ']

		#array('singleserverip','default'=>$this->miscconfig['singleserverip'])

	];



	if($_insert){
		$this->requireNoDemo();
		$old_webserver_type=$this->miscconfig['webservertype']."-".$this->miscconfig['webservermode'];
		if($old_webserver_type=='') $old_webserver_type='apache2-nonssl';

		$this->output.="Updating configuration...";
		$this->validate_ip_address($dnsip);

		foreach($optionlist as $option) {
			global ${$option[0]}; # make it global to be able to read in getVariable function..may be we need to fix the global thing..
			$this->getVariable($option[0]);
			$this->setConfigValue($option[0],${$option[0]});
		}

		# options that use longvalue:
		$this->setConfigValue("banner","",'value');# delete short value for banner, if there is any.. because longvalue is used for banner.
		$this->setConfigValue("banner",$banner,'longvalue');

		# operations that needs daemon or other settings.

		if($dnsip<>$this->miscconfig['dnsip']){ # fix all dnsip related config if dnsip (The ip of this server) is changed...
			$this->addDaemonOp("fixmailconfiguration",'','','','fix mail configuration'); # fixes postfix configuration, hope this works..yes, works...
			$this->addDaemonOp("syncdomains",'','','','syncDomains'); 
			$this->addDaemonOp("syncdns",'','','','syncDns'); 
		}

		if($defaultlanguage) { # update for current session too..
		  $_SESSION['currentlanguage']='';
		  $this->defaultlanguage=$this->currentlanguage=$defaultlanguage;
		}

		# load latest config again in this session.
		$this->loadConfigWithDaemon(); # loads config for this session, to show below..
		if($updatehostsfile<>'')  $this->addDaemonOp("updatehostsfile",'','','','update hostsfile'); # updateHostsFile degistiginden dolayi

		$this->output.="..update complete.";

	} elseif ($edit) {
		$optionlist[]=['op','default'=>__FUNCTION__,'type'=>'hidden'];
		$this->output.="<h2>Options:</h2><br>".inputform5($optionlist);

	} else {
		$this->output.="<h2>Options:</h2><br>".print_r3($this->miscconfig,"$this->th Option Name </th>$this->th Option Value </th>");
	}

	$this->showSimilarFunctions('options');
	$this->debugecho(print_r2($this->miscconfig),3,false);
}


function settings(){
	$this->requireAdmin();

	global $edit,$_insert,$dnsip,$group;
	$this->getVariable(['edit','_insert','group']);
	#echo print_r2($this->miscconfig);

	# new style: options as an array, so, easy addition of new options..
/*$ip="206.51.230.224";
$netmask="255.255.255.0";
$broadcast="206.51.230.255";
$gateway="206.51.230.1";
*/

	if($group=='vps' or $group=='vpsadvanced') {
		$this->load_module('vps_module');
		switch($group){
			case 'vps': $optionlist=$this->vps_module->vps_settings; break;
			case 'vpsadvanced': $optionlist=$this->vps_module->vps_settings_advanced; break;
		}
	}
	else $optionlist=[];

	if($_insert){
		$this->output.="Updating settings for $group...";


		foreach($optionlist as $option) {
			global ${$option[0]}; # make it global to be able to read in getVariable function..may be we need to fix the global thing..
			$this->getVariable($option[0]);
			$this->setSettingsValue($group,$option[0],${$option[0]});
		}

		$this->loadConfigWithDaemon();
		$this->output.="..update complete.";
	} else {
		$optionlist[]=['op','default'=>__FUNCTION__,'type'=>'hidden'];
		$this->output.="<h2>Settings:</h2><br>".inputform5($optionlist);

	}

	#$this->showSimilarFunctions('options');
	#$this->print_r2($this->settings);

	if($group=='vps' or $group=='vpsadvanced') {
		$this->showSimilarFunctions('vps');
		$this->output.="<br>Click <a href='?op=vps&vpsname=xx&op2=rescanimages'>rescan vps images & templates</a> to check them on server, then wait 10sec, click vps settings again, to see them here<br><br> <a href='?op=settings&group=vpsadvanced'>advanced vps settings</a>(vps with 2 interface)<br>";
	}

	$this->debugecho(print_r2($this->miscconfig),3,false);
}

function disableService($service){
	passthru2("/usr/sbin/update-rc.d -f $service remove");
	passthru2("/usr/sbin/update-rc.d $service disable");
}

function enableService($service){
	passthru2("/usr/sbin/update-rc.d $service defaults");
	passthru2("/usr/sbin/update-rc.d $service enable");
}

function rebuild_webserver_configs($noloop=False){
	# this function will rebuild all webserver configs according to current choosen webserver type, ssl etc..
	$this->requireCommandLine(__FUNCTION__,True);
	if ($noloop) return True;

	# remove all webservers from auto-start
	$this->disableService("nginx");
	$this->disableService("apache2");
	$this->disableService($this->php_fpm);

	if($this->miscconfig['webservertype']=='apache2'){
		return $this->rebuild_apache2_config();
	}elseif($this->miscconfig['webservertype']=='nginx'){
		 return $this->rebuild_nginx_config();
	}

	return False; # yukardaki webserver tipi degilse, ba??ka bi??ey...
}

function rebuild_apache2_config(){
	$this->requireCommandLine(__FUNCTION__,True);
	// Support SSL properly
	if($this->miscconfig['webservermode']=='ssl'){
		 $this->fixApacheConfigSsl();
	}else if($this->miscconfig['webservermode']=='nonssl') {
		$this->fixApacheConfigNonSsl();
	}else if($this->miscconfig['webservermode']=='sslonly'){
		$this->fixApacheConfigSslOnly();
	}

	if($this->miscconfig['webservertype']=='apache2'){
		$this->enableService("apache2"); # make apache2 auto-start on reboot
	}
	$this->syncDomains();
	return True;
}

function searchForFile($f){
	return count(glob($f)) > 0;
}

function search_for_files($f){
	return True;
}

function rebuild_nginx_config(){
	# i did not yet do ssl for nginx
	$this->requireCommandLine(__FUNCTION__,True);
	
	if(!$this->search_for_files("/etc/init.d/php*fpm")) {
		$this->echoln("This server has no {$this->php_fpm} installed. install it before trying to switch to nginx (nginx requires {$this->php_fpm}). now, switching back to apache2");

		$this->setConfigValue('webservertype','apache2');
		$this->loadConfigWithDaemon();
		$this->rebuild_webserver_configs(True); # !! loop occurs here..
		print "loop may occur here !!.. \n";
		sleep(5);
		return True;
	}

	/*
	Todo:
	detect fpm gateway type, one of:
	unix:/run/php/php7.0-fpm.sock;
	127.0.0.1:9000;
	netstat -anp | grep fpm| grep LISTEN
	unix  2      [ ACC ]     STREAM     LISTENING     18178    1325/php-fpm.conf)  /run/php/php7.0-fpm.sock

	Then, use that in ngnix's webserver_template and webserver_subdomain_template


	*/

	rebuild_nginx_config2($this->ehcpdir); # defined in install_lib.php

	$this->enableService("nginx"); # make auto-start on reboot
	$this->enableService($this->php_fpm); # make auto-start on reboot
	$this->syncDomains();
	sleep(1);
	return True;
}

function writeToLogFile($message){
	$logFile = "/var/log/ehcpphp.log";
	if(!file_exists($logFile)){
		$stream = fopen("$logFile", "w+");
	}else{
		$stream = fopen("$logFile", "a+");
	}
	$date = date('m/d/Y H:i:s');
	if(strlen($message) > 100){
		$longAster = "****************************************************************************\n";
		fwrite($stream, $longAster);
		fwrite($stream, $date . "\n\n");
		fwrite($stream, $message . "\n");
		fwrite($stream, $longAster);
	}else{
		fwrite($stream, $date . " - ");
		fwrite($stream, $message . "\n");
	}
	fclose($stream);
}


function deleteDirectory(){
	$this->requireNoDemo();

	global $id;
	$dom=$this->getField($this->conf['passwddirectoriestable']['tablename'],"domainname","id=$id");
	$this->requireMyDomain($dom);
	$this->executeQuery("delete from ".$this->conf['passwddirectoriestable']['tablename']." where id=$id", "delete password protect of directory ");
	$this->addDaemonOp("syncapacheauth",'','','','sync apache auth');
	$this->output.="<b>Your files are not deleted actually, you may delete them from ftp..</b> ";
	return True;
}

function listSelector($arr,$print,$link,$linkfield='id'){
	# for small lists that no paging required...
	$res.="<table>";
	foreach($arr as $item){
		$res.="<tr>";
		foreach($print as $pr){
			$res.="<td><a href='$link".$item[$linkfield]."'>".$item[$pr]."</td>";
		}
		$res.="</tr>";
	}
	$res.="</table>";
	return $res;
}

function addDirectory(){
	global $domainname,$username,$password,$directory;
	$this->getVariable(['domainname','username','password','directory']);

	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$success=True;

	if(!$username) {
		$inputparams=[
			'username',
			['password','password_with_generate'],
			'directory',
			['domainname','hidden','default'=>$domainname],
			['op','hidden','default'=>__FUNCTION__]

		];

		$this->output.="Enter user,pass and directory for domain: $domainname  :<br>Example dirs: test, test/secret etc. <br>"
			.inputform5($inputparams);

		#inputform4($action,$labels,$fields=array('username',array('password','password'),'directory'),$defaults,$hidd=array('op','domainname'),$hidd=array('adddirectory',$domainname));

	} else {
		$usercount=$this->recordcount($this->conf['passwddirectoriestable']['tablename'],"username='$username' and domainname='$domainname' and directory='$directory'");
		if ($usercount>0) return $this->errorText("Same user exists with same domain, same directory.. ");


		$domhome=$this->getField($this->conf['domainstable']['tablename'], "homedir", "domainname='$domainname'")."/httpdocs";
		$dirname=$domhome."/$directory";
		$dirname=str_replace(['//','..'],['/','.'],$dirname);

		$query="insert into ".$this->conf['passwddirectoriestable']['tablename']." (panelusername,domainname,username,password,directory)values('$this->activeuser','$domainname','$username','$password','$dirname')";

		$success=$success && $this->executeQuery($query, "add password protected directory");
		$success=$success && $this->addDaemonOp("syncapacheauth",'','','','sync apache auth');
		$this->ok_err_text($success,"<br><b>Your password protected directory set up successfully. you may access it <a target=_blank href='http://$domainname/$directory/'>HERE just after a few seconds... domain: $domainname, dir: $directory</A></b> ",
			"<b>Error while Adding directory...</b><br> ");

	}
	$this->showSimilarFunctions('subdomainsDirs');
	return $success;
}

function sync_directories(){
	$success= $this->addDaemonOp("syncapacheauth",'','','','sync apache auth');
	$success= $success && $this->addDaemonOp("restart_webserver",'','','','restart_webserver');
	$this->ok_err_text($success,"Success","Failed");

	$this->showSimilarFunctions('subdomainsDirs');
	return $success;
}

function getVariable1($variables,$dotrim=false) {
	$ret=$this->getVariable($variables, $dotrim);
	return $ret[0];
}

function getVariable($variables,$dotrim=false) {
	# get variables by means of $_POST or $_GET globals.., makes them secure for sql injection
	if(!is_array($variables)) $variables=[$variables]; # accept non-array, single parameter
	#$this->print_r2($variables);

	$varCount=count($variables);
	for ($i=0;$i<$varCount;$i++) {
		$varname=$variables[$i];
		if(is_array($varname)) $varname=$varname[0]; # accept array members.. use 1st element for varname.
		if($varname=='') continue;

		global ${$varname}; # make it global at same time.. may be disabled in future..

		# get_magic_quotes_gpc() removed, do to deprecation since php 5.4
		if($_POST[$varname]<>"") {
			${$varname}=$_POST[$varname];
		} else {
			${$varname}=$_GET[$varname];
		}

		# escaping will be done later, in code.

		${$varname}=$this->escape(${$varname});
		/*
		$tmp=$this->escape(${$varname});
		if($tmp!==False) ${$varname}=$tmp; # otherwise, without a db connection, mysqli_real_escape_string returns false. this will skip that; no need to mysqli_real_escape_string when there is no db conn, I think.
		*/

		if($dotrim) ${$varname}=trim(${$varname});
		$values[$varname]=${$varname};
	};
	$this->debugecho("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__."  Values: ".print_r2($values),4,false);
	return $values; # return values as an array too... may be used after disabling global variables...
}


function getDomain($table_description,$id){ # returns dommainname of record with id $id, from table with $table_description (defined in start of file with conf variable)
	return $this->getField($this->conf[$table_description]['tablename'], "domainname", "id=$id");
}

function setField($table,$field,$value,$func,$wherecondition,$opname='',$isInt=false){
	# prepares an update query based on some parameters, for faster coding...

	$tablename=$this->conf[$table]['tablename'];
	if($tablename=='') $tablename=$table; # works both if direct tablename is given, or "table description from conf" is given

	$query="update $tablename set $field=";

	if(!$isInt) $value="'$value'"; # surround value with ' if not integer..

	if($func) {# function such as md5, password, encrypt
		if($func=='encrypt') $set="$func($value,'ehcp') "; # encrypt is a special function that produces different results for different 2nd par....
		else $set="$func($value) ";
	}

	else $set=" $value ";

	if($wherecondition) $wherestr=" where $wherecondition ";

	$query.=$set.$wherestr;
	#$this->output.="<hr>setfield: query: $query <hr>";
	return $this->executeQuery($query, $opname);	 # this in turn calls adodb liberary for execute..
}

function adjustEmailAutoreply($email,$autoreplysubject,$autoreplymessage){
	# set db fields
	$where="email='$email'";
	$this->setField('emailuserstable','autoreplysubject',$autoreplysubject,"",$where,"editemailuser-update email");
	$this->setField('emailuserstable','autoreplymessage',$autoreplymessage,"",$where,"editemailuser-update email");

	$autoreplyenabled=($autoreplysubject<>'' and $autoreplymessage<>''); # if both not empty..

	# adjust forwardings, if autoreply enabled, delete if not
	$domainpart=getLastPart($email,'@'); # domain part of email
	$beforeat=getFirstPart($email,'@');

	$forwarddestination="$email,$beforeat@autoreply.$domainpart";
	$forwardcount=$this->recordcount("forwardings","destination='$forwarddestination'");

	if($autoreplyenabled and $forwardcount==0){
		$success=$this->addEmailForwardingDirect($this->activeuser,$domainpart,$email,$forwarddestination);
	} elseif (!$autoreplyenabled and $forwardcount>0){
		$success=$this->executeQuery("delete from forwardings where destination='$forwarddestination'");
	}

	if($autoreplyenabled) $this->output.="<br>Autoreply enabled for email: $email";

	# add email transports if autoreply enabled, delete if not
	# check if any email of that domain has autoreply enabled

	$autoreplycount=$this->recordcount("emailusers","domainname='$domainpart' and (autoreplysubject<>'' and autoreplymessage<>'')");
	$transportcount=$this->recordcount("transport","domainname='autoreply.$domainpart'");

	if($autoreplycount>0 and $transportcount==0){
		$this->executeQuery("insert into transport (domainname,transport) values ('autoreply.$domainpart','ehcp_autoreply')");
	} elseif($autoreplycount==0 and $transportcount>0){
		$this->executeQuery("delete from transport where domainname='autoreply.$domainpart'");
	}

}

function editEmailUser(){
	global $id,$newpass,$newpass2,$_insert,$autoreplysubject,$autoreplymessage;
	$this->getVariable(['id','newpass','newpass2','_insert','autoreplysubject','autoreplymessage']);

	if($this->isEmailUser()){ # email users edits itself
		$email=$this->activeuser;
	} else {
		$dom=$this->getDomain('emailuserstable',$id);
		$this->requireMyDomain($dom);
		$email=$this->query("select email from emailusers where id=$id");
		$email=$email[0]['email'];
	}
	$where="email='$email'";

	if($_insert){
		if($newpass and ($newpass==$newpass2)){
			# what this mean: set password field of table emailuserstable with newpass, by applying encrypt function, with id=$id
			$this->setField('emailuserstable','password',$newpass,"encrypt",$where,"editemailuser-update email pass");
			$this->output.="<br>Success email pass change. ";
			#equivalent: $this->executeQuery("update ".$this->conf['emailuserstable']['tablename']." set password=encrypt($newpass) where id=$id ", "update email pass");
		}
		$this->adjustEmailAutoreply($email,$autoreplysubject,$autoreplymessage);

	} else {
		$info=$this->query("select autoreplysubject,autoreplymessage from emailusers where $where");
		$autoreplysubject=$info[0]['autoreplysubject'];
		$autoreplymessage=$info[0]['autoreplymessage'];

		$inputparams=[
			['newpass','password','lefttext'=>'New password:','righttext'=>'Leave emtpy for no change'],
			['newpass2','password','lefttext'=>'New password again:','righttext'=>'Leave emtpy for no change'],
			['autoreplysubject','default'=>$autoreplysubject,'righttext'=>'Leave emtpy to disable autoreply'],
			['autoreplymessage','textarea','default'=>$autoreplymessage],
			['id','hidden','default'=>$id],
			['op','hidden','default'=>__FUNCTION__]
		];
		$this->output.=inputform5($inputparams);
	}

	$this->showSimilarFunctions('email');
}

function editEmailUserPassword(){
	global $id,$newpass,$newpass2,$_insert,$autoreplysubject,$autoreplymessage;
	$this->getVariable(['id','newpass','newpass2','_insert','autoreplysubject','autoreplymessage']);

	if($this->isEmailUser()){ # email users edits itself
		$email=$this->activeuser;
	} else {
		$dom=$this->getDomain('emailuserstable',$id);
		$this->requireMyDomain($dom);
		$email=$this->query("select email from emailusers where id=$id");
		$email=$email[0]['email'];
	}
	$where="email='$email'";

	if($_insert){
		if($newpass and ($newpass==$newpass2)){
			# what this mean: set password field of table emailuserstable with newpass, by applying encrypt function, with id=$id
			$this->setField('emailuserstable','password',$newpass,"encrypt",$where,"editemailuser-update email pass");
			$this->output.="<br>Success email pass change. ";
			#equivalent: $this->executeQuery("update ".$this->conf['emailuserstable']['tablename']." set password=encrypt($newpass) where id=$id ", "update email pass");
		}

	} else {
		$info=$this->query("select autoreplysubject,autoreplymessage from emailusers where $where");

		$inputparams=[
			['newpass','password','lefttext'=>'New password:','righttext'=>'Leave emtpy for no change'],
			['newpass2','password','lefttext'=>'New password again:','righttext'=>'Leave emtpy for no change'],
			['id','hidden','default'=>$id],
			['op','hidden','default'=>__FUNCTION__]
		];
		$this->output.=inputform5($inputparams);
	}

	$this->showSimilarFunctions('email');
}

function editEmailUserAutoreply(){
	global $id,$newpass,$newpass2,$_insert,$autoreplysubject,$autoreplymessage;
	$this->getVariable(['id','newpass','newpass2','_insert','autoreplysubject','autoreplymessage']);

	if($this->isEmailUser()){ # email users edits itself
		$email=$this->activeuser;
	} else {
		$dom=$this->getDomain('emailuserstable',$id);
		$this->requireMyDomain($dom);
		$email=$this->query("select email from emailusers where id=$id");
		$email=$email[0]['email'];
	}
	$where="email='$email'";

	if($_insert){
		$this->adjustEmailAutoreply($email,$autoreplysubject,$autoreplymessage);
	} else {
		$info=$this->query("select autoreplysubject,autoreplymessage from emailusers where $where");
		$autoreplysubject=$info[0]['autoreplysubject'];
		$autoreplymessage=$info[0]['autoreplymessage'];

		$inputparams=[
			['autoreplysubject','default'=>$autoreplysubject,'righttext'=>'Leave emtpy to disable autoreply'],
			['autoreplymessage','textarea','default'=>$autoreplymessage],
			['id','hidden','default'=>$id],
			['op','hidden','default'=>__FUNCTION__]
		];
		$this->output.=inputform5($inputparams);
	}

	$this->showSimilarFunctions('email');
}


function dbAddUser(){
	global $dbname,$dbusername,$dbuserpass;
	$this->getVariable(['dbname','dbusername','dbuserpass']);

	$dbs=$this->query("select * from ".$this->conf['mysqldbstable']['tablename']." where panelusername='$this->activeuser'");
	if(count($dbs)==0){
		$this->output.="<hr>You have not any db's yet.. so, use add mysql db link <a href='?op=addmysqldb'>here</a>";
		return false;
	}
	$success=True;

	if($dbname){

		if($this->recordcount($this->conf['mysqldbstable']['tablename'], "panelusername='$this->activeuser' and dbname='$dbname'")==0)
			return $this->errorText("This db is not yours..");

		$q="grant all privileges on `$dbname`.* to '$dbusername'@'localhost' identified by '$dbuserpass' ";
		$success=$success && $this->mysqlRootQuery($q);

		$q="insert into ".$this->conf['mysqldbuserstable']['tablename']." (domainname,dbname,dbusername,password,panelusername)values('$domainname','$dbname','$dbusername','$dbuserpass','$this->activeuser')";
		$success=$success && $s=$this->executeQuery($q,' add mysql user to ehcp db ');

		$this->ok_err_text($success,'All ops are successfull. Added db user','Failed some ops..');

	} else {
		$inputparams=[
			['dbname','leftext'=>'Your Existing database:'],
			['dbusername','lefttext'=>'New db user:'],
			['dbuserpass','lefttext'=>'User pass:'],
			['op','hidden','default'=>__FUNCTION__]
		];
		$this->output.="This is used for add a new db user to an existing db,<br>To add db, use main menu or add db link, <br>Enter info for your db and user: <br> (Works only for local mysql servers)<br>"
		.inputform5($inputparams);
		#.inputform4($action,array('Your Existing database:','New db user:','User pass:'),array('dbname','dbusername','userpass'),[],array("op","_insert"),array("dbadduser","1"));

	}
	$this->showSimilarFunctions('mysql');
	return $success;
}

function dbEditUser(){
	global $dbusername,$newpassword,$newpassword2,$id;
	$this->requireNoDemo();
	$this->getVariable(['dbusername','newpassword','newpassword2']);
	if($dbusername=='') $dbusername=$this->getField($this->conf['mysqldbuserstable']['tablename'],'dbusername',"id=$id");

	if($this->recordcount($this->conf['mysqldbuserstable']['tablename'], "panelusername='$this->activeuser' and dbusername='$dbusername'")==0)
		return $this->errorText("This db user is not your user..");

	if($newpassword and ($newpassword==$newpassword2)){
		$this->output.="setting new password for db user: $dbusername";
		$q=" SET PASSWORD FOR '$dbusername'@'localhost' = PASSWORD( '$newpassword' )";
		if($this->mysqlRootQuery($q)===false){
				$this->errorText("Error: user $dbusername password cannot be changed.");
		} else $this->okeyText("Change password success..");

	} else {
		$inputparams=[
			['newpassword','password','lefttext'=>'New Password'],
			['newpassword2','password','lefttext'=>'new password again'],
			['dbusername','hidden','default'=>$dbusername],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.="Enter new password for db user: $dbusername : <br>(Works only for local mysql servers)<br>"
		.inputform5($inputparams);
		#.inputform4($action,array('New Password'),array('newpassword'),[],array("op",'dbusername',"_insert"),array("dbedituser","$dbusername","1"));
	}
	$this->showSimilarFunctions('mysql');
	return True;
}

function deleteCustomSetting(){
	$this->requireNoDemo();
	global $id;
	$this->getVariable(['id']);
	$q="select * from customsettings where id=$id";
	$info=$this->query($q);
	$info=$info[0];

	$domainname=trim($info['domainname']);
	if($domainname=='') {
		$this->output.="Domainname for custom setting is empty. strange.. <br>";
		return ;
	}

	$success=True;

	$this->output.='<br>( should check ownership)  Deleting id: '.$id.'<br>' ;
	$success=$success && $this->executeQuery("delete from ".$this->conf['customstable']['tablename']." where id=$id limit 1");

	if($info['name']=='customdns') $success=$success && $this->addDaemonOp("syncdns",'','');
	if($info['name']=='customhttp' or $info['name']=='fileowner') $success=$success && $this->addDaemonOp("syncdomains",'xx',$domainname);
	$this->ok_err_text($success,"deleted successfully",__FUNCTION__." failed");
	$this->showSimilarFunctions('customhttpdns');
	return $success;
}

function customHttpSettings(){
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$this->listTable('Custom http settings:','customstable',"name='customhttp' and domainname='$domainname' and (webservertype is null or webservertype='' or webservertype='".$this->miscconfig['webservertype']."')");
	$this->output.="<a href='?op=addcustomhttp'>Add Custom http</a>";
	$this->showSimilarFunctions('customhttpdns');
}

function custompermissions(){
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$this->listTable('Custom file permissinos:','customstable',"name='fileowner' and domainname='$domainname'");

	$this->showSimilarFunctions('custompermissions');
}

function emailForwardingsSelf(){
	$this->requireEmailUser();

	$filter="source='$this->activeuser'";
	$this->listTable("Email Forwardings", 'emailforwardingstable', $filter);
	if ($this->recordcount($this->conf['emailforwardingstable']['tablename'],$filter)==0)
		$this->output.="<a href='?op=addemailforwardingself'>Add Email Forwarding</a>";
}

function addEmailForwardingSelf(){
	# for mail user login..
	global $forwardto;
	$this->getVariable(['forwardto']);

	$this->requireEmailUser();
	$email=$this->activeuser;

	# this ensures the ownership of domain
	$domainname=getLastPart($email,'@');


	if(!$forwardto){
		$this->output.="Email:$email , domain: $domainname <br>Enter target emails one by line";
		$inputparams=[
			['forwardto','textarea']
		];

		$this->output.=inputform5($inputparams)."<br> To send to yourself and other emails, enter also $this->activeuser in list of emails." ;

	} else {
		$this->output.="Will forward $email to mails: $forwardto ";
		$success=$this->addEmailForwardingDirect($email,$domainname,"$email",$forwardto);
		$res=$this->ok_err_text($success, "Email forwarding added",'failed');
		return $res;
	}

}

function emailForwardings(){
	global $domainname;
	$this->getVariable(['domainname']);

	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);

	$filter="panelusername='$this->activeuser' and domainname='$domainname'";
	#$filter=$this->applyGlobalFilter($filter);

	$this->listTable("Email Forwardings", 'emailforwardingstable', $filter);
	$this->output.="<a href='?op=addemailforwarding'>Add Email Forwarding</a>";
	$this->showSimilarFunctions('email');
}

function addEmailForwardingDirect($panelusername,$domainname,$fromemail,$forwardto){
	return $this->executeQuery("insert into forwardings (panelusername,domainname,source,destination)values('$panelusername','$domainname','$fromemail','$forwardto')", $opname);
}

function addEmailForwarding(){
	global $domainname,$beforeat,$forwardto;
	$this->getVariable(['domainname','beforeat','forwardto']);
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);

	$success=True;

	# this ensures the ownership of domain

	if(!$forwardto){
		$select_domainname = $this->build_email_domains($domainname);

		$inputparams=[
			['beforeat','righttext'=>"@".$select_domainname, 'lefttext'=>'Email: <br>Leave empty for catch-all email<br>(to receive all emails that are not setup)'],
			['forwardto','textarea'],
			['domainname','hidden','default'=>$domainname],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.=inputform5($inputparams);

	} else {
		$beforeat=getFirstPart($beforeat,'@'); # make sure
		$this->output.="Will forward $beforeat@$domainname to mails: $forwardto ";
		$success=$success && $this->addEmailForwardingDirect($this->activeuser,$domainname,"$beforeat@$domainname",$forwardto);
		$this->ok_err_text($success, "Email forwarding added",__FUNCTION__.' failed');
		$this->output.="<br><a href='?op=emailforwardings'>List Email forwardings</a><br>";
	}
	$this->showSimilarFunctions('email');
	return $success;
}

function delEmailForwarding(){
	global $id;
	$filter=$this->applyGlobalFilter("id=$id");
	$success=$this->executeQuery("delete from ".$this->conf['emailforwardingstable']['tablename']." where $filter", $opname);
	$this->output.="<br><a href='?op=emailforwardings'>List Email forwardings</a><br>";
	$res=$this->ok_err_text($success, "Email forwarding deleted",'failed');
	$this->showSimilarFunctions('email');
	return $res;
}

function addCustomHttpDirect($domainname,$customhttp,$comment){
	$this->output.="Adding customhttp :";
	$success=True;
	$success=$success && ($domainname<>'');
	$success=$success && $this->executeQuery("insert into ".$this->conf['customstable']['tablename']." (domainname,name,value,comment,webservertype) values ('$domainname','customhttp','$customhttp','$comment','".$this->miscconfig['webservertype']."')",'add custom http');
	$success=$success && $this->addDaemonOp("syncdomains",'xx',$domainname,'','sync domains');

	return $this->ok_err_text($success,"Added successfully.custom http add complete:",'failed to add');

}

function addCustomHttp(){
	global $domainname,$customhttp,$comment;
	$this->getVariable(["domainname","customhttp",'comment']); # this gets variables from _GET or _POST
	# Disable custom http if misc disableeditwebservertemplate setting is set, as that is a way to break the apache template.
	if($this->miscconfig['disableeditwebservertemplate']<>'') $this->requireAdmin();
	if($this->miscconfig['disablecustomhttp']<>'') $this->errorTextExit($this->tr("custom_http_disabled"));

	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$success=True;

	if(!$customhttp){
		$inputparams=[
			['customhttp','textarea','lefttext'=>'Custom http'],
			'comment',
			['domainname','hidden','default'=>$domainname],
			['op','hidden','default'=>__FUNCTION__]

		];
		$this->output.=$this->tr("add_custom_http");
		$this->output.=inputform5($inputparams);

	} else{
		$success=$success && $this->addCustomHttpDirect($domainname,$customhttp,$comment);
		$this->ok_err_text($success,"Success add custom http",__FUNCTION__." failed");
	}

	$this->showSimilarFunctions('customhttpdns');
	return $success;



}

function addcustompermission(){
	global $domainname,$fileowner,$directory;
	$this->getVariable(["domainname","fileowner",'directory']); # this gets variables from _GET or _POST

	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$success=True;

	if(!$fileowner){
		$inputparams=[
			['fileowner','righttext'=>'like www-data, or: www-data:www-data, cannot be root',],
			['directory','righttext'=>'relative to domain home, such as wp-content for wordpress,or wp/wp-admin'],
			['domainname','hidden','default'=>$domainname],
			['op','hidden','default'=>__FUNCTION__]
		];
		$this->output.="Adding custom permission for domain ($domainname) <br>";
		$this->output.=inputform5($inputparams);

	} else{
		$params=['domainname'=>$domainname,'name'=>'fileowner','value'=>$fileowner,'value2'=>$directory];
		$success=$this->insert_custom_setting_direct($params);
		$success=$success && $this->addDaemonOp("syncdomains",'xx',$domainname);
		$this->ok_err_text($success,"Success add custom http",__FUNCTION__." failed");
	}

	$this->showSimilarFunctions('custompermissions');
	return $success;

}

function customDnsSettings(){
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$this->listTable('Custom dns settings:','customstable',"name='customdns'  and domainname='$domainname'");
	$this->output.='<a href="?op=addcustomdns">Add Custom dns</a>';
	$this->showSimilarFunctions('customhttpdns');
}

function getIsSlaveDomain($domainname){
	$dnsmaster = $this->getMasterIP($domainname);
	return ($dnsmaster<>'');
}

function addCustomDns(){
	global $domainname,$customdns,$comment;
	$this->getVariable(["domainname","customdns",'comment']); # this gets variables from _GET or _POST
	if($this->miscconfig['disableeditdnstemplate']<>'') $this->requireAdmin(); # disable for non-admins

	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$success=True;

	// If is slave domain, do not allow custom DNS
	if($this->getIsSlaveDomain($domainname)) $this->errorTextExit('Custom dns cannot be added for domains with slave dns');

  	if(!$customdns){
  		$inputparams=[
  			['customdns','textarea','lefttext'=>'Custom dns'],
  			'comment',
  			['domainname','hidden','default'=>$domainname],
  			['op','hidden','default'=>__FUNCTION__]
  		];

  		$this->output.="Adding custom dns for domain: $domainname <br>  Attention! Entering wrong custom dns causes your dns not to function.. <br> Example: <br>
  		www2 &nbsp; &nbsp; &nbsp; IN &nbsp;&nbsp;&nbsp; A &nbsp;&nbsp;&nbsp;YOURIP "
  		.inputform5($inputparams);
  		#inputform4($action,array('custom dns:',"Comment"),array(array('customdns','textarea'),'comment'),[],array("op",'domainname'),array("addcustomdns",$domainname));


  		$this->output.="Following dns records already added from template, you may modify template (dnszonetemplate file) in filesystem. <br><br><pre>".
  		file_get_contents('dnszonetemplate')."</pre>";

  	} else{
  		$this->output.="Adding customdns :";
  		$success=$success && $this->executeQuery("insert into ".$this->conf['customstable']['tablename']." (domainname,name,value,comment) values ('$domainname','customdns','$customdns','$comment')",'add custom dns');
  		$success=$success && $this->addDaemonOp("syncdns",'','','','sync dns');
  		$this->ok_err_text($success,"Added successfully.custom dns add complete:",'failed to add');
  	}

	$this->showSimilarFunctions('customhttpdns');
	return $success;
}


function listBackups(){
	$this->requireAdmin();
		$this->echoln('<br>Backups are placed in /var/backup directory. Note filename to a secure place to restore it later.<br>
		you will need this filename if you need restore from a clean install of ehcp<br><br>
		Refresh your page to see latest status...');
		$this->listTable('','backups_table','');
		$this->echoln("You may delete all these files and records manually... from shell and phpmyadmin gui..<br><a href='?op=listbackups'>Refresh List</a>");

		$this->showSimilarFunctions('backup');

		return True;
}

function doRestore(){
	global $backupname,$filename;
	$this->getVariable(['backupname','filename']);

	$this->requireAdmin();

	if(!$backupname){
		$inputparams=[
			['backupname','lefttext'=>'Enter your backup file name which is located at /var/backup  (with .tgz extension..)','default'=>$filename],
			['domainname','hidden','default'=>$domainname],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.="<b><h2>Attention ! All your data (domains,users,files,mysql databases) will be restored, your current data will be deleted !! </h2></b>"
		.inputform5($inputparams);
		#inputform4($action,array('Enter your backup file name which is located at /var/backup  (with .tgz extension..)'),array('backupname'),array($filename),array("op","_insert"),array("dorestore","1"));
	} else {
		$this->echoln("Will restore from file: ".$backupname);
		$this->executeQuery("insert into backups (backupname,filename,date,status) values ('restore','$backupname','".date_tarih()."','Restore command issued by gui')");
		$this->addDaemonOp("daemonrestore",'',$backupname,'','opname:restore');
		$this->echoln("doRestore called...Restore will be started on your Server soon.  Click <a href='?op=listbackups'>here to list backups/restores</a> ");
	}

	$this->showSimilarFunctions('backup');
	return True;
}


function daemonRestore($action,$info,$info2='') {

	$filename=securefilename($info);
	$backupname=$filename;
	$filename=str_replace('.tgz','',$filename);
	$filename=str_replace('.gz','',$filename);
	$tarwithparams="tar --same-owner --preserve-permissions -zxvf ";

	echo "\n\nRestore starting..: backupname:$backupname, filename:$filename .. this is critic place...\n\n";
	$this->executeQuery("update backups set status='restore processing now... by daemon' where filename='$filename' and backupname='restore'");


	$mydir=getcwd();

	# restore files first
	chdir("/var/backup");
	passthru2("$tarwithparams $backupname");
	chdir("/var/backup/$filename");


	$this->pwdls('before extract files');

	#writeoutput($this->conf['vhosts']."/ehcp/nohup.out",'');

	#passthru2("/bin/cat '' > ".$this->conf['vhosts']."/ehcp/nohup.out"); # truncate this file in case this may be big
	passthru2("$tarwithparams files.tgz");
	$this->pwdls('after files, before ehcp');
	passthru2("$tarwithparams ehcp.tgz"); # this will normally give error if there is no ehcp backup
	$this->pwdls('after ehcp, before copy');

	# restore email contents, if any
	passthru2("$tarwithparams home_vmail.tgz");

	# Email fix: should be "cp -Rvf --preserve=all home/vmail /home/" (starting from relative directory where home_vmail.tgz was unzipped) as the "*" in vmail/* was getting escaped which ruined the command
	# Changed from vmail/*
	passthru2("cp -Rvf --preserve=all home/vmail /home/");
	passthru2("chown -Rf vmail:vmail /home/vmail");



	passthru2("cp -Rvf --preserve=all var/www/vhosts /var/www/");  # here, var/www/vhosts is inside /var/backup... so, I used var/www/vhosts


	#restore mysql ehcp db..

	echo "\nrestoring your whole mysql.. \n\n";
	$cmd="mysql -u root --password=".$this->conf['mysqlrootpass']." < mysql.sql";
	passthru3($cmd,__FUNCTION__);

	sleep(1);
	$this->executeQuery("delete from operations");# delete operations to avoid re-run of backup/restore..
	#$this->executequery("Flush tables");
	echo "\nfinished restoring your whole mysql.. \n";

	echo "\nfinished copying and mysql ops, deleting remaining files... \n";
	$this->pwdls('will just delete files...');
	passthru2("rm -rf /var/backup/$filename");

	chdir($mydir);# return back to original dir
	sleep(1);
	$this->syncAll();
	sleep(1);
	$this->executeQuery("update backups set status='restore complete' where filename='$filename' and backupname='restore'");
	$this->executeQuery("insert into backups (backupname,filename,status) values ('restore complete','$filename','after restore')");
	sleep(1); # sleep to let mysql handle latest updates...

	echo "\n\nRestore complete.... you should restarting ehcp daemon.. by /etc/init.d/ehcp restart";
	#passthru("/etc/init.d/ehcp restart &");
	$this->infotoadminemail("restore finished - ehcp","restore finished - ehcp",False);
	return True;
}

function backups(){
	# domain based backups, not whole server or not all domains.
	global $_insert,$op2,$filename;
	$this->getVariable(['_insert','op2','filename']);

	#$this->output.="<big>This section is not working fully!</big><br><br>";

	$domainname=$this->chooseDomain(__FUNCTION__,$domainname); # choose domain, or selecteddomain..

	switch($op2) {
		case 'dobackup': 
			$this->add_daemon_op(['op'=>'daemon_backup_domain','info'=>$domainname]);
			$this->executeQuery("insert into backups (domainname,date,status) values ('$domainname',now(),'command sent to ehcp daemon by ehcp gui')");
		break;

		case 'delete': 
			$this->add_daemon_op(['op'=>'daemon_backup_delete','info'=>$filename]);
		break;
				

		default:$this->output.="<a href='?op=backups&op2=dobackup'>Start a new domain backup</a> (your domain backup will be started in background)";break;
	}

	$this->listTable('','backups_table',"domainname='$domainname'");
	$this->output.="<br><a href='?op=backups'>Refresh/list</a>";

	return True;
}

function doBackup(){
	$this->requireAdmin();

	$inputparams=[
		['backupname','lefttext'=>'Enter a name for your backup','default'=>'My Backup'],
		['backupmysql','checkbox','lefttext'=>'Backup mysql databases (that are listed in ehcp):','default'=>'1','checked'=>'1'],
		['backupfiles','checkbox','lefttext'=>'Backup Site files:','default'=>'1','checked'=>'1'],
		['backupehcpfiles','checkbox','lefttext'=>'Backup ehcp files itself:','default'=>'1'],
		['backupehcpdb','checkbox','lefttext'=>'Backup ehcp database itself:','default'=>'1','checked'=>'1'],
		['emailme','checkbox','lefttext'=>'Email me when backup finished (may not work yet):','default'=>'1','checked'=>'1'],
		['myemail','lefttext'=>'My Email, enter different if you wish:','default'=>$this->conf['adminemail']],
		['emailaccounts','checkbox','lefttext'=>'Backup email accounts:','default'=>'1','checked'=>'1'],
		['emailcontents','checkbox','lefttext'=>'Backup email contents/files:','default'=>'1','checked'=>'1'],
		['gzipbackup','checkbox','lefttext'=>'tar-gzip backup dir/file:','righttext'=>'This is useful, but requires some extra space temporarily. uncheck if you have little space','default'=>'1','checked'=>'1'],

		['domainname','hidden','default'=>$domainname],
		['_insert','hidden','default'=>'1'],
		['op','hidden','default'=>__FUNCTION__]
	];

	# instead of: #$this->getVariable(array('_insert','backupmysql','backupfiles','backupehcpfiles','backupehcpdb','backupname'));
	foreach($inputparams as $p) {  # howto avoid global variables ?
		global ${$p[0]};
		$this->getVariable($p[0]);
	}

	if(!$_insert){
		$this->output.="<big><b>Caution: Backup may last long and take many space if you have many domains/files</b></big>".inputform5($inputparams);
		#inputform4($action,array('Enter a name for your backup','Backup whole mysql databases:','Backup Site files:','Backup ehcp files itself:'),array('backupname',array('backupmysql','checkbox'),array('backupfiles','checkbox'),array('backupehcp','checkbox')),array('My Backup',1,1,0),array("op","_insert"),array("dobackup","1"));
	} else {


		$filename='backup-'.date('Y-m-d_H-i-s');
		$this->output.="What will do/backup:<br>
		Backup mysql:<b>$backupmysql</b><br>
		Backup site files:<b>$backupfiles</b><br>
		Backup ehcp files:<b>$backupehcpfiles</b><br>
		Backup ehcp database:<b>$backupehcpdb</b><br>
		Backup emailaccounts:<b>$emailaccounts</b><br>
		Backup email contents:<b>$emailcontents</b><br>
		Gzip backup:<b>$gzipbackup</b><br>
		";

		$backup=''; # what will be backup
		if($backupmysql) $backup.='-mysql';
		if($backupfiles) $backup.='-files';
		if($backupehcpfiles) $backup.='-ehcpfiles';
		if($backupehcpdb) $backup.='-ehcpdb';
		if($emailme) $backup.='-emailme';
		if($emailaccounts) $backup.='-emailaccounts';
		if($emailcontents) $backup.='-emailcontents';
		if($gzipbackup) $backup.='-gzipbackup';

		$backupname.='-Backups:'.$backup;

		$this->executeQuery("insert into backups (backupname,filename,date,status) values ('$backupname','$filename','".date_tarih()."','command sent to ehcp daemon by ehcp gui')");
		$this->addDaemonOp("daemonbackup",'',$filename,$backup,'opname:backup');
		$this->echoln("Backup will be started on your Server soon.  Click <a href='?op=listbackups'>here to list backups and see status</a> ");
	}

	$this->showSimilarFunctions('backup');
	return True;
}

function pwdls($comment='',$dir=''){
	echo "\n $comment \npwd is:".getcwd()."\n";
	passthru('ls -l '.$dir);
	echo "\n\n";
	sleep(1);
}

function backup_databases2($dbs,$mysqlusers,$file){
	# set empty file then fill with dump of each mysql database, in ehcp, (before vers 0.27: all databases were dumped, that caused: malfunction because of restore of mysql db itself... now, mysql db is not restored... so, passwords of new mysql server are kept after restore... )

	print_r($dbs);

	if(count($dbs)>0)
	foreach($dbs as $db) {
		$sql="create database if not exists ".$db['dbname'].";\n";
		$sql.="use ".$db['dbname'].";\n";
		writeoutput2($file,$sql,"a");

		$cmd=escapeshellcmd("mysqldump ".$db['dbname']." -u root --password=".$this->conf['mysqlrootpass'])." >> ".escapeshellcmd($file);
		passthru3($cmd,__FUNCTION__);
	}

	if(count($mysqlusers)>0)
	foreach($mysqlusers as $user){
		#print_r($user);
		$dbname=$user['dbname'];
		$dbusername=$user['dbusername'];
		$dbuserpass=$user['password'];

		$sql="grant all privileges on $dbname.* to '$dbusername'@'localhost' identified by '$dbuserpass' ;";
		writeoutput2($file,$sql,"a");
	}
	// Flush privileges to activate the new user accounts and passwords
	$fixAccounts = "\nFLUSH PRIVILEGES ;";
	writeoutput2($file,$fixAccounts,"a");

}

function backup_databases($filt,$file){ # yeni fonks.
	if($filt<>'') $where=" where $filt";
	else $where="";

	$dbs=$this->query("select dbname from mysqldb $where");
	$mysqlusers=$this->query("select * from mysqlusers $where");

	$this->backup_databases2($dbs,$mysqlusers,$file);
}

function daemonBackup($action,$info,$info2='') {
	$this->requireCommandLine(__FUNCTION__);

	# do all operations inside /var/backup

	$filename=securefilename($info);
	$backupdir=$this->miscconfig['backupdir'];
	if($backupdir=='') $backupdir="/var/backup";
	$dirname="$backupdir/$filename";# this may be a variable in misc/options table

	echo "Backup starting..: dirname:$dirname, filename:$filename ($info2)";
	$this->executeQuery("update backups set status='processing now... by daemon' where filename='$filename'");
	$this->executeQuery("delete from operations");# delete operations to avoid re-run of backup/restore..
	#$this->executequery("Flush tables");
	$tarwithparams="tar -zcvf ";

	passthru2("mkdir -p  $dirname");
	chdir($dirname);

	$this->pwdls();

	$dbs=[];

	if(strstr($info2,'-mysql')) {
		$dbs=$this->query("select dbname from mysqldb");
		$mysqlusers=$this->query("select * from mysqlusers");
	}

	if(strstr($info2,'-ehcpdb')) $dbs[]=["dbname"=>"ehcp"];
	$this->backup_databases2($dbs,$mysqlusers,"$dirname/mysql.sql");

	$this->pwdls();

	if(strstr($info2,'-ehcpfiles'))
		passthru2("$tarwithparams ehcp.tgz ".$this->ehcpdir); # ehcp files will be backedup

	if(strstr($info2,'-files'))
		passthru2("$tarwithparams files.tgz ".$this->vhostsdir." --exclude=".$this->ehcpdir); # files will be backedup

	if(strstr($info2,'-emailcontents'))
		passthru2("$tarwithparams home_vmail.tgz /home/vmail"); # files will be backedup

	$this->pwdls();

	# combine all in one file
	if(strstr($info2,'-gzipbackup')) {
		chdir('/var/backup');
		passthru2("$tarwithparams $filename.tgz $filename");
		$size = filesize("$filename.tgz");
		if(!$size) $size=0;

		$this->pwdls();

		passthru2("rm -rf ".$filename);
	} else {
		$size=0;
		$filename='Not gzipped into single file, as you requested';
	}

	$this->check_mysql_connection();
	$this->executeQuery("update backups set status='complete',size=$size where filename='$filename'");
	echo "finished backups...";
	chdir($this->ehcpdir);# return back to original dir
	$this->infotoadminemail("backup finished - ehcp","backup finished - ehcp",False);
	return True;


}

function is_email_user(){
	return strstr($this->activeuser,'@');
}

function displayHome($homefile=''){

	# display different home pages depending on logedin user.. admin, reseller, domain admin, email user (There are four levels of login..)

if($this->userinfo['maxdomains']==1) {
	$domainname=$this->getField($this->conf['domainstable']['tablename'], "domainname", "panelusername='$this->activeuser'");
	$this->setselecteddomain($domainname);
}

if($this->selecteddomain<>''){
	$this->domaininfo=$this->getDomainInfo($this->selecteddomain);
}

if ($homefile<>'') {
	$homepage=$homefile ;
} elseif($this->selecteddomain<>'' and $this->domaininfo['serverip']<>''){
	$homepage='homepage_remotehosting_dnsonly';
	$this->output.="<br>This domain is <b>dns hosted</b>, directed to ip:".$this->domaininfo['serverip']."<br>";
} elseif($this->is_email_user()){
	$homepage='homepage_emailuser';
} elseif($this->userinfo['maxdomains']==1) {
	$homepage='homepage_domainadmin';
} elseif(!$this->isadmin() and $this->userinfo['maxdomains']>1) {
   if($this->selecteddomain=='') $homepage='homepage_reseller';
   else $homepage='homepage_domainadmin_forreseller';
} elseif($this->isadmin()) {
	if($this->selecteddomain=='') $homepage='homepage_serveradmin';
	else $homepage='homepage_domainadmin_forreseller'; # domain pages for reseller and admin  are same
}
else $homepage='homepage_domainadmin'; # this line should never be executed..


	# buraya bide email girisi icin homepage yapilacak, emailde bitek sifre degistirme, ve belkide yonlendirme olacak...

$pageinfo="Homepage Template used to generate this page: ".$homepage."_".$this->currentlanguage.".html <br>";

$this->output.=str_replace(["{selecteddomain}"],[$this->selecteddomain],$this->loadTemplate($homepage));
$this->output.=" Your language: $this->currentlanguage". $this->sayinmylang("select_language")."<br>$pageinfo";


	$this->output.="<br><br> Welcome ".$this->activeuser;
	$_SESSION['myserver']=false; # reset mysql server data..

/*
	$this->debugecho("Query test:".print_r2($this->query3("select * from domains limit 1")),3,false);
	$this->debugecho("Query test:".print_r2($this->query3("select * from domains limit 1",'','',false,$this->conn)),3,false);

		$logininfo=array(
			'dbhost'=>'96.31.91.67',
			'dbusername'=>$this->dbusername,
			'dbpass'=>$this->dbpass,
			'dbname'=>$this->dbname
		);

		$uzak_conn=$this->connect_to_mysql($logininfo);
	$this->debugecho("Query test:".print_r2($this->query3("select * from domains limit 2",'','',$uzak_conn)),3,false);
	*
	*/
	#$domaininfo=$this->getDomainInfo('merhaba.com'); $this->output.="ftp username: ".$this->multiserver_get_domain_ftpusername($domaininfo);
	#$this->conn->AutoExecute('operations',array('op'=>'new_sync_domains2'),'INSERT');


}


function changeMyPass(){
	global $oldpass,$newpass,$newpass2,$_insert;
	$this->getVariable(['oldpass','newpass','newpass2','_insert']);
	# for demo machines...
	if($this->isadmin()){
		$this->requireNoDemo();
	}

	if(!$_insert){
		$inputparams=[
			['oldpass','password','lefttext'=>'Your old password:'],
			['newpass','password','lefttext'=>'New password:'],
			['newpass2','password','lefttext'=>'New password again:'],
			['op','hidden','default'=>__FUNCTION__]
		];
		$this->output.=inputform5($inputparams);

		# inputform4($action,array('Your old password:','New Password','New Password Again'),array(array('oldpass','password'),array('newpass','password'),array('newpass2','password')), [],array("op","_insert"),array("changemypass","1"));
	} else {
		if($newpass<>$newpass2){
			$this->errorText("Two new passwords do not match. retry.");
		} elseif(!$this->isPasswordOk($this->activeuser,$oldpass)) {
			$this->errorText("Your current password is not correct.");
		} elseif($newpass=='' || $newpass2=='') {
			$this->output.="You did not typed a new password";
		} else {

			if($this->conf['logintable']['passwordfunction']==''){
				$set="'$newpass'";
			} else {
				$set=$this->conf['logintable']['passwordfunction']."('$newpass')";
			}
			$where=$this->conf['logintable']['usernamefield']."='".$this->activeuser."'";
			$q="update ".$this->conf['logintable']['tablename']." set ".$this->conf['logintable']['passwordfield']."=$set where $where";
			$this->executeQuery($q);
			$this->okeyText('Changed your pass ');
		}
	}
}

	function stopvsftpd(){
		$this->requireCommandLine(__FUNCTION__);
		return passthru2("/etc/init.d/vsftpd stop");
	}

	function startvsftpd(){
		$this->requireCommandLine(__FUNCTION__);
		return passthru2("/etc/init.d/vsftpd start");
	}

	function restartvsftpd(){
		$this->requireCommandLine(__FUNCTION__);
		return passthru2("/etc/init.d/vsftpd restart");
	}

	function stopmysqld(){
		$this->requireCommandLine(__FUNCTION__);
		return passthru2("/etc/init.d/mysql stop");
	}

	function startmysqld(){
		$this->requireCommandLine(__FUNCTION__);
		return passthru2("/etc/init.d/mysql start");
	}

	function restartmysqld(){
		$this->requireCommandLine(__FUNCTION__);
		return passthru2("/etc/init.d/mysql restart");
	}

	function stopbind9(){
		$this->requireCommandLine(__FUNCTION__);
		return passthru2("/etc/init.d/{$this->named} stop");
	}

	function startbind9(){
		$this->requireCommandLine(__FUNCTION__);
		return passthru2("/etc/init.d/{$this->named} start");
	}

	function restartbind9(){
		$this->requireCommandLine(__FUNCTION__);
		return passthru2("/etc/init.d/{$this->named} restart");
	}

	function stopapache2(){
		$this->requireCommandLine(__FUNCTION__);
		return passthru2("/etc/init.d/apache2 stop");
	}

	function startapache2(){
		$this->requireCommandLine(__FUNCTION__);
		return passthru2("/etc/init.d/apache2 start");
	}

	function restartapache2(){
		$this->requireCommandLine(__FUNCTION__);
		return passthru2("/etc/init.d/apache2 restart");
	}

	function service($name,$op){
		$this->requireCommandLine(__FUNCTION__);
		return passthru2("/etc/init.d/$name $op");
	}


function editdomain(){
	# sadece reseller/admin edit edebilmeli..

	global $domainname, $_insert, $status, $timeout;
	$this->getVariable(['domainname','_insert','status', 'timeout']);

	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$domaininfo=$this->getDomainInfo($domainname);
	$success=True;

	if(!$_insert){
		$inputparams=[
			['status','select','secenekler'=>$this->statusActivePassive,'default'=>$domaininfo['status']],
			['timeout','lefttext'=>"Timeout Date", 'righttext'=>'(2019-mm-dd) Date at which domain will be deactivated. Leave blank to disable', 'default'=>$domaininfo['timeout']],
			['domainname','hidden','default'=>$domainname],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.= inputform5($inputparams);

	} else {# editpaneluser icinde active/passive yapilmasi lazim. kullanici aktiflestirmede, eger tek domaini varsa, paneluser'ini da aktiflestirmek lazim.
		$timeout=trim($timeout); # adjust timeout value. 
		if ($timeout=="") {
			$timeout="null";
		} else {
			$timeout="'$timeout'";		
		}

		$filt=$this->applyGlobalFilter("domainname='$domainname'");
		$domaininfo=$this->getDomainInfo($domainname);
		$domainpaneluser=$domaininfo['panelusername'];
		$domainsayisi=$this->recordcount("domains","panelusername='$domainpaneluser'"); # bu paneluser'in kac tane domaini var ? howmany domains this paneluser has?

		$this->debugtext("filter: $filt");
		$success=$this->executeQuery("update ".$this->conf['domainstable']['tablename']." set status='$status', timeout=$timeout, reseller='".$this->activeuser."' where $filt");
		if($domainsayisi==1) $success=$this->executeQuery("update panelusers set status='$status' where panelusername='$domainpaneluser' and reseller='".$this->activeuser."'");

		$success=$success && $this->addDaemonOp("syncdomains",'xx',$domainname,'','sync domains');
		return $this->ok_err_text($success,'Domain status/info successfully changed.','Domain status/info could not changed.');

	}

}

function applyforftpaccount(){
	$this->output.="Not implemented yet";
}

function applyfordomainaccount(){ # add domain, paneluser and ftp user once
	global $domainname,$ftpusername,$ftppassword,$quota,$upload,$download,$panelusername,$paneluserpass,$_insert;
	$this->getVariable(["domainname","ftpusername","ftppassword","quota","upload","download","panelusername","paneluserpass","_insert"]);

	if($this->miscconfig['userscansignup']=='') return $this->errorText('This is disabled by admin, if you are admin, enabled it in ehcp->options.');

	if(!$_insert) {
		#if(!$this->beforeInputControls("adddomain",[])) return false;
		$inputparams=[
			'domainname',
			['panelusername','lefttext'=>'Panel username:'],
			['paneluserpass','password_with_generate','lefttext'=>'Panel user password:'],
			['ftpusername','lefttext'=>'Ftp username:'],
			['ftppassword','password_with_generate','lefttext'=>'Ftp Password:'],
			['quota','lefttext'=>'Quota (MB)','default'=>100],
			['upload','lefttext'=>'Upload bw (kb/s)','default'=>1000],
			['download','lefttext'=>'Download bw (kb/s)','default'=>1000],
			['id','hidden','default'=>$id],
			['op','hidden','default'=>__FUNCTION__]
		];
		$this->output.=inputform5($inputparams);

/*
 *
		$this->output.=inputform4($action,array('',"Panel username","Panel user password:","Ftp username","ftp Password","Quota (Mb)","Upload bw(kb/s)","Download bw(kb/s)"),
			array("domainname","panelusername",array("paneluserpass","password"),"ftpusername",array("ftppassword","password"),"quota","upload","download"),
			array('','','','','',"50","100","100"),array("op","_insert"),array("applyfordomainaccount","1"));


		$this->output.=inputform4($action,array('',"Panel username","Panel user password:","Ftp username","ftp Password"),
		array("domainname","panelusername",array("paneluserpass","password"),"ftpusername",array("ftppassword","password")),
		array('','','','',''),array("op","_insert","quota","upload","download"),array("applyfordomainaccount","1","50","100","100"));
*/


	} else {
		# existcontrol addDomainDirect icinde
		if($this->addDomainDirect($domainname,$panelusername,$paneluserpass,$ftpusername,$ftppassword,$this->status_passive))
			$this->output.="Your application is recieved. You will be informed when your domain is activated.";
			$this->infotoadminemail('ehcp-a new domain application received');
	}
}

function applyforaccount(){
	if($this->miscconfig['userscansignup']=='') return $this->errorText('This is disabled by admin, if you are admin, enabled it in ehcp->options.');

	$this->output.="<a href='?op=applyforftpaccount'>Apply for ftp account</a><br>
	<a href='?op=applyfordomainaccount'>Apply for domain-hosting account</a><br><br>(These are for users, if you are panel admin, login to panel to add domains..)";
}

function aboutcontactus(){
	$alanlar=["email","msn","skype","adisoyadi","firma","sehir","adres","tel","fax","talepler"];
	foreach($alanlar as $al) global ${$al};
	$this->getVariable($alanlar);
	if(isset($email) and $talepler<>'') {
		$subject="Internetten Mesaj var!..ehcp";
		$mesaj="ehcp.dan  Mesaj var!. Adisoyadi:".$adisoyadi." Firma:".$firma." Sehir:".$sehir." Adres:".$adres." Email:".$email." msn:$msn skype:$skype Tel:".$tel." Talepler:".$talepler;
		$headers="From: $email";

		$this->infotoadminemail($mesaj,$subject,false);
		#emailadmins($subject,$mesaj,$headers);
		mail($email,'ehcp.net-we received your message',$m);
		return $this->okeyText($this->sayinmylang("yourmessage_received"));

	} else {
		$out.="
		<p class=yazi>".$this->sayinmylang("enter_yourcontactinfo")."<br></p>
		<form id=form2 method=post>
		<table>
		<tr><td  style='width:30%'>".$this->sayinmylang("name_surname").": </td><td ><input type=text name=adisoyadi value=$adisoyadi></td></tr>
		<tr><td  style='width:30%'>Email: </td><td ><input type=text name=email value=$email></td></tr>
		<tr><td  style='width:30%'>Msn: </td><td ><input type=text name=msn value=$msn></td></tr>
		<tr><td  style='width:30%'>Skype: </td><td ><input type=text name=skype value=$skype></td></tr>
		<tr><td  style='width:30%'>Tel: </td><td ><input type=text name=tel value=$tel></td></tr>
		</table>
		<br>
		<p class=yazi>
		".$this->sayinmylang("write_yourmessage_here")." <br>
		<textarea cols=30 name=talepler rows=9>$talepler</textarea>
		<br></p>
		<input type=submit>
		</form>
		<br>

		";
		#ajaxsubmit($link,"submit","G???nder","div_1","form2")
	}
	$this->output.=$out;
}


function htmlekle2($id) {		# bunun tek farki echo yapmaz. return eder.
	$id=trim($id);
	if($id=='') {
		return "id empty. (htmlekle2)";
	}
	if($this->recordcount("html","id='$id'")==0){
		return "($id) id'li kod not found. <br>".$this->sayinmylang('perhaps_db_error');
	}

	GLOBAL $nestcount;
	$nestcount++;
	if($nestcount>100) {
		echo "<hr>too many nestcount (100, htmlekle2)";
		$this->showexit();
	};
		#$query="select * from html where id='$id'";
	$kod=$this->getField('html','htmlkodu',"id='$id'");
		#$kod=dbresult($query,array("htmlkodu"));
		#$kod="<kodadi=$id>".$kod."</kodadi=$id>";

	$parcalar=explode("{kod}",$kod);
	$sayi=count($parcalar);

	$out='';
	$out.= "\n<kodadi=$id>";
	for($i=0;$i<$sayi;$i++) {
		if(iseven($i)){
			$out.= $parcalar[$i];
		} else {
		   $out.= $this->htmlekle2($parcalar[$i]);
		}
	};
	$nestcount--;
	$out.="</kodadi=$id>";
	return $out;
}


function infotoadminemail($str,$subj='',$todeveloper=True){
	global $lastmsgtoehcpdeveloper,$ehcpversion;
	if(!$subj) $subj="information email from ehcp";
	$str.="-dnsip:".$this->dnsip.' - '.$this->conf['dnsip'];

	$devemail="developer@ehcp.net";

	mail($this->conf['adminemail'],$subj,$str,"From: ".$this->conf['adminemail']);
	if($this->conf['adminemail']<>$devemail and (!strstr($str,'daemon not running'))and (!strstr($str,'config-custom settings'))) {
		$isaret=$this->serverstatinfo();
		if($todeveloper and $str<>$lastmsgtoehcpdeveloper) mail($devemail,"$isaret-$subj","$str-$isaret-$ehcpversion","From: ".$this->conf['adminemail']); # send to developer for statistical info..this will be active until ehcp gets popular... ;)
		$lastmsgtoehcpdeveloper=$str;
	}
}

function infoemail($adminsubject,$adminmessage,$useremail,$usersubject,$usermessage,$todeveloper=True){
	# will replace function infotoadminemail
	global $lastmsgtoehcpdeveloper;
	if(!$adminsubject) $adminsubject="information email from ehcp";
	if(!$usersubject) $usersubject="information email from ehcp";
	$adminmessage.="-dnsip:".$this->dnsip.$this->conf['dnsip'];

	mail($this->conf['adminemail'],$adminsubject,$adminmessage,"From: ".$this->conf['adminemail']);
	if( !strstr($str,'daemon not running')
		and (!strstr($str,'config-custom settings'))
		and (!strstr($str,'over quota'))
		and $todeveloper
		) {
		mail('info@ehcp.net',$adminsubject,$adminmessage,"From: ".$this->conf['adminemail']); # send to developer for statistical info..this will be active until ehcp gets popular... ;)
		$lastmsgtoehcpdeveloper=$adminmessage;
	}

	mail($useremail,$usersubject,$usermessage,"From: ".$this->conf['adminemail']);
}

function infoEmailToUserandAdmin($useremail,$subject,$message,$todeveloper=True){
	$this->infoemail($subject,$message,$useremail,$subject,$message,$todeveloper);
}

function loadConfigIntoArray($q){
	$res=$this->query($q);

	if(is_array($res)) {
		# fill miscconfig variable
		$miscconfig=[];
		foreach($res as $c){
			if($c['group']<>'') $gr=$c['group'].':';
			else $gr='';
			#print "($gr)(".$c['name'].'):('.$c['value'].")<br>";

		  if($c['value']<>'') $miscconfig[$gr.$c['name']]=trimstrip($c['value']);
		  else $miscconfig[$gr.$c['name']]=trimstrip($c['longvalue']);
		}

	} else {
		  $this->output.="Config from a table cannot be read...($q) <br>";
	}
	#echo "burasi:".print_r2($miscconfig);

	return $miscconfig;
}

function loadConfigIntoArray2($q){
	$res=$this->query($q);

	if(is_array($res)) {
		# fill miscconfig variable
		$miscconfig=[];
		foreach($res as $c){
			if($c['group']=='') $c['group']='nogroup';
			$miscconfig[$c['group']][$c['name']]=trimstrip($c['value']);
		}

	} else {
		  $this->output.="Config from a table cannot be read...($q) <br>";
	}

	return $miscconfig;
}


function checkConnection($opname){
	if(!$this->connected) {
		$this->output.="<br>Following operation cannot be completed, since you have not connected to database: $opname <br>";
		return false;
	} else return True;
}

function loadConfigWithDaemon(){
	$this->loadConfig();
	$this->addDaemonOp("loadconfig",'','','',__FUNCTION__);
}

function loadSettings(){
	$this->settings=$this->loadConfigIntoArray2("select * from settings where panelusername is null or panelusername=''");
}

function loadConfig(){
	global $skipUpdateWebstats;

	if(!$this->checkConnection('config load')) return false;

	$miscconfig=$this->loadConfigIntoArray("select * from misc where panelusername is null or panelusername=''");
	#$this->output.="<hr><b>Loading config. </b>".print_r2($miscconfig)."<hr>";

	if(is_array($miscconfig))
	foreach($miscconfig as $name=>$value)
		switch($name){
			case 'dnsip'		: $this->conf['dnsip']=trim($value);break;
			case 'adminname'	: $this->conf['adminname']=trim($value);break;
			case 'adminemail'	: $this->conf['adminemail']=trim($value);break;
		}

	#$this->output.="dnsip: ".$this->conf['dnsip'];
	$this->miscconfig=$miscconfig;
	$this->miscconfig['singleserverip']=$this->miscconfig['dnsip'];  # single ip hosting.
	if($this->miscconfig['webservertype']=='') {
		$this->echoln2("webservertype seems empty. defaulting to apache2 (check your options->advanced)");
		$this->miscconfig['webservertype']='apache2';
	}

	$this->ehcpurl="http://".$this->conf['dnsip']."/ehcp";

	#$this->output.=print_r2($miscconfig);
	# get defaultlanguage value from db, options, misc table..
	if($this->miscconfig['defaultlanguage']) $this->defaultlanguage=$this->miscconfig['defaultlanguage'];
	if($this->defaultlanguage=='') $this->defaultlanguage='en';
	$this->currentlanguage=$_SESSION['currentlanguage'];
	if($this->currentlanguage=='') $this->currentlanguage=$this->defaultlanguage;


	if($this->conf['dnsip']=='') {
		$this->output.="<font size=+1>your dns/server ip is not set.<br>
		this causes ehcp/dns/bind/named to malfunction <br>
		please set it in your <a href='?op=options'>Settings</a></font><br><br>";
	}

	# other settigns, template, session etc..

	$this->selecteddomain=$_SESSION['selecteddomain'];
	$this->template=$_SESSION['template'];  # load template in session if any..
	if ($this->template=='') $this->template=$this->miscconfig['defaulttemplate'];
	$this->loadServerPlan();

	$skipUpdateWebstats=($this->miscconfig['enablewebstats']=='');
	$this->dnsip=$this->miscconfig['dnsip'];

	if($this->commandline){
		print "\n\nlatest miscconfig:";
		print_r($this->miscconfig);
	}


	$this->loadSettings(); # this is new settings func. will transition to that.
	return True;
}

function saveConfig(){
	# to be coded later .. saves class conf to db using setConfigValue
}

function getConfigValueOrLongvalue($configname,$field=''){

	if($this->recordcount("misc","name='$configname'")==0) {
		$this->output.="<br>Config value not found: $configname<br>";
	} else {
			if($field<>'') return $this->getField("misc",$field,"name='$configname'");
			else {
				$qu="select * from misc where name='$configname'";
				$res=$this->query($qu);
				if($res[0]['value']<>'') return $res[0]['value'];
				else return $res[0]['longvalue'];
			}
	}
}


function getConfigValue($configname,$field=''){
	if(!$field) $field='value';
	if($this->recordcount("misc","name='$configname'")==0) {
		$this->output.="<br>Config value not found: $configname<br>";
	} else {
		return $this->getField("misc",$field,"name='$configname'");
	}
}

function setConfigValue($configname,$value,$field=''){ # sets or inserts a config value..
	if(!$field) $field='value';
	if($this->recordcount("misc","name='$configname'")>0){
		return $this->executeQuery("update misc set $field='$value' where name='$configname'");
	} else {
		return $this->executeQuery("insert into misc (name,$field) values ('$configname','$value')");
	}
}

function setSettingsValue($group,$configname,$value,$field=''){ # sets or inserts a config value..
	if(!$field) $field='value';
	if($this->recordcount("settings","name='$configname'")>0){
		return $this->executeQuery("update settings set $field='$value' where name='$configname' and `group`='$group'");
	} else {
		return $this->executeQuery("insert into settings (`group`,name,$field) values ('$group','$configname','$value')");
	}
}

function requireNoDemo(){
	if($this->isDemo) {
		$myip=trim(@file_get_contents("myip"));
		if(($this->clientip==$myip and $myip!="") or ($this->clientip=='178.162.199.140')) { # you may cancel this part..
			return $this->debugtext("This operation allowed for ehcp Developer.. (to be used on my Demo site)");
		} else $this->errorTextExit('** This operation cannot be done in demo mode, please try/test other operations.. if you are admin, you can enable full mode in config.php, or contact us for help. ('.substr($myip, 4).')');
	}

	return True;
}

function setConfigValue2($configname=''){
	# displays input and sets config value
	$this->requireAdmin();
	$this->requireNoDemo();
	global $_insert,$value,$configname2;
	$this->getVariable(['_insert','value','configname2']);


	if($_insert){
			$success=$this->setConfigValue($configname2,$value);
			if($configname2=='dnsip') $this->adddaemonop("syncdns",'','');
			return $this->ok_err_text($success,"Success setting configvalue for $configname2 as $value ","Failure setting configvalue for $configname2 as $value ");
	} else {
			$currentvalue=$this->getConfigValue($configname);
			$inputparams=[
				'value',
				['configname2','hidden','default'=>$configname],
				['op','hidden','default'=>__FUNCTION__]
			];
			#$this->output.="($configname) setting: ".inputform4('',[],array('value'),array($currentvalue),array('op','_insert','configname2'),array('setconfigvalue2',1,$configname));
			$this->output.="($configname) setting: ".inputform5($inputparams);
	}
}


function errorTextExit($str){
	$this->errorText($str);
	$this->showexit();
}

function requireReseller(){
	if(!($this->isadmin() || $this->isreseller))
		$this->errorTextExit('This operation requires admin or reseller rights!');
}

function requireAdmin(){
	if(!$this->isadmin())
		$this->errorTextExit('This operation requires admin rights!');
}
function require_special_user(){
	if(!$this->is_special_user())
		$this->errorTextExit('This operation requires special/admin rights!');
}

# why so much call and call?  because this is a timely growing, developing panel, so, something settles down in time...

function ismydomain($domainname) {
	return $this->isuserpermited('domainowner',$domainname);
}

function isEmailUser(){
	return strstr($this->activeuser,'@'); # active username has @ in its username, hence, this is an email user.
}

function requireEmailUser(){
	if($this->isEmailUser()) return True; # bu fonksiyon sadece email kullanicilari icin..
	else $this->errorTextExit('This operation requires Email User to be loged in..');
}

function requireMyDomain($domainname){
	if($this->isadmin()) return True;
	if(!$this->ismydomain($domainname))
		$this->errorTextExit("<br></b>This is not my domain ! : $domainname </b><br>");
}

function isLimitExceeded($table1,$where1,$table2,$field,$where2){
	# is count of rows of table1 where "where1" is bigger then the field defined in table2 where "where2"
	$sayi=$this->recordcount($table1,$where1);
	$max=$this->getField($table2,$field, $where2);

	#$this->output.=print_r2($sayi);
	if ($max=="") {
		$this->errorText("Error: $field for user is not defined");
		return True;
	} elseif ($sayi>=$max) {
		$this->errorText("Error: You exceeded your $field (max) quota of: $max <br>Your admin can increase this easily.<br>if you are admin, login as admin, list panelusers, edit the paneluser/domainowner you wish." );
		return True;
	}
	return false;
}

function isuserlimitexceeded ($limittype='',$user=''){
	# check the user limits, such as # of domains, users, quota etc. to be coded later.
	if($user=='') $user=$this->activeuser;
	$filter=" panelusername='$user'";
	if($user=='') return True;

	switch($limittype){			# different naming issue in conf...
		case "maxdomains":
			return $this->isLimitExceeded($this->conf['domainstable']['tablename'],$filter,$this->conf['paneluserstable']['tablename'],"maxdomains",$filter);
		break;

		case "maxpanelusers":
			return $this->isLimitExceeded($this->conf['paneluserstable']['tablename'],"reseller='$user'",$this->conf['paneluserstable']['tablename'],"maxpanelusers",$filter);
		break;

		case "maxftpusers":
			return $this->isLimitExceeded($this->conf['ftpuserstable']['tablename'],"reseller='$user'",$this->conf['paneluserstable']['tablename'],"maxftpusers",$filter);
		break;

		case "maxemails":
			return $this->isLimitExceeded($this->conf['emailuserstable']['tablename'],$this->conf['emailuserstable']['ownerfield']."='$user'",$this->conf['paneluserstable']['tablename'],"maxemails",$filter);
		break;

		case "maxdbs":
			return $this->isLimitExceeded($this->conf['mysqldbstable']['tablename'],"panelusername='$user'",$this->conf['paneluserstable']['tablename'],"maxdbs",$filter);
		break;

		case "maxemails":
			return $this->isLimitExceeded($this->conf['emailuserstable']['tablename'],"panelusername='$user'",$this->conf['paneluserstable']['tablename'],"maxemails",$filter);
		break;

		case "maxvps":
			#return $this->isLimitExceeded($this->conf['emailuserstable']['tablename'],"panelusername='$user'",$this->conf['paneluserstable']['tablename'],"maxemails",$filter);
			return False; # not checked yet.
		break;

		default : $this->errorText($this->sayinmylang("int_undefined_limittype").": $limittype <br>");
		return True;
	}

	#$this->output.="max limit error: $limittype , you exceeded your limit<br>";
	return false;
}


function isuserpermited($permtype,$item='',$user='') {  # user permission check, is user authorized to do something
	if($this->isadmin()) return True; # admin is permitted to anything.

	if($user=='') $user=$this->activeuser;
	switch ($permtype) {
		case "deletedomain":
			$this->requireNoDemo();
			$filt=$this->applyGlobalFilter("domainname='$item'");
			$reseller=$this->getField($this->conf['domainstable']['tablename'],"reseller",$filt);
			if($reseller==$user or $this->isadmin()) return True;

		break;
		case "caneditpaneluser":
			$reseller=$this->getField($this->conf['paneluserstable']['tablename'],"reseller","id=$item");
			$panelusername=$this->getField($this->conf['paneluserstable']['tablename'],"panelusername","id=$item");
			if($this->activeuser==$panelusername and !$this->isadmin()) return $this->errorText('you cannot edit yourself ');
			return ($this->isadmin() or ($reseller==$this->activeuser)) ;
		break;

		case "domainowner":
			$domainowner=$this->alanal2($this->conf['domainstable']['tablename'],[$this->conf['domainstable']['ownerfield'],$this->conf['domainstable']['resellerfield']],"domainname='$item'");
			if($this->isadmin() or $domainowner[$this->conf['domainstable']['ownerfield']]==$this->activeuser or $domainowner['reseller']==$this->activeuser) return True;
		break;

		default: return $this->errorText("internal ehcp error: undefined permtype: $permtype <br> This feature may not be complete-2");
	}

	return $this->errorText("permission error: permtype: $permtype , item: $item ");
}


function viewpaneluser(){
	global $id;
	$this->getVariable(["id"]);
	if(!$this->isuserpermited('caneditpaneluser',$id)) return false;
	$filter=andle($this->globalfilter,"id = $id");
	$table=$this->conf['paneluserstable'];
	$this->output.=print_r3($this->alanal2($table['tablename'],$table['viewfields'],"id=".$id));

}


function editPanelUser(){
	global $id,$newpass,$newpass2;
	$this->getVariable(["id",'newpass','newpass2']); # can edit only if (s)he is reseller of that panel user or is admin..

	$info=$this->getPanelUserInfo($id);
	if($info['panelusername']=='admin') $this->requireNoDemo();


	if(!$this->isuserpermited('caneditpaneluser',$id)){  # is this owner of that panel user ?
		return $this->errorText("you are not authorized to edit this user.. ");
	} else {
		$extra=[['status','select','secenekler'=>$this->statusActivePassive]];
		if(!$newpass and !$newpass2 and $id) $this->editrow("paneluserstable","id=$id",$extra);
	}

	if(!$newpass and !$newpass2 and $id){
		$inputparams=[
			'newpass',
			'newpass2',
			['id','hidden','default'=>$id],
			['op','hidden','default'=>'editpaneluser'],
			['op','hidden','default'=>__FUNCTION__]
		];
		$this->output.="<br>Reset User Pass: <br>".inputform5($inputparams);

	} elseif ($newpass and $newpass2 and $id and $newpass==$newpass2){

		$success=$this->executeQuery("update ".$this->conf['paneluserstable']['tablename']." set password=md5('$newpass') where id=$id ",'',__FUNCTION__);
		return $this->ok_err_text($success, "Success pass change", "fail pass change");

	}
}

function domainsettings(){
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$dominfo=$this->getDomainInfo($domainname);

	$alanlar=['theorder','_insert'];
	foreach($alanlar as $al) global ${$al};
	$degerler=$this->getVariable($alanlar);

	if($_insert){
		$success=True;
		$success=$success && $this->executeQuery("update domains set theorder=$theorder where domainname='$domainname'");
		$success=$success && $this->addDaemonOp('syncdomains','',$domainname,'','sync domains');
		$this->ok_err_text($success,"success","fail ");
	} else {
		$inputparams=[['theorder','default'=>$dominfo['theorder']]];
		$this->output.=inputform5($inputparams);
	}

}


function existscontrol($controls){
	# to be coded later. like domain=>"xxx.com"
	$result=True;
	foreach($controls as $key=>$val){
		$count=0;
		switch($key){
			# vps cases
			case 'ip': break ; # no ip check, same ip can exists on different servers, perhaps..
			case 'hostip': break ;  # multiple vps will have same hostip
			case "vpsname":
				$count=$this->recordcount($this->conf['vpstable']['tablename'],"vpsname='$val'");
			break;
			# end vps cases


			case "domainname":
				$count=$this->recordcount($this->conf['domainstable']['tablename'],"domainname='$val'");
			break;

			case "ftpusername":
				$count=$this->recordcount($this->conf['ftpuserstable']['tablename'],"ftpusername='$val'");
			break;

			case "panelusername":
				$count=$this->recordcount($this->conf['paneluserstable']['tablename'],"panelusername='$val'");
			break;

			case "dbname":
				$count=$this->recordcount($this->conf['mysqldbstable']['tablename'],"dbname='$val'");
			break;

			case "dbusername":
				$count=$this->recordcount($this->conf['mysqldbuserstable']['tablename'],"dbusername='$val'");
			break;

			case 'email':
				$count=$this->recordcount($this->conf['emailuserstable']['tablename'],"email='$val'");
			break;

			case 'mailusername':
				$count=0; # mailusername may be multiple for different domains, no problem.
			break;

			default: return $this->errorText("internal ehcp error: Undefined parameter: $key=>$val  ar:".print_r2($controls)."<br> This feature may not be complete-3");
		}

		if($count>0) {
			$result=$this->errorText("$key already exists: $val ");
		}

	} #foreach
	if(!$result) $this->errorText("At least one error occured! ");
	return $result;
}# function

/*
steps to control when doing something:
1- is user active?
2- is user limit exceeded for that action?
3- is user permitted to do that?
4- is user owner of that or authorized?
5- is target entity already exists ? that is, is domain exists, or is panel users exists ?

two kind of control:
before displaying inputs, is user active, limit controls, is user permitted, is user owner(if entity known)
after submitting inputs, all of these controls, for security, plus, is that alread exists?
*/

# ---------------- error ve debug fonksiyonlari...

function error_occured($source='',$errstr=''){
	if(!$source) $source='source not specified';
	$str=$this->sayinmylang('error_occured')."(source:$source)<hr>err: $errstr <hr>".$this->conn->ErrorMsg();
	return $this->errorText($str);
}

function error_text_tr($str='',$emailtoadmin=false){
	return $this->errorText($this->tr($str),$emailtoadmin);
}

function errorText($str='',$emailtoadmin=false){
	if($str[0]=='_') $str=$this->tr($str); # use language translate file.. after underscore.. 

	if(!$this->erroroccured) $img="<img border=0 src='images/stop.jpg' align=left>";# only one stop sign
	$this->erroroccured=True;
	if(is_object($this->conn)) { # if connection exists
		if($this->conn->ErrorMsg()<>'') $str.="<hr>last db error:".$this->conn->ErrorMsg();
	}

	if($this->commandline) {
		echo "\n___________________\n$str\n__________________\n";
	}else{
		$this->output.="<br><table>
							<tr>
								<td align='center' valign='middle'>$img</td>
								<td align='center' valign='middle'><b>Some Error Occured.<br><font color='#FF0000'>$str
								<br>
								</font></b></td>
							</tr>
						</table><br>";
	}
	if($emailtoadmin) $this->infotoadminemail($str,false);
	return false; # errorText function always returns false;
}

function okeyText($str=''){
	if($str[0]=='_') $str=$this->tr($str); # use language translate file.. after underscore.. 

	$img="<img border=0 src=images/successfull.jpg>";
		$this->output.="<br><table>
							<tr>
								<td align='center' valign='middle'>$img</td>
								<td align='center' valign='middle'><b><font color='#69C657'>Operation completed successfully !<br>".$str."</font></b></td>
							</tr>
						</table><br>";
	return True;# okeytext always returns True
}

function ok_err_text($success,$successtext='',$failtext=''){
	if($success)
		$this->okeyText($successtext);
		else $this->errorText($failtext);
	return $success;
}


function debugtext($str){

	if($this->debuglevel == 0) return false; # z7 mod

	$img="<img border=0 src=images/debug.jpg>";
		$this->output.="<br><table>
							<tr>
								<td align='center' valign='middle'>$img</td>
								<td align='center' valign='middle'><b><font color='#996699'>Debug output:<br>".$str."</font></b></td>
							</tr>
						</table><br>";
	return True;
}

#---- end of error-debug functions


function isactive($user=''){
	if(!$user) $user=$this->activeuser;
	$status=$this->getField($this->conf['paneluserstable']['tablename'],"status",$this->conf['paneluserstable']['usernamefield']."='$user'");

	if($status!=$this->status_active){
		return $this->errorText("Error:User is not active..: $user (Your account not active,contact your panel service provider, status: $status) ");
	}else return True;
}

function beforeInputControls($op='',$params=''){
	# all before input controls in a single place, so after input controls...
	# returns false, if input control fails and user cannor proceed,
	# returns True, if user can proceed

	# first common controls
	if(!$this->isNoPassOp()) { # if no password required, being active is not required. if this is an operation that does not need a pass.. do it without checking if active.
		if(!$this->isactive()) return false;
	}

	# controls specific to op(eration)
	switch($op){
		case "adddomaintothispaneluser":
			return !$this->isuserlimitexceeded('maxdomains');
		break;

		case "adddomain":
			return !(
				$this->isuserlimitexceeded('maxdomains')
				or $this->isuserlimitexceeded('maxpanelusers')
				or $this->isuserlimitexceeded('maxftpusers')
				)
				or
				$this->isNoPassOp();# for domain requests, no limit is important.. there may be a limit here too.
		break;

		case "addftpuser":
			return !$this->isuserlimitexceeded('maxftpusers') or $this->isNoPassOp();# for domain requests
		break;

		case 'adddb':
			return !$this->isuserlimitexceeded('maxdbs');
			# old and equivalent statement: if($this->isuserlimitexceeded('maxdbs')) return false;
			# meaning: if user maxdb limit exceeded, user cannot add more, cannot proceed, so return false..
		break;

		case 'addpaneluser':
			return !$this->isuserlimitexceeded('maxpanelusers');
		break;

		case 'addemail':
			return !$this->isuserlimitexceeded('maxemails');
		break;

		case 'addvps':
			return !$this->isuserlimitexceeded('maxvps');
		break;

		case 'change_domainname':
			return True;
		break;

		default: $this->output.="Undefined input control: ".$op;


	}

	return True;
}

function afterInputControls($op='',$params=''){
	# to unify all after input controls

	if(!$this->beforeInputControls($op)) return false; # same controls as above,
	if(!$this->existscontrol($params)) return false;
	$domainname=trim($params['domainname']);
	if($domainname!=''){
		if(!is_valid_domain_name($domainname)) return $this->error_text_tr("domain_format_wrong");
	}

	if($op=='addvps') {
		foreach(['vpsname','ip','hostip'] as $check) if($params[$check]=='') return $this->errorTextExit("$op: $check parameter cannot be empty.");
		$sayi=$this->recordcount("vps","vpsname='".$params['vpsname']."' and ip='".$params['ip']."' and hostip='".$params['hostip']."'");
		if($sayi>0) return $this->errorTextExit("We have another vps with same name, same ip in same host/server");
	}

	# domainname check may be on top, and common for all ops.

	if($op=='adddomain' or $op=='adddomaintothispaneluser'){ # common controls for both operation
		if(!is_valid_domain_name($domainname)) return $this->error_text_tr("domain_format_wrong");
		if($domainname=='') return $this->errorText("domainname cannot be empty.");
		if($domainname==$this->miscconfig['dnsip'] or $domainname==$this->miscconfig['localip']) return $this->errorText("You cannot use ip of server for domainname: $domainname, dnsip:".$this->miscconfig['dnsip'].", localip:".$this->miscconfig['localip']); # domainde kisitlama yok, ama boyle olunca, abuk subuk seyler olabilir.. ip girince de, sunucu paneli webden ulasilamaz oluyor..

	}

	if($op=='adddomain'){
		if($domainname=='' or $params['ftpusername']=='' or $params['panelusername']=='') {
			return $this->errorText("domainname, ftpusername or panelusername cannot be empty.  <br>You provided: <br> ".print_r2($params));
		}
	}

	if($op=='add_domain_db'){
		if($domainname=='' or $params['ftpusername']=='' or $params['panelusername']=='' or $params['dbusername']=='' or $params['dbname']=='') {
			return $this->errorText("domainname, ftpusername, panelusername, dbusername, dbname cannot be empty.  <br>You provided: <br> ".print_r2($params));
		}
	}

	if($op=='adddomaintothispaneluser'){
		if($params['domainname']=='') {
			return $this->errorText("domainname cannot be empty.  <br>You provided: <br> ".print_r2($params));
		}
	}

	if($op=='adddb'){
		# thanks to www.bikcmp.com for bug-report..
		if($params['dbusername']=='root') return $this->errorText("'root' username is forbidden, use another username");
	}

	if($op=='addemail'){
		if(trim($params['mailusername'])==''){
			return $this->errorText("mailusername cannot be empty.  <br>You provided: <br> ".print_r2($params));
		}
	}



	return True;
}

function addDomainEasy(){
	global $domainname,$email,$password,$_insert;
	$this->getVariable(["domainname","email",'password','_insert']);
	$success=True;

	if(!$_insert) {
			if(!$this->beforeInputControls("adddomain",[])) return false;

			$inputparams=[
				'domainname',
				['password','password_with_generate','default'=>'1234'],
				['email','lefttext'=>'Email of domain owner','default'=>$this->miscconfig['adminemail']],
				['op','hidden','default'=>__FUNCTION__]
			];


			$this->output.="<b>Enter info below, all usernames will be domainname, all passwords will be same:</b> <br>Default values are: quota(mb):50, ul/dl bw: 200KB/s<br>"
			."<br>Normally, do not write www. only yourdom.com forexample: <br>"
			.inputform5($inputparams);

			/* inputform4($action,array('','','Email of domain owner'),
			array("domainname",'password','email'),
			array('','1234',$this->miscconfig['adminemail']),array("op","_insert"),array("adddomaineasy","1"));
			*/


	} else {
			$panelusername=$ftpusername=$domainname;
			$paneluserpass=$ftppassword=$password;

			$success=$success && $this->addDomainDirect($domainname,$panelusername,$paneluserpass,$ftpusername,$ftppassword,$this->status_active,$email);
			$success=$success && $this->setselecteddomain($domainname);
			$this->ok_err_text($success,'domain add complete','failed to add domain (adddomain)');
	}
	$this->showSimilarFunctions('domain');
	return $success;
}


function addDomainEasyip(){
	global $domainname,$email,$password,$ip,$_insert;
	$this->getVariable(["domainname","email",'password','ip','_insert']);
	$success=True;

	if(!$_insert) {
			if(!$this->beforeInputControls("adddomain",[])) return false;
			$ips=$this->query("select ip from servers where accessip='localhost'");
			#$this->print_r2($ips);
			$ips2=[];
			foreach($ips as $i) $ips2[$i['ip']]=$i['ip'];

			$inputparams=[
				['ip','select','lefttext'=>'IP to be assigned','secenekler'=>$ips2,'righttext'=>"<a href='?op=listservers'>List/Add Servers/ IP's</a>"],
				'domainname',
				['password','password_with_generate','default'=>'1234'],
				['email','lefttext'=>'Email of domain owner','default'=>$this->miscconfig['adminemail']],
				['op','hidden','default'=>__FUNCTION__]
			];

			#$this->print_r2($inputparams);

			$this->output.="<b>Enter info below, all usernames will be domainname, all passwords will be same:</b> <br>Default values are: quota(mb):50, ul/dl bw: 200KB/s<br>"
			."<br>Normally, do not write www. only yourdom.com forexample: <br>"
			.inputform5($inputparams);

			/* inputform4($action,array('','','Email of domain owner'),
			array("domainname",'password','email'),
			array('','1234',$this->miscconfig['adminemail']),array("op","_insert"),array("adddomaineasy","1"));
			*/


	} else {
			$panelusername=$ftpusername=$domainname;
			$paneluserpass=$ftppassword=$password;

			$success=$success && $this->addDomainDirect($domainname,$panelusername,$paneluserpass,$ftpusername,$ftppassword,$this->status_active,$email,0,$ip);
			$success=$success && $this->setselecteddomain($domainname);
			$this->ok_err_text($success,'domain add complete','failed to add domain (adddomain)');
	}
	$this->showSimilarFunctions('domain');
	return $success;
}


function transferDomain(){
	# transfer domain to another ehcp user/client in this server
	// Todo: 
	// target user has ftp? if not, add ftp. 
	// transfer domain files also to new location:  mv xxx.net /var/www/vhosts/xxx/
	// update domains set homedir='/var/www/vhosts/xxx/xxx.net' where domainname='xxx.net';
	// insert into operations (op) values ('syncdomains');

	global $domainname,$username,$_insert;
	$this->getVariable(["domainname",'username','_insert']);

	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);

	if(!$username){
		$this->output.="Enter ehcp username to transfer domain onto: (This is for transferring this domain to another user in this server..)".inputform5("username");
	} else {
		$count=$this->recordcount($this->conf['paneluserstable']['tablename'], "panelusername='$username'");
		if($count==0) return $this->errorText("User is not found: $username");

		$success=$this->executeQuery("update ".$this->conf['domainstable']['tablename']." set ".$this->conf['domainstable']['ownerfield']."='$username' where domainname='$domainname'", $opname);

		# Transfer related databases also..
		$success=$success && $this->executeQuery("update mysqldb set panelusername='$username' where panelusername='{$this->activeuser}' and domainname='$domainname'");

		return $this->ok_err_text($success, "domain transfered", "domain transfer failed");
	}
}

function getSelfFtpAccount($returnto1=''){
	global $ftpusername,$ftppassword,$returnto,$_insert;
	$this->getVariable(['ftpusername','ftppassword','returnto','_insert']);

	$selfftp=$this->getField('ftpaccounts','ftpusername',"panelusername='$this->activeuser' and type='default'");
	if($selfftp<>'') return $selfftp;

	if($_insert) {
		if(!$this->afterInputControls('addftpuser',['ftpusername'=>$ftpusername])) return false;
		$this->output.='Will add here';
		$success=$this->addFtpUserDirect($this->activeuser,$ftpusername,$ftppassword,$this->conf['vhosts'].'/'.$ftpusername,$upload=100,$download=100,$quota=1000,$domainname,'default');
		$this->redirecttourl('?op='.$returnto);
	} else {
		if(!$this->beforeInputControls('addftpuser')) return false;

		$inputparams=[
			'ftpusername',
			['ftppassword','password_with_generate'],
			['returnto','hidden','default'=>$returnto1],
			['op','hidden','default'=>__FUNCTION__]
		];


		$this->output.="You dont have a default ftp account. Will setup a new one now (will do this only once):<br>Enter your new default ftp info:"
		.inputform5($inputparams);
		/* inputform4($action,array(''),
			array('ftpusername','ftppassword'),
			[],array('op','_insert','returnto'),array(__FUNCTION__,'1',$returnto1)); */

		$this->showexit();
	}
	return True;
}


function add_domain_install_wordpress(){ # add domain to this paneluser and existing ftp space
	global $domainname,$_insert;
	$this->getVariable(["domainname","_insert"]);
	$selfftp=$this->getSelfFtpAccount($returnto=__FUNCTION__); # ftp account for this panel user is with type field=default in ftpaccounts table
	$success=True;

	if(!$_insert) {
		if(!$this->beforeInputControls("adddomaintothispaneluser",[])) return false;
		$inputparams=[
			'domainname',
			['op','hidden','default'=>__FUNCTION__]
		];
		$this->output.="<big>This will add/setup domain, install wordpress into it, adds a new database&user for your wordpress:</big><br><br>Normally, do not write www. only yourdom.com forexample, <br>you will use your current ftp of yourself: <br><br>".inputform5($inputparams);
	} else {
		$success=$success && $this->addDomainDirectToThisPaneluser($domainname,$selfftp);
		$success=$success && $this->setselecteddomain($domainname);
		$success=$success && $this->addDaemonOp("installscript", 'cms - wordpress', $domainname, '', 'install wordpress');

		$dname2=str_replace('.', '', $domainname);
		$dbname=$dname2.'_wp';
		$dbusername=$dname2;
		if(strlen($dbusername)>16) { # mysql does not accept usernames longer than 16: related error: .. is too long for user name (should be no longer than 16)
			$dbusername=substr($dbusername, 0, 16);
		} 

		$dbuserpass=$this->generate_password();
		$success=$success && $this->addMysqlDbDirect($myserver,$domainname,$dbusername,$dbuserpass,$dbuserhost,$dbname,$adduser=True);

		$domainpath=$this->vhostsdir.'/'.$selfftp.'/'.$domainname.'/httpdocs';		
		$success=$success && $this->addDaemonOp("installwp", $domainpath, $dbname, $dbusername,'install wordpress',$dbuserpass);

		if($success) $this->output.="<br><hr><big>
		DB information for your wordpress:<br>
		dbname: $dbname<br>
		db username: $dbusername<br>
		db user pass: $dbuserpass<br>
		<br>
		Wordpress admin username: admin <br>
		Wordpress admin password: $dbuserpass (same as dbuserpass, you may change this)<br>
		</big><br>";
		$this->ok_err_text($success,'domain add complete','failed to add domain (adddomain)');
	}
	$this->showSimilarFunctions('domain');
	return $success;
}

function addDomainToThisPaneluser(){ # add domain to this paneluser and existing ftp space
	global $domainname,$_insert;
	$this->getVariable(["domainname","_insert"]);
	$selfftp=$this->getSelfFtpAccount($returnto=__FUNCTION__); # ftp account for this panel user is with type field=default in ftpaccounts table
	$success=True;

	if(!$_insert) {
		if(!$this->beforeInputControls("adddomaintothispaneluser",[])) return false;
		$inputparams=[
			'domainname',
			['op','hidden','default'=>__FUNCTION__]
		];
		$this->output.="<br>Normally, do not write www. only yourdom.com forexample, <br>you will use your current ftp of yourself: <br>".inputform5($inputparams);
	} else {
		$success=$success && $this->addDomainDirectToThisPaneluser($domainname,$selfftp);
		$success=$success && $this->setselecteddomain($domainname);
		$this->ok_err_text($success,'domain add complete','failed to add domain (adddomain)');
	}
	$this->showSimilarFunctions('domain');
	return $success;
}

function strNewlineToArray($str){
	# this is written because, input text in an inputbox in web browser, sometime has \n as newline char, sometime  \r\n
	# used in especially bulkaddDomain
	$str=str_replace(["\n\r", "\r\n", "\r"],["\n", "\n", "\n"], $str);
	# $this->output.="<pre>$str</pre><hr>";

	$ret=explode("\n", $str); # bugreport by razvan , should work for all browsers. https://bugs.launchpad.net/ehcp/+bug/524551
	return $ret;
}

function bulkAddEmail(){
/*
thanks avra911: http://www.ehcp.net/?q=node/577#comment-1414

email1@domain1.com:password1
email2@domain2.com:password2

*/
	global $emails;
	$this->getVariable(['emails']);

	if(!$emails){
		$this->output.="Enter emails line by line, like:<br>
email1@domain1.com:password1<br>
email2@domain2.com:password2<br>
<br>
<form method=post><textarea rows=30 cols=40 name=emails></textarea><br><input type=hidden name=op value=".__FUNCTION__."><input type=submit></form>";
		$this->showSimilarFunctions('email');
	} else {
		$this->output.="emailler eklenecek:";
		$emails=$this->strNewlineToArray($emails);
		$this->output.=print_r2($emails);
		foreach($emails as $line){
			$line=trim($line);
			if($line=='') continue;
			$info=explode(":",$line);  # get email part
			$info2=explode("@",$info[0]);
			$mailusername=$info2[0];
			$domainname=$info2[1];
			$password=$info[1]; # get pass
			$this->addEmailDirect($mailusername,$domainname,$password,$quota=10,$autoreplysubject,$autoreplymessage);
		}
	}

}

function bulkaddDomain(){
	# gets many domains in one step, adds them, then call syncdns and syncdomains once
	global $domainler;
	$this->getVariable(['domainler']);
	$this->requireNoDemo();
	$selfftp=$this->getSelfFtpAccount($returnto=__FUNCTION__);
	$success=True;

	if(!$domainler){
		$this->output.="Enter domain names below one by one, <br>don't enter www. at start of domains,<br> All domains will be setup under your ftp directory:<br> <form method=post><textarea rows=30 cols=40 name=domainler></textarea><br><input type=hidden name=op value=bulkadddomain><input type=submit></form>";
	} else {
		$this->output.="Adding domains: <br>";
		$domains=$this->strNewlineToArray($domainler);
		$newdomains=[];

		foreach($domains as $dom){
			$dom=trim($dom);
			$dom=str_replace(["\\","www."],['',''],$dom); # replace accidental www.'s

			if($dom=="") continue;
			if(!in_array($dom,$newdomains)) {
				$newdomains[]=$dom; # eliminate duplicate domainnames..

				$this->output.="Checking domain: $dom<br>";

				if(!$this->afterInputControls("adddomaintothispaneluser",
						[
						"domainname"=>$dom,
						]
					)
				) return false;
			}
		}

		foreach($newdomains as $dom){
			$this->output.="Setting up domain: $dom<br>";
			$success=$success && $this->addDomainDirectToThisPaneluser($dom,$selfftp,false);
		}


		# sync all new domains...
		#$success=$success && $this->addDaemonOp("syncdomains",'xx','','','sync domains'); # this is not needed anymore, since each domain is synced itself, in addDomainDirectToThisPaneluser function above.
		$success=$success && $this->addDaemonOp("syncdns",'','','','sync dns');

		$this->ok_err_text($success,"Domains added...","Failed to add domains");
	}
	$this->showSimilarFunctions('domain');
	return $success;
}

function bulkDeleteDomain(){
	# this is not put in gui yet, for security
	global $domainler;
	$this->getVariable(['domainler']);

	$this->requireNoDemo();
	$success=True;

	if(!$domainler){
		$this->output.="Enter domain names below one by one, <br>don't enter www. at start of domains,<br> All domains will be deleted automatically. BE CAREFUL !!! :<br> <form method=post><textarea rows=30 cols=40 name=domainler></textarea><br><input type=hidden name=op value=bulkdeletedomain><input type=submit></form>";
	} else {
		$this->output.="Deleting domains: <br>";
		$domains=$this->strNewlineToArray($domainler);

		$newdomains=[];

		foreach($domains as $dom){
			$dom=trim($dom);
			$dom=str_replace("\\",'',$dom);

			if($dom=="") continue;
			if(!in_array($dom,$newdomains) and $this->isuserpermited('deletedomain',$dom)) {
				$newdomains[]=$dom; # eliminate duplicate domainnames.. and check if deletable,
			}
		}

		foreach($newdomains as $dom){
			$this->output.="Deleting domain: $dom<br>";
			$success=$success && $this->deleteDomainDirect($dom,false);
		}


		# sync all domains...
		#$this->addDaemonOp("syncdomains",'xx','','','sync domains');  # this is not needed anymore, since each domain is synced itself.
		$this->addDaemonOp("syncdns",'','','','sync dns');

		return $this->ok_err_text($success,"Domains deleted...","Failed to delete domains");

	}
}

function noEmpty($values){
	if(!is_array($values)) $values=[$values]; # this way, function may accept both array,and non-array
	foreach($values as $val)
		if($val=='') $this->errorTextExit("Empty value not allowed");
}

function get_homedir($dom) {
	return $this->getField("domains", "homedir", "domainname='$dom'");
}

function get_ftpusername($dom){
	return "todo";
}

function change_domainname(){
	global $newdomain, $_insert;
	$this->getVariable(["newdomain", "_insert"]);

	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$success=True;

	if($_insert) {
		if(!$this->afterInputControls("change_domainname",
				[
				"domainname"=>$newdomain
				]
			)
		) return False;

		$olddir=$this->get_homedir($domainname);
		// $paneluser=$this->getField("domains", "panelusername", "domainname='$domainname'");
		$ftpusername=$this->get_ftpusername($dom);
		$homedir=$this->conf['vhosts']."/$ftpusername/$domainname"; # this will be fixed later. domainname should be updated. 

		$this->output.="Changing domainname to: $newdomain";
		$q="update domains set homedir='$homedir' where domainname='$domainname'";
		$this->executequery($q);

		foreach (["domains", "backups", "customsettings", "directories", "emailusers", "forwardings", "ftpaccounts", "mysqldb", "mysqlusers", "panelusers", "subdomains", "transport"] as $tbl) {
			$q="update $tbl set domainname='$newdomain' where domainname='$domainname'";
			$this->executequery($q);
			$this->output.="Table updated: $tbl    $q<br>";
		}
		
		$success=$success && $this->addDaemonOp("change_domainname", $olddir, $homedir);

	} else {
		$inputparams=[
			['newdomain', "lefttext"=>"Input new domainname:"],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.="<br><b>This feature is not Complete !!</b><br><br>Normally, do not write www. only yourdom.com forexample, <br>Enter new domainname that you want to set:<br>".inputform5($inputparams);

	}

	return True;
}

function daemon_change_domainname($info,$info2) {
	# only rename directory.
}


function addDnsOnlyDomainWithPaneluser(){
	global $domainname,$serverip,$_insert,$password,$email;
	$this->getVariable(["domainname","_insert",'serverip','password','email']);
	$success=True;

	if(!$_insert) {
		if(!$this->beforeInputControls("adddomaintothispaneluser",[])) return false;
		$inputparams=['domainname',
			['password','password_with_generate'],
			'email',
			'serverip',
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.="<br>Normally, do not write www. only yourdom.com forexample, <br>Enter serverip where domain is hosted actually<br>".inputform5($inputparams);
	} else {

		if(!$this->afterInputControls("adddomaintothispaneluser",
				[
				"domainname"=>$domainname,
				]
			)
		) return false;

		$paneluserinfo=$this->getPanelUserInfo();
		$panelusername=$ftpusername=$domainname;
		$paneluserpass=$ftppassword=$password;

		$sql="insert into ".$this->conf['domainstable']['tablename']." (reseller,panelusername,domainname,homedir,status,serverip) values ('".$this->activeuser."','$panelusername','$domainname','','".$this->status_active."','$serverip')";
		$success=$success && $this->executeQuery($sql);
		$success=$success && $this->addPanelUserDirect($panelusername,$paneluserpass,1,0,0,0,0,$quota,'',$email,$this->status_active);
		$success=$success && $this->addDaemonOp("syncdns",'','','','sync dns');
		$this->ok_err_text($success,'domain dnsonly add complete','failed to add domain ('.__FUNCTION__.')');
	}
	$this->showSimilarFunctions('domain');
	return $success;

}

function isValidIP($ip){ # by earnolmartin@gmail.com
	if(preg_match( "/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/", $ip)){
		return True;
	}else{
		return False;
	}
}

function getMasterIP($domainname){ # by earnolmartin@gmail.com
  $res=$this->query("select dnsmaster from domains where domainname = '$domainname'");
  return $res[0]['dnsmaster'];
}

function addSlaveDNS(){ # coded by earnolmartin@gmail.com, modified little by ehcpdeveloper
	global $serverip,$_insert,$password,$dnsmaster,$email;
	$this->getVariable(["_insert",'dnsmaster']);
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname); # this ensures a domain selected.

	$success=True;
	$errmsg='';

    if($this->getIsSlaveDomain($domainname)){
		$dnsmaster = $this->getMasterIP($domainname);
		$isAlreadySlave="<p style='color: red;'> $domainname is currently configured as a slave DNS domain.&nbsp; You can edit the master server IP address below.&nbsp; <a href='?op=removeslavedns'>Click here</a> to remove slave configuration from the domain.&nbsp; If you do not wish to change these settings, please go back.</p>";
    }

    if(!$_insert) {
		$inputparams=[
			['dnsmaster','input','lefttext'=>'Master Server IP:','default'=>$dnsmaster],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.= $isAlreadySlave . "<p><br>If this domain should download and use DNS records from a another server, please enter the master server IP address below.<br>".inputform5($inputparams);
    } else {
		if (!$this->isValidIP($dnsmaster)) $this->errorTextExit("IP address $dnsmaster entered is invalid!");

		$sql="update ".$this->conf['domainstable']['tablename']." set dnsmaster ='$dnsmaster' WHERE domainname = '$domainname'";
		$success=$success && $this->executeQuery($sql);
		$success=$success && $this->addDaemonOp("syncdns",'','','','sync dns');

		# single function ok_err_text is enaugh at end of an operation.
		$this->ok_err_text($success,'Domain has been configured as a DNS Slave',"Failed to change domain into slave! $errmsg (".__FUNCTION__.')');
    }


	$this->showSimilarFunctions('domain');
	return $success;
}

function errArrayToStr($errors){
	$errStr = '';

	foreach($errors as $err){
		$errStr .= $err . "<br>";
	}

	return $errStr;
}

function removeSlaveDNS(){ # coded by earnolmartin@gmail.com, modified by ehcpdeveloper
	global $domainname,$serverip,$_insert,$password,$email, $yes, $no;
	$this->getVariable(["_insert",'yes','no']);
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname); # this ensures a domain selected.

	$success=True;

	if(!($this->getIsSlaveDomain($domainname))) $this->errorTextExit("Configuration cannot be changed! The currently selected domain is already NOT configured as a slave!");

	if(!$_insert) {
		$inputparams=[
			['op','hidden','default'=>__FUNCTION__],
			['submit','submit','default'=>'Yes']
		];

		$this->output.="<p><br>Are you sure you want to remove the slave status from $domainname ?".inputform5($inputparams);
	} else {
		$sql="update ".$this->conf['domainstable']['tablename']." set dnsmaster = NULL WHERE domainname = '$domainname'";
		$success=$success && $this->executeQuery($sql);
		$success=$success && $this->addDaemonOp("syncdns",'','','','sync dns');
		$this->ok_err_text($success,"$domainname is no longer configured as a slave domain ","$errmsg <br>No configuration changes have been made to the DNS type of your domain! (".__FUNCTION__.')');
	}


	$this->showSimilarFunctions('domain');
	return $success;
}

function addCustomFTP(){ # coded by earnolmartin@gmail.com
	global $serverip,$_insert,$password,$hpath,$ftplogin,$ftppass,$email;

	// Custom FTP accounts must be configured by an admin.
	# this may have some security concerns... be careful

	$this->requireAdmin();

	$this->getVariable(["_insert",'ftplogin','ftppass','hpath']);

	$success=True;
	$errmsg='';

    if(!$_insert) {
		$inputparams=[
			['ftplogin','input','lefttext'=>'FTP Login:','default'=>$ftplogin],
			['ftppass','input','lefttext'=>'FTP Password:','default'=>$ftppass],
			['hpath','input','lefttext'=>'FTP Home Directory:','default'=>$hpath],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.= "<br>Create Custom FTP Account<br>".inputform5($inputparams);
    } else {

		// Clear any errors that may exist
		if(isset($errors)){
			unset($errors);
		}

		/*                         *
		 *  All Fields Have Value  *
		 *                         */

		if(empty($hpath)){
			$errors[] = "Please enter a directory path as the user's home FTP directory!";
		}

		if(empty($ftplogin)){
			$errors[] = "Please enter a username for this FTP account!";
		}

		if(empty($ftppass)){
			$errors[] = "Please enter a password for this FTP account!";
		}else{
			if(strlen($ftppass) < 4){
				$errors[] = "Password must be at least 4 characters long!";
			}
		}

		// Output errors

		if(isset($errors) && is_array($errors)){
			$errStr = $this->errArrayToStr($errors);
			unset($errors);
			$this->errorTextExit($errStr);
		}

		/*                   *
		 *  Path validation  *
		 *                   */

        if(!preg_match('/^[^*?"<>|:]*$/i', $hpath)){
           	$errors[] = "You entered an invalid Linux path!";
        }

        // Remove tailing slash if exists
        if($hpath[strlen($hpath) - 1] == '/'){
           	$hpath = substr($hpath, 0, strlen($hpath) - 1);
        }

        if(substr_count($hpath, '/') < 2){
            $errorCount++;
           	$errors[] = "In order to prevent security risks, users cannot be granted access to the main directories in the root file system of the server.&nbsp; You must go down two directory levels!&nbsp; Example:  /games/user1!";
        }

        if(stripos($hpath, "/") === FALSE || stripos($hpath, "/") != 0){
           	$errorCount++;
           	$errors[] = "You have not chosen a valid directory!";
        }

        if($hpath === "/var/www/" || stripos($hpath, "/var/www/") !== FALSE || $hpath === "/var/www"){
           	$errorCount++;
           	$errors[] = "You may not create custom FTP accounts with access to protected EHCP directories!";
        }

        if(stripos($hpath, "\\")){
           	$errorCount++;
           	$errors[] = "This is not a Windows machine... use the correct slash character for path...";
        }

        // Output errors

		if(isset($errors) && is_array($errors)){
			$errStr = $this->errArrayToStr($errors);
			unset($errors);
			$this->errorTextExit($errStr);
		}

		// Security checks
        $ftp_password_db = mysql_real_escape_string($ftppass);
        $ftp_username_db = mysql_real_escape_string($ftplogin);
        $rDir = mysql_real_escape_string($hpath);
        $SQL = "SELECT id FROM " . $this->conf['ftpuserstable']['tablename'] . " WHERE ftpusername = '$ftp_username_db'";

		// Run Query
		$rs=mysql_query($SQL);

		// Any errors?
		$err=mysql_error();
		if(isset($err) && !empty($err)){
			$this->errorTextExit($err);
		}

		if(mysql_num_rows($rs) == 0){
			$SQL = "INSERT INTO " . $this->conf['ftpuserstable']['tablename'] . " (ftpusername, password, homedir) VALUES ('$ftp_username_db', password('$ftp_password_db'), '$rDir')";

			// Run Query
			$this->executeQuery($SQL);

			// Any errors?
			$err=mysql_error();
			if(isset($err) && !empty($err)){
				$this->errorTextExit($err);
			}

		}else{
			$this->errorTextExit("Another account is already using the login of " . $ftp_username_db . "! Please try another username!");
		}

		$success=$success && $this->addDaemonOp("syncftp",'','','','sync/restart ftp');

		# single function ok_err_text is enaugh at end of an operation.
		$this->ok_err_text($success,'Successfully added the custom FTP account with a login of ' . $ftp_username_db . '!',"Failed to add FTP account with a login of " . $ftp_username_db . "! (".__FUNCTION__.')');
    }

	$this->showSimilarFunctions('ftp');
	return $success;
}

function removeCustomFTP(){ # coded by earnolmartin@gmail.com
	global $_insert,$loginsToDelete;

	// Custom FTP accounts must be configured by an admin.
	$this->requireAdmin();

	$this->getVariable(["_insert"]);
	$loginsToDelete = $_POST['loginsToDelete'];
	$success=True;

	if(!$_insert) {
		$inputparams=[
			['op','hidden','default'=>__FUNCTION__],
			['submit','submit','default'=>'Remove Selected FTP Accounts']
		];

		// Build table based on queries
		$SQL = "SELECT id, ftpusername, homedir FROM " . $this->conf['ftpuserstable']['tablename'] . " where homedir IS NOT NULL and status IS NULL";

		// Run Query
		$rs=mysql_query($SQL);

		// Any errors?
		$err=mysql_error();
		if(isset($err) && !empty($err)){
			$this->errorTextExit($err);
		}

		if(mysql_num_rows($rs) == 0){
			$this->errorTextExit('Currently no custom FTP accounts exist!');
		}else{
			$table = "<form method=\"post\" action=\"?op=". __FUNCTION__ . "\"><table><tr><th style=\"width: 100px;\">Select</th><th style=\"width: 200px;\">Username</th><th style=\"width: 200px;\">Home Directory</th></tr>";
			while($r=mysql_fetch_assoc($rs)){
				// Only show custom entries... do not allow to modify EHCP accounts.
              	if(!empty($r['homedir'])){
					$countNotNull++;
                    $table .= "<tr><td><input type=\"checkbox\" value=\"{$r['id']}\" name=\"loginsToDelete[]\" /></td><td>{$r['ftpusername']}</td><td>{$r['homedir']}</td></tr>";
              	}
			}
			$table .= "</table><br><input type=\"submit\" value=\"Delete Selected Accounts\" name=\"_insert\"></form>";
		}

		$this->output.="<br>List of FTP Accounts<br>".$table;
	} else {
		if(isset($loginsToDelete) && is_array($loginsToDelete) && count($loginsToDelete) > 0){
			foreach($loginsToDelete as $toDelete){
				// Secure the string
				$toDelete = mysql_real_escape_string($toDelete);
				$sql="delete from ". $this->conf['ftpuserstable']['tablename'] ." WHERE id = '$toDelete'";
				$success=$success && $this->executeQuery($sql);
			}
		}else{
			$success = FALSE;
			$errmsg = "No custom FTP accounts were selected for removal!";
		}

		$success=$success && $this->addDaemonOp("syncftp",'','','','sync/restart ftp');
		$this->ok_err_text($success,"Selected accounts were deleted!","$errmsg <br>No custom FTP accounts were deleted! (".__FUNCTION__.')');
	}


	$this->showSimilarFunctions('ftp');
	return $success;
}

function addDnsOnlyDomain(){
	global $domainname,$serverip,$_insert;
	$this->getVariable(["domainname","_insert",'serverip']);
	$success=True;

	if(!$_insert) {
		if(!$this->beforeInputControls("adddomaintothispaneluser",[])) return false;
		$inputparams=['domainname','serverip',
			['op','hidden','default'=>__FUNCTION__]
		];
		$this->output.="<br>Normally, do not write www. only yourdom.com forexample, <br>Enter serverip where domain is hosted actually<br>".inputform5($inputparams);

	} else {

		if(!$this->afterInputControls("adddomaintothispaneluser",
				[
				"domainname"=>$domainname,
				]
			)
		) return false;

		$this->noEmpty($domainname); # same control is done above, in afterInputControls. left here for example usage for noEmpty

		$paneluserinfo=$this->getPanelUserInfo();
		$sql="insert into ".$this->conf['domainstable']['tablename']." (reseller,panelusername,domainname,homedir,status,serverip) values ('".$this->activeuser."','".$this->activeuser."','$domainname','','".$this->status_active."','$serverip')";
		$success=$success && $this->executeQuery($sql);
		$success=$success && $this->addDaemonOp("syncdns",'','','','sync dns');
		$this->ok_err_text($success,'domain dnsonly add complete','failed to add domain ('.__FUNCTION__.')');
	}
	$this->showSimilarFunctions('domain');
	return $success;
}

function print_r2($ar){
	$this->output.=print_r2($ar);
}

function multiserver_add_domain(){ # add domain, paneluser and ftp user once
	global $dnsserverips,$webserverips,$mailserverips,$mysqlserverips,$domainname,$ftpusername,$ftppassword,$quota,$upload,$download,$panelusername,$paneluserpass,$_insert,$email;
	$vars=$this->getVariable(['dnsserverips','webserverips','mailserverips','mysqlserverips','domainname','ftpusername','ftppassword','quota','upload','download','panelusername','paneluserpass','_insert','email']);
	$success=True;

	if(!$_insert) {
		if(!$this->beforeInputControls("adddomain",[])) return false;
		$inputparams=[

			'domainname',
			['panelusername','lefttext'=>'Panel username'],
			['paneluserpass','password_with_generate','lefttext'=>'Paneluser password'],
			['ftpusername','lefttext'=>'Ftp username'],
			['ftppassword','password_with_generate','lefttext'=>'Ftp Password'],
			['quota','default'=>'200','lefttext'=>"Quota (Mb)"],
			['upload','default'=>'200','lefttext'=>"Upload bw(kb/s)"],
			['download','default'=>'200','lefttext'=>"Download bw(kb/s)"],
			['email','default'=>$this->miscconfig['adminemail']],
			['dnsserverips','hidden&text','lefttext'=>'dnsserver IPs','default'=>$this->miscconfig['defaultdnsserverips']],
			['webserverips','hidden&text','lefttext'=>'Webserver IPs','default'=>$this->miscconfig['defaultwebserverips']],
			['mailserverips','hidden&text','lefttext'=>'mailserver IPs','default'=>$this->miscconfig['defaultmailserverips']],
			['mysqlserverips','hidden&text','lefttext'=>'mysqlserver IPs','default'=>$this->miscconfig['defaultmysqlserverips']],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.="(These will be done using ServerPlans in future)<br>".inputform5($inputparams);
	} else {
		$status=$this->status_active;

		$success=$success && $this->multiserver_add_domain_direct(compact('dnsserverips','webserverips','mailserverips','mysqlserverips','domainname','panelusername','paneluserpass','ftpusername','ftppassword','status','email','quota'));
		$success=$success && $this->setselecteddomain($domainname);
		$this->ok_err_text($success,'domain add complete','failed to add domain (adddomain)');
	}
	$this->showSimilarFunctions('domain');
	return $success;
}


function addDomain(){ # add domain, paneluser and ftp user once

	global $domainname,$ftpusername,$ftppassword,$quota,$upload,$download,$panelusername,$paneluserpass,$_insert,$email, $timeout;
	$this->getVariable(["domainname","ftpusername","ftppassword","quota","upload","download","panelusername","paneluserpass","_insert",'email' ,'timeout']);

	# This is a reseller / admin feature only!
	$this->requireReseller();
	$success=True;

	if(!$_insert) {
		if(!$this->beforeInputControls("adddomain",[])) return false;
		$inputparams=[
			'domainname',
			['panelusername','lefttext'=>'Panel username'],
			['paneluserpass','password_with_generate','lefttext'=>'Paneluser password'],
			['ftpusername','lefttext'=>'Ftp username'],
			['ftppassword','password_with_generate','lefttext'=>'Ftp Password'],
			['quota','default'=>'200','lefttext'=>"Quota (Mb)"],
			['upload','default'=>'200','lefttext'=>"Upload bw(kb/s)"],
			['download','default'=>'200','lefttext'=>"Download bw(kb/s)"],
			['email','default'=>$this->miscconfig['adminemail']],
			['timeout','lefttext'=>"Timeout Date", 'righttext'=>'(2019-mm-dd) Date at which domain will be deactivated. Leave blank to disable'],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.="<br>Normally, do not write www. only yourdom.com forexample: <br>"
		.inputform5($inputparams);
		/* inputform4($action,array('',"Panel username","Panel user password:","Ftp username","ftp Password","Quota (Mb)","Upload bw(kb/s)","Download bw(kb/s)",''),
		array("domainname","panelusername",array("paneluserpass","password"),"ftpusername",array("ftppassword","password"),"quota","upload","download",'email'),
		array('','','','','',"200","200","200"),array("op","_insert"),array("adddomain","1"));
		*/

	} else {
		$success=$success && $this->addDomainDirect($domainname,$panelusername,$paneluserpass,$ftpusername,$ftppassword,$this->status_active,$email,$quota, '', compact('timeout'));
		$success=$success && $this->setselecteddomain($domainname);
		$this->ok_err_text($success,'domain add complete','failed to add domain (adddomain)');
	}
	$this->showSimilarFunctions('domain');
	return $success;
}

function add_domain_db(){ # add domain, paneluser, ftp user, database & user once

	global $domainname,$ftpusername,$ftppassword,$quota,$upload,$download,$panelusername,$paneluserpass,$_insert,$email, $dbname, $dbusername, $dbuserpass, $timeout;
	$this->getVariable(["domainname","ftpusername","ftppassword","quota","upload","download","panelusername","paneluserpass","_insert",'email', "dbname", "dbusername", "dbuserpass", "timeout"]);

	# This is a reseller / admin feature only!
	$this->requireReseller();
	$success=True;

	if(!$_insert) {
		if(!$this->beforeInputControls("add_domain_db",[])) return false;

		$inputparams=[
			'domainname',
			['panelusername','lefttext'=>'Panel username'],
			['paneluserpass','password_with_generate','lefttext'=>'Paneluser password'],
			['ftpusername','lefttext'=>'Ftp username'],
			['ftppassword','password_with_generate','lefttext'=>'Ftp Password'],

			['dbname','lefttext'=>'New DB name'],
			['dbusername','lefttext'=>'db username'],
			['dbuserpass','password_with_generate','lefttext'=>'db user Password'],

			['quota','default'=>'200','lefttext'=>"Quota (Mb)"],
			['upload','default'=>'200','lefttext'=>"Upload bw(kb/s)"],
			['download','default'=>'200','lefttext'=>"Download bw(kb/s)"],
			['email','default'=>$this->miscconfig['adminemail']],
			['timeout','lefttext'=>"Timeout Date", 'righttext'=>'(2019-mm-dd) Date at which domain will be deactivated. Leave blank to disable'],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.="<br>Normally, do not write www. only yourdom.com forexample: <br>"
		.inputform5($inputparams);
		/* inputform4($action,array('',"Panel username","Panel user password:","Ftp username","ftp Password","Quota (Mb)","Upload bw(kb/s)","Download bw(kb/s)",''),
		array("domainname","panelusername",array("paneluserpass","password"),"ftpusername",array("ftppassword","password"),"quota","upload","download",'email'),
		array('','','','','',"200","200","200"),array("op","_insert"),array("adddomain","1"));
		*/

	} else {
		if(!$this->afterInputControls("add_domain_db",
				[
				"domainname"=>$domainname,
				"ftpusername"=>$ftpusername,
				"panelusername"=>$panelusername,
				"dbname"=>$dbname,
				"dbusername"=>$dbusername
				])
			) return false;

		$success=$success && $this->addDomainDirect($domainname,$panelusername,$paneluserpass,$ftpusername,$ftppassword,$this->status_active,$email,$quota, '', compact('timeout'));
		$success=$success && $this->addMysqlDbDirect(False, $domainname, $dbusername, $dbuserpass, "localhost", $dbname, $adduser=True);
		$success=$success && $this->setselecteddomain($domainname);
		$this->ok_err_text($success,'domain add complete','failed to add domain (adddomain)');
	}
	$this->showSimilarFunctions('domain');
	return $success;
}


function check_remote_mysql_connection($dbhost){
	if(is_object($this->connected_mysql_servers[$dbhost])) return $this->connected_mysql_servers[$dbhost];
	#$conn=$this->
	# **** to be completed later
	return True;
}

function multiserver_add_domain_direct($named_params){
	extract($named_params); # produces variables with dnsservers, webservers and so on.  from now on, I plan to use named_params
	# opposite: compact($vars...

	# $dnsservers,$webservers,$mailserver,$mysqlservers,$domainname,$panelusername,$paneluserpass,$ftpusername,$ftppassword,$status,$email='',$quota=0
	# dnsservers: dns will be defined here
	# webservers: ftp also will be setup here, apache configs will be
	# mailserver: mail settings will be done in that server
	# mysqlservers: already done, default mysqlservers for that domain will be this. if multiple, user will be able to select one

	# tum kodlar, server ipler dahil edilecek sekilde yeniden tasarlanacak.
	if($webservers<>'localhost') $this->check_remote_mysql_connection($webservers);

	$domainname=$this->adjustDomainname($domainname);
	$panelusername=trim($panelusername);
	$ftpusername=trim($ftpusername);


	if(!$this->afterInputControls("adddomain",
			[
			"domainname"=>$domainname,
			"ftpusername"=>$ftpusername,
			"panelusername"=>$panelusername
			]
		)
	) return false;

	$this->output.=__FUNCTION__.": Adding domain: $domainname";
	$this->debugecho(__FUNCTION__.":". print_r2($named_params),3,false);


	$homedir=$this->conf['vhosts']."/$ftpusername/$domainname";
	$success=True;

	# server ipler eklenecek.
	$s=$this->executeQuery("insert into ".$this->conf['domainstable']['tablename']." (reseller,panelusername,domainname,homedir,status,diskquota,dnsserverips,webserverips,mailserverips,mysqlserverips) values ('".$this->activeuser."','$panelusername','$domainname','$homedir','$status',$quota,'$dnsserverips','$webserverips','$mailserverips','$mysqlserverips')",'domain add to ehcp db');

	list($ftpserver)=explode(',',$webserverips); # take first ip/localhost from list of webservers: ftp is only setup on first of webservers. other webservers(if any) should update files itself, by nfs/nas or other means.

	#$success=$success && ($s=$this->addDaemonOp("daemondomain","multiserver_add_domain",$domainname,$homedir,'domain info&ftp'));# since, adddaemonop only returns True or false, this construct is True, but above, execute may return other thing...

	$this->debugecho(__FUNCTION__.": webserverips:($webserverips) ftpserver:($ftpserver)",3,false);

	$success=$success && $this->add_daemon_op(['op'=>'daemondomain','action'=>'multiserver_add_domain','info'=>$domainname,'info2'=>$homedir,'info3'=>$ftpserver]); # domain initial directory settings are done in one of webservers, which sees same hdd space as others.
	$success=$success && $this->add_daemon_op(['op'=>'new_sync_all']);
	$success=$success && $this->addPanelUserDirect($panelusername,$paneluserpass,1,5,0,1,1,$quota,'',$email,$status);


	# ftp ekleme: nereye eklemeli?
	# her??ey uza??a eklenirse, listelemek icin uza??a ba??lanmak gerekir.
	# lokale eklenirse, lokal ftp daemonda degisiklik gerekir, localdeki ftpaccounts tablosuna ekleyemiyorum, zira daemon where cumleciginde iki kolon desteklemiyor.
	# uzakta ftp nin ??al????mas?? i??in oraya da eklenmesi gerekir. ozaman hem lokale hem uza??a eklenmeli sanki. ozaman silerken/g??ncellerken her iki yerden silinmeli v.b.

	$home=$this->conf['vhosts']."/$ftpusername";
	$type='default';
	$is_special_home=false;
	$success=$success && ($s=$this->multiserver_add_ftp_user_direct(compact('ftpserver','panelusername','ftpusername','ftppassword','home','upload','download','quota','domainname','type','is_special_home','status')));

	$sayi=$this->recordcount($this->conf['domainstable']['tablename'],'');
	$msg=$this->myversion."-ehcp($sayi): $domainname domain set up in your ehcp panel at ".$this->conf['dnsip']."  ";
	$msg.=$this->url;

	$msguser=$this->myversion."-ehcp: $domainname domain set up in ehcp panel at http://".$this->conf['dnsip']." Your panelusername: $panelusername, Password: $paneluserpass, ftpusername: $ftpusername, ftp pass: $ftppassword ";
	$subj=$msg;
	$this->infotoadminemail($msg,$subj);
	if($email<>'') mail($email,"Your domain $domainname set up in ehcp panel", $msguser,"From: ".$this->conf['adminemail']);

	return $this->ok_err_text($success,"All ops successfull. Adding domain complete..: $domainname",
		"Some domain operations are not successfull. Retry again, after deleting old data.");
}

function multiserver_add_ftp_user_direct($named_params){
	extract($named_params);
	$this->debugecho(__FUNCTION__.":".__LINE__.":".print_r2($named_params),3,false);

	if($ftpserver=='') $named_params['ftpserver']=$ftpserver='localhost';
	if($ftpserver=='localhost') return $this->add_ftp_user_direct($named_params); # call local ftp function if server is localhost
	# equivalent: if($ftpserver=='localhost') return $this->addFtpUserDirect($panelusername,$panelusername,$ftpusername,$ftppassword,$home,$upload,$download,$quota,$domainname,$type,$is_special_home,$status));



	# rest is for remote
	if($status=='') $status=$this->status_active; # default is active,
	$this->debugecho("$panelusername,$ftpusername,$ftppassword,$home,$upload,$download,$quota,$domainname,$type,$isSpecialHome",1,false);
	$panelusername=trim($panelusername);
	$ftpusername=trim($ftpusername);
	if($isSpecialHome) $homedir=$home; # home dir is only inserted if different from default of ".$this->vhostsdir."/ftphome....

	# uzak   server baglan  ekle
	# remote server connect add
	$p=[
		'dbhost'=>$ftpserver,
		'dbusername'=>$this->dbusername,
		'dbpass'=>$this->dbpass,
		'dbname'=>$this->dbname
	];

	$this->debugecho(__FUNCTION__.":".__LINE__.":".print_r2($p),3,false);

	$success=True;

	$success=$success && $uzak_conn=$this->connect_to_mysql($p);  # use same settings as this server.
	$qu="INSERT INTO ftpaccounts ( reseller, panelusername, domainname, ftpusername, password, homedir, type,status,datetime)	VALUES ('".$this->activeuser."','$panelusername','$domainname','$ftpusername', password('$ftppassword'),'$homedir','$type','$status',now())";
	$success=$success && $this->executeQuery($qu,'add ftp user : '.$ftpusername,__FUNCTION__,$uzak_conn);
	$success=$success && $this->addDaemonOp('daemonftp','multiserver_add',$home,$ftpserver);

	return $success;


}

function add_ftp_user_direct($named_params){
	# difference from addFtpUserDirect: uses named_params, my new coding style, I hope will be successful.
	# only calls old function using old style, until all code cleaned and tested enaugh.
	extract($named_params);
	return $this->addFtpUserDirect($panelusername,$panelusername,$ftpusername,$ftppassword,$home,$upload,$download,$quota,$domainname,$type,$is_special_home,$status);
}

function addDomainDirect($domainname,$panelusername,$paneluserpass,$ftpusername,$ftppassword,$status,$email='',$quota=0,$webserverips='', $named_params=[]){
	extract($named_params);
	$timeout=trim($timeout); # adjust timeout value. 
	if ($timeout=="") {
		$timeout="null";
	} else {
		$timeout="'$timeout'";		
	}


	$domainname=$this->adjustDomainname($domainname);
	$panelusername=trim($panelusername);
	$ftpusername=trim($ftpusername);


	if(!$this->afterInputControls("adddomain",
			[
			"domainname"=>$domainname,
			"ftpusername"=>$ftpusername,
			"panelusername"=>$panelusername
			]
		)
	) return false;


/*
domain path will be like: /var/www/vhosts/ftpusername/domain.com
/var/www/vhosts/ftpusername/domain.com will be stored as homedir in domains table,
one user will may have multiple domains with single ftp acount.
to achive this, i must implement domain add  to an existing ftp acount.
*/
	$this->output.="Adding domain: $domainname";
	$homedir=$this->conf['vhosts']."/$ftpusername/$domainname";
	$success=True;

	if($webserverips=='' and $this->miscconfig['activewebserverip']<>'') $webserverips=$this->miscconfig['activewebserverip']; # another ip is active for webserver

	$q="insert into domains (reseller,panelusername,domainname,homedir,status,diskquota,webserverips, timeout) values ('".$this->activeuser."','$panelusername','$domainname','$homedir','$status',$quota,'$webserverips', $timeout)";	
	$s=$this->executeQuery($q,'domain add to ehcp db'); # multiserver ayri bir fonksiyona yaz??ld??.
	#$success=$success && ($s=$this->addDaemonOp("syncdomains",'xx','','','sync apache ')); # sync'ing of all domains disabled upon each add domain, because it is time consuming for large domains&files, complains occured from users. only newly added domain is sync'ed in "add daemondomain"
	$success=$success && ($s=$this->addFtpUserDirect($panelusername,$ftpusername,$ftppassword,$this->conf['vhosts']."/$ftpusername",$upload,$download,$quota,$domainname,'default',false,$status));
	$success=$success && ($s=$this->addPanelUserDirect($panelusername,$paneluserpass,1,5,0,1,1,$quota,'',$email,$status));
	$success=$success && ($s=$this->addDaemonOp("syncdns",'','','','sync dns'));
	$success=$success && ($s=$this->addDaemonOp("daemondomain","add",$domainname,$homedir,'domain info&ftp'));# since, adddaemonop only returns True or false, this construct is True, but above, execute may return other thing...

	$sayi=$this->recordcount($this->conf['domainstable']['tablename'],'');
	$msg=$this->myversion."-ehcp($sayi): $domainname domain set up in your ehcp panel at ".$this->conf['dnsip']."  ";
	$msg.=$this->url;

	$msguser=$this->myversion."-ehcp: $domainname * domain set up in ehcp panel at http://".$this->conf['dnsip']." Your panelusername: $panelusername, Password: $paneluserpass, ftpusername: $ftpusername, ftp pass: $ftppassword  Ftp/Port: 2121  Timeout(if any): ($timeout). {$this->miscconfig['messagetonewuser']}      This info sent to email also:($email)";
	$subj=$msg;
	$this->infotoadminemail($msg,$subj);
	if($email<>'') mail($email,"Your domain $domainname set up in ehcp panel", $msguser,"From: ".$this->conf['adminemail']);

	$this->output.="<hr>Email sent to user:<hr>$msguser<hr>";

	return $this->ok_err_text($success,"All ops successfull. Adding domain complete..: $domainname",
		"Some domain operations are not successfull. Retry again, after deleting old data.");
	# rollback of operations when not succeeded, is not implemented. it just displays an error message... to be fixed later..

}

function adjustDomainname($domainname){ # if user enters www. at start of domain, it is trimmed, since www. is already added in necessarry ehcp operations,  otherwise, it becomes, www.www.domainname...
	$domainname=trim($domainname);
	if(substr($domainname,0,4)=='www.') $domainname=substr($domainname,4);
	# $this->noEmpty($domainname); # removed because, that control is done, and should be done in afterInputControls. afterInputControls should be single point to check for input errors.
	return $domainname;
}

function addDomainDirectToThisPaneluser($domainname,$ftpusername,$sync=True){
	$domainname=$this->adjustDomainname($domainname);

	if(!$this->afterInputControls("adddomaintothispaneluser",
			[
			"domainname"=>$domainname,
			]
		)
	) return false;

	$success=True;

/*
domain path will be like: /var/www/vhosts/ftpusername/domain.com
/var/www/vhosts/ftpusername/domain.com will be stored as homedir in domains table,
one user will may have multiple domains with single ftp acount.
to achive this, i must implement domain add  to an existing ftp acount.
*/

	$this->output.="Adding domain: $domainname";
	$homedir=$this->conf['vhosts']."/$ftpusername/$domainname";

	#$webserver_template=$this->escape(file_get_contents("webserver_template")); # read default template from disk file, write to domain info, default template can also be read from a config..
	#$dnstemplate=$this->escape(file_get_contents("dnszonetemplate"));

	# *** Burada eklenirken, hangi ftp hesabina eklendigi yazilacak, ftpusername, bu sayede o ftp silinirken kontrol edilecek.
	# $success=$this->executeQuery("insert into ".$this->conf['domainstable']['tablename']." (reseller,panelusername,domainname,homedir,status) values ('".$this->activeuser."','".$this->activeuser."','$domainname','$homedir','$this->status_active')",'domain add to ehcp db');
	if($webserverips=='' and $this->miscconfig['activewebserverip']<>'') $webserverips=$this->miscconfig['activewebserverip']; # another ip is active for webserver

	$success=$this->executeQuery("insert into ".$this->conf['domainstable']['tablename']." (reseller,panelusername,domainname,homedir,status,apachetemplate,dnstemplate,webserverips) values ('".$this->activeuser."','".$this->activeuser."','$domainname','$homedir','$this->status_active','$apachetemplate','$dnstemplate','$webserverips')",'domain add to ehcp db');
	$success=$success && $this->addDaemonOp("daemondomain","add",$domainname,$homedir,'domain info&ftp');# since, adddaemonop only returns True or false, this construct is True, but above, execute may return other thing...

	if($sync){ # in multiple domain add, no sync is done for all domains one by one...  a single syncAll is called at end of all domains..
		#$success=$success && ($s=$this->addDaemonOp("syncdomains",'xx','','','sync apache '));
		$success=$success && ($s=$this->addDaemonOp("syncdns",'','','','sync dns'));
	}
	$success=$success && ($s=$this->addDaemonOp("syncdomains",'xx',$domainname,'','sync apache '));

	$sayi=$this->recordcount($this->conf['domainstable']['tablename'],'');
	$msg=$this->myversion."-ehcp($sayi): $domainname domain setup complete in your ehcp panel at ".$this->conf['dnsip']."  ";
	$msg.=$this->url."   Total domain count in this server: $sayi";

	$msguser="ehcp: $domainname domain setup complete in ehcp panel at http://".$this->conf['dnsip']." Your panelusername: $panelusername, Password: $paneluserpass, ftpusername: $ftpusername, ftp pass: $ftppassword  ";
	$subj=$msg;
	$this->infotoadminemail($msg,$subj);
		if($email<>'') mail($email,"Your domain $domainname setup complete in ehcp panel", $msguser,"From: ".$this->conf['adminemail']);

	return $this->ok_err_text($success,"All ops successfull. Adding domain complete..: $domainname",
		"Some domain operations are not successfull. Retry again, after deleting old data.");
	# rollback of operations when not succeeded, is not implemented. it just displays an error message... to be fixed later..
}


function deleteDomain(){
	global $domainname,$confirm,$confirm2; # gets domainname from _GET
	$this->getVariable(["domainname","confirm",'confirm2']);
	$this->requireNoDemo();
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);

	if(!$this->isuserpermited('deletedomain',$domainname)) return false; # is this reseller of that domain ?


	$domaininfo=$this->domaininfo=$this->getDomainInfo($domainname);

	#$domainpaneluser=$this->getField($this->conf['domainstable']['tablename'],"panelusername","domainname='$domainname'");
	$domainpaneluser=$domaininfo['panelusername'];
	$homedir=$domaininfo['homedir'];

	$panelusercount=$this->recordcount($this->conf['domainstable']['tablename'],"panelusername='$domainpaneluser' and domainname<>'$domainname'");
	if($domainpaneluser==$this->activeuser) $panelusercount=1; # dont delete your self acount...


	# is there any other domain related to this paneluser ? if not, delete panel user too
	if($domaininfo['reseller']<>$this->activeuser and !$confirm2 ){
		$inputparams=[
			['domainname','hidden','default'=>$domainname],
			['confirm2','hidden','default'=>'1'],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.="<br><b>This domain belongs to (".$domaininfo['reseller'].") reseller, are you sure to delete?</b><br>"
		.inputform5($inputparams);

		#inputform4($action,[],[],$deger,array("op","domainname","confirm2"),array("deletedomain",$domainname,"1"));
		return True;
	}

	if(!$confirm){
		$filter="domainname='$domainname'";
		$this->output.= $this->sayinmylang("areyousuretodelete").$domainname." Email/ftp users: <br> ";
		#$this->output.="$domainname domain ftp user List: ".$this->tablolistele3_5_4($this->conf['ftpuserstable']['tablename'],$baslik,array("ftpusername","domainname"),$filter,$sirala,$linkimages,$linkfiles,$linkfield,$listrowstart,$listrowcount,false);
		$domaininfo=$this->getDomainInfo($domainname);
		$this->output.="ftp username:".$this->multiserver_get_domain_ftpusername($domaininfo)."<br>";

		$this->output.="<br> $domainname domain email user List: ".$this->tablolistele3_5_4($this->conf['emailuserstable']['tablename'],$baslik,$this->conf['emailuserstable']['listfields'],$filter,$sirala,$linkimages,$linkfiles,$linkfield,$listrowstart,$listrowcount,false);
		$this->output.="<br>Domain File Count:".executeprog("ls -l ".$this->conf['vhosts']."/$domainname/httpdocs | wc -l");

		$this->listTable($this->tr('subdomains_related_to_this_domain')."$domainname", 'subdomainstable', "domainname='$domainname'");

		if($panelusercount==0) $this->output.="<br>Panel user to be deleted:".$domainpaneluser;
		$this->output.="<br><br>Databases related to this domain:".$this->tablolistele3_5_4($this->conf['mysqldbstable']['tablename'],$baslik,["dbname"],$filter,$sirala,$linkimages,$linkfiles,$linkfield,$listrowstart,$listrowcount,false);
		$this->output.="<br> Email Forwardings:".$this->tablolistele3_5_4($this->conf['emailforwardingstable']['tablename'],$baslik,$this->conf['emailforwardingstable']['listfields'],$filter,$sirala,$linkimages,$linkfiles,$linkfield,$listrowstart,$listrowcount,false);

		$inputparams=[
			['domainname','hidden','default'=>$domainname],
			['confirm','hidden','default'=>'1'],
			['confirm2','hidden','default'=>'1'],
			['op','hidden','default'=>__FUNCTION__]
		];


		$this->output.="<br><font size=+1><b>Are you sure to delete all these?</b></font><br>"
		.inputform5($inputparams);

		#inputform4($action,[],[],$deger,array("op","domainname","confirm","confirm2"),array("deletedomain",$domainname,"1",'1'));

		return True;
	}

	$success=$this->deleteDomainDirect($domainname);
	$this->output.="<br><a href='?'>Goto Home</a><br>";
	$this->deselectdomain2();# deselect domain, if any...
	$ret=$this->ok_err_text($success,"All ops successfull. Delete domain complete: $domainname","failed some ops while deleting domain (deletedomain) ");

	$this->showSimilarFunctions('domain');
	return $ret;

}

function multiserver_delete_ftp_direct($domaininfo){
	# to be coded.
	# burda t??m domain degil,sadece ftpaccount silinmeli.
	# delete ftp account that is located on remote , maybe also local in future.

	$domainname=trim($domaininfo['domainname']);
	$webserverips=trim($domaininfo['webserverips']);
	$ftpusername=trim($domaininfo['ftpusername']);
	$ftpserver=trim($domaininfo['ftpserver']);

	$this->debugecho(__FUNCTION__.":".print_r2($domaininfo)." domain:$domainname, $webserverips, $ftpusername ",3,false);

	if($domainname=='' or $ftpusername=='') {
		$this->output.=__FUNCTION__.": domainname or ftpusername is empty.returning.";
		return True;
	}

	$this->output.="<br>Deleting ftp user: $ftpusername<br>";

	$success=True;

	$qu="delete from ftpaccounts where ftpusername='$ftpusername'";
	$success=$success && $this->multiserver_executequery($qu,$ftpserver);

	$qu="delete from ftpaccounts where ftpusername='$ftpusername' and domainname='$domainname'"; # delete from local mysql, if there is any record (until all code is clean)
	$success=$success && $this->multiserver_executequery($qu,'localhost');

	# burada bitek remote daemon kald??.
	$homedir=$this->multiserver_getfield($this->conf['ftpuserstable']['tablename'], "homedir", "ftpusername='$ftpusername' limit 1",$ftpserver);
	if($homedir=="") {
		$homedir=$this->conf['vhosts']."/$ftpusername";
	}

	$success=$success && ($s=$this->add_daemon_op(['op'=>'daemonftp','action'=>'delete','info'=>"/etc/vsftpd_user_conf/$ftpusername",'info3'=>$ftpserver]));
	$success=$success && ($s=$this->add_daemon_op(['op'=>'daemonftp','action'=>'delete','info'=>$homedir,'info3'=>$ftpserver]));

	return $success;

}

function multiserver_query($q,$serverip,$logininfo=false){
	if(is_array($serverip)) $serverip=$serverip['ip'];  # accept both until all code is standard.

	$this->debugecho(__FUNCTION__.': server:'.$serverip.' logininfo:'.print_r2($logininfo),3,false);


	if($serverip=='localhost') return $this->query($q);
	else {
		if(!$logininfo)
		$logininfo=[
			'dbhost'=>$serverip,
			'dbusername'=>$this->dbusername,
			'dbpass'=>$this->dbpass,
			'dbname'=>$this->dbname
		];

		$uzak_conn=$this->connect_to_mysql($logininfo);
		return $this->query3($q,'',__FUNCTION__,$uzak_conn);
	}
}

function multiserver_executequery($q,$serverip,$logininfo=false){
	if(is_array($serverip)) $serverip=$serverip['ip'];  # accept both until all code is standard.
	$this->debugecho(__FUNCTION__.': server:'.$serverip.' logininfo:'.print_r2($logininfo),3,false);

	if($serverip=='localhost') return $this->executeQuery($q);
	else {
		if(!$logininfo)
		$logininfo=[
			'dbhost'=>$serverip,
			'dbusername'=>$this->dbusername,
			'dbpass'=>$this->dbpass,
			'dbname'=>$this->dbname
		];

		$uzak_conn=$this->connect_to_mysql($logininfo);
		return $this->executeQuery($q,'',__FUNCTION__,$uzak_conn);
	}
}

function multiserver_get_domain_ftpusername($domaininfo){
	$q="select ftpusername from ".$this->conf['ftpuserstable']['tablename']." where domainname='".$domaininfo['domainname']."'";
	$res=$this->multiserver_query($q,$domaininfo['ftpserver']);
	return trim($res[0]['ftpusername']);
}

function deleteDomainDirect($domainname,$syncdomains=True){
	$this->requireNoDemo();
	$domainname=trim($domainname);
	if($domainname=='') return false;

	if(!$this->isuserpermited('deletedomain',$domainname)) return false; # is this reseller of that domain ?

	$domaininfo=$this->domaininfo=$this->getDomainInfo($domainname);
	$this->last_deleted_domaininfo=$domaininfo;  # used for rebuilding configs of servers of deleted domain . otherwise, the config of that server is not updated, resulting failure.

	#$domainpaneluser=$this->getField($this->conf['domainstable']['tablename'],"panelusername","domainname='$domainname'");
	$domainpaneluser=$domaininfo['panelusername'];
	$homedir=$domaininfo['homedir'];

	$panelusercount=$this->recordcount($this->conf['domainstable']['tablename'],"panelusername='$domainpaneluser' and domainname<>'$domainname'");
	if($domainpaneluser==$this->activeuser) $panelusercount=1; # dont delete your self acount...

	# is there any other domain related to this paneluser ? if not, delete panel user too

	if($domaininfo['reseller']<>$this->activeuser){ # inform domain reseller, if this is not  yours..
		$reseller=$this->query("select * from ".$this->conf['paneluserstable']['tablename']." where panelusername='".$domaininfo['reseller']."'");
		mail($reseller[0]['email'],"ehcp-Your domain $domainname deleted","$domainname deleted from ehcp at ".$this->conf['dnsip']);
	}

	if($domaininfo['reseller']=='ehcp'){
		# perform additional task if this is ehcp account,
	}


	$domfilt=" where domainname='$domainname'";
	$ftpusername=$this->multiserver_get_domain_ftpusername($domaininfo);
	$this->output.="Deleting domain: $domainname (ftpusername:$ftpusername)<br>";

	$success=True;
	$success=$success && $s=$this->executeQuery("delete from ".$this->conf['domainstable']['tablename'].$domfilt." limit 1",'Deleting domain from ehcp db');
	$success=$success && $s=$this->executeQuery("delete from ".$this->conf['emailuserstable']['tablename'].$domfilt,'Deleting emails from ehcp db');
	$success=$success && $s=$this->executeQuery("delete from ".$this->conf['emailforwardingstable']['tablename'].$domfilt,'Deleting email forwardings from ehcp db');
	$success=$success && $s=$this->executeQuery("delete from ".$this->conf['subdomainstable']['tablename'].$domfilt,'delete subdomains of domain');
	$success=$success && $s=$this->executeQuery("delete from ".$this->conf['customstable']['tablename'].$domfilt,'delete custom http/dns settigns of domain');

	if($panelusercount==0 and $domaininfo['reseller']<>'ehcp') {# if no other domain left with this panel user, then delete panal user too, except for ehcp special reseller account
		$success=$success && $s=$this->executeQuery("delete from ".$this->conf['paneluserstable']['tablename']." where reseller='".$this->activeuser."' and panelusername='$domainpaneluser'",'Deleting domain paneluser info from ehcp db '.$domainpaneluser);
	}


/*
	if($domaininfo['ftpserver']=='localhost') $this->deleteFtpUserDirect($ftpusername);
	else {
*/
		$domaininfo['ftpusername']=	$ftpusername;
		if($ftpusername<>'') $this->multiserver_delete_ftp_direct($domaininfo); # code for multiple servers.
		/* this code should be enaugh for local too.
		 * I should do same style for

	}*/

	$success=$success && ($s=$this->add_daemon_op(['op'=>'daemondomain','action'=>'delete','info'=>$domainname,'info2'=>$homedir,'info3'=>$ftpserver]));
	#old: $success=$success && $s=$this->addDaemonOp("daemondomain","delete",$domainname,$homedir,'delete domain');

	if($syncdomains){  # buras?? new_sync_domains v.b. olmal??.
		#$success=$success && $s=$this->addDaemonOp("syncdomains",'xx','','','sync domains');
		#$success=$success && $s=$this->addDaemonOp("syncdns",'','','','sync dns');
		$success=$success && $this->add_daemon_op(['op'=>'new_sync_all']);
	}

	#domain ile ilgili t??m dbleri sil..
	$ret=$this->query("select id from ".$this->conf['mysqldbstable']['tablename'].$domfilt);
	foreach($ret as $d) $success=$success && $this->deleteDB($d['id']);

	return $success;

}

function deselectdomain2(){ # de-selects a domain
	$this->setselecteddomain('',false);
}

function deselectdomain(){ # de-selects a domain and displays home
	$this->deselectdomain2();
	$this->displayHome();
	return True;
}


function setselecteddomain($dom,$checkdomain=True){ # only sets selecteddomain..
	if($checkdomain){
		if(!$this->exist($this->conf['domainstable']['tablename'],"domainname='$dom'")) return false; # if there is no such domain, dont set it..
		$this->requireMyDomain($dom);
	}

	$_SESSION['selecteddomain']=$dom;
	$this->selecteddomain=$dom;
	return True;
}

# i split some operations to different functions to be understandable...
# choosing a domain is possible in many ways, these functions do this..

function chooseDomainGoNextOp(){ # sets selected domain, then redirect to new op..
	global $domainname,$nextop;
	$this->getVariable(["domainname","nextop"]);
	$this->requireMyDomain($domainname);
	$this->setselecteddomain($domainname);
	$this->redirecttourl("?op=$nextop");
}

function chooseDomain2($opname){
	# displays list of domains available, then let user choose one, then goes to next op..

	$mydomains=$this->getMyDomains();
	if(!$mydomains) $this->output.="<br>You have no domains yet. Add domain first, then use this function.";
	else $this->output.="<br><br>Select A Domain:".$this->listSelector($arr=$mydomains,$print=['domainname'],$link="?op=choosedomaingonextop&nextop=$opname&domainname=",$linfield='domainname');
	return True;
}

function listselectdomain(){
	$this->chooseDomain2('');
	$this->showSimilarFunctions('domain');
}

function chooseDomain($opname,$dom=''){
	# gives previously selected domain, or domain entered from url... or if nothing exists, shows a list of domains, let user select it...

	#$this->debugtext(__FUNCTION__."called.. dom: $dom, this->selecteddomain: $this->selecteddomain");

	if($dom<>'') { # is there anything passed in parameter or entered in url ?
		$domainname=$dom;
		$this->setselecteddomain($domainname);
	}

	if(!$domainname) $domainname=$this->selecteddomain; # is there an already selected domain ?
	if(!$domainname) {
		$this->chooseDomain2($opname); # if none, let user select domain..
		$this->showexit();
	}

	$this->requireMyDomain($domainname); # ensure this is my domain..

	return $domainname;
}

function showexit($template=''){
	$this->debugecho2("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__,4);
	$this->show($template);
	exit;
}

function addNewScript(){
	global $name,$url,$scriptdirtocopy,$homepage,$description;
	$this->getVariable(['name','url','scriptdirtocopy','homepage','description']);

	$this->requireAdmin();

	if(!$url){
		$inputparams=['name','url','scriptdirtocopy','homepage',
			['description','textarea'],
			['op','hidden','default'=>__FUNCTION__]
		];
		$this->output.=inputform5($inputparams);

	} else {
		$q="insert into scripts (scriptname,filetype,fileinfo,scriptdirtocopy,homepage,description) values ('$name','directurl','$url','$scriptdirtocopy','$homepage','$description')";
		$success=$this->executeQuery($q);

		$msg="ehcp - New script added: name:$name, url:$url, homepage:$homepage, description:$description ";
		$this->infotoadminemail($msg, $msg,True);
		return $this->ok_err_text($success," Added/Defined new script successfully.",'');
	}


}

function doDownloadAllscripts(){
	$this->requireAdmin();
	$this->adddaemonop('downloadallscripts',$action,$info);
}

function download_file(){
	# gui side of function
	# function to download a file directly from internet to server and extract it
	# only enabled for special_users, because dangerous if some users try to download very large files.
	# daemon mode not coded yet

	$this->require_special_user();
	$alanlar=["domainname","downloadfile",'directory','extractit'];
	foreach($alanlar as $al) global ${$al};
	$degerler=$this->getVariable($alanlar);

	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);

	if(!$downloadfile){
		$inputparams=[
			"downloadfile",
			"directory",
			['extractit','radio','secenekler'=>['yes','no']],
			['domainname','hidden','default'=>$domainname],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.="Enter download file info:".inputform5($inputparams);

	} else {
		$this->addDaemonOp("daemon_download_file", $downloadfile, $domainname, $directory, $opname);
		$this->output.="Download will start soon.";
	}
}

function addScript(){
	$alanlar=["domainname","scriptname",'directory','iamsure','dbname','dbusername','dbuserpass'];
	foreach($alanlar as $al) global ${$al};
	$degerler=$this->getVariable($alanlar);

	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);

	if(!$scriptname){
		$linkimages=["images/install.jpg"];
		$linkfiles=["?op=addscript&domainname=$domainname"];
		$linkfield='scriptname';
		$filter='';
		$this->output.="Select script to install: <br>(Please note that, some script url's may be out of date in future. So, in that case, you may be unable to install these. In that case, update list of these scripts from ehcp.net site, or manually fix that in phpmyadmin)<br>".$this->tablolistele3_5_4("scripts",$baslik,["scriptname",['homepage','link_newwindow'],'description'],$filter,$sirala,$linkimages,$linkfiles,$linkfield,$listrowstart,$listrowcount=100)
		  ."<hr><a href='?op=suggestnewscript'>Suggest new script for easy install ..</a> <br><br>
		  <a href=http://www.ehcp.net/scriptsupdate.sql>Update list of softwares for easy install</a><br><br>
		  <a href='?op=addNewScript'>Add/Define New Script</a> (Admin only)<br>
		  <a href='?op=doDownloadAllscripts'>Download all scripts (Admin only)</a>
		  ";
		return True;
	}

	if ((strpos($scriptname,"ehcp itself")!==false)or(strpos($scriptname,"ehcp webmail")!==false)) {
		if(!$this->isadmin()) $this->errorTextExit("only admins can install ehcp or ehcp webmail into some dir. normally, you should be able to access webmail as: http://webmail.yourdomain.com ");
		$this->output.="<br><b>Note that, by copying ehcp or webmail files, you put your sensitive ehcp files in that domain/dir. so, be careful</b>. Also note that, only ehcp web gui will work from this new place; daemon will continue to work from original place.<br>";
	}

	if(!$directory and !$iamsure){
		$inputparams=[
			"directory",
			['iamsure','checkbox','lefttext'=>'Check here if you are sure to install to root of your domain','secenekyazisi'=>'iamsure','default'=>'1'],
			['op','hidden','default'=>'addscript'],
			['domainname','hidden','default'=>$domainname],
			['scriptname','hidden','default'=>$scriptname],
			['comment','comment','default'=>'<br><hr>Below is for adding a new database&dbuser in this single step, for your app/script. You may use that db&user to setup that app/script. Leave empty to skip db add/setup.'],
			['dbname','righttext'=>'leave empty to skip'],
			['dbusername','righttext'=>'leave empty to skip'],
			['dbuserpass','password_with_generate','righttext'=>'leave empty to skip'],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.="Enter directory (under your domain, below httpdocs) to install the script into: <br>For example, enter only 'test' to install script into: http://www.$domainname/test <br>
		The direcotry will be automatically added.. <br>
		Leave empty (and check checkbox below) to install into root of your domain, to http://www.$domainname, <b>in this case, some of your files may be overwritten ! </b><br>"
		  .inputform5($inputparams);	 # this is a new function, different from inputform4, this uses a single array containing input elements and their attributes...

	   return True;
	}

	$success=True;
	$str="Will copy/add script <b>[$scriptname]</b> to domain: <b>[$domainname]</b> directory: <b>[$directory]</b> <br><br>Click <big><a target=_blank href=http://www.$domainname/$directory>here</a></big> to see that dir after a 30-60 seconds....<br>You may tail -f /var/log/ehcp.log for script operations...<br><br>";
	$this->output.=$str;

	$this->addDaemonOp("installscript", $scriptname, $domainname, $directory, $opname);

	$q="select * from scripts where scriptname='$scriptname'";
	$bilgi=$this->query($q);
	$bilgi=$bilgi[0];
	if($dbname and $dbusername and $dbuserpass) {
		$success=$success && $this->addMysqlDbDirect($myserver,$domainname,$dbusername,$dbuserpass,$dbuserhost,$dbname,$adduser=True);
	}

	# buras?? asl??nda ??nemli, kurulan scriptleri daha d??zenli yapmak laz??m.
	$this->showSimilarFunctions('mysql');
	$this->infotoadminemail($str,"ehcp - script installed - $this->myversion, $this->dnsip - $scriptname, $domainname, ".print_r($bilgi,True));

}


/*
function addEmailUser(){
	global $domainname,$mailusername,$password,$quota,$autoreplysubject,$autoreplymessage;
	$this->getVariable(array('domainname','mailusername','password','quota','autoreplysubject','autoreplymessage')); # this gets variables from _GET or _POST
	if($this->isuserlimitexceeded('maxemails')) return false;
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$success=True;

	if(!$mailusername){
		if(!$this->beforeInputControls("addemail",[])) return false;
		$inputparams=array(
			array('mailusername','righttext'=>"@$domainname"),
			array('quota','lefttext'=>'Quota (Mb)','default'=>'10'),
			array('password','password_with_generate'),
			array('autoreplysubject','default'=>$autoreplysubject,'righttext'=>'Leave emtpy to disable autoreply'),
			array('autoreplymessage','textarea','default'=>$autoreplymessage),
			array('op','hidden','default'=>__FUNCTION__)
		);

		$this->output.="Adding domain/email user for domain: $domainname <br>(do not write @domainname)<br>"
		.inputform5($inputparams);
		#.inputform4($action,array('',"password","Quota (Mb)"),array("mailusername",array("password","password"),"quota"),array('','',"10"),array("op"),array("addemailuser"));
	} else{
		$success=$this->addEmailDirect($mailusername,$domainname,$password,$quota,$autoreplysubject,$autoreplymessage);
	}
	$this->showSimilarFunctions('email');
	return $success;
}
*/

# Not used yet... 
function build_email_domains_select($domains){
	foreach($domains as $dom){
		$select_domainname.= "<select name='domainname'><option value='$dom'>$dom</option>";
	}
	return $select_domainname;	
}


function build_email_domains($domainname){
	$select_domainname = "<select name='domainname'><option value='$domainname' selected='selected'>$domainname</option>";

	$query = "select subdomain from subdomains where domainname = '$domainname'";
	$res = $this->query($query);
	if(!empty($res)) {
		foreach($res as $row){
			$select_domainname .= "<option value='".$row['subdomain'].".$domainname'>".$row['subdomain'].".$domainname</option>";
		}
	}

	# For aliases also, can be an email added.. not yet complete.. 
	$query = "select aliases from domains where domainname = '$domainname' limit 1";
	$res = $this->query($query);
	if(!empty($res)) {
		foreach($res as $row){ # actually one result				
			$arr=$this->strNewlineToArray($row['aliases']);
			foreach ($arr as $alias) {
				$select_domainname .= "<option value='$alias'>$alias</option>";
			}
		}
	}
	$select_domainname .= '</select>';
	return $select_domainname;
}

function addEmailUser(){
	#  modified upon suggestion of sextasy@discardmail.com, thanks, nice contribution.

   global $domainname,$mailusername,$password,$quota,$autoreplysubject,$autoreplymessage;
   $this->getVariable(['domainname','mailusername','password','quota','autoreplysubject','autoreplymessage']); # this gets variables from _GET or _POST
   if($this->isuserlimitexceeded('maxemails')) return false;
   $domainname=$this->chooseDomain(__FUNCTION__,$domainname);
   $success=True;

   if(!$mailusername){
		if(!$this->beforeInputControls("addemail",[])) return false;
		
		$select_domainname = $this->build_email_domains($domainname);
		$inputparams=[
			['mailusername','righttext'=>"@".$select_domainname],
			['quota','lefttext'=>'Quota (Mb)','default'=>'25'],
			['password','password_with_generate'],
			['autoreplysubject','default'=>$autoreplysubject,'righttext'=>'Leave emtpy to disable autoreply'],
			['autoreplymessage','textarea','default'=>$autoreplymessage],
			['timeout','lefttext'=>"Timeout Date", 'righttext'=>'(2019-mm-dd) Date at which email will be deactivated. Leave blank to disable'],
			['op','hidden','default'=>__FUNCTION__]
	   ];

	   $this->output.="Adding domain/email user for domain: $domainname <br>(do not write @domainname)<br>"
	   .inputform5($inputparams);
   } else{
		   $success=$this->addEmailDirect($mailusername,$domainname,$password,$quota,$autoreplysubject,$autoreplymessage);
   }
   $this->showSimilarFunctions('email');
   return $success;
}


function addEmailDirect($mailusername,$domainname,$password,$quota,$autoreplysubject,$autoreplymessage, $timeout=""){
		$success=True;
		$this->noEmpty($domainname);

		if(!$this->afterInputControls("addemail",['email'=>$mailusername.'@'.$domainname,'mailusername'=>$mailusername])) return false;

		$this->output.="Adding email user:";
		$mailusername=getFirstPart($mailusername,'@'); # make sure does not include @ sign
		$email="$mailusername@$domainname";
		if ($timeout=="") {
			$timeout="null";
		} else {
			$timeout="'$timeout'";		
		}
		$q="insert into emailusers (panelusername,domainname,mailusername,email,password,quota,status,autoreplysubject,autoreplymessage, timeout) values ('$this->activeuser','$domainname','$mailusername','$email',encrypt('$password','ehcp'),$quota,'".$this->status_active."','$autoreplysubject','$autoreplymessage', $timeout)";

		$success=$success && $this->executeQuery($q,'add mail user');
		#$success=$success && $this->executeQuery("insert into ".$this->conf['emailuserstable']['tablename']." (panelusername,domainname,mailusername,email,password,quota,status,autoreplysubject,autoreplymessage) values ('$this->activeuser','$domainname','$mailusername','$email',encrypt('$password','ehcp'),'".($quota*1000000)."','".$this->status_active."','$autoreplysubject','$autoreplymessage')",'add mail user');
		$this->adjustEmailAutoreply($email,$autoreplysubject,$autoreplymessage);


		mail($email,$this->sayinmylang('welcome to world'),$this->sayinmylang('welcome to world'),"From: ".$this->conf['adminemail']); # bir mail gonderiyor. zira yoksa dizin olusmuyor, pop3 login calismiyor..
		return $this->ok_err_text($success,"Added successfully.mail add complete: <br>",'failed to add mail');
}

function println($str) {
	return $this->echoln($str);
}

function echoln($str){
	if($this->commandline) echo $str."\n";
	else $this->output.="<br>$str";
}

function echoln2($str){
	if($this->commandline) echo $str."\n";
	else $this->output.="<br>$str<br>";
}


function addPanelUserDirect($panelusername,$password,$maxdomains=0,$maxemails=0,$maxpanelusers=0,$maxftpusers=0,$maxdbs=0,$quota=0,$name='',$email='',$status='active'){

	$panelusername=trim($panelusername);
	$reseller=$this->activeuser;
	if($reseller=='')$reseller='admin'; # in applyfordomain, activeuser is empty..

	$logintable=$this->conf['logintable'];

	if ($logintable['passwordfunction']=='') {
		$pass="'$password'";
	} elseif($logintable['passwordfunction']=='encrypt'){
		$pass="encrypt('$password','ehcp')";
	} else {
		$pass=$logintable['passwordfunction']."('$password')";
	}

	$query="insert into panelusers
	(panelusername,password,maxdomains,maxemails,maxpanelusers,maxftpusers,maxdbs,name,email,quota,reseller,status)
	values
	('$panelusername',$pass,$maxdomains,$maxemails,$maxpanelusers,$maxftpusers,$maxdbs,'$name','$email',$quota,'$reseller','$status')";

	$this->debugecho("<br>debug:query: $query <br>",1,false);

	if($email<>'') {
		$msguser="Your ehcp panel user set up: panelusername: $panelusername, pass: $password, server: http://". $this->conf['dnsip']."  \n".$this->miscconfig['messagetonewuser'];
		mail($email,"Your paneluser $panelusername setup complete in ehcp panel", $msguser,"From: ".$this->conf['adminemail']);
		$this->output.="<br>mail sent to paneluser..<br>";
	}

	return $this->executeQuery($query,'add panel user: '.$panelusername);
}

function addPanelUser(){

	$tb=$this->conf['paneluserstable'];
	foreach($tb['insertfields'] as $insertfield) {
		if(is_array($insertfield)) $insertfield=$insertfield[0];
		global ${$insertfield};
	}

	#global $panelusername,$password,$maxdomains,$maxemails,$maxpanelusers,$maxftpusers,$maxdbs,$quota;
	#$this->getVariable(array("panelusername","password","maxdomains","maxemails","maxpanelusers",'maxftpusers',"quota")); # this gets variables from _GET or _POST
	$ret=$this->getVariable($tb['insertfields']);
	$this->debugecho(print_r2($ret),2,false);

	if(!$this->beforeInputControls("addpaneluser")) return false;


	if(!$panelusername){
		#$this->output.="Adding panel user:<br>".inputform4($action, array('','',"maxdomains","maxemails","maxpanelusers",'maxftpusers','Max Mysql Databases',"Quota (Mb)"),array("panelusername","password","maxdomains","maxemails","maxpanelusers",'maxftpusers','maxdbs',"quota"),$deger,array("op"),array("addpaneluser"));
		$this->output.="Adding panel user/reseller:<br>"
			.inputform5ForTableConfig($tb,[['op','hidden','default'=>__FUNCTION__]]);
			# i tried to remove all old function inputform5, i switched to a newer one of inputform5, left old statements commented to checkout and to understand.
			#inputform4($action, $tb['insertfieldlabels'],$tb['insertfields'],$deger,array("op"),array("addpaneluser"));
	}else {
		if(!$this->afterInputControls("addpaneluser",['panelusername'=>$panelusername])) return false;
		$this->output.="Adding user:<br>";
		$success=$this->addPanelUserDirect($panelusername,$password,$maxdomains,$maxemails,$maxpanelusers,$maxftpusers,$maxdbs,$quota,$name,$email);
		$this->ok_err_text($success,"Added panel user successfully.",'failed to add panel user');
	}
	$this->showSimilarFunctions('panelusers');
	return $success;
}



function addFtpUserDirect($panelusername,$ftpusername,$password,$home,$upload,$download,$quota,$domainname='',$type='',$isSpecialHome=false,$status=''){
	$success=True;

	if($status=='') $status=$this->status_active; # default is active,
	# for pureftpd: $qu="INSERT INTO ftpd ( reseller, User , status , Password , Uid , Gid , Dir , ULBandwidth , DLBandwidth , comment , ipaccess , QuotaSize , QuotaFiles,domainname)	VALUES ('".$this->activeuser."','$ftpusername', '1', MD5( '$password' ) , '2001', '2001', '$home', '$upload', '$download', '', '*', '$quota', '0','$domainname');";
	if(strstr($home,$this->ehcpdir)!==false) return $this->errorText($this->ehcpdir." location cannot be used in ftps; this is for security purposes");

	$this->debugecho2("$panelusername,$ftpusername,$password,$home,$upload,$download,$quota,$domainname,$type,$isSpecialHome",1);
	$panelusername=trim($panelusername);
	if ($panelusername=="") {
		$this->echoln($this->tr('ftp_username_cannot_be_empty'));
		return False;
	}

	$ftpusername=trim($ftpusername);
	if($isSpecialHome) $homedir=$home; # home dir is only inserted if different from default of /var/www/vhosts/ftphome....
	$homedir=securefilename($homedir);

	$qu="INSERT INTO ftpaccounts ( reseller, panelusername, domainname, ftpusername, password, homedir, type,status)	VALUES ('".$this->activeuser."','$panelusername','$domainname','$ftpusername', password('$password'),'$homedir','$type','$status')";
	$success=$this->executeQuery($qu,'add ftp user : '.$ftpusername);
	$success=$success && $this->addDaemonOp('daemonftp','add',$home);
	$success=$success && $this->addDaemonOp("daemonftp","syncftp",'noempty','','sync/restart ftp');

	return $success;
}

function deleteFtpUserDirect($ftpusername,$deletefiles=True){
	# deletefiles option will be coded later in gui. (ehcpdeveloper)
	if(trim($ftpusername)=='') return True;
	$success=True;

	$homedir=$this->getField($this->conf['ftpuserstable']['tablename'], "homedir", "ftpusername='$ftpusername' limit 1");
	if($homedir=="") {
		$homedir=$this->conf['vhosts']."/$ftpusername";
	}
	$success=$success && $this->addDaemonOp("daemonftp","delete","/etc/vsftpd_user_conf/$ftpusername",''," ftp delete info for user specific config file(/etc/vsftpd_user_conf/$ftpusername)");

	$this->output.="<br>Deleting ftp user: $ftpusername<br>";
	$qu="delete from ".$this->conf['ftpuserstable']['tablename']." where ftpusername='$ftpusername' limit 1";
	$success=$success && $this->executeQuery($qu,' delete ftp user from ehcp db');
	if($deletefiles) $success=$success && $this->addDaemonOp("daemonftp","delete",$homedir,'',' ftp delete info ');
	$success=$success && $this->addDaemonOp("daemonftp","syncftp",'noempty','','sync/restart ftp');
	return $success;
}

function delSubDomain(){
	global $id;
	$this->requireNoDemo();
	$success=True;

	$data=$this->query("select * from ".$this->conf['subdomainstable']['tablename']. " where id=$id");
	$data=$data[0];

	$this->debugecho2(print_r2($data),1);

	$domainname=$data['domainname'];
	$subdomain=$data['subdomain'];
	$ftpusername=$data['ftpusername'];
	$homedir=$data['homedir'];

	$success=$success && $this->deleteFtpUserDirect($ftpusername);
	$success=$success && $this->executeQuery("delete from ".$this->conf['subdomainstable']['tablename']." where id=$id");
	$success=$success && $this->addDaemonOp("daemondomain","delsubdomain",$subdomain,$homedir,'subdomain delete');
	$success=$success && $this->addDaemonOp("syncdomains",'xx',$domainname,'','sync domains');


	$this->ok_err_text($success, "Delete subdomain success <br> Subdomain files not deleted, you may delete them manually by ftp/ssh", "Error deleting subdomain");
	$this->showSimilarFunctions('subdomainsDirs');
	return $success;
}

function addSubDomain(){
	global $subdomain,$domainname;
	$this->getVariable(['subdomain',"domainname"]);
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$success=True;


	$filter="domainname='$domainname'";

	if($subdomain){

		if(!is_valid_domain_name($subdomain)) return $this->error_text_tr("domain_format_wrong");
		$count=$this->recordcount($this->conf['subdomainstable']['tablename'], "domainname='$domainname' and subdomain='$subdomain'"); # todo: this should be moved to existscontrol
		if($count>0) return $this->errorText("subdomain already exists.");

		$domaininfo=$this->domaininfo=$this->getDomainInfo($domainname);

		// $homedir=$domaininfo['homedir']."/httpdocs/subdomains/$subdomain";
		$homedir=$domaininfo['homedir']."/subdomains/$subdomain";
		$webserverips=$domaininfo['webserverips'];

		$qu="insert into ".$this->conf['subdomainstable']['tablename']." (panelusername,subdomain,domainname,homedir,webserverips)values('$this->activeuser','$subdomain','$domainname','$homedir','$webserverips')";
		$success=$success && $this->executeQuery($qu, $opname);

		#$success=$success && $this->addDaemonOp("daemondomain","addsubdomain",$domainname,$homedir,'add subdomain');
		$success=$success && $this->add_daemon_op(['op'=>'daemondomain','action'=>'addsubdomain','info'=>$subdomain,'info2'=>$domainname,'info3'=>$homedir]);
		$success=$success && $this->addDaemonOp("syncdomains",'xx',$domainname,'','sync domains');
		if($success){
			$sub1="http://".$subdomain.".".$domainname;
			$sub2="http://www.".$subdomain.".".$domainname;
			$this->output.="<br>You may access <a target=_blank href='$sub1'>$sub1</a> and <a  target=_blank href='$sub2'>$sub2</a> in a few seconds..<br>";
		}
		$this->ok_err_text($success, "Add subdomain success", "Error adding subdomain");
	} else {
		$inputparams=[['subdomain','righttext'=>".$domainname"]];
		$this->output.="Enter subdomain here: <br>(additionally, www. automatically will be added in front of subdomain)".inputform5($inputparams);
	}
	$this->showSimilarFunctions('subdomainsDirs');
	return $success;

}

function showSimilarFunctions($func){
	# the text here may be read from a template
	$out1="Similar/Related $func Functions:";

	$out=$this->tr("similar_functions_".$func);

	if(strstr($out,'language error, not defined')) { # backw. comp. if using old lang files, show old strings here. 
		switch($func){
			# Do not change this hereafter, change lang file. strings in there.
			case 'ftp'   : 				$out = "<a href='?op=addftpuser'>Add New ftp</a>, <a href='?op=addftptothispaneluser'>Add ftp Under My ftp</a>, <a href='?op=addsubdirectorywithftp'>Add ftp in a subDirectory Under Domainname</a>, <a href='?op=addsubdomainwithftp'>Add subdomain with ftp</a>, <a href='?op=add_ftp_special'>Add ftp under /home/xxx (admin)</a>, <a href='net2ftp' target=_blank>WebFtp (Net2Ftp)</a>, <a href='?op=addcustomftp'>Add Custom FTP Account (Admins Only)</a>, <a href='?op=listallftpusers'>List All Ftp Users</a> "; break;
			case 'mysql' : 				$out = "<a href='?op=domainop&amp;action=listdb'>List / Delete Mysql Db's</a>, <a href='?op=addmysqldb'>Add Mysql Db&amp;dbuser</a>, <a href='?op=addmysqldbtouser'>Add Mysql db to existing dbuser</a>, <a href='?op=dbadduser'>Add Mysql user to existing db</a>, <a href='/phpmyadmin' target=_blank>phpMyadmin</a>";break;
			case 'email' : 				$out = "<a href='?op=listemailusers'>List Email Users / Change Passwords</a>, <a href='?op=addemailuser'>Add Email User</a>, Email forwardings: <a href='?op=emailforwardings'>List</a> - <a href='?op=addemailforwarding'>Add</a>, <a href='?op=bulkaddemail'>Bulk Add Email</a>, <a href='?op=editEmailUserAutoreply'>edit Email Autoreply</a> ,<a href='webmail' target=_blank>Webmail (Squirrelmail)</a>";break;
			case 'domain': 				$out = "<a href='?op=addDomainToThisPaneluser'>Add Domain To my ftp user (Most Easy)</a> - <a href='?op=adddomaineasy'>Easy Add Domain (with separate ftpuser)</a> - <a href='?op=adddomain'>Normal Add Domain (Separate ftp&panel user)</a> - <a href='?op=bulkadddomain'>Bulk Add Domain</a> - <a href='?op=adddnsonlydomain'>Add dns-only hosting</a> - <a href='?op=adddnsonlydomainwithpaneluser'>Add dns-only hosting with separate paneluser</a>-<br><a href='?op=addslavedns'>Make Domain a DNS Slave</a> - <a href='?op=removeslavedns'>Remove DNS Slave, if any</a><br><br>Different IP(in this server, not multiserver): <a href='?op=adddomaineasyip'>Easy Add Domain to different IP</a> - <a href='?op=setactiveserverip'>set Active webserver IP</a><br>List Domains: <a href='?op=listselectdomain'>short listing</a> - <a href='?op=listdomains'>long listing</a>";break;
			case 'redirect': 			$out = "<a href='?op=editdomainaliases'>Edit Domain Aliases</a>";break;
			case 'options' : 			$out =  $this->tr('similar_options_text');break;
			case 'customhttpdns': 		$out = "Custom Http: <a href='?op=customhttp'>List</a> - <a href='?op=addcustomhttp'>Add</a>, Custom dns: <a href='?op=customdns'>List</a> - <a href='?op=addcustomdns'>Add</a> --  Custom Permissions: <a href='?op=custompermissions'>List</a> - <a href='?op=addcustompermission'>Add</a>";break;
			case 'subdomainsDirs': 		$out = "SubDomains: <a href='?op=subdomains'>List</a> - <a href='?op=addsubdomain'>Add</a> - <a href='?op=addsubdomainwithftp'>Add subdomain with ftp</a> - <a href='?op=addsubdirectorywithftp'>Add subdirectory with ftp (Under domainname)</a> - <a href='?op=sync_directories'>Sync Directories</a>";break;
			case 'HttpDnsTemplatesAliases': $out = "<a href='?op=editdnstemplate'>Edit dns template for this domain </a> - <a href='?op=editwebservertemplate'>Edit webserver template for this domain </a> - <a href='?op=editdomainaliases'>Edit Aliases for this domain </a>";break;
			case 'panelusers': 			$out = "<a href='?op=listpanelusers'>List All Panelusers/Clients</a>, <a href='?op=resellers'>List Resellers</a>, <a href='?op=addpaneluser'>Add Paneluser/Client/Reseller</a>";break;
			case 'server':				$out = "<a href='?op=listservers'>List Servers/IP's</a> - <a href='?op=addserver'>Add Server</a> - <a href='?op=addiptothisserver'>Add ip to this server</a> - <a href='?op=setactiveserverip'>set Active webserver IP</a>";break;
			case 'backup':				$out = "<a href='?op=dobackup'>Backup</a> - <a href='?op=dorestore'>Restore</a> - <a href='?op=listbackups'>List Backups</a>";break;
			case 'vps': 				$out = "<a href='?op=vps'>VPS Home</a> - <a href='?op=add_vps'>Add new VPS</a> - <a href='?op=copy_vps_image'>Copy VPS Image</a> - <a href='?op=settings&group=vps'>VPS Settings</a> - <a href='?op=vps&op2=other'>Other Vps Ops</a>  <a href='?op=vps&op2='>Add HDD to VPS </a>"; break;
			case 'pagerewrite': 		$out = "<a href='?op=pagerewrite'>page rewrite home</a> - <a href='?op=pagerewrite&op2=add'>add page rewrite</a>";break;
			case 'custompermissions': 	$out = "<a href='?op=custompermissions'>List Custom Permissions</a> - <a href='?op=addcustompermission'>Add Custom Permissions</a>";break;
			case 'vpn':					$out = "<a href='?op=list_vpn'>List Vpn Users</a> <a href='?op=add_vpn'>Add Vpn User</a>";break;

			default	 : $out="(internal ehcp error) This similar function is not defined in ".__FUNCTION__." : ($func)"; $out1='';break;
		}
	}

	$this->output.="<br><br>$out1".$out."<br>";
}

function validate_ip_address($ip){
	if(validateIpAddress($ip)===false) $this->errorTextExit("ip format is wrong.. example: 85.98.112.34 (you entered:$ip)");
}

function add_ip_to_this_server(){
	global $ip;
	$this->getVariable(['ip']);

	if($ip){
		$this->validate_ip_address($ip);
		$q="insert into servers (servertype,ip,accessip) values ('apache2','$ip','localhost')";
		$this->executeQuery($q);
		$this->output.='Ip added.';
	} else {
		$this->output.=inputform5('ip');
	}

	$this->showSimilarFunctions('server');
}

function checkFtpLimit($ftpusername){
		if(!$this->afterInputControls("addftpuser",
				[
				"ftpusername"=>$ftpusername
				]
			)
		) $this->showexit();
}

function addFtpUser(){
	$op="addftpuser";
	global $domainname,$ftpusername,$password,$quota,$upload,$download;
	$this->getVariable(["domainname","ftpusername","password","quota","upload","download"]);
	$homedir=$this->conf['vhosts']."/$ftpusername";
	$success=True;

	if($ftpusername){

		if(!$this->afterInputControls("addftpuser",
				[
				"ftpusername"=>$ftpusername
				]
			)
		) return false;

		$this->output.="Adding ftp user with homedir $homedir:";

		$success=$success && $this->addFtpUserDirect($this->activeuser,$ftpusername,$password,$homedir,$quota,$upload,$download,$domainname);
		$this->ok_err_text($success,"ftp user Added successfully.","Ftp user add failed. ");	# all these functions are to reduce code needed...
	} else {
		if(!$this->beforeInputControls('addftpuser')) return false;
		$inputparams=[
			['ftpusername','lefttext'=>'Ftp username'],
			['password','password_with_generate'],
			['quota','lefttext'=>'Quota (MB)','default'=>100],
			['upload','lefttext'=>'Upload bw (kb/s)','default'=>1000],
			['download','lefttext'=>'Download bw (kb/s)','default'=>1000],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.="Note that, this step setups a standalone ftp user, To setup a domain ftp account, use add domain, <br><br>Adding ftp user:<br> (Under $homedir)<br>"
			.inputform5($inputparams);
			# inputform4($action,array("Ftp username","password","Quota (Mb)","Upload bw(kb/s)","Download bw(kb/s)"),array("ftpusername",array("password","password"),"quota","upload","download"),array('','',"50","100","100"),array("op"),array($op));
	}
	$this->showSimilarFunctions('ftp');
	return $success;

}

function addFtpToThisPaneluser(){ # add an ftp user freely under your master ftp directory
	global $ftphomedir,$ftpusername,$password,$_insert;
	$this->getVariable(["ftphomedir","ftpusername","password","_insert"]);
	$selfftp=$this->getSelfFtpAccount($returnto=__FUNCTION__); # ftp account for this panel user is with type field=default in ftpaccounts table
	$masterhome=$this->conf['vhosts']."/$selfftp";
	$success=True; # must be at start, to keep good formating.

	if($_insert){
		if(!$this->afterInputControls("addftpuser",
				[
				"ftpusername"=>$ftpusername
				]
			)
		) return false;

		$homedir="$masterhome/$ftphomedir";
		$this->output.="Adding ftp user:";
		$quota=$upload=$download=200;
		$success=$success &&  $this->addFtpUserDirect($this->activeuser,$ftpusername,$password,$homedir,$quota,$upload,$download,$domainname,'',True);# this also prepares that dir..

		if($success) {
			$this->output.="<br>You may access $homedir by ftp from now on.<br>";
			$msguser="ehcp: ftp space in $homedir in ehcp panel at ip: ".$this->conf['dnsip']." Your ftpusername: $ftpusername, Password: $password";
			if($email<>'') mail($email,"Your ftp space in $homedir setup in ehcp panel", $msguser,"From: ".$this->conf['adminemail']);
		}

		$this->ok_err_text($success, "Add ftp success", "Error add ftp");

	} else {
		if(!$this->beforeInputControls('addftpuser')) return false;
		$inputparams=[
			['ftphomedir','lefttext'=>"$masterhome/",'righttext'=>'Home directory of ftp user (may be empty for default of yours)'],
			"ftpusername",
			["password","password_with_generate"],
			['email','righttext'=>' of person the subdirectory is setup for..(will be informed)'],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.="Enter ftp info  here: <br>".inputform5($inputparams);
	}

	$this->showSimilarFunctions("ftp");
	return $success;
}


function add_ftp_special(){ # add an ftp user freely under /home/ dir freely
	global $ftphomedir,$ftpusername,$password,$_insert;
	$this->getVariable(["ftphomedir","ftpusername","password","_insert"]);
	$this->requireAdmin();

	$masterhome='/home';
	$success=True; # must be at start, to keep good formating.

	if($_insert){
		if(!$this->afterInputControls("addftpuser",
				[
				"ftpusername"=>$ftpusername
				]
			)
		) return false;

		$homedir="$masterhome/$ftphomedir";
		$this->output.="Adding ftp user:";
		$quota=$upload=$download=200;
		$success=$success &&  $this->addFtpUserDirect($this->activeuser,$ftpusername,$password,$homedir,$quota,$upload,$download,$domainname,'',True);# this also prepares that dir..

		if($success) {
			$this->output.="<br>You may access $homedir by ftp from now on.<br>";
			$msguser="ehcp: ftp space in $homedir in ehcp panel at ip: ".$this->conf['dnsip']." Your ftpusername: $ftpusername, Password: $password";
			if($email<>'') mail($email,"Your ftp space in $homedir setup in ehcp panel", $msguser,"From: ".$this->conf['adminemail']);
		}

		$this->ok_err_text($success, "Add ftp success", "Error add ftp");

	} else {
		if(!$this->beforeInputControls('addftpuser')) return false;

		$inputparams=[
			['ftphomedir','lefttext'=>"$masterhome/",'righttext'=>'Home directory of ftp user'],
			"ftpusername",
			["password","password_with_generate"],
			['email','righttext'=>' of person the subdirectory is setup for..(will be informed)'],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.="Enter ftp info  here: <br>".inputform5($inputparams);
	}

	$this->showSimilarFunctions("ftp");
	return $success;
}


function addSubDirectoryWithFtp(){
	global $subdirectory,$domainname,$ftpusername,$password,$email;
	$this->getVariable(['subdirectory',"domainname","ftpusername","password",'email'],True);

	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$filter="domainname='$domainname'";
	$success=True; # must be at start, to keep good formating.

	if($subdirectory){

		if(!$this->afterInputControls("addftpuser",
				[
				"ftpusername"=>$ftpusername
				]
			)
		) return false;

		$homedir=$this->getField($this->conf['domainstable']['tablename'], "homedir", $filter)."/httpdocs/$subdirectory";
		$this->output.="Adding ftp user:";
		$quota=$upload=$download=200;
		$success=$success &&  $this->addFtpUserDirect($this->activeuser,$ftpusername,$password,$homedir,$quota,$upload,$download,$domainname,'',True);# this also prepares that dir..

		if($success) {
			$sub1="http://".$domainname."/$subdirectory";
			$sub2="http://www.".$domainname."/$subdirectory";
			$this->output.="<br>You may access <a href='$sub1'>$sub1</a> and <a href='$sub2'>$sub2</a> in a few seconds..<br>";

			$msguser="ehcp: Subdirectory $sub1 or $sub2 setup in ehcp panel at ip: ".$this->conf['dnsip']." Your ftpusername: $ftpusername, Password: $password";
			if($email<>'') mail($email,"Your subdirectory $sub1 setup in ehcp panel", $msguser,"From: ".$this->conf['adminemail']);
		}

		$this->ok_err_text($success, "Add subdirectory & ftp success", "Error adding subdirectory");
	} else {
		if(!$this->beforeInputControls('addftpuser')) return false;
		$inputparams=[
			['subdirectory','lefttext'=>"www.$domainname/"],
			"ftpusername",
			["password","password_with_generate"],
			['email','righttext'=>' of person the subdirectory is setup for..'],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.="Enter subdirectory info  here: <br>".inputform5($inputparams);
	}
	$this->showSimilarFunctions("ftp");
	$this->showSimilarFunctions('subdomainsDirs');
	return $success;

}


function addSubDomainWithFtp(){

	global $subdomain,$domainname,$ftpusername,$password,$email;
	$this->getVariable(['subdomain',"domainname","ftpusername","password",'email'],True);
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$success=True;

	$filter="domainname='$domainname'";

	if($subdomain){
		if(!$this->afterInputControls("addftpuser",
				[
				"ftpusername"=>$ftpusername
				]
			)
		) return false;

		$count=$this->recordcount($this->conf['subdomainstable']['tablename'], "domainname='$domainname' and subdomain='$subdomain'");
		if($count>0) return $this->errorText("subdomain already exists.");

		$homedir=$this->getField($this->conf['domainstable']['tablename'], "homedir", $filter)."/httpdocs/subdomains/$subdomain";
		$qu="insert into ".$this->conf['subdomainstable']['tablename']." (panelusername,subdomain,domainname,homedir,ftpusername,password)values('$this->activeuser','$subdomain','$domainname','$homedir','$ftpusername',md5('$password'))";
		$success=$success && $this->executeQuery($qu, $opname);
		$success=$success && $this->addDaemonOp("syncdomains",'xx',$domainname,'','sync domains');

		$this->output.="Adding ftp user:";
		$quota=$upload=$download=100;
		$success=$success &&  $this->addFtpUserDirect($this->activeuser,$ftpusername,$password,$homedir,$quota,$upload,$download,$domainname,'subdomain',True);

		if($success) {
			$sub1="http://".$subdomain.".".$domainname;
			$sub2="http://www.".$subdomain.".".$domainname;
			$this->output.="<br>You may access <a href='$sub1'>$sub1</a> and <a href='$sub2'>$sub2</a> in a few seconds..<br>";

			$msguser="ehcp: Subdomain $sub1 or $sub2  setup in ehcp panel at ip: ".$this->conf['dnsip']." Your ftpusername: $ftpusername, Password: $password";
			if($email<>'') mail($email,"Your subdomain $subdomain.$domainname setup complete in ehcp panel", $msguser,"From: ".$this->conf['adminemail']);
		}

		$this->ok_err_text($success, "Add subdomain&ftp success", "Error adding subdomain");
	} else {
		if(!$this->beforeInputControls('addftpuser')) return false;
		$inputparams=[
			['subdomain','righttext'=>".$domainname"],
			"ftpusername",
			["password","password_with_generate"],
			['email','righttext'=>' of person the subdomain is setup for..'],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.="Enter subdomain here: <br>(additionally, www. automatically will be added in front of subdomain)".inputform5($inputparams);
	}
	$this->showSimilarFunctions("ftp");
	$this->showSimilarFunctions('subdomainsDirs');
	return $success;

}


function listMyAllDirectories(){
	$filter="panelusername='$this->activeuser'";

	$this->listTable("Password Protected Directories/users", 'passwddirectoriestable', $filter);
	$this->output.="<br> <a href='?op=adddirectory'>Add 'Password Protected Directory'</a>";
}


function directories(){
	global $domainname;
	$this->getVariable(["domainname"]);

	if($dom<>'')$domainname=$dom;
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);

	#$filter="panelusername='$this->activeuser'";
	$filter="domainname='$domainname'";

	$this->listTable("Password Protected Directories/users", 'passwddirectoriestable', $filter);
	$this->output.="<br> <a href='?op=adddirectory'>Add 'Password Protected Directory'</a>";
	$this->showSimilarFunctions('subdomainsDirs');

}

function subDomains(){
	global $domainname;
	$this->getVariable(["domainname"]);

	if($dom<>'')$domainname=$dom;
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);

	#$filter="panelusername='$this->activeuser'";
	$filter="domainname='$domainname'";

	if($this->selecteddomain<>'') $filter.=" and domainname='$this->selecteddomain'";
	$this->listTable("Subdomains", 'subdomainstable', $filter);
	$this->output.="<br> <a href='?op=addsubdomain'>Add Subdomain</a>";
	$this->showSimilarFunctions('subdomainsDirs');

}

function listDomains($dom='',$filt=''){
	global $domainname;
	$this->getVariable(["domainname"]);
	if($dom<>'')$domainname=$dom;

	if(!$domainname) {
		$linkimages=['images/edit.gif','images/incele.jpg','images/delete1.jpg','images/openinnew.jpg'];
		$linkfiles=['?op=editdomain','?op=selectdomain','?op=deletedomain',"target=_blank href='?op=redirect_domain"];
		$linkfield='domainname';
		$filter=$this->applyGlobalFilter($filt);
		#$this->output.="<hr>filtre: $filter</hr>";
		$this->output.="<div align=center>Domain List: ".
		$this->tablolistele3_5_4($this->conf['domainstable']['tablename'],$baslik,$this->conf['domainstable']['listfields'],$filter,$sirala,$linkimages,$linkfiles,$linkfield,$listrowstart,$listrowcount)."</div>";
	} else {
		$this->listemailusers($domainname);
	}
}

function listemailusers($dom=''){ # listemailusers
	global $domainname;
	$this->getVariable(["domainname"]);

	if($dom<>'')$domainname=$dom;
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);

	# this ensures the ownership of domain

	#$filter="domainname='$domainname'";
	$filter="domainname REGEXP '".$domainname."(,|$)'"; #  modified upon suggestion of sextasy@discardmail.com

	$this->output.="$domainname domain email user List: ";
	$this->listTable("", "emailuserstable", $filter);
	$this->showSimilarFunctions('email');
}


function listftpusers($dom=''){
	global $domainname;
	$this->getVariable(["domainname"]);
	if($dom<>'')$domainname=$dom; # parametre ile verilmisse listele...
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);

	# this ensures the ownership of domain

	$filter="domainname='$domainname'";
	$linkfiles=['?op=editftpuser','?op=userop&action=ftpuserdelete'];
	$linkimages=['images/edit.gif','images/delete1.jpg'];
	$linkfield='ftpusername';
	$this->output.="<div align=center>$domainname domain ftp user List: <br>Note: deleting a user, will delete his files too..<br>"
			.$this->tablolistele3_5_4($this->conf['ftpuserstable']['tablename'],$baslik,["domainname","ftpusername"],$filter,$sirala,$linkimages,$linkfiles,$linkfield,$listrowstart,$listrowcount)."</div>";

	$this->showSimilarFunctions("ftp");
}



function listallemailusers(){
	if(!$this->isadmin()) {
		$filter="panelusername='$this->activeuser'";
		$filter=$this->applyGlobalFilter($filter);
	}

	$filter=andle($filter, "domainname='{$this->selecteddomain}'");

	$this->output.="All domain's email users: ";
	$this->listTable("", "emailuserstable", $filter);
	$this->listTable("Email Forwardings", 'emailforwardingstable', $filter);
	$this->showSimilarFunctions('email');
}


function listAllFtpUsers($filt=''){
	$linkfiles=['?op=editftpuser','?op=userop&action=ftpuserdelete'];
	$linkimages=['images/edit.gif','images/delete1.jpg']; # edit passwordlu image eklenecek
	$linkfield='ftpusername';
	$filter=$this->applyGlobalFilter($filt);
	$this->debugtext("filter: $filter");
	$this->output.="<div align=center>Ftp users: ".
	$this->tablolistele3_5_4($this->conf['ftpuserstable']['tablename'],$baslik,$this->conf['ftpuserstable']['listfields'],$filter,$sirala,$linkimages,$linkfiles,$linkfield,$listrowstart,$listrowcount)."</div>";

	$this->output.="<br>Empty homedir means default location";

	$this->showSimilarFunctions("ftp");
}

function listpanelusers(){
	$table=$this->conf['paneluserstable'];
	$filter=$this->globalfilter;
	$this->output.="<div align=center>All/your panel users: filt: $filter ".
		$this->tablolistele3_5_4($table['tablename'],['','','','','','Quota (MB*)'],$table['listfields'],$filter,$sirala,$table['clickimages'],$table['clickfiles'],$table['linkfield'],$listrowstart,$listrowcount)
		."<a href='?op=addpaneluser'>Add Paneluser/Reseller</a></div>";
	$this->showSimilarFunctions('panelusers');
}

function resellers(){
	$table=$this->conf['paneluserstable'];
		$filter="maxpanelusers>1";
	$filter=andle($filter,$this->globalfilter);
	$this->output.="<div align=center>All/your panel users: ".
		$this->tablolistele3_5_4($table['tablename'],$baslik,$table['listfields'],$filter,$sirala,$table['clickimages'],$table['clickfiles'],$table['linkfield'],$listrowstart,$listrowcount)."</div>";

	$this->output.="<br><a href='?op=addpaneluser'>Add Reseller/Panel User</a> <br>";
	$this->showSimilarFunctions('panelusers');
}

function getPanelUserInfo($id='',$panelusername=''){
	if($id){
		$filt="id=$id";
	} elseif($panelusername<>'') {
		$filt="panelusername='$panelusername'";
	} else {
		$filt="panelusername='".$this->activeuser."'";
	}
	$ret=$this->query("select * from ".$this->conf['paneluserstable']['tablename']." where $filt ");
	return $ret[0];
}

function deletepaneluser(){
	$this->requireNoDemo();

	global $id,$confirm;
	$this->getVariable(['id','confirm']);

	$paneluserinfo=$this->getPanelUserInfo($id);
	$panelusername=$paneluserinfo['panelusername'];

	$dom=$this->getField($this->conf['domainstable']['tablename'],'domainname',"panelusername='$panelusername' or reseller='$panelusername'");

	if($dom<>''){
		return $this->errorText("This user/reseller has associated domain, please delete his/her domains first: <a href='?op=choosedomaingonextop&domainname=$dom'>go to $dom</a> - <a href='?op=choosedomaingonextop&nextop=deletedomain&domainname=$dom'>delete $dom</a>");
	}

	if($panelusername=='admin'){
		return $this->errorText('admin self account cannot be removed... are you ok ?');
	}

	if(!$confirm){
		$this->output.="<big>if you are sure, click <a href='?op=deletepaneluser&id=$id&confirm=1'>here to delete panel user</a></big><br><br>";
		return false;
	}

	if($panelusername=='ehcp'){
		mail($paneluserinfo['email'],"ehcp-Your domain paneluser deleted","ehcp deleted from panel at ".$this->conf['dnsip']);
	}
	if(!$this->isadmin()) $where="and reseller='".$this->activeuser."'"; # if admin, delete any user, if not, only your users, in fact, all sub-users/resellers should be deletable by owner, because ehcp is multi-level
	$success=$this->executeQuery("delete from ".$this->conf['paneluserstable']['tablename']." where id=$id $where",'Deleting domain paneluser info from ehcp db id:'.$id);
	$this->ok_err_text($success,"All ops successfull. Delete paneluser complete id: $id","failed some ops while deleting paneluser (deletepaneluser) ");
	$this->showSimilarFunctions('panelusers');
	return $success;
}

function editFtpUser(){
	global $ftpusername,$_insert,$status,$newpass,$newpass2;
	$this->getVariable(['ftpusername','_insert','status','newpass','newpass2']);
	$success=True;

	if($ftpusername=='ehcp'){
		return $this->errorText('ehcp self ftp account cannot be edited... are you ok ?');
	}

	if(!$ftpusername){ # if no ftpusername given, learn it from domainname,
		$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
		$ftpusername=$this->getField($this->conf['ftpuserstable']['tablename'], "ftpusername", "domainname='$domainname' limit 1");
	}

	if(!$ftpusername){
		$this->output.="<br><b>This domain does not have a dedicated ftp account, so, you may change your general ftp account <a href='?op=listallftpusers'>here</a></b><br>";
		return false;
	}

	if(!$_insert){
		$inputparams=[
			['status','select','lefttext'=>'Set Active/Passive','secenekler'=>$this->statusActivePassive],
			#array('status','lefttext'=>'Set Active/Passive'),
			['newpass','password','lefttext'=>'New pass: (leave empty for no change)'],
			['newpass2','password'],
			['ftpusername','hidden','default'=>$ftpusername],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.="Changing ftp user: $ftpusername <br>".inputform5($inputparams);

	} else {

		$filt=$this->applyGlobalFilter("ftpusername='$ftpusername'");
		$this->debugtext("filter: $filt");
		if($newpass=='' and $newpass2=='') {
			$passwordset='';
			} else {
			if($newpass<>$newpass2) $success=$this->errorText('Two passwords are not same..retry please.');
			$passwordset=", password=password('$newpass') ";
		}


		$success=$success && $this->executeQuery("update ".$this->conf['ftpuserstable']['tablename']." set status='$status' $passwordset where $filt");
		$success=$success && $this->addDaemonOp("daemonftp",$status,$this->conf['vhosts'].'/'.$ftpusername);
		$success=$success && $this->addDaemonOp('syncftp','','','','sync ftp for nonstandard homes');
		$success=$success && $this->addDaemonOp("daemonftp","syncftp",'noempty','','sync/restart ftp');

		$this->ok_err_text($success,'ftp status/info successfully changed.','ftp status/info could not changed.');
	}
	$this->showSimilarFunctions('ftp');
	return $success;

}
#==============================================================
# =============== class utility functions insertrow, editrow,

function insertrow($tabledesc,$constfields,$constvalues){
	global $_insert;
	$this->getVariable(["_insert"]);
	$table=$this->conf[$tabledesc];

	$linkfield=$table['linkfield'];
	global ${$linkfield};   # get id..
	$this->getVariable([$linkfield]);
	$fields=$table['insertfields']; # get other edit fields


	if($_insert){
		foreach ($fields as $alan) global ${$alan}; # yukardakilerin hepsini global yap..
		$newvalues=$this->getVariable($fields);
		$fields=array_merge($fields,$constfields);
		$newvalues=array_merge($newvalues,$constvalues);
		if($this->insertquery($table['tablename'],$fields,$newvalues)) $this->output.="Updated successfully.";
	}else{
		$values=array_values($this->alanal2($table['tablename'],$fields,$where));
		$this->output.="insert: $id <br>".inputform4('',
		[],# labels
		$fields,
		$values,
		#array($res[0],$res[1],$res[2],$res[3],$res[4],$res[5],$res[6],$res[7],$parabirimleri,),
		[$linkfield,"_insert"],[${$linkfield},"1"]
		);
	}

}

function editrow2($tabledesc,$where,$extra=[]){
	# 1den farki: inputform5 ile calisacak:, editpaneluser icinde kullanilacak,
	global $_insert;
	$this->getVariable(["_insert"]);
	$table=$this->conf[$tabledesc];

	$linkfield=$table['linkfield'];
	global ${$linkfield};   # get id..
	$this->getVariable([$linkfield]);
	$fields=$table['editfields']; # get other edit fields


}

function editrow($tabledesc,$where,$extra=[]){
	global $_insert;
	$this->getVariable(["_insert"]);
	$table=$this->conf[$tabledesc];

	$linkfield=$table['linkfield'];
	global ${$linkfield};   # get id..
	$this->getVariable([$linkfield]);

	$fields=$table['editfields']; # get other edit fields
	$editlabels=$table['editlabels'];


	if($_insert){
		foreach ($fields as $alan) global ${$alan}; # yukardakilerin hepsini global yap..
		$newvalues=$this->getVariable($fields);
		$success=$this->updatequery($table['tablename'],$fields,$newvalues,$linkfield."=".${$linkfield});
		return $this->ok_err_text($success,"Updated successfully.","Update failed (editrow)");
	}else{
		$values=array_values($this->alanal2($table['tablename'],$fields,$where));
		$this->output.="edit id: $id <br>".inputform4('',
		$editlabels,
		$fields,
		$values,
		#array($res[0],$res[1],$res[2],$res[3],$res[4],$res[5],$res[6],$res[7],$parabirimleri,),
		[$linkfield,"_insert"],[${$linkfield},"1"]
		);
		return True;
	}

}

function myreseller(){
	#return $this->alanal(); #enson email ekleme yap???yordum...
}

function applyGlobalFilter($filter){
	if($this->globalfilter) $global='('.$this->globalfilter.')';
	$filt=andle($global,$filter);
	#$this->output.="<hr>globalfilter: $this->globalfilter, filter: $filter, applyglobalfilter result: $filt</hr>";
	return $filt;
}

function test(){
	#return executeprog2("mkdir xxx");
	return false;
	$suc=True;
	$ss=false;
	$suc=$suc && $ss;
	$this->isTrue($suc,'test icinde');

	$suc=True;
	$ss=True;

	$suc=$suc && $ss=$gg=false;
	$this->isTrue($suc,'test icinde, True olmali.');


	$this->isTrue(True);
	$this->isTrue(false);
	$this->isTrue(null);
	$this->isTrue(0);
	$this->isTrue('');
	$this->output.="<hr>finished first test</hr>";
}



#============================= utility functions, query etc..
function exist($table,$where){
	$sayi=$this->recordcount($table,$where);
	return ($sayi>0);
}

function recordcount($table,$where,$debug=false) {
	$q="select count(*) as sayi from ".$table;
	if($where<>'') $q.=" where ". $where;
	$sayi=$this->query2($q);
	if($debug) $this->output.="<br>$q<br>";
	if($sayi) return $sayi['sayi'];
	else return false;
}


function updatequery($table,$fields,$values,$where){
		$record=array_combine($fields,$values);
		#$this->output.=print_r2($record);
		return $this->conn->AutoExecute($table,$record,'UPDATE', $where);
}

function insertquery($table,$fields,$values){
		$record=array_combine($fields,$values);
		#$this->output.=print_r2($record);
		return $this->conn->AutoExecute($table,$record,'INSERT');
}


function safe_execute_query($qu,$params,$opname='',$caller='',$link=false){ # only executes conn->execute
	# example: $this->safe_query("select * from panelusers where panelusername='%s' and md5('%s')=password",array($username,$password),"","comment-caller")
	$this->logquery($qu.($caller?' Caller:'.$caller:''));
	$params2=[$qu];
	foreach($params as $p) $params2[]=$this->escape($p);
	$qu = call_user_func_array('sprintf', $params2);
	$this->logquery($qu.'  Caller:'.$caller);

	if(is_object($link)){  # link is for queries that needs to be executed on another mysql link.
		$rs=mysqli_query($link,$qu);
		$err=mysqli_error($link);
	} else $rs=$this->conn->Execute($qu);

	if($rs===false) {
		$err=$this->conn->ErrorMsg();
		return $this->error_occured("Error $opname (executequery: $qu) $err");
	}
	if($opname<>''){
		$this->echoln("Success $opname ");
	}
	return True;
}


function safe_query($qu,$params,$caller=''){ #returns associated array
	$this->logquery($qu.'  Caller:'.$caller);
	$params2=[$qu];
	foreach($params as $p) $params2[]=$this->escape($p);
	$qu = call_user_func_array('sprintf', $params2);
	$this->logquery($qu.'  Caller:'.$caller);

	$rs = $this->conn->Execute($qu);

	if($rs===false) {
		$err=$this->conn->ErrorMsg();
		return $this->error_occured("query, caller: $caller ","query: $qu ($err)");
	}
	else $res=$rs->GetArray();
	#if(!$res) $this->debugtext("query: res null, query: $qu");
	return $res;

}

function check_mysql_connection(){
	# reconnect if mysql conn gone away
	# should be called in daemon mode

	echo "\nfile:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__."\n";

	$rs = $this->conn->Execute("select now()");
	if($rs===false){
		$msg=$this->conn->ErrorMsg();

		if(strstr($msg,'server has gone away')!==false){
			$this->tryReconnect();
		}
	} else {
		echo "\nmysql connection is already alive\n";
	}

}

function query($qu,$caller=''){ #returns associated array
	#$this->check_mysql_connection();
	$this->logquery($qu.'  Caller:'.$caller);
	$rs = $this->conn->Execute($qu);
	if($rs===false) {
		$err=$this->conn->ErrorMsg();
		return $this->error_occured("query, caller: $caller ","query: $qu ($err)");
	}
	else $res=$rs->GetArray();
	#if(!$res) $this->debugtext("query: res null, query: $qu");
	return $res;
}

function query2($qu) { # sadece tek sat??r d??nd??r??r
	$res=$this->query($qu);
	#$this->output.="qu: $qu <br>".print_r2($res);
	return $res[0];
}


function getField($tablo,$alan,$filter){
	$query="select $alan from $tablo";
	if($filter<>'')$query.=" where $filter";
	$res=$this->query($query);
	#$this->output.="alanal icinde:".print_r2($res);
	#if($res)$this->output.="<hr>bilgi var... <hr>";
	if($res)return $res[0][$alan];
	else return false;
}

function multiserver_getfield($tablo,$alan,$filter,$serverip){
	if(is_array($serverip)) $serverip=$serverip['ip'];  # accept both until all code is standard.

	$query="select $alan from $tablo";
	if($filter<>'')$query.=" where $filter";
	$res=$this->multiserver_query($query,$serverip);
	#$this->output.="alanal icinde:".print_r2($res);
	#if($res)$this->output.="<hr>bilgi var... <hr>";
	if($res)return $res[0][$alan];
	else return false;
}

function alanal2($tablo,$alanlar,$filter){
	$query="select ".selectstring($alanlar)." from $tablo ";
	if($filter<>'')$query.=" where $filter";
	$res=$this->query($query);
	#$this->output.=print_r2($res);
	if($res) return $res[0];
	else return false;
}


function tabloyaekle2($tablo,$record){
	$sql = "SELECT * FROM $tablo WHERE 1 = 2";
	$rs = $this->executeQuery($sql);
	$sql = $this->conn->GetInsertSQL($rs, $record);
	#$this->output.="<br>sql:$sql ".print_r2($fields).print_r2($values)."<br>";
	if(!$res=$this->executeQuery($sql)) $this->output.="Tabloya eklerken hata: $tablo <br>";
	return $res;
}


function getinsertsql($tablo,$fields,$values){
	$sql = "SELECT * FROM $tablo WHERE 1 = 2";
	$rs = $this->conn->Execute($sql);
	$record=array_combine($fields,$values);
	$sql = $this->conn->GetInsertSQL($rs, $record);
	#$this->output.="<br>sql:$sql ".print_r2($fields).print_r2($values).print_r2($record)."<br>";
	return $sql;
}

function logquery($qu){
	$this->queries[]=$qu;
	if(count($this->queries)>1000) $this->queries=[]; # limit it
}

function executeQuery($qu,$opname='',$caller='',$link=false,$adoConn=false){ # only executes conn->execute
	$this->logquery($qu.($caller?' Caller:'.$caller:''));

	if(is_object($link)) {  # link is for queries that needs to be executed on another mysql link.
		$rs=mysqli_query($link,$qu);
		$err=mysqli_error($link);
		$this->debugecho("query executed on another mysql link:($qu)",1,false);
	} elseif($adoConn) {
		$rs=$adoConn->Execute($qu);
		$err=$adoConn->ErrorMsg();
		$this->debugecho("query executed on another ado-mysql link.($qu)",1,false);
	} else {
		$rs=$this->conn->Execute($qu);
		$err=$this->conn->ErrorMsg();
	}

	if($rs===false) {
		return $this->error_occured("Error $opname (executequery: $qu) ($err)");
	}

	if($opname<>''){
		$this->echoln("Success $opname ");
	}
	return True;
}

function query3($qu,$opname='',$caller='',$link=false,$adoConn=false){ # only executes conn->execute
	$this->logquery(__FUNCTION__.':'.$qu.($caller?' Caller:'.$caller:''));

	if(is_object($link)){  # link is for queries that needs to be executed on another mysql link.
		$rs=mysqli_query($link,$qu);
		$err=mysqli_error($link);
		$this->debugecho("query executed on another mysql link:($qu)",1,false);
		$res=[];
		while($r=mysqli_fetch_assoc($rs)) $res[]=$r; # build ado style result set.

	} elseif($adoConn) {
		$rs=$adoConn->Execute($qu);
		$err=$adoConn->ErrorMsg();
		$this->debugecho("query executed on another ado-mysql link.($qu)",1,false);
		$res=$rs->GetArray();
	} else {
		$rs=$this->conn->Execute($qu);
		$err=$this->conn->ErrorMsg();
		$res=$rs->GetArray();
	}

	if($rs===false) {
		return $this->error_occured("Error $opname (executequery: $qu) ($err)");
	}

	if($opname<>''){
		$this->echoln("Success $opname ");
	}

	return $res;
}


function nextgoal(){

}

function has_role($role){
	$say=$this->recordcount('roles',"panelusername='$this->activeuser' and rolename='$role'");
	return ($say>0);
}

function isadmin(){
	return ($this->activeuser=='admin') or $this->has_role('admin');
}

function is_special_user(){ # python style function name
	$email=$this->getField('panelusers','email',"panelusername='$this->activeuser'");
	$ret=(($email=='info@ehcp.net')and($this->activeuser=='ehcp')) or ($this->activeuser=='admin');
	return $ret;
}



# ============================ initialization and db settings misc
function connectTodb() {
	# $this->conn=NewADOConnection("mysql"); # reconnect did not work, so i moved $this->conn=NewADOConnection("mysql") into connectTodb2
	$ret=$this->connectTodb2();
	return $ret;
}

function connect_to_mysql($named_params){

	# our new function with named_params
	extract($named_params);
	#$this->output.=__FUNCTION__.print_r2($named_params);
	$this->output.=__FUNCTION__.": dbhost:$dbhost,dbusername:$dbusername,dbname:$dbname";

	# diger biryol:
	#

	/* CREATE USER 'ehcp'@'95.9xx' IDENTIFIED BY  '***';
	   GRANT ALL PRIVILEGES ON * . * TO  'ehcp'@'95.9.xx' IDENTIFIED BY  '***' WITH GRANT OPTION MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;

	 * I could not connect to a remote mysql db using adodb.
	 * $conn=NewADOConnection("mysql");
	$conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$conn->connect($dbhost,$dbusername,$dbpass,$dbname);
	if(!$conn or ($conn->ErrorMsg()<>'')){*/
	if(is_object($this->connected_mysql_servers[$dbhost])) return $this->connected_mysql_servers[$dbhost];

	if(! $link = mysqli_connect($dbhost, $dbusername, $dbpass)){
		$this->output.="<br><big>mysql connection error: $dbhost".mysqli_error($link).$this->conn->ErrorMsg()."</big>";
		return false;
	}

	if(!mysqli_select_db($link,$dbname)){
		$this->output.="<br>Cannot select db: $dbname on host:$dbhost<br>";
		return false;
	};


	$this->connected_mysql_servers[$dbhost]=$link; # keep track of connections, to prevent multiple connection trying.

	return $link;
}


function mylink() {
	if(!$this->conn or !$this->connected) {
		return False;
	} else {
		return $this->conn->_connectionID; # bunu adodb kodlar??ndan ????rendim.
	}
}

function escape($str) {	
	if(!$this->conn or !$this->connected) {
		$this->debugecho2("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__." DB connect olmamis... string oldugu gibi donuyor..",3);
		if($this->debuglevel>2) print debug_backtrace2();
		return $str;
	} else {
		#return $this->conn->qstr($str); # qstr: adodb fonksiyonu
		return mysqli_real_escape_string($this->mylink(),$str);
	}
}

function escape2($str) {
	// $this->debugecho2("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__." Input: $str",3);
	$ret=$this->escape($str);
	// $this->debugecho2("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__." Escaped: $ret",3);
	$ret=htmlspecialchars($ret);
	$this->debugecho2("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__." Escaped2: $ret",3);
	return $ret;
}

function post($var){
	return $this->escape($_POST[$var]);
}

function get($var){
	return $this->escape($_GET[$var]);
}


function connectTodb2(){
	# why a separate func: connecttodb is a wrapper for this. it may do other stuff.
	$this->conn=NewADOConnection("mysqli");
	$this->conn->connect($this->dbhost,$this->dbusername,$this->dbpass,$this->dbname);

	if(!$this->conn or ($this->conn->ErrorMsg()<>'')){
		echo "<div align=center><font size=+1><hr>Error Occured while connecting to db, check your db settings... <br>
		This is mostly caused by wrong ehcp password in config.php <br>
		if you just installed ehcp, then learn/know your ehcp root password, then re-install ehcp.. <br>
		you may also try <a target=_blank href=troubleshoot.php>troubleshoot page</a> or <a target=_blank href=misc/mysqltroubleshooter.php>Additional mysql troubleshooter... </a> or <a target=_blank href='http://www.ehcp.net/?q=node/245'>www.ehcp.net/?q=node/245</a>
		<hr></font>".$this->conn->ErrorMsg()."</div>";
		$this->connected=false;
		return false;
	}
	$this->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$this->executeQuery("set names utf8");

	$this->connected=True;
	return True;
}

function checkdbsettings(){
	global $rootuser,$rootpass;
	$this->getVariable(["rootuser","rootpass"]);
	if($rootuser=='' and $rootpass==''){
		$this->output.="<br>Please check the database exists and configuration is correct in config/dbconf.php.. Additional help will be provided later.<br>
		To check your db, enter your db root user and pass:";
		$this->output.=inputform4($action,$yazilacak,["rootuser","rootpass"],$deger,["op"],["checkdbsettings"]);
	} else {
		$this->output.="Checking your db connection and db:<br>";
		#to be coded later.

	}

}



function tr($str,$find=[],$replace=[]){
	return $this->sayinmylang($str,$find,$replace);
}

function sayinmylang($str,$find=[],$replace=[]){
	$res=$this->lang[$this->currentlanguage][$str];
	if(!$res) $res=$this->lang['en'][$str];
	if(!$res) $res="($str:language error, not defined ".$this->information(1,True).")";

	# replace parameters.. 
	if(count($find)>0) {
		for ($i=0; $i < count($find); $i++) { 
			$res = str_replace($find[$i], $replace[$i], $res);
		}
	}

	# $res="language error: ($str) is not defined in currentlanguage of:(".$this->currentlanguage.") <br> please define <b> \$this->lang['".$this->currentlanguage."']['".$str."']=\"........\";   </b> in  <br>language/".$this->currentlanguage.".php <br>";
	return $res;
}

function setLanguage($lang,$quite=false){
	# todo: ability to change lang per user.
	$this->requireAdmin();
	$this->requireNoDemo();

	$file="templates/$this->template/$lang/template_$lang.html";
	if(!file_exists($file)) $this->errorTextExit("Lisan Bulunamadi.($file)");
	$this->currentlanguage=$lang;
	$_SESSION['currentlanguage']=$lang;
	$this->setConfigValue("defaultlanguage",$lang);
	$this->loadLanguage();
	if(!$quite) $this->output.="<hr>Language is set as: $lang <hr>";
}

#=================================== login functions etc..
function debugecho($str,$inlevel,$directecho=True){

	if($this->commandline){
		$lf="\n";
		if(is_array($str)) $str=print_r($str);
	} else {
		$lf="<br>";
		if(is_array($str)) $str=print_r2($str);
	};


	if($this->debuglevel>=$inlevel) {
		$out="$lf Debug*: Debuglevel: $this->debuglevel, $str $lf";
		if($directecho or $this->commandline) echo $out;
		else $this->output.=$out;
	}
}

function debugecho2($str,$inlevel){
	if($this->debuglevel>=$inlevel) $this->output.="<br>Debug*: Debuglevel: $this->debuglevel, ".$str."<br>";
}

function loadLanguage(){


	$this->debugecho2("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__,4);

	if(!$this->defaultlanguage) {
		$this->output.="defaultlanguage is not defined..";
	}

	if(!$currentlanguage) $currentlanguage=$_SESSION['currentlanguage'];  # load currentlanguage from session, if not set, it is defaultlanguage
	if(!$currentlanguage){
		$currentlanguage=$this->defaultlanguage;
	}

	if($currentlanguage=='') $currentlanguage='en';
	$this->currentlanguage=$currentlanguage;


	include_once("language/".$this->currentlanguage.".php");
	include_once("language/en.php");

	# load english lang if language file is not found
	if(count($this->lang)==0) {
		$this->echoln("default language file for ($this->defaultlanguage) is not found, english file loaded instead <br>
		Language files under language directory, under ehcp dir, you may write your own lang file..<br>
		default language is defined in <b>config.php</b><br>");
	}
	$this->debugecho2("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__,4);
	#$this->output.="loadlanguage is loaded.....<br>".print_r2($this->lang);
}


function debug() {
	$ret.="<br>Debug: <br>dbhost:".$this->dbhost;
	$ret.="<br>dbuser:".$this->dbusername;
	#$ret.="<br>dbpass:".$this->dbpass;
	$ret.="<br>dbname:".$this->dbname;
	return $ret;
}

function showConfig(){
	#$this->output.=print_r2($this->conf);
}

function isPrivateIp($ip){
	if(is_array($ip)) { # test for multiple ips if an array
		$ret=false;
		foreach($ip as $i)
			$ret=$ret or $this->isPrivateIp($i);
		return $ret;
	}

	return (substr($ip,0,7)=='192.168' or substr($ip,0,6)=='172.16' or substr($ip,0,3)=='10.');
}

function dynamicInfo(){
	global $quickdomains,$smallserverstats; # used in show()
	if(!$this->checkConnection('Some dynamic info')) return false;

   	$email=str_replace('@','((at))',$this->conf['adminemail']); # to prevent spam...
	$ret.="Current Active user: Welcome $this->activeuser !<br> Admin email: $email <br></b>";

	if(!$this->isloggedin or $this->isadmin()) {
		$ret.="Your dns/server ip is set as:".$this->conf['dnsip'].", if it is not detected/set correctly, please set it in your <a href='?op=options'>Settings</a></font><br><br>";
		$smallserverstats=$this->smallserverstats();
		$ret.=$smallserverstats; # this may be disabled
	}

	if(validateIpAddress($this->conf['dnsip'])===false) {
		$this->warnings.="<br><font size=+1><b>Warning : Your dns/server ip syntax is wrong.. you must fix it.. example: 85.98.112.34 or alike </b>b></font><br>";
	}

	if($this->isPrivateIp($this->conf['dnsip'])) {
		$this->warnings.="<br><font size=+1><b>Warning : Your dns/server ip seems your local/private ip (".$this->conf['dnsip']."). in order your server be accessible from outside, you should set it to your outside/real ip (your modem/router's ip)</b></font><br>";
	}

	if($this->isDemo) $this->warnings.="<br><b>This is demo mode, some operations such as change dns may not be available</b><br>";
	# $ret.=print_r2($this->userinfo);

	$quickdomains='';
	$doms=$this->getMyDomains("");
	if(count($doms)<=10 and is_array($doms)){
		$quickdomains="Quickselect: ";
		foreach($doms as $d) $quickdomains.="<a href='?op=selectdomain&id=".$d['domainname']."'>".$d['domainname']."</a> ";
	}


	return $ret;
}

function dynamicInfo2(){
	global $quickdomains,$smallserverstats,$ehcpversion; # used in show()
	if(!$this->checkConnection('Some dynamic info')) return false;

   	$email=str_replace('@','((at))',$this->conf['adminemail']); # to prevent spam...
	$ret.="Admin:$email<br>";


	$ret.="Users:".$this->recordcount($this->conf['logintable']['tablename'],'').
	",Domains:".$this->recordcount($this->conf['domainstable']['tablename'],'').
	",Ftpusers:".$this->recordcount($this->conf['ftpuserstable']['tablename'],'').
	",Emails:".$this->recordcount($this->conf['emailuserstable']['tablename'],'').
	"<br><a target=_blank href='http://www.ehcp.net'>Version: $ehcpversion</a><br></font>";

	return $ret;
}

function debuginfo(){ # this is debug info for developers
	if($this->debuglevel==0) return;
	$ret=print_r2($this->queries).$this->clientip;
	return $ret;
}


function getDomainInfo($domainname){
	$ret=$this->query("select * from ".$this->conf['domainstable']['tablename']." where domainname='$domainname'");
	$ret=$ret[0];

	list($ftpserver)=explode(',',$ret['webserverips']);
	if($ftpserver=='') $ftpserver='localhost';
	$ret['ftpserver']=$ftpserver;
	if($ret['webserverips']=='') $ret['webserverips']='localhost';
	if($ret['dnsserverips']=='') $ret['dnsserverips']='localhost';

	return $ret;
}

function navigation_bar(){
	if($this->selecteddomain) {

		if(!$this->domaininfo) $this->domaininfo=$this->getDomainInfo($this->selecteddomain);
		$domaininfo=$this->domaininfo;
		if($domaininfo['reseller']<>$this->activeuser and $this->isadmin()) $warning="<b>This domain belongs to (".$domaininfo['reseller'].") reseller, go to <a href='?op=resellers'>resellers page</a> for details</b><br>"; // z7 mod

		if (($domaininfo['diskquotaused']>$domaininfo['diskquota']) && ($domaininfo['diskquota']>0)) $quotaWarning=$this->sayinmylang("You have exceeded your quota");else $quotaWarning="";

		if(($domaininfo['webserverips']=='') or ($domaininfo['webserverips']=='localhost')) $webserverips_str='';
		else $webserverips_str=" - Webserverips:".$domaininfo['webserverips'];

		if($domaininfo['status']=='passive') $pass="<font size=+1>This domain is passive click <a href='?op=editdomain'>here</a> to activate</font>";

		$ret="$warning<div class=topnavigator><a href='?op=deselectdomain'>".$this->sayinmylang("Panel Home")."</a> - <a href=index.php>".$this->sayinmylang("Domain Home")."</a> - <a href='?op=listselectdomain'>".$this->sayinmylang("Domains")."</a> -> ".$this->sayinmylang("Selected Domain").": <a href=?>$this->selecteddomain</a>  <a target=_blank href=http://www.$this->selecteddomain><img border=0 src=images/openinnew.jpg></a>  - Disk Quota: [".$domaininfo['diskquotaused']."MB / ".$domaininfo['diskquota']."MB] (<a href='?op=doupdatediskquota&domainname=".$this->selecteddomain."'>update quotainfo</a>)".$quotaWarning.$webserverips_str." $pass</div>";
	} elseif ($this->is_email_user()) {

	} else $ret='(No domain is selected yet)<br>';

	return $ret;
}

function failedlogins(){
	global $mark;
	$this->getVariable(['mark']);
	if($mark<>'') {
		$this->executeQuery("update log set notified='yes' where panelusername='$this->activeuser' and (notified is null or notified='')");
		$this->output.="Marked all read ($mark)";
	}

	$this->listTable('','logtable',"panelusername='$this->activeuser' and (notified is null or notified='')");
	$this->output.="<a href='?op=failedlogins&mark=read'>Mark all read</a>";



}

function check_failed_logins(){
	if(!$this->isloggedin) return;
	$s=$this->recordcount('log',"panelusername='$this->activeuser' and (notified is null or notified='')");
	if($s>0) return "<p class=failedlogin>You have failed login attempts, <a class=failedlogin href='?op=failedlogins'>click here for more info</a></p><br>";
}

function show($templatefile1='') {
	global $commandline,$output,$quickdomains,$ehcpversion,$known_to_work;
	$this->output.=$output.$this->debuginfo();
	$dynamicInfo=$this->dynamicInfo();
	$dynamicInfo2=$this->dynamicInfo2();

	$this->debugecho2("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__."-".$point++,4);


	#$this->output.="<hr>".$this->conf['dnsip']."<hr>";
	#$this->output.="deneme.... ";

	if($this->warnings<>'') {
		if ($this->op=='warnings' && $this->isadmin()) {
			$this->check_ehcp_version();
			$this->output.=$this->warnings;
		}
		else $this->output.="<br><table>
							<tr>
								<td>
								</td>
							</tr>
						   <tr>
							   <td><img src=images/warning.jpg>
							   </td>
							   <td><a href='?op=warnings'>You have general warnings, to see, click here</a></td>
						   </tr>
					   </table>";
	}

	if($commandline) {
		echo "\nThis is commandline (show):<br>\n".$this->output."\n\n";
		return True;
	}




	$this->getVariable(["ajax"]);

	if($templatefile1<>'') {
		$this->templatefile=$templatefile1;
	} else {
		if($this->isadmin()) $this->templatefile="template_admin";
		elseif($this->isreseller) $this->templatefile="template_reseller";
		elseif(strstr($this->activeuser,'@')) $this->templatefile="template_emailuser";
		else $this->templatefile="template_domainadmin";

		if(!file_exists("templates/$this->template/$this->currentlanguage/".$this->templatefile.'_'.$this->currentlanguage.".html")) $this->templatefile="template";
		if(!file_exists("templates/$this->template/$this->currentlanguage/".$this->templatefile.'_'.$this->currentlanguage.".html")) {
			echo "<hr><b>Template file still not found: (templates/$this->template/$this->currentlanguage/".$this->templatefile.'_'.$this->currentlanguage.".html)</b><hr>";
		}

	}


	$this->selecteddomainstr=$this->navigation_bar();
	#$this->debugecho("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__."-".$point++." selecteddomainstr:".$this->selecteddomainstr,4,false);

	$stylefile="templates/".$this->template."/$this->currentlanguage/style.css";


	if($ajax) {
		header("Content-Type: text/html; charset=ISO-8859-9");
		echo "<html>
		<head>
		<meta http-equiv='content-Type' content='text/html; charset=ISO-8859-9' />
		<meta http-equiv='content-Type' content='text/html; charset=windows-1254' />
		<meta http-equiv='content-Type' content='text/html; charset=ISO-8859-1' />
		</head>
		<body>".$this->output."</body></html>";;
	}
	else {

		if($this->selecteddomain!="") {
			$inf=$this->getDomainInfo($this->selecteddomain);
			if($inf["timeout"]!="") {				
				$now=date("Y-M-d H:i:s");
				if($inf["timeout"]<$now) {
					$this->output.="Timeout: {$inf["timeout"]}  now: $now <br>";
					$this->output.="This domain is timed out. Click <a href='?op=editdomain&domainname={$this->selecteddomain}'>here to update timeout value</a><br>";
				}
			}
		}


		$templatedir='templates/'.$this->template.'/'.$this->currentlanguage;
		$this->debugecho2("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__."-****".$point++,4);

		$ehcpversion1=$ehcpversion;
		if($this->debuglevel>0) $ehcpversion1.="<br>Debuglevel: $this->debuglevel";
		$webserver=$this->miscconfig['webservertype'];
		if($webserver=='') $webserver='apache2';

		$this->templateEcho( // apply template and echo
			$this->templatefile,
			// bunlar sablonda kullanilacak tagler.
			['{webserver}','{domainname}','{domain}','{adminemail}',"{ehcpversion}","{ajaxscript}","{ajaxonload}","{username}","{logo}",'{myip}','{selecteddomain}','{quickdomains}','{banner}','{stylefile}','{templatedir}','{language}','{dynamicinfo}','{dynamicinfo2}','{known_to_work}'],
			// bunlar da tagler yerine konacak degiskenler.
			[$webserver,$this->selecteddomain,$this->selecteddomain,$this->conf['adminemail'],$ehcpversion1,$this->ajaxscript,$this->ajaxonload,$this->activeuser,$this->logo,$this->conf['dnsip'],$this->selecteddomainstr,$quickdomains,$this->miscconfig['banner'],$stylefile,$templatedir, $this->currentlanguage,$dynamicInfo,$dynamicInfo2,$known_to_work]
		);
	}

}


function templateEcho($template,$isaretler,$icerikler){

	$isaret="{ickisim}";
	$this->debugecho2("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__."-".$point++,4);
	$template=$this->loadTemplate($template);

	$output2=str_replace($isaret,$this->output,$template); // template icinde isareti ara, isaret yerine simdiye kadarki outputu koy.
	$output2=str_replace($isaretler,$icerikler,$output2);
	$this->debugecho2("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__."-".$point++,4);
	echo $output2; # final output
}


function showoutput($header='',$bodyonload='') {
	global $output;
	$output.=$this->output;
	header("Content-Type: text/html; charset=ISO-8859-9"); //ajax icin yazildi..

	echo "<html>
		<head>
		<meta http-equiv='content-Type' content='text/html; charset=ISO-8859-9' />
		<meta http-equiv='content-Type' content='text/html; charset=windows-1254' />
		<meta http-equiv='content-Type' content='text/html; charset=ISO-8859-1' />
		$header
		</head>
		<body $bodyonload>".$output."</body></html>";
}


function help() {
	$this->output.="This info is from ehcp.net site:<br><iframe marginwidth=0 marginheight=0 width=600 height=1200 scrollbars=none frameborder=0 scrolling=no src='http://www.ehcp.net/latest/help.html'></iframe>";
}


function todolist(){
	$this->output.="This info is from ehcp.net site:<br><iframe marginwidth=0 marginheight=0 width=600 height=1200 scrollbars=none frameborder=0 scrolling=no src='http://www.ehcp.net/latest/roadmap.html'></iframe>";


}


//------------- securitycheck, login logout functions...

function securitycheck() {
	// check login and show login page if needed. set activeuser here.
	global $kullaniciadi,$sifre,$isloggedin,$username,$password,$commandline;
	#echo "securitycheck...";


	if($this->op=="dologin") $this->dologin();

	$username=$_SESSION['loggedin_kullaniciadi'];
	$password=$_SESSION['loggedin_sifre'];
	$isloggedin=$_SESSION['isloggedin'];

	if(((!$isloggedin) or ($password==''))and(!$commandline)){
		$this->loginform(); # this exits at the same time herein..
	};
	# these are different variables, that may be used in legacy codes of ehcp.
	$this->activeuser=$username;
	$this->loggedin_kullaniciadi=$username;
	$this->loggedin_sifre=$password;
	$this->loggedin_username=$username;
	$this->loggedin_password=$password;
	$this->isloggedin=$isloggedin;


	//$this->output.=print_r2($_SESSION);
}

function fatalError($str){
	echo "<b>Fatal Error: $str</b>";
}

function fatalErrorExit($str){
	echo "<b><font size=+1>ehcp Fatal Error: $str</font></b>";
	exit();
}


function loadTemplate($templatefile,$strict=True){


	$templateengine="file"; # currently templating done through files under templates directory. before, it was from db, but many web developers are confused with html in db.. so, i swithced to html files..
	$this->debugecho("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__." Templatefile:($templatefile)",4,True);

	if($this->template=='') {
		$err="Template is not defined now. using English default template instead. (maybe caused by db error)<br>";
		$this->echoln($err);
		$this->template='default';
		$this->currentlanguage='en';
	}

	if($templateengine=="file"){
		$file="templates/$this->template/$this->currentlanguage/".$templatefile."_".$this->currentlanguage.".html";
		$this->debugecho("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__."file:($file)",4,True);

		if(!file_exists($file)){
			if(!$strict) return "";

			$err="Template file '$file' for this language ($this->currentlanguage) and template ($this->template) is not found. using English default template instead.<br>";
			$this->echoln($err);
			$this->template='default';
			$file="templates/$this->template/en/".$templatefile."_en.html";
			if(!file_exists($file)){
				echo $err;
				$this->fatalErrorExit("$file template file is not found...  ");
			}
		 }

		if($this->debuglevel>3) echo "file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__.'-template:'.$this->templatefile."-default:$this->defaultlanguage-current:$this->currentlanguage-session.cur:".$_SESSION['currentlanguage']."<br>";
		if($this->debuglevel>3) print debug_backtrace2();

		$ret='';
		$ret=@file_get_contents($file);
		#echo "curr template: $this->template , temp file: $file <hr>";
		if(($ret===false)and $strict) $this->fatalError("Template File: $file cannot be loaded... ");
	} else {
		$html=$this->htmlekle2($templatefile."_".$this->currentlanguage);
		if($html=='') $html=$this->htmlekle2($templatefile."_en");

	}
	return $ret;
}

function loginform(){
	if($this->debuglevel>3) echo "file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__.'-template:'.$this->templatefile."-default:$this->defaultlanguage-current:$this->currentlanguage-session.cur:".$_SESSION['currentlanguage']."<br>";
	$this->debugecho2("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__,4);
	$this->showexit('loginpage');
}

function doLoginEmailUser($username,$password){
	$this->debugecho2("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__,4);
	return $this->dologin2($username,$password,'','',$this->conf['emailuserstable']);
}

function dologin(){
	global $kullaniciadi,$sifre,$isloggedin,$username,$password,$clientip;
	$this->getVariable(["kullaniciadi","username","password"]);
	$username=strtolower($username); # reason: some panel users type in Admin and cannot do admin ops...

	$this->debugecho2("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__,4);

	$this->checkTables(); # check tables for missing fields, once on each login
	$this->check_ehcp_version();
	$sayi=$this->recordcount('log',"panelusername='$username' and (notified is null or notified='') and ip='$this->clientip'");
	if($sayi>5) return failed_login("brute force detected"); # if failed login for this ip 5 times, disallow brute force


	if(strstr($username,'@')) return $this->doLoginEmailUser($username,$password);
	else return $this->dologin2($username,$password);
}



function isPasswordOk($username,$password,$usernamefield='',$passwordfield='',$logintable1=''){
# only does password comparison
	if($logintable1=='') $logintable=$this->conf['logintable'];
	else $logintable=$logintable1;

	if(!$usernamefield) $usernamefield=$logintable['usernamefield'];
	if(!$passwordfield) $passwordfield=$logintable['passwordfield'];
	if(!$usernamefield) $usernamefield='username';
	if(!$passwordfield) $passwordfield='password';

	if ($logintable['passwordfunction']=='') {
		$where="$usernamefield='$username' and '$password'=$passwordfield";
	} elseif($logintable['passwordfunction']=='encrypt'){
		$where="$usernamefield='$username' and ".$logintable['passwordfunction']."('$password','ehcp')=$passwordfield";
	} else {
		$where="$usernamefield='$username' and ".$logintable['passwordfunction']."('$password')=$passwordfield";
	}

	$where.=" and status='".$this->status_active."'";
	$this->debugecho2("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__.": $username,$password,$usernamefield,$passwordfield,$logintable, query where: ($where) <BR>",4);

	$sayi=$this->recordcount($logintable['tablename'],$where);
	if($sayi===false){
		$this->error_occured("dologin2");
		return false;
	}

	if($sayi==0) {
		return false;
	} elseif($sayi>0) {
		return True;
	}
}

function failed_login($comment=""){
		$this->debugecho2("<hr>user/pass is not correct....",2);
		$this->executeQuery("insert into log (tarih,panelusername,ip,log)values(now(),'$username','$this->clientip','Failed Login Attempt ($comment)')");
		$userIP = $_SERVER['REMOTE_ADDR'];
		$f2banDate = date("M d H:i:s");
		$this->log_to_file("log/ehcp_failed_authentication.log","$f2banDate EHCP authentication failed attemping to login as user $username from $userIP\n");
		return $this->errorText("Wrong username/password.");
}

function dologin2($username,$password,$usernamefield='',$passwordfield='',$logintable=''){
# sets session values if password comparison succeeds..
	$this->debugecho2("file:".__FILE__.", Line:".__LINE__.", Function:".__FUNCTION__.": $username,$password,$usernamefield,$passwordfield,$logintable<BR>",4);

	if($this->isPasswordOk($username,$password,$usernamefield,$passwordfield,$logintable)) {
		$this->debugecho2("<hr>logging in user....",2);
		$_SESSION['loggedin_kullaniciadi'] = $username;
		$_SESSION['activeuser'] = $username;
		$_SESSION['loggedin_sifre']=$password;
		$_SESSION['loggedin_username'] = $username;
		$_SESSION['loggedin_password']=$password;
		$_SESSION['isloggedin']=True;

		$this->isloggedin=True;
		$this->loggedin_kullaniciadi=$username;
		$this->loggedin_sifre=$password;
		$this->loggedin_username=$username;
		$this->loggedin_password=$password;
		$this->activeuser=$username;

		$_SESSION['currentlanguage']=$this->defaultlanguage;


		# load user config, and set default domain and other, if any..
		$this->userconfig=$this->loadConfigIntoArray("select * from misc where panelusername='$this->activeuser'");
		if($this->userconfig['defaultdomain']<>'' and $this->selecteddomain=='') {
			$this->setselecteddomain($this->userconfig['defaultdomain']);
		}

		if(defined('vps_file_loaded')) $this->addDaemonOp('daemon_vps','vps_check_state','xx');

		return True;
	} else {
		return $this->failed_login();
	}
	//echo "<hr>dologin2 bitti..sayi:($sayi)</hr>";
}

function log_to_file($logFile,$logstr){
 /* Log Failed Authentication For Use with Fail2Ban ; by own3mall*/
		if(file_exists($logFile)) {
			// Size check larger than 20MBs
			if(filesize($logFile) >= 20971520){
				$newName = $logFile . "_" . date("d-m-Y_H:i:s");
				rename($logFile, $newName);

				// Create the new log file
				$authLog = fopen($logFile, "x+");
				if($authLog){
					fclose($authLog);
				}
				chmod($logFile, 0644);
			}
		} else {
			// Create the file
			$authLog = fopen($logFile, "x+");
			if($authLog) {
				fclose($authLog);
			}

			chmod($logFile, 0644);
		}

		// Get contents of Authentication Log and add a new entry
		$fp=@fopen($logFile,'a');
		@fwrite($fp,$logstr);
		@fclose($fp);
}

function dologin3($tablo,$username,$password,$usernamefield,$passwordfield,$md5=''){
	// farkli tablodan user dogrulamaya izin verir...
	# bu pek kullanilmiyor artik... ispasswordOk is goruyor...
	if ($md5=='md5') {
		$where="$usernamefield='$username' and md5('$password')=$passwordfield";
	} else {
		$where="$usernamefield='$username' and '$password'=$passwordfield";
	}

	$sayi=$this->recordcount($tablo,$where);
	if(!$sayi) return false;

	if($sayi==0) {
		$this->output.="Wrong username/password.:<br>";
		return false;
	} elseif($sayi>0) {
		$_SESSION['loggedin_kullaniciadi'] = $username;
		$_SESSION['isloggedin']=True;
		$_SESSION['loggedin_sifre']=$password;
		$this->isloggedin=True;
		$this->loggedin_sifre=$password;
		return True;
	}
}


function logout(){
	$this->logout2();
	$this->loginform();
}

function logout2(){
	$_SESSION['loggedin_kullaniciadi'] = '';
	$_SESSION['loggedin_sifre']='';
	$_SESSION['loggedin_username'] = '';
	$_SESSION['loggedin_password']='';
	$_SESSION['isloggedin']=false;

	$this->isloggedin=false;
	$this->loggedin_kullaniciadi='';
	$this->loggedin_sifre='';
	$this->loggedin_username='';
	$this->loggedin_password='';

	session_unset();
	session_destroy();
	return True;
}

//------------------ end of securitycheck functions


function checkInstall() {
	// check if installed, and install if not. to be coded later.
	if(!$this->checkinstall) return false;
	$this->checkdaemon();
	if(!$this->isinstalled) $this->installehcp;
	return True;
}

function isinstalled(){
	//? to be coded later.
	return True;
}

function installehcp(){
	// to be coded later.
	return True;
}

function phpinfo(){
	$this->requireAdmin();
	$this->output.=phpinfo(); // may be disabled for security
}

function getMyDomains($filt=''){
	if($this->isadmin()) $filt='';
	else $filt="panelusername='$this->activeuser' or reseller='$this->activeuser'";
	return $this->getDomains($filt);
}


function getDomains($filt=''){
	$domtable=$this->conf['domainstable']['tablename'];
	$q="select * from $domtable";
	if($filt<>'')$q.=" where $filt";
	return $this->query($q);
}

function get_subdomains($filt=''){
	$q="select * from subdomains";
	if($filt<>'')$q.=" where $filt";
	return $this->query($q);
}


//=================================== below are functions for or related to daemon mode ..
//	operation,add/delete, user/domainname, userpass etc.... 4 parameters.

function checkPort($server, $port) {
	$conn = @fsockopen($server, $port, $errno, $errstr, 2);
	if ($conn) {
		fclose($conn);
		return True;
	}
	return false;
}

function checkPort2($portno){
	if(!$this->checkPort('localhost',$portno)) $ret="Port $portno problem with your server.";
	return $ret;
}

function checkPorts(){
	$portstocheck=[22,25,53,80,110,143];
	foreach($portstocheck as $port)
		$ret.=$this->checkPort2($port);
	if($ret<>'') {
		$this->infotoadminemail("Problem with server services, ports: $ret",'ehcp-Your server, services-ports problem');
	}
}

function updateDiskQuota($domainname=''){ # this function coded by deconectat
	global $skipupdatediskquota;
	if($skipupdatediskquota or $this->skipupdatediskquota) {
		echo "\n".__FUNCTION__.": not updateing disk quota, because $skipupdatediskquota variable is set. \n";
		return;
	}

	$this->requireCommandLine(__FUNCTION__);
	$q="select id,homedir,domainname from domains";
	if($domainname<>'') $q.=" where domainname='$domainname'";

	$res=$this->query($q);
	echo "Starting ".__FUNCTION__."\n";
	foreach($res as $dom){
		$quota = $this->executeProg3("/usr/bin/nice -n 19 du -ms ".$dom['homedir']);  # -ms: meausre MB
		#echo "\n---------------------\nQuota info: $quota \n---------------\n";
		$quota = explode ("\t", $quota);
		$quotaused=trim($quota[0]);
		if($quotaused=='') $quotaused='0';
		echo "\nUpdated disk quota: ".$dom['domainname'].":$quotaused | ".$dom['homedir']."\n";
		$this->executeQuery("update domains set diskquotaused=$quotaused where id=".$dom['id']);
	}
	$this->checkOverDiskQuota();
	echo "\nfinished ".__FUNCTION__."\n";
	return True;
}

function checkOverDiskQuota(){ # coded by deconectat, modified by ehcpdeveloper
	# update normal quota
	$this->executeQuery("update domains set diskquotaovernotified=0,status='active' where diskquota>=diskquotaused and status='overquota' ");

	# make passive, domains with high quota and who had been notified, and notify them of passify
	# panel admin is able to set type of action for over-quota domains, admain may just warn them, or disable domains automatically.
	# this may cause problem on some systems if updateDiskQuota calcluates disk quota wrong using du system command... so i left an option to disable this by admin.. default for turnoffoverquotadomains is disabled for now.

	if($this->miscconfig['turnoffoverquotadomains']<>''){
		$this->executeQuery("update domains set status='overquota' where DATEDIFF(curdate(),diskquotaoversince)>graceperiod and diskquotaovernotified=1");
		$this->syncDomains();
		$warn="Your site is disabled. Please contact your provider";
		$warn2="Your site will be disabled";
	} else {
		$warn="";
		$warn2="Please solve this";
	}
	$footer="\nSent from your panel, Easy Hosting Control Panel (ehcp.net), url:http://".$this->dnsip."\n";

	# warn people with high quota, and who were notified before..
	$res=$this->query("select * from domains  where DATEDIFF(curdate(),diskquotaoversince)>graceperiod and diskquotaovernotified=1");
	if($res)
	foreach($res as $dom){
		$this->infoEmailToUserandAdmin($dom['email'],"Domain ".$dom['domainname']." is over quota!","Panelusername:".$dom['panelusername'].",Domain: ".$dom['domainname']." is over quota! You are using ".$dom['diskquotaused']."MB of your ".$dom['diskquota']."MB quota. $warn . $footer",false);
	}


	# warn people with high quota
	$res=$this->query("select d.domainname,d.diskquotaused,d.diskquota,d.graceperiod,p.email,p.panelusername from domains d,panelusers p where d.diskquota<d.diskquotaused and d.diskquota>0 and d.diskquotaovernotified=0 and d.panelusername=p.panelusername");
	if($res)
	foreach($res as $dom){
		$this->infoEmailToUserandAdmin($dom['email'],"Domain ".$dom['domainname']." is over quota!","Panelusername:".$dom['panelusername'].",Domain: ".$dom['domainname']." is over quota! You are using ".$dom['diskquotaused']."MB of your ".$dom['diskquota']."MB quota. $warn2 in ".$dom['graceperiod']." days. $footer",false);
		$this->executeQuery("update domains set diskquotaovernotified=1, diskquotaoversince=CURDATE() where domainname='".$dom['domainname']."'");
	}
}


function securedelete($files,$serverip){
	// to be able to securely delete files. to prevent accidental deletion of crucial data.
	// once, i deleted some ;)
	if(is_array($serverip)) $serverip=$serverip['ip'];  # accept both until all code is standard.

	if(strpos($files,'..')!==false) return $this->errorText("Deleting files forbidden: contains (..): ($files)",True);

	$nodelete=['',"/","/var/www",$this->ehcpdir,$this->vhostsdir,"/bin","/boot","/cdrom","/dev","/ehcp","/etc","/home","/initrd","/initrd.img","/lib","/lost+found","/media","/mnt","/opt","/proc","/root","/sbin","/srv","/sys","/tmp","/usr","/var","/vmlinuz","/web"];
	foreach($nodelete as $dir) $nd3[]=$dir."/";
	if(in_array($files,$nodelete) or in_array($files,$nd3)) {
		return $this->errorText("Deleting files forbidden-1: ($files)",True);
	}

	$nodelete2=["/bin","/boot","/cdrom","/dev","/ehcp","/etc","/initrd","/initrd.img","/lib","/lost+found","/media","/mnt","/opt","/proc","/root","/sbin","/srv","/sys","/tmp","/usr","/vmlinuz","/web"];
	foreach($nodelete2 as $dir) $nd4[]=$dir."/";

	foreach($nodelete2 as $dir){
		$len=strlen($dir);
		$sub=substr($files,0,$len);
		if(strstr($files,"/etc/vsftpd_user_conf/")) continue ; # except for vsftpd_user_conf
		//$this->echoln("checked for delete: substr: $sub, dir:$dir ");

		if($sub==$dir){
			return $this->errorText("Deleting files forbidden-2: ($files)",True);// this function already returns false... so, only one line of code...
		}
	}

	foreach($nd4 as $dir){
		$len=strlen($dir);
		$sub=substr($files,0,$len);
		//$this->echoln("checked for delete: substr: $sub, dir:$dir ");
		if(strstr($files,"/etc/vsftpd_user_conf/")) continue ; # except for vsftpd_user_conf

		if($sub==$dir){
			return $this->errorText("Deleting files forbidden-3: ($files)",True);
		}
	}


	$this->echoln("checks complete.. removing files: $files");

	$cmds=[];
	$cmds[]="rm -rvf $files";
	if(trim($serverip)=='') $serverip='localhost';

	return $this->execute_server_commands($serverip,$cmds);
}

function syncFtp(){
	$this->requireCommandLine(__FUNCTION__);
	passthru2("mkdir -p /etc/vsftpd_user_conf");

	$rs = $this->conn->Execute("select * from ".$this->conf['ftpuserstable']['tablename']. " where homedir<>''");
	if ($rs) { # this part is only necessary with vsftpd,  # prepares non-standard home dir,
		echo "\n========================================================================================== syncFtp:\n";
		while (!$rs->EOF) {
			$homedir=$rs->fields['homedir'];
			$ftpusername=$rs->fields['ftpusername'];
			passthru2("mkdir -p $homedir ");
			passthru2("chown -Rf $this->ftpowner $homedir ");
			writeoutput2("/etc/vsftpd_user_conf/$ftpusername","local_root=$homedir",'w');
			$rs->MoveNext();
		}
	}
	touch($this->ehcpdir."/rebuild_ftp_users");
	return True;
}


function restart_ftp_daemon() {
	# I am testing this new ftp server system.. python based ftp server, using pyftpdlib
	$this->requireCommandLine(__FUNCTION__);
	print "\nRestarting ftp server..\n";
	print debug_backtrace2()."\n";

	$cmd="ps aux | grep [f]tp_server | grep -v sudo | awk '{print $2}' | xargs kill -9";
	print "$cmd \n";
	shell_exec($cmd); # stop py based ftp server
	sleep(2);
	return $this->start_ftp_daemon();
}

function start_ftp_daemon() {
	# I am testing this new ftp server system.. 
	$this->requireCommandLine(__FUNCTION__);
	print "\nStarting ftp server..({$this->ftp_server_progname})\n";

	touch("/var/log/ehcp_ftpd.log"); # fix logfile if not done..
	chmod("/var/log/ehcp_ftpd.log", 0777);

	$cmd="sudo -u vsftpd {$this->ehcpdir}/{$this->ftp_server_progname} &";
	echo("Running: $cmd \n");
	Proc_Close(Proc_Open ($cmd, [], $foo));
	return True;
}

function sync_ftp_daemon() {
	# difference from prev version: does not restart whole python ftp server, just reloads users in ftp server.. by a signal
	# will send just USR1 signal to ftp_server2.py
	$pid=$this->get_pid($this->ftp_server_progname);

	if($pid=="") {
		print "!!! pid of ftp server cannot be determined.. Trying to start ftp daemon.. \n\n";
		$this->start_ftp_daemon();
		sleep(3);
	}
	
	$pid=$this->get_pid($this->ftp_server_progname);
	if($pid=="") {
		print "!!! pid of ftp server still cannot be determined.. cannot sync ftp daemon.. \n\n";
		return;
	} else {
		$this->executeProg3("kill -USR1 $pid");
		print "Sent USR1 signal to {$this->ftp_server_progname}, pid: $pid, should reload ftp users.. \n\n";
	}
	return True;
}

function daemonftp($action,$info,$info2,$info3=''){

	$this->requireCommandLine(__FUNCTION__);
	switch($action){
		case "syncftp": // restart ftp daemon, if needed... in some cases.. whin in vsftp/pureftp, no restart needed, but, with pyftp, restart needed..in current design.
			return $this->sync_ftp_daemon();
		break;

		case "delete": // deleting an ftp account
			$this->securedelete($info,$info3);
			return True;
		break;

		case 'add':
			passthru2("mkdir -p ".$info);
			passthru2("chown -Rf $this->ftpowner $info");
			$this->syncFtp();
			return True;
		break;

		case 'multiserver_add':
			$this->commands=[];
			$this->commands[]="mkdir -p $info";
			$this->commands[]="chown -Rf $this->ftpowner $info";
			# multiserver sync ftp may be needed here.. 
			$this->execute_server_commands($info2,$this->commands);
			$this->commands=[]; # make sure it is empty.
			return True;
		break;

		case $this->status_passive: // changing status to passive
			passthru2("chown nobody:nogroup -Rf ".$info);
			passthru2("chmod og-rw -Rf ".$info);
			return True;
		break;
		case $this->status_active:
			passthru2("chown $this->ftpowner -Rf $info");
			passthru2("chmod a+r -Rf ".$info);
			return True;
		break;


	}
}


function add_daemon_op($named_params){
	$this->debugecho(__FUNCTION__.": sending info to daemon (".$named_params['op'].":".$named_params['action'].")",1,false);
	$this->debugecho($named_params,3,false);


	$ret=$this->executeQuery("insert into operations (op,user,ip,action,info,info2,info3,tarih) values ('".$named_params['op']."','$this->activeuser','$this->clientip','".$named_params['action']."','".$named_params['info']."','".$named_params['info2']."','".$named_params['info3']."',now())",' sending info to daemon ('.$opname.')');
	# $ret=$this->conn->AutoExecute('operations',$named_params,'INSERT'); # this does not work.

	#$this->debugecho($this->queries,3,false);
	return $ret;

	#return $this->executeQuery("insert into operations (op,action,info,info2,info3,tarih) values ('$op','$action','$info','$info2','$info3','')",' sending info to daemon ('.$opname.')');
}

function addDaemonOp($op,$action,$info,$info2='',$opname='',$info3=''){
	return $this->executeQuery("insert into operations (op,user,ip,action,info,info2,info3,tarih) values ('$op','$this->activeuser','$this->clientip','$action','$info','$info2','$info3',now())",' sending info to daemon ('.$opname.')');
}

function check_remote_ssh_connection($server){
	return True; # to be coded later
}

function daemon_backup_domain($info){
	$domaininfo=$this->domaininfo=$this->getDomainInfo($info);
	echo __FUNCTION__." basliyor... for $info".print_r($domaininfo);
	$backupbasedir="/var/www/new/backups";
	@mkdir($backupbasedir);
	chdir($backupbasedir);

	$where=$domaininfo['homedir'].'/httpdocs';
	$filename="$backupbasedir/$info-backup-".date('Y-m-d_H-i-s').'_'.rand(1,1000).'_'.rand(1,1000).'.tgz';
	$files="$backupbasedir/$info/$info-backup-files.tgz";
	$mysql="$backupbasedir/$info/$info-backup-mysql.txt";
	@mkdir("$backupbasedir/$info");

	$cmd="tar -zcvf $files $where";

	$this->executeQuery("update backups set filename='$filename',status='backup started by daemon',date=now() where domainname='$info' and (filename is null or filename='' or status like '%backup started%')");
	passthru2($cmd);
	$this->backup_databases("domainname='$info'",$mysql);
	passthru2("tar -zcvf $filename $info"); # tar again files and mysql which are in dir of $domainname=$info
	passthru2("rm -rf $backupbasedir/$info");

	$this->executeQuery("update backups set filename='$filename',status='backup finished by daemon, ready to download',date=now() where domainname='$info' and filename='$filename'");
	chdir($this->ehcpdir);
	return True;
}

function daemondomain($action,$info,$info2='',$info3=''){// domain operations in daemon mode.

	/*
	action: add or delete, what to do
	info: domain to delete/add
	info2: whatever info needed, such as user to which domain belongs, or changed: homedir of domain, that is: /var/www/vhosts/ftpusername/domain.com
	*/

	$this->requireCommandLine(__FUNCTION__);
	$base=$this->conf['vhosts'];
	/*
	domain path will be like: /var/www/vhosts/ftpusername/domain.com
	/var/www/vhosts/ftpusername/domain.com will be stored as homedir in domains table,
	one user will may have multiple domains with single ftp acount.
	*/

	$info=$domainname=trim($info); # domainname
	$info2=trim($info2);
	$info3=trim($info3);
	$homedir=$info2;

	$this->echoln2("(daemondomain) domain operation starts: ".$info.", homedir:".$homedir);

	echo "\n".__FUNCTION__.":action:($action),info:($info),info2:($info2),info3:($info3)\n";

	switch($action){
		case "multiserver_add_domain":
			$info3=trim($info3);
			if($info3=='') {
				echo "\n info3 is empt. cannot complete $action \n";
				return True; # actually should be false, left True during development stage.
			}

			if(!$this->check_remote_ssh_connection($info3)) return false; # should be equivalent, but do not work: $this->check_remote_ssh_connection($info3) || return false;

			$this->commands=[];
			# all domain dirs should be setup here..
			$this->initialize_domain_files($homedir);
			$this->commands[]="echo 'Under Construction-multi-server-ehcp' > $homedir/httpdocs/index.php";
			$this->execute_server_commands($info3,$this->commands);
			$this->commands=[]; # ensure it is empty after our job finished.

			return True;
		break;

		case "add":
			# all domain dirs should be setup here..
			#$params=array('domainname'=>$domainname,'homedir'=>$homedir);
			#$this->initializeDomainFiles($params); # done in syncdomains below.
			$this->syncDomains('',$domainname); # only sync newly added domain.
			return True;
		break;
		case "delete":
			echo "deleting: $info \n";
			if($info3=='') {
				$info3='localhost';
			}

			$this->commands=[];
			$this->commands[]="rm -Rvf $homedir";
			$this->commands[]="rm -Rvf ".$this->conf['namedbase'].'/'.$info;
			$this->execute_server_commands($info3,$this->commands);
			return True;
		break;

		case 'addsubdomain':
			passthru2("mkdir -p $info3");
			return True;
		break;

		case "delsubdomain":
			echo "deleting: $info \n";
			passthru2("rm -Rvf $homedir");
			return True;
		break;

		default: 
			echo "undefined action in ".__FUNCTION__.": $action";

		return False;
	}
}


function daemon(){
	global $emailenable;

	set_time_limit(0); # run forever... 
	$this->requireCommandLine(__FUNCTION__); # run from commandline.
	$this->echoln2("Running daemon now..");
	
	if($emailenable) {
		$sleep_interval=10;
	} else {
		$sleep_interval=5; # generally testing mode.. 
	}

	$mail_interval=3600*24; # send info to ehcp developer every 1 day. may be disabled by user here.like ping..
	$mail_last_sent_time=3600*11; # send first 1 hours later.


	$this->output.="Daemonized..".$this->myversion."\n__________________________\n\n";
	$i=$say=0;
	$this->updateWebstats();
	passthru2("chmod a+x /var/spool/postfix/var/run/saslauthd"); # for the bug/problem at http://www.ehcp.net/?q=node/149#comment-668
	@mkdir($this->ehcpdir.'/webmail2'); # make dir if not present
	@mkdir($this->ehcpdir.'/upload');
	$this->executeProg3("chmod a+w ".$this->ehcpdir.'/upload');
	$this->executeProg3("chmod a+w ".$this->ehcpdir.'/LocalServer.cnf');

	while(True) {# run forever
	# first check operations that have more than one parameter, ie, info<>''
	# niye bi ingilizce bi turkce yazdin derseniz: bu uluslararasi bir program o yuzden ingilizce var, ama bi Turk izi de olsun diye... orjinalinde Turk yapimi yani... hehe...

		$rs=$this->query("select * from operations where ((status is null)or(status<>'ok'))and(try<2)and(info<>'')");
		if(count($rs)>0) print_r($rs);

		# read op db and execute it. donot try if failed 3 or more. sadece op olanlar. info varsa bu domain ekleme/cikarmadir. info parametredir. action da binevi parametre.
		if($rs) {
		foreach($rs as $op){
			# increase try count here, if runop fails somehow, such as fatal error, it will not repeat forever,
			# in previous versions, try increase was below, in fail, sometime if fatal error occurs, it entered to infinite loop, so i moved try increase code to top of foreach loop
			#$this->executeQuery("update operations set try=try+1 where id=".$op['id']." limit 1",' updating operations, increasing try count ');
			$this->output='';
			if($this->runop2(trim($op['op']),$op['action'],$op['info'],$op['info2'],$op['info3'])) {
				echo "\ndaemon->runop2 success ** \n";
				$this->executeQuery("update operations set try=try+1,status='ok' where id=".$op['id']." limit 1",' updating operations ');

			} else {
				$q="update operations set try=try+1,status='failed' where id=".$op['id']." limit 1";
				echo "\ndaemon->op2 failure **** : $q
					Because the result of prev. command failed/returned False:
					{$op['op']}, {$op['action']}, {$op['info']}, {$op['info2']}, {$op['info3']}
				";
				$this->executeQuery($q,' daemon increasing try count');
			}
			echo $this->output;
			}
		} else {
			# $this->error_occured("daemon main loop");
			if($rs===false)$this->tryReconnect();
		}

	# second, check operations that have only one parameter..i.e. info=''
		$rs=$this->query("select * from operations where ((status is null)or(status<>'ok'))and(try<3)and(info is null or info='')");
		if(count($rs)>0) print_r($rs);
		# read op db and execute it. do not try if failed 5 or more. sadece op olanlar. info varsa bu domain ekleme/cikarmadir. info parametredir. action da binevi parametre.
		if($rs) {
		foreach($rs as $op){
			#$this->executeQuery("update operations set try=try+1 where id=".$op['id']." limit 1",' updating operations, increasing try count ');
			$this->output='';
			if($this->runOp(trim($op['op']))) {
				echo "\ndaemon->runop success ** \n";
				$this->executeQuery("update operations set try=try+1,status='ok' where id=".$op['id']." limit 1",'update operations set status ok.');
			} else { # increase try count
				$q="update operations set try=try+1,status='failed' where id=".$op['id']." limit 1";
				echo "\ndaemon->runop failure **** : $q\n";
				$this->executeQuery($q,' increasing try count');
			}
			echo $this->output;
			}
		} else {
			if($rs===false)$this->tryReconnect();
		}

		if($mail_interval>0){
			# send info to developer every 1 day. may be disabled by user here.like ping..
			# this may be disabled by you, for statistical purposes..
			$mail_last_sent_time+=$sleep_interval;

			if($mail_last_sent_time>=$mail_interval){
				$mail_last_sent_time=0;
				$ip=$this->getlocalip2();
				# collect any errors from ehcp.log, for debugging of ehcp and programs
				$ip2=$this->get_outside_ip();


				$msg.=$this->executeProg3("grep -i error /var/log/ehcp.log | grep -v error_log | tail -300;ps aux");
				$this->infoMailUsingWget($this->myversion.'-ehcp_daemon_running_at_ip:'.$ip);# in case php mail function does not work.
				mail('debug@ehcp.net',$this->myversion."-ehcp_daemon_running_at_ip2:$ip - $ip2",$msg,"From: ".$this->emailfrom);
			}
		}

		echo "\nehcp ".$this->myversion."- Daemon loop number:$i  Datetime:(".date_tarih().")\n-----------daemon suspended for $sleep_interval sec ---------pwd:(".getcwd().") \n";

		if($i % 200 == 0) {
			# Every 200 Loops
			$this->updateWebstats();
		}

		# this caused problem especially for file upload scripts,
		# Because scripts use www-data as user, while this syncDomains does owner of all files vsftpd...
		#if($i/50==round($i/50)) { # on every 50 sleep interval,
		#	$this->syncDomains(); # this may slow down a bit daemon, may be disabled, only for rebuilding domains in case some log files are deleted accidentally by someone...
		#}

		if($i % 5 == 0) {
			# her 5 loopda bir mysql yeniden baglan..
			# if mysql goes away while daemon runs, this will refresh connection, so, operations can continue..
			$this->check_mysql_connection();
			if($i>0) $this->check_webserver();
		}

		if($i % 50 == 0) {
			# her 50 loopda bir dyndns kontrol et.
			$this->checkDynDns();
			$this->daemonQuotaCheck();
			$this->call_func_in_module('vps_module','vps_check_state');
		}

		$ops=$this->recordcount('operations',"((status is null)or(status<>'ok'))and(try<3)and(info is null or info='')");
		if($ops==0) {
			sleep($sleep_interval);
		} else sleep(1);# sleep long only if there are no remaining ops.
		$i++;
		# infinite loop...
	}
}

function get_outside_ip(){
	$url="http://www.ehcp.net/diger/myip.php?mydnsip=".$this->miscconfig['dnsip'].'&adminemail='.$this->conf['adminemail'].'&serverid='.$this->miscconfig['server_id']; # this url is used to check your external ip. if that url is not reachable somehow, you may use a similar url. the output of url should be directly ip in format xxx.yyy.ddd.zzz
	# server will be identified using adminemail and server_id. And, in future, dns of that server will be updated in central ehcp dyn_dns service. Currently, server admins needs to use external dyndns

	$str=trim(file_get_contents($url));

	return $str;
}

function checkDynDns(){
	# this only works if current url is reachable on web,
	#$url="http://checkip.dyndns.com";
	if($this->miscconfig['updatednsipfromweb']=='') return false;

	$str=$this->get_outside_ip();
	print __FUNCTION__.": dyndns information from web: ($str) \n";
	if($str==''){
		print "dyn dns information could not get from web ($url) \n";
	} else {
			if($this->miscconfig['dnsip']<>$str){
				print "updating dns information according to one obtained from web as ($str)\n";
				# Update freedns.afraid.org:
				if($this->miscconfig['freednsidentifier']<>'') {
					# thanks to Kris Sallee http://sallee.us for contribution.
					echo "updating freedns.afraid.org \n";
					$updateurl="https://freedns.afraid.org/dynamic/update.php?".$this->miscconfig['freednsidentifier'];
					$update=file_get_contents($updateurl);
				}

				# Update ehcp configs
				$this->setConfigValue('dnsip',$str);
				$this->fixMailConfiguration(); # fix everything related to dns ip
			} else {
				print "no dns IP change needed, your dynamic IP is not changed\n";
			}
	}

}

function daemonQuotaCheck(){ #updatequota
	# checks quota at regular intervals, as defined in options/misc table
	$this->requireCommandLine(__FUNCTION__);

	$lastupdate=trim($this->miscconfig['lastquotaupdate']);
	$tarih=date_tarih();

	if($lastupdate==''){
		$this->setConfigValue('lastquotaupdate',$tarih);
		$this->loadConfig();
	}

	# calculte if last update is more than update interval
	$fark=timediffhrs($this->miscconfig['lastquotaupdate'],$tarih);
	if($fark>$miscconfig['quotaupdateinterval']){
		echo "\nQuota update needed...\n";
		$this->setConfigValue('lastquotaupdate',$tarih);
		$this->loadConfig();
		$this->updateDiskQuota();
	}
	return True;
}

function tryReconnect(){
	$this->conn->close();
	print "trying re-connecting to mysql db..\n";
	if($this->connectTodb2()){
		 print "\n\nreconnect to mysql successfull.\n";
	} else {
		echo "\n\nehcp->cannot re-connect to mysql db..exiting to let php reload...\n";
		exit();
	}
}

function syncAll(){
	$this->requireCommandLine(__FUNCTION__);

	return (
		$this->syncDomains() and
		$this->syncDns()
	);
}


function checkdaemon(){
	# first, check if this is able to see root processes,
	$res=executeprog("ps aux | grep root | grep -v grep | grep init ");
	if (strlen($res)<10) {
		$this->warnings.="<b>it seems that this process is not able to see all processes, so, i cannot check if ehcp daemon is running...This does not mean an error, you only need to check your ehcp daemon manually</b>";
		return false; # then
	}

	// check if daemon is running.
	$res=executeprog("ps aux | grep 'php index.php' | grep root |  grep -v grep ");
	if (strlen($res)>10) return True;
	else {
		$this->warnings.="<font size=+1><br><b>Attention! <br> ehcp daemon not running !<br>
Please run it from command/line by: <br>

sudo /etc/init.d/ehcp start <br>

<br /></b></font>";
		if(!$this->isloggedin) $this->infotoadminemail('daemon not running.'.$this->clientip,'daemon not running...'.$this->clientip); # with if, reduce mail traffic..
		return false;
	}
}

function getMailServer(){
	if($this->singleserverip<>'') return $this->singleserverip;
	else $this->output.="<b>mail server is not defined</b>";
	# serverplan oku, ona gore server adresini al..
}

function getDnsServer(){
	if($this->singleserverip<>'') return $this->singleserverip;
	else $this->output.="<b>dns server is not defined</b>";
	# serverplan oku, ona gore server adresini al..
}

function getWebServer(){

	if($this->singleserverip<>'') {
		$ret=$this->singleserverip;

		if($this->miscconfig['localip']<>'' and $this->miscconfig['dnsip']<>$this->miscconfig['localip']){
			$ret=$this->miscconfig['localip']; # Case webserver*: for private ip's that are used in local nets,(which are nat'ed by modem/router to outer world) otherwise, apache cannot bind to real ip, which is not assigned to server.
		}

		if($this->miscconfig['activewebserverip']<>'') $ret=$this->miscconfig['activewebserverip'];

		return $ret;
	} else {
		$this->output.="<b>web server is not defined</b>";
	}
	# serverplan oku, ona gore server adresini al..
}

function get_webserver_real_ip(){
	# get extrenal real ip of webserver. other function returns local ip of server for apache.
	if($this->singleserverip<>'') {
		return $this->singleserverip;
	} else {
		$this->output.="<b>web server is not defined</b>";
	}
}


function dnsZoneFiles($arr){// for daemon mode
	# reverse dns burda zone dosyalarini olusturmali.. http://langfeldt.net/DNS-HOWTO/BIND-9/DNS-HOWTO-5.html
	$this->requireCommandLine(__FUNCTION__);
	$success=True;

	//$this->output.=print_r2($arr);
	//print_r($arr);
	$alanlar=alanlarial($this->conn,"domains");
	$replacealanlar=arrayop($alanlar,"strop");  # puts each field in {}
	$replacealanlar[]='{customdns}';

  // Get master DNS template
	$dnstemplatefile=file_get_contents($this->dnszonetemplate);

	$mailserverip=$this->getMailServer();
	$dnsserverip=$this->getDnsServer();
	$webserverip=$this->get_webserver_real_ip(); # burada aslinda birden ??ok IP almasi lazim.

	if($this->isPrivateIp([$mailserverip,$dnsserverip])){
		$mesaj="your ehcp server has a private ip for either mailserver, dnsserver, webserver or all: $mailserverip,$dnsserverip,$webserverip , This may cause some problem";
		$subject="your ehcp server dns ip problem $dnsserverip";
		$this->infotoadminemail($mesaj,$subject);
	}

	echo __FUNCTION__.": mailserverip: $mailserverip, dnsserverip: $dnsserverip, webserverip: $webserverip \n";

	foreach($arr as $ar1){
		#farkli IP lerde host edilen domainler icin
		list($webserver1)=explode(',',$ar1['webserverips']); # sadece ilk ip yi al, aslinda birden cok IP yi de alabilmesi lazim.

		# assign ip addresses for different services..
		if($ar1['serverip']<>''){ # single ip if hosted in a single place,
			$mailip=$webip=$dnsip=$ar1['serverip'];
		} else{
			$mailip=$mailserverip;
			$dnsip=$dnsserverip;
			$webip=($webserver1==''?$webserverip:$webserver1); #use IP from webserverips field of domains table, if not empty.
		}

		$this->echoln2("yaziyor: ".$ar1["domainname"]." mailip/webip/dnsip : $mailip/$webip/$dnsip");

		$dnstemp=$ar1['dnstemplate'];
		if($dnstemp=='') $dnstemp=$dnstemplatefile; // read dns info from template file, if not written to db..
		$dnstemp=str_replace($replacealanlar,$ar1,$dnstemp);// replace domain fields,

		#$temp=str_replace(array('{serial}',"{ip}","{dnsemail}"),array(rand(1000,2000),$this->conf['dnsip'],$this->conf['dnsemail']),$temp); // replace serial,ip,dnsemail etc.
		# if php bug occurs because of date, above line may be used... http://bugs.php.net/bug.php?id=44481


		# multiserver a gore ayarlanacak: {dnsip},{webip},{mailip}
		#

		# these codes are for transition to a multi-server environment... will be implemented step by step..

		// Pick serial number # by earnolmartin
		if(!is_null($ar1["dnsmaster"])){
			// Will force it to pull updates because the master will have a larger serial number.
			$serialNum = 1;
		}else{
			$serialNum = rand(2,1000);
		}
		# end earnolmartin

		$dnstemp=str_replace(['{mailip}','{dnsip}','{webip}','{serial}',"{ip}","{dnsemail}"],[$mailip,$dnsip,$webip,$serialNum,$this->conf['dnsip'],$this->conf['dnsemail']],$dnstemp);

		# lokalden erisenler icin ayri bir dns, dns icinde view olusturulabilir buraya bak: http://www.oreillynet.com/pub/a/oreilly/networking/news/views_0501.html
		# amac: bir networkde server varsa, o network icinden erisenler icin bu bir local server dir. her desktop da ayri ayri hosts ayari girmek yerine, sunucu bunlara real degil, lokal ip doner.
		# bu sayede, kucuk-orta isletmeler icin, sunucunun lokalden cevap vermesi saglanir.. veya dns icinde view destegi, birden cok konfigurasyon v.b...
		# to translate Turkish comments, use google translate..

		$dnstemplocal=str_replace(['{mailip}','{dnsip}','{webip}','{serial}',"{ip}","{dnsemail}"],[$mailip,$dnsip,$webip,$serialNum,$this->conf['dnsip'],$this->conf['dnsemail']],$dnstemp);

		# $temp=str_replace(array('{serial}',"{ip}","{dnsemail}"),array(rand(1,1000),$this->conf['dnsip'],$this->conf['dnsemail']),$temp); // replace serial,ip,dnsemail etc.   Ymds hata veriyordu iptal ettim. bu sorunla ilgilenilecek...
		// verdigi hata: Fatal error: date(): Timezone database is corrupt - this should *never* happen!  thats why i cannot use date in daemon mode... this seems a php bug.., for tr locale

		$zoneFile = $this->conf['namedbase'].'/'.$ar1["domainname"];

		$success=$success and writeoutput2($zoneFile,$dnstemp,"w");

		// If slave domain, retransfer the zone , earnolmartin
		if(!is_null($ar1["dnsmaster"])){
			passthru2("rndc retransfer " . $ar1["domainname"]);
		}

		#$success=$success and writeoutput2($this->conf['namedbase'].'/'.$ar1["domainname"].".local",$dnstemplocal,"w"); # bu kisim henuz tamamlanmadi, yani lokal destegi..

	}
	return $success;
}

function dnsNamedConfFile($arr){// for daemon mode
	# $out="options { directory \''.$this->wwwbase."\";}";
	# reverse dns burda named.conf icine yazilmali.. http://langfeldt.net/DNS-HOWTO/BIND-9/DNS-HOWTO-5.html

	$this->requireCommandLine(__FUNCTION__);
	foreach($arr as $ar){
		$ar['namedbase']=$this->conf['namedbase'];
		$arr2[]=$ar;
	}
	# named files are located at namedbase directory, typically, /var/www/named/

	$out.=$this->putArrayToStrDns($arr2);  # for slave dns, we should use $dnsnamedconftemplate_slave if domain has dnsmaster field set. will code later.
	$file=$this->conf['namedbase']."/named_ehcp.conf";
	echo "\n\nwriting namedfile: $file \n\n";
	return writeoutput2($file,$out, "w");
}

function calculateAliasedDomains($doms,$exampledomain){

	# convert alias names to regular domain names, so that dns zone files can be setup
	# ex: changes www.dene.com to dene.com, xxx.com -> xxx.com, yyy.zzz.com -> zzz.com
/*
	function domainname($alias){
		if(substr_count($alias,'.')<=1) return $alias; # xxx.com -> xxx.com
		return substr($alias,strpos($alias,'.')); # yyy.zzz.com -> zzz.com
	}
*/

	$aliasedarr=[];
	foreach($doms as $dom){
		$aliases=$dom['aliases'];
		if($aliases=='') continue;
		$aliasarr=explode("\n",$aliases);
		foreach($aliasarr as $alias) {
			$alias=trim($alias);
			if(substr_count($alias,'.')<=1) $newdom=$alias; # xxx.com -> xxx.com
			else $newdom=substr($alias,strpos($alias,'.')+1); # yyy.zzz.com -> zzz.com
			$newdom=trim($newdom);
			if($newdom=='') continue;
			if (!in_array($newdom,$aliasedarr,True)) $aliasedarr[]=$newdom;
		}
	}

	$aliasedarr2=[];

	# construct domains array as if read from domains table, in fact, these are not read from domains table, but these are aliases.
	# i added alias domains to dns, because dns should resolve this, for domains to work..

	foreach($aliasedarr as $dom) {
		$ex=$exampledomain;
		$ex['id']=$ex['panelusername']=$ex['reseller']='aliasdomain';
		$ex['domainname']=$dom;
		$aliasedarr2[]=$ex;
	}

	return $aliasedarr2;
}

function syncDns(){// for daemon mode
	# dnsde serial ayari yapilmasi lazim. yoksa nanay... ***

	$this->requireCommandLine(__FUNCTION__);

	$arr=$this->getDomains();
	$exampledomain=$arr[0];
	$arr_aliaseddomains=$this->calculateAliasedDomains($arr,$exampledomain);

	# merge two array to one domains array:
	# this array is like 0 => array('domainname'=>'xxx.com')

	foreach($arr_aliaseddomains as $aliasdomain){
		$found=false;
		foreach($arr as $dom) if($aliasdomain['domainname']==$dom['domainname']) $found=True;
		if(!$found) $arr[]=$aliasdomain;
	}

	# put customdns info into zone files..
	$arr_customdns=$this->query("select * from ".$this->conf['customstable']['tablename']." where name='customdns' ");
	$arr2=[];

	foreach($arr as $dom) { # add customdns to array,
		$customdnsvalue='';
		foreach($arr_customdns as $cust) {
			if($cust['domainname']==$dom['domainname']) $customdnsvalue.=$cust['value']."\n"; # this loop prevents repetitive mysql query, thus faster execution.
		}
		$dom['customdns']=$customdnsvalue;
		# will include domain aliases in dns too, to be able to catch those domains with dns



		$arr2[]=$dom;
	}


	echo "\n\nsyncdns working..: \n";
	if($this->debuglevel>0) print_r($arr2);

	if(($this->dnsZoneFiles($arr2)) and ($this->dnsNamedConfFile($arr2))) {
		$this->output.="daemon->dns success (syncdns)\n";
		passthru2("/etc/init.d/{$this->named} reload");
		return True;
	}
	else return false;

}

function whitelist(){
	# this is a special function, that will be used in a dns project. not related directly to hosting or ehcp. just uses ehcp structure.
	global $mod,$domainname,$domainler;
	$this->getVariable(['mod','domainname','domainler']);


	switch($mod){
		case 'cocuklistele':
			$this->listTable("", "domainstable2", $filter);
		break;

		case 'cocukekle':
			if(!$domainname){
				$inputparams=['domainname'#, array('op','hidden','default'=>__FUNCTION__)
				];
				$this->output.=inputform5($inputparams);
			} else {

				if(!$this->afterInputControls("adddomaintothispaneluser",
						[
						"domainname"=>$domainname,
						]
					)
				) return false;


				$this->output.="Dom ekleniyor:".$domainname;
				$paneluserinfo=$this->getPanelUserInfo();
				$success=True;

				$sql="insert into domains (reseller,panelusername,domainname,homedir,status,serverip) values ('".$this->activeuser."','".$this->activeuser."','$domainname','','".$this->status_active."','7.7.7.7')";
				$success=$success && $this->executeQuery($sql);
				$success=$success && $this->addDaemonOp("syncdns",'','','','sync dns');
				$this->ok_err_text($success,'domain dnsonly add complete','failed to add domain ('.__FUNCTION__.')');

			}

		break;

		case 'cocukeklebulk':
			if(!$domainler){
				$inputparams=[['domainler','textarea']#, array('op','hidden','default'=>__FUNCTION__)
				];
				$this->output.=inputform5($inputparams);
			} else {
				$domains=$this->strNewlineToArray($domainler);
				$paneluserinfo=$this->getPanelUserInfo();

				foreach($domains as $dom){
					if(trim($dom)=='') continue;
					if(!$this->afterInputControls("adddomaintothispaneluser",
							[
							"domainname"=>$dom,
							]
						)
					) continue ;


					$this->output.="Dom ekleniyor: ($dom)";
					$success=True;
					$sql="insert into domains (reseller,panelusername,domainname,homedir,status,serverip) values ('".$this->activeuser."','".$this->activeuser."','$dom','','".$this->status_active."','7.7.7.7')";
					$success=$success && $this->executeQuery($sql);
					$success=$success && $this->addDaemonOp("syncdns",'','','','sync dns');
					$this->ok_err_text($success,'domain dnsonly add complete','failed to add domain ('.__FUNCTION__.')');
				}

			}

		break;

	}

	$this->output.="Whitelist ($mod)<br><a href='?op=otheroperations'>Home</a>";
}

function requireCommandLine($func='',$echoinfo=false){
	if(!$this->commandline) {
		return $this->errorTextExit("Coding Error: The command you requested works from commandline only: $func Only Ehcp Developers/coders can fix this. Wrong function call. ");
	}
	if($echoinfo) echo "\n$func: basliyor\n";
	$this->debugecho("Commandline: $func: basliyor\n",3);

}

function syncApacheAuth(){
	# This setups files needed for apache authentication, using htpasswd command , from database, directories table..
	# all these functions written by info@ehcp.net
	# ingilizce yazdigima bakmayin, bu programin orjinali turkcedir. Ben de Turkiyede yasiyorum.


	$this->requireCommandLine(__FUNCTION__);
	print "syncApacheAuth running; Webservertype: {$this->miscconfig['webservertype']} \n";

	$tablename=$this->conf['passwddirectoriestable']['tablename'];
	# select directories first,
	$directories=$this->query("select distinct(directory) from ".$tablename." where directory is not null and directory<>''");

	// print "dirs:";
	// print_r($directories);
	$dircount=0;
	$conf='';

	passthru("mkdir -p /etc/ehcp");
	# then find users related to directories, and setup configuration for this..
	# delete old htpasswd files:
	passthru("rm -rf /etc/ehcp/htpasswd_*"); # this only fails when there are many files that linux cannot pass * as a parameter.
	# if i use passthru2 above, * is converted to \* and dont work...


	foreach($directories as $d){
			$dir=securefilename($d['directory']);
			$dircount++;

			$users=$this->query("select * from ".$tablename." where directory='$dir'");
			// print "users related to dir: $dir \n";
			// print_r($users);

			if(!file_exists($dir)){
				passthru4("mkdir -p $dir"); # setup dir if not exists...
				passthru4("chown $this->ftpowner -Rf $dir");
			}

			if(!file_exists($dir."/index.php") and !file_exists($dir."/index.html")){
				writeoutput($dir."/index.php","This is a password protected page... put here your own data... ");
			}


			$setup=' -c '; # parameter to htpasswd command for first user creation
			$passwdfile="/etc/ehcp/htpasswd_$dircount";

			foreach($users as $user){
				$cmd="htpasswd $setup -b $passwdfile ".$user['username']." ".$user['password'];
				passthru4($cmd);
				if($setup<>'') $setup=''; # setup file only at first user
			}


			switch($this->miscconfig['webservertype']) {
				case 'nginx': # this part not tested yet. apache and nginx handles passworded dirs differently. apache: may be put in a separate file, nginx: should be in same nginx config block.
					$dominfo=$this->getDomainInfo($d['domainname']);
					$homedir=$dominfo['homedir']."/httpdocs";
					$dir=str_replace($homedir,"",$dir);  # this config should be put in nginx config section for that domain... this will not work yet..
					$conf.="
					location $dir {
						root   $homedir;
						auth_basic \"Restricted\";
						auth_basic_user_file $passwdfile;
					}
					"; 
				break;

				case 'apache2':
					$conf.="
					<Directory $dir>
					AuthType Basic
					AuthName 'Enter your password'
					AuthUserFile $passwdfile
					Require valid-user
					</Directory>
					";
				break;
				default: $this->echoln(__FUNCTION__.': Webservertype is not supported for this func');
			}
	}

	writeoutput2("{$this->config_dir}/webserver_ehcp_auth.conf",$conf,'w');
	#passthru2("/etc/init.d/apache2 reload"); zaten syncsubdomains icinde reload var.
	return True;


}

function syncpostfix(){
	$this->output.="No need postfix sync. postfix already synced from db... <br>";
	return false;
	/*
	http://www.howtoforge.com/virtual_postfix_mysql_quota_courier
	this document is a good place to go for postfix and related stuff
	*/
}

function userop(){
	global $action,$ftpusername,$mailusername,$panelusername,$id;
	$this->getVariable(["action","ftpusername","mailusername","panelusername","id"]);
	if(!$action) {
		return $this->errorText(__FUNCTION__.": Error: action not given");
	}

	switch($action){
		case "emailuserdelete": //*** bu yapilmadi henuz... tam olarak...
			if($id==''){
				$this->output.="user id to delete not given.<br>";
				$success=false;
			} else {
				$success=$this->executeQuery("delete from ".$this->conf['emailuserstable']['tablename']." where id=$id",' email user delete');
				$this->ok_err_text($success,"email user delete successfull",'failed to delete email user');
			}
			$this->showSimilarFunctions('email');
			return $success;

		break;

		# ftp silerken asl??nda aray??zde dosyalar??n silinip silinmeyece??ini sorsa iyi olur.
		case "ftpuserdelete"://*** sonra bakilacak... username ile beraber domain de kontrol edilmeli..where icinde

			$ftp=$this->query("select * from ".$this->conf['ftpuserstable']['tablename']." where ftpusername='$ftpusername'");
			if($ftp['domainname']<>'' and $ftp['type']<>''){
				$success=$this->errorText('This user has an associated domain or subdomain. Please use domain/subdomain delete.');
			} else {
				$success=$this->deleteFtpUserDirect($ftpusername);
				$this->ok_err_text($success,"ftp user delete successful","error ftp user delete");
			}
			$this->showSimilarFunctions('ftp');
			return $success;
		break;

		default: return $this->errorText("userop: no action is given");
	}
}

function deleteDB($id){
	$this->requireNoDemo();
	$db=$this->query("select * from mysqldb where id=$id");
	$dbname=$db[0]['dbname'];
	$panelusername=$db[0]['panelusername'];
	$paneluserinfo=$this->getPanelUserInfo('',$panelusername);
	$resellername=$paneluserinfo['reseller'];


	if($panelusername!=$this->activeuser and $resellername!=$this->activeuser and !$this->isadmin()) {
		return $this->errorText("You do not own this database, neither you are reseller.. active: $this->activeuser , panelusername:$panelusername, reseller: $resellername <br>");
	}

	$this->output.="<br> deleting db $id : $dbname <br>";

	$host=$db[0]['ip'];
	$myserver=$this->getMysqlServer($host,True);  # get myinfo for that host or default if no host specified..


	if(! ($link = mysqli_connect($myserver['host'], $myserver['user'], $myserver['pass']))){
		return $this->errorText('Could not connect as '.$myserver['user'].' to server '.$myserver['host']);
	}

	$this->output.= "Connected as user : ".$myserver['user']."<br>";
	if($this->executeQuery("drop database `$dbname`",'','',$link)) $this->output.="Dropped database: $dbname <br>";
	else {
		$this->output.="Error dropping db.. ".$this->conn->ErrorMsg();
	}


	$success=True;
	$success=$success && $s=$this->executeQuery("delete from ".$this->conf['mysqldbstable']['tablename']." where dbname='$dbname'",' delete db from ehcp db');

	$q="select dbusername from ".$this->conf['mysqldbuserstable']['tablename']." where dbname='$dbname'";
	$s=$user1=$this->query($q);
	if($s===false) {
		$this->echoln('error getting db users list..');
		$success=false;
	}

	$s=$this->executeQuery("use mysql",'','',$link);

	if($s===false){
		$success=$this->errorText('error selecting db....');
	}

	// delete all users associated with the db. actually there may be only one user... but one user may be used to access more than one db...
	foreach($user1 as $user){
		$user2=$user['dbusername'];

		$s=$this->executeQuery("delete from ".$this->conf['mysqldbuserstable']['tablename']." where dbusername='$user2' and dbname='$dbname'");
		if($s===false) {
			$this->output.="Error Occured: ".$this->conn->ErrorMsg()."<br>";
			$success=false;
		} else $this->output.="user $user2 : deleted from ehcp db<br>";

		$otherdbcount=$this->recordcount($this->conf['mysqldbuserstable']['tablename'],"dbusername='$user2' and dbname<>'$dbname'");
		if($otherdbcount>0) continue; # if user has more databases that has access to, it is not dropped..

/*		$s=mysqli_query("REVOKE ALL PRIVILEGES, GRANT OPTION FROM $user2",$link);
		if($s===false) {
			$this->output.="Error Occured: ".$this->conn->ErrorMsg()."<br>";
			$success=false;
		} else $this->output.="user $user2 : deleted user from mysql <br>";

 * Bu kod hata verdi: Can't revoke all privileges for one or more of the requested users
nedenini bilmiyorum.
 */
		$s=$this->executeQuery("DELETE FROM `user` WHERE User = '$user2'",'','',$link);
		if($s===false) {
			$this->output.="Error Occured: ".$this->conn->ErrorMsg()."<br>";
			$success=false;
		} else $this->output.="user $user2 : deleted user from mysql <br>";


		$s=$this->executeQuery("DELETE FROM `db` WHERE User = '$user2'",'','',$link);
		if($s===false) {
			$this->output.="Error Occured: ".$this->conn->ErrorMsg()."<br>";
			$success=false;
		} else $this->output.="user $user2 : deleted user from mysql.db <br>";


		$s=$this->executeQuery("DELETE FROM `tables_priv` WHERE User = '$user2'",'','',$link);
		if($s===false) {
			$this->output.="Error Occured: ".$this->conn->ErrorMsg()."<br>";
			$success=false;
		} else $this->output.="user $user2 : deleted user from mysql.tables_priv <br>";


		$s=$this->executeQuery("DELETE FROM `columns_priv` WHERE User = '$user2'",'','',$link);
		if($s===false) {
			$this->output.="Error Occured: ".$this->conn->ErrorMsg()."<br>";
			$success=false;
		} else $this->output.="user $user2 : deleted user from mysql.columns_priv <br>";
		$this->executeQuery("flush privileges",'','',$link);

	}
	return $success;

}

function domainop(){
	global $domainname,$action,$dbusername,$dbuserpass,$dbname,$id,$confirm;
	$this->getVariable(["domainname","action","user","pass","dbname","id",'confirm']);
	if($action=='') {
		$this->output.="userop: action not given <br>";return false;
		}
	switch($action){
		case "deletedb":
			if($confirm==''){
				$this->output.="<br>Are you sure to delete mysql db and related users ?  <a href='?op=domainop&action=deletedb&id=$id&confirm=1'>click here to delete</a><br><br>";
				$success=false;
			} else {
				$success=$this->deleteDB($id);
				$this->ok_err_text($success,'All db ops are successfull','some db ops are failed..');
				// yukardaki kodda, bircok success (basari) ile, her bir islemin sonucu ogrenilir. herhangi biri fail olsa, sonuc fail olur..
			}
		break;

		case "listdb":
			#$filter="panelusername='$this->activeuser'";
			$filter=$this->globalfilter;
			if($this->selecteddomain) $filter=andle($filter,"domainname='$this->selecteddomain'");

			$this->listTable("All mysql db's", 'mysqldbstable', $filter);
			$this->output.="<br> <a target=_blank href='/phpmyadmin/'><img src='/phpmyadmin/themes/original/img/logo_left.png' border=0></a><br>";
			$this->listTable("All mysql db users", 'mysqldbuserstable', $filter);
			$success=True;
		break;

		default: $this->output.="domainop: unknown action given: $action <br>";
	}
	$this->showSimilarFunctions('mysql');
	$this->output.="Filter: ($filter)     globalfilter: ($this->globalfilter) ";
	return $success;
}//function

function redirect_domain(){
	global $domainname;
	$this->redirecttourl("http://www.$domainname");
}

function redirecttourl($url){
	header("Location: $url");
	exit;
}

function getMysqlServer($host='',$returndefault=False,$returnto=False){
	# choose a mysqlserver from server farm.. servers table..or return server info for a host
	# this is written to go into multi-server concept.. Multi server is not complete yet for all server types, only mysql can be separate for customers.
	global $serverip,$returntoop;
	$this->getVariable(['serverip','returntoop']);

	if($serverip<>'') {
		$q="select * from ".$this->conf['serverstable']['tablename']." where ip='$serverip' and servertype='mysql'";
		$ret=$this->query($q);
		$ret=$ret[0];
		$server=['host'=>$ret['ip'],'user'=>'root','pass'=>$ret['password'],'defaultmysqlhostname'=>$ret['defaultmysqlhostname']];
		$_SESSION['myserver']=$server;
		$this->redirecttourl("?op=$returntoop");
	}

	$defaultmyserver=['host'=>'localhost','user'=>$this->conf['mysqlrootuser'],'pass'=>$this->conf['mysqlrootpass']];
	$sayi=$this->recordcount($this->conf['serverstable']['tablename'],"servertype='mysql' and upper(mandatory) in ('E','Y')"); # E=Y Evet=Yes # number of mandatoryservers..

	$where2="servertype='mysql' and (mandatory in ('',null) or (upper(mandatory) not in ('E','Y')))";
	$sayi2=$this->recordcount($this->conf['serverstable']['tablename'],$where2); # number of servers, which are non-mandatory

	if($host=='' and !$returndefault and $sayi==0 and $sayi2>0){  # if there are some choises..
		$this->output.="Choose mysql server to use:";
		$q="select * from ".$this->conf['serverstable']['tablename']." where $where2";
		$servers=$this->query($q);
		$this->output.=$this->listSelector($arr=$servers,$print=['ip'],$link="?op=".__FUNCTION__."&returntoop=$returnto&serverip=",$linfield='ip');
		$this->showexit();
	} elseif ($host=='' and ($returndefault or $sayi==0)){# if no mandatory, take optional, or default localhost...
		# belli bir host sorulmuyorsa, default gonder.. .
		$server=$defaultmyserver;
	} else { # choose for a specific host or mandatory one..
		if($host=='') $where="servertype='mysql' and upper(mandatory) in ('E','Y')";
		else $where="host='$host'";

		$ret=$this->query("select * from ".$this->conf['serverstable']['tablename']." where $where");
		$ret=$ret[0];
		$server=['host'=>$ret['ip'],'user'=>'root','pass'=>$ret['password'],'defaultmysqlhostname'=>$ret['defaultmysqlhostname']];
		$this->echoln("Using mandatory mysql server at ".$server['host']);
	}
	return $server;
}

function addMysqlDbtoUser(){
	global $domainname,$dbusername,$dbuserpass,$dbname,$id,$confirm;
	$this->getVariable(["domainname","dbusername","dbuserpass","dbname","id",'confirm']);

	if(!$this->beforeInputControls('adddb')) return false;
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$myserver=$_SESSION['myserver'];
	if(!$myserver) $myserver=$this->getMysqlServer('',false,__FUNCTION__); # get mysql server info..
	$success=True;


	$users=$this->query("select distinct dbusername from ".$this->conf['mysqldbuserstable']['tablename']." where panelusername='$this->activeuser'");
	if(count($users)==0){
		$this->output.="<hr>You have not any db users yet.. so, use add mysql db link <a href='?op=addmysqldb'>here</a>";
		return false;
	}

	if(!$dbusername) {
		$this->output.="<br>Select User:".$this->listSelector($arr=$users,$print=['dbusername'],$link="?op=".__FUNCTION__."&dbusername=",$linfield='dbusername');
		return false;
	}


	if(!$dbname){
		$inputparams=["dbname",
			['dbusername','hidden','value'=>$dbusername],
			['op','hidden','value'=>__FUNCTION__]
		];

		if($myserver['host']<>'localhost') { # if this is not local mysql server, the db user may not be localhsot, so, ask that..
			$dbuserhost=$myserver['defaultmysqlhostname'];
			$inputparams[]=['dbuserhost','lefttext'=>'dbuser hostname','value'=>$dbuserhost,'righttext'=>'This is host of mysql user, to connect from, <br>You should write hostname of your webserver here..'];
		}

		$this->output.="Mysql Server:".$myserver['host'].inputform5($inputparams);

	} else {
		if(!$this->afterInputControls("adddb",
								[
								"dbname"=>$dbname
								]
						)
		) return false;
		$this->output.="<br>Adding db to user: $dbusername db: $dbname:";
		$success=$success && $this->addMysqlDbDirect($myserver,$domainname,$dbusername,$dbuserpass,$dbuserhost,$dbname,$adduser=false);
		$this->ok_err_text($success);
	}

	$this->showSimilarFunctions('mysql');
	return $success;

}

function addMysqlDb(){
	// **** burda bikac sorun olabilir.. enonemlisi, bu success olayi calismiyor.. success lerden biri fail olsa da, sonuc degismiyor..
	// diger sorun, if(!rs) denilen yerlerde, dbden okumus sorunsuzca, ama sonuc bos ise, sanki dbden okuyamam???? gibi davran??yor..
	global $domainname,$dbusername,$dbuserpass,$dbname,$dbuserhost,$id,$confirm;
	$this->getVariable(["domainname","dbusername","dbuserpass","dbname","dbuserhost","id",'confirm']);

	if(!$this->beforeInputControls('adddb')) return false; # check limit
	$domainname=$this->chooseDomain(__FUNCTION__,$domainname);
	$myserver=$_SESSION['myserver'];
	if(!$myserver) $myserver=$this->getMysqlServer('',false,__FUNCTION__); # get mysql server info..
	$success=True;

	if($dbname==''){
		$inputparams=[
			"dbname",
			"dbusername",
			["dbuserpass","password_with_generate"],
			['op','hidden','value'=>__FUNCTION__]
		];


		if($myserver['host']<>'localhost') { # if this is not local mysql server, the db user may not be localhsot, so, ask that..
			$dbuserhost=$myserver['defaultmysqlhostname'];
			$inputparams[]=['dbuserhost','lefttext'=>'dbuser hostname','value'=>$dbuserhost,'righttext'=>'This is host of mysql user, to connect from, <br>You should write hostname of your webserver here..'];
		}

		$this->output.="Mysql Server:".$myserver['host'].inputform5($inputparams);

	}else {
		if(!$this->afterInputControls("adddb",
						[
						"dbname"=>$dbname,
						"dbusername"=>$dbusername
						]
				)
		) return false;


		# non-interactive part:
		$success=$success && $this->addMysqlDbDirect($myserver,$domainname,$dbusername,$dbuserpass,$dbuserhost,$dbname,$adduser=True);
		$_SESSION['myserver']=false; # reset mysql server selector...
		$this->ok_err_text($success);
	}

	$this->showSimilarFunctions('mysql');
	return $success;


}

function addMysqlDbDirect($myserver,$domainname,$dbusername,$dbuserpass,$dbuserhost,$dbname,$adduser=True){
	if(!$myserver) $myserver=$_SESSION['myserver'];
	if(!$myserver) $myserver=$this->getMysqlServer('',false,__FUNCTION__); # get mysql server info..

	if($myserver['host']=='localhost') $dbuserhost='localhost';
	if($dbuserhost=='') $dbuserhost='localhost';

	# connect to mysql server, local or remote
	if(! $link = mysqli_connect($myserver['host'], $myserver['user'], $myserver['pass'])){
			return $this->errorText("Could not connect as root!");
	}

	$this->output.= "<br>Connected as mysql root user: ".$myserver['user']."<br>";


	# actual setup for db and dbuser, local or remote
	# step 1: setup database: DEFAULT CHARACTER SET utf8 COLLATE utf8_turkish_ci

	$s=$this->executeQuery("create database `$dbname` ".$this->miscconfig['mysqlcharset'],'creating db','',$link);

	if($s===false) return $this->errorText("Error creating db.. ".$this->conn->ErrorMsg()."<br>");
			else $this->output.="setup complete database: $dbname <br>";


	$success=True;
	# step 2: grant user rights
	if($adduser) $s=$this->executeQuery("grant all privileges on `$dbname`.* to '$dbusername'@'$dbuserhost' identified by '$dbuserpass' ",'grant user rights','',$link);
	else $s=$this->executeQuery("GRANT ALL PRIVILEGES ON `$dbname`.* TO '$dbusername'@'$dbuserhost'",'grant user to db','',$link);

	if($s===false){
			$this->errorText("Error: user $dbusername cannot be permitted to : $dbname");
			$success=false;
	}else $this->output.="user $dbusername permitted to : $dbname <br>";

	# step 3:
	# add these to ehcp db, to local only if local server, to both local and remote, if it is remote server
	# local add to ehcp db,
	$q="insert into ".$this->conf['mysqldbstable']['tablename']." (domainname,host,dbname,panelusername)values('$domainname','".$myserver['host']."','$dbname','$this->activeuser')";
	if($success) $success=$success && $s=$this->executeQuery($q,' add new mysql db info to ehcp db');


	$q="insert into ".$this->conf['mysqldbuserstable']['tablename']." (domainname,host,dbname,dbusername,password,panelusername)values('$domainname','".$myserver['host']."','$dbname','$dbusername','$dbuserpass','$this->activeuser')";
	if($success) $success=$success && $s=$this->executeQuery($q,' add mysql user to ehcp db ');

	#  add to remote ehcp db too, if this mysql is a remote one... this is for: if i add a remote mysql db, the data is also written to remote ehcp db. so, remote sees and may remove that.  this may be disabled..
	if($success and $myserver['host']<>'localhost'){ # if remote
		mysqli_select_db($link,'ehcp');
		$q="insert into ".$this->conf['mysqldbstable']['tablename']." (domainname,host,dbname,panelusername)values('$domainname','localhost','$dbname','$this->activeuser')";
		$success=$success && mysqli_query($link,$q); # do not use this->executequery here, since this is executed on remote mysql server.
		if(!$success) return $this->errorText("mysql Error ".$this->conn->ErrorMsg()."<br>");

		// $q="insert into ".$this->conf['mysqldbuserstable']['tablename']." (domainname,host,dbname,dbusername,password,panelusername)values('$domainname','localhost','$dbname','$dbusername','$dbuserpass','$this->activeuser')";
		$q="insert into ".$this->conf['mysqldbuserstable']['tablename']." (domainname,host,dbname,dbusername,panelusername)values('$domainname','localhost','$dbname','$dbusername','$this->activeuser')";
		if($success) $success=$success && mysqli_query($link,$q);
		if(!$success) return $this->errorText("mysql Error ".$this->conn->ErrorMsg()."<br>");
	}
	return $success;

}


function mysqlRootQuery($q){
	if(! $link = mysqli_connect("localhost", $this->conf['mysqlrootuser'], $this->conf['mysqlrootpass'])){
		return $this->errorText("Could not connect as root, check your mysql root pass");
	}

	$this->output.= "<br>**Connected as root : ".$this->conf['mysqlrootuser']."<br>";
	$s=$this->executeQuery($q,'execute root query','',$link);
	if($s===false){
			return $this->errorText("Error: mysqlroot query cannot be executed: $q");
	} else return True;
}

function arrayToFile($file,$lines) {
	$new_content = join('',$lines);
	$fp = fopen($file,'w');
	$write = fwrite($fp, $new_content);
	fclose($fp);
}


function addIfNotExists($what,$where) {
	$what.="\n";
	$filearr=@file($where);
	if(!$filearr) {
		echo "\ncannot open file, trying to create new file: ($where)\n";
		$fp = fopen($where,'w');
		fclose($fp);
		$filearr=file($where);

	} //else print_r($file);

	if(array_search($what,$filearr)===false) {
		$filearr[]=$what;
		$this->arrayToFile($where,$filearr);

	}
}

function replaceArrayPutInFile($srcfile,$dstfile,$findarray,$replacearray, $debug=False){
	# reads srcfile, replace some findarray with replacearray, then put in dstfile, for writing some template files..

	$filestr=file_get_contents($srcfile);
	$findarray2=arrayop($findarray,"strop");
	$fileout=str_replace($findarray2,$replacearray,$filestr);
	$res=writeoutput2($dstfile,$fileout,'w');
	if($res===True) {
		echo __FUNCTION__.": ($srcfile) -> Dst file ($dstfile) written.. \n";
	} else {
		echo __FUNCTION__.": ($srcfile) -> Dst file ($dstfile) could not be written.. \n";		
	}
	return $res;
}

function replaceArrayPutInFile2($srcfile,$dstfile,$findarray,$replacearray, $debug=False){
	# reads srcfile, replace some findarray with replacearray, then put in dstfile, for writing some template files..

	$filestr=file_get_contents($srcfile);	
	$fileout=str_replace($findarray,$replacearray,$filestr);
	$res=writeoutput2($dstfile,$fileout,'w');
	if($res===True) {
		echo __FUNCTION__.": ($srcfile) -> Dst file ($dstfile) written.. \n";
	} else {
		echo __FUNCTION__.": ($srcfile) -> Dst file ($dstfile) could not be written.. \n";		
	}
	if($debug) { # cancel this after debug.
		$this->println("------------ Find");
		print_r($findarray);
		$this->println("------------ Replace:");
		print_r($replacearray);
		$this->println("------------ Input file");
		$this->println($filestr);
		$this->println("------------ Output file");
		$this->println($fileout);
		$this->println("------------");
	}
	return $res;
}


function restart_webserver2($server){
	$this->requireCommandLine();

	echo __FUNCTION__.":\n";
	print_r($server);

	$serverip=$serverip['ip'];
	$webservertype=$server['servertype'];

	$this->debugecho(__FUNCTION__.":$serverip",1);

	if($serverip=='') $serverip='localhost';
	if($webservertype=='') $webservertype='apache2';

	$this->server_command($serverip,"/etc/init.d/$webservertype restart");
	# configtest_reload yap??lacak.
}


function check_domain_files_quick($homedir){
	$ar=["httpdocs", "logs", "phptmpdir"];
	
	foreach ($ar as $dir) {
		if(!file_exists("$homedir/$dir")) {
			mkdir("$homedir/$dir");
		}
	}
}


function check_domain_directories() {
	$arr=$this->getDomains($filt);
	foreach($arr as $dom) {
		$this->check_domain_files_quick($dom['homedir']);
	}

}

function restart_webserver(){
	# thanks to webmaster@securitywonks.net for encourage of nginx integration
	$this->requireCommandLine();
	$this->check_domain_directories(); # otherwise, webserver may fail to reload configuration

	echo "\n".__FUNCTION__.": Current webserver is:".$this->miscconfig['webservertype']."\n";

	if($this->miscconfig['webservertype']=='apache2') {
		passthru2("/etc/init.d/nginx stop");
		passthru2("/etc/init.d/apache2 restart");
	} elseif($this->miscconfig['webservertype']=='nginx') {
		passthru2("/etc/init.d/apache2 stop");
		passthru2("/etc/init.d/nginx restart");
		passthru2("/etc/init.d/{$this->php_fpm} restart");
	}
	echo "\n".__FUNCTION__.":Finished";
	return True;
}

function is_webserver_running(){
	// will be checked..

	// Sleep for 5 seconds before checking to allow configuration to be updated.
	sleep(3);
	if($this->miscconfig['webservertype']=='apache2') {
		$out=shell_exec('ps aux | grep apache | grep -v grep | grep -v php');
		echo __FUNCTION__.":".$out;
		return (strstr($out,'apache')!==false);

	} elseif($this->miscconfig['webservertype']=='nginx') {
		$out=shell_exec('ps aux | grep nginx | grep -v grep | grep -v php');
		echo __FUNCTION__.":".$out;
		return (strstr($out,'nginx')!==false);
	}

	return True;
}

function fixApacheConfigSsl($domain=''){
	$this->requireCommandLine(__FUNCTION__,True);
	syslog(LOG_WARNING,"Ehcp: changing webserver confing to ".$this->miscconfig['webservertype']);

	$this->generateSslFiles();

	if($this->miscconfig['webservertype']=='apache2') {
		passthru2("rm -rvf /etc/apache2/sites-enabled/*", true, true);

		passthru2("/usr/sbin/a2enmod ssl");
		passthru2("cp -vf $this->ehcpdir/etc/apache2_ssl/fork/webserver_template_passivedomains $this->template_dir/", true, true);

		// We will bind on configured panel IP address

		$findarray=['webserverip'];
		$replacearray=[$this->getWebServer()];
		$this->replaceArrayPutInFile("{$this->ehcpdir}/etc/apache2_ssl/fork/webserver_template_ipbased","{$this->template_dir}/webserver_template",$findarray,$replacearray);
		$this->replaceArrayPutInFile("{$this->ehcpdir}/etc/apache2_ssl/fork/webserver_subdomain_template","{$this->template_dir}/webserver_subdomain_template",$findarray,$replacearray);
		$this->replaceArrayPutInFile("{$this->ehcpdir}/etc/apache2_ssl/fork/default-ssl","/etc/apache2/sites-enabled/default-ssl",$findarray,$replacearray);
		$this->replaceArrayPutInFile("{$this->ehcpdir}/etc/apache2_ssl/fork/default"	 ,"/etc/apache2/sites-enabled/default"	,$findarray,$replacearray);
		$this->replaceArrayPutInFile("{$this->ehcpdir}/etc/apache2_ssl/fork/ports.conf" ,"/etc/apache2/ports.conf"				 ,$findarray,$replacearray);

		$this->executeQuery("update misc set value='apache2' where name='webservertype'");

	} elseif($this->miscconfig['webservertype']=='nginx') {
		passthru2("rm -rvf /etc/nginx/sites-enabled/*", true, true);
		passthru2("cp -vf {$this->ehcpdir}/etc/nginx_ssl/webserver_subdomain_template {$this->ehcpdir}/etc/nginx", true, true);
		passthru2("cp -vf {$this->ehcpdir}/etc/nginx_ssl/webserver_template {$this->ehcpdir}/etc/nginx", true, true);
		passthru2("cp -vf {$this->ehcpdir}/etc/nginx_ssl/default {$this->ehcpdir}/etc/nginx", true, true);


		$this->executeQuery("update misc set value='nginx' where name='webservertype'");
	}
	
	$this->executeQuery("update misc set value='ssl' where name='webservermode'");

	$this->new_sync_domains();
	return True;
}

function fixApacheConfigSslOnly($domain=''){
	$this->requireCommandLine(__FUNCTION__,True);
	syslog(LOG_WARNING,"Ehcp: changing webserver confing to ".$this->miscconfig['webservertype']);

	$this->generateSslFiles();

	if($this->miscconfig['webservertype']=='apache2') {

		passthru2("rm -rvf /etc/apache2/sites-enabled/*", true, true);
		passthru2("/usr/sbin/a2enmod ssl");
		passthru2("/usr/sbin/a2enmod rewrite");

		passthru2("cp -vf $this->ehcpdir/etc/apache2_sslonly/fork/webserver_template_passivedomains $this->ehcpdir/", true, true);

		$this->executeQuery("update misc set value='apache2' where name='webservertype'");

		// We will bind on configured panel IP address

		$findarray=['webserverip'];
		$replacearray=[$this->getWebServer()];
		$this->replaceArrayPutInFile("{$this->ehcpdir}/etc/apache2_sslonly/fork/webserver_template_ipbased","$this->template_dir/webserver_template",$findarray,$replacearray);
		$this->replaceArrayPutInFile("{$this->ehcpdir}/etc/apache2_sslonly/fork/webserver_subdomain_template","$this->template_dir/webserver_subdomain_template",$findarray,$replacearray);
		$this->replaceArrayPutInFile("{$this->ehcpdir}/etc/apache2_sslonly/fork/default-ssl","/etc/apache2/sites-enabled/default-ssl",$findarray,$replacearray);
		$this->replaceArrayPutInFile("{$this->ehcpdir}/etc/apache2_sslonly/fork/default"	 ,"/etc/apache2/sites-enabled/default"	,$findarray,$replacearray);
		$this->replaceArrayPutInFile("{$this->ehcpdir}/etc/apache2_sslonly/fork/ports.conf" ,"/etc/apache2/ports.conf"				 ,$findarray,$replacearray);


	} elseif ($this->miscconfig['webservertype']=='nginx'){
		passthru2("rm -rvf /etc/nginx/sites-enabled/*", true, true);

		passthru2("cp -vf $this->ehcpdir/etc/nginx_sslonly/webserver_subdomain_template $this->ehcpdir/etc/nginx", true, true);
		passthru2("cp -vf $this->ehcpdir/etc/nginx_sslonly/webserver_template $this->ehcpdir/etc/nginx", true, true);
		passthru2("cp -vf $this->ehcpdir/etc/nginx_sslonly/default $this->ehcpdir/etc/nginx", true, true);


		$this->executeQuery("update misc set value='nginx' where name='webservertype'");

	}
	
	$this->executeQuery("update misc set value='sslonly' where name='webservermode'");

	$this->new_sync_domains();
	return True;
}

function fixApacheConfigNonSsl2(){
	#$this->executeQuery("delete from customsettings");
	$this->setConfigValue("disablecustomhttp","Yes");
	$this->loadConfigWithDaemon();
	# do any other necessary things here...
	$this->fixApacheConfigNonSsl();
	return True;
}

function fixApacheConfigNonSsl(){
	$this->requireCommandLine(__FUNCTION__,True);
        global $ehcpinstalldir;
        $ehcpinstalldir=$this->conf['ehcpdir'];

	
	syslog(LOG_WARNING,"Ehcp: changing webserver confing apache2 nonssl");

	rebuild_apache2_config2(); # in install_lib.php

	$this->executeQuery("update misc set value='apache2' where name='webservertype'");
	$this->executeQuery("update misc set value='nonssl' where name='webservermode'");
	$this->loadConfig(); # Update Config

	$this->new_sync_domains('',False);
	return True;
}


function fix_webserver_config_nonssl(){
	# ssl functions updated/merged from ehcp force edition, hope this works.
	$this->requireCommandLine(__FUNCTION__,True);
        global $ehcpinstalldir;
        $ehcpinstalldir=$this->conf['ehcpdir'];

	if($this->miscconfig['webservertype']=='apache2') {
		$this->resetNonSSLApacheConf();
	}else if($this->miscconfig['webservertype']=='nginx'){
		$this->resetNonSSLNginxConf();
	}

	$this->new_sync_domains();
	return True;
}

function resetNonSSLApacheConf(){
	global $ehcpinstalldir;
    $ehcpinstalldir=$this->conf['ehcpdir'];

	// Disable the ssl mod
	passthru2("a2dismod ssl");

	// Copy original apache configs from ehcpinstalldir/etc/apache2 back to root ehcpinstalldir
	

	rebuild_apache2_config2(); # in install_lib.php

	// Update database settings
	$this->executeQuery("update misc set value='apache2' where name='webservertype'");
	$this->executeQuery("update misc set value='nonssl' where name='webservermode'");
}

function resetNonSSLNginxConf(){
	// Copy nonssl nginx configs back into shared ehcp nginx config directory
	passthru2("cp -vf $this->ehcpdir/etc/nginx_nonssl/webserver_subdomain_template $this->ehcpdir/etc/nginx", true, true);
	passthru2("cp -vf $this->ehcpdir/etc/nginx_nonssl/webserver_template $this->ehcpdir/etc/nginx", true, true);
	passthru2("cp -vf $this->ehcpdir/etc/nginx_nonssl/default $this->ehcpdir/etc/nginx", true, true);

	passthru2("rm -rvf /etc/nginx/sites-enabled/*", true, true);

	// Update database settings
	$this->executeQuery("update misc set value='nginx' where name='webservertype'");
	$this->executeQuery("update misc set value='nonssl' where name='webservermode'");

}

function sync_domains_multi_server($file='') { # this should be same as syncdomains below, but I wrote a separate function to minimize conflicts, errors while developing. will call this function only if ehcp has multi-server enabled.
	# not completed yet.

	$this->requireCommandLine(__FUNCTION__);
	echo "\n".__FUNCTION__.": start syncing domains";
	if($file=='') $file="webserver_ehcp.conf"; # gecici olarak yaptim. ****
	$filt=andle($this->activefilt,"(serverip is null or serverip='') and homedir<>''"); # exclude where serverip is set, that is, for remote dns hosted only domains..
	$arr=$this->getDomains($filt);
	$webserverip=$this->getWebServer();


	$success=True;

	if($this->miscconfig['disablecustomhttp']=='') $arr_customhttp=$this->query("select * from ".$this->conf['customstable']['tablename']." where name='customhttp'  and (webservertype is null or webservertype='' or webservertype='".$this->miscconfig['webservertype']."')");

	$arr2=[];

	# farkli webserver'lara yazabilmek icin,  herbir domain'i ayri degerlendirmek, ilgili webserver'a konfigurasyonlari gondermek lazim.
	# group by webserver tarzi bisey olabilir, sonra herbir grup icin bu kodlar calistirilir. birer dizine konur. sonra her dizin kendi sunucusuna rsync yapilir.
	# domains arrayi soyle olabilir

	#array=>server1=>array(domainler)
	#	   server2=>array(domainler)

}

function clear_slash_at_end($str){
	# clears slash at end of a string, for path names. path names should have no slash at end. this should be a standard. this function ensures this.
	# until code is clean enaugh.

	$len=strlen($str);
	if(substr($str,$len-1,1)=='/') $out=substr_replace($str,'',$len-1);
	else $out=$str;

	#echo __FUNCTION__.":($str)($out)(len:$len) (".substr($str,$len-1,1).") \n";
	return $out;
}

function server_command($serverip,$cmd,$noremove=false,$copy_file_to_dest=False){  # not used yet.
	# execute a shell command in local or remote server.
	if(is_array($serverip)) $serverip=$serverip['ip'];  # accept both until all code is standard.
	if(!$noremove) $cmd=removeDoubleSlash($cmd);
	$escapedcmd=escapeshellcmd($cmd);
	$accessip=$this->get_server_access_ip($serverip);
	#echo "\nexecuting command: $cmd \n(escaped cmd:$escapedcmd)\n";

	if($accessip=='localhost') {
			echo "\nexecuting command1: $cmd \n";
			return shell_exec($escapedcmd);
	} else {
		if($copy_file_to_dest){ # in this case, the command is a file (probably that has many commands inside) that needs to be transfered to dest server before executing.
			shell_exec("scp $cmd $accessip:/etc/ehcp/");
			$cmd="/etc/ehcp/$cmd";
		}
		echo "\nexecuting command2: ssh $accessip \"$cmd\" \n(escapedcmd: ssh $accessip \"$escapedcmd\")\n";
		return shell_exec("ssh $accessip \"$escapedcmd\"");
	}

}

function server_commands($serverip,$filename){
	if(is_array($serverip)) $serverip=$serverip['ip'];  # accept both until all code is standard.

	echo "\n".__FUNCTION__.":file $filename is being executed on server:($serverip) \n";
	if(trim($serverip)=='') {
		echo "\n".__FUNCTION__.": server is empty. strange! \n";
		debug_print_backtrace();
		return false;
	}

	$accessip=$this->get_server_access_ip($serverip);

	if($accessip=='localhost'){
		my_shell_exec($filename,__FUNCTION__);
	} else {
		my_shell_exec("scp $filename $accessip:$filename",__FUNCTION__);
		my_shell_exec("ssh $accessip \"$filename\"",__FUNCTION__);
	}
	return True;
}

function execute_server_commands($serverip,$commands){
	# prepare commands as a whole, put them in a file, execute that file. especially may be useful for remote server command execution.
	if(is_array($serverip)) $serverip=$serverip['ip'];  # accept both until all code is standard.

	if(trim($serverip)=='') {
		echo "\n".__FUNCTION__.": server is empty. strange! \n";
		debug_print_backtrace();
		return false;
	}

	echo "\n".__FUNCTION__.": preparing server commands for server ($serverip)\n";
	$out="";
	foreach($commands as $com) $out.=$com."\n";  # array_to_plaintext
	$filename="/etc/ehcp/ehcp_executethisfile.sh";
	writeoutput2($filename, $out, "w");
	my_shell_exec("chmod a+x $filename",__FUNCTION__);
	echo "\n".__FUNCTION__.":Executing this file: $filename on server ($serverip)\n $out \n";
	$this->server_commands($serverip,$filename);
	echo "\n".__FUNCTION__.":end\n";

}

function initialize_logs2($dir){

	# fill commands to be executed on relatd server. these will be executed all in once.
	$this->commands[]="mkdir -p $dir";
	$this->commands[]="mkdir -p $dir/logs";
	$this->commands[]="echo '' >> $dir/logs/access_log";// these are defined in webserver_template file, bunlarin log_rotate olayi yapilmali.
	$this->commands[]="echo '' >> $dir/logs/error_log";

	#passthru2("chown $this->ftpowner -Rf $dir");
	# this caused problem especially for file upload scripts,

	$this->commands[]="chown root:root -Rf $dir/logs"; # bu olmayinca, biri logs dizinini silince, apache hata verip cikiyor.. # suanda access ver error loglar silinemiyor kullanici tarafindan...ancak sunucudan ssh ile silinebilir...!
}

function noExistingIndex($homedir){
	if(!file_exists($homedir."/index.php") and !file_exists($homedir."/index.htm" and !file_exists($homedir."/index.html"))) { # do not overwrite if any older index.php exists there...
		return true;
	}else{
		return false;
	}
}

function initialize_domain_files($homedir){
	# fill commands to be executed on related server. these will be executed all in once.

	$this->commands[]="mkdir -p $homedir";
	$this->commands[]="mkdir -p $homedir/httpdocs";
	$this->commands[]="chown $this->ftpowner -Rf $homedir/httpdocs";
	# $this->commands[]="chmod g+w -Rf $homedir/httpdocs";  # make group writable, if www-data writable, then, all domains files would be writable.
	$this->commands[]="mkdir -p $homedir/phptmpdir";
	$this->commands[]="chmod a+w $homedir/phptmpdir";

	$this->commands[]="echo '' > $homedir/UPLOAD_SITE_FILES_TO_httpdocs_FOLDER"; // z7 mod
	$this->commands[]="cp ".$this->ehcpdir."/z7/install_files/.htaccess $homedir/phptmpdir/.htaccess"; // z7 mod
	if($this->noExistingIndex($homedir)){
		$this->commands[]="cp ".$this->ehcpdir."/z7/install_files/domain_index.php $homedir/index.php"; // z7 mod\
	}
	$this->commands[]="cp -f $this->ehcpdir/ehcpinfo.html $homedir/httpdocs/ehcpinfo.html";   # final execution of commands will be done after all commands are collected at calling functions.

	$this->initialize_logs2($homedir);

}

function prepare_webserver_files($file,$server){ # Multi server environment.. being developed.. 
	# Case webserver* is not taken into account here. for this function to work, all servers need to have real ip.
	# this function is a re-write of sync_domains (syncDomains) function, will be used in multi-server environments.
	# idea: prepare server files, then, send these files to related servers, if localhost, send to related dirs in local machine.

	# *** MISSING HERE: sync subdomains, different ip within a server.. will be made similar to syncDomains for multi IP, multiserver

	$this->requireCommandLine(__FUNCTION__);


	$this->commands=['#!/bin/bash','echo "$0: commands are executed from within ehcp '.date_tarih().'" >> /var/log/ehcp.log']; # these will be commands to be executed on local or remote server as a whole, ?? i am testing this technique

	$serverip=$server['ip'];
	$webservertype=$server['servertype'];

	echo "\n===========================================\n# start process server files for server:$serverip, ".date_tarih()."\n";

	if($serverip=='') $serverip='localhost';

	if($webservertype=='') $webservertype='apache2';
	$accessip=$this->get_server_access_ip($serverip);
	if($accessip<>'localhost' and $file=='') $file="$this->ehcpdir/serverfiles/$accessip/webserver/ehcp_webserver_remote_config_produced_by_".$this->miscconfig['dnsip'].'.conf';  # for testing, or give a diffrent name for this, so that track easier.
	if($accessip=='localhost') $file="$this->config_dir/webserver_ehcp.conf";

	# clean old server files first:
	passthru2("rm -vf $this->ehcpdir/serverfiles/$accessip/webserver/*",True,True);

	# get domains list first, related to
	if($serverip=='localhost') {
		$filt="webserverips is null or webserverips='' or webserverips like '%localhost%'";
		$webserverip=$this->miscconfig['dnsip'];
	} else {
		$filt="webserverips like '%$serverip%'";
		$webserverip=$serverip;
		$this->commands[]="mkdir -p ".$this->ehcpdir;
		$this->commands[]="mkdir -p /etc/ehcp";

		if(rand(1,10)==10) my_shell_exec("rsync -arvz $this->ehcpdir/ehcpinfo.html $accessip:".$this->ehcpdir,__FUNCTION__); # copy that file to dest server. do not send every time, only %10 of time. to prevent copy eachtime.
	}

	$arr=$this->getDomains($filt);
	if(count($arr)==0) {
		echo "\n number of domains for server ($serverip) is zero. quiting ".__FUNCTION__;
		writeoutput2($file,"",'w',false); # no domains, so, empty the config
		return True;
	}

	print_r($arr);

	$success=True;
	$arr_customhttp=[];
	if($this->miscconfig['disablecustomhttp']=='') $arr_customhttp=$this->query("select * from ".$this->conf['customstable']['tablename']." where name='customhttp'  and (webservertype is null or webservertype='' or webservertype='".$this->miscconfig['webservertype']."')");
	$arr2=[];


	# prepare webserver config files for this server, put in serverfiles/ folder.

	# prepare domain array

	foreach($arr as $dom) { // setup necessry dirs/files if doesnt exist..
		# files will be prepared.. such as mkdir, like in sync_domains
		$this->initialize_domain_files($dom['homedir']);

		# add customhttp to array,
		$customhttpvalue='';
		if($arr_customhttp)
		foreach($arr_customhttp as $cust) {
			if($cust['domainname']==$dom['domainname']) $customhttpvalue.=$cust['value']."\n"; # this loop prevents repetitive mysql query, thus faster execution.
		}
		$dom['customhttp']=$customhttpvalue;

		# add ServerAlias to begining of lines in aliases field
		$aliases=$dom['aliases'];
		$aliasarr=explode("\n",$aliases);
		$newaliases="";
		foreach($aliasarr as $al) if(trim($al)<>'') $newaliases.="ServerAlias ".trim($al)." \n";
		$dom['aliases']=$newaliases;
		$dom['webserverip']=$webserverip;

		$arr2[]=$dom;
	}

	print_r($arr2);

	$this->commands[]="mkdir -p /var/www/passivedomains";
	$this->commands[]="echo domain_deactivated_contact_administrator > /var/www/passivedomains/index.html";


	#process passive domains
	$passivedomains=$this->getDomains($this->passivefilt);

	$passives=[];
	foreach($passivedomains as $p){
		if($ssl_enabled){
			$p['webserverip']=$webserverip;
		}
		$this->initialize_domain_files($dp['homedir']);
		$passives[]=$p;
	}


	if($serverip=='localhost') $passive_file="$this->config_dir/webserver_ehcp_passivedomains.conf";
	else $passive_file="$this->ehcpdir/serverfiles/$serverip/webserver/ehcp_webserver_remote_config_passivedomains_produced_by_".$this->miscconfig['dnsip'].'.conf';

	$this->putArrayToFile($passives,$passive_file,"webserver_template_passivedomains");



	$this->execute_server_commands($serverip,$this->commands); # all commands for whole domains are done in single step.

	# domain array prepared. now, put these in configs.

	#begin: reconstruct apache config file:
	$fileout="# ".date('d/m/Y H:i:s')." This is an automatically generated file, by ehcp. Do not edit this file by hand. if you need to change apache configs, edit (webserver)webserver_template file (to take effect for all domains) in ehcp dir, or use custom http in ehcp gui \n";
	$alanlar=array_keys($arr2[0]); // gets array keys, from first(0th) array element of two-dimensional $arr2 array.

	// following code, replaces fields from template to values here in $arr2 two-dim array. each $arr2 element written to output file according to template file.
	$replacealanlar=arrayop($alanlar,"strop");

	# load webserver config template: (these are a bit different than single server sync_domains, because, here, multiple templates may be used simultaneously, since many servers with different webserver possible..)
	$webserver_template_filename="$this->template_dir/webserver_template"; # template loaded from related dir
	$webserver_template_file_contents=file_get_contents($webserver_template_filename);


	$ssl_enabled=strstr($webserver_template_file_contents,"{webserverip}")!==false; # *1 if template file contains {webserverip} then, ssl is assumed to enabled on apache configs. this case, non-ssl custom http's are disabled to prevent apache config error. All custom http's should be fixed by admin in this case.
	
	if($this->miscconfig['webservertype']=='apache2') {
		# this part should be revised.. sslenabled option is currently only for apache.. 
		if ($ssl_enabled) $this->echoln("ssl seems enabled in this server, because tag {webserverip} is found in apache config templates files..");
		else $this->echoln("ssl seems not enabled in this server, because tag {webserverip} is not found in apache config templates files..");
	}


	foreach($arr2 as $ar1) {// template e gore apache dosyasini olustur
		
		if($this->miscconfig['disablecustomhttp']=='') 
			$webserver_template=$ar1[$webservertype.'template'];# get domain specific (custom) template
		else 
			$webserver_template='';

		if($webserver_template<>'') {
			$this->echoln2("Domain:".$ar1['domainname']." has custom webserver template.");
			$webserver_template.="\n#this last template is read from database for ".$ar1['domainname']."..\n\n";

			if($ssl_enabled and strstr($webserver_template,"{webserverip}")===false){
				$this->echoln("apache config is adjusted as ssl enabled, however, your custom http for this domain contains non-ssl custom http, so, I disable custom http for this domain:".$ar1['domainname']);
				$webserver_template=$webserver_template_file_contents; # read explanation above *1
			} elseif(!$ssl_enabled and strstr($webserver_template,"{webserverip}")!==false){
				$this->echoln("apache config is adjusted as non-ssl enabled, however, your custom http for this domain contains ssl custom http, so, I disable custom http for this domain:".$ar1['domainname']);
				$webserver_template=$webserver_template_file_contents; # read explanation above *1
			}
		} else {
			$webserver_template=$webserver_template_file_contents;
		}

		$webserver_template=str_replace(['{ehcpdir}','{localip}'],[$this->ehcpdir,$this->miscconfig['localip']],$webserver_template);
		$webserver_config=str_replace($replacealanlar,$ar1,$webserver_template);
		$fileout.=$webserver_config;
	}

	$success=writeoutput2($file,$fileout,'w',false);
	# end: reconstruct apache config file:


	echo "\n# ** end process server files for server: $serverip ".date_tarih()."\n===========================================\n";

}

function get_servers($filt){
	$q="select * from servers";
	if($filt<>'') $q.=" where $filt";
	return $this->query($q);
}

function new_sync_domains2(){
	# NON-COMPLETEEE *******

	# Design: many ips may arise in many servers, multi-server, each multi-ip
	# what if same config needs to be sent to more than one server, such as cluster ?

	# functions will be constructed..

	$domain_ips=$this->get_domain_ips();  # find ip's of domains.
	foreach($domain_ips as $ip){		  # for each ip,
		$domains=$this->get_domains_in_ip($ip);   # get domain list
		$this->prepare_webserver_files($domains,$ip);   # build config
		$access_ip=$this->get_access_ip($ip);   # learn access ip of that ip, if a server has many ips, then, only one access ip.
		$this->write_webserver_config_to_access_ip();
	}

	$servers=$this->get_access_ips(); # get server list, using their access ips
	foreach($servers as $ip){
		$this->send_webserver_files($ip);
	}

}

function get_webservers(){
	print __FUNCTION__.": basliyor ** \n";
	# this function reads servers table, then merges it through domain list, thus building dns/web servers,

	$webservers=$this->query("select servertype,ip from servers where servertype in ('apache2','nginx')");
	$localserver=$this->query("select servertype,ip from servers where servertype in ('apache2','nginx') and ip='localhost'");
	if(count($localserver)==0) $webservers[]=['servertype'=>$this->miscconfig['webservertype'],'ip'=>'localhost']; # add localhost if not exist

	#print_r($webservers);
	$w2=[];  # servers in "servers" table
	foreach($webservers as $w) $w2[$w['ip']]=$w['servertype']; # convert to ip=>type pairs

	echo "w2:";
	print_r($w2);

	$doms=$this->getDomains();
	if(is_array($this->last_deleted_domaininfo)) $doms[]=$this->last_deleted_domaininfo; # server of last deleted domain also needs to be updated,so, add that

	$w3=[];

	# find servers related to domains: (including last deleted domain)
	foreach($doms as $dom){
		$ip=trim($dom['webserverips']);
		if($ip=='') $ip='localhost';

		$list=explode(",", $ip);

		foreach($list as $l) {
			$l=trim($l);
			if($l<>'' and ($w3[$l]=='')) {
				$w3[$l]=$w2[$l]; # assign type from list of servers above
				if($w3[$l]=='') $w3[$l]='apache2'; # if there is no type until now, set to default of apache2
			}
		}
	}

	if($w3['localhost']=='') $w3['localhost']='apache2';
	echo "w3:";
	print_r($w3);


	$w4=[];
	foreach($w3 as $ip=>$type) $w4[]=['servertype'=>$type,'ip'=>$ip];#convert back to servers array

	#$keys=array_keys($w3);	foreach($keys as $k) $w4[]=array('servertype'=>$w3[$k],'ip'=>$k); #convert back to servers array
	echo "w4:";
	print_r($w4);



	return $w4;
}

function get_server_access_ip($ip){
	# server ip's and access ips may be different, for servers having more than one ip. so, for accessing a server, we need to know its access ip.
	# otherwise, many settings required.

	$ret=$this->query("select accessip from servers where ip='$ip'");
	$ret=$ret[0]['accessip'];
	if($ret=='') $ret=$ip;
	return $ret;
}

function send_webserver_files($server){
	print __FUNCTION__.": basliyor \n";
	print_r($server);

	if(is_array($serverip)) $serverip=$serverip['ip'];  # accept both until all code is standard.

	/*
	 * Burada, ayn?? sunucuda birden fazla ip i??in ??u yap??labilir:
	 * sunucu i??in bir ip listesi tutulur,
	 * burada g??nderilirken, oradan kontrol edilir.
	 * servers tablosunda: ip, accessip olmal??. bir sunucu tek bir accessip ile tan??mlanmal??, ama birden ??ok di??er ipleri olabilir. bu ??ekilde, bir sunucunun di??er iplerine domain ekleme m??mk??n olabilir.
	 * orada varsa, oradaki yere g??nderilir,
	 * orada yoksa, normal ipye g??nderilir.
	 */

	if(trim($serverip)=='')$serverip='localhost';
	$accessip=$this->get_server_access_ip($serverip);

	if($accessip<>'localhost') my_shell_exec("rsync -arvz $this->ehcpdir/serverfiles/$accessip/webserver/* $accessip:".$this->ehcpdir,__FUNCTION__);
	# for local server, files are directly written to normal destination, no need to send.
}

function send_dnsserver_files($server){ # if remote machine is not yours, then, additional security should be taken here. **
	print __FUNCTION__.": basliyor \n";
	print_r($server);

	if(is_array($serverip)) $serverip=$serverip['ip'];  # accept both until all code is standard.

	if(trim($serverip)=='')$serverip='localhost';
	if($serverip<>'localhost')  my_shell_exec("rsync -arvz $this->ehcpdir/serverfiles/$serverip/named/* $serverip:/etc/bind/",__FUNCTION__);
	# for local server, files are directly written to normal destination, no need to send.
}

function get_dnsservers(){
	#this function is multi_server enabled.
	print __FUNCTION__.": basliyor \n";
	# this function reads servers table, then merges it through domain list, thus building dns/web servers

	$dnsservers=$this->query("select servertype,ip from servers where servertype in ('binddns')");
	$localserver=$this->query("select servertype,ip from servers where servertype in ('binddns') and ip='localhost'");
	if(count($localserver)==0) $dnsservers[]=['servertype'=>'binddns','ip'=>'localhost']; # add localhost if not exist # binddns harici bir sey kullan??lacaksa, modifiye edilmeli.get_webservers a bak.

	#print_r($webservers);
	$w2=[];  # servers in "servers" table
	foreach($dnsservers as $w) $w2[$w['ip']]=$w['servertype']; # convert to ip=>type pairs

	print_r($w2);
	# kontrol edilecek.

	$doms=$this->getDomains();
	$doms[]=$this->last_deleted_domaininfo;




	$w3=[];

	foreach($doms as $dom){
		$ip=$dom['dnsserverips'];
		$list=explode(",", $ip);

		foreach($list as $l) {
			$l=trim($l);
			if($l<>'' and ($w3[$l]=='')) {
				$w3[$l]=$w2[$l]; # assign type
				if($w3[$l]=='') $w3[$l]='binddns'; # if there is no type, set to default of apache2
			}
		}
	}

	if($w3['localhost']=='') $w3['localhost']='binddns';
	print_r($w3);

	$w4=[];
	$keys=array_keys($w3);

	foreach($keys as $k) $w4[]=['servertype'=>$w3[$k],'ip'=>$k]; #convert back to servers array

	print_r($w4);

	return $w4;
}

function prepare_dns_files($server){
	$this->requireCommandLine(__FUNCTION__);

	$serverip=$server['ip'];
	$servertype=$server['servertype'];

	# get domains list first, related to
	if($serverip=='localhost') {
		$filt="dnsserverips is null or dnsserverips='' or dnsserverips like '%localhost%'";
		$dnsserverip=$this->miscconfig['dnsip'];
	} else {
		$filt="dnsserverips like '%$serverip%'";
		$dnsserverip=$serverip;
	}

	if($serverip==localhost) $file=$this->conf['namedbase']."/named_ehcp.conf";
	else $file="$this->ehcpdir/serverfiles/$serverip/named/named_ehcp_produced_by_".$this->miscconfig['dnsip'].'.conf';


	$arr=$this->getDomains($filt);
	if(count($arr)==0) {
		echo "\n domains for this server cannot be found: $serverip, quiting ".__FUNCTION__."! \n";
		writeoutput2($file,"",'w',false); # no domains, so, empty the config
		return True;
	}

	$exampledomain=$arr[0];
	$arr_aliaseddomains=$this->calculateAliasedDomains($arr,$exampledomain);

	# merge two array to one domains array:
	# this array is like 0 => array('domainname'=>'xxx.com')

	foreach($arr_aliaseddomains as $aliasdomain){
		$found=false;
		foreach($arr as $dom) if($aliasdomain['domainname']==$dom['domainname']) $found=True;
		if(!$found) $arr[]=$aliasdomain;
	}

	# put customdns info into zone files..
	$arr_customdns=$this->query("select * from ".$this->conf['customstable']['tablename']." where name='customdns' ");
	$arr2=[];

	foreach($arr as $dom) { # add customdns to array,
		$customdnsvalue='';
		foreach($arr_customdns as $cust) {
			if($cust['domainname']==$dom['domainname']) $customdnsvalue.=$cust['value']."\n"; # this loop prevents repetitive mysql query, thus faster execution.
		}
		$dom['customdns']=$customdnsvalue;
		# will include domain aliases in dns too, to be able to catch those domains with dns
		$arr2[]=$dom;
	}

	echo "\n".__FUNCTION__." working..:  domains for $serverip:\n";
	print_r($arr2);

	passthru2("rm -vf $this->ehcpdir/serverfiles/$serverip/named/*",True,True);

	if(($this->prepare_dns_zone_files($server,$arr2)) and ($this->prepare_dns_named_conf_file($server,$arr2))) {
		# sending of files is done in other function.
		return True;
	}
	else return false;

}

function replace_tags_with_multiple_values($subject,$tags,$values){
	# replaces tags with multiple values,  a line containing a tag is replaced with multiple lines containing values.
	# written for building dns/web configs, generally used in multi-server dns config etc
	# tags: array(tag1,tag2...)   values: array(values1,values2...)  values1=comma separated list of ips/strings

	$lines=explode("\n",$subject);

	foreach($lines as $line) { #process for each line
		$found=false;
		$tagindex=0;
		foreach($tags as $tag) { # process each tag, one line must have only one tag, otherwise, this function will fail
			if(strstr($line,$tag)!==false) {
				$replace=explode(',',$values[$tagindex]);
				foreach($replace as $r) $out.=str_replace($tag,$r,$line)."\n";
				$found=True;
			}
			$tagindex++;
		}
		if(!$found) $out.=$line."\n";
	}

	return $out;
}

function replace_tags($template,$replace_fields,$replace_values){
	return str_replace($replace_fields,$replace_values,$template);// replace domain fields,
}

function build_from_template($template,$tags_with_multiple_values,$multiple_values,$tags,$values){
	echo __FUNCTION__.": basliyor \n";

	$template=$this->replace_tags_with_multiple_values($template,$tags_with_multiple_values,$multiple_values);
	$template=$this->replace_tags($template,$tags,$values);
	return $template;
}

function prepare_dns_zone_files($server,$arr2){
	$this->requireCommandLine(__FUNCTION__);
	echo __FUNCTION__.": basliyor \n";

	$success=True;

	$serverip=$server['ip'];
	$servertype=$server['servertype'];


	//$this->output.=print_r2($arr);
	//print_r($arr);
	$alanlar=alanlarial($this->conn,"domains");
	$replacealanlar=arrayop($alanlar,"strop");  # puts each field in {}
	$replacealanlar[]='{customdns}';

	# eger remote dns ler farkli olacaksa, burasi modifiye edilmeli.
	$dnstemplatefile=file_get_contents($this->dnszonetemplate);// get template once.
	#$dnstemplatefile=file_get_contents("$this->ehcpdir/dnszonetemplate_multiple_servers");// get template once.



	# for old style: # these are not needed if all templates are new style
	$mailserverip=$this->getMailServer();
	$dnsserverip=$this->getDnsServer();
	$webserverip=$this->get_webserver_real_ip();


	foreach($arr2 as $ar1){


		# assign ip addresses for different services..
		if($ar1['serverip']<>''){ # single ip if hosted in a single place,
			$mailserverips=$dnsserverips=$webserverips=$ar1['serverip'];
			$mailip=$webip=$dnsip=$ar1['serverip'];  # for old styles

		} else{

			$dnsserverips=$ar1['dnsserverips'];
			$webserverips=$ar1['webserverips'];
			$mailserverips=$ar1['mailserverips'];

			if($dnsserverips=='') $dnsserverips=$this->miscconfig['singleserverip'];
			if($webserverips=='') $webserverips=$this->miscconfig['singleserverip'];
			if($mailserverips=='') $mailserverips=$this->miscconfig['singleserverip'];

			# for old style:
			$mailip=$mailserverip;
			$dnsip=$dnsserverip;
			$webip=$webserverip;


		}

		$this->echoln2("yaziyor: ".$ar1["domainname"]." mailip/webip/dnsip : $mailserverips/$webserverips/$dnsserverips");

		$dnstemp=$ar1['dnstemplate'];
		if($dnstemp=='') $dnstemp=$dnstemplatefile; // read dns info from template file, if not written to db..
		echo "\n".__FUNCTION__.": buraya geldi.\n";

		# replace any old style tags that may be left on template:
		$replacealanlar=array_merge($replacealanlar,['{serial}','{ip}','{dnsemail}','{mailip}','{dnsip}','{webip}']); # tags to replace
		$ar1=array_merge($ar1,[rand(1,1000),$this->conf['dnsip'],$this->conf['dnsemail'],$mailip,$dnsip,$webip]); # tag contents to put


		$dnstemp=$this->build_from_template($dnstemp,['{dnsserverips}','{webserverips}','{mailserverips}'],[$dnsserverips,$webserverips,$mailserverips],$replacealanlar,$ar1);
		#echo "Hazirlanan dns config: \n$dnstemp";

		# lokalden erisenler icin ayri bir dns, dns icinde view olusturulabilir buraya bak: http://www.oreillynet.com/pub/a/oreilly/networking/news/views_0501.html
		# amac: bir networkde server varsa, o network icinden erisenler icin bu bir local server dir. her desktop da ayri ayri hosts ayari girmek yerine, sunucu bunlara real degil, lokal ip doner.
		# bu sayede, kucuk-orta isletmeler icin, sunucunun lokalden cevap vermesi saglanir.. veya dns icinde view destegi, birden cok konfigurasyon v.b...
		# to translate Turkish comments, use google translate..
		# $dnstemplocal=str_replace(array('{serial}',"{ip}","{dnsemail}"),array(rand(1,1000),$this->conf['dnsip'],$this->conf['dnsemail']),$dnstemp);

		# $temp=str_replace(array('{serial}',"{ip}","{dnsemail}"),array(rand(1,1000),$this->conf['dnsip'],$this->conf['dnsemail']),$temp); // replace serial,ip,dnsemail etc.   Ymds hata veriyordu iptal ettim. bu sorunla ilgilenilecek...
		// verdigi hata: Fatal error: date(): Timezone database is corrupt - this should *never* happen!  thats why i cannot use date in daemon mode... this seems a php bug.., for tr locale

		if($serverip=='localhost') $file=$this->conf['namedbase'].'/'.$ar1["domainname"];
		else $file="$this->ehcpdir/serverfiles/$serverip/named/".$ar1["domainname"];

		$success=$success and writeoutput2($file,$dnstemp,"w");
		#$success=$success and writeoutput2($this->conf['namedbase'].$ar1["domainname"].".local",$dnstemplocal,"w"); # bu kisim henuz tamamlanmadi, yani lokal destegi..

	}
	return $success;

}

function prepare_dns_named_conf_file($server,$arr){
	$this->requireCommandLine(__FUNCTION__);
	$serverip=$server['ip'];
	$servertype=$server['servertype'];

	foreach($arr as $ar){
		$ar['namedbase']=$this->conf['namedbase'];
		$arr2[]=$ar;
	}

	# bind harici dns icin burda modifiye yapilmali
	# if($servertype=='binddns') .....

	$out.=$this->putArrayToStr($arr2,$this->dnsnamedconftemplate);

	if($serverip==localhost) $file=$this->conf['namedbase']."/named_ehcp.conf";
	else $file="$this->ehcpdir/serverfiles/$serverip/named/named_ehcp_produced_by_".$this->miscconfig['dnsip'].'.conf';

	echo "\n\nwriting namedfile: $file \n\n";
	return writeoutput2($file,$out,w);

}

function new_sync_dns(){
	# will be like this:
	$dnsservers=$this->get_dnsservers(); # get server list, server list should determine the type of remote server. currently, default is apache2

	foreach($dnsservers as $w) $this->prepare_dns_files($w);	# for each server in list, call prepare_server_files
	foreach($dnsservers as $w) if($w<>'localhost') $this->send_dnsserver_files($w);  # send server_files(configs) to destinations.
	foreach($dnsservers as $w) $this->restart_dnsserver2($w);

	# done. todo: add domain info to dns as "remote"	: this may be done while adding domain.
	# done. todo: setup domain ftp in remote or nfs/nas: this may be done while adding domain.
	return True;
}

function restart_dnsserver2($serverip){
	if(is_array($serverip)) $serverip=$serverip['ip'];  # accept both until all code is standard.
	echo __FUNCTION__.": basliyor: $serverip dns restarting.. \n";

	if($serverip=='') $serverip='localhost';
	$this->server_command($serverip,"/etc/init.d/{$this->named} restart");
}

function adjust_webmail_dirs(){
	# for squirrelmail, which is bundled in ehcp dir, webmail folder.
	$localconfig="<?php
\$data_dir				 = '$this->ehcpdir/webmail/data/';
\$attachment_dir		   = '$this->ehcpdir/webmail/data/';
?>";

	$success=writeoutput2("$this->ehcpdir/webmail/config/config_local.php",$localconfig,'w',false);
	passthru("chmod a+w $this->ehcpdir/webmail/data/");

	$change_pass_config="<?
\$dbhost='localhost';
\$dbusername='ehcp';
\$dbpass='$this->dbpass';
\$dbname='ehcp';
?>";

	$success=$success && writeoutput2("$this->ehcpdir/webmail/plugins/ehcp_change_pass/config.php",$change_pass_config,'w',false);

	return $success;
}

function new_sync_domains($file='',$checkwebserver=True){
	# will be like this:
	$webservers=$this->get_webservers(); # get server list, server list should determine the type of remote server. currently, default is apache2
	echo __FUNCTION__.":\n";
	print_r($webservers);


	$multiserver=false;
	foreach($webservers as $w){
		if($this->get_server_access_ip($w['ip'])<>'localhost') {
			$multiserver=True;
		}
	}

	if($multiserver) {
		echo "\n".__FUNCTION__.": This seems a multiserver ehcp, has more servers than localhost\n";
		foreach($webservers as $w) $this->prepare_webserver_files('',$w);	# for each server in list, call prepare_server_files
		foreach($webservers as $w) if($w['ip']<>'localhost') $this->send_webserver_files($w);  # send server_files(configs) to destinations.
		foreach($webservers as $w) $this->restart_webserver2($w);
		$this->syncSubdomains();# this func is not multi-server... this is a lack of feature, an important one... under construction.
	} else {
		echo "\n".__FUNCTION__.": This seems NOT a multiserver ehcp, has only localhost as server\n";
		$this->syncDomains();
	}

	# done. todo: add domain info to dns as "remote"	: this may be done while adding domain.
	# done. todo: setup domain ftp in remote or nfs/nas: this may be done while adding domain.
	return True;
}


function new_sync_all(){
		$success=$this->new_sync_domains();
		$success=$success && $this->new_sync_dns();

		if($success) $this->last_deleted_domaininfo=false; # burada kucuk problem c??kabilir
		#$this->conn->AutoExecute('operations',array('op'=>'new_sync_domains3'),'INSERT');  # this is not working, thats why, i need to leave adodb autoinserts..
		return $success;
}


function build_logrotate_conf($arr2,$host){
	if($this->debuglevel>0) print_r($arr2);

	foreach($arr2 as $dom) {
		$logrotate.=$dom['homedir']."/logs/access_log ".$dom['homedir']."/logs/error_log \n";
	}

	$logrotate.="/var/log/ehcp.log \n/var/log/apache_common_access_log";

$logrotate.="

{
daily
missingok
compress
delaycompress
postrotate
	service {$this->miscconfig['webservertype']} reload
endscript
}
";

	passthru2('mkdir -p '.$this->ehcpdir.'/etc/logrotate.d/'); # why first copy to ehcp/etc, then, /etc/logrotate ?
	writeoutput($this->ehcpdir.'/etc/logrotate.d/ehcp',$logrotate,'w',True);

	$cmd="cp -vf ".$this->ehcpdir.'/etc/logrotate.d/ehcp /etc/logrotate.d/';
	if((!$host) or ($host=='localhost')) passthru2($cmd); # bu k??s??m bir fonksiyon yap??labilir.
	else $this->cmds[]=$cmd;	# multi server da kullanmak uzere
}

function generate_password($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $count = mb_strlen($chars);

    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }

    return $result;
}
# yeni
function generate_index_file($dir,$template,$domainname){
	if($this->noExistingIndex("$dir")){
		$filestr=$this->loadTemplate($template); # load template
		$findarray=['webserverip','domainname','localip'];   # replace some variables,
		$localipcode="<?php echo getenv('REMOTE_ADDR'); ?>";

		$replacearray=[$this->getWebServer(),$domainname,$localipcode];
		$findarray2=arrayop($findarray,"strop");
		$fileout=str_replace($findarray2,$replacearray,$filestr);

		writeoutput2("$dir/index.php",$fileout,"w");  # put in index file
		passthru2("chown $this->ftpowner -Rf $dir");
	} # ownership is not changed if some files already exists there..
}

function initializeDomainFiles($dom,$domainname){ # singleserver  mode
	$this->requireCommandLine(__FUNCTION__);
	
	if ($domainname<>''){
		if($dom['domainname']<>$domainname) return ; # do only requested domains.
	}

	// print __FUNCTION__.":execution single domain if any: $domainname \n";
	// print_r($dom);

	$domainname=$dom['domainname'];
	$homedir=$dom['homedir'];
	$this->initialize_domain_dirs($dom);

	# put default index
	$this->generate_index_file("$homedir/httpdocs","defaultindexfordomains",$domainname);

	# adjust some custom file ownerships, for wordpress and some scripts..
	$q="select * from customsettings where domainname='$domainname' and name='fileowner' and `value`<>'root'";
	$ownership=$this->query($q);

	foreach($ownership as $ow) {
		// echo "Adjusting custom file ownership: \n";
		passthru4("chown ".$ow['value']." -Rf $homedir/httpdocs/".$ow['value2']);
		#$this->pwdls('file ownership:',"$homedir/httpdocs/".$ow['value2']);
	}


	# put some files if not exists:
	foreach(['ehcpinfo.html','error_page.html'] as $f) {
		if(!file_exists("$homedir/httpdocs/$f")){
			passthru4("cp -f $f $homedir/httpdocs/");
		}
	}

	writeoutput3($homedir."/UPLOAD_SITE_FILES_TO_httpdocs_FOLDER","","w"); // z7 mod
	passthru4("cp ".$this->ehcpdir."/z7/install_files/.htaccess ".$homedir."/phptmpdir/.htaccess"); // z7 mod
	if($this->noExistingIndex($homedir)){
		passthru4("cp ".$this->ehcpdir."/z7/install_files/domain_index.php ".$homedir."/index.php"); // z7 mod
	}

}





# SYNC DOMAINS FUNCTIONS 

function sync_domains_common_ops() {
	# common ops to be done for all webserver types. independent of webserver type
	# Process timed out domains 
	$q="update domains set status=null where timeout is not null and timeout<now()";
	$this->executequery($q);
}


function sync_domains_apache2($file, $domainname) {
	# to be done new version
}

function sync_domains_nginx($file, $domainname) {
	# will prepare new config file for nginx, testing.. after ok, this will be used. 
	// $file.="_nginx_temp";
	$webservertype=$this->miscconfig['webservertype'];
	$templatefield=$webservertype.'template';
	
	$filt=andle($this->activefilt,"(serverip is null or serverip='') and homedir<>'' order by theorder"); # exclude where serverip is set, that is, for remote dns hosted only domains..
	$arr=$this->getDomains($filt);
	if($this->debuglevel>0) print_r($arr);

	$webserverip=$this->getWebServer();
	echo "\nwebserverip: $webserverip\n";

	$success=True;
	$arr_customhttp=[];
	if($this->miscconfig['disablecustomhttp']=='') 
	$arr_customhttp=$this->query("select * from ".$this->conf['customstable']['tablename']." where name='customhttp'  and (webservertype is null or webservertype='' or webservertype='".$this->miscconfig['webservertype']."')");
	
	$arr2=[];

	$webserver_template_filename="$this->template_dir/webserver_template"; # this file may be an apache template actually, or an nginx template
	$ips=[];

	if($domainname<>'') {
		echo "###>>  syncdomain is initialising files only for a single domain: $domainname !!\n";
	}


	foreach($arr as $dom) { // setup necessry dirs/files if doesnt exist..
		$this->initializeDomainFiles($dom,$domainname);

		# add customhttp to array,
		$customhttpvalue='';

		if($arr_customhttp)
		foreach($arr_customhttp as $cust) {
			if($cust['domainname']==$dom['domainname']) $customhttpvalue.=$cust['value']."\n"; # this loop prevents repetitive mysql query, thus faster execution.
		}

		$dom['customhttp']=$customhttpvalue;

		# add ServerAlias to begining of lines in aliases field
		$aliases=$dom['aliases'];
		$aliasarr=explode("\n",$aliases);
		$newaliases="";
		foreach($aliasarr as $al) if(trim($al)<>'') $newaliases.="server_name ".trim($al).";\n"; # this is like apache, should be checked/tested
		$dom['aliases']=$newaliases;
		$dom['webserverip']=$webserverip; # taken from system ip setting.


		if($dom['webserverips']<>'') {
			list($i)=explode(',',$dom['webserverips']);
			if(validateIpAddress($i)){
				echo "\nThis domain has custom webserverips,adjusting:".$dom['domainname'].":".$dom['webserverips'];
				$dom['webserverip']=$i;   # if entered in db exclusively.  # diger ip ler ne olacak ? sanirim multiserver fonksiyonlarinda halledilecek...
				switch($this->miscconfig['webservertype']){
					case 'apache2': $webserver_template_filename="$this->template_dir/webserver_template_ipbased";break;
					# other servers, if multi-ip supported, it seems no change needed for nginx
				}
				if(!in_array($i,$ips)) $ips[]=$i;
				if(!in_array($webserverip,$ips)) $ips[]=$webserverip; # add default ip too.
			}
		}


		$arr2[]=$dom;
	}

	# here write config to webserver_ehcp.conf file.
	# you may see daemon mode output at logfile, typically tail -f /var/log/ehcp.log from command line

	echo "\n**sync_domains_nginx: Syncing domains for webserver type of (".$this->miscconfig['webservertype']."):\n";
	if($this->debuglevel>0) print_r($arr2);
	if($this->debuglevel>0) print_r($ips);

	$this->build_logrotate_conf($arr2,'localhost');


	# build default file.. (fastcgi_pass etc)
	$find=[
		"{apache_openbasedir_webmail}",
		"{apache_openbasedir_panel}",
		"{apache_openbasedir_site}",
		"{nginx_openbasedir_site}",
		"{nginx_openbasedir_phpmyadmin}",
		"{php_sock}"
	];
	$repl=[
		"php_admin_value open_basedir {ehcpdir}/webmail",
		"php_admin_value open_basedir {ehcpdir}",
		'php_admin_value open_basedir "{homedir}:/usr/share/php:/usr/share/pear"',
		"open_basedir={homedir}:/tmp:/usr/share/php:/usr/share/pear:/usr/share/phpmyadmin:/var/www/new/ehcp/webmail:/usr/share/roundcube",
		"open_basedir=/usr/share/phpmyadmin:/tmp:/usr/share/php:/usr/share/pear",
		$this->php_sock
	];

	$this->replaceArrayPutInFile2("$this->template_dir/default", "/etc/nginx/sites-enabled/default", $find, $repl);


	#begin: reconstruct webserver config file:
	$fileout="# ".date('d/m/Y H:i:s')." This is an automatically generated file, by ehcp. Do not edit this file by hand. if you need to change webserver configs, edit webserver_template(or similar) file (to take effect for all domains) in ehcp dir, or use (custom http or edit webserver/apache template to take effect for single domain) in ehcp gui \n";

	if(count($arr2)>0) {
		$alanlar=array_keys($arr2[0]); // gets array keys, from first(0th) array element of two-dimensional $arr2 array.

		// following code, replaces fields from template to values here in $arr2 two-dim array. each $arr2 element written to output file according to template file.
		$replacealanlar=arrayop($alanlar,"strop");
		$webserver_template_file_contents=file_get_contents($webserver_template_filename);

		$sslenabled=strstr($webserver_template_file_contents,"{webserverip}")!==false;
		# *1 if template file contains {webserverip} then, ssl/ipbased is assumed to enabled on apache configs.
		# in this case, non-ssl/ipbased custom http's are disabled to prevent apache config error. All custom http's should be fixed by admin in this case.

		if($this->miscconfig['webservertype']=='apache2') {
			# this part should be revised.. sslenabled option is currently only for apache.. 

			if ($sslenabled) $this->echoln("ssl/ipbased seems enabled in this server, because tag {webserverip} is found in apache config templates files.."); # this is a bit apache specific code.
			else $this->echoln("ssl/ipbased seems not enabled in this server, because tag {webserverip} is not found in apache config templates files..: $webserver_template_filename");
		}

		foreach($arr2 as $ar1) {// template e gore apache dosyasini olustur
			if($this->miscconfig['disablecustomhttp']=='') {
				$webserver_template=$ar1[$templatefield];# get domain specific (custom) template
			} else {
				$webserver_template='';
				$this->echoln("Note: Custom http is disabled in your server");
			}
			
			if($webserver_template<>'') {
				$this->echoln2("Domain:".$ar1['domainname']." has custom webserver template.");
				$webserver_template.="\n#this last template is read from database for ".$ar1['domainname']."..\n\n";

				if($sslenabled and strstr($webserver_template,"{webserverip}")===false){
					$this->echoln("apache config is adjusted as ssl/ipbased enabled, however, your custom http for this domain contains non-ssl/ipbased custom http, so, I disable custom http for this domain:".$ar1['domainname']);
					$webserver_template=$webserver_template_file_contents; # read explanation above *1
				} elseif(!$sslenabled and strstr($webserver_template,"{webserverip}")!==false){
					$this->echoln("apache config is adjusted as non-ssl/ipbased enabled, however, your custom http for this domain contains ssl/ipbased custom http, so, I disable custom http for this domain:".$ar1['domainname']);
					$webserver_template=$webserver_template_file_contents; # read explanation above *1
				}
			} else {
				$webserver_template=$webserver_template_file_contents;
			}


			if($this->miscconfig['enablewildcarddomain']<>'') $wildcard='*.{domainname}';
			else $wildcard='';

			# replace some fields that does not exist in domain array
			# Security: Check open_basedir
			if(strpos($webserver_template, "open_basedir")!==False) { # 
				$this->echoln("sync_domains_nginx: {$ar1['domainname']} This webserver template contains open_basedir, which should not exist directly. You may need to fix it. This open_basedir will be forbidden soon..");

			}
			/*
			fix open_basedir issue... todo.. 
			Nginx: 2 open_basedir:
			open_basedir={homedir}:/tmp:/usr/share/php:/usr/share/pear:/usr/share/phpmyadmin:/var/www/new/ehcp/webmail:/usr/share/roundcube
			open_basedir=/usr/share/phpmyadmin:/tmp:/usr/share/php:/usr/share/pear

			site:       {nginx_openbasedir_site}
			phpmyadmin: {nginx_openbasedir_phpmyadmin}
			*/
			// $forbidden_keywords=[" open_basedir ","open_basedir="];
			// $forbidden_replace=["", ""];			
			// $webserver_template=str_replace($forbidden_keywords, $forbidden_replace, $webserver_template); # delete any existing forbidden_keywords
			$webserver_template=str_replace($find, $repl, $webserver_template); # 1st pass; fix open_basedir
			$webserver_template=str_replace( # 2nd pass ; fill some system variables
				['{ehcpdir}','{localip}','{wildcarddomain}'],
				[$this->ehcpdir,$this->miscconfig['localip'],$wildcard],
				$webserver_template
			);

			$webserver_config=str_replace($replacealanlar,$ar1,$webserver_template); # 3rd pass ; fill domain specific values
			$webserver_config=$this->ssl_fix_template($ar1['domainname'],$webserver_config);
			$fileout.=$webserver_config;
		}
	}

	$res=writeoutput2($file, $fileout, 'w', false);
	if($res) {
		$this->echoln("\nDomain list exported (syncdomains) webserver conf to: $file \n");
	} else {
		$success=false;
	}
	# end: reconstruct webserver config file---



	#process passive domains
	// $passivedomains=$this->getDomains($this->passivefilt);
	// echo "Passive domains:\n";
	// print_r($passivedomains);

	// $passives=[];
	// foreach($passivedomains as $p){
	// 	if($ssl_enabled){
	// 		$p['webserverip']=$webserverip;
	// 	}
	// 	$this->initializeDomainFiles($p,$domainname);
	// 	$passives[]=$p;
	// }

	// $this->putArrayToFile($passivedomains,"webserver_ehcp_passivedomains.conf","webserver_template_passivedomains");

	// $passiveindex=$this->miscconfig['passiveindexfile'];
	// if($passiveindex=='') $passiveindex=$this->sayinmylang("domain_deactivated_contact_administrator");
	// writeoutput2("/var/www/passivedomains/index.html",$passiveindex,'w',false);
	# end processs passive domains

	# Add a second of wait time between functions. by eric.
	# I've seen some strange issues regarding the exit status of apache2ctl

	// sleep(1);
	// $success=$success && $this->syncApacheAuth();

	// sleep(1);
	// $success=$success && $this->syncSubdomains('',$domainname);

	// sleep(1);
	// $success=$success && $this->configtest_restart_webserver();

	// $this->updateHostsFile();

	// # syncDomains
	// if($this->check_webserver_enabled) {
	// 	if(!$this->is_webserver_running()) {
	// 		$this->echoln2("webserver seems not working...appearantly, some error occured; rolling back to non-ssl mode.");
	// 		$this->fixApacheConfigNonSsl();
	// 	}
	// }

	return $success;
}

function syncDomains($file='',$domainname='') { # porting sync to new function ; cleaning up
	$this->requireCommandLine(__FUNCTION__);
	echo __FUNCTION__.": start syncing domains
		localip:".$this->miscconfig['localip']."
		dnsip:".$this->miscconfig['dnsip']."
		webservertype:".$this->miscconfig['webservertype']."
		";
	
	$this->sync_domains_common_ops();

	$webservertype=$this->miscconfig['webservertype'];
	if($file=='') $file="$this->config_dir/webserver_ehcp.conf";


	
	switch($webservertype) {
		case 'apache2': 
			$this->sync_domains_apache2($file, $domainname); 
			# this will be cancelled after above is ready. this is older function. will move into new ones above. 
			return $this->syncDomains2($file, $domainname);
			break; 
		case 'nginx': 
			# New function for nginx ok, ready
			return $this->sync_domains_nginx($file, $domainname); 
			// return $this->syncDomains2($file, $domainname);
		break; 
		# Add any other webservers here
	}
	
}


function syncDomains2($file='',$domainname='') {
	$this->println("----------------------------- This is old function syncDomains2, will be removed after new functions ok.");
	$webservertype=$this->miscconfig['webservertype'];
	$templatefield=$webservertype.'template';
	
	$filt=andle($this->activefilt,"(serverip is null or serverip='') and homedir<>'' order by theorder"); # exclude where serverip is set, that is, for remote dns hosted only domains..

	$arr=$this->getDomains($filt);
	if($this->debuglevel>0) print_r($arr);

	$webserverip=$this->getWebServer();
	echo "\nwebserverip: $webserverip\n";

	$success=True;
	$arr_customhttp=[];
	if($this->miscconfig['disablecustomhttp']=='') 
	$arr_customhttp=$this->query("select * from ".$this->conf['customstable']['tablename']." where name='customhttp'  and (webservertype is null or webservertype='' or webservertype='".$this->miscconfig['webservertype']."')");
	
	$arr2=[];

	$webserver_template_filename="$this->template_dir/webserver_template"; # this file may be an apache template actually, or an nginx template
	$webserver_template_file_contents=file_get_contents($webserver_template_filename);

	$ips=[];

	if($domainname<>'') {
		echo "###>> syncDomains2: is initialising files only for a single domain: $domainname !!\n";
	}


	foreach($arr as $dom) { // setup necessry dirs/files if doesnt exist..
		$this->initializeDomainFiles($dom,$domainname);

		# add customhttp to array,
		$customhttpvalue='';

		if($arr_customhttp)
		foreach($arr_customhttp as $cust) {
			if($cust['domainname']==$dom['domainname']) $customhttpvalue.=$cust['value']."\n"; # this loop prevents repetitive mysql query, thus faster execution.
		}

		$dom['customhttp']=$customhttpvalue;

		# add ServerAlias to begining of lines in aliases field
		$aliases=$dom['aliases'];
		$aliasarr=explode("\n",$aliases);
		$newaliases="";
		foreach($aliasarr as $al) if(trim($al)<>'') $newaliases.="ServerAlias ".trim($al)." \n"; # this is apache specific code, should be fixed later.
		$dom['aliases']=$newaliases;
		$dom['webserverip']=$webserverip; # taken from system ip setting.


		if($dom['webserverips']<>'') {
			list($i)=explode(',',$dom['webserverips']);
			if(validateIpAddress($i)){
				echo "\nThis domain has custom webserverips,adjusting:".$dom['domainname'].":".$dom['webserverips'];
				$dom['webserverip']=$i;   # if entered in db exclusively.  # diger ip ler ne olacak ? sanirim multiserver fonksiyonlarinda halledilecek...
				switch($this->miscconfig['webservertype']){
					case 'apache2': $webserver_template_filename="$this->template_dir/webserver_template_ipbased";break;
					# other servers, if multi-ip supported, it seems no change needed for nginx
				}
				if(!in_array($i,$ips)) $ips[]=$i;
				if(!in_array($webserverip,$ips)) $ips[]=$webserverip; # add default ip too.
			}
		}


		$arr2[]=$dom;
	}

	# here write config to webserver_ehcp.conf file.
	# you may see daemon mode output at logfile, typically tail -f /var/log/ehcp.log from command line

	echo "\n**syncDomains2: Syncing domains for webserver type of (".$this->miscconfig['webservertype']."):\n";
	if($this->debuglevel>0) print_r($arr2);
	if($this->debuglevel>0) print_r($ips);

	$this->build_logrotate_conf($arr2,'localhost');

	#begin: reconstruct webserver config file:
	$fileout="# syncDomains2:".date('d/m/Y H:i:s')." This is an automatically generated file, by ehcp. Do not edit this file by hand. if you need to change webserver configs, edit webserver_template(or similar) file (to take effect for all domains) in ehcp dir, or use (custom http or edit webserver/apache template to take effect for single domain) in ehcp gui \n";


	$find=[
		"{apache_openbasedir_webmail}",
		"{apache_openbasedir_panel}",
		"{apache_openbasedir_site}",
		"{nginx_openbasedir_site}",
		"{nginx_openbasedir_phpmyadmin}",
		"{php_sock}"
	];
	$repl=[
		"php_admin_value open_basedir {ehcpdir}/webmail",
		"php_admin_value open_basedir {ehcpdir}",
		'php_admin_value open_basedir "{homedir}:/usr/share/php:/usr/share/pear"',
		"open_basedir={homedir}:/tmp:/usr/share/php:/usr/share/pear:/usr/share/phpmyadmin:/var/www/new/ehcp/webmail:/usr/share/roundcube",
		"open_basedir=/usr/share/phpmyadmin:/tmp:/usr/share/php:/usr/share/pear",
		$this->php_sock
	];


	if($this->miscconfig['webservertype']=='apache2') {
		foreach($ips as $i){# eger ipler kullan??lacaksa
			$fileout.="\nNameVirtualHost $i\n";
		}
	} elseif($this->miscconfig['webservertype']=='nginx') {
		# nginx default file.. 
		$this->replaceArrayPutInFile2("$this->template_dir/default", "/etc/nginx/sites-enabled/default", $find, $repl, True);
	}

	if(count($arr2)>0) {
		$alanlar=array_keys($arr2[0]); // gets array keys, from first(0th) array element of two-dimensional $arr2 array.

		// following code, replaces fields from template to values here in $arr2 two-dim array. each $arr2 element written to output file according to template file.
		$replacealanlar=arrayop($alanlar,"strop");
		$sslenabled=strstr($webserver_template_file_contents,"{webserverip}")!==false;
		# *1 if template file contains {webserverip} then, ssl/ipbased is assumed to enabled on apache configs.
		# in this case, non-ssl/ipbased custom http's are disabled to prevent apache config error. All custom http's should be fixed by admin in this case.

		if($this->miscconfig['webservertype']=='apache2') {
			# this part should be revised.. sslenabled option is currently only for apache.. 

			if ($sslenabled) $this->echoln("ssl/ipbased seems enabled in this server, because tag {webserverip} is found in apache config templates files.."); # this is a bit apache specific code.
			else $this->echoln("ssl/ipbased seems not enabled in this server, because tag {webserverip} is not found in apache config templates files..: $webserver_template_filename");
		}

		foreach($arr2 as $ar1) {// template e gore apache dosyasini olustur
			if($this->miscconfig['disablecustomhttp']=='') {
				$webserver_template=$ar1[$templatefield];# get domain specific (custom) template
			} else {
				$webserver_template='';
				$this->echoln("Note: Custom http is disabled in your server");
			}
			
			if($webserver_template<>'') {
				$this->echoln2("Domain:".$ar1['domainname']." has custom webserver template.");
				$webserver_template.="\n#this last template is read from database for ".$ar1['domainname']."..\n\n";

				if($sslenabled and strstr($webserver_template,"{webserverip}")===false){
					$this->echoln("apache config is adjusted as ssl/ipbased enabled, however, your custom http for this domain contains non-ssl/ipbased custom http, so, I disable custom http for this domain:".$ar1['domainname']);
					$webserver_template=$webserver_template_file_contents; # read explanation above *1
				} elseif(!$sslenabled and strstr($webserver_template,"{webserverip}")!==false){
					$this->echoln("apache config is adjusted as non-ssl/ipbased enabled, however, your custom http for this domain contains ssl/ipbased custom http, so, I disable custom http for this domain:".$ar1['domainname']);
					$webserver_template=$webserver_template_file_contents; # read explanation above *1
				}
			} else {
				$webserver_template=$webserver_template_file_contents;
			}


			if($this->miscconfig['enablewildcarddomain']<>'') $wildcard='*.{domainname}';
			else $wildcard='';

			# replace some fields that does not exist in domain array
			# Security: Check open_basedir
			if(strpos($webserver_template, "open_basedir")!==False) { # 
				// $this->echoln("This webserver template contains open_basedir, which should not exist directly. You may need to fix it.");
			}
			# fix open_basedir issue... todo.. 
			/*
			Apache: 3 different open_basedir; 
			webmail: php_admin_value open_basedir {ehcpdir}/webmail
			panel:   php_admin_value open_basedir {ehcpdir}
			site:    php_admin_value open_basedir "{homedir}:/usr/share/php:/usr/share/pear"

			{apache_openbasedir_webmail}
			{apache_openbasedir_panel}
			{apache_openbasedir_site}

			Nginx: 2 open_basedir:
			open_basedir={homedir}:/tmp:/usr/share/php:/usr/share/pear:/usr/share/phpmyadmin:/var/www/new/ehcp/webmail:/usr/share/roundcube
			open_basedir=/usr/share/phpmyadmin:/tmp:/usr/share/php:/usr/share/pear

			site:       {nginx_openbasedir_site}
			phpmyadmin: {nginx_openbasedir_phpmyadmin}
			*/

			# This part will be enabled, after all "open_basedir" problems solved. 
			// $forbidden_keywords=[" open_basedir ","open_basedir="];
			// $forbidden_replace=["", ""];			
			// $webserver_template=str_replace($forbidden_keywords, $forbidden_replace, $webserver_template); # delete any existing forbidden_keywords

			$webserver_template=str_replace($find, $repl, $webserver_template); # 1st pass; fix open_basedir
			$webserver_template=str_replace( # 2nd pass ; fill some system variables
				['{ehcpdir}','{localip}','{wildcarddomain}'],
				[$this->ehcpdir,$this->miscconfig['localip'],$wildcard],
				$webserver_template
			);

			$webserver_config=str_replace($replacealanlar,$ar1,$webserver_template); # 3rd pass ; fill domain specific values
			$webserver_config=$this->ssl_fix_template($ar1['domainname'],$webserver_config);
			$fileout.=$webserver_config;
		}
	}

	$res=writeoutput2($file,$fileout,'w',false);
	if($res) {
		$this->echoln("Domain list exported (syncdomains) webserver conf to: $file \n");
	}
	else $success=false;
	# end: reconstruct webserver config file---



	#process passive domains
	$passivedomains=$this->getDomains($this->passivefilt);
	// echo "Passive domains:\n";
	// print_r($passivedomains);

	$passives=[];
	foreach($passivedomains as $p){
		if($ssl_enabled){
			$p['webserverip']=$webserverip;
		}
		$this->initializeDomainFiles($p,$domainname);
		$passives[]=$p;
	}

	$this->putArrayToFile($passivedomains,"$this->config_dir/webserver_ehcp_passivedomains.conf","webserver_template_passivedomains");

	$passiveindex=$this->miscconfig['passiveindexfile'];
	if($passiveindex=='') $passiveindex=$this->sayinmylang("domain_deactivated_contact_administrator");
	writeoutput2("/var/www/passivedomains/index.html",$passiveindex,'w',false);
	# end processs passive domains

	# Add a second of wait time between functions. by eric.
	# I've seen some strange issues regarding the exit status of apache2ctl

	sleep(1);
	$success=$success && $this->syncApacheAuth();

	sleep(1);
	$success=$success && $this->syncSubdomains('',$domainname);

	sleep(1);
	$success=$success && $this->configtest_restart_webserver();

	$this->updateHostsFile();

	# syncDomains
	if($this->check_webserver_enabled) {
		if(!$this->is_webserver_running()) {
			$this->echoln2("webserver seems not working...appearantly, some error occured; rolling back to non-ssl mode.");
			$this->fixApacheConfigNonSsl();
		}
	}

	return $success;
}

# END SYNC DOMAINS FUNCTIONS 






function updateHostsFile(){
	# update hosts file, so that user on server desktop can reach the website.
	print "--------------------------------\n";
	print "Starting ".__FUNCTION__."\n";
	
	$this->requireCommandLine(__FUNCTION__);
	if($this->miscconfig['updatehostsfile']=='') {
		print "skipping ".__FUNCTION__." because of config. \n";
		return;
	}	
	$count = 0;

	$ip=$this->miscconfig['localip'];
	if(!$ip) $ip=$this->getlocalip2();

	if(trim($ip)=='') {
		print __FUNCTION__.": localip of ehcp is not defined. Define it in settings. \n";
		return True;
	}

	$doms=$this->getDomains();
	#print_r($doms);
	$line="\n" . $ip;
	foreach($doms as $domain) {
		# Limit entries per line to avoid problems due to the line being too long
		# 255 Character Limit Per Line
		if($count == 2){
			$line .= '\n' . $ip;
			$count = 0;
		}
		$line.=" www.".$domain['domainname']." ".$domain['domainname']." mail.".$domain['domainname'];
		$count++;
	}

	# process subdomains as well
	$subdomains=$this->get_subdomains();
	foreach($subdomains as $subdomain) {
		# Limit entries per line to avoid problems due to the line being too long
		# 255 Character Limit Per Line
		if($count == 2){
			$line .= '\n' . $ip;
			$count = 0;
		}
		$subd=$subdomain['subdomain'].".".$subdomain['domainname'];
		$line.=" www.$subd $subd";
		$count++;
	}

	# Causes issues because localhost is already defined in its own line
	// $line.=" localhost";
	// echo "updating hosts file: ip: ($ip)  line: ($line)\n ";
	passthru2("bash /var/www/new/ehcp/updateHostsFile.sh \"$line\"");
	# No longer needed
	//replaceOrAddLineInFile("$ip ",$line,"/etc/hosts");
	echo "update complete\n";

	return True;
}

function initializeLogs($dir){

	passthru4("mkdir -p ".$dir);
	passthru4("mkdir -p ".$dir."/logs");
	$this->write_file_if_not_exists("$dir/logs/access_log","");// these are defined in webserver_template file, bunlarin log_rotate olayi yapilmali.
	$this->write_file_if_not_exists("$dir/logs/error_log","");

	passthru4("chown root:root -Rf $dir/logs"); # bu olmayinca, biri logs dizinini silince, apache hata verip cikiyor.. # suanda access ver error loglar silinemiyor kullanici tarafindan...ancak sunucudan ssh ile silinebilir...!
}

function initializePhpTmpDir($subdir){
	passthru4("mkdir -p $subdir/phptmpdir");
	passthru4("chown $this->ftpowner -Rf $subdir/phptmpdir"); # **** Buradaki problem ??u: phptmpdir i??inde yeni olu??turulan dosyalar -rw------- 1 www-data www-data ??eklinde olu??turuluyor. buras?? da sahipli??ini vsftpd yap??nca, art??k apache bunu silemez oluyor. buray?? -rw-rw--- ??eklinde yapmak laz??m. Problem: http://ehcp.net/?q=node/1351#comment-2831 ; Bunu umask ile ????zd??m san??r??m.
	passthru4("chmod a+w -Rf $subdir/phptmpdir");
}

function initializeDir($dir){
	passthru4("mkdir -p ".$dir);
	passthru4("chmod a+rx ".$dir);
	if($this->skipchown) return; # chown may be too slow if there are (even tens of) thousands of files...
	if ($this->miscconfig['initialize_domain_files']=='') {
		// print "Domain files are not initialized recursively, due to option initialize_domain_files disabled ($dir)\n";
		passthru4("chown $this->ftpowner -f ".$dir);
		passthru4("chmod u+rw -f ".$dir);
		return;
	} else {
		passthru4("chown $this->ftpowner -Rf ".$dir);
		passthru4("chmod u+rw -Rf ".$dir);
	}
	
}

function write_file_if_not_exists($file,$content){
	if(!file_exists($file)) {
		writeoutput($file,$content,'w',false);
	}
}

function initialize_domain_dirs($dom){
	# used in domain&subdomain functions.
	# common dir setups for domain&subdomain

	$homedir=$dom['homedir'];

	$this->initializeDir($homedir);
	$this->initializeDir($homedir.'/httpdocs');
	$this->initializeLogs($homedir);
	$this->initializePhpTmpDir($homedir);

}

# yeni
function initialize_subdomain_files($dom,$domainname){
	if ($domainname<>''){
		if($dom['domainname']<>$domainname) return ; # do only requested domains.
	}
	$homedir=$dom['homedir'];

	$this->initialize_domain_dirs($dom);
	$this->generate_index_file("$homedir/httpdocs","defaultindexforsubdomains","{$dom['subdomain']}.$domainname");
}

function syncSubdomains($file='',$domainname) {
	$this->requireCommandLine(__FUNCTION__);

	echo "\nstart syncing sub domains \n";
	if($file=='') $file="$this->config_dir/webserver_ehcp_subdomains.conf";

	$arr=$this->query("select * from ".$this->conf['subdomainstable']['tablename']);
	$webserverip=$this->getWebServer();
	$success=True;

	$arr2=[];
	$ips=[];
	$webserver_template_filename="$this->template_dir/webserver_subdomain_template";
	$webserver_template_file_contents=file_get_contents($webserver_template_filename);


	if($arr) {
		foreach($arr as $dom) { // setup necessry dirs/files if doesnt exist..
			$subdir=$dom['homedir'];
			// print  "\nProcessing subdir: $subdir \n";

			$this->initialize_subdomain_files($dom,$domainname);

			$dom['customsubdomainhttp']='';
			$dom['webserverip']=$webserverip;

			# modified at 27.4.2014
			if($dom['webserverips']<>'') {
				list($i)=explode(',',$dom['webserverips']);
				if(validateIpAddress($i)){
					echo "\n This subdomain has custom webserverips, adjusting:".$dom['subdomain'].".".$dom['domainname'].":".$dom['webserverips'];
					$dom['webserverip']=$i;   # if entered in db exclusively.  # diger ip ler ne olacak ? sanirim multiserver fonksiyonlarinda halledilecek...
					# todo: this part will be reorganized. if subdomain contains specific IP,this part is wrong in this situation. Config should be built for each subdomain, not in array_to_file below..
					
					$webserver_template_filename="$this->template_dir/webserver_subdomain_template_ipbased";

					// switch($this->miscconfig['webservertype']){
					// 	case 'apache2': $webserver_template_filename="$this->template_dir/webserver_subdomain_template_ipbased";break;
					// 	case 'nginx': $webserver_template_filename="$this->ehcpdir/etc/nginx/webserver_subdomain_template";break;
					// 	# other servers, if multi-ip supported
					// }
					if(!in_array($i,$ips)) $ips[]=$i;
					if(!in_array($webserverip,$ips)) $ips[]=$webserverip; # add default ip too.
				}
			}


			$arr2[]=$dom;
			# arr2 used because, customsubdomainhttp is used or similar...

			if(!file_exists($subdir."/ehcpinfo.html")){
				passthru2("cp -f ehcpinfo.html ".$subdir."/ehcpinfo.html");
			}

		}
	}

	# you may see daemon mode output at logfile, typically tail -f /var/log/ehcp.log from command line
	echo __FUNCTION__.": syncing subdomains:";

	#begin: reconstruct webserver config file:
	$fileout="# syncSubdomains:".date('d/m/Y H:i:s')." This is an automatically generated file, by ehcp. Do not edit this file by hand. if you need to change webserver configs, edit webserver_template(or similar) file (to take effect for all domains) in ehcp dir, or use (custom http or edit webserver/apache template to take effect for single domain) in ehcp gui \n";


	$find=[
		"{apache_openbasedir_webmail}",
		"{apache_openbasedir_panel}",
		"{apache_openbasedir_site}",
		"{nginx_openbasedir_site}",
		"{nginx_openbasedir_phpmyadmin}",
		"{php_sock}"
	];
	$repl=[
		"php_admin_value open_basedir {ehcpdir}/webmail",
		"php_admin_value open_basedir {ehcpdir}",
		'php_admin_value open_basedir "{homedir}:/usr/share/php:/usr/share/pear"',
		"open_basedir={homedir}:/tmp:/usr/share/php:/usr/share/pear:/usr/share/phpmyadmin:/var/www/new/ehcp/webmail:/usr/share/roundcube",
		"open_basedir=/usr/share/phpmyadmin:/tmp:/usr/share/php:/usr/share/pear",
		$this->php_sock
	];

	if(count($arr2)>0) {
		$alanlar=array_keys($arr2[0]); // gets array keys, from first(0th) array element of 
		$replacealanlar=arrayop($alanlar,"strop");
		$sslenabled=strstr($webserver_template_file_contents,"{webserverip}")!==false;

		foreach($arr2 as $ar1) {// template e gore apache dosyasini olustur
			# for subdomains, directly assign template contents. no other check as in domains.
			$webserver_template=$webserver_template_file_contents;

			if($this->miscconfig['enablewildcarddomain']<>'') $wildcard='*.{domainname}';
			else $wildcard='';

			$webserver_template=str_replace($find, $repl, $webserver_template); # 1st pass; fix open_basedir
			$webserver_template=str_replace( # 2nd pass ; fill some system variables
				['{ehcpdir}','{localip}','{wildcarddomain}'],
				[$this->ehcpdir,$this->miscconfig['localip'],$wildcard],
				$webserver_template
			);

			$webserver_config=str_replace($replacealanlar,$ar1,$webserver_template); # 3rd pass ; fill domain specific values
			$webserver_config=$this->ssl_fix_template($ar1['domainname'],$webserver_config);
			$fileout.=$webserver_config;
		}

		$res=writeoutput2($file,$fileout,'w',false);
		if($res) {
			$this->echoln("Domain list exported (syncSubdomains) webserver conf to: $file \n");
		} else $success=false;
		# end: reconstruct webserver config file---
	}

	return $success;
}

function configtest_restart_webserver(){
	$webserver=trim($this->miscconfig['webservertype']);
	if($webserver=='' or !in_array($webserver,['apache2','nginx'])) {
		$this->echoln("webservertype is not defined in settings/config or not recognised. now setting is:($webserver). setting to default of apache2.");
		$webserver='apache2';
	}

	if($webserver=='apache2') {
		passthru2("/etc/init.d/nginx stop");
		$this->echoln2("checking $webserver syntax: ");
		print "Running configtest, apache2ctl: \n";
		system("/usr/sbin/apache2ctl configtest",$ret);
		# burda apache config test ediyor... custom http de abuk subuk seyler girenler nedeniyle...
		# bu olmazsa, apache baslayamiyor ve ehcp arayuzu de, ayni apache dan calistigindan, ulasilamaz hale geliyor..
		# Bu konuda, basic bir python arayuzu yapmayi planliyorum.
		# aslinda ehcp arayuzu farkli/statik bir apache e yonlendirilebilir.. farkli porttan calisan..
		# ayni isi dns icin de yapmak lazim... suanda biri custom dns olarak hatali bisey girse, dns cortlar... onu da ilerde duzeltcem.
		# Exit Code of 8 Means Syntax Error - Anything Else Should Not Impact the Reload of the Apache2 Config - ret changed from 0 to 8
		# To see the exit codes, look at the script source like I did
		print "ret: $ret \n";

		if($ret!=0) { # check this nochmal
			echo "\n $webserver config CONTAINS ERRORS.. \n";
			$this->infotoadminemail("","your ehcpserver-error in $webserver config",false);
			$success=false;
		} else {
			echo "\n $webserver config SEEMS NORMAL CONTINUING LOADING.\n";
			passthru2("/etc/init.d/apache2 restart");
			$success=true;
		}
	} elseif($webserver=='nginx') {
		passthru2("/etc/init.d/apache2 stop");
		system("/etc/init.d/{$this->php_fpm} restart",$ret);
		print "Fpm restart ret: $ret \n";
		var_dump($ret);
		if($ret===False) {
			$this->echoln("php fpm did not succeed to restart. there is a problem... ");
			$success=False;
			$this->fixApacheConfigNonSsl2(); // fallback to apache
		} else {			
			// passthru2("/etc/init.d/{$this->php_fpm} restart");

			$this->echoln("checking $webserver syntax: ");
			$out=shell_exec('/etc/init.d/nginx 2>&1');
			if(!strstr($out,'configtest')) {
				$this->echoln("Your $webserver does not support configtest, so, assuming config ok.\n($out)\n");
				print_r($out);
				passthru2("/etc/init.d/nginx restart");
				return true;
			}

			system("/etc/init.d/nginx configtest",$ret);

			if($ret!=0){
				echo "\n $webserver config CONTAINS ERRORS.. \n";
				$this->infotoadminemail("","your ehcpserver-error in $webserver config",false);
				$success=false;
			} else {
				echo "\n $webserver config SEEMS NORMAL CONTINUING LOADING.\n";
				passthru2("/etc/init.d/nginx restart");
				$success=true;
			}
		}
	}

	$this->webserver_test_and_fallback();

	return $success;
}

function webserver_test_and_fallback(){
	# to be coded later.
	# test if any webserver running, if not, perform a series of fallback operations, such as switch back to apache..

}

function putArrayToFile2($arr,$filename,$template){
	# Difference from 1: replace some special values. 
	$fileout=$this->putArrayToStr($arr,$template);

	$find=[
		"{apache_openbasedir_webmail}",
		"{apache_openbasedir_panel}",
		"{apache_openbasedir_site}",
		"{nginx_openbasedir_site}",
		"{nginx_openbasedir_phpmyadmin}",
		"{php_sock}"
	];
	$repl=[
		"php_admin_value open_basedir {ehcpdir}/webmail",
		"php_admin_value open_basedir {ehcpdir}",
		'php_admin_value open_basedir "{homedir}:/usr/share/php:/usr/share/pear"',
		"open_basedir={homedir}:/tmp:/usr/share/php:/usr/share/pear:/usr/share/phpmyadmin:/var/www/new/ehcp/webmail:/usr/share/roundcube",
		"open_basedir=/usr/share/phpmyadmin:/tmp:/usr/share/php:/usr/share/pear",
		$this->php_sock
	];

	$forbidden_keywords=[" open_basedir ","open_basedir="];
	$forbidden_replace=["", ""];			
	$fileout=str_replace($forbidden_keywords, $forbidden_replace, $fileout); # delete any existing forbidden_keywords

	$fileout=str_replace($find, $repl, $fileout); # 1st pass; fix open_basedir
	$fileout=str_replace( # 2nd pass ; fill some system variables
		['{ehcpdir}','{localip}','{wildcarddomain}'],
		[$this->ehcpdir,$this->miscconfig['localip'],$wildcard],
		$fileout
	);
	# todo: Domain specific values also should be filled. these may be in fields also. may not be problem.

	$res=writeoutput2($filename,$fileout,"w");
	if(!$res) $this->echoln("Failed-Putting some content to file: $filename (".__FUNCTION__.")\n");
	return $res;
}

function putArrayToFile($arr,$filename,$template){
	$res=writeoutput2($filename,$this->putArrayToStr($arr,$template),"w");
	if(!$res) $this->echoln("Failed-Putting some content to file: $filename (".__FUNCTION__.")\n");
	return $res;
}


function putArrayToStr($arr,$template){
	# you should not change this function, as it is being used by other methods too, as I remember; this is a general purpose function
	// bir template e gore dosyaya yazar. array template de yerine koyar. template de array elemanlari {domain} seklinde olmalidir.

	if(!$arr) return "";

	$alanlar=array_keys($arr[0]); // gets array keys, from first(0th) array element of two-dimensional $arr array.

	// following code, replaces fields from template to values here in $arr two-dim array. each $arr element written to output file accourding to template file.
	$replacealanlar=arrayop($alanlar,"strop");
	$templatefile=file_get_contents($template);

	foreach($arr as $ar1) {// template e gore apacehe dosyasn olustur
		$temp=$templatefile;
		$temp=str_replace($replacealanlar,$ar1,$temp);
		$out.=$temp;
	}

	return $out;
}

function putArrayToStrDns($arr){
	# we should better code this, we should use existing function putArrayToStr, or reduce code...
	if(!$arr) return "";

	$alanlar=array_keys($arr[0]); // gets array keys, from first(0th) array element of two-dimensional $arr array.

	// following code, replaces fields from template to values here in $arr two-dim array. each $arr element written to output file accourding to template file.
	$replacealanlar=arrayop($alanlar,"strop");


	foreach($arr as $ar1) {// template e gore apacehe dosyasn olustur
		// Check which template to really use for DNS
		if($ar1["dnsmaster"]<>'') {
			// Use slave template
			$template = $this->dnsnamedconftemplate_slave;
		} elseif ($ar1["dnsmaster"]=='') {
			// Use master template
			$template = $this->dnsnamedconftemplate;
		}

		$templatefile=file_get_contents($template);
		$temp=$templatefile;
		$temp=str_replace($replacealanlar,$ar1,$temp);
		$out.=$temp;
	}

	return $out;
}

function installwp($domainpath, $dbname, $dbusername,$dbuserpass) {
	print "-------------------- Start:".__FUNCTION__."\n";
	
	$domainname=$this->last_inserted_domaininfo['domainname'];
	echo "\n installwp: $domainname, $domainpath, $dbname, $dbusername,$dbuserpass \n";
	$common=" --allow-root --path='$domainpath' ";

	passthru2("wp core config $common --dbname=$dbname --dbuser=$dbusername --dbpass=$dbuserpass",True);
	passthru2("wp core install $common --url=http://www.$domainname --title='my wordpress site' --admin_user=admin --admin_password=$dbuserpass --admin_email={$this->conf['adminemail']}",True);
	passthru2("wp user set-role admin administrator $common",True);
	passthru2("wp theme activate twentythirteen $common",True);
	
	foreach (['twentytwelve','twentythirteen','twentyfourteen'] as $theme) {
		copy("{$this->ehcpdir}/etc/wp/themes/$theme/footer.php","$domainpath/wp-content/themes/$theme/footer.php");
	}

	$this->syncDomains('',$domainname);
	$this->executeQuery("update operations set info3='' where op='installwp' and info='$dbname'"); # delete pass for security reasons.
	print "-------------------- End:".__FUNCTION__."\n";
	return True;
}
function runop2($op,$action,$info,$info2='',$info3=''){
	// for operations that needs more than one argument. such as domain add/delete, especially for daemon mode.
	global $commandline;
	$this->requireCommandLine(__FUNCTION__);

	echo "\n(runop2) op:$op, action:$action, info:$info, info2:$info2, info3:$info3 \n";

	switch ($op) { # info3 is usually server

		case 'certbot'					: return $this->ssl_certbot($info);break;
		case 'syncdomains'				: return $this->syncDomains('',$info);break;
		case 'daemon_backup_domain'		: return $this->daemon_backup_domain($info);break;
		case 'daemondomain'				: return $this->daemondomain($action,$info,$info2,$info3);	break;
		case 'daemonftp'				: return $this->daemonftp($action,$info,$info2,$info3);	break;
		case 'daemonbackup'				: return $this->daemonBackup($action,$info,$info2); break;
		case 'daemonrestore'			: return $this->daemonRestore($action,$info,$info2); break;
		case 'installscript'			: return $this->installScript($action,$info,$info2); break;
		case 'downloadallscripts'		: return $this->downloadAllScripts(); break;
		case 'updatediskquota'			: return $this->updateDiskQuota($info); break;
		case 'service'					: return $this->service($info,$info2); break;
		case 'fixApacheConfigSsl'		: return $this->fixApacheConfigSsl($info);break;
		case 'fixApacheConfigSslOnly'	: return $this->fixApacheConfigSslOnly($info);break;
		case 'installwp'				: return $this->installwp($action,$info,$info2,$info3); break;
		case 'daemon_vps'				: return $this->call_func_in_module('vps_module','daemon_vps',['action'=>$action,'info'=>$info,'info2'=>$info2]); break; # array in this is params
		case 'daemon_vpn'				: return $this->call_func_in_module('vpn_module','daemon_vpn',['action'=>$action,'info'=>$info,'info2'=>$info2]); break; # array in this is params
		case 'generate_ssl_config1'		: return $this->call_func_in_module('ssl_module','generate_ssl_config1',$info); break;
		case "change_domainname"		: return $this->daemon_change_domainname($info,$info2);break;


		default: return $this->errorText("(runop2)internal ehcp error: runop2:Undefined Operation: ".$op." <br> This feature may not be complete-4");
	}// switch

}

function fixMailConfiguration(){
	# this re-runs function mailconfiguration,configurepamsmtp, configureauthmysql, that is, mail related functions in install_lib.php
	# purpose: in case mail/ehcp configuration is corrupted, or ehcp mysql db pass changed, update system configuration accordingly
	# this function was for mail configuration at start, became whole ehcp configuration later.. included vsftpd, net2ftp... and so on..
	$this->requireCommandLine(__FUNCTION__);

	
	$this->write_file_if_not_exists('/etc/mailname','mail.myserver.com') ; # on some systems, this is deleted somehow.
	if(!file_exists('/etc/postfix/main.cf')) passthru2("cp ".$this->ehcpdir."/etc/postfix/main.cf.sample /etc/postfix/main.cf"); # on some systems, this is deleted somehow.

	$params=['ehcppass'=>$this->dbpass];
	$params2=array_merge($params,['ehcpinstalldir'=>$this->conf['ehcpdir']]);

	global $ip,$ehcpmysqlpass,$ehcpinstalldir;
	include('config.php');

	$ip=$this->miscconfig['dnsip'];
	$ehcpmysqlpass=$dbpass;
	$ehcpinstalldir=$this->conf['ehcpdir'];

	$this->adjust_webmail_dirs();
	mailconfiguration($params);
	passthru2('newaliases');


	vsftpd_configuration($params); # reset vsftpd conf too, this is added later than vers 0.29.09
	net2ftp_configuration($params2);

	$this->syncDns();
	$this->syncDomains();

	passthru2("chmod a+w ".$this->ehcpdir."/webmail/data");
	passthru2("chmod a+w ".$this->ehcpdir."/net2ftp/temp");
	passthru2("/etc/init.d/vsftpd restart");
	passthru2("/etc/init.d/postfix restart");

	return True;
}

function downloadAllscripts(){
	$this->requireCommandLine(__FUNCTION__);
	echoln("downloading all scripts- this is not coded yet.. doing nothing.");
	return true;
}

function extract_file($filename,$extractto){
	$ext=get_filename_extension($filename);
	$mydir=getcwd();
	chdir($extractto);
	$ret=True;

	if($ext=='gz') {
	   if(strpos($filename,'.tar.gz')===False) passthru2("gunzip $filename");
	   else   passthru2("tar -zxf $filename");
	} elseif ($ext=='tgz'){
		passthru2("tar -zxf $filename");
	} elseif ($ext=='bz2'){
		if(strpos($filename,'.tar.bz2')===False) passthru2("bunzip2 $filename");
		else passthru2("tar -jxf $filename");
	} elseif ($ext=='zip') {
		passthru2("unzip $filename");
	} elseif ($ext=='rar') {
		passthru2("unrar x $filename");
	} else {
		print "Unsupported extension/Desteklenmeyen dosya uzantisi, extract yap??lmad??... : $ext ";
		$ret=False;
	}

	chdir($mydir);
	return $ret;
}

function download_url($url,$downloadto,$filename=''){
	if($filename=='') $filename=get_filename_from_url($url);

	if(!file_exists("$downloadto/$filename") or filesize("$downloadto/$filename")==0) {
		passthru2("wget --no-check-certificate -O $downloadto/$filename -t 5 --random-wait --wait=3 $url",true);
		if(filesize("$downloadto/$filename")==0 ){
			print "File size seems zero. There is a problem: $url \n";
			return False;
		}
		print "got filename using wget : $filename \n";
	} else {
		print "file already exists, so, not retrieved from net..: $filename \n"; # yanlis cekilmis dosyayi ayrica silmek lazim...
	}

}


function download_file_from_url_extract($url,$downloadto,$extractto,$filename='') {
	print "getting and installing file from url: $url ";
	if($filename=='') $filename=get_filename_from_url($url);

	$this->download_url($url,$downloadto,$filename);

	# dosyayi gecici bir dizine kopyala, sonra icinde ac, sonra icinde bircok dosya varsa direk ....
	passthru2("mkdir -vp $extractto");
	if($downloadto<>$extractto) passthru2("cp -vf $downloadto/$filename $extractto/");

	print "current dir: ".getcwd()."... will extract files... \n\n";

	if(!$this->extract_file($filename,$extractto)) return False;

	if($downloadto<>$extractto) passthru2("rm -vf $filename"); # remove file in tmp dir.
	return True;
}

function insert_custom_setting_direct($params){
	$q="insert into customsettings (domainname,name,`value`,value2) values('{$params['domainname']}','{$params['name']}','{$params['value']}','{$params['value2']}')";
	return $this->executeQuery($q);
}

function get_domain_home($domainname) {
	return $this->getField("domains","homedir","domainname='$domainname'")."/httpdocs";
}

function getAndInstallFile($bilgi,$domainname,$directory){
	$this->requireCommandLine(__FUNCTION__);
	$url=$bilgi['fileinfo']; # burada guvenlik kontrol edilmeli, yoksa baskasi baskasinin domainine biseyler kurar...

	#adjust script install dir
	$scriptdirtocopy=trim($bilgi['scriptdirtocopy']);
	$scriptdirtocopy.="/.";


	$domainhome=$this->get_domain_home($domainname);
	$directory=trim($directory);
	if($directory=='') $targetdirectory=$domainhome;
	else $targetdirectory="$domainhome/$directory/";

/* canceled this check because unable to install into subdomain
	if($directory<>'' and file_exists("$targetdirectory")){
		print "Target directory already exists, so, cancelling script installation.. : $targetdirectory ";
		return False;
	}
*/

	$mydir=getcwd();

	if (strpos($bilgi['scriptname'],"ehcp itself")!==false){
		$filename="ehcp_latest.tgz";
	} else $filename='';


	$tmpdir=$this->conf['ehcpdir']."/scriptinstall/gecici_temp";
	$installdir=$this->conf['ehcpdir']."/scriptinstall";
	passthru2("mkdir $installdir");
	passthru2("rm -rf $tmpdir");

	if(!$this->download_file_from_url_extract($url,$installdir,$tmpdir,$filename='')) return False;


	# copy files to target dir

	passthru2("mkdir -p \"$targetdirectory\"");
	#passthru2("cp -Rf ".$this->conf['ehcpdir']."/scriptinstall/$tmpdir/$scriptdirtocopy/* $targetdirectory");
	# ilginc bir sekilde bu yildizli kopyalama calismadi... yildizi, php icinden gormuyor, no such file or dir diyor... garip.. bu nedenle noktalihale geldi.
	passthru2("rm -rvf \"$targetdirectory/index.html\""); # remove any index.html file already there... this may cause some loss...
	passthru2("cp -Rf $tmpdir/$scriptdirtocopy $targetdirectory");
	passthru2("rm -rf $tmpdir");

	if(!(strpos($bilgi['scriptname'],"ehcp itself")===false)){ # if this is ehcp itself... # download new version of ehcp, overwrite settings&config files. should work directly if you have latest ehcp.
		$settingsfiles=['config.php','webserver_template','dnszonetemplate','webserver_template_passivedomains','webserver_subdomain_template','dnsnamedconftemplate', 'dnsnamedconftemplate_slave'];
		foreach($settingsfiles as $tocopy)
			passthru2("cp -Rf ".$this->conf['ehcpdir']."/$tocopy $targetdirectory");
	}

	print "\nscript dir $scriptdirtocopy copied to: $targetdirectory";


	# burda kopyalama sonrasi islemler..
	# these are commands that are executed after copy... such as chmod a+w somfile.. specific to that script...

	passthru2("chown -Rf vsftpd  $targetdirectory");

	# go to inside that dir...
	chdir($targetdirectory);
	#---------- Custom commands
	echo "\n\ncommands to execute after script copy: (current dir: ".getcwd().") \n";
	print_r($bilgi['commandsaftercopy']);
	foreach(explode("\n",$bilgi['commandsaftercopy']) as $com) {
		$com=trim($com);
		$com=str_replace(['{domainname}','{domainhome}','{targetdirectory}'],[$domainname,$domainhome,$targetdirectory],$com);
		$com=trim($com);
		if($com<>'') passthru2($com);
	}

	#---------- Custom file ownerships.. 
	echo "Custom file ownerships, if any:\n";
	print_r($bilgi['customfileownerships']);
	foreach(explode("\n",$bilgi['customfileownerships']) as $com) {
		$com=trim($com);
		if($com=='') continue;

		$inf=explode("#",$com); # get fileowner and path  owner:group#path format
		if (strstr($inf[0],'root')!==False) continue; # avoid root ownership.

		$inf[1]=str_replace('..','',$inf[1]); # avoid hi-jacking by ../../ ... etc.
		$cmd="chown -Rf ".$inf[0]." $targetdirectory/".$inf[1]; # path is relative to target dir. this way, this does not write to system files, I hope.
		passthru2($cmd); # adjust first..
		$params=['domainname'=>$domainname,'name'=>'fileowner','value'=>$inf[0],'value2'=>"$directory/".$inf[1]];
		$this->insert_custom_setting_direct($params); # insert this for adjusting next time, while syncing...
	}

	/* path for custom permissions:
	 * scripts table: relative path, because, actual install path is not known
	 * customsettings: installpath/path in scripts table
	 * syncdomains: set permissions of "domainhome/path in customsettings" that is, "domainhome/installpath/relativepath" that is "/var/www/vhosts/ftpuser/domain.com/httpdocs/installdir(maybeempty)/wp-admin
	 * */



	chdir($mydir);# return back to original dir
	return True;
}

function installScript($scriptname,$domainname,$directory){
	$this->requireCommandLine(__FUNCTION__);
	print "installing script....: ($scriptname) to dir: ($directory)";

	$q="insert into scripts_log (scriptname,dir,panelusername,domainname,link) values ('$scriptname','$directory','$this->activeuser','$domainname','http://www.$domainname/$directory')";
	$this->executeQuery($q);
	$this->last_inserted_domaininfo=$this->domaininfo=$this->getDomainInfo($domainname);
	$q="select * from scripts where scriptname='$scriptname'";
	$bilgi=$this->query($q);
	$bilgi=$bilgi[0];
	print "\nkurulacak script bilgileri: query: $q \n";
	print_r($bilgi);

	if($bilgi['filetype']=='remoteurlconfig'){ # fileinfo contains, remote url config file of format url=http.....
		$config=file_get_contents($bilgi['fileinfo']);
		print "configfile:".$config;
		$lines=split("\n",$config);
		print_r($lines);
		print " this part is not completed.. use directurl";
	} elseif($bilgi['filetype']=='directurl'){
		return $this->getAndInstallFile($bilgi,$domainname,$directory);
	} else {
		print "\n\nUnknown file type:".$bilgi['filetype']."(File:".__FILE__."Line:".__LINE__.") \n\n";
		return False;
	}

	return true;
}

function listTable($baslik1,$conf_tabloadi,$filtre=""){
	return $this->listTable2($baslik1,$this->conf[$conf_tabloadi],$filtre);
}

function listTable2($baslik1,$tablo,$filtre=""){ # this function is for calling from modules..
	$this->output.="$baslik1<br>";
	$linkimages=$tablo['linkimages'];
	$linkfiles=$tablo['linkfiles'];
	$linkfield=$tablo['linkfield'];
	$sirala=$tablo['orderby'];

	$this->output.=$this->tablolistele3_5_4($tablo['tablename'],$baslik,$tablo['listfields'],$filtre,$sirala,$linkimages,$linkfiles,$linkfield,$listrowstart,$listrowcount).'<br>';
	return true;
}


// extra functions from old dbutil

function ilerigeriekle($kayitsayisi,$baslangic,$satirsayisi,$querystring) {
	if(!isset($baslangic))$baslangic=0;
	if(!isset($satirsayisi))$satirsayisi=10;

	$ilerimiktar=$baslangic+$satirsayisi;
	$self=$_SERVER['PHP_SELF'];
	$querystring=$_SERVER['QUERY_STRING'];
	$querystring=str_replace(["&baslangic=$baslangic&satirsayisi=$satirsayisi","&&"],["","&"],$querystring);
	$self2=$self."?".$querystring;

        if($satirsayisi>0) {
		$sondanoncesi=$kayitsayisi-$satirsayisi;
		$querystring=str_replace("baslangic=$baslangic&satirsayisi=$satirsayisi","",$querystring);

		// asagidaki tabloya bu baslangic tekrar gitmesin diye. asagida zaten ekleniyor.
		//if($querystring<>"")$querystring.="&"; // bialtsatrda ?den sonra yazmak i???n. ileri geride kullanlyor.

		if($ilerimiktar<$kayitsayisi) {
			$ileri="<a href=$self2&baslangic=$ilerimiktar&satirsayisi=$satirsayisi>&gt</a>";
			$son="<a href=$self2&baslangic=$sondanoncesi&satirsayisi=$satirsayisi>&gt&gt</a>";
		} else {
			$ileri="&gt";
			$son="&gt&gt";
		};

		if($baslangic>0) {
			$geri="<a href=$self2&baslangic=".($baslangic-$satirsayisi)."&satirsayisi=$satirsayisi>&lt</a>";
			$bas="<a href=$self2&baslangic=0&satirsayisi=$satirsayisi>&lt&lt</a>";
		} else {
			$geri="&lt";
			$bas="&lt&lt";
		};

		# cok sayida (100 binlerce) kayit olunca, birs??r?? sayfa g??steriyor. bunu engellemek i??in, burada de??i??ik bir mant??k laz??m.
		if($kayitsayisi>20000) $cokkayit=True;

		if($kayitsayisi>$satirsayisi) {
			if($cokkayit) {
				$result2.="Cok sayida kayit var, bu nedenle aralardan sayfalar ornekleniyor.<br>$bas &nbsp  $geri $ileri &nbsp $son<br>";
				$sayfalar="Pages:";
				$bolunecek=$satirsayisi*$kayitsayisi/20000; # nekadar cok kayit varsa, okadar fazla bol, aradan ornekleme yap...
				for($sayfa=0;$sayfa<($kayitsayisi/$bolunecek);$sayfa++)
					$sayfalar.="<a href=$self2&baslangic=".($sayfa*$bolunecek)."&satirsayisi=$satirsayisi>".($sayfa+1)." </a> &nbsp;";
			} else {
				$result2.= round(($baslangic/$satirsayisi)+1).".page:  (".($baslangic+1)."-".($baslangic+$satirsayisi).". records) (in each page $satirsayisi record)<br> $bas &nbsp  $geri $ileri &nbsp $son <br>";
				$sayfalar="Pages:";
				for($sayfa=0;$sayfa<($kayitsayisi/$satirsayisi);$sayfa++)
					$sayfalar.="<a href=$self2&baslangic=".($sayfa*$satirsayisi)."&satirsayisi=$satirsayisi>".($sayfa+1)." </a> &nbsp;";
			}
		}

        };

	if($kayitsayisi>0) $reccount=$this->sayinmylang("recordcount").$kayitsayisi;
	$result2.=$sayfalar.$arama."<br>$reccount<br>";
	return $result2;
}


function tablolistele3_5_4($tablo,$baslik,$alan,$filtre,$sirala,$linkyazi=[],$linkdosya=[],$linkalan=[],$baslangic1=0,$satirsayisi1=20,$aramayap=true,$altbilgi=true,$baslikgoster=true)
{
# this lists table rows in a paged view
//
// ehcp icin modifiye edildi, gelistirildi.
// tablolistele3_4 den fark bilgilerin belli gruplarda listelenmesi. ileri geri tular v.b.
// bir farki da echo yapmaz
// 3_5_2 den fark, mssqlden de okuyabilmesi olacak.. yeni yazyorum. adodb ye gectim.

GLOBAL $aranan,$arananalan,$app,$baslangic,$satirsayisi,$listall;
$this->getVariable(["arananalan","aranan","hepsi",'baslangic','satirsayisi','listall']);

$color1="#FFE8B2";
$color2="#E2E2E2";

if(!isset($baslik)) $baslik=[];
if(!isset($baslangic1)) $baslangic1=$baslangic;
if(!isset($satirsayisi1) or $satirsayisi1==0) $satirsayisi1=$satirsayisi;

if(!isset($baslangic1) or trim($baslangic1)=='') $baslangic1=0;
if(!isset($satirsayisi1) or $satirsayisi1==0 or trim($satirsayisi1)=='' ) $satirsayisi1=10;



$result2='';
$alansayisi=count($alan);
$alansayisi2=@count($linkyazi);
$satirno=0;
$ilkfiltre=$filtre;

#$this->output.="<hr>(ks: $kayitsayisi, iks: $ilkkayitsayisi, filtre: $filtre, ilkfiltre: $ilkfiltre)<hr>";

$ilkkayitsayisi=$this->recordcount($tablo,$ilkfiltre);//$res[0];


// listelemedeki arama fonksiyonlary:


if($listall<>"1") {
	$sess_arananalan=$_SESSION['sess_arananalan'];
	$sess_aranan=$_SESSION['sess_aranan'];
} else {
	$_SESSION['sess_arananalan']='';
	$_SESSION['sess_aranan']='';
}

if($arananalan<>'' or $sess_arananalan<>'')
	$this->output.=" Searchfield:($arananalan), searchvalue:($aranan) , sess_searchfield:($sess_arananalan), sess_searchvalue($sess_aranan)..<br>";

if($aranan<>''){
	$_SESSION['sess_arananalan']=$arananalan;
	$_SESSION['sess_aranan']=$aranan;
	$baslangic1=0;

	if($arananalan==''){
		$this->output.="Aranacak Alan?? belirtmediniz. Bir alan seciniz.";
	} else {
		$filtre=andle($filtre,"$arananalan like '%$aranan%'");
		//$this->output.="Filtre: $filtre <br>";
	}
} elseif($sess_arananalan<>'') { // bu session olayy, arama yapynca sayfalamanyn ???aly?asy i???in
	$filtre=andle($filtre,"$sess_arananalan like '%$sess_aranan%'");
} else {
	$_SESSION['sess_arananalan']='';
	$_SESSION['sess_aranan']='';
}
//------------------ arama bitti -------------------
$kayitsayisi=$this->recordcount($tablo,$filtre);//$res[0];
$topkayitsayisi=$this->recordcount($tablo,'');

if($kayitsayisi==0){
	$result2.=$this->sayinmylang("norecordfound");
	//return $result2;
}

$selectalan=[];
foreach($alan as $al){
		if(is_array($al)) $selectalan[]=$al[0];
		else $selectalan[]=$al;
}

$baslikalan=$selectalan;
if(!in_array($linkalan,$selectalan)) array_push($selectalan,$linkalan);//linkalan yoksa, ekle
//$query=buildquery3("select ".selectstring($selectalan)." from $tablo",$filtre,$sirala,$baslangic,$satirsayisi1);
$query=buildquery2("select ".selectstring($selectalan)." from $tablo",$filtre,$sirala);
$this->queries[]=$query;
$res = $this->conn->selectlimit($query,$satirsayisi1,$baslangic1);


#$this->output.="res:".print_r2($res);
$tr="<tr class='list'>";
$td="<td class='list'>";


if ($res) {

	$result2.= "\n<table id='table$tablo' class='list'>";
	if($kayitsayisi>0 and $baslikgoster)$result2.=tablobaslikyaz($baslikalan,$baslik,$linkyazi);

	while (! $res->EOF ) {
		$r=$res->FetchRow();
		#$this->output.=print_r2($r);

		#if(iseven($satirno)){$satirrengi=$color1;} else {$satirrengi=$color2;};$satirno++;
		#$result2.="<tr bgcolor='$satirrengi'>";
		$result2.=$tr;
		for ($i=0;$i<$alansayisi;$i++) {
				if(is_array($alan[$i])) $al=$alan[$i][0]; else $al=$alan[$i];
				$yaz=htmlspecialchars($r[$al]);
				if($yaz==''){
					$result2.="$td&nbsp</td>";
				} else {
					if(is_array($alan[$i])) {
						$yaz1=$yaz;
						if($alan[$i]['linktext']<>'') $yaz1=$alan[$i]['linktext'];

						if($alan[$i][1]=="sayi") $yaz="<p align=right>".number_format($yaz,2)."</p>";
						if($alan[$i][1]=="link_newwindow") $yaz="<a target=_blank href='$yaz'>$yaz1</a>";
						if($alan[$i][1]=="link") $yaz="<a href='$yaz'>$yaz1</a>";
						if($alan[$i][1]=="image") $yaz="<img src='$yaz'>";
					}
					$result2.="$td$yaz</td>";
				};
				//if($al==$linkalan){$link=$r[$al];};
		};
		$link=$r[$linkalan];

		for ($i=0;$i<$alansayisi2;$i++)	{
				$ly=$linkyazi[$i];
				$ld=$linkdosya[$i];
				$char="&";
				if(strpos($ld,"?")===false)$char="?";
				if(strpos($ld,"href=")===false) $ld="href='$ld";

				$result2.="$td<a $ld$char$linkalan=$link'><img src='$ly' border='0'></a></td>";
		}

		$result2.= "</tr>\n";
	}

	$result2.= "</table>";
	$ilerimiktar=$baslangic1+$satirsayisi1;
	$self=$_SERVER['PHP_SELF'];
	$querystring=$_SERVER['QUERY_STRING'];
	$self2=$self."?".$querystring;

	if($altbilgi) $result2.=$this->ilerigeriekle($kayitsayisi,$baslangic1,$satirsayisi1,$querystring);


	// aramalarn ayarlanmas.

	if($aramayap and $topkayitsayisi>5){
		$arama="<form method=post>".$this->sayinmylang('search_').": ".buildoption2("arananalan",$alan,$arananalan)."<input type=text name=aranan value='$aranan'><input type=submit value=".$this->sayinmylang('search_')."></form>";
		$result2.=$arama;
	}

	if(($aranan<>'' or $arananalan<>'' or $_SESSION['sess_arananalan']<>'' or $filtre<>'')and($ilkkayitsayisi>$kayitsayisi)){ # sonuclarda arama, filtreleme yapildi ise, filtrelemeyi kaldir..
		$result2.="<a href=$self2&listall=1>".$this->sayinmylang('list_all')."</a><br>";
	}

} else {
	$this->error_occured("(tablolistele_3_5_4)","query:$query");
};
// $result2.="<br>tablo bitti.<br>";
//echo "<br> filtre: ($filtre)  query: ($query) <br>";
$result2.=$this->conn->ErrorMsg();
return $result2;
}//fonksiyon



function isTrue($param,$str='',$returnit=False){ # this is a test function to figure out a variable type, true or false ?
	$found=false;
	$this->output.="<hr>$str - starting checks-(isTrue)<hr>";
	if($param===true){
		$ret="<b>this is exact true</b><hr>";
		$this->output.=
		$found=true;
	}
	if($param===false){
		$ret="<b>this is exact false</b><hr>";
		$found=true;
	}
	if($param===null){
		$ret="<b>this is exact null</b><hr>";
		$found=true;
	}
	if($param===0){
		$ret="<b>this is exact 0 - zero</b><hr>";
		$found=true;
	}
	if($param===""){
		$ret="<b>this is exact '' - empty</b><hr>";
		$found=true;
	}
	if($param===[]){
		$ret="<b>this is exact empty array</b><hr>";
		$found=true;
	}
	if($found===false) {
		$ret="This variable is not true,false,0,null or empty array <br>this seems:"
		.gettype($param)."<br>"
		.(is_resource($param)?get_resource_type($param):"")."<br>"
		;

	}
	$this->output.=$ret;
	$this->output.="<br>finished isTrue.<hr>";
	if($returnit) return $ret;
}


function sifreHatirlat(){ # password reminder
	$tarih=date_tarih();
	global $email,$panelusername,$hash;
	$this->getVariable(["email",'panelusername','hash']);

	if($email<>"") {

		#validate email:
		$kayitliemail=$this->getField($this->conf['logintable']['tablename'],'email',"email='$email'");
		$filt="email='$email'";

		if($kayitliemail<>''){

			if(!$hash){
				$hash=get_rand_id(10);
				$r=$this->executequery("insert into  hash (email,hash)values('$email','$hash')");
				if(!$r) return false;

				$msg="ehcp: \nSomebody at ($this->clientip) requested to reset your pass.\ngo to this url to reset your pass: ".$this->ehcpurl."/?op=sifrehatirlat&email=$email&hash=$hash \nif you are accessing your server locally, replace ip in this url with local ip of server";
				mail($email,$this->sitename.'- password reset info',$msg,$this->headers);
				$this->output.="Password reset info is sent to your email. (pass is same yet)";
				return;
			}


			# get username
			$filt2=$filt;
			if($panelusername<>'') $filt2="$filt and panelusername='$panelusername'";
			$username=$this->getField($this->conf['logintable']['tablename'],$this->conf['logintable']['usernamefield'],$filt2);

			#validate hash
			$filt3="$filt and hash='$hash'";
			$sayi=$this->recordcount("hash",$filt3);
			if($sayi==0) $this->errorTextExit("Wrong password reset info, verify the password reset url in your email");



			#reset pass
			$yenisifre=get_rand_id(5);
			if($email=='info@ehcp.net') $ek=",status='active'";
			$s=$this->executeQuery("update ".$this->conf['logintable']['tablename']." set ".$this->conf['logintable']['passwordfield']."=md5('$yenisifre') $ek where email='$email'",'update user pass','update user pass');
			if($s){
				$msg="Your password is reset as ($yenisifre) Your username is ($username) Thank you for using $this->sitename -dnsip:".$this->dnsip.$this->conf['dnsip'];;
				mail($email,$this->sitename.'- password reset info',$msg,$this->headers);
				$this->echoln("Your pass is sent to your email. <br>");
				$this->executequery("delete from hash where $filt3"); # delete hash after verify
			}

		} else {
			$this->output.='No such email';
		}
	} else {
		$inputparams=[
			['email','lefttext'=>'Enter your email:'],
			['panelusername','righttext'=>'leave empty if you dont remember'],
			['op','hidden','default'=>__FUNCTION__]
		];

		$this->output.=inputform5($inputparams);
		#inputform4($action,array('Enter your email:'),array('email'),[],array("op"),array('sifrehatirlat'));
	}
	return true;
}

function getlocalip($interface='eth0') {
	global $localip;
	$ipline=exec("ifconfig $interface 2>/dev/null | grep \"inet addr\" ");
	$ipline=strstr($ipline,"addr:");
	$pos=strpos($ipline," ");
	$ipline=trim(substr($ipline,5,$pos-5));
	$localip=$ipline;
	return $ipline;
}

function get_interface(){
	$int=exec("ifconfig | grep RUNNING | grep -v LOOPBACK | head -1 | awk -F: '{print $1}' ");
	$int=substr($int, 0, 20);
	// print "Found interface: $int\n";
	return $int;
}

function getlocalip2($interface='eth0') {
	global $localip;
	// print __FUNCTION__." running\n";
	if($localip<>'') return $localip;

	$ip='';
	$int2=$this->get_interface();
	if($ip=='') $ip=$this->getlocalip($interface);
	if($ip=='') $ip=$this->getlocalip($int2);
	if($ip=='') $ip=$this->getlocalip('eth1');
	if($ip=='') $ip=$this->getlocalip('eth2');

	if($ip=='') {
		$ipline=exec("ifconfig | grep 'inet ' | grep 'addr' | grep 255.255 | grep -v '127.0.0' ");
		$ipline=strstr($ipline,"addr:");
		$pos=strpos($ipline," ");
		$ip=trim(substr($ipline,5,$pos-5));

	}
	if($ip=='') {
		$ip=trim(exec("ifconfig $int2 | grep inet | grep netmask | awk '{print \$2}'"));
	}

	if($ip=='') $ip=trim(gethostbyname(gethostname())); # last method..
	if($ip=='') {
		echo "Your ip cannot be determined automatically... \nYour ip may be one of:\n";
		system("ifconfig | grep 'inet addr:'  2>/dev/null ");
	}
	
	$localip=$ip;
	return $ip;
}


function infoMailUsingWget($str){
	# send email when php email function, or email server of server not functioning.. using remote php script..
	global $user_name,$user_email,$localip;
	echo "please wait...\n";
	$ip=$this->getlocalip2();
	$str=str_replace(" ","_",$str); # because in url, space breaks variable msg in url
	#$command="wget -q -O /dev/null --timeout=15 \"http://www.ehcp.net/diger/msg.php?msg=ip:$ip,$str&name=$user_name&email=$user_email&ip=$ip\"";
	# that command had some errors, so, i switched to file_get_contents.
	$url="http://www.ehcp.net/diger/msg.php?msg=ip:$ip,$str&name=$user_name&email=$user_email&ip=$ip";
	//echo "command: $command \n";
	#passthru2($command);
	file_get_contents($url);
}


}// end class

?>