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

-- Before these Groups and Categories can be used as Models
-- the Tiki Admin must set them up so that
-- Group + Category => Permissions as follows:
-- Project_Readers + User::Test::Readable => Read
-- Project_Posters + User::Test::Postable => Read, Post, Comment
-- Project_Editors + User::Test::Editable => Read, Write, Post, Comment
-- Project_Admins + User::Test::Admin => admin permissions

-- ** Group Registered => Category Registered

-- Group Registered (all logged in Users) have Read permission on Objects in Category RegisteredReadable

CALL project_group_category_models('RegisteredReadable', 'Registered!', 'RegisteredReadable!', 'Project_Readers', 'Readable');

-- ** Project NGender

-- Most NGender pages will be explicitly marked with category public
-- NGenderPartners and NGenderAdmins

-- CALL project_group_models__('NGender', 'Partners', 'Project_Editors', 'Editable');
-- CALL project_group_category_models__('NGender', 'Partners', 'NGender!', 'Project_Editors', 'Editable');
CALL project_group_models('NGender', 'Partners', 'Project_Editors', 'Editable');
CALL project_group_models('NGender', 'Admins', 'Project_Admins', 'Admin');

-- ** Project Abundance

-- Anonymous (Public) -> Registered -> Abundancers and AbundanceAdmins
-- Observers is available in case we ever change Registered to Readable

CALL project_group_models('Abundance', 'Anonymous!', 'Project_Readers', 'Readable');
CALL project_group_models('Abundance', 'Registered!', 'Project_Posters', 'Postable');
CALL project_group_models('Abundance', 'Observers', 'Project_Posters', 'Postable');
CALL project_group_models('Abundance', 'Abundancers!', 'Project_Editors', 'Editable');
CALL project_group_models('Abundance', 'Admins', 'Project_Admins', 'Admin');

-- ** Project Someclues

-- Registered -> SomeCluesObservers -> SomeCluesDispensors and SomeCluesAdmins

CALL project_group_models('SomeClues', 'Registered!', 'Project_Readers', 'Readable');
CALL project_group_models('SomeClues', 'Observers', 'Project_Posters', 'Postable');
CALL project_group_models('SomeClues', 'Dispensors', 'Project_Editors', 'Editable');
CALL project_group_models('SomeClues', 'Admins', 'Project_Admins', 'Admin');

-- ** Project UncommonKnowledge

-- Registered -> Uncommoners and UncommonKnowledgeAdmins
-- Observers is available in case we ever change Registered to Readable

CALL project_group_models('UncommonKnowledge', 'Registered!', 'Project_Posters', 'Postable');
CALL project_group_models('UncommonKnowledge', 'Observers', 'Project_Posters', 'Postable');
CALL project_group_models('UncommonKnowledge', 'Uncommoners!', 'Project_Editors', 'Editable');
CALL project_group_models('UncommonKnowledge', 'Admins', 'Project_Admins', 'Admin');

-- ** Project SkillsBank

-- SkillsBankObservers -> SkillsBankAssociates -> SkillsBankPartners and SkillsBankAdmins

CALL project_group_models('SkillsBank', 'Observers', 'Project_Posters', 'Postable');
CALL project_group_models('SkillsBank', 'Associates', 'Project_Posters', 'Postable');
CALL project_group_models('SkillsBank', 'Partners', 'Project_Editors', 'Editable');
CALL project_group_models('SkillsBank', 'Admins', 'Project_Admins', 'Admin');
CALL project_group_category_models('SkillsBank', 'Associates', 'Associate', 'Project_Editors', 'Editable');

-- ** Project LOYL

-- Registered -> LOYL_Observers -> LOYL_Learners -> LOYL_Peers -> LOYL_Partners and LOYL_Admins

CALL project_group_models('LOYL', 'Registered!', 'Project_Readers', 'Readable');
CALL project_group_models('LOYL', 'Observers', 'Project_Posters', 'Postable');
CALL project_group_models('LOYL', 'Learners', 'Project_Posters', 'Postable');
CALL project_group_models('LOYL', 'Peers', 'Project_Posters', 'Postable');
CALL project_group_models('LOYL', 'Admins', 'Project_Admins', 'Admin');
-- CALL project_group_category_models__('LOYL', 'Learners', 'Learner', 'Project_Editors', 'Editable');
CALL project_group_category_models('LOYL', 'Learners', 'Learner', 'Project_Editors', 'Editable');
CALL project_group_category_models('LOYL', 'Peers', 'Peer', 'Project_Editors', 'Editable');

-- ** Project RPTUG

-- Registered -> RPTUG_Associates -> RPTUG_Partners and RPTUG_Admins
-- Most PRTUG pages will be explicitly marked with category public

-- Do we really want all registered users to be able to Post??
-- We could allow them to read and create RPTUG observers
CALL project_group_models('RPTUG', 'Registered!', 'Project_Posters', 'Postable');
CALL project_group_models('RPTUG', 'Observers', 'Project_Posters', 'Postable');
CALL project_group_models('RPTUG', 'Associates', 'Project_Posters', 'Postable');
CALL project_group_models('RPTUG', 'Partners', 'Project_Editors', 'Editable');
CALL project_group_models('RPTUG', 'Admins', 'Project_Admins', 'Admin');
-- CALL project_group_category_models__('RPTUG', 'Associates', 'Associate', 'Project_Editors', 'Editable');
CALL project_group_category_models('RPTUG', 'Associates', 'Associate', 'Project_Editors', 'Editable');

-- ** Project LTHL

CALL project_group_models('LTHL', 'Associates', 'Project_Posters', 'Postable');
CALL project_group_models('LTHL', 'Partners', 'Project_Editors', 'Editable');
CALL project_group_models('LTHL', 'Admins', 'Project_Admins', 'Admin');
CALL project_group_category_models('LTHL', 'Associates', 'Associate', 'Project_Editors', 'Editable');


CALL project_group_category_models('RPTUG', 'Associates', 'Associate', 'Project_Editors', 'Editable');

-- ** Project DesignSpace

-- Registered -> DesignSpaceObservers -> DesignSpacePartners and DesignSpaceAdmins

CALL project_group_models('DesignSpace', 'Registered!', 'Project_Posters', 'Postable');
CALL project_group_models('DesignSpace', 'Observers', 'Project_Posters', 'Postable');
CALL project_group_models('DesignSpace', 'Partners', 'Project_Editors', 'Editable');
CALL project_group_models('DesignSpace', 'Admins', 'Project_Admins', 'Admin');

-- ** Project LTHL

-- LTHL_Observers -> LTHL_Partners and LTHL_Admins

CALL project_group_models('LTHL', 'Observers', 'Project_Posters', 'Postable');
CALL project_group_models('LTHL', 'Partners', 'Project_Editors', 'Editable');
CALL project_group_models('LTHL', 'Admins', 'Project_Admins', 'Admin');

-- ** Personal Observers

-- Some folks may want to empower more than one other to be able to
-- easily observe and comment on a lot of what they do.
-- This is a User-Specific project, so the Group and Category should be
-- under the User's Default Group and Default Category, respectively!

-- CALL project_group_category_models__('User::Greg', 'User_Greg_RFC!', 'User::Greg::RFC', 'Project_Posters', 'Postable');
CALL project_group_category_models('User::Greg', 'User_Greg_RFC!', 'User::Greg::RFC', 'Project_Posters', 'Postable');

-- * Instructions and MIscellaneous

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
-- Pair up your the Group/Category pairs for your projects with their models
-- using project_group_category_models as above.

-- SELECT * FROM group_category_models_view ORDER BY target_category, target_group;

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

-- ** Cleanup Obsolete Groups and Categories

-- After everything current is in group_category_models
-- delete all groups and categories from the Tiki and from old_groups_and_categories
-- that are in old_groups_and_categories and are NOT in group_category_models
