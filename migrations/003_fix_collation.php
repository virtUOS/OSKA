<?php

class FixCollation extends Migration
{
    public function description()
    {
        return 'fix collation for OSKA tables';
    }

    public function up()
    {
        $db = DBManager::get();

        $sql = 'ALTER TABLE oska_mentors
                CHANGE user_id user_id VARCHAR(32) COLLATE latin1_bin NOT NULL,
                CHANGE teacher teacher TINYINT(1) NOT NULL DEFAULT 0';
        $db->exec($sql);

        $sql = 'ALTER TABLE oska_mentees
                CHANGE user_id user_id VARCHAR(32) COLLATE latin1_bin NOT NULL';
        $db->exec($sql);

        $sql = 'ALTER TABLE oska_matches
                CHANGE mentor_id mentor_id VARCHAR(32) COLLATE latin1_bin NOT NULL,
                CHANGE mentee_id mentee_id VARCHAR(32) COLLATE latin1_bin NOT NULL';
        $db->exec($sql);
    }

    public function down()
    {
    }
}
