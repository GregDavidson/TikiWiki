-- after everything is set up:
-- CALL establish_group_category_models();

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