#!/bin/bash
#
# unpack using tar -xvzf filename.tar.gz
#
# typically the site consists of a git clone
# combined with the result of this package
# and then if you delete the archive after unpacking
# you should have a clean git status
# and a working site
#
if [ $# -lt 1 ]; then
  echo 1>&2 "$0: not enough arguments"
  exit 2
elif [ $# -gt 1 ]; then
  echo 1>&2 "$0: too many arguments"
  exit 3
fi
tar zcvf $1.tar.gz log priv_cache priv_data data/md2/data/user data/md2/data/swf data/md2/data/css data/md2/data/js/tinymce log apps/md2/config/config.php apps/md2/config/database.php .htaccess crossdomain.xml google*.html favicon.ico apps/md2/views/styles apps/md2/views/page apps/md2/views/modules apps/md2/views/form apps/md2/views/email apps/md2/views/body
