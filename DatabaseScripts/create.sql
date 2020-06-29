CREATE DATABASE library; 

USE library;

CREATE TABLE users 
  ( 
     id         INT NOT NULL auto_increment PRIMARY KEY, 
     users_name VARCHAR(255) NOT NULL, 
     user_login CHAR(20) UNIQUE NOT NULL, 
     pass       VARCHAR(255) NOT NULL, 
     is_admin   TINYINT(1) DEFAULT 0, 
     email      VARCHAR(255) UNIQUE NOT NULL 
  ); 

CREATE TABLE publishers 
  ( 
     publisher_id   INT NOT NULL auto_increment PRIMARY KEY, 
     publisher_name VARCHAR(255) 
  ); 

CREATE TABLE authors 
  ( 
     author_id   INT NOT NULL auto_increment PRIMARY KEY, 
     author_name VARCHAR(255) 
  ); 

CREATE TABLE types 
  ( 
     types_id   INT NOT NULL auto_increment PRIMARY KEY, 
     types_name VARCHAR(255) 
  ); 

CREATE TABLE categories 
  ( 
     category_id   INT NOT NULL auto_increment PRIMARY KEY, 
     parent_id     INT NULL, 
     category_name TEXT, 
     FOREIGN KEY (parent_id) REFERENCES categories(category_id) ON DELETE no 
     action 
  ); 

CREATE TABLE items 
  ( 
    item_id            INT NOT NULL auto_increment PRIMARY KEY, 
    types_id           INT, 
    category_id        INT, 
    publisher_id       INT, 
    title              VARCHAR(255) NOT NULL, 
    isbn               VARCHAR(255) UNIQUE, 
    quantity           INT DEFAULT 0, 
    image_url          TEXT, 
    year_published     CHAR(4), 
    item_description   TEXT, 
    display_front_page TINYINT(1) DEFAULT 0, 
    FOREIGN KEY (types_id) REFERENCES types(types_id), 
    FOREIGN KEY (category_id) REFERENCES categories(category_id), 
    FOREIGN KEY (publisher_id) REFERENCES publishers(publisher_id),
    FULLTEXT index_fulltext_items (title, isbn)
  );

CREATE TABLE items_authors 
  ( 
     item_id   INT NOT NULL, 
     author_id INT NOT NULL, 
     PRIMARY KEY (item_id, author_id), 
     FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE no action, 
     FOREIGN KEY (author_id) REFERENCES authors(author_id) ON DELETE no action 
  ); 

CREATE TABLE borrowed 
  ( 
     borrowed_id   INT NOT NULL auto_increment PRIMARY KEY, 
     item_id       INT NOT NULL, 
     id            INT NOT NULL, 
     issued_date   DATETIME NOT NULL, 
     due_date      DATETIME NOT NULL, 
     returned_date DATETIME, 
     FOREIGN KEY (item_id) REFERENCES items(item_id), 
     FOREIGN KEY (id) REFERENCES users(id) 
  ); 

CREATE TABLE reserved 
  ( 
     reserved_id   INT NOT NULL auto_increment PRIMARY KEY, 
     item_id       INT NOT NULL, 
     id            INT NOT NULL, 
     reserved_date DATETIME NOT NULL, 
     FOREIGN KEY (item_id) REFERENCES items(item_id), 
     FOREIGN KEY (id) REFERENCES users(id) 
  ); 