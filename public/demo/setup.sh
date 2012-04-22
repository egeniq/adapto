if [ "$1X" = "X" ]
then
  echo
  echo Usage: ./setup.sh wwwuser
  echo
  exit
fi
chown -R $1 atktmp
