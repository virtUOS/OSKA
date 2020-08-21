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
          `abilities` JSON NOT NULL,
          `mentee_counter` TINYINT(1) NOT NULL DEFAULT 0 ,
          `description` TEXT NOT NULL COLLATE utf8mb4_unicode_ci,
          `mkdate` DATETIME NOT NULL DEFAULT 0,
          `chdate` DATETIME ON UPDATE CURRENT_TIMESTAMP NOT NULL
        )');

        $db->exec('CREATE TABLE IF NOT EXISTS `oska_mentees` (
          `user_id` VARCHAR(32) NOT NULL PRIMARY KEY,
          `teacher` BOOLEAN NOT NULL DEFAULT FALSE,
          `preferences` JSON NOT NULL,
          `has_tutor` BOOLEAN NOT NULL DEFAULT FALSE ,
          `mkdate` DATETIME NOT NULL DEFAULT 0,
          `chdate` DATETIME ON UPDATE CURRENT_TIMESTAMP NOT NULL
        )');

        $db->exec('CREATE TABLE IF NOT EXISTS `oska_matches` (
          `mentor_id` VARCHAR(32) NOT NULL,
          `mentee_id` VARCHAR(32) NOT NULL,
          `issue` BOOLEAN NOT NULL DEFAULT FALSE,
          `mkdate` DATETIME DEFAULT 0 NOT NULL,
          `chdate` DATETIME ON UPDATE CURRENT_TIMESTAMP NOT NULL,
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