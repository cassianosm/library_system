1. Run create.sql and procedures.sql to setup db;
2. Run all scripts in data folder to load dummy data (must follow numbers);
3. Create a user called library with pass library to work;
4. If using other user, changes are made in code/class/DBConnection.class.php
    private $user = 'library';
    private $pwd = 'library';