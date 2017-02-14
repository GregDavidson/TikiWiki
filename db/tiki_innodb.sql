-- tiki_innodb.sql is run after tiki.sql if InnoDB is being installed
-- Do not include fulltext index definitions. The installer will include these when the current db engine supports fulltext search
-- $Id: tiki_innodb.sql 46632 2013-07-11 18:07:29Z arildb $

-- Force Tiki fulltext search off, when InnoDB is run
insert into tiki_preferences (name, value) values ('feature_search_fulltext', 'n');
