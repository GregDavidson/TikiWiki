#!/bin/bash
# https://stackoverflow.com/questions/3856435/how-to-convert-all-tables-from-myisam-into-innodb
db='tiki'
user='root'
pw=
script_file="convert-$db-to-innodb.sql"
echo "SELECT concat(
	'ALTER TABLE \`',
	TABLE_SCHEMA,
	'\`.\`',
	TABLE_NAME,
	'\` ENGINE=InnoDB;'
) FROM information_schema.TABLES 
WHERE ENGINE != 'InnoDB' AND TABLE_TYPE='BASE TABLE' 
AND TABLE_SCHEMA='$db'" |
  mysql -u "$user" -p "$pw" |
  grep '^ALTER ' > "$script_file"

# mysql -u "$user" -p < "$script_file"
