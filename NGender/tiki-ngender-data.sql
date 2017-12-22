-- SQL Code for managing the NGender.org Tiki
-- Author: J. Greg Davidson, 2017
-- All rights reserved

-- Support for wrangling Tiki Category permissions
-- Depends on
-- - tiki-ngender.sql
-- - tiki-ngender-schema.sql

-- See the commented out code at the bottom to effect the Tiki tables!

-- We need the model Groups and Categories
-- Their permissions will have to be manually set by an administrator

CALL assert_categorypath_comment( 'User', 'root of user default categories' );
CALL assert_categorypath_comment( 'User::Test', 'root of Model and Test User Default Model categories' );
CALL assert_categorypath_comment( 'User::Test::Readable', 'model for Project_Readers' );
CALL assert_categorypath_comment( 'User::Test::Postable', 'model for Project_Posters' );
CALL assert_categorypath_comment( 'User::Test::Editable', 'model for Project_Editors' );
CALL assert_categorypath_comment( 'User::Test::Admin', 'model for Project_Admins' );

CALL assert_groupname_comment( 'Stewards', 'feature_ngender_stewards participation, default category permissions model' );
CALL assert_groupname_comment( 'Project_Readers', 'permissions model with User::Test::Readable' );
CALL assert_groupname_comment( 'Project_Posters', 'permissions model with User::Test::Postable' );
CALL assert_groupname_comment( 'Project_Editors', 'permissions model with User::Test::Editable' );
CALL assert_groupname_comment( 'Project_Admins', 'permissions model with User::Test::Admin' );

-- We could do with less permissions with
-- - the right inheritance model
-- - more categories assigned to each object
-- Do we want to grant the project admins rights over all project categories or only some?

-- project_group_category_models(project_path, group_name, category_name, model_group_name, model_category_name)
-- will prefix group_name with tail of project_path unless group_name ends with ! (which will be removed)
-- will suffix category_name with model_category_name unless category_name ends with ! (which will be removed)
-- will prefix category_name with project_name unless category_name contains a :: substring

CALL project_group_category_models('', 'Registered', 'Registered', 'Project_Readers', 'Readable');

-- How do we want Project_Admins to work?
-- We shouldn't have a PROJECT::Admin category within a given PROJECT
-- PROJECT_Admins should generally have Project_Admins/Admin permissions on all PROJECT categories
-- If all PROJECT categories are children of PROJECT_PARENT_CATEGORY, how can this best be done?
-- Possible solution:
-- -  Have all PROJECT categories inherit from PROJECT_PARENT_CATEGORY
-- - Have PROJECT_Admins be the ONLY group with any permissions on PROJECT_PARENT_CATEGORY!
-- - Super-secure project categories can simply block inheritance!

-- we just unnested these categories to eliminate inheritance
CALL project_group_category_models('SkillsBank', 'Observers', 'Observer', 'Project_Readers', 'Readable');
CALL project_group_category_models('SkillsBank', 'Observers', 'Observer', 'Project_Posters', 'Postable');
CALL project_group_category_models('SkillsBank', 'Associates', 'Associate', 'Project_Readers', 'Readable');
CALL project_group_category_models('SkillsBank', 'Associates', 'Associate', 'Project_Editors', 'Editable');
CALL project_group_category_models('SkillsBank', 'Partners', 'Partner', 'Project_Readers', 'Readable');
CALL project_group_category_models('SkillsBank', 'Partners', 'Partner', 'Project_Editors', 'Editable');
CALL project_group_category_models('', 'SkillsBankAdmins', 'SkillsBank!', 'Project_Admins', 'Admin');

-- Rename LearnerReadable to LearnerPostable??

-- we just unnested these categories to eliminate inheritance
CALL project_group_category_models('LOYL', '_Observers', 'Observer_', 'Project_Readers', 'Readable');
CALL project_group_category_models('LOYL', '_Observers', 'Observer_', 'Project_Posters', 'Postable');
CALL project_group_category_models('LOYL', '_Learners', 'Learner_', 'Project_Readers', 'Readable');
CALL project_group_category_models('LOYL', '_Learners', 'Learner_', 'Project_Editors', 'Editable');
CALL project_group_category_models('LOYL', '_Peers', 'Peer_', 'Project_Readers', 'Readable');
CALL project_group_category_models('LOYL', '_Peers', 'Peer_', 'Project_Editors', 'Editable');
CALL project_group_category_models('LOYL', '_Partners', 'Partner_', 'Project_Readers', 'Readable');
CALL project_group_category_models('LOYL', '_Partners', 'Partner_', 'Project_Editors', 'Editable');
CALL project_group_category_models('', 'LOYL_Admins', 'LOYL!', 'Project_Admins', 'Admin');

CALL project_group_category_models('SomeClues', 'Observers', 'Observer', 'Project_Posters', 'Postable');
CALL project_group_category_models('SomeClues', 'Dispensors', 'Partner', 'Project_Editors', 'Editable');
CALL project_group_category_models('', 'SomeCluesAdmins', 'SomeClues!', 'Project_Admins', 'Admin');

CALL project_group_category_models('Abundance', 'Observers', 'Observer', 'Project_Posters', 'Postable');
CALL project_group_category_models('Abundance', 'Abundancers!', '', 'Project_Editors', 'Editable');
CALL project_group_category_models('', 'AbundanceAdmins', 'Abundance!', 'Project_Admins', 'Admin');

CALL project_group_category_models('UncommonKnowledge', 'Observers', 'Observer', 'Project_Posters', 'Postable');
CALL project_group_category_models('UncommonKnowledge', 'Uncommoners!', '', 'Project_Editors', 'Editable');
CALL project_group_category_models('', 'UncommonKnowledgeAdmins', 'UncommonKnowledge!', 'Project_Admins', 'Admin');

-- -- SELECT * FROM group_category_models_view;

-- Put everyone intended to be stewards into group Stewards
-- If it's all or nearly all users, then:
-- -- CALL add_everyone_to_group_stewards();
-- remove inappropriate users from group Stewards

-- -- CALL make_stewards_be_stewards();
-- Rerun after adding new Stewards users
-- If you remove a user from group Stewards you might want to also
-- - Remove their Default Group
-- - Remove their Default Category

-- -- CALL let_stewards_view_categories();
-- Rerun after adding new categories!!

-- Ensure the desired permissions between the model Group/Category pairs
-- - This must be done manually!
-- Pair up your the Gruop/Category pairs for your projects with their models
-- using project_group_category_models as above.

-- -- CALL establish_group_category_models();

-- You can also do this manually through the Category features:
-- -- CALL feature_ngender_stewards(true);

-- After manual work, these may help:
-- CALL copy_perms_grp_cat_grp_cat(group_named('Project_Readers'), category_of_path('User::Test::Readable'),group_named('Project_Posters'), category_of_path('User::Test::Postable'));
-- CALL copy_perms_grp_cat_grp_cat(group_named('Project_Posters'), category_of_path('User::Test::Postable'), group_named('Project_Editors'), category_of_path('User::Test::Editable'));

-- CALL perms_grp_cat(group_named('Project_Readers'), category_of_path('User::Test::Readable')); -- 14 rows
-- CALL perms_grp_cat(group_named('Project_Posters'), category_of_path('User::Test::Postable')); -- 14 rows
-- CALL perms_grp_cat(group_named('Project_Editors'), category_of_path('User::Test::Editable'));; -- 14 rows
-- CALL perms_grp_cat(group_named('Project_Admins'), category_of_path('User::Test::Admin')); -- 70 rows

CALL project_group_category_models('RPTUG', 'Observers', 'Observer', 'Project_Readers', 'Readable');
CALL project_group_category_models('RPTUG', 'Observers', 'Observer', 'Project_Posters', 'Postable');
CALL project_group_category_models('RPTUG', 'Associates', 'Associate', 'Project_Readers', 'Readable');
CALL project_group_category_models('RPTUG', 'Associates', 'Associate', 'Project_Editors', 'Editable');
CALL project_group_category_models('RPTUG', 'Partners', 'Partner', 'Project_Readers', 'Readable');
CALL project_group_category_models('RPTUG', 'Partners', 'Partner', 'Project_Editors', 'Editable');
CALL project_group_category_models('', 'RPTUGAdmins', 'RPTUG!', 'Project_Admins', 'Admin');

CALL project_group_category_models('DesignSpace', 'Observers', 'Observer', 'Project_Readers', 'Readable');
CALL project_group_category_models('DesignSpace', 'Observers', 'Observer', 'Project_Posters', 'Postable');
CALL project_group_category_models('DesignSpace', 'Associates', 'Associate', 'Project_Readers', 'Readable');
CALL project_group_category_models('DesignSpace', 'Associates', 'Associate', 'Project_Editors', 'Editable');
CALL project_group_category_models('DesignSpace', 'Partners', 'Partner', 'Project_Readers', 'Readable');
CALL project_group_category_models('DesignSpace', 'Partners', 'Partner', 'Project_Editors', 'Editable');
CALL project_group_category_models('', 'DesignSpaceAdmins', 'DesignSpace!', 'Project_Admins', 'Admin');
0
