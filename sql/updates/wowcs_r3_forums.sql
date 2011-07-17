UPDATE  `wow_db_version` SET  `version` =  '3';

ALTER TABLE  `wow_forum_posts` ADD `deleted` INT( 11 ) NULL DEFAULT NULL;
