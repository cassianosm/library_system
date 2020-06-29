
-- Login Procedure

USE library;

DELIMITER //

create or replace PROCEDURE sp_user_login (
    IN email_or_login VARCHAR(255),
    IN input_pass VARCHAR(255)
)
BEGIN

    select 
        u.id, 
        u.users_name, 
        u.user_login, 
        u.is_admin, 
        u.email, 
        GROUP_CONCAT(r.item_id) as reserved_items 
    from users u 
        left join reserved r on r.id = u.id 
    where (email = email_or_login and pass = input_pass) OR ( user_login = email_or_login and pass = input_pass)
    group by u.id, u.users_name, u.user_login, u.is_admin, u.email;

END;
//

DELIMITER ;

-- Retrieve User Info
USE library;

DELIMITER //

create or replace PROCEDURE sp_retrieve_user_info (
    IN x int
)
BEGIN

    select user_login, users_name, email from users where id = x;

END;
//

DELIMITER ;




-- update User Info
USE library;

DELIMITER //

create or replace PROCEDURE sp_update_user_info (
    in users_name varchar(255),
    in user_login CHAR(20),
    in is_admin TINYINT(1),
    in email VARCHAR(255),
    IN x int
)

BEGIN

    update users SET
        
        users_name = users_name, 
        user_login = user_login, 
        is_admin = is_admin, 
        email = email

    where id = x;
    select id, users_name, user_login, is_admin, email from users where id = x;

END;
//

DELIMITER ;

-- update User Info
USE library;

DELIMITER //

create or replace PROCEDURE sp_update_user_pass (

    in pass VARCHAR(255),
    IN x int
)

BEGIN

    update users SET
 
        pass = pass

    where id = x;

END;
//

DELIMITER ;


-- insert user
USE library;

DELIMITER //

create or replace PROCEDURE sp_insert_user (
    in users_name varchar(255),
    in user_login CHAR(20), 
    in pass VARCHAR(255),
    in is_admin TINYINT(1),
    in email VARCHAR(255)
)


BEGIN

        insert into users (users_name, user_login, pass, is_admin, email) values (users_name, user_login, pass, is_admin, email);
        select 
            u.id, 
            u.users_name, 
            u.user_login, 
            u.is_admin, 
            u.email, 
            GROUP_CONCAT(r.item_id) as reserved_items 
        from users u 
            left join reserved r on r.id = u.id 
        where u.id = last_insert_id()
        group by u.id, u.users_name, u.user_login, u.is_admin, u.email;

END;
//

DELIMITER ;



-- retrieve user's borrowed and reserved items;
USE library;

DELIMITER //

create or replace PROCEDURE sp_retrieve_user_borrowed_reserved (
    in x int
)

BEGIN
    select b.*, v.title from borrowed b inner join v_retrieve_items v on v.item_id = b.item_id where b.id = x;
    select r.*, v.title from reserved r inner join v_retrieve_items v on v.item_id = r.item_id where r.id = x;
END;
//

DELIMITER ;



-- Reserve items

USE library;

DELIMITER //

create or replace PROCEDURE sp_reserve_item (
    IN n_item_id int,
    IN n_id int
)
BEGIN

    insert into reserved (item_id, id, reserved_date) values (n_item_id, n_id, now());
    select GROUP_CONCAT(r.item_id) as reserved_items from reserved r where r.id = n_id group by r.id;

END;
//

DELIMITER ;



-- Reserve items

USE library;

DELIMITER //

create or replace PROCEDURE sp_cancel_reserve (
    IN n_item_id int,
    IN n_id int
)
BEGIN

    delete from reserved where id = n_id and item_id = n_item_id; 
    select GROUP_CONCAT(r.item_id) as reserved_items from reserved r where r.id = n_id group by r.id;

END;
//

DELIMITER ;


-- View to retrieve total items borrowed per item
use library;

create or replace view v_items_borrowed_count AS
select item_id, count(item_id) as total from borrowed
GROUP by item_id;



-- view that returns all items' infos.
use library;

create or replace view v_retrieve_items AS

select i.item_id,
    i.title, i.isbn,
    i.image_url,
    i.year_published,
    i.item_description,
    i.display_front_page,
    i.category_id,
    t.types_name,
	p.publisher_name, 
    i.quantity,
    (i.quantity - COALESCE(v.total, 0)) as available,
    GROUP_CONCAT(a.author_name) as item_authors 
from items i
    left join items_authors ia on ia.item_id = i.item_id
    LEFT join authors a on a.author_id = ia.author_id
    left join publishers p on p.publisher_id = i.publisher_id
    left join types t on t.types_id = i.types_id
    left join v_items_borrowed_count v on v.item_id = i.item_id
GROUP by
    i.item_id, 
    i.title, 
    i.isbn, 
    i.image_url, 
    i.year_published, 
    i.item_description, 
    i.display_front_page, 
    i.category_id,
    t.types_name,
    p.publisher_name, 
    i.quantity, 
    v.total
order by i.item_id;


-- retrieve First Page Item List
USE library;

DELIMITER //

create or replace PROCEDURE sp_retrieveFirstPageItems (
    IN page_start int,
    IN page_end int
)

BEGIN

    select * from v_retrieve_items
    where display_front_page = 1
    LIMIT page_start, page_end;

END;
//

DELIMITER ;



-- retrieve Items by category
USE library;

DELIMITER //

create or replace PROCEDURE sp_retrieveItemsByCategory (
    IN page_start int,
    IN page_end int,
    IN cat int
)

BEGIN

    select * from v_retrieve_items
    where category_id IN
    (
        select cat as category_id
        UNION
        select category_id from categories where parent_id = cat
    )
    LIMIT page_start, page_end;

END;
//

DELIMITER ;


-- retrieve total Items by category
USE library;

DELIMITER //

create or replace PROCEDURE sp_retrieveTotalInCategory (
    IN cat int
)

BEGIN

    select count(*) as total from v_retrieve_items
    where category_id IN
    (
        select cat as category_id
        UNION
        select category_id from categories where parent_id = cat
    );

END;
//

DELIMITER ;


-- retrieve Item Info by id
USE library;

DELIMITER //

create or replace PROCEDURE sp_retrieveItem (
    IN id int
)

BEGIN

    select * from v_retrieve_items
    where item_id = id;

END;
//

DELIMITER ;





-- retrieve categories
USE library;

DELIMITER //

create or replace PROCEDURE sp_retrieveCategories (
)

BEGIN

    SELECT c1.category_id, c1.category_name, GROUP_CONCAT(concat(c2.category_id,",",c2.category_name) SEPARATOR ';') as subcategories from categories c1
    left join categories c2 on c2.parent_id = c1.category_id
    where c1.parent_id is null
    group by c1.category_id, c1.category_name;

END;
//

DELIMITER ;



-- search Items Full Text
USE library;

DELIMITER //

create or replace PROCEDURE sp_searchItems (
    IN search_term varchar(255)
)

BEGIN

    SELECT v.* FROM items i 
    inner join v_retrieve_items v on v.item_id = i.item_id
    WHERE MATCH(i.title, i.isbn) AGAINST(search_term);

END;
//

DELIMITER ;
