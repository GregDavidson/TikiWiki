#gillesm 2008-10-27
#2008_10_26 GillesM
ALTER TABLE tiki_trackers ADD `groupforAlert` varchar(255) default NULL ;
ALTER TABLE tiki_trackers DROP `groupforAlert`;

