<?php

class AddTables extends Migration
{
    public function description()
    {
        return 'add tables for plugin OSKA';
    }

    public function up()
    {
        $db = DBManager::get();

        $db->exec('CREATE TABLE IF NOT EXISTS `oska_mentors` (
          `user_id` VARCHAR(32) NOT NULL PRIMARY KEY,
          `teacher` BOOLEAN DEFAULT FALSE,
          `abilities` TEXT NOT NULL,
          `mentee_counter` TINYINT(1) NOT NULL DEFAULT 0 ,
          `description` TEXT NOT NULL COLLATE utf8mb4_unicode_ci,
          `mkdate` INT(11) NOT NULL DEFAULT 0,
          `chdate` INT(11) NOT NULL DEFAULT 0
        )');

        $db->exec('CREATE TABLE IF NOT EXISTS `oska_mentees` (
          `user_id` VARCHAR(32) NOT NULL PRIMARY KEY,
          `teacher` BOOLEAN NOT NULL DEFAULT FALSE,
          `preferences` TEXT NOT NULL,
          `has_tutor` BOOLEAN NOT NULL DEFAULT FALSE ,
          `mkdate` INT(11) NOT NULL DEFAULT 0,
          `chdate` INT(11) NOT NULL DEFAULT 0
        )');

        $db->exec('CREATE TABLE IF NOT EXISTS `oska_matches` (
          `mentor_id` VARCHAR(32) NOT NULL,
          `mentee_id` VARCHAR(32) NOT NULL,
          `issue` BOOLEAN NOT NULL DEFAULT FALSE,
          `mkdate` INT(11) NOT NULL DEFAULT 0,
          `chdate` INT(11) NOT NULL DEFAULT 0,
          PRIMARY KEY (`mentor_id`, `mentee_id`)
        )');

        SimpleORMap::expireTableScheme();
    }

    public function down()
    {
        $db = DBManager::get();

        foreach ([
            'oska_mentors', 'oska_mentees', 'oska_matches'
        ] as $table) {
            $db->exec(sprintf('DROP TABLE IF EXISTS `%s`', $table));
        }

        SimpleORMap::expireTableScheme();
    }
}
