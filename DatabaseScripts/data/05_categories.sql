use library;

insert into categories (category_name) values ('Book');
insert into categories (category_name) values ('Magazine');
insert into categories (category_name) values ('Parchment');
insert into categories (category_name) values ('Media');
insert into categories (category_name) values ('Maps');

-- random subcategories - 1 sublevel only

insert into categories (category_name, parent_id) values ('Garden', 5);
insert into categories (category_name, parent_id) values ('Garden', 4);
insert into categories (category_name, parent_id) values ('Baby', 5);
insert into categories (category_name, parent_id) values ('Jewelery', 3);
insert into categories (category_name, parent_id) values ('Clothing', 2);
insert into categories (category_name, parent_id) values ('Movies', 2);
insert into categories (category_name, parent_id) values ('Movies', 5);
insert into categories (category_name, parent_id) values ('Health', 1);
insert into categories (category_name, parent_id) values ('Clothing', 5);
insert into categories (category_name, parent_id) values ('Automotive', 1);
insert into categories (category_name, parent_id) values ('Books', 3);
insert into categories (category_name, parent_id) values ('Home', 3);
insert into categories (category_name, parent_id) values ('Shoes', 2);
insert into categories (category_name, parent_id) values ('Electronics', 5);
insert into categories (category_name, parent_id) values ('Sports', 2);
insert into categories (category_name, parent_id) values ('Jewelery', 4);
insert into categories (category_name, parent_id) values ('Grocery', 1);
insert into categories (category_name, parent_id) values ('Jewelery', 3);
insert into categories (category_name, parent_id) values ('Health', 1);
insert into categories (category_name, parent_id) values ('Clothing', 3);