-- * NGender Tiki Schema and Support Code for Projects & Stewards
-- License: Same as regular Tiki Wiki (tiki.org) License
-- Authors:
-- jgd = J. Greg Davidson: 2017, 2018
-- kas = Keith Allen Shillington: 2018

-- Support for wrangling Tiki Category permissions
-- Depends on tiki-ngender.sql
-- Complements php code implementing feature_ngender_stewards
-- Not dependent on specific NGender Categories, though!

-- Really, we only need to make sure that Stewards see
-- (1) the default categories of other Stewards
-- (2) the categories in table group_category_models
-- We're being much more generous here by allowing
-- Stewards to see nearly all Tiki Categories.
-- See let_stewards_view_categories() for the details!

-- ** Group, Category, Models: Plumbing

-- Plumbing, in the language of the Linux Kernel Developers,
-- refers to the underlying mechanisms making desired
-- functionality possible.  Bare plumbing is not always
-- comfortable for direct use by users, so see below for
-- Porcelain!

-- *** Conventions

-- These functions can be replaced with mysql/mariadb global
-- variables as soon as all platforms using this code
-- support the very recent ability to restore global
-- variables.

-- Stewards should be able to see all top-level user
-- categories plus the category tree under their own
-- category.

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `USER_CATEGORY_PATH`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `USER_CATEGORY_PATH`()
RETURNS TEXT DETERMINISTIC
COMMENT 'Parent of all User Categories'
BEGIN
RETURN 'User';
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `USER_CATEGORY_PATTERN`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `USER_CATEGORY_PATTERN`()
RETURNS TEXT DETERMINISTIC
COMMENT 'Parent of all User Categories'
BEGIN
RETURN CONCAT(USER_CATEGORY_PATH(), '::%');
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `USER_CATEGORY`;
DROP FUNCTION IF EXISTS `USER_CATEGORY_ID`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `USER_CATEGORY_ID`()
RETURNS INTEGER	DETERMINISTIC
COMMENT 'Parent of all User Categories'
BEGIN
RETURN category_of_path(USER_CATEGORY_PATH());
END//
DELIMITER ;
-- #+END_SRC

-- Stewards should be able to see all categories under
-- Category Project.

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `PROJECT_CATEGORY_PATH`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `PROJECT_CATEGORY_PATH`()
RETURNS TEXT	DETERMINISTIC
COMMENT 'Parent of Project Categories not under a single User'
BEGIN
RETURN 'Project';
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `PROJECT_CATEGORY_PATTERN`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `PROJECT_CATEGORY_PATTERN`()
RETURNS TEXT	DETERMINISTIC
COMMENT 'Parent of Project Categories not under a single User'
BEGIN
RETURN CONCAT(PROJECT_CATEGORY_PATH(), '::%');
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `PROJECT_CATEGORY`;
DROP FUNCTION IF EXISTS `PROJECT_CATEGORY_ID`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `PROJECT_CATEGORY_ID`()
RETURNS INTEGER	DETERMINISTIC
COMMENT 'Parent of Project Categories not under a single User'
BEGIN
RETURN category_of_path(PROJECT_CATEGORY_PATH());
END//
DELIMITER ;
-- #+END_SRC

-- Stewards should be able to see all categories under
-- Category Type.

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `TYPE_CATEGORY_PATH`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `TYPE_CATEGORY_PATH`()
RETURNS TEXT	DETERMINISTIC
COMMENT 'Parent of Type Categories not under a single User'
BEGIN
RETURN 'Type';
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `TYPE_CATEGORY_PATTERN`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `TYPE_CATEGORY_PATTERN`()
RETURNS TEXT	DETERMINISTIC
COMMENT 'Parent of Type Categories not under a single User'
BEGIN
RETURN CONCAT(TYPE_CATEGORY_PATH(), '::%');
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `TYPE_CATEGORY`;
DROP FUNCTION IF EXISTS `TYPE_CATEGORY_ID`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `TYPE_CATEGORY_ID`()
RETURNS INTEGER	DETERMINISTIC
COMMENT 'Parent of Type Categories not under a single User'
BEGIN
RETURN category_of_path(TYPE_CATEGORY_PATH());
END//
DELIMITER ;
-- #+END_SRC

-- Stewards should NOT be able to see any Model Categories!
-- These categories are only to be used to store permissions
-- to use in building new projects.

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `MODEL_CATEGORY_PATH`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `MODEL_CATEGORY_PATH`()
RETURNS TEXT	DETERMINISTIC
COMMENT 'Parent of all Model Categories'
BEGIN
RETURN 'User::Test';
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `MODEL_CATEGORY_PATTERN`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `MODEL_CATEGORY_PATTERN`()
RETURNS TEXT	DETERMINISTIC
COMMENT 'Parent of all Model Categories'
BEGIN
RETURN CONCAT(MODEL_CATEGORY_PATH(), '::%');
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP FUNCTION IF EXISTS `MODEL_CATEGORY`;
DROP FUNCTION IF EXISTS `MODEL_CATEGORY_ID`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `MODEL_CATEGORY_ID`()
RETURNS INTEGER	DETERMINISTIC
COMMENT 'Parent of all Model Categories'
BEGIN
RETURN category_of_path(MODEL_CATEGORY_PATH());
END//
DELIMITER ;
-- #+END_SRC

-- *** Tracking Groups and Categories of the Past

-- We need a convenient way to garbage collect Groups and Categories
-- that were created once upon a time so that we can check them against
-- the categories which are current and delete the others!
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
) ENGINE=InnoDB COMMENT 'accumulates groups and categories we may have created; after rebuilding group_category_models any groups and categories occurring ONLY here should be removed from here AND from the Tiki';
-- #+END_SRC

-- *** TABLE group_category_models

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
-- project_: a category associated with a project as a whole
-- group_: a group representing a role in the project
-- category_: same as project_ or a child thereof -- enforce!!
-- group_model, category_model: copy permissions from these
-- #+END_SRC

-- *** old_group_category_models += group_category_models, then clear group_category_models

-- #+BEGIN_SRC sql
INSERT IGNORE INTO old_groups_and_categories(group_, category_)
SELECT group_, category_ FROM group_category_models;
DELETE FROM group_category_models;
-- SELECT	group_name(group_) as target_group, category_path(category_) as target_category
-- FROM old_groups_and_categories;
-- #+END_SRC

-- *** VIEW group_category_models_view

-- #+BEGIN_SRC sql
DROP VIEW group_category_models_view;
CREATE VIEW group_category_models_view AS
SELECT	category_path(project_) as project,
	group_name(group_) as target_group, category_path(category_) as target_category,
	group_name(group_model) as model_group, category_path(category_model) as model_category
FROM group_category_models ORDER BY project, target_group, target_Category;
-- #+END_SRC

-- *** Associated Plumbing Functions

-- **** PROCEDURE let_stewards_view_categories and delegates

-- #+BEGIN_SRC sql
-- Grant Stewards view permission to categories in the category_ column of group_category_models
DROP PROCEDURE IF EXISTS `let_stewards_view_project_categories`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `let_stewards_view_project_categories`()
READS SQL DATA MODIFIES SQL DATA
COMMENT 'Grant Stewards view permission to the categories in group_category_models.category_'
BEGIN
DECLARE groupname_ int DEFAULT group_named('Stewards');
DECLARE permname_ TEXT DEFAULT 'tiki_p_view_category';
DECLARE category_ int;
DECLARE done_ int DEFAULT 0;
DECLARE cursor_ CURSOR FOR 
SELECT DISTINCT category_ FROM group_category_models;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_ = 1;
OPEN cursor_;
foo: LOOP
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
-- Grant Stewards view permission to top-level categories under Category User except for 'User::Test'
DROP PROCEDURE IF EXISTS `let_stewards_view_user_categories`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `let_stewards_view_user_categories`()
READS SQL DATA MODIFIES SQL DATA
COMMENT 'Grant Stewards view permission to non-model child categories of User'
BEGIN
DECLARE groupname_ int DEFAULT group_named('Stewards');
DECLARE permname_ TEXT DEFAULT 'tiki_p_view_category';
DECLARE parent_ int = USER_CATEGORY_ID();
DECLARE model_ int = MODEL_CATEGORY_ID();
DECLARE category_ int;
DECLARE done_ int DEFAULT 0;
DECLARE cursor_ CURSOR FOR 
SELECT DISTINCT categId_ FROM tiki_categories
WHERE parentId = parent_ AND categId != model_;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_ = 1;
OPEN cursor_;
foo: LOOP
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
-- Grant members of group view permission on categories under given category
DROP PROCEDURE IF EXISTS `let_group_view_descendant_categories`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `let_group_view_descendant_categories`(group_id INT, root_cat_id INT)
READS SQL DATA MODIFIES SQL DATA
COMMENT 'Grant members of group view permission on categories under given category'
BEGIN
DECLARE permname_ TEXT DEFAULT 'tiki_p_view_category';
DECLARE category_ int;
DECLARE done_ int DEFAULT 0;
DECLARE cursor_ CURSOR FOR 
WITH RECURSIVE cats AS (
	SELECT root_cat_id AS id
	UNION ALL
	SELECT categId AS id
	FROM tiki_categories, cats
	WHERE parentId = cats.id
) SELECT id FROM cats WHERE id != root_cat_id;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_ = 1;
OPEN cursor_;
foo: LOOP
	FETCH cursor_ INTO category_;
	IF done_ THEN LEAVE foo; END IF;
	INSERT IGNORE
	INTO users_objectpermissions(`groupName`,`permName`, `objectType`,`objectId`)
	VALUES (group_id, permname_, 'category', MD5(CONCAT('category', category_)));
END LOOP;
CLOSE cursor_;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
-- Grant Stewards view permission to all categories under Category Type
DROP PROCEDURE IF EXISTS `let_stewards_view_type_categories`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `let_stewards_view_type_categories`()
READS SQL DATA MODIFIES SQL DATA
COMMENT 'Grant Stewards view permission to all categories under Category Type'
BEGIN
  CALL let_group_view_descendant_categories(
    group_named('Stewards'), TYPE_CATEGORY_ID()
  );
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
-- Grant Stewards view permission on categories under their default category
-- replace with simple loop calling function above!!
DROP PROCEDURE IF EXISTS `let_stewards_view_their_own_categories`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `let_stewards_view_their_own_categories`()
READS SQL DATA MODIFIES SQL DATA
COMMENT 'Grant Stewards view permission to all categories under Category Type'
BEGIN
 DECLARE user_id int;
 DECLARE done_ int DEFAULT 0;
 DECLARE cursor_ CURSOR FOR 
   SELECT userId FROM users_usergroups
   WHERE groupName = group_named('Stewards');
 DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_ = 1;
 OPEN cursor_;
 foo: LOOP
   FETCH cursor_ INTO user_id;
   IF done_ THEN LEAVE foo; END IF;
   CALL let_group_view_descendant_categories(
     user_default_group(user_id), user_default_category(user_id)
   );
 END LOOP;
 CLOSE cursor_;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
-- Run this after adding/deleting Categories Stewards should be able to see and use
-- The policy for which Categories Stewards can view view is in the routines this routine calls!
DROP PROCEDURE IF EXISTS `let_stewards_view_categories`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `let_stewards_view_categories`()
READS SQL DATA MODIFIES SQL DATA
COMMENT 'Run this after adding/deleting Categories Stewards should be able to see and use'
BEGIN
	CALL let_stewards_view_user_categories;
	CALL let_stewards_view_type_categories;
	CALL let_stewards_view_project_categories;
	CALL let_stewards_view_their_own_categories;
END//
DELIMITER ;
-- #+END_SRC

-- *** group_category_models service routines

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `establish_group_category_models`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `establish_group_category_models`()
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'establish group/category permissions according to the models in table group_category_models'
BEGIN
	 DECLARE group_id int;
 	 DECLARE category_id int;
 	 DECLARE group_model_id int;
 	 DECLARE category_model_id int;
 	 DECLARE done_ int DEFAULT 0;
 	 DEClARE cursor_ CURSOR FOR 
	  SELECT group_, category_, group_model, category_model	FROM group_category_models;
 	 DECLARE CONTINUE HANDLER FOR NOT FOUND SET done_ = 1;
	 OPEN cursor_;
	 foo: LOOP
		 FETCH cursor_ INTO group_id, category_id, group_model_id, category_model_id;
		 IF done_ THEN LEAVE foo; END IF;
		 CALL copy_perms_grp_cat_grp_cat(group_model_id, category_model_id, group_id, category_id);
	 END LOOP;
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

-- ** Porcelain

-- *** Preparing for Porcelain

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
DROP FUNCTION IF EXISTS `get_model_category`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `get_model_category`(model_name TEXT) RETURNS INT
READS SQL DATA DETERMINISTIC
	COMMENT 'returns category_id_of_model adding parent category if none'
BEGIN
	IF model_name LIKE '%::%' THEN
	   RETURN category_of_path(model_name);
	END IF;
	RETURN category_named_parent(model_name, MODEL_CATEGORY_ID());
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

-- *** Behold: Porcelain!

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
	DECLARE project_path TEXT DEFAULT inferred_category_path(project_name, PROJECT_CATEGORY_PATH());
	DECLARE project_ INT DEFAULT ensure_categorypath_comment(project_path, comment_);
	DECLARE project_group_name TEXT DEFAULT inferred_group_name(project_path, grp_name);
	DECLARE cat_path TEXT DEFAULT inferred_cat_path(project_path, cat_name, model_cat_name);
	DECLARE grp_ INT DEFAULT ensure_groupname_comment(project_group_name, comment_);
	DECLARE cat_ INT DEFAULT ensure_categorypath_comment(cat_path, comment_);
	CALL add_group_category_models(
		project_, grp_, cat_, group_named(model_grp_name), get_model_category(model_cat_name)
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
	DECLARE project_path TEXT DEFAULT inferred_category_path(project_name, PROJECT_CATEGORY_PATH());
	DECLARE project_group_name TEXT DEFAULT inferred_group_name(project_path, grp_name);
	DECLARE cat_path TEXT DEFAULT inferred_cat_path(project_path, cat_name, model_cat_name);
	SELECT project_path, project_group_name, cat_path, comment_,
		model_grp_name, CONCAT(MODEL_CATEGORY_PATH(), '::', model_cat_name) AS model_cat;
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
-- need unit test code here!!

-- ** Cleanup and Misc

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
