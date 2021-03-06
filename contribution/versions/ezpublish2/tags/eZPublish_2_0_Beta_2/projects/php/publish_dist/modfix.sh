#!/bin/sh
echo "Creating symbolic links and setting permissions as needed."
chmod 666 site.ini

touch error.log
chmod 666 error.log

# [cache section]
# This part will create the cache dirs which are needed and make sure
# that they are writeable by php.

dirs="
admin/tmp
ezimagecatalogue/catalogue
ezimagecatalogue/catalogue/variations
ezfilemanager/files
ezpoll/cache
ezarticle/cache
eznewsfeed/cache
ezforum/cache
ezlink/cache
ezarticle/admin/cache
ezad/admin/cache
ezarticle/admin/cache
ezcontact/admin/cache
ezforum/admin/cache
ezlink/admin/cache
eznewsfeed/admin/cache
ezpoll/admin/cache
ezstats/admin/cache
eztodo/admin/cache
ezuser/admin/cache
eztrade/admin/cache
eztrade/cache
ezaddress/admin/cache
ezcalendar/user/cache
ezbug/user/cache
ezbug/admin/cache
ezexample/admin/cache
"

for dir in $dirs
do
    if [ -d $dir ]; then
	    echo "$dir already exist"
    else
        echo "Creating $dir"
	    mkdir -p $dir
    fi
    chmod 777 $dir   
done

# [admin section]
# This part will link the modules into the admin directory

files="
error.log
ezlink
site.ini
ezforum
ezarticle
ezad
classes
ezclassified
ezimagecatalogue
ezfilemanager
ezpoll
ezuser
ezsession
ezcontact
ezstats
eztodo
eznewsfeed
eztrade
ezaddress
ezbug
ezexample
"

for file in $files
do
    if [ -d admin/$file ]; then
	    echo "admin/$file already exist"
    else
	    echo "Linking ./$file to admin/$file"
	    ln -s ../$file admin/$file
    fi
done
