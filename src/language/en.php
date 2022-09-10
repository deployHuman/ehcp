<?php
// ehcp language file
$this->lang['en']=
[
'undefined_limittype'=>'Undefined limittype.',
'norecordfound'=>'Nothing/No Record Found.',
'recordcount'=>'Record Count:',
'areyousuretodelete'=>'Are you sure to delete ? <br>',
'error_occured'=>'Error Occured ',
'write_yourmessage_here'=>'Write Your Messsage Here',
'name_surname'=>'Name Surname',
'enter_yourcontactinfo'=>'Enter Your Contact Info Here',
'yourmessage_received'=>'Thanks. We Received Your Message.',
'search_'=>'Search',
'list_all'=>'List All',
'int_undefined_limittype'=>'internal ehcp error: undefined limittype',
'perhaps_db_error'=>"This may mean that your db settings are not correct, you cannot connect to db; or your db/html table has missing rows.<br> You may edit config.php for ehcp mysql user password. <br> Try<a class='button-ehcp' href=misc/mysqltroubleshooter.php>mysql troubleshooter</a> to solve this problem.",
'welcome to world'=>'Welcome to World...',
'Panel Home'=>'Panel Home/Deselect',
'Domain Home'=>'Domain Home',
'Domains'=>'Domains',
'Selected Domain'=>'Selected Domain',
'domain_deactivated_contact_administrator'=>'This domain is deactivated by owner/admin. Contact Your domain administrator..  / Bu domain, sahibi ya da yoneticisi tarafindan kapatilmistir. yonetici ile irtibata geciniz.',
'updatehostsfile'=>'This machine is used for Desktop access too (Update hosts file with domains)',
'dnsip'=>'dnsip (outside/real/static ip of server)',
'dnsipv6'=>'dnsip V6(outside/real/static V6 ip of server)',

'initialize_domain_files'=>'Initialize domain files on domain ops',
'initialize_domain_files_righttext'=>'You may disable this, if you have huge number of files in domains..depending on your storage speed. Applicable on existing domains only. For new domains, all related dirs/files are initialized independent of this setting.',

'switchtoapacheonerror'=>'Switch to apache on error',
'switchtoapacheonerror_righttext'=>'If some error occurs in current webserver config, ehcp tries to switch to a failsafe apache config',

'disablecustomhttp'=>'Disable Custom http',
'disablecustomhttp_righttext'=>'All custom http disabled, this is automatically activated when there are error in webserver configs.',

'disableeditapachetemplate'=>'Disable Custom http for non-admins',
'disableeditwebservertemplate'=>'Disable Custom webserver http for non-admins',
'disableeditdnstemplate'=>'Disable Custom dns for non-admins',
'turnoffoverquotadomains'=>'Turn off over quota domains',
'domain_format_wrong'=>'(Sub)Domain format is not suitable, check it..',

# These are separated to this lang file, to enable editing html of pages easily, for ex, add buttons for functions..
'similar_functions_ftp' => "<a class='button-ehcp' href='?op=addftpuser'>Add New ftp</a> <a class='button-ehcp' href='?op=addftptothispaneluser'>Add ftp Under My ftp</a> <a class='button-ehcp' href='?op=addsubdirectorywithftp'>Add ftp in a subDirectory Under Domainname</a> <a class='button-ehcp' href='?op=addsubdomainwithftp'>Add subdomain with ftp</a> <a class='button-ehcp' href='?op=add_ftp_special'>Add ftp under /home/xxx (admin)</a> <a class='button-ehcp' href='net2ftp' target=_blank>WebFtp (Net2Ftp)</a> <a class='button-ehcp' href='?op=addcustomftp'>Add Custom FTP Account (Admins Only)</a> <a class='button-ehcp' href='?op=listallftpusers'>List All Ftp Users</a> ",
'similar_functions_mysql' => "<a class='button-ehcp' href='?op=domainop&amp;action=listdb'>List / Delete Mysql Db's</a> <a class='button-ehcp' href='?op=addmysqldb'>Add Mysql Db&amp;dbuser</a> <a class='button-ehcp' href='?op=addmysqldbtouser'>Add Mysql db to existing dbuser</a> <a class='button-ehcp' href='?op=dbadduser'>Add Mysql user to existing db</a> <a class='button-ehcp' href='/phpmyadmin' target=_blank>phpMyadmin</a>",
'similar_functions_email' => "<a class='button-ehcp' href='?op=listemailusers'>List Email Users / Change Passwords</a> <a class='button-ehcp' href='?op=addemailuser'>Add Email User</a> Email forwardings: <a class='button-ehcp' href='?op=emailforwardings'>List</a> <a class='button-ehcp' href='?op=addemailforwarding'>Add</a> <a class='button-ehcp' href='?op=bulkaddemail'>Bulk Add Email</a> <a class='button-ehcp' href='?op=editEmailUserAutoreply'>edit Email Autoreply</a> ,<a class='button-ehcp' href='webmail' target=_blank>Webmail (Squirrelmail)</a>",
'similar_functions_domain' => "<a class='button-ehcp' href='?op=addDomainToThisPaneluser'>Add Domain To my ftp user (Most Easy)</a> <a class='button-ehcp' href='?op=adddomaineasy'>Easy Add Domain (with separate ftpuser)</a> <a class='button-ehcp' href='?op=adddomain'>Normal Add Domain (Separate ftp&panel user)</a> <a class='button-ehcp' href='?op=bulkadddomain'>Bulk Add Domain</a> <a class='button-ehcp' href='?op=adddnsonlydomain'>Add dns-only hosting</a> <a class='button-ehcp' href='?op=adddnsonlydomainwithpaneluser'>Add dns-only hosting with separate paneluser</a>-<br><a class='button-ehcp' href='?op=addslavedns'>Make Domain a DNS Slave</a> <a class='button-ehcp' href='?op=removeslavedns'>Remove DNS Slave, if any</a><br><br>Different IP(in this server, not multiserver): <a class='button-ehcp' href='?op=adddomaineasyip'>Easy Add Domain to different IP</a> <a class='button-ehcp' href='?op=setactiveserverip'>set Active webserver IP</a><br>List Domains: <a class='button-ehcp' href='?op=listselectdomain'>short listing</a> <a class='button-ehcp' href='?op=listdomains'>long listing</a>",
'similar_functions_redirect' => "<a class='button-ehcp' href='?op=editdomainaliases'>Edit Domain Aliases</a>",
'similar_functions_customhttpdns' => "Custom Http: <a class='button-ehcp' href='?op=customhttp'>List</a> <a class='button-ehcp' href='?op=addcustomhttp'>Add</a> Custom dns: <a class='button-ehcp' href='?op=customdns'>List</a> <a class='button-ehcp' href='?op=addcustomdns'>Add</a>-  Custom Permissions: <a class='button-ehcp' href='?op=custompermissions'>List</a> <a class='button-ehcp' href='?op=addcustompermission'>Add</a>",
'similar_functions_subdomainsDirs' => "SubDomains: <a class='button-ehcp' href='?op=subdomains'>List</a> <a class='button-ehcp' href='?op=addsubdomain'>Add</a> <a class='button-ehcp' href='?op=addsubdomainwithftp'>Add subdomain with ftp</a> <a class='button-ehcp' href='?op=addsubdirectorywithftp'>Add subdirectory with ftp (Under domainname)</a> <a class='button-ehcp' href='?op=sync_directories'>Sync Directories</a>",
'similar_functions_HttpDnsTemplatesAliases' => "<a class='button-ehcp' href='?op=editdnstemplate'>Edit dns template for this domain </a> <a class='button-ehcp' href='?op=editwebservertemplate'>Edit webserver template for this domain </a> <a class='button-ehcp' href='?op=editdomainaliases'>Edit Aliases for this domain </a>",
'similar_functions_panelusers' => "<a class='button-ehcp' href='?op=listpanelusers'>List All Panelusers/Clients</a> <a class='button-ehcp' href='?op=resellers'>List Resellers</a> <a class='button-ehcp' href='?op=addpaneluser'>Add Paneluser/Client/Reseller</a>",
'similar_functions_server' => "<a class='button-ehcp' href='?op=listservers'>List Servers/IP's</a> <a class='button-ehcp' href='?op=addserver'>Add Server</a> <a class='button-ehcp' href='?op=addiptothisserver'>Add ip to this server</a> <a class='button-ehcp' href='?op=setactiveserverip'>set Active webserver IP</a>",
'similar_functions_backup' => "<a class='button-ehcp' href='?op=dobackup'>Backup</a> <a class='button-ehcp' href='?op=dorestore'>Restore</a> <a class='button-ehcp' href='?op=listbackups'>List Backups</a>",
'similar_functions_vps' => "<a class='button-ehcp' href='?op=vps'>VPS Home</a> <a class='button-ehcp' href='?op=add_vps'>Add new VPS</a> <a class='button-ehcp' href='?op=copy_vps_image'>Copy VPS Image</a> <a class='button-ehcp' href='?op=settings&group=vps'>VPS Settings</a> <a class='button-ehcp' href='?op=vps&op2=other'>Other Vps Ops</a>",
'similar_functions_pagerewrite' => "<a class='button-ehcp' href='?op=pagerewrite'>page rewrite home</a> <a class='button-ehcp' href='?op=pagerewrite&op2=add'>add page rewrite</a>",
'similar_functions_custompermissions' => "<a class='button-ehcp' href='?op=custompermissions'>List Custom Permissions</a> <a class='button-ehcp' href='?op=addcustompermission'>Add Custom Permissions</a>",
'similar_functions_vpn' => "<a class='button-ehcp' href='?op=list_vpn'>List Vpn Users</a> <a class='button-ehcp' href='?op=add_vpn'>Add Vpn User</a>",

'similar_functions_options'=> 
"
	<br><a class='button-ehcp' href='?op=options&edit=1'>Edit/Change Options</a>
	<br><a class='button-ehcp' href='?op=changemypass'>Change My Password</a>
	<br><a class='button-ehcp' href='?op=listpanelusers'>List/Add Panelusers/Resellers</a>
	<br><a class='button-ehcp' href='?op=dosyncdns'>Sync Dns</a>
	<br><a class='button-ehcp' href='?op=dosyncdomains'>Sync Domains</a>
	<br><a class='button-ehcp' href='?op=dosyncftp'>Sync Ftp (for non standard home dirs)</a>
	
	<br><a class='button-ehcp' href='?op=advancedsettings'>Advanced Settings</a>
	<br><a class='button-ehcp' href='?op=dofixmailconfiguration'>Fix Mail Configuration<br>Fix ehcp Configuration</a> (This is used after changing ehcp mysql user pass, or if you upgraded from a previous version, in some cases)
	<br><a class='button-ehcp' href='?op=dofixapacheconfigssl'>Fix Apache Configuration with ssl</a>(use with caution,may be risky)
	<br><a class='button-ehcp' href='?op=dofixapacheconfignonssl'>Fix Apache Configuration without ssl</a>
	<br><a class='button-ehcp' href='?op=dofixapacheconfignonssl2'>Fix Apache Configuration without ssl, way2</a> use this if first wone does not work. this disables custom apache configurations, if any

	<br><a class='button-ehcp' href='?op=dump_config'>Dump Configuration</a>
	<br>
	<a class='button-ehcp' href='?op=listservers'>List/Add Servers/ IP's</a>
	
	<br>
	Roles: <a class='button-ehcp' href='?op=list_roles'>List</a> <a class='button-ehcp' href='?op=assign_role'>Assign Role</a>

	<br>
	Experimental:
	<br><a class='button-ehcp' href='?op=donewsyncdns'>New Sync Dns - Multiserver</a>
	<br><a class='button-ehcp' href='?op=donewsyncdomains'>New Sync Domains - Multiserver</a>
	<br><a class='button-ehcp' href='?op=multiserver_add_domain'>Multiserver Add Domain</a>
	"
,

'select_language'=>
"
(
<a class='button-ehcp' href='?op=setlanguage&id=en'>En</a>
<a class='button-ehcp' href='?op=setlanguage&id=tr'>Tr</a>
<a class='button-ehcp' href='?op=setlanguage&id=german'>German</a>
<a class='button-ehcp' href='?op=setlanguage&id=spanish'>Spanish</a>
<a class='button-ehcp' href='?op=setlanguage&id=fr'>Fr</a>
<a class='button-ehcp' href='?op=setlanguage&id=lv'>Latvie&#353;u</a>
<a class='button-ehcp' href='?op=setlanguage&id=it'>Italian</a>
<a class='button-ehcp' href='?op=setlanguage&id=zh'>Taiwan</a>
)
<br>
<table>
<tr>
<td style='padding: 3px;'><a class='button-ehcp' href='?op=setlanguage&id=en'><img height=30 width=50 src=images/en.jpg border=0></a></td>
<td style='padding: 3px;'><a class='button-ehcp' href='?op=setlanguage&id=tr'><img height=30 width=50 src=images/tr.jpg border=0></a></td>
<td style='padding: 3px;'><a class='button-ehcp' href='?op=setlanguage&id=german'><img height=30 width=50 src=images/german.jpg border=0></a></td>
<td style='padding: 3px;'><a class='button-ehcp' href='?op=setlanguage&id=spanish'><img height=30 width=50 src=images/spanish.jpg border=0></a></td>
<td style='padding: 3px;'><a class='button-ehcp' href='?op=setlanguage&id=fr'><img height=30 width=50 src=images/fr.jpg border=0></a></td>
<td style='padding: 3px;'><a class='button-ehcp' href='?op=setlanguage&id=lv'><img height=30 width=50 src=images/lv.jpg border=0></a></td>
<td style='padding: 3px;'><a class='button-ehcp' href='?op=setlanguage&id=it'><img height=30 width=50 src=images/it.jpg border=0></a></td>
<td style='padding: 3px;'><a class='button-ehcp' href='?op=setlanguage&id=zh'><img height=30 width=50 src=images/zh.jpg border=0></a></td>

</tr>
</table>
",

'dns_warning'=>"Careful, this a dangerous thing, you should know about dns configuration syntax!<br>",
'webserver_warning'=>"Careful, this a dangerous thing, you should know about webserver ({webserver}, currently active) configuration syntax!<br>if syntax is broken, a series of fallback operations will be done to make your panel reachable, such as rebuilding config using default apache configuration<br>",
'enter_alias'=>"Enter one alias per line one by one<br>
	Example:<br>
	www.domain2.com<br>
	www.domain3.com<br>
	other.domain2.com<br>
	<hr>",
"_Success adding server"=>"Success adding server",
'_Failed adding server'=>'Failed adding server',
'ftp_username_cannot_be_empty'=>'Ftp username cannot be empty.. ',
'custom_http_disabled'=>"Custom http disabled in options. Go to Options for enabling. (custom http may be automatically disabled when there are error in custom http's. Check your custom http list.)",
'add_custom_http'=>"Adding custom http CONFIGURATON for domain ($domainname) and your current webserver of (".$this->miscconfig['webservertype'].")<br>(Note that this custom http configuration will be active whenever your current webserver type is active, You should KNOW WEBSERVER Configuration syntax, otherwise, you will lose connection to your panel, except if there is any python emergency backend..):
	<br>
	<br>
	Do NOT ENTER here any html code.. this is for webserver configuration.. 

	",
'subdomains_related_to_this_domain'=>'<br><br>Subdomains related to this domain:',
];




?>
