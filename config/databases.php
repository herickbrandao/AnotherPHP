<?php use \system\Database;

Database::set(name: "my_db", data: [
	'MYSQL',            # DB TYPE  / ex: MYSQL or PGSQL
	'localhost',        # ADDRESS  / ex: localhost
	'database_name',    # DB NAME  / ex: database_name
	'root',             # DB USER  / ex: root
	'',                 # DB PASS  / ex: password
	'',                 # DB PORT  / ex: 5432
]);
