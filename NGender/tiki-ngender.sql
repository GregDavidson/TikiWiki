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
-- - Reference variables are ids unless explicitly stated otherwise
--   -- e.g. group_ is an id, groupname_ is a string
-- - Parameters and locals often have a trailing slash
--   -- distinguishing them from fields, globals, keywords, etc.
-- - Double ? or ! indicates something problematic to revisit
-- - Triple ? or ! indicates an issue which must be resolved

-- Should DEFINER be changed to, e.g. tiki?  Or omitted??

-- ** test procedures

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
DROP PROCEDURE IF EXISTS `assert_true_`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `assert_true_`(value_ INT, expression_ TEXT, message_ TEXT)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'Signal error if value_ is zero'
BEGIN
	DECLARE msg_ TEXT;
	IF value_ != 0 THEN
		SET @TESTS_PASSED = @TESTS_PASSED + 1;
	ELSE
		SET @TESTS_FAILED = @TESTS_FAILED + 1;
		SET msg_ = CONCAT('Assert ', expression_, ' failed');
		IF message_ != '' THEN
			 SET msg_ = CONCAT(msg_, ': ', message_);
		END IF;
		SET msg_ = CONCAT(msg_, '!');
		SIGNAL SQLSTATE '02234'	SET MESSAGE_TEXT = msg_;
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
	COMMENT 'Unit Test: Complain if expression evaluates to non-zero value'
BEGIN
	DECLARE msg_ TEXT;
  DECLARE sql_ TEXT DEFAULT CONCAT(
	  'CALL chuck_int_(', expression_, 'IS NOT NULL)'
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
	SET @TESTS_FAILED = @TESTS_FAILED + 1;
	SET msg_ = CONCAT('Assert ', expression_, ' failed to fail');
	IF message_ != '' THEN
		 SET msg_ = CONCAT(msg_, ': ', message_);
	END IF;
	SET msg_ = CONCAT(msg_, '!');
	SIGNAL SQLSTATE '45001'	SET MESSAGE_TEXT = msg_;
  DEALLOCATE PREPARE stmt_;
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

-- ** User Names <-> User IDs

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `user_named`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `user_named`(username_ TEXT)
RETURNS INT READS SQL DATA DETERMINISTIC
COMMENT 'return id of user of given name or raise exception if none'
BEGIN
	DECLARE msg_ TEXT;
	DECLARE found_ INT DEFAULT 0;
	SELECT userId INTO found_ FROM users_users WHERE login = username_;
	IF found_ = 0 THEN
		SET msg_ = CONCAT('Username ', username_, ' not found!');
		SIGNAL SQLSTATE '02234'	SET MESSAGE_TEXT = msg_;
	END IF;
	RETURN found_;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `user_name`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `user_name`(user_ INT)
RETURNS TEXT READS SQL DATA DETERMINISTIC
COMMENT 'return name of user of given id or raise exception if none'
BEGIN
	DECLARE msg_ TEXT;
	DECLARE found_ TEXT DEFAULT '';
	SELECT login INTO found_ FROM users_users WHERE userId = user_;
	IF found_ = '' THEN 
		SET msg_ = CONCAT('User ', user_, ' not found!');
		SIGNAL SQLSTATE '02234'	SET MESSAGE_TEXT = msg_;
	END IF;
	RETURN found_;
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
DROP FUNCTION IF EXISTS `group_named`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `group_named`(groupname_ TEXT)
RETURNS INT READS SQL DATA DETERMINISTIC
COMMENT 'return id of group of given name or raise exception if none'
BEGIN
	DECLARE msg_ TEXT;
	DECLARE found_ INT DEFAULT 0;
	SELECT id INTO found_ FROM users_groups WHERE groupName = groupname_;
	IF found_ = 0 THEN 
		SET msg_ = CONCAT('Groupname ', groupname_, ' not found!');
		SIGNAL SQLSTATE '02234'
		SET MESSAGE_TEXT = msg_;
	END IF;
	RETURN found_;
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
	DECLARE msg_ TEXT;
	DECLARE found_ TEXT DEFAULT '';
	SELECT groupName INTO found_ FROM users_groups WHERE id = group_;
	IF found_ = '' THEN
		SET msg_ = CONCAT('Group ', group_, ' not found!');
		SIGNAL SQLSTATE '02234'
		SET MESSAGE_TEXT = msg_;
	END IF;
	RETURN found_;
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

-- ** Category Names <-> Category IDs

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `category_named_parent`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `category_named_parent`(category_name TEXT, parent INT)
RETURNS INT READS SQL DATA DETERMINISTIC
COMMENT 'return id of category of given name and parent id or raise exception if none'
BEGIN
	DECLARE msg_ TEXT DEFAULT 'category_named_parent: ';
	DECLARE found_ INT DEFAULT 0;
	SELECT categId INTO found_ FROM tiki_categories
	  WHERE name = category_name AND parentId = parent;
	IF found_ = 0 THEN
		SET msg_ =
		CONCAT(msg_, category_name, ' parent ', parent, ' not found!');
		SIGNAL SQLSTATE '02234'	SET MESSAGE_TEXT = msg_;
	END IF;
	RETURN found_;
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
	DECLARE msg_ TEXT;
	DECLARE found_ TEXT DEFAULT '';
	SELECT name INTO found_ FROM tiki_categories WHERE categId = category_;
	IF found_ = '' THEN 
		SET msg_ = CONCAT('Category ', category_, ' not found!');
		SIGNAL SQLSTATE '02234'	SET MESSAGE_TEXT = msg_;
	END IF;
	RETURN found_;
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
	DECLARE msg_ TEXT;
	DECLARE found_ INT DEFAULT -1;
	SELECT parentId INTO found_ FROM tiki_categories WHERE categId = category_;
	IF found_ = -1 THEN 
		SET msg_ = CONCAT('Category ', category_, ' not found!');
		SIGNAL SQLSTATE '02234'	SET MESSAGE_TEXT = msg_;
	END IF;
	RETURN found_;
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

-- this first one fails!! - do we care?? - fix or replace!!
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

-- this first one fails!! - do we care?? - fix or replace!!
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

-- this first one fails!! - do we care?? - fix or replace!!
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
	RETURN group_named(user_default_groupname(user_id));
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
-- Find a stable test user without a default group for next line!!!
-- SELECT user_default_group( user_named('sad_user') ) = 0;
CALL assert_fail('user_default_group( 0 )');
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

-- * Procedures to Create and Relate Default Groups and Categories

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
		IF convert(user_name using latin1) REGEXP convert('^Z[[:upper:]]' using latin1)
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
  -- Assert: is_user(user_):
	IF COALESCE(user_name(user_), '') = '' THEN
		SET msg_ = CONCAT(msg_, 'User ', user_, ' not found!');
		SIGNAL SQLSTATE '02234'	SET MESSAGE_TEXT = _msg;
	END IF;
  -- Assert: is_groupname(groupname_):
	IF COALESCE(group_named(groupname_), 0) = 0 THEN
		SET msg_ = CONCAT(msg_, 'Group ', groupname_, ' not found!');
		SIGNAL SQLSTATE '02234'	SET MESSAGE_TEXT = _msg;
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
	DECLARE msg_ TEXT DEFAULT 'user_add_group: ';
	DECLARE groupname_ TEXT DEFAULT group_name(group_);
	IF COALESCE(groupname_, '') = '' THEN
		SET msg_ = CONCAT(msg_, 'Group ', group_, ' not found!');
		SIGNAL SQLSTATE '02234'	SET MESSAGE_TEXT = _msg;
	END IF;
	CALL user_add_groupname(user_, groupname_);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `create_steward_default_group`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `create_steward_default_group`(user_ INT, username_ TEXT)
  READS SQL DATA MODIFIES SQL DATA
BEGIN
	DECLARE group_ INT DEFAULT user_default_group(user_);
	DECLARE groupname_ TEXT;
	-- Warn if default group exists with unexpected name??
	IF COALESCE(group_, 0) = 0 THEN
		SET groupname_ = CONCAT('User_', username_);
		-- some fields are NOT defaulted!!
		-- following non-default values set by Web Interface
		-- why is prorateInterval = 'day' ??
		INSERT INTO `users_groups`(
			`groupName`, `groupHome`,	`usersTrackerId`, `groupTrackerId`,
			`usersFieldId`,	`groupFieldId`,	`registrationUsersFieldIds`,
			`userChoice`, `prorateInterval`, `groupDesc`
		)
		VALUES (
			groupname_, '', 0, 0, 0, 0, '', '', 'day',
			concat('default group of steward ', username_)
		);
		SET group_ = LAST_INSERT_ID();
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
	DECLARE msg_ TEXT DEFAULT 'make_steward_user: ';
	DECLARE username_ TEXT DEFAULT user_name(user_);
	-- assert user_ exists:
	IF COALESCE(user_, 0) = 0 THEN
		SET msg_ = CONCAT(msg_, 'Bad user!');
		SIGNAL SQLSTATE '02234'	SET MESSAGE_TEXT = _msg;
	END IF;
	-- assert username_ exists:
	IF COALESCE(username_, '') = '' THEN
		SET msg_ = CONCAT(msg_, 'Bad user', user_, '!');
		SIGNAL SQLSTATE '02234'	SET MESSAGE_TEXT = _msg;
	END IF;
	CALL create_steward_default_group(user_, username_);
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
	DECLARE msg_ TEXT DEFAULT 'make_steward_username: ';
	DECLARE user_ INT DEFAULT user_named(user_name);
	-- assert user_ exists:
	IF COALESCE(user_, 0) = 0 THEN
		SET msg_ = CONCAT(msg_, 'User ', user_name, ' not found!');
		SIGNAL SQLSTATE '02234'	SET MESSAGE_TEXT = _msg;
	END IF;
	CALL make_steward_user(user_);
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
 DECLARE msg_ TEXT DEFAULT 'make_stewards_be_stewards: ';
 DECLARE stewards_ int DEFAULT group_named('Stewards');
 DECLARE found_ int DEFAULT 0;
 DECLARE done_ int DEFAULT 0;
 DEClARE cursor_ CURSOR FOR 
 SELECT userId FROM users_usergroups WHERE groupName = 'Stewards';
 DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_ = 1;
 -- assert group_named('Stewards') exists:
 IF NOT COALESCE(stewards_, 0) THEN
		SET msg_ = CONCAT(msg_, 'Group ', 'Steward', ' not found!');
		SIGNAL SQLSTATE '02234'	SET MESSAGE_TEXT = _msg;
 ELSE
	OPEN cursor_;
	WHILE NOT done_ DO
		FETCH cursor_ INTO found_;
		IF NOT done_ THEN
			CALL make_steward_user(found_);
		END IF;
	END WHILE;
	CLOSE cursor_;
 END IF;
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
 DECLARE msg_ TEXT DEFAULT 'add_everyone_to_group_stewards: ';
 DECLARE stewards_ int DEFAULT group_named('Stewards');
 DECLARE found_ int DEFAULT 0;
 DECLARE done_ int DEFAULT 0;
 DEClARE cursor_ CURSOR FOR SELECT userId FROM users_users;
 DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_ = 1;
 -- assert group_named('Stewards') exists:
 IF NOT COALESCE(stewards_, 0) THEN
	 SET msg_ = CONCAT(msg_, 'Group ', 'Steward', ' not found!');
	 SIGNAL SQLSTATE '02234'	SET MESSAGE_TEXT = _msg;
 ELSE
	 OPEN cursor_;
	 WHILE NOT done_ DO
		FETCH cursor_ INTO found_;
		IF NOT done_ THEN
			CALL user_add_groupname(found_, 'Stewards');
		END IF;
	 END WHILE;
	 CLOSE cursor_;
 END IF;
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
	COMMENT 'Sets cat_stew_* Session Variables for feature_ngender_stewardship'
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

-- CALL add_everyone_to_group_stewards();
-- - remove Stewards from inappropriate Users
-- CALL make_stewards_be_stewards();
