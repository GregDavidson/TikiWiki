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
	INSERT INTO `tiki_preferences`(`group_`, `category_`,`group_model`, `category_model`)
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
	COMMENT 'ensure row of table groups_category_model_permissions reflects given arguments -- all args passed as names, not ids!'
BEGIN
	DECLARE project_cat_name TEXT DEFAULT CONCAT(project, cat_name);
	DECLARE maybe_project TEXT DEFAULT NULLIF( project, '' );
	DECLARE comment_ TEXT DEFAULT COALESCE(concat('for ', project), '');
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
CALL assert_true( 'ensure_categorypath_comment( \'User::Test::Read\', \'model for Project_Readers\' )' );
CALL assert_true( 'ensure_categorypath_comment( \'User::Test::Post\', \'model for Project_Posters\' )' );
CALL assert_true( 'ensure_categorypath_comment( \'User::Test::Edit\', \'model for Project_Editors\' )' );
CALL assert_true( 'ensure_categorypath_comment( \'User::Test::Admin\', \'model for Project_Admins\' )' );

CALL assert_true( 'ensure_groupname_comment( \'Stewards\', \'feature_ngender_stewards participation, default category permissions model\' )' );
CALL assert_true( 'ensure_groupname_comment( \'Project_Readers\', \'permissions model with User::Test::Read\' )' );
CALL assert_true( 'ensure_groupname_comment( \'Project_Posters\', \'permissions model with User::Test::Post\' )' );
CALL assert_true( 'ensure_groupname_comment( \'Project_Editors\', \'permissions model with User::Test::Edit\' )' );
CALL assert_true( 'ensure_groupname_comment( \'Project_Admins\', \'permissions model with User::Test::Admin\' )' );

CALL project_group_cat_model_group_cat('SkillsBank', 'SkillsBankObservers', 'ObserverReadable', 'Project_Readers', 'User::Test::Read');
CALL project_group_cat_model_group_cat('SkillsBank', 'SkillsBankObservers', 'ObserverPostable', 'Project_Posters', 'User::Test::Post');
CALL project_group_cat_model_group_cat('SkillsBank', 'SkillsBankAssociates', 'AssociateReadable', 'Project_Readers', 'User::Test::Read');
CALL project_group_cat_model_group_cat('SkillsBank', 'SkillsBankAssociates', 'AssociateWriteable', 'Project_Writers', 'User::Test::Write');
CALL project_group_cat_model_group_cat('SkillsBank', 'SkillsBankPartners', 'PartnerReadable', 'Project_Readers', 'User::Test::Read');
CALL project_group_cat_model_group_cat('SkillsBank', 'SkillsBankPartners', 'PartnerWriteable', 'Project_Writers', 'User::Test::Write');
-- CALL project_group_cat_model_group_cat('SkillsBank', 'SkillsBankAdmins', 'Admin', 'Project_Admins', 'User::Test::Admin');

CALL project_group_cat_model_group_cat('LOYL', 'LOYL_Observers', 'ObserverReadable', 'Project_Readers', 'User::Test::Read');
CALL project_group_cat_model_group_cat('LOYL', 'LOYL_Observers', 'ObserverPostable', 'Project_Posters', 'User::Test::Post');
CALL project_group_cat_model_group_cat('LOYL', 'LOYL_Learners', 'Learner_Readable', 'Project_Readable', 'User::Test::Read');
CALL project_group_cat_model_group_cat('LOYL', 'LOYL_Learners', 'Learner_Writeable', 'Project_Writeable', 'User::Test::Write');
CALL project_group_cat_model_group_cat('LOYL', 'LOYL_Peers', 'Peer_Readable', 'Project_Readers', 'User::Test::Read');
CALL project_group_cat_model_group_cat('LOYL', 'LOYL_Peers', 'Peer_Writeable', 'Project_Writers', 'User::Test::Edit');
CALL project_group_cat_model_group_cat('LOYL', 'LOYL_Partners', 'Partner_Readable', 'Project_Readers', 'User::Test::Read');
CALL project_group_cat_model_group_cat('LOYL', 'LOYL_Partners', 'Partner_Writeable', 'Project_Writers', 'User::Test::Edit');
-- CALL project_group_cat_model_group_cat('LOYL', 'LOYL_Admins', 'Admin', 'Project_Admins', 'User::Test::Admin');

CALL project_group_cat_model_group_cat('SomeClues', 'SomeCluesObservers', 'ObserverPostable', 'Project_Posters', 'User::Test::Post');
CALL project_group_cat_model_group_cat('SomeClues', 'SomeCluesDispensors', 'PartnerWritable', 'Project_Writers', 'User::Test::Edit');

CALL project_group_cat_model_group_cat('Abundance', 'AbundanceObservers', 'ObserverPostable', 'Project_Posters', 'User::Test::Post');
CALL project_group_cat_model_group_cat('Abundance', 'Abundancers', 'Writeable', 'Project_Writers', 'User::Test::Edit');

CALL project_group_cat_model_group_cat('UncommonKnowledge', 'UncommonKnowledgeObservers', 'ObserverPostable', 'Project_Posters', 'User::Test::Post');
CALL project_group_cat_model_group_cat('UncommonKnowledge', 'Uncommoners', 'Writeable', 'Project_Writers', 'User::Test::Edit');
