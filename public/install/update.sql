SET FOREIGN_KEY_CHECKS=0;

ALTER TABLE `ech_games`
  DROP `db_host`,
  DROP `db_user`,
  DROP `db_pw`,
  DROP `db_name`;