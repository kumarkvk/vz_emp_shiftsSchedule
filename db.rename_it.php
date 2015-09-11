<?php
/* rename this file to db.php and enter your MySQL database login details */
define( 'NTS_DB_HOST',	'127.0.0.1');
define( 'NTS_DB_USER',	'my_username');
define( 'NTS_DB_PASS',	'my_password');
define( 'NTS_DB_NAME',	'shiftexec');

/* usually not required to change */
define( 'NTS_DB_TABLES_PREFIX',	'shf2_');

/* you might need to set it to a bigger value if you have heavy schedules */
ini_set( 'memory_limit', '32M' );

/* uncomment below if you need SMTP to send mail */
// define( 'NTS_SMTP_HOST', 'smtp.yoursite.com' );
// define( 'NTS_SMTP_USER', 'myusername' );
// define( 'NTS_SMTP_PASS', 'mypassword' );
// define( 'NTS_SMTP_SECURE', TRUE );
?>