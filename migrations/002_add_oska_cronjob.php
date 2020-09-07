<?php

require_once 'public/plugins_packages/virtUOS/OSKA/OskaCronjob.php';

class AddOskaCronjob extends Migration
{

    public function description()
    {
        return 'add mentor-mentee-matching cronjob for plugin OSKA';
    }

    public function up()
    {
        OskaCronjob::register()->schedulePeriodic(-30)->activate();
    }

    public function down()
    {
        OskaCronjob::unregister();
    }
}
