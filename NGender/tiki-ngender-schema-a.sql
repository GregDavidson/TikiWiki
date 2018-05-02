-- * SQL Code for NGender Tiki Wiki Contributions
-- License: Same as regular Tiki Wiki (tiki.org) License
-- Author: J. Greg Davidson, 2017

-- Support for wrangling Tiki Category permissions
-- Depends on tiki-ngender.sql but NOT on feature_ngender_stewards
-- Not dependent on specific NGender Categories, though!

-- ** Group, Category, Models: Plumbing

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

INSERT INTO non_steward_categories(category_)
SELECT categId FROM tiki_categories
WHERE parentId = category_of_path('User::Test');

-- #+BEGIN_SRC sql
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
		) OR categId IN (SELECT category_ FROM nonleaf_steward_categories);
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

DROP TABLE IF EXISTS `group_category_models`;
CREATE TABLE `group_category_models` (
  `group_` int(11) NOT NULL REFERENCES users_groups(id),
  `category_` int(12) NOT NULL REFERENCES tiki_categories(categId),
  `group_model` int(11) NOT NULL REFERENCES users_groups(id),
  `category_model` int(12) NOT NULL REFERENCES tiki_categories(categId),
  PRIMARY KEY `gc` (`group_`, `category_`)
) ENGINE=InnoDB COMMENT 'the permissions of group_ on category_ should be the same as those on group_model on category_model and can be made so using copy_perms_grp_cat_grp_cat()';

-- #+BEGIN_SRC sql
CREATE VIEW group_category_models_view AS
SELECT	group_name(group_) as target_group, category_path(category_) as target_category,
				group_name(group_model) as model_group, category_path(category_model) as model_category
FROM group_category_models;
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
PROCEDURE `add_group_category_models`(grp_ INT, cat_ INT, model_grp INT, model_cat INT)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'ensure row of table group_category_models reflects given arguments'
BEGIN
	INSERT INTO `group_category_models`(`group_`, `category_`,`group_model`, `category_model`)
 	VALUES (grp_, cat_, model_grp, model_cat)
	ON DUPLICATE KEY UPDATE `group_model` = VALUES(`group_model`),  `category_model` = VALUES(`category_model`);
END//
DELIMITER ;
-- #+END_SRC

-- ** Group, Category, Models: Porcelain

-- #+BEGIN_SRC sql
-- Infer no prefix if name ends with exclamation point - but remove exclamation point!
--- Is this still needed???
-- Use _ after project_path ending in upper-case letter
DROP FUNCTION IF EXISTS `inferred_group_name`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `inferred_group_name`(maybe_project_path TEXT, group_name_ TEXT)
RETURNS TEXT	DETERMINISTIC
	COMMENT 'returns group name possibly prefixed by project name'
BEGIN
	DECLARE project_path TEXT DEFAULT COALESCE(maybe_project_path, '');
	DECLARE group_name TEXT DEFAULT TRIM(trailing '!' FROM group_name_);
	DECLARE project_name TEXT DEFAULT project_path;
	DECLARE sep_pos INT DEFAULT instr(reverse(project_path), '::');
	-- trim any '::' delimited path from project_name
	IF sep_pos <> 0 THEN
		SET project_name = SUBSTR(project_path, CHAR_LENGTH(project_path) - sep_pos + 2);
	END IF;
	IF group_name = '' AND project_name = '' THEN
		RETURN signal_no_text('inferred_group_name: Project Name or Group Name required!');
	END IF;
	IF project_name = '' OR group_name <> group_name_ THEN RETURN group_name; END IF;
	IF group_name = '' THEN RETURN project_name; END IF;
	IF BINARY substr(project_name, -1) = BINARY UPPER( substr(project_name, -1) ) THEN
 		RETURN CONCAT(project_name, '_', group_name);
	ELSE
 		RETURN CONCAT(project_name, group_name);
	END IF;
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
CALL assert_true( 'inferred_group_name(\'User::Test::DesignSpace\', \'Observers\') = \'DesignSpaceObservers\'' );
CALL assert_true( 'inferred_group_name(\'DesignSpace\', \'Observers\') = \'DesignSpaceObservers\'' );
CALL assert_true( 'inferred_group_name(\'DesignSpace\', \'Observers!\') = \'Observers\'' );
CALL assert_true( 'inferred_group_name(\'User::Test::LOYL\', \'Observers\') = \'LOYL_Observers\'' );
CALL assert_true( 'inferred_group_name(\'LOYL\', \'Observers\') = \'LOYL_Observers\'' );
CALL assert_true( 'inferred_group_name(\'LOYL\', \'Observers!\') = \'Observers\'' );
CALL assert_true( 'inferred_group_name(\'\', \'Observers!\') = \'Observers\'' );
CALL assert_true( 'inferred_group_name(\'Public::LOYL\', \'\') = \'LOYL\'' );
CALL assert_true( 'inferred_group_name(\'LOYL\', \'\') = \'LOYL\'' );
-- #+END_SRC

-- #+BEGIN_SRC sql
-- Infer no suffix if name ends with exclamation point - but remove exclamation point!
-- Infer no path prefix if name contains one or more :: 
DROP FUNCTION IF EXISTS `inferred_cat_path`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
FUNCTION `inferred_cat_path`(project_path TEXT, cat_name TEXT, model_name TEXT)
RETURNS TEXT	DETERMINISTIC
	COMMENT 'returns path of category under project category, possibly suffixed by model name'
BEGIN
	DECLARE path_ TEXT DEFAULT TRIM(trailing '!' FROM cat_name);
	IF cat_name = path_ THEN SET path_ = CONCAT(path_, model_name); END IF;
	IF COALESCE(project_path, '') = '' THEN RETURN path_; END IF;
	IF path_ LIKE '%::%' THEN RETURN path_; END IF;
	RETURN CONCAT(project_path, '::', path_);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
CALL assert_true( 'inferred_cat_path(\'LOYL\', \'Observer\', \'Readable\') = \'LOYL::ObserverReadable\'' );
CALL assert_true( 'inferred_cat_path(\'LOYL\', \'ObserverCanSee!\', \'Readable\') = \'LOYL::ObserverCanSee\'' );
CALL assert_true( 'inferred_cat_path(\'\', \'Observer_\', \'Readable\') = \'Observer_Readable\'' );
CALL assert_true( 'inferred_cat_path(\'LOYL\', \'Public::Observer_\', \'Readable\') = \'Public::Observer_Readable\'' );
CALL assert_true( 'inferred_cat_path(\'LOYL\', \'Public::ObserverCanSee!\', \'Readable\') = \'Public::ObserverCanSee\'' );
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `project_group_category_models`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `project_group_category_models`(
	project TEXT, grp_name TEXT, cat_name TEXT, model_grp_name TEXT, model_cat_name TEXT
) READS SQL DATA MODIFIES SQL DATA
	COMMENT 'ensure row of table group_category_models; all args passed as names, not ids; project is context for cat_name'
BEGIN
	DECLARE model_cat_parent TEXT DEFAULT 'User::Test';
	DECLARE model_cat_path TEXT DEFAULT concat( model_cat_parent, '::', model_cat_name );
	DECLARE maybe_project TEXT DEFAULT COALESCE( project, '' );
	DECLARE project_group_name TEXT DEFAULT inferred_group_name(maybe_project, grp_name);
	DECLARE cat_path TEXT DEFAULT inferred_cat_path(maybe_project, cat_name, model_cat_name);
	DECLARE comment_ TEXT DEFAULT COALESCE(concat('for ', maybe_project), '');
	DECLARE model_grp INT DEFAULT group_named(model_grp_name);
	DECLARE model_cat INT DEFAULT category_of_path(model_cat_path);
	DECLARE grp_ INT DEFAULT ensure_groupname_comment(project_group_name, comment_);
	DECLARE cat_ INT DEFAULT ensure_categorypath_comment(cat_path, comment_);
	CALL add_group_category_models(grp_, cat_, model_grp, model_cat);
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
