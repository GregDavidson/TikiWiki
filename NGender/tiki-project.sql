-- What return type should be used for group and category ids?

-- * Names <-> IDs

-- Naming & Datatype policy:
-- Everything is handled by id unless explicitly stated otherwise!

-- ** test procedures

SET @TESTS_PASSED = 0;
SET @TESTS_FAILED = 0;

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `chuck_int_`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `chuck_int_`(value_ INT)
	COMMENT 'throw away the argument'
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
	COMMENT 'Complain if value_ evalutes to non-zero integer'
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
	COMMENT 'Complain if expression fails or evalutes to non-zero integer'
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
	COMMENT 'Complain if expression fails or evalutes to non-zero integer'
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
	COMMENT 'Complain if expression fails or evalutes to non-zero integer'
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
	COMMENT 'Complain if expression fails or evalutes to non-zero integer'
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
BEGIN
	DECLARE msg_ TEXT;
	DECLARE found_ INT DEFAULT 0;
	SELECT userId INTO found_ FROM users_users WHERE login = username_;
	IF found_ = 0 THEN
		SET msg_ = CONCAT('Username ', username_, 'not found!');
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
BEGIN
	DECLARE msg_ TEXT;
	DECLARE found_ TEXT DEFAULT '';
	SELECT login INTO found_ FROM users_users WHERE userId = user_;
	IF found_ = '' THEN 
		SET msg_ = CONCAT('User ', user_, 'not found!');
		SIGNAL SQLSTATE '02234'	SET MESSAGE_TEXT = msg_;
	END IF;
	RETURN found_;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
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
BEGIN
	DECLARE msg_ TEXT;
	DECLARE found_ INT DEFAULT 0;
	SELECT id INTO found_ FROM users_groups WHERE groupName = groupname_;
	IF found_ = 0 THEN 
		SET msg_ = CONCAT('Groupname ', groupname_, 'not found!');
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
BEGIN
	DECLARE found_ TEXT DEFAULT '';
	SELECT groupName INTO found_ FROM users_groups WHERE id = group_;
	IF found_ = '' THEN 
		SIGNAL SQLSTATE '02234'
		SET MESSAGE_TEXT = CONCAT('Group ', group_, 'not found!');
	END IF;
	RETURN found_;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
CALL assert_true('group_name(2) != \'\'');
CALL assert_true('group_named(group_name(2)) = 2');
CALL assert_true('group_named(0) = \'\'');
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
BEGIN
	DECLARE msg_ TEXT;
	DECLARE found_ INT DEFAULT 0;
	SELECT categId INTO found_ FROM tiki_categories
	  WHERE name = category_name AND parentId = parent;
	IF found_ = 0 THEN
		SET msg_ =
		CONCAT('Category name ', category_name_, ' parent ', parent, 'not found!');
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
BEGIN
	DECLARE msg_ TEXT;
	DECLARE found_ TEXT DEFAULT '';
	SELECT name INTO found_ FROM tiki_categories WHERE categId = category_;
	IF found_ = '' THEN 
		SET msg_ = CONCAT('Category ', category_, 'not found!');
		SIGNAL SQLSTATE '02234'	SET MESSAGE_TEXT = _msg;
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
CALL assert_true('category_name( category_named_parent(\'user\', 0) ) = \'user\'');
CALL assert_fail('category_name( 0 )');
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `category_parent`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `category_parent`(category_ INT)
RETURNS INT READS SQL DATA DETERMINISTIC
BEGIN
	DECLARE found_ INT DEFAULT -1;
	SELECT parentId INTO found_ FROM tiki_categories WHERE categId = category_;
	RETURN found_;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
CALL assert_fail('category_parent( category_named_parent(\'user\', 0) )');
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
DROP PROCEDURE IF EXISTS `copy_group_category_permissions`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `copy_group_category_permissions`(group_ INT, model_ INT, target_ INT)
  READS SQL DATA MODIFIES SQL DATA
BEGIN
	DECLARE groupname_ TEXT DEFAULT group_name(group_);
	DELETE FROM user_objectpermissions
	WHERE objectType = 'category' AND groupName = groupname_
	AND objectId = MD5(CONCAT('category', target_));
	INSERT INTO user_objectpermissions(`groupName`,`permName`, `objectType`,`objectId`)
	SELECT `groupName`,`permName`, `objectType`,`objectId`
	FROM user_objectpermissions
	WHERE objectType = 'category' AND groupName = groupname_
	AND objectId = MD5(CONCAT('category', model__));
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
	DECLARE target_ INT DEFAULT group_default_category(group_);
	DECLARE parent_ INT;
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
	CALL copy_category_permissions(model_, target_);
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
  -- Assert: is_user(user_) !!
  -- Assert: is_groupname(groupname_) !!
	INSERT IGNORE INTO `users_usergroups`(userId, groupName, created)
	VALUES ( user_, groupname_, UNIX_TIMESTAMP() );
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `user_add_group`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `user_add_groupname`(user_ INT, group_ INT)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'ensure given user is associated with given group'
BEGIN
  -- assert is_user(user_) !!
  -- assert is_group(group_) !!
	DECLARE groupname_ INT DEFAULT group_name(group_);
	CALL user_add_groupname(user_, groupname_);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `create_steward_group`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `create_steward_group`(user_ INT, username_ TEXT)
  READS SQL DATA MODIFIES SQL DATA
BEGIN
	DECLARE group_ INT DEFAULT user_default_group(user_);
	DECLARE groupname_ TEXT;
	IF group_ = 0 THEN
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
	COMMENT 'Ensure user initiated into categorical stewardship'
BEGIN
	DECLARE username_ INT DEFAULT user_name(user_);
	-- assert user_ <> 0 !!
	-- assert username_ <> '' !!
	IF user_ != 0 AND username_ != '' THEN
		CALL create_steward_group(user_, username_);
		CALL user_add_groupname(user_, 'steward');
	END IF;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `make_steward_username`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `make_steward_username`(user_name TEXT)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'Ensure user initiated into categorical stewardship'
BEGIN
	DECLARE user_ INT DEFAULT user_named(user_name);
	-- assert user_ <> 0 !!
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
	COMMENT 'Ensure users in group Steward initiated into categorical stewardship'
BEGIN
-- assert group_named('steward') <> 0 !!
 DECLARE found_ int DEFAULT 0;
 DECLARE done_ int DEFAULT 0;
 DEClARE cursor_ CURSOR FOR 
 SELECT userId FROM users_usergroups WHERE groupName = 'steward';
 DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
 OPEN cursor_;
 LOOP
  FETCH cursor_ INTO found_;
  IF done_ = 1 THEN CLOSE cursor_; RETURN; END IF;
	CALL make_steward_user(found_);
 END LOOP;
END//
DELIMITER ;
-- #+END_SRC

SELECT @TESTS_PASSED, @TESTS_FAILED;
