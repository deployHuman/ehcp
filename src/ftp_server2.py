#!/usr/bin/python3 -u
# -*- coding: utf-8 -*-
"""
Ftp server using python, Coded by Ehcp, All rights reserved
Authenticate users through ehcp database in mysql. 

For use instead of vstfpd or other ftp server in ehcp. 
Being tested now.

pip install pyftpdlib
apt-get install clamav
freshclam


Differences from ftp_server.py : 
users may be reloaded by a USR1 system signal, no need to restart whole program.. 
handles pyftpdlib error: https://github.com/giampaolo/pyftpdlib/issues/400

"""

import logging
from classapp import Application
from pyftpdlib.authorizers import DummyAuthorizer  # On error: pip install pyftpdlib
from pyftpdlib.authorizers import AuthenticationFailed
from pyftpdlib.handlers import FTPHandler
from pyftpdlib.servers import FTPServer
import os,pwd,grp,time,signal

prefix="Ehcp ftpserver:"

def log_print(s):
    print(s)
    logging.info(s)

def rebuild_users():
    log_print(prefix+"Rebuilding/adding users:")
    initdb()
    authorizer = Ehcp_Authorizer()
    q="select * from ftpaccounts"
    for i in app.query(q):
        homedir=i['homedir']
        if homedir in [None,'']:
            homedir="/var/www/vhosts/"+i['ftpusername']
        print((prefix,"User defined:",i['ftpusername'],homedir))
        if not os.path.exists(homedir): os.mkdir(homedir)
        authorizer.add_user(i['ftpusername'],i['password'],homedir, perm="elradfmw")

    del(server.handler.authorizer)
    server.handler.authorizer=authorizer


def signal_handler(signal, frame):
    print(("-"*20))
    log_print(prefix+"USR1 signal received..")
    rebuild_users()
    print(("-"*20))

signal.signal(signal.SIGUSR1, signal_handler)

try:
    ftpuid = pwd.getpwnam("vsftpd").pw_uid
    ftpgid = grp.getgrnam("www-data").gr_gid
except Exception as e:
    print("Ftp kullanıcı ve grup tanımları yok.. ")
    raise

infected_dir="/var/www/new/infected"
"""
if not os.path.exists(infected_dir):
	os.mkdir(infected_dir)
"""
virus_scan=False # clamav şuanda, en küçük dosyada bile 15 sn civarında sürüyor, bu haliyle kullanılması zor. 


class My_Handler(FTPHandler):
    def on_file_received(self, file):
        print((prefix,"file received:",file))
        os.chown(file, ftpuid, ftpgid)
        if virus_scan: os.system("clamscan --move={} {}".format(infected_dir,file))


    def on_incomplete_file_received(self, file):
        print((prefix,"incomplete file received:",file))
        os.chown(file, ftpuid, ftpgid)
        if virus_scan: os.system("clamscan --move={} {}".format(infected_dir,file))



class Ehcp_Authorizer(DummyAuthorizer):

    def validate_authentication(self, username, password, handler):
        initdb()
        wh=f"ftpusername='{app.esc(username)}' and password('{app.esc(password)}')=`password` and (status is null or status in ('', 'active')) and (expire_date is null or expire_date in ('') or expire_date>now())"
        say=app.kayitsayisi("ftpaccounts",wh)
        #print wh,say
        if say==0:
            print((prefix,"Ehcp: Login failed to ftp server:",username))
            raise AuthenticationFailed("")
        else:
        	print((prefix,"validate_authentication: Login OK... "))


    def has_perm(self, username, perm, arg):
        #print prefix,"has_perm:",username,perm,arg
        #return super(Ehcp_Authorizer, self).has_perm(username,perm,arg)
        # once user has loged in, it has all perm related to ftp..
        return True


    def get_msg_quit(self, username):
        if username not in self.user_table:
            return "" # because of https://github.com/giampaolo/pyftpdlib/issues/400
        else:
            return super(Ehcp_Authorizer, self).get_msg_quit(username)



def initdb():
    try:
        app.conn.execute("select now()")
    except:
        print((prefix,"reconnecting..."))
        app.connecttodb()


app=Application()


handler = My_Handler
handler.banner = "Welcome to python based EHCP Ftp Server, managed by EHCP (Easy Hosting Control Panel, www.ehcp.net) (Beta)"
handler.authorizer = None
logging.basicConfig(filename='/var/log/ehcp_ftpd.log', level=logging.INFO)

print((prefix,"server starting in 2 sec.. "))
time.sleep(2)
server = FTPServer(("0.0.0.0", 2121), handler)
server.max_cons = 256
server.max_cons_per_ip = 5
rebuild_users()


server.serve_forever()
