Database Search Tool
====================
In an effort to make searching entire databases for specific strings, numbers,
and email addresses easier, I have made this script that will pull the list of
tables and their respective columns from the database and then search each and
every column for the search term, ie,

    SELECT column_name
    FROM table_name
    WHERE column_name LIKE '%search_term%'

It will then output the results grouped by the table names.

Just copy the connection information to the `$db_client` array and select them
from the drop-down menu.
