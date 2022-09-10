cd /etc/init.d
for i in courier-*
do
./$i restart
done

./postfix restart