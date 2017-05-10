DROP TABLE IF EXISTS `groups_category_model_permissions`;
CREATE TABLE `groups_category_model_permissions` (
  `group_` int(11) NOT NULL REFERENCES users_groups(id),
  `category_` int(12) NOT NULL REFERENCES tiki_categories(categId),
  `group_model` int(11) NOT NULL REFERENCES users_groups(id),
  `category_model` int(12) NOT NULL REFERENCES tiki_categories(categId),
  PRIMARY KEY `gc` (`group_`, `category_`)
) ENGINE=InnoDB COMMENT 'the permissions of group_ on category_ should be the same as those on group_model on category_model and can be made so using copy_perms_grp_cat_grp_cat()';

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `add_group_cat_model_group_cat`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `add_group_cat_model_group_cat`(grp_ INT, cat_ INT, model_grp INT, model_cat INT)
  READS SQL DATA MODIFIES SQL DATA
	COMMENT 'ensure row of table groups_category_model_permissions reflects given arguments'
BEGIN
	INSERT INTO `groups_category_model_permissions`(`group_`, `category_`,`group_model`, `category_model`)
 	VALUES (grp_, cat_, model_grp, model_cat)
	ON DUPLICATE KEY UPDATE `group_model` = VALUES(`group_model`),  `category_model` = VALUES(`category_model`);
END//
DELIMITER ;
-- #+END_SRC

-- #+BEGIN_SRC sql
DROP PROCEDURE IF EXISTS `project_group_cat_model_group_cat`;
DELIMITER //
CREATE DEFINER=`phpmyadmin`@`localhost`
PROCEDURE `project_group_cat_model_group_cat`(
	project TEXT, grp_name TEXT, cat_name TEXT, model_grp_name TEXT, model_cat_name TEXT
) READS SQL DATA MODIFIES SQL DATA
	COMMENT 'ensure row of table groups_category_model_permissions; all args passed as names, not ids; project is context for cat_name'
BEGIN
	DECLARE maybe_project TEXT DEFAULT NULLIF( project, '' );
	DECLARE project_cat_name TEXT DEFAULT COALESCE(
		CONCAT(maybe_project, '::', NULLIF(cat_name, '')),
		CONCAT(project, cat_name)
	);
	DECLARE comment_ TEXT DEFAULT COALESCE(concat('for ', maybe_project), '');
	DECLARE model_grp INT DEFAULT group_named(model_grp_name);
	DECLARE model_cat INT DEFAULT category_of_path(model_cat_name);
	DECLARE grp_ INT DEFAULT ensure_groupname_comment(grp_name, comment_);
	DECLARE cat_ INT DEFAULT ensure_categorypath_comment(project_cat_name, comment_);
	CALL add_group_cat_model_group_cat(grp_, cat_, model_grp, model_cat);
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

CALL project_group_cat_model_group_cat('', 'Registered', 'RegisteredReadable', 'Project_Readers', 'User::Test::Readable');

-- we just unnested these categories to eliminate inheritance
CALL project_group_cat_model_group_cat('SkillsBank', 'SkillsBankObservers', 'ObserverReadable', 'Project_Readers', 'User::Test::Readable');
CALL project_group_cat_model_group_cat('SkillsBank', 'SkillsBankObservers', 'ObserverPostable', 'Project_Posters', 'User::Test::Postable');
CALL project_group_cat_model_group_cat('SkillsBank', 'SkillsBankAssociates', 'AssociateReadable', 'Project_Readers', 'User::Test::Readable');
CALL project_group_cat_model_group_cat('SkillsBank', 'SkillsBankAssociates', 'AssociateEditable', 'Project_Editors', 'User::Test::Editable');
CALL project_group_cat_model_group_cat('SkillsBank', 'SkillsBankPartners', 'PartnerReadable', 'Project_Readers', 'User::Test::Readable');
CALL project_group_cat_model_group_cat('SkillsBank', 'SkillsBankPartners', 'PartnerEditable', 'Project_Editors', 'User::Test::Editable');
-- CALL project_group_cat_model_group_cat('SkillsBank', 'SkillsBankAdmins', 'Admin', 'Project_Admins', 'User::Test::Admin');

-- we just unnested these categories to eliminate inheritance
CALL project_group_cat_model_group_cat('LOYL', 'LOYL_Observers', 'Observer_Readable', 'Project_Readers', 'User::Test::Readable');
CALL project_group_cat_model_group_cat('LOYL', 'LOYL_Observers', 'Observer_Postable', 'Project_Posters', 'User::Test::Postable');
CALL project_group_cat_model_group_cat('LOYL', 'LOYL_Learners', 'Learner_Readable', 'Project_Readers', 'User::Test::Readable');
CALL project_group_cat_model_group_cat('LOYL', 'LOYL_Learners', 'Learner_Editable', 'Project_Editors', 'User::Test::Editable');
CALL project_group_cat_model_group_cat('LOYL', 'LOYL_Peers', 'Peer_Readable', 'Project_Readers', 'User::Test::Readable');
CALL project_group_cat_model_group_cat('LOYL', 'LOYL_Peers', 'Peer_Editable', 'Project_Editors', 'User::Test::Editable');
CALL project_group_cat_model_group_cat('LOYL', 'LOYL_Partners', 'Partner_Readable', 'Project_Readers', 'User::Test::Readable');
CALL project_group_cat_model_group_cat('LOYL', 'LOYL_Partners', 'Partner_Editable', 'Project_Editors', 'User::Test::Editable');
-- CALL project_group_cat_model_group_cat('LOYL', 'LOYL_Admins', 'Admin', 'Project_Admins', 'User::Test::Admin');

-- Figure out what the inheritance from Public is doing and either get the nesting right or unnest these:

-- CALL project_group_cat_model_group_cat('Public::SomeClues', 'SomeCluesObservers', 'ObserverPostable', 'Project_Posters', 'User::Test::Postable');
-- CALL project_group_cat_model_group_cat('Public::SomeClues', 'SomeCluesDispensors', 'PartnerWritable', 'Project_Editors', 'User::Test::Editable');
-- CALL project_group_cat_model_group_cat('Public::SomeClues', 'SomeCluesAdmins', 'Admin', 'Project_Admins', 'User::Test::Admin');

-- CALL project_group_cat_model_group_cat('Public::Abundance', 'AbundanceObservers', 'ObserverPostable', 'Project_Posters', 'User::Test::Postable');
-- CALL project_group_cat_model_group_cat('Public::Abundance', 'Abundancers', 'Editable', 'Project_Editors', 'User::Test::Editable');
-- CALL project_group_cat_model_group_cat('Abundance', 'AbundanceAdmins', 'Admin', 'Project_Admins', 'User::Test::Admin');

-- CALL project_group_cat_model_group_cat('Public::UncommonKnowledge', 'UncommonKnowledgeObservers', 'ObserverPostable', 'Project_Posters', 'User::Test::Postable');
-- CALL project_group_cat_model_group_cat('Public::UncommonKnowledge', 'Uncommoners', 'Editable', 'Project_Editors', 'User::Test::Editable');
-- CALL project_group_cat_model_group_cat('UncommonKnowledge', 'UncommonKnowledgeAdmins', 'Admin', 'Project_Admins', 'User::Test::Admin');
