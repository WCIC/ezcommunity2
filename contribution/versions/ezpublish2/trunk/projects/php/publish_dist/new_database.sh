#!/bin/sh
# This script will create the publish MySQL database with all the patches applied.
# by Chris Mason
#vars
mysqldb='sql/publish_mysql.sql'
postgresdb='sql/publish_postgresql.sql'
mysqldata='sql/data_mysql.sql'
default_dbname='publish'

modules=`ls -d ez*`
rm -f $postgresdb
rm -f $mysqldb

for module in $modules
do
    if [ -f $module/sql/postgresql/$module.sql ]
   then cat $module/sql/postgresql/$module.sql >> $postgresdb
    fi
    if [ -f $module/sql/mysql/$module.sql ]
   then cat $module/sql/mysql/$module.sql >> $mysqldb
    fi
done


echo -n "Name of Database to create [publish]:"
read DBNAME
if [ -n "$DBNAME" ]; then
   echo -n 'Database is' $DBNAME
else
   DBNAME="$default_dbname"
fi
echo "Database "$DBNAME" will be created"

echo -n "mysql or postgres [mysql]:"
read DBTYPE
if [ -n "$DBTYPE" ]; then
   echo -n 'Database is' $DBTYPE
else
   DBTYPE="mysql"
fi

if [ "$DBTYPE" = "postgres" ]; then
echo "Sorry, not available yet"
break
fi

echo "Database type "$DBTYPE" will be created"
echo -n 'Mysql root password: '
read PASS
if [ -n "$PASS" ]
then
   echo "Password is $PASS"
   
   echo "Dropping database"

      if  mysqladmin -u root -p'$PASS' drop $DBNAME
      then
         echo "Dropping database"
      else
         echo "No database to drop"
      fi

   echo "Creating database"
   mysqladmin -u root -p'$PASS' create $DBNAME
   echo "Adding Tables"
   mysql -u root -p'$PASS' $DBNAME < $mysqldb
   echo "Adding Data"
   mysql -u root -p'$PASS' $DBNAME < $mysqldata 
   mysql -u root -p'$PASS' -e"grant all on $DBNAME.* to $DBNAME@localhost identified by '$DBNAME' " 
else
   echo "Blank Password"
   echo "Dropping database"

   if  mysqladmin -u root drop $DBNAME
   then
      echo "Dropping database"
   else
      echo "No database to drop"
   fi

   echo "Creating database"
   mysqladmin -u root create $DBNAME
   echo "Adding Tables"
   mysql -u root $DBNAME < $mysqldb
   echo "Adding Data"
   mysql -u root $DBNAME < $mysqldata 
   mysql -u root -e"grant all on $DBNAME.* to $DBNAME@localhost identified by '$DBNAME' "   
fi


