#!/bin/bash
# mysql root pass reset utility. by info@ehcp.net
clear
echo "This will reset your msyql root pass"
echo "Only continue if you lost mysql root pass and you know what you do"
echo "if you have other programs that use old mysql root pass, you need to fix them manually."
echo 
echo "press enter to continue or Ctrl-C to cancel"
read

echo
echo "Please wait..."
echo
mkdir /var/run/mysqld              
chown mysql:mysql /var/run/mysqld  


/etc/init.d/mysql stop
mysqld_safe --skip-grant-tables &
sleep 5
echo
echo
echo "Enter NEW mysql root pass:"
read newpass

# echo "UPDATE mysql.user SET authentication_string=PASSWORD('$newpass'), plugin='mysql_native_password' WHERE User='root'; flush privileges;" | mysql -u root
echo "UPDATE mysql.user SET Password=PASSWORD('$newpass'), plugin='mysql_native_password' WHERE User='root'; flush privileges;" | mysql -u root

if [ $? -eq 0 ] ; then
	/etc/init.d/mysql restart
	echo
	echo
	echo "mysql root pass reset COMPLETE .... "

else
	echo "Error reseting pass. Something went wrong."
fi

# UPDATE mysql.user SET Password=PASSWORD('1234') WHERE User='root'; flush privileges;
