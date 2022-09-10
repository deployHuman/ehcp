#!/usr/bin/python
# -*- coding: utf-8 -*-
"""
Base Application class for database and similar functions.
"""

import os,sys,time,subprocess,socket,traceback
import shutil
import MySQLdb,datetime
import MySQLdb.cursors
import configparser, os

class Application:
	input=''
	output=''
	filefinished=False
	dbhost=''
	dbname=''
	dbusername=''
	dbuserpassword=''

	activeuser=''
	logedin_username=''

	def initialize(self):
		self.connecttodb()

	def __init__(self):
		self.initialize()
		self.hataayikla=False

	def findValueInStr(self,str,find):
		if str.count(find)<0:
			return ""
		ret=str[len(find)+1:]
		return ret
		

	def readDbConfig(self):
		print('reading config...')
		ehcpdir=''
		dosya='/etc/ehcp/ehcp.conf'
		
		try:
			config = configparser.ConfigParser()
			config.read(dosya)
			ehcpdir=config.get('ehcp','ehcpdir')
		except Exception as e:
			try:
				f=open(dosya)
			except:
				print("Error:dosya acarken hata olustu.",dosya)
				return
				
			for i in f:
				i=i.strip()
				if i.count('ehcpdir=')>0:		    
					ehcpdir=self.findValueInStr(i,'ehcpdir')
			
		if ehcpdir!='':
			print("found ehcpdir setting:",ehcpdir)
		else:
			print("ehcpdir setting not found in (/etc/ehcp/ehcp.conf) !")
			return
		
		try:
			dosya=ehcpdir+'/config.php'
			f=open(dosya)
		except:
			print("Error:dosya acarken hata olustu.",dosya)
			return
			
		for i in f:
			i=i.strip()
			if i.count('$dbhost')>0:
				self.dbhost=self.findValueInStr(i,'$dbhost')[1:-2]
			if i.count('$dbname')>0:
				self.dbname=self.findValueInStr(i,'$dbname')[1:-2]
			if i.count('$dbusername')>0:
				self.dbusername=self.findValueInStr(i,'$dbusername')[1:-2]
			if i.count('$dbpass')>0:
				self.dbuserpassword=self.findValueInStr(i,'$dbpass')[1:-2]
		print("db settings:",self.dbhost,self.dbname,self.dbusername)


	def connecttodb(self,db='ehcp'):
		print("connecting to db.. ",db)
		self.readDbConfig()
		try:
			hostn=socket.gethostname()
			passw=''

			self.connection = MySQLdb.connect(self.dbhost, self.dbusername, self.dbuserpassword, self.dbname,cursorclass=MySQLdb.cursors.DictCursor)
			#self.connection = MySQLdb.connect("localhost", "root", passw,db,cursorclass=MySQLdb.cursors.DictCursor)
			self.conn=self.connection.cursor()
			self.connection.set_character_set('utf8')
			self.conn.execute("set names utf8")
			self.conn.execute("SET CHARACTER SET utf8")
			self.conn.execute("SET COLLATION_CONNECTION = 'utf8_turkish_ci'")
			self.conn.execute("SET autocommit= on")
			self.connected=True
			#print "Baglanti tamam."
		except Exception as ex:
			print("Veritabanina baglanirken hata olustu.",db,str(ex))

	def checkDuplicateProgram(self):
		pid=os.getpid()
		# bu programin ismini de al.
		progname=subprocess.getoutput("ps ax | grep -v grep | grep "+pid.__str__()+" | awk '{print $6}'")
		progname=progname.strip()
		progname=progname.split('/').pop()  # path ile beraber yazinca boylece, sondaki programi aliyor. 
		print("Progname:",progname)
		
		# ismi ayni olan, ancak pidi farkli baska program var mi ?
		otherprog=subprocess.getoutput("ps ax | grep -v grep | grep '"+progname+"' | grep -v "+pid.__str__()+" | awk '{print $5 \" \" $6}'")
		otherprog=otherprog.strip()	
		
		if otherprog !='':
			progs=subprocess.getoutput("ps ax | grep -v grep | grep '"+progname+"'")
			print(progs,"\n")
			print("program zaten calisiyor. ",pid," cikiyor...")		
			
			sys.exit()


	def query(self,q,debugprint=False):
		try:
			if debugprint: print(q)
			self.conn.execute(q)
			return self.conn.fetchall()
		except Exception as e:
			print("** query islenirken hata olustu:",q,repr(e))
			self.traceback()
			#traceback.print_exc(file=sys.stdout)
			#print '-'*60
			return False

	def kayitsayisi(self,tablo,where=''):
		q="select count(*) as sayi from "+tablo
		if where!='':
			q=q+" where "+where
		res=self.query(q)
		if type(res)==type(None):
			print("kayitsayisi: kayit alinamadı:",q,res)
			return -1
		elif len(res)==0:
			print("kayitsayisi: kayit alinamadı:",q,res)
			return -1
		else:
			return res[0]['sayi']

	def executequery(self,q,debugprint=False):
		try:
			if debugprint: print(q)
			return self.conn.execute(q)
		except Exception as ex:
			print("++query islerken hata olustu:",q,str(ex))
			#self.traceback()

			if str(ex).count('gone away'):
				self.connecttodb()
				try:
					return self.conn.execute(q)
				except:
					pass

			if self.hataayikla:
				raise

			return False


	def traceback(self):
		if os.path.exists("./traceit"):
			print('-'*60)
			traceback.print_stack()
			print('-'*60)
		if os.path.exists("./raiseit"):
			raise

	def escape(self,s):
		# http://stackoverflow.com/questions/9942594/unicodeencodeerror-ascii-codec-cant-encode-character-u-xa0-in-position-20
		if type(s)==str:
			return self.connection.escape_string(s.encode('utf-8'))
		else:# type(s)==str:
			return self.connection.escape_string(s)


	def esc(self, s):
		ret=self.escape(s)
		if type(ret)==bytes:
			return ret.decode()
		else:
			return ret