# Prerequisites

- PHP (v5.6 or above), see http://www.php.net/
  - with SOAP extension, see https://secure.php.net/manual/en/book.soap.php
- MariaDB (v10.0 or above), see https://mariadb.org/

# Get the code

````
cd /path/to/tabt
git clone https://github.com/gfrenoy/TabT-API.git
````

# Setup database

Managing a database is outside the scope of this install guide, the below instructions assume you have created a database called ````tabtdb```` with full access to user ````tabtuser````

````
cd /path/to/tabt/TabT-API/tabt/db
mysql -utabtuser -p tabtdb < tabt-db.sql
mysql -utabtuser -p tabtdb < tabt-db-demo.sql
````

For your information:

- tabt-db.sql contains the schema of the TabT database
- tabt-db-demo.sql contains a fictional demo database

# Configure

Copy the default configuration file (config.inc-default) to "config.inc":

````
cp config.inc-default config.inc
````

and modify the required parameters to connect to the TabT database.

# Start web server

````
cd /path/to/tabt/TabT-API
php -S 127.0.0.1:9200
````

# Test

To test if your installation is successful, you may want to try:

````
php -r 'var_dump((new SoapClient("http://127.0.0.1:9200/tabtapi_main.php?wsdl"))->Test());'
````

In any SOAP client (example SoapUI, http://www.soapui.org/), the service description to use is:

````
http://127.0.0.1:9200/tabtapi_main.php?WSDL
````
