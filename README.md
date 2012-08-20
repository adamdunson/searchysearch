Database Search Tool
====================
In an effort to make searching entire databases for specific strings, numbers,
and email addresses easier, I have made this script that will pull the list of
tables and their respective columns from the database and then search each and
every column for the search term, e.g.,

    SELECT column_name
    FROM table_name
    WHERE column_name LIKE '%search_term%'

This tool can take a very long time to run. It is literally searching every
column in every table with a wildcard search term.

Usage
-----
Put this directory somewhere accessible via a web browser.

Create a database.yml in the this directory with your desired searchable
databases. See database.yml.example for an example of what it should look like.

Navigate to the script, e.g., http://localhost/db_search/mysql_search.php,
select your client, enter a search term, and hit Search.

Results will be grouped by table name, and then by column name. Each matching
row will be numbered and your search term will be highlighted.

At the very bottom of the page, it will give you the total number of matched rows.
