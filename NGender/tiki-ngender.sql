-- SQL Code for NGender Tiki Wiki Contributions
-- License: Same as regular Tiki Wiki (tiki.org) License
-- Author: J. Greg Davidson, 2017

-- Administrative steps to utilize
--   category feature feature_ngender_stewards
-- (0) ensure your tiki php code supports feature_ngender_stewards
-- (1) ensure group Stewards exists
-- (2) ensure category User::Test::Steward exists with its ancestry
-- (3) set up maximal permissions for that Group and that Category
--		 -- give members of group Stewards broad permissions
--		 -- on objects associated with group Stewards
-- Note: These permissions are a MODEL which will be copied
--		 into each Steward's default group and default category
-- (4) ensure feature_ngender_stewards is activated
-- 		 -- it's in the Category Features admin panel
--		 -- or use SQL procedure from this file
-- (5) ensure this code is in the tiki database
-- 		 -- check for any unit test errors failing during load
-- 		 -- feel free to add some of the missing unit tests!
-- (6) either
--			-- manually make Steward Users to group Stewards or
--			-- (1) CALL add_everyone_to_group_stewards()
--			-- (2) manually remove unwanted users
-- (7) CALL make_stewards_be_stewards() to finish the setup

-- * Names <-> IDs

-- Naming conventions:
-- - Procedures and functions whose names end in underscore (_)
-- 	 should not be called directly.
-- - Functions whose names begin with try_ return NULL on failure.
-- - Reference variables are ids unless explicitly stated otherwise
--   -- e.g. group_ is an id, groupname_ is a string
-- - Parameters and locals often have a trailing slash
--   -- distinguishing them from fields, globals, keywords, etc.
-- - Double ? or ! indicates something problematic to revisit
-- - Triple ? or ! indicates an issue which must be resolved

-- Should DEFINER be changed to, e.g. tiki?  Or omitted??

-- ** Unit test procedures - do not call these routines in implemention code!

SET @TESTS_PASSED = 0;
SET @TESTS_FAILED = 0;

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `chuck_int_`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `chuck_int_`(value_ INT)
	COMMENT 'throw away the argument (avoids creating result sets)'
BEGIN
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `test_passed`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `test_passed`(expression_ TEXT, message_ TEXT)
	COMMENT 'Record passed test, args ignored and api allows for future logging option; see test_failed'
BEGIN
	SET @TESTS_PASSED = @TESTS_PASSED + 1;
END//
DELIMITER ;
-- #+END_SRC

-- Our error routines will be raising exceptions
-- MYSQL SIGNAL numbers starting with 02 mean either
--   an exception or not found condition - differentiated by MYSQL_ERRNO
--   the rest of the number must remain unique within our software
--   We use 02234 to signal a testing framework error!!
-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `test_failed`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `test_failed`(expression_ TEXT, message_ TEXT)
	COMMENT 'Record failed test, signal error with appropriate message'
BEGIN
	DECLARE msg_ TEXT DEFAULT CONCAT('Assert ', expression_, ' failed');
	SET @TESTS_FAILED = @TESTS_FAILED + 1;
	IF message_ != '' THEN
		 SET msg_ = CONCAT(msg_, ': ', message_);
	END IF;
	SET msg_ = CONCAT(msg_, '!');
	SIGNAL SQLSTATE '02234'	SET MESSAGE_TEXT = msg_, MYSQL_ERRNO = ER_SIGNAL_EXCEPTION;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `assert_true_`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `assert_true_`(value_ INT, expression_ TEXT, message_ TEXT)
	COMMENT 'Signal error if value_ is zero'
BEGIN
	IF value_ = 0 THEN
		CALL test_failed(expression_, message_);
	ELSE
		CALL test_passed(expression_, message_);
	END IF;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `assert_true_msg`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `assert_true_msg`(expression_ TEXT, message_ TEXT)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'Unit Test: Complain if expression fails or evalutes to zero'
BEGIN
  DECLARE sql_ TEXT DEFAULT CONCAT(
		'CALL assert_true_(COALESCE(', expression_, ', 0), ',
			QUOTE(expression_), ', ',	QUOTE(message_),
		')'
	);
	SET @sql_ = sql_;
	PREPARE stmt_ FROM @sql_;
  EXECUTE stmt_;
  DEALLOCATE PREPARE stmt_;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `assert_true`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `assert_true`(expression_ TEXT)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'Unit Test: Complain if expression fails or evalutes to zero'
BEGIN
	CALL assert_true_msg(expression_, '');
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `assert_fail_msg`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `assert_fail_msg`(expression_ TEXT, message_ TEXT)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'Unit Test: Complain unless expression throws exception; or evaluates to a truthy value (non zero)'
BEGIN
	DECLARE msg_ TEXT;
  DECLARE sql_ TEXT DEFAULT CONCAT(
	  'SELECT COALESCE(', expression_, ', 13) INTO @testval'
	);
	DECLARE assert_failure CONDITION FOR SQLSTATE '02234';
  DECLARE EXIT HANDLER FOR assert_failure
	BEGIN
		SET @TESTS_PASSED = @TESTS_PASSED + 1;
	  DEALLOCATE PREPARE stmt_;
	END;
	SET @sql_ = sql_;
	PREPARE stmt_ FROM @sql_;
  EXECUTE stmt_;
  DEALLOCATE PREPARE stmt_;
	IF @testval !=0 THEN 
		SET @TESTS_FAILED = @TESTS_FAILED + 1;
		SET msg_ = CONCAT('Assert ', expression_, ' returned ', @testval);
		IF message_ != '' THEN
				SET msg_ = CONCAT(msg_, ': ', message_);
		END IF;
		SET msg_ = CONCAT(msg_, '!');
		SIGNAL SQLSTATE '45001'	SET MESSAGE_TEXT = msg_;
  END IF;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `assert_fail`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `assert_fail`(expression_ TEXT)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'Unit Test: Complain if expression evaluates to non-zero value'
BEGIN
	CALL assert_fail_msg(expression_, '');
END//
DELIMITER ;
-- #+END_SRC

-- ** Failure for User Functions

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `signal_no_text`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `signal_no_text`(msg_ TEXT)
	RETURNS TEXT
	COMMENT 'Raise exception with message where a string is required'
BEGIN
		SIGNAL SQLSTATE '02234' SET MESSAGE_TEXT = msg_, MYSQL_ERRNO = ER_SIGNAL_EXCEPTION;
		RETURN '';										-- will never happen!
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `signal_no_int`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `signal_no_int`(msg_ TEXT)
	RETURNS INT
	COMMENT 'Raise exception with message where an integer is required'
BEGIN
		SIGNAL SQLSTATE '02234' SET MESSAGE_TEXT = msg_, MYSQL_ERRNO = ER_SIGNAL_EXCEPTION;
		RETURN 0;										-- will never happen!
END//
DELIMITER ;
-- #+END_SRC

-- ** String Manipulation

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `split_str_delim_head_rest`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `split_str_delim_head_rest`(str_ TEXT, delim_ TEXT, OUT head_ TEXT, OUT rest_ TEXT)
	DETERMINISTIC
	COMMENT 'split string at first instance of delim, return the head before and the rest after'
BEGIN
		DECLARE pos_ INT DEFAULT instr(str_, delim_);
		IF pos_ = 0 THEN
			SET head_ = str_;
			SET rest_ = '';
		ELSE
			SET head_ = SUBSTR(str_, 1, pos_ - 1);
			SET rest_ = SUBSTR(str_, pos_ + length(delim_));
		END IF;
END//
DELIMITER ;
-- #+END_SRC

SET @x = -1; SET @y = -1;
CALL split_str_delim_head_rest('aa::bbb::cccc', '::', @x, @y);
CALL assert_true('@x = \'aa\'');
CALL assert_true('@y = \'bbb::cccc\'');
CALL split_str_delim_head_rest('aa::bbb', '::', @x, @y);
CALL assert_true('@x = \'aa\'');
CALL assert_true('@y = \'bbb\'');
CALL split_str_delim_head_rest('aa', '::', @x, @y);
CALL assert_true('@x = \'aa\'');
CALL assert_true('@y = \'\'');
CALL split_str_delim_head_rest('', '::', @x, @y);
CALL assert_true('@x = \'\'');
CALL assert_true('@y = \'\'');

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `split_str_delim_tail_rest`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `split_str_delim_tail_rest`(str_ TEXT, delim_ TEXT, OUT tail_ TEXT, OUT rest_ TEXT)
	DETERMINISTIC
	COMMENT 'split string at last instance of delim, return the tail after and the rest before'
BEGIN
		-- All of this reversing seems too cute!
		-- Can you think of a better way in vanilla MySQL?
		-- And is reverse Unicode-safe??
		DECLARE pos_ INT DEFAULT instr(reverse(str_), reverse(delim_));
		IF pos_ = 0 THEN
			SET tail_ = str_;
			SET rest_ = '';
		ELSE
			SET tail_ = SUBSTR(str_, length(str_) - pos_ + length(delim_) );
			SET rest_ = SUBSTR(str_, 1, length(str_) - pos_ - length(delim_) + 1);
		END IF;
END//
DELIMITER ;
-- #+END_SRC

SET @x = -1; SET @y = -1;
CALL split_str_delim_tail_rest('aa::bbb::cccc', '::', @x, @y);
CALL assert_true('@x = \'cccc\'');
CALL assert_true('@y = \'aa::bbb\'');
CALL split_str_delim_tail_rest('aa::bbb', '::', @x, @y);
CALL assert_true('@x = \'bbb\'');
CALL assert_true('@y = \'aa\'');
CALL split_str_delim_tail_rest('aa', '::', @x, @y);
CALL assert_true('@x = \'aa\'');
CALL assert_true('@y = \'\'');
CALL split_str_delim_tail_rest('', '::', @x, @y);
CALL assert_true('@x = \'\'');
CALL assert_true('@y = \'\'');

-- ** User Names <-> User IDs

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `user_named`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `user_named`(username_ TEXT)
RETURNS INT READS SQL DATA DETERMINISTIC
COMMENT 'return id of user of given name or raise exception if none'
BEGIN
	DECLARE found_ INT DEFAULT 0;
	SELECT userId INTO found_ FROM users_users WHERE login = username_;
	RETURN COALESCE(
		NULLIF( found_, 0 ),
		signal_no_int( CONCAT('Username ', COALESCE(username_, 'NULL'), ' not found!') )
	);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `user_name`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `user_name`(user_ INT)
	RETURNS TEXT
	READS SQL DATA DETERMINISTIC
	COMMENT 'return name of user of given id or raise exception if none'
BEGIN
	DECLARE found_ TEXT DEFAULT '';
	SELECT login INTO found_ FROM users_users WHERE userId = user_;
	RETURN COALESCE(
		NULLIF( found_, ''),
		signal_no_text( CONCAT('User ', COALESCE(user_, -1), ' not found!') )
	);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
-- If id 2 fails, pick a user id that exists on your system!
CALL assert_true('user_name(2) != \'\'');
CALL assert_true('user_named(user_name(2)) = 2');
CALL assert_fail('user_name(0)');
CALL assert_fail('user_named(\'Huh?\')');
-- #+END_SRC

-- ** Group Names <-> Group IDs

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `group_id`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `group_id`(group_ INT)
RETURNS int READS SQL DATA DETERMINISTIC
COMMENT 'return argument unchanged or raise exception if no such group'
BEGIN
	DECLARE found_ int DEFAULT 0;
	SELECT id INTO found_ FROM users_groups WHERE id = group_;
	RETURN COALESCE(
		NULLIF( found_, 0 ),
		signal_no_int( CONCAT('Group ', COALESCE(group_, -1), ' not found!') )
	);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `try_group_named`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `try_group_named`(groupname_ TEXT)
RETURNS INT READS SQL DATA DETERMINISTIC
COMMENT 'return id of group of given name or NULL if none'
BEGIN
	DECLARE found_ INT DEFAULT 0;
	SELECT id INTO found_ FROM users_groups WHERE groupName = groupname_;
	RETURN NULLIF( found_, 0 );
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `group_named`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `group_named`(groupname_ TEXT)
RETURNS INT READS SQL DATA DETERMINISTIC
COMMENT 'return id of group of given name or raise exception if none'
BEGIN
	RETURN COALESCE(
		try_group_named( groupname_ ),
		signal_no_int( CONCAT('Groupname ', COALESCE(groupname_, 'NULL'), ' not found!') )
	);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `group_name`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `group_name`(group_ INT)
RETURNS TEXT READS SQL DATA DETERMINISTIC
COMMENT 'return name of group of given id or raise exception if none'
BEGIN
	DECLARE found_ TEXT DEFAULT '';
	SELECT groupName INTO found_ FROM users_groups WHERE id = group_;
	RETURN COALESCE(
		NULLIF( found_, '' ),
		signal_no_text( CONCAT('Group ', COALESCE(group_, -1), ' not found!') )
	);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
CALL assert_true('group_named(\'Stewards\') != 0');
CALL assert_true('group_name(group_named(\'Stewards\')) = \'Stewards\'');
CALL assert_fail('group_name(0) <> \'\'');
CALL assert_fail('group_named(\'\')');
CALL assert_fail('group_named(\'Huh?\')');
-- #+END_SRC

-- ** Object Ids <-> Object IDs -- test for existence

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `object_id`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `object_id`(object_ INT)
RETURNS int READS SQL DATA DETERMINISTIC
COMMENT 'return argument unchanged or raise exception if no such object'
BEGIN
	DECLARE found_ int DEFAULT 0;
	SELECT objectId INTO found_ FROM tiki_objects WHERE objectId = object_;
	RETURN COALESCE(
		NULLIF( found_, 0 ),
		signal_no_int( CONCAT('Object ', COALESCE(object_, -1), ' not found!') )
	);
END//
DELIMITER ;
-- #+END_SRC

-- ** Category IDs/Names/Paths <-> Category IDs

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `category_id`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `category_id`(category_ INT)
RETURNS int READS SQL DATA DETERMINISTIC
COMMENT 'return argument unchanged or raise exception if no such category'
BEGIN
	DECLARE found_ int DEFAULT 0;
	SELECT categId INTO found_ FROM tiki_categories WHERE categId = category_;
	RETURN COALESCE(
		NULLIF( found_, 0 ),
		signal_no_int( CONCAT('Category ', COALESCE(category_, -1), ' not found!') )
	);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `try_category_named_parent`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `try_category_named_parent`(category_name TEXT, parent INT)
RETURNS INT READS SQL DATA DETERMINISTIC
COMMENT 'return id of category of given name and parent id or NULL if none'
BEGIN
	DECLARE found_ INT DEFAULT 0;
	SELECT categId INTO found_ FROM tiki_categories
	  WHERE name = category_name AND parentId = parent;
	RETURN NULLIF(found_, 0);
END//
DELIMITER ;
-- #+END_SRC

CALL assert_true('try_category_named_parent(\'User\', 0) IS NOT NULL');
CALL assert_true('try_category_named_parent(\'NoSuchCategory\', 0) IS NULL');

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `category_named_parent`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `category_named_parent`(category_name TEXT, parent INT)
RETURNS INT READS SQL DATA DETERMINISTIC
COMMENT 'return id of category of given name and parent id or raise exception if none'
BEGIN
	DECLARE msg_ TEXT DEFAULT 'category_named_parent: ';
	RETURN COALESCE(
		try_category_named_parent(category_name, parent),
		signal_no_int( CONCAT(
				msg_, COALESCE(category_name, 'NULL'),
				' parent ', COALESCE(parent, -1), ' not found!'
		) )
	);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
-- category names aren't necessarily unique
-- see FUNCTION category_path()
DROP FUNCTION IF EXISTS `category_name`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `category_name`(category_ INT)
RETURNS TEXT READS SQL DATA DETERMINISTIC
COMMENT 'return category name (not unique!) given id - or raise exception if none; see also category_path()'
BEGIN
	DECLARE found_ TEXT DEFAULT '';
	SELECT name INTO found_ FROM tiki_categories WHERE categId = category_;
	RETURN COALESCE(
		NULLIF( found_, '' ),
		signal_no_text( CONCAT('Category ', COALESCE(category_, -1), ' not found!') )
	);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
CALL assert_fail('category_named_parent(\'huh?\', 0)');
CALL assert_fail('category_named_parent(\'user\', 5)');
CALL assert_true('category_named_parent(\'user\', 0)');
CALL assert_true('category_named_parent(\'test\', category_named_parent(\'user\', 0) )');
CALL assert_true('category_named_parent(\'steward\', category_named_parent(\'test\', category_named_parent(\'user\', 0) ) )');
CALL assert_true('category_name( category_named_parent(\'user\', 0) ) = \'user\'');
CALL assert_fail('category_name( 0 )');
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `category_parent`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `category_parent`(category_ INT)
RETURNS INT READS SQL DATA DETERMINISTIC
COMMENT 'return parent id of category of given id - or raise exception if none; see also category_path()'
BEGIN
	DECLARE found_ INT DEFAULT -1;
	SELECT parentId INTO found_ FROM tiki_categories WHERE categId = category_;
	RETURN COALESCE(
		NULLIF( found_, -1 ),
		signal_no_int( CONCAT('Category ', COALESCE(category_, -1), ' not found!') )
	);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
CALL assert_true('category_parent( category_named_parent(\'User\', 0) ) = 0');
CALL assert_fail('category_parent( 0 )');
CALL assert_true('category_named_parent(\'user\', 0) =
category_parent( category_named_parent(\'test\', category_named_parent(\'user\', 0) ) )');
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `category_path`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `category_path`(category_id INT)
RETURNS TEXT READS SQL DATA DETERMINISTIC
COMMENT 'return :: delimited path of category of given id - or raise exception if no such category'
BEGIN
	DECLARE path_ TEXT DEFAULT category_name(category_id);
	DECLARE parent_ INT DEFAULT category_parent(category_id);
	WHILE parent_ > 0 DO
		SET path_ = CONCAT(category_name(parent_), '::', path_);
		SET parent_ = category_parent(parent_);
	END WHILE;
	RETURN path_;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
CALL assert_fail('category_path( 0 )');
CALL assert_true('category_path( category_named_parent(\'user\', 0) ) = \'user\'');
CALL assert_true('\'user::test\' =
category_path(category_named_parent(\'test\', category_named_parent(\'user\',0)))');
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `category_of_path`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `category_of_path`(path_ text)
RETURNS int READS SQL DATA DETERMINISTIC
COMMENT 'return category id given :: separated category path - or raise exception if no such category'
BEGIN
	DECLARE parent_ INT DEFAULT 0;
	DECLARE head_ TEXT;
	WHILE pos_ != 0 DO
		CALL split_str_delim_head_rest(path_, '::', head_, path_);
		SET parent_ = category_named_parent(head_, parent_);
	END WHILE;
	RETURN category_named_parent(path_, parent_);
END//
DELIMITER ;
-- #+END_SRC

SET @x = 'User';
SET @y = category_named_parent(@x, 0);
CALL assert_true('category_of_path(@x) = @y');

SET @x = 'User::Test';
SET @y = category_named_parent(@x, 0);
SET @z = category_named_parent('Test', @y);
CALL assert_true('category_of_path(@x) = @z');

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `try_create_categoryname_parent_comment`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `try_create_categoryname_parent_comment`(name__ TEXT, parent_ INT, comment_ TEXT)
	RETURNS INT
	READS SQL DATA MODIFIES SQL DATA
	COMMENT 'create category of name and parent and return its id; or NULL if it already exists'
BEGIN
	INSERT IGNORE INTO `tiki_categories`(`name`, `parentId`, `description`)
	VALUES (name_, parent_, comment_);
	RETURN NULLIF( LAST_INSERT_ID(), 0 );
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `ensure_categorypath_comment`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `ensure_categorypath_comment`(path_ TEXT, comment_ TEXT)
	RETURNS int
	READS SQL DATA MODIFIES SQL DATA
	COMMENT 'ensure category of given path exists and return its id; only last component of path will be created'
BEGIN
	DECLARE parent_ INT DEFAULT 0;
	DECLARE tail_ TEXT;
	DECLARE rest_ TEXT;
	DECLARE cat_ INT;
	CALL split_str_delim_tail_rest(path_, '::', tail_, rest_);
	IF rest_ != '' THEN
		SET parent_ = category_of_path(rest_);
	END IF;
	RETURN COALESCE(							-- avoid using up ids in collisions
		try_category_named_parent(tail_, parent_),
		try_create_categoryname_parent_comment(tail_, parent_, comment_) -- ,
		-- category_named_parent(tail_, parent_)
	);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `assert_categorypath_comment`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `assert_categorypath_comment`(path_ TEXT, comment_ TEXT)
	READS SQL DATA MODIFIES SQL DATA
	COMMENT 'assert success of ensure_categorypath_comment'
BEGIN
	DECLARE test_	TEXT DEFAULT concat('ensure_categorypath_comment(', path_, ',', comment_, ')');
	IF ensure_categorypath_comment(path_, comment_) THEN
		CALL test_passed( test_, '' );
	ELSE
		CALL test_failed( test_, '' );
	END IF;
END//
DELIMITER ;
-- #+END_SRC

SET @x = 'User';
SET @y = category_named_parent(@x, 0);
SET @z = 'root of user default categories';
SET @w = ensure_categorypath_comment(@x, @z);
CALL assert_true('@y = @w');

SET @x = category_of_path('User::Test');
SET @y = 'root of test account default categories';
CALL assert_true('@x =  ensure_categorypath_comment(\'User::Test\', @y)');

-- ** add test/add/drop category <-> object associations

-- Note:
-- - these need testing!!
-- - there are 6 catObjectid rows in tiki_categorized_objects
--   which do NOT correspond to any rows in tiki_category_objects
--   with that catObjectId field value!!

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `has_object_category`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `has_object_category`(obj_ int, cat_ int)
	RETURNS boolean
  READS SQL DATA
	COMMENT 'Does given object have given category?'
BEGIN
	DECLARE found_ INT DEFAULT 0;
	SELECT 1 INTO found_ FROM tiki_category_objects
	WHERE catObjectId = object_id(obj_) AND categId = category_id(cat_) LIMIT 1;
	RETURN found_ = 1;
End//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `add_object_category`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `add_object_category`(obj_ int, cat_ int)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'associate object of given objectId with category of given categId'
BEGIN
	INSERT IGNORE INTO tiki_category_objects(catObjectId, categId)
	VALUES ( object_id(obj_), category_id(cat_) );
	INSERT IGNORE INTO tiki_categorized_objects(catObjectId) VALUES (obj_);
End//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `drop_object_category`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `drop_object_category`(obj_ int, cat_ int)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'disassociate object of given objectId from category of given categId'
BEGIN
	DECLARE found_ INT DEFAULT 0;
	DELETE IGNORE FROM tiki_category_objects
	WHERE catObjectId = object_id(obj_) AND categId = category_id(cat_);
	SELECT 1 INTO found_ FROM tiki_category_objects WHERE catObjectId = obj_ LIMIT 1;
	IF found_ = 0 THEN
		DELETE IGNORE FROM tiki_categorized_objects WHERE catObjectId = obj_;
	END IF;
End//
DELIMITER ;
-- #+END_SRC

-- * IDs|Names --> Properties

-- ** Group IDs|Names --> Group Properties

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `group_default_category`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
	FUNCTION `group_default_category`(group_ INT)
	RETURNS INT	READS SQL DATA DETERMINISTIC
	COMMENT 'returns the default category of the given group or 0 if none'
BEGIN
	DECLARE found_ INT DEFAULT 0;
	SELECT groupDefCat INTO found_ FROM users_groups WHERE id = group_;
	RETURN found_;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql

CALL assert_fail('group_default_category( group_named(\'Registered\') )');

CALL assert_true('group_default_category( group_named(\'User_Greg\') ) =
 category_named_parent(\'Greg\', category_named_parent(\'User\', 0))');
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `groupname_default_category`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
	FUNCTION `groupname_default_category`(group_name TEXT)
	RETURNS INT	READS SQL DATA DETERMINISTIC
	COMMENT 'returns the default category of the given group or 0 if none'
BEGIN
	DECLARE found_ INT DEFAULT 0;
	SELECT groupDefCat INTO found_ FROM users_groups WHERE groupName = group_name;
	RETURN found_;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql

CALL assert_fail('groupname_default_category( \'Registered\' )');

CALL assert_true('groupname_default_category( \'User_Greg\' ) =
 category_named_parent(\'Greg\', category_named_parent(\'User\', 0))');
-- #+END_SRC

-- ** User IDs --> User Properties

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `user_default_groupname`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `user_default_groupname`(user_ INT)
RETURNS TEXT	READS SQL DATA DETERMINISTIC
	COMMENT 'returns the name of the default group of the given user or the empty string if none'
BEGIN
	DECLARE found_ TEXT DEFAULT '';
	SELECT default_group INTO found_ FROM users_users WHERE userId = user_;
	RETURN found_;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
-- Find a stable test user without a default group for next line!!!
-- SELECT user_default_groupname( user_named('sad_user') ) = '';

CALL assert_fail('user_default_groupname( 0 )');

CALL assert_true('user_default_groupname( user_named(\'Greg\') ) = \'User_Greg\'');
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `user_default_group`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `user_default_group`(user_id INT)
RETURNS INT READS SQL DATA DETERMINISTIC
	COMMENT 'returns the id of the default group of the given user or the empty string if none'
BEGIN
	RETURN try_group_named(user_default_groupname(user_id));
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
-- Find a stable test user without a default group for next line!!!
-- SELECT user_default_group( user_named('sad_user') ) = 0;
CALL assert_true('user_default_group( 0 ) IS NULL');
CALL assert_true('user_default_group( user_named(\'Greg\') ) = group_named(\'User_Greg\')');
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `user_default_category`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `user_default_category`(user_id INT)
RETURNS INT READS SQL DATA DETERMINISTIC
	COMMENT 'returns the id of the default category of the default group of the given user or 0 if none'
BEGIN
	DECLARE default_groupname TEXT DEFAULT user_default_groupname(user_id);
  IF char_length(default_groupname) = 0 THEN RETURN 0; END IF;
	RETURN groupname_default_category(default_groupname);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
-- !!! next line !!!
-- SELECT user_default_category( user_named('sad_user') ) = 0;
CALL assert_true('user_default_category( user_named(\'Greg\') ) =
 category_named_parent(\'Greg\', category_named_parent(\'User\', 0))');
-- #+END_SRC

-- * Relating Groups and Categories and the Permissions between them

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `perms_grp_cat`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `perms_grp_cat`(grp_ INT, cat_ INT)
  READS SQL DATA
	COMMENT 'show permissions between given group and category'
BEGIN
	DECLARE groupname_ TEXT DEFAULT group_name(grp_);
	DECLARE category_ TEXT DEFAULT MD5(CONCAT('category', cat_));
	SELECT permName FROM users_objectpermissions
	WHERE objectType = 'category' AND groupName = groupname_
	AND objectId = category_;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `set_perm_grp_cat`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `set_perm_grp_cat`(perm_ TEXT, grp_ INT, cat_ INT)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'sets permission for group with category'
BEGIN
	DECLARE groupname_ TEXT DEFAULT group_name(grp_);
	DECLARE category_ TEXT DEFAULT MD5(CONCAT('category', category_id(cat_)));
	INSERT IGNORE
	INTO users_objectpermissions(`groupName`,`permName`, `objectType`,`objectId`)
	VALUES (groupname_, perm_, 'category', category_);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `copy_perms_grp_cat_grp_cat`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `copy_perms_grp_cat_grp_cat`(grp_ INT, cat_ INT, to_grp INT, to_cat INT)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'makes the permissions of group to_grp with category to_cat the same as those of group grp_ with category cat_'
BEGIN
	DECLARE groupname_ TEXT DEFAULT group_name(grp_);
	DECLARE to_groupname TEXT DEFAULT group_name(to_grp);
	DECLARE from_category TEXT DEFAULT MD5(CONCAT('category', cat_));
	DECLARE to_category TEXT DEFAULT MD5(CONCAT('category', to_cat));
	DELETE FROM users_objectpermissions
	WHERE objectType = 'category' AND groupName = to_groupname
	AND objectId = to_category;
	INSERT INTO users_objectpermissions(`groupName`,`permName`, `objectType`,`objectId`)
	SELECT to_groupname,`permName`, `objectType`, to_category
	FROM users_objectpermissions
	WHERE objectType = 'category' AND groupName = groupname_
	AND objectId = from_category;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `create_steward_category`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `create_steward_category`(group_ INT, username_ TEXT)
  READS SQL DATA MODIFIES SQL DATA
BEGIN
	DECLARE user_ INT DEFAULT category_named_parent('User', 0);
	DECLARE test_ INT DEFAULT category_named_parent('Test',user_);
	DECLARE model_ INT DEFAULT category_named_parent('Steward',test_);
	DECLARE stewards_ int DEFAULT group_named('Stewards');
	DECLARE target_ INT DEFAULT group_default_category(group_);
	DECLARE parent_ INT;
	-- Warn if target_ exists with unexpected name or parent??
	IF target_ = 0 THEN
		IF convert(username_ using latin1) REGEXP convert('^Z[[:upper:]]' using latin1)
		COLLATE 'latin1_general_cs' THEN
			SET parent_ = test_;
		ELSE
			SET parent_ = user_;
		END IF;
		INSERT INTO `tiki_categories`(`name`,`parentId`,`rootId`,`hits`,`description`)
		VALUES (
					 username_, parent_, user_, 0,
					 concat('default category of steward ', username_)
		);
		SET target_ = LAST_INSERT_ID();
		UPDATE `users_groups` SET groupDefCat = target_ WHERE id = group_;
	END IF;
	-- Really reset permissions if category already existed??
	CALL copy_perms_grp_cat_grp_cat(stewards_,model_,group_,target_);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `user_add_groupname`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `user_add_groupname`(user_ INT, groupname_ TEXT)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'ensure given user is associated with given group'
BEGIN
	DECLARE msg_ TEXT DEFAULT 'user_add_groupname: ';
	DECLARE chuck_ INT;
  -- Assert: is_user(user_):
	IF length(COALESCE(user_name(user_), '')) = 0 THEN
		SET chuck_ = signal_no_int( CONCAT(msg_, 'User ', COALESCE(user_, -1), ' not found!') );
	END IF;
  -- Assert: is_groupname(groupname_):
	IF COALESCE(group_named(groupname_), 0) = 0 THEN
		SET chuck_ = signal_no_int( CONCAT(
				msg_, 'Group ', COALESCE(groupname_, 'NULL'), ' not found!'
		) );
	END IF;
	INSERT IGNORE INTO `users_usergroups`(userId, groupName, created)
	VALUES ( user_, groupname_, UNIX_TIMESTAMP() );
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `user_add_group`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `user_add_group`(user_ INT, group_ INT)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'ensure given user is associated with given group'
BEGIN
	CALL user_add_groupname( user_, group_name(group_) );
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `ensure_groupname_comment`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `ensure_groupname_comment`(group_name TEXT, comment_ TEXT)
	RETURNS INT
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'ensure group of given name exists and return its id'
BEGIN
		-- some fields are NOT defaulted!!
		-- following non-default values set by Web Interface
		-- why is meaning of prorateInterval = 'day' ??
		INSERT IGNORE INTO `users_groups`(
			`groupName`, `groupHome`,	`usersTrackerId`, `groupTrackerId`,
			`usersFieldId`,	`groupFieldId`,	`registrationUsersFieldIds`,
			`userChoice`, `prorateInterval`, `groupDesc`
		) VALUES (	group_name, '', 0, 0, 0, 0, '', '', 'day',	comment_	);
		RETURN group_named(group_name);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `assert_groupname_comment`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `assert_groupname_comment`(group_name TEXT, comment_ TEXT)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'assert success of ensure_groupname_comment'
BEGIN
	DECLARE test_ TEXT DEFAULT concat('ensure_groupname_comment(', group_name, ',', comment_, ')');
	IF ensure_groupname_comment(group_name, comment_) THEN
		CALL test_passed( test_, '' );
	ELSE
		CALL test_failed( test_, '' );
	END IF;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `create_steward_default_group`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `create_steward_default_group`(user_ INT)
  READS SQL DATA MODIFIES SQL DATA
BEGIN
	DECLARE username_ TEXT DEFAULT user_name(user_);
	DECLARE group_ INT DEFAULT user_default_group(user_);
	DECLARE groupname_ TEXT;
	-- Warn if default group exists with unexpected name??
	IF COALESCE(group_, 0) = 0 THEN
		SET groupname_ = CONCAT('User_', username_);
		SET group_ = ensure_groupname_comment(groupname_, concat('default group of steward ', username_));
		CALL user_add_groupname(user_, groupname_);
		UPDATE `users_users` SET default_group = groupname_
		WHERE userId = user_;
	END IF;
	CALL create_steward_category(group_, username_);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `make_steward_user`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `make_steward_user`(user_ INT)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'Ensure user initiated into categorical stewards'
BEGIN
	CALL create_steward_default_group(user_);
	CALL user_add_groupname(user_, 'Stewards');
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `make_steward_username`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `make_steward_username`(user_name TEXT)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'Ensure user initiated into categorical stewards'
BEGIN
	CALL make_steward_user( user_named(user_name) );
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `make_stewards_be_stewards`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `make_stewards_be_stewards`()
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'Ensure users in group Stewards initiated into Categorical Stewards'
BEGIN
 DECLARE stewards_ int DEFAULT group_named('Stewards');
 DECLARE found_ int DEFAULT 0;
 DECLARE done_ int DEFAULT 0;
 DEClARE cursor_ CURSOR FOR 
 SELECT userId FROM users_usergroups WHERE groupName = 'Stewards';
 DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_ = 1;
 OPEN cursor_;
 WHILE NOT done_ DO
	 FETCH cursor_ INTO found_;
	 IF NOT done_ THEN
		 CALL make_steward_user(found_);
	 END IF;
 END WHILE;
 CLOSE cursor_;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `add_everyone_to_group_stewards`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `add_everyone_to_group_stewards`()
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'handy if nearly all users should be Stewards; be sure to remove group Stewards from any users who should not be afterwards'
BEGIN
 DECLARE stewards_ int DEFAULT group_named('Stewards');
 DECLARE found_ int DEFAULT 0;
 DECLARE done_ int DEFAULT 0;
 DEClARE cursor_ CURSOR FOR SELECT userId FROM users_users;
 DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_ = 1;
 OPEN cursor_;
 WHILE NOT done_ DO
	FETCH cursor_ INTO found_;
	IF NOT done_ THEN
		CALL user_add_groupname(found_, 'Stewards');
	END IF;
 END WHILE;
 CLOSE cursor_;
END//
DELIMITER ;
-- #+END_SRC

SELECT @TESTS_PASSED, @TESTS_FAILED;

-- ** Session Variables

-- It would be good if set_cat_stew_vars
-- checked that all of the manual things
-- that the administrator needs to do
-- for feature_ngender_stewardship
-- have been done!!

-- It woud also be good if there were a
-- procedure which could be called to
-- set everything up that isn't set up
-- for feature_ngender_stewardship!!

-- #+BEGIN_SRC sql

DROP PROCEDURE IF EXISTS `set_cat_stew_vars`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `set_cat_stew_vars`()
  READS SQL DATA
	COMMENT 'Sets cat_stew_* Session Variables for feature_ngender_stewardship; not currently used!!'
BEGIN
 DECLARE user_ INT DEFAULT category_named_parent('User', 0);
 DECLARE test_ INT DEFAULT category_named_parent('Test',user_);
 DECLARE model_ INT DEFAULT category_named_parent('Steward',test_);
 DECLARE group_ INT;
 DECLARE msgs_ VARCHAR(200);		-- needs to start out NULL
 SELECT id INTO group_ FROM users_groups WHERE groupName = 'Stewards';
 IF user_ && test_ && model_ && id THEN
 		SET @cat_stew_cat_user = user_;
 		SET @cat_stew_cat_test = test_;
 		SET @cat_stew_group = group_;
 		SET @cat_stew_state = 1;
 		SET @cat_stew_status = '';
 ELSE
	SET @cat_stew_state = 0;
	IF NOT COALESCE(user_, 0) THEN
		SET msgs_ = CONCAT_WS(', ', msgs_, 'no category User'); 
	END IF;
	IF NOT COALESCE(test_, 0) THEN
		SET msgs_ = CONCAT_WS(', ', msgs_, 'no category User::Test'); 
	END IF;
	IF NOT COALESCE(model_, 0) THEN
		SET msgs_ = CONCAT_WS(', ', msgs_, 'no category User::Test::Steward'); 
	END IF;
	IF NOT COALESCE(group_, 0) THEN
		SET msgs_ = CONCAT_WS(', ', msgs_, 'no group Stewards'); 
	END IF;
 	SET @cat_stew_status = msgs_;
 END IF;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `feature_ngender_stewards`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `feature_ngender_stewards`(activate boolean)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT '(De)Activates feature_ngender_stewards; alternative to web ui'
BEGIN
	DECLARE flag_ text;
	IF (activate) THEN SET flag_ = 'y';
	ELSE SET flag_ = 'n';
	END IF;
	INSERT INTO `tiki_preferences`(`name`, `value`)
 	VALUES ('feature_ngender_stewards', flag_)
	ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
END//
DELIMITER ;
-- #+END_SRC
