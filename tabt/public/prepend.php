<?php
// Using MySQL database
require($_PHPLIB["libdir"] . "db_mysql.inc");
// Data storage container
require($_PHPLIB["libdir"] . "ct_sql.inc");
// Session management (required for everything below)
require($_PHPLIB["libdir"] . "session.inc");
// Authentication
require($_PHPLIB["libdir"] . "auth.inc");
// Permission checks
require($_PHPLIB["libdir"] . "perm.inc");
// Per-user variables
require($_PHPLIB["libdir"] . "user.inc");

// Local configuration
require($_PHPLIB["libdir"] . "local.inc");

?>
