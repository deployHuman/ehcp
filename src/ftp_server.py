#!/usr/bin/python
# -*- coding: utf-8 -*-
"""
Ftp server using python, 
Authenticate users through ehcp database in mysql. 

For use instead of vstfpd or other ftp server in ehcp. 
Being tested now.

pip install pyftpdlib
apt-get install clamav
freshclam

"""

import logging
from classapp import Application
from pyftpdlib.authorizers import DummyAuthorizer  # On error: pip install pyftpdlib
from pyftpdlib.authorizers import AuthenticationFailed
from pyftpdlib.handlers import FTPHandler
from pyftpdlib.servers import FTPServer
import os,pwd,grp,time

try:
    ftpuid = pwd.getpwnam("vsftpd").pw_uid
    ftpgid = grp.getgrnam("www-data").gr_gid
except Exception, e:
    print "Ftp kullanıcı ve grup tanımları yok.. "
    raise

infected_dir="/var/www/new/infected"
"""
if not os.path.exists(infected_dir):
	os.mkdir(infected_dir)
"""
virus_scan=False # clamav şuanda, en küçük dosyada bile 15 sn civarında sürüyor, bu haliyle kullanılması zor. 


class My_Handler(FTPHandler):
    def on_file_received(self, file):
        print "file received:",file
        os.chown(file, ftpuid, ftpgid)
        if virus_scan: os.system("clamscan --move={} {}".format(infected_dir,file))


    def on_incomplete_file_received(self, file):
        print "incomplete file received:",file
        os.chown(file, ftpuid, ftpgid)
        if virus_scan: os.system("clamscan --move={} {}".format(infected_dir,file))



class Ehcp_Authorizer(DummyAuthorizer):

    def validate_authentication(self, username, password, handler):
        initdb()
        wh="ftpusername='{}' and password('{}')=`password`".format(username,password)
        say=app.kayitsayisi("ftpaccounts",wh)
        #print wh,say
        if say==0:
        	raise AuthenticationFailed("")
        else:
        	print "validate_authentication: Login OK... "


def initdb():
    try:
        app.conn.execute("select now()")
    except:
        print "reconnecting..."
        app.connecttodb()


app=Application()


authorizer = Ehcp_Authorizer()
q="select * from ftpaccounts"
for i in app.query(q):
	homedir=i['homedir']
	if homedir in [None,'']:
		homedir="/var/www/vhosts/"+i['ftpusername']
	print "User defined:",i['ftpusername'],homedir
	if not os.path.exists(homedir): os.mkdir(homedir)
	authorizer.add_user(i['ftpusername'],i['password'],homedir, perm="elradfmw")
	#print authorizer.user_table

handler = My_Handler
handler.authorizer = authorizer
handler.banner = "Welcome to python based EHCP Ftp Server, managed by EHCP (Easy Hosting Control Panel, www.ehcp.net) (Beta)"
logging.basicConfig(filename='/var/log/ehcp_ftpd.log', level=logging.INFO)

print "Ftp server starting in 2 sec.. "
time.sleep(2)
server = FTPServer(("0.0.0.0", 2121), handler)

server.max_cons = 256
server.max_cons_per_ip = 5


server.serve_forever()