<? $gender = array(_('egal'), _('männlich'), _('weiblich'), _('divers')); ?>
<form action="<?= $controller->link_for('admin/store_match');?>" class="default" method="post">
<table class="default">
    <colgroup>
        <col width="60">
        <col>
        <col>
        <col>
        <col width="80">
        <col width="80">
        <col width="80">
        <col width="80">
        <col width="80">
        <col width="80">
    </colgroup>
        <thead>
            <tr>
                <th></th>
                <th><?= _('Mentee')?></th>
                <th><?= _('Fach')?></th>
                <th><?= _('Fach-Präferenz')?></th>
                <th style="text-align: center"><?= _('Lehramt') ?></th>
                <th style="text-align: center"><?= _('Geschlecht') ?></th>
                <th style="text-align: center"><?= _('Migrationshintergrund') ?></th>
                <th style="text-align: center"><?= _('Kinder') ?></th>
                <th style="text-align: center"><?= _('First Generation') ?></th>
                <th style="text-align: center"><?= _('abgeschlossene Ausbildung') ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <?= $mentee_avatar->getImageTag(Avatar::SMALL) ?>
                </td>
                <td>
                    <?= htmlReady($mentee_user->vorname) . ' ' . htmlReady($mentee_user->nachname);?>
                    <input type="hidden" name="mentee_id" value="<?= $mentee_user->user_id?>" />
                </td>
                <td>
                    <?= htmlReady($mentee_studycourses); ?>
                </td>
                <td>
                    <?= htmlReady($mentee_studycourse_preference); ?>
                </td>
                <td style="text-align: center">
                <?= ($mentee->teacher == 1) ? Icon::create('accept', 'accept') :Icon::create('decline', 'attention')  ?>
                </td>
                <td style="text-align: center">
                    <?= $gender[$mentee_preferences->gender]?>
                </td>
                <td style="text-align: center">
                    <?= ($mentee_preferences->migration == 1) ? Icon::create('accept', 'accept') :Icon::create('decline', 'attention')?>
                </td>
                <td style="text-align: center">
                    <?= ($mentee_preferences->children == 1) ? Icon::create('accept', 'accept') :Icon::create('decline', 'attention')?>
                </td>
                <td style="text-align: center">
                    <?= ($mentee_preferences->first_gen == 1) ? Icon::create('accept', 'accept') :Icon::create('decline', 'attention')?>
                </td>
                <td style="text-align: center">
                    <?= ($mentee_preferences->apprentice == 1) ? Icon::create('accept', 'accept') :Icon::create('decline', 'attention')?>
                </td>
            </tr>
        </tbody>
</table>



<table class="default">
    <colgroup>
        <col width="60">
        <col>
        <col>
        <col width="80">
        <col width="80">
        <col width="80">
        <col width="80">
        <col width="80">
        <col width="80">
    </colgroup>
        <thead>
            <tr>
                <th></th>
                <th><?= _('OSKA')?></th>
                <th><?= _('Fach')?></th>
                <th style="text-align: center"><?= _('Lehramt') ?></th>
                <th style="text-align: center"><?= _('Geschlecht') ?></th>
                <th style="text-align: center"><?= _('Migrationshintergrund') ?></th>
                <th style="text-align: center"><?= _('Kinder') ?></th>
                <th style="text-align: center"><?= _('First Generation') ?></th>
                <th style="text-align: center"><?= _('abgeschlossene Ausbildung') ?></th>
            </tr>
        </thead>
        <tbody>
            <? foreach($mentors as $mentor): ?>
                <tr>
                    <td>
                        <input type="radio" name="mentor_id" value="<?= $mentor->user->user_id ?>" />
                    </td>
                    <td>
                        <?= htmlReady($mentor->user->vorname) . ' ' . htmlReady($mentor->user->nachname);?>
                    </td>
                    <td>
                        <?= htmlReady($mentor->studycourses); ?>
                    </td>
                    <td style="text-align: center">
                    <?= ($mentor->teacher == 1) ? Icon::create('accept', 'accept') :Icon::create('decline', 'attention')  ?>
                    </td>
                    <td style="text-align: center">
                        <?= $gender[$mentor->user->geschlecht]?>
                    </td>
                    <td style="text-align: center">
                        <?= ($mentor->abilities->migrant == 1) ? Icon::create('accept', 'accept') :Icon::create('decline', 'attention')?>
                    </td>
                    <td style="text-align: center">
                        <?= ($mentor->abilities->children == 1) ? Icon::create('accept', 'accept') :Icon::create('decline', 'attention')?>
                    </td>
                    <td style="text-align: center">
                        <?= ($mentor->abilities->first_gen == 1) ? Icon::create('accept', 'accept') :Icon::create('decline', 'attention')?>
                    </td>
                    <td style="text-align: center">
                        <?= ($mentor->abilities->apprentice == 1) ? Icon::create('accept', 'accept') :Icon::create('decline', 'attention')?>
                    </td>
                </tr>
            <? endforeach; ?>
        </tbody>
</table>
<footer data-dialog-button>
    <button class="button accept">speichern</button>
</footer>
</form>

