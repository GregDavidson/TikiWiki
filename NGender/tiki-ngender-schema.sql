DROP TABLE IF EXISTS `group_category_models`;
CREATE TABLE `group_category_models` (
  `group_` int(11) NOT NULL REFERENCES users_groups(id),
  `category_` int(12) NOT NULL REFERENCES tiki_categories(categId),
  `group_model` int(11) NOT NULL REFERENCES users_groups(id),
  `category_model` int(12) NOT NULL REFERENCES tiki_categories(categId),
  PRIMARY KEY `gc` (`group_`, `category_`)
) ENGINE=InnoDB COMMENT 'the permissions of group_ on category_ should be the same as those on group_model on category_model and can be made so using copy_perms_grp_cat_grp_cat()';

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
	  SELECT group_, category_, group_model, category_model	FROM users_usergroups;
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

-- after everything is set up:
-- CALL establish_group_category_models();

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
	DECLARE maybe_project TEXT DEFAULT NULLIF( project, '' );
	DECLARE project_cat_name TEXT DEFAULT COALESCE(
		CONCAT(maybe_project, '::', NULLIF(cat_name, '')),
		CONCAT(project, cat_name)
	);
	DECLARE comment_ TEXT DEFAULT COALESCE(concat('for ', maybe_project), '');
	DECLARE model_grp INT DEFAULT group_named(model_grp_name);
	DECLARE model_cat INT DEFAULT category_of_path(model_cat_path);
	DECLARE grp_ INT DEFAULT ensure_groupname_comment(grp_name, comment_);
	DECLARE cat_ INT DEFAULT ensure_categorypath_comment(project_cat_name, comment_);
	CALL add_group_category_models(grp_, cat_, model_grp, model_cat);
END//
DELIMITER ;
-- #+END_SRC

-- We need the model Groups and Categories
-- Their permissions will have to be manually set by an administrator
CALL assert_true( 'ensure_categorypath_comment( \'User\', \'root of user default categories\' )' );
CALL assert_true( 'ensure_categorypath_comment( \'User::Test\', \'root of Model and Test User Default Model categories\' )' );
CALL assert_true( 'ensure_categorypath_comment( \'User::Test::Readable\', \'model for Project_Readers\' )' );
CALL assert_true( 'ensure_categorypath_comment( \'User::Test::Postable\', \'model for Project_Posters\' )' );
CALL assert_true( 'ensure_categorypath_comment( \'User::Test::Editable\', \'model for Project_Editors\' )' );
CALL assert_true( 'ensure_categorypath_comment( \'User::Test::Admin\', \'model for Project_Admins\' )' );

CALL assert_true( 'ensure_groupname_comment( \'Stewards\', \'feature_ngender_stewards participation, default category permissions model\' )' );
CALL assert_true( 'ensure_groupname_comment( \'Project_Readers\', \'permissions model with User::Test::Readable\' )' );
CALL assert_true( 'ensure_groupname_comment( \'Project_Posters\', \'permissions model with User::Test::Postable\' )' );
CALL assert_true( 'ensure_groupname_comment( \'Project_Editors\', \'permissions model with User::Test::Editable\' )' );
CALL assert_true( 'ensure_groupname_comment( \'Project_Admins\', \'permissions model with User::Test::Admin\' )' );

-- We could do with less permissions with
-- - the right inheritance model
-- - more categories assigned to each object
-- Do we want to grant the project admins rights over all project categories or only some?

CALL project_group_category_models('', 'Registered', 'RegisteredReadable', 'Project_Readers', 'Readable');

-- we just unnested these categories to eliminate inheritance
CALL project_group_category_models('SkillsBank', 'SkillsBankObservers', 'ObserverReadable', 'Project_Readers', 'Readable');
CALL project_group_category_models('SkillsBank', 'SkillsBankObservers', 'ObserverPostable', 'Project_Posters', 'Postable');
CALL project_group_category_models('SkillsBank', 'SkillsBankAssociates', 'AssociateReadable', 'Project_Readers', 'Readable');
CALL project_group_category_models('SkillsBank', 'SkillsBankAssociates', 'AssociateEditable', 'Project_Editors', 'Editable');
CALL project_group_category_models('SkillsBank', 'SkillsBankPartners', 'PartnerReadable', 'Project_Readers', 'Readable');
CALL project_group_category_models('SkillsBank', 'SkillsBankPartners', 'PartnerEditable', 'Project_Editors', 'Editable');
-- CALL project_group_category_models('SkillsBank', 'SkillsBankAdmins', 'Admin', 'Project_Admins', 'Admin');

-- we just unnested these categories to eliminate inheritance
CALL project_group_category_models('LOYL', 'LOYL_Observers', 'Observer_Readable', 'Project_Readers', 'Readable');
CALL project_group_category_models('LOYL', 'LOYL_Observers', 'Observer_Postable', 'Project_Posters', 'Postable');
CALL project_group_category_models('LOYL', 'LOYL_Learners', 'Learner_Readable', 'Project_Readers', 'Readable');
CALL project_group_category_models('LOYL', 'LOYL_Learners', 'Learner_Editable', 'Project_Editors', 'Editable');
CALL project_group_category_models('LOYL', 'LOYL_Peers', 'Peer_Readable', 'Project_Readers', 'Readable');
CALL project_group_category_models('LOYL', 'LOYL_Peers', 'Peer_Editable', 'Project_Editors', 'Editable');
CALL project_group_category_models('LOYL', 'LOYL_Partners', 'Partner_Readable', 'Project_Readers', 'Readable');
CALL project_group_category_models('LOYL', 'LOYL_Partners', 'Partner_Editable', 'Project_Editors', 'Editable');
-- CALL project_group_category_models('LOYL', 'LOYL_Admins', 'Admin', 'Project_Admins', 'Admin');

-- Figure out what the inheritance from Public is doing and either get the nesting right or unnest these:

-- CALL project_group_category_models('Public::SomeClues', 'SomeCluesObservers', 'ObserverPostable', 'Project_Posters', 'Postable');
-- CALL project_group_category_models('Public::SomeClues', 'SomeCluesDispensors', 'PartnerWritable', 'Project_Editors', 'Editable');
-- CALL project_group_category_models('Public::SomeClues', 'SomeCluesAdmins', 'Admin', 'Project_Admins', 'Admin');

-- CALL project_group_category_models('Public::Abundance', 'AbundanceObservers', 'ObserverPostable', 'Project_Posters', 'Postable');
-- CALL project_group_category_models('Public::Abundance', 'Abundancers', 'Editable', 'Project_Editors', 'Editable');
-- CALL project_group_category_models('Abundance', 'AbundanceAdmins', 'Admin', 'Project_Admins', 'Admin');

-- CALL project_group_category_models('Public::UncommonKnowledge', 'UncommonKnowledgeObservers', 'ObserverPostable', 'Project_Posters', 'Postable');
-- CALL project_group_category_models('Public::UncommonKnowledge', 'Uncommoners', 'Editable', 'Project_Editors', 'Editable');
-- CALL project_group_category_models('UncommonKnowledge', 'UncommonKnowledgeAdmins', 'Admin', 'Project_Admins', 'Admin');
