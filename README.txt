WTL - Study Material Manager

EXTRA FEATURE:
Search/filter box above the material table. It filters the database-loaded rows on the page without changing the required CRUD functionality.

XAMPP SETUP:
1. Start Apache and MySQL in XAMPP.
2. Copy the entire material_manager folder into:
   C:\xampp\htdocs\
3. Open phpMyAdmin:
   http://localhost/phpmyadmin
4. Import database.sql.
5. Make sure the folder:
   C:\xampp\htdocs\material_manager\uploads
   exists and is writable.
6. Open:
   http://localhost/material_manager/index.php

FILES:
- task1.html     -> Task 1 HTML-only submission
- task2.html     -> Task 2 JavaScript/in-memory version
- index.php      -> Task 3 main page and CRUD handling
- db.php         -> MySQL connection
- functions.php  -> server-side validation and secure PDF upload
- script.js      -> edit/delete modal behavior + extra search feature
- style.css      -> simple styling
- database.sql   -> database/table creation
- uploads/       -> uploaded PDFs

SECURITY:
- Every SQL query uses a prepared statement except the fixed SELECT used only to load the table.
- User input is never concatenated into SQL.
- Server-side validation repeats the Task 2 checks.
- PDF extension and MIME type are checked.
- Uploaded files get a unique timestamp/randomized filename.

VIVA:
Create = INSERT
Read = SELECT
Update = UPDATE
Delete = DELETE
The file is stored in uploads/ and only its path is stored in MySQL.
