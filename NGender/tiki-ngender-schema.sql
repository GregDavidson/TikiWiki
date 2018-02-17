-- SQL Code for NGender Tiki Wiki Contributions
-- License: Same as regular Tiki Wiki (tiki.org) License
-- Author: J. Greg Davidson, 2017

-- Support for wrangling Tiki Category permissions
-- Depends on tiki-ngender.sql but NOT on feature_ngender_stewards
-- Not dependent on specific NGender Categories, though!

-- Really, we only need to make sure that Stewards see
-- (1) the default categories of other Stewards
-- (2) the categories in table group_category_models
-- We're being much more generous here by allowing
-- Stewards to see nearly all Tiki Categories.
-- See let_stewards_view_categories() for the details!

-- Parent of all User Categories
SET @USER_CATEGORY_PATH = 'User';
SET @USER_CATEGORY_PATTERN = CONCAT(@USER_CATEGORY_PATH, '::%');
SET @USER_CATEGORY = category_of_path(@USER_CATEGORY_PATH);
-- Parent of all Model Categories
SET @MODEL_CATEGORY_PATH = 'User::Test';
SET @MODEL_CATEGORY_PATTERN = CONCAT(@MODEL_CATEGORY_PATH, '::%');
SET @MODEL_CATEGORY = category_of_path(@MODEL_CATEGORY_PATH);
-- Parent of Project Categories not under a single User
SET @PROJECT_CATEGORY_PATH = 'Project';
SET @PROJECT_CATEGORY_PATTERN = CONCAT(@PROJECT_CATEGORY_PATH, '::%');
SET @PROJECT_CATEGORY = category_of_path(@PROJECT_CATEGORY_PATH);

-- #+BEGIN_SRC sql
DROP TABLE IF EXISTS `nonleaf_steward_categories`;
CREATE TABLE `nonleaf_steward_categories` (
  `category_` int(12) PRIMARY KEY REFERENCES tiki_categories(categId)
) ENGINE=InnoDB
COMMENT 'always give Stewards tiki_p_view_category permission on these categories';
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP TABLE IF EXISTS `non_steward_categories`;
CREATE TABLE `non_steward_categories` (
  `category_` int(12) PRIMARY KEY REFERENCES tiki_categories(categId)
) ENGINE=InnoDB
COMMENT 'do not give Stewards tiki_p_view_category permission on these categories';
-- #+END_SRC

-- #+BEGIN_SRC sql
INSERT INTO non_steward_categories(category_)
SELECT categId FROM tiki_categories
WHERE parentId = @MODEL_CATEGORY;
-- #+END_SRC

-- We need a convenient way to garbage collect Groups and Categories
-- that were created once upon a time and which are no longer wanted.
-- Right now we can do this manually by
-- (1) Making sure old_groups_and_categories has all of them
-- (2) Dropping all rows from group_category_models
-- (3) Rebuilding group_category_models from [[file:tiki-ngender-data.sql]]
-- (4) Dropping all groups and categories ONLY in old_groups_and_categories

-- #+BEGIN_SRC sql
CREATE TABLE IF NOT EXISTS `old_groups_and_categories` (
  `group_` int(11) NOT NULL REFERENCES users_groups(id),
  `category_` int(12) NOT NULL REFERENCES tiki_categories(categId),
  PRIMARY KEY `gc` (`group_`, `category_`)
) ENGINE=InnoDB COMMENT 'accumulates groups and categories we may have created; after rebuilding group_category_models any occurring ONLY here should be removed from here AND from the Tiki';
-- #+END_SRC

-- #+BEGIN_SRC sql
-- Use the Convenience Procedures later in this file to grow this Table!
-- Those Procedures are called by code in [[file:tiki-ngender-data.sql]]
-- The relationships represented by this table are added to the Tiki
-- by calling establish_group_category_models().
-- DROP TABLE IF EXISTS `group_category_models`;
CREATE TABLE IF NOT EXISTS `group_category_models` (
  `project_` int(12) NOT NULL REFERENCES tiki_categories(categId),
  `group_` int(11) NOT NULL REFERENCES users_groups(id),
  `category_` int(12) NOT NULL REFERENCES tiki_categories(categId),
  `group_model` int(11) NOT NULL REFERENCES users_groups(id),
  `category_model` int(12) NOT NULL REFERENCES tiki_categories(categId),
  PRIMARY KEY `gc` (`group_`, `category_`)
) ENGINE=InnoDB COMMENT 'the permissions of group_ on category_ should be the same as those on group_model on category_model and can be made so using copy_perms_grp_cat_grp_cat()';
-- Field comments:
-- project_: kas
-- category_:  -- either = to project_ or a child of project_ - enforce!!
-- PRIMARY KEY `gc`:  -- should we include project_ ??
-- #+END_SRC

-- #+BEGIN_SRC sql
INSERT IGNORE INTO old_groups_and_categories(group_, category_)
SELECT group_, category_ FROM group_category_models;
DELETE FROM group_category_models;
-- SELECT	group_name(group_) as target_group, category_path(category_) as target_category
-- FROM old_groups_and_categories;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP VIEW group_category_models_view;
CREATE VIEW group_category_models_view AS
SELECT	category_path(project_) as project,
				group_name(group_) as target_group, category_path(category_) as target_category,
				group_name(group_model) as model_group, category_path(category_model) as model_category
FROM group_category_models ORDER BY project, target_group, target_Category;
-- #+END_SRC

-- #+BEGIN_SRC sql
-- Run this after adding any Categories to the Tables above!
SET @CATS_SEEN = 0;
DROP PROCEDURE IF EXISTS `let_stewards_view_categories`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `let_stewards_view_categories`()
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'establish group/category permissions according to the models in table group_category_models'
BEGIN
	 DECLARE groupname_ int DEFAULT group_named('Stewards');
	 DECLARE permname_ TEXT DEFAULT 'tiki_p_view_category';
 	 DECLARE category_ int;
 	 DECLARE done_ int DEFAULT 0;
 	 DEClARE cursor_ CURSOR FOR 
	  SELECT categId FROM tiki_categories
		WHERE (
			categId NOT IN (SELECT category_ FROM non_steward_categories)
			AND categId NOT IN (SELECT parentId FROM tiki_categories)
		) OR categId IN (SELECT category_ FROM nonleaf_steward_categories)
		 OR categId IN (SELECT category_ FROM group_category_models);
 	 DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_ = 1;
	 OPEN cursor_;
	 foo: LOOP
		SET @CATS_SEEN = @CATS_SEEN + 1;
		 FETCH cursor_ INTO category_;
		 IF done_ THEN LEAVE foo; END IF;
		 INSERT IGNORE
		 INTO users_objectpermissions(`groupName`,`permName`, `objectType`,`objectId`)
		 VALUES (groupname_, permname_, 'category', MD5(CONCAT('category', category_)));
	 END LOOP;
	 CLOSE cursor_;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `establish_group_category_models`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `establish_group_category_models`()
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'establish group/category permissions according to the models in table group_category_models'
BEGIN
	 DECLARE group_ int;
 	 DECLARE category_ int;
 	 DECLARE group_model int;
 	 DECLARE category_model int;
 	 DECLARE done_ int DEFAULT 0;
 	 DEClARE cursor_ CURSOR FOR 
	  SELECT group_, category_, group_model, category_model	FROM group_category_models;
 	 DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_ = 1;
	 OPEN cursor_;
	 WHILE NOT done_ DO
		 FETCH cursor_ INTO group_, category_, group_model, category_model;
		 IF NOT done_ THEN
		 		CALL copy_perms_grp_cat_grp_cat(group_model, category_model, group_, category_);
		 END IF;
	 END WHILE;
	 CLOSE cursor_;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `add_group_category_models`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `add_group_category_models`(prj_cat INT, grp_ INT, cat_ INT, model_grp INT, model_cat INT)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'ensure row of table group_category_models reflects given arguments'
BEGIN
	INSERT INTO `group_category_models`(`project_`, `group_`, `category_`,`group_model`, `category_model`)
 	VALUES (prj_cat, grp_, cat_, model_grp, model_cat)
	ON DUPLICATE KEY UPDATE `project_` = prj_cat, `group_model` = model_grp, `category_model` = model_cat;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `nice_concat`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `nice_concat`(left_ TEXT, right_ TEXT)
RETURNS TEXT	DETERMINISTIC
	COMMENT 'returns left_ concatenated with right_ possibly separated by a hypen; suppress concatenation on trailing !'
BEGIN
	DECLARE left_trim TEXT DEFAULT TRIM(trailing '!' FROM left_);
	DECLARE right_trim TEXT DEFAULT TRIM(trailing '!' FROM right_);
	IF CHAR_LENGTH(left_) = 0 THEN	RETURN right_trim;	END IF;
	IF CHAR_LENGTH(right_) = 0 THEN RETURN left_trim;	END IF;
	IF left_ != left_trim THEN RETURN left_trim; END IF;
	IF right_ != right_trim THEN RETURN right_trim; END IF;
 	RETURN CONCAT(left_trim, '_', right_trim);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
-- the two ugly cases might better produce exceptions or warnings!!
CALL assert_true( 'nice_concat(\'foo\', \'\') = \'foo\'' );
CALL assert_true( 'nice_concat(\'\', \'foo\') = \'foo\'' );
CALL assert_true( 'nice_concat(\'\', \'\') = \'\'' ); -- ugly!!
CALL assert_true( 'nice_concat(\'left\', \'right\') = \'left_right\'' );
CALL assert_true( 'nice_concat(\'LEFT\', \'RIGHT\') = \'LEFT_RIGHT\'' );
CALL assert_true( 'nice_concat(\'Left\', \'Right\') = \'Left_Right\'' );
CALL assert_true( 'nice_concat(\'LEFT\', \'Right\') = \'LEFT_Right\'' );
CALL assert_true( 'nice_concat(\'LEFT\', \'right\') = \'LEFT_right\'' );
CALL assert_true( 'nice_concat(\'Left!\', \'Right\') = \'Left\'' );
CALL assert_true( 'nice_concat(\'Left!\', \'Right!\') = \'Left\'' ); -- ugly!!
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `cat_path_tail`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `cat_path_tail`(cat_path TEXT)
RETURNS TEXT	DETERMINISTIC
	COMMENT 'returns tail of category path'
BEGIN
	DECLARE sep_pos INT DEFAULT instr(reverse(cat_path), '::');
	-- trim any '::' delimited path from project_name
	IF sep_pos = 0 THEN
		RETURN cat_path;
	ELSE
		RETURN SUBSTR(cat_path, CHAR_LENGTH(cat_path) - sep_pos + 2);
	END IF;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
CALL assert_true( 'cat_path_tail(\'\') = \'\'' );
CALL assert_true( 'cat_path_tail(\'foo::\') = \'\'' );
CALL assert_true( 'cat_path_tail(\'foo::bar::\') = \'\'' );
CALL assert_true( 'cat_path_tail(\'foo::\') = \'\'' );
CALL assert_true( 'cat_path_tail(\'foo\') = \'foo\'' );
CALL assert_true( 'cat_path_tail(\'::foo\') = \'foo\'' );
CALL assert_true( 'cat_path_tail(\'x::y::foo\') = \'foo\'' );
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `inferred_group_name`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `inferred_group_name`(project_path TEXT, group_name TEXT)
RETURNS TEXT	DETERMINISTIC
	COMMENT 'returns nice_concat of group name and project name'
BEGIN
	DECLARE project_name TEXT DEFAULT cat_path_tail(project_path);
	IF group_name = '' AND project_name = '' THEN
		RETURN signal_no_text('inferred_group_name: Project Name or Group Name required!');
	END IF;
 	RETURN concat('Project_', nice_concat(project_name, group_name));
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
CALL assert_true( 'inferred_group_name(\'User::Test::DesignSpace\', \'Observers\') = \'Project_DesignSpace_Observers\'' );
CALL assert_true( 'inferred_group_name(\'DesignSpace\', \'Observers\') = \'Project_DesignSpace_Observers\'' );
CALL assert_true( 'inferred_group_name(\'DesignSpace\', \'Observers!\') = \'Project_Observers\'' );
CALL assert_true( 'inferred_group_name(\'User::Test::LOYL\', \'Observers\') = \'Project_LOYL_Observers\'' );
CALL assert_true( 'inferred_group_name(\'LOYL\', \'Observers\') = \'Project_LOYL_Observers\'' );
CALL assert_true( 'inferred_group_name(\'LOYL\', \'Observers!\') = \'Project_Observers\'' );
CALL assert_true( 'inferred_group_name(\'\', \'Observers!\') = \'Project_Observers\'' );
CALL assert_true( 'inferred_group_name(\'Public::LOYL\', \'\') = \'Project_LOYL\'' );
CALL assert_true( 'inferred_group_name(\'LOYL\', \'\') = \'Project_LOYL\'' );
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `model_category`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `model_category`(model_name TEXT) RETURNS INT
READS SQL DATA DETERMINISTIC
	COMMENT 'returns category_id_of_model adding parent category if none'
BEGIN
	IF model_name LIKE '%::%' THEN
	   RETURN category_of_path(model_name);
	END IF;
	RETURN category_named_parent(model_name, @MODEL_CATEGORY);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `inferred_category_path`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `inferred_category_path`(cat_name TEXT, cat_parent_path TEXT) RETURNS TEXT
READS SQL DATA DETERMINISTIC
	COMMENT 'returns inferred category path by adding parent category if none'
BEGIN
	DECLARE name_ TEXT DEFAULT TRIM(trailing '!' FROM cat_name);
	IF name_ != cat_name OR name_ LIKE '%::%' THEN
	   RETURN name_;
	END IF;
	IF name_ = cat_parent_path OR cat_parent_path LIKE CONCAT('%::', name_) THEN
	   RETURN cat_parent_path;
	END IF;
	RETURN CONCAT(cat_parent_path, '::', name_);
END//
DELIMITER ;
-- #+END_SRC

-- Need tests!!

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `inferred_cat_path`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `inferred_cat_path`(project_category TEXT, category TEXT, model_name TEXT)
RETURNS TEXT	DETERMINISTIC
	COMMENT 'returns project_category + :: + category + _ + model_name, except where contraindicated'
BEGIN
	RETURN inferred_category_path(nice_concat(category, model_name), project_category);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
CALL assert_true( 'inferred_cat_path(\'Project::LOYL\', \'Observer\', \'Readable\') = \'Project::LOYL::Observer_Readable\'' );
CALL assert_true( 'inferred_cat_path(\'Project::LOYL\', \'ObserverCanSee!\', \'Readable\') = \'Project::LOYL::ObserverCanSee\'' );
CALL assert_true( 'inferred_cat_path(\'Project::LOYL\', \'Public::ObserverCanSee!\', \'Readable\') = \'Public::ObserverCanSee\'' );
CALL assert_true( 'inferred_cat_path(\'Project::NGender\', \'NGender!\', \'Editable\') = \'Project::NGender\'' );
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `project_group_category_models`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `project_group_category_models`(
	project_name TEXT, grp_name TEXT, cat_name TEXT, model_grp_name TEXT, model_cat_name TEXT
) READS SQL DATA MODIFIES SQL DATA
	COMMENT 'ensure row of table group_category_models; all args passed as names, not ids; project is context for cat_name' 
BEGIN
	DECLARE comment_ TEXT DEFAULT concat('for ', project_name);
	DECLARE project_path TEXT DEFAULT inferred_category_path(project_name, @PROJECT_CATEGORY_PATH);
	DECLARE project_ INT DEFAULT ensure_categorypath_comment(project_path, comment_);
	DECLARE project_group_name TEXT DEFAULT inferred_group_name(project_path, grp_name);
	DECLARE cat_path TEXT DEFAULT inferred_cat_path(project_path, cat_name, model_cat_name);
	DECLARE grp_ INT DEFAULT ensure_groupname_comment(project_group_name, comment_);
	DECLARE cat_ INT DEFAULT ensure_categorypath_comment(cat_path, comment_);
	CALL add_group_category_models(
		project_, grp_, cat_, group_named(model_grp_name), model_category(model_cat_name)
	);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `project_group_category_models__`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `project_group_category_models__`(
	project_name TEXT, grp_name TEXT, cat_name TEXT, model_grp_name TEXT, model_cat_name TEXT
) READS SQL DATA MODIFIES SQL DATA
	COMMENT 'ensure row of table group_category_models; all args passed as names, not ids; project is context for cat_name'
BEGIN
	DECLARE comment_ TEXT DEFAULT concat('for ', project_name);
	DECLARE project_path TEXT DEFAULT inferred_category_path(project_name, @PROJECT_CATEGORY_PATH);
	DECLARE project_group_name TEXT DEFAULT inferred_group_name(project_path, grp_name);
	DECLARE cat_path TEXT DEFAULT inferred_cat_path(project_path, cat_name, model_cat_name);
	SELECT project_path, project_group_name, cat_path, comment_,
		model_grp_name, CONCAT(@MODEL_CATEGORY_PATH, '::', model_cat_name) AS model_cat;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `project_group_models`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `project_group_models`(
	project TEXT, grp_name TEXT, model_grp_name TEXT, model_cat_name TEXT
) READS SQL DATA MODIFIES SQL DATA
	COMMENT 'call project_group_category_models with the category name the same as the project name'
BEGIN
	CALL project_group_category_models(
		project,grp_name, CONCAT(project, '!'), model_grp_name, model_cat_name
	);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `project_group_models__`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `project_group_models__`(
	project TEXT, grp_name TEXT, model_grp_name TEXT, model_cat_name TEXT
) READS SQL DATA MODIFIES SQL DATA
	COMMENT 'call project_group_category_models with the category name the same as the project name'
BEGIN
	CALL project_group_category_models__(
		project,grp_name, CONCAT(project, '!'), model_grp_name, model_cat_name
	);
END//
DELIMITER ;
-- #+END_SRC

-- ** Various procedures designed to automate common tasks

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `add_category_if_category`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `add_category_if_category`(add_cat_ int, has_cat_ int)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'associate category add_cat_ with all objects associated with category has_cat_'
BEGIN
 DECLARE has_cat int DEFAULT category_id(has_cat_);
 DECLARE add_cat int DEFAULT category_id(add_cat_);
 DECLARE found_ int DEFAULT 0;
 DECLARE done_ int DEFAULT 0;
 DEClARE cursor_ CURSOR FOR
 SELECT catObjectId FROM tiki_category_objects WHERE categId = has_cat;
 DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_ = 1;
 OPEN cursor_;
 FETCH cursor_ INTO found_;
 WHILE NOT done_ DO
 	 CALL add_object_category(found_, add_cat);
	 FETCH cursor_ INTO found_;
 END WHILE;
 CLOSE cursor_;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `drop_category_if_category`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `drop_category_if_category`(drop_cat_ int, has_cat_ int)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'drop category drop_cat_ with all objects associated with category has_cat_'
BEGIN
 DECLARE has_cat int DEFAULT category_id(has_cat_);
 DECLARE drop_cat int DEFAULT category_id(drop_cat_);
 DECLARE found_ int DEFAULT 0;
 DECLARE done_ int DEFAULT 0;
 DEClARE cursor_ CURSOR FOR
 SELECT catObjectId FROM tiki_category_objects WHERE categId = has_cat;
 DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_ = 1;
 OPEN cursor_;
 FETCH cursor_ INTO found_;
 WHILE NOT done_ DO
 	 CALL drop_object_category(found_, drop_cat);
	 FETCH cursor_ INTO found_;
 END WHILE;
 CLOSE cursor_;
END//
DELIMITER ;
-- #+END_SRC
