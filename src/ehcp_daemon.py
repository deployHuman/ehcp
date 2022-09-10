#!/usr/bin/env python

import cherrypy
import os,sys
import MySQLdb,datetime
from classapp import Application

conf={
	'logintable':{			
		'tablename':'panelusers',
		'passwordtype':'md5',
		'usernamefield':'panelusername',
		'passwordfield':'password'
	}
}



def isEmpty(input):
	if input==None:
		return True		
	if input=='':
		return True			
	else:
		return False


class Application1(Application):
	# Incomplete.. 

	def __init__(self):
		global conf
		
		self.initialize()
		self.conf=conf


	def error_occured(self,sender):
		self.output+=sender+' An error occured..'
		return False

		
	def htmlekle(self,id):
		return self.alanal('html','htmlkodu',"id='"+id+"'")
		
		
	def isPasswordOk(self,username,password,usernamefield='',passwordfield=''):
		if (isEmpty(usernamefield)):
			usernamefield=self.conf['logintable']['usernamefield'];
		if (isEmpty(passwordfield)):
			passwordfield=self.conf['logintable']['passwordfield'];
		if (isEmpty(usernamefield)):
			usernamefield='username';
		if (isEmpty(passwordfield)):
			passwordfield='password';
		if (self.conf['logintable']['passwordtype']=='md5'):
			where=""+usernamefield+"='"+username+"' and md5('"+password+"')="+passwordfield+"";
		else:
			where=""+usernamefield+"='"+username+"' and '"+password+"'="+passwordfield+"";
		sayi=self.kayitsayisi(self.conf['logintable']['tablename'],where);
		if (sayi==False):
			#echo "<hr>buraya geldiii..</hr>";
			self.error_occured("dologin2");
			return False;
		if (sayi==0):
			return False;
		elif (sayi>0):
			return True;
			
	def doLogin2(self,username,password):
		if self.isPasswordOk(username,password):
			self.logedin_username=username; # burdaki logedin_username app classinin.
			self.islogedin=True;
			return True
		else:
			self.logedin_username='';
			self.islogedin=False;
			return False


			

app = Application()
print "\n ehcp-python daemon starting... \n This is ehcp_daemon.py, An experimental daemon backend for ehcp, written in python, \n To run real/current ehcp daemon, run '/etc/init.d/ehcp start' command instead \n You may connect to http://yourip:8080 to see this server backend.."



class OnePage(object):
	def index(self,username='',password=''):
		return "one page!, userpass:",username,password
	index.exposed = True
	
class twoPage(object):
	def index(self,a='5'):
		return "two page!"+a
	index.exposed = True

 
class HelloWorld(object):
	activeuser=''
	logedin_username=''
	sayi=0	
	

	def index(self,displayBottom=True):		
		out='Hi, this is ehcp-python background proses, under development now... you will find usefull tools here soon... <br>check back later... <br>on future versions of ehcp <br>'
		out+='<b>Planned tools here:</b><br> server stats, apache reset/restart in case a configuration failure in apache... <br>'
		out+="<hr>Login to ehcp backend using admin password:<br><form action=login method=post>username: <input type=text name=username><br>password: <input type=password name=password><br><input type=submit></form>"
		if displayBottom: out+=self.bottom()
		
		print "ehcp index (user:%s)\n"  % (self.logedin_username)

		return out
		
	index.exposed = True
	
	def bottom(self):
		self.sayi+=1
		return "<br><a href='/mainMenu'>Home/Anamenu</a> - \
		<a href='/operations?op=logout'>logout</a> - \
		<a href='/index'>login</a><br>\
		<hr>This is a very basic controlling way for your ehcp, in case you cannot reach your webserver otherwise.(activeuser:%s) sayi:%s\n"  % (self.logedin_username,self.sayi)
	
	def checkLogin(self):
		if self.logedin_username=='':
			print "not loged in"
			return False
		else:
			return True
		
	
	def mainMenu(self):
		if not self.checkLogin():
			return self.index()

		ret="<br>Mini Main Menu:<br>\
		<a href='/operations?op=dofixapacheconfignonssl'>fixapacheconfignonssl (rebuild apache config with ssl disabled, repairs many things)</a><br>\
		<a href='/operations?op=dosyncdomains'>sync Domains</a><br>"+self.bottom()
		return ret
		
	mainMenu.exposed=True
	
	def secureString(self,str):
		return str.replace("'",'').replace('%','').replace("\\",'')
	
	def login(self,username='',password=''):
		username=self.secureString(username)
		password=self.secureString(password)
		
		if app.doLogin2(username,password):
			self.logedin_username=username
			self.activeuser=True
			print "ehcp login success (user:%s)\n"  % (self.logedin_username)
			ret="<br>pass OK. you logged in. <br> "+self.mainMenu()
		else:
			self.logedin_username=''
			self.activeuser=False

			print "ehcp login fail (user:%s)\n"  % (self.logedin_username)
			ret="<br>yanlis sifre, <a href=/>tekrar deneyiniz.. </a> "
			
		return " userpass:",username,ret
	
	login.exposed = True
	
	def default(self,args):
		return "yanlis url girdiniz:  ",args

	default.exposed=True

	def operations(self,op):
		if not self.checkLogin():
			return self.index()
			
		msg=''
		if op=="dosyncdomains":
			self.addDaemonOp('syncdomains','','','')
		elif op=='logout':
			self.logedin_username=''
			self.activeuser=''
			msg+="<b>Logout complete </b><br>"+self.index(False)
		elif op=='dofixapacheconfignonssl':
			self.addDaemonOp('fixapacheconfignonssl','','','')
		else:
			msg+=" <b>Bilinmeyen talimat/komut- Unknown command:"+op+" </b><br>"
		
		msg+="<br>("+op+" komutu calistirildi)<br>"
		print msg + "(user:%s)\n"  % (self.logedin_username)
		
		return msg+" <hr>"+self.bottom()
		
		
	operations.exposed=True
		

	def addDaemonOp(self,op,action,info,info2='',opname=''):
		#return $this->executeQuery("insert into operations (op,action,info,info2,tarih) values ('$op','$action','$info','$info2','".date("Y-m-d H:i:s")."')",' sending info to daemon ('.$opname.')'); # date fonksiyonu hatasindan dolayi iptal
		query="insert into operations (op,action,info,info2,tarih) values ('%s','%s','%s','%s','')" % (op,action,info,info2);
		app.executequery(query); 
		
	
	



root = HelloWorld()
print app.conf
cherrypy.quickstart(root)
