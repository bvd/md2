#!/bin/bash
if [ $# -lt 1 ]; then
  echo 1>&2 "$0: not enough arguments"
  exit 2
elif [ $# -gt 1 ]; then
  echo 1>&2 "$0: too many arguments"
  exit 3
fi
tar zcvf $1.tar.gz log priv_cache priv_data data/md2/data/user data/md2/data/swf data/md2/data/css data/md2/data/js/tinymce log apps/md2/config/config.php apps/md2/config/database.php .htaccess
