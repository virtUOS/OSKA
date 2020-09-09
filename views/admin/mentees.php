<h1>Mentees (<?= $mentees_counter?>)</h1>

<form action="<?= $controller->link_for('admin/fach_filter');?>" class="default" method="post">
    <select name="fach_filter">
    <option value="" <? if($fach_filter == null) {echo 'selected';}?>><?=_('kein Filter') ?></option>
    <? foreach ($fächer as $fach): ?>
    <option value="<?= $fach['fach_id'] ?>" <? if($fach_filter == $fach['fach_id']) {echo 'selected';}?>><?= htmlReady($fach['name']); ?></option>
    <? endforeach; ?>
    </select>
    <button type="submit" class="button"><?= _('Filter anwenden')?></button>
</form>

<table class="sortable-table default">
    <colgroup>
        <col width="600">
        <col>
        <col width="60">
        <col width="80">
    </colgroup>
        <thead>
            <tr>
                <th>Nachname, Vorname</th>
                <th>Fach</th>
                <th style="text-align: center">OSKA</th>
                <th style="text-align: right">Aktionen</th>
            </tr>
        </thead>
        <tbody>
        <? foreach ($mentees as $mentee): ?>
            <tr>
                <td><?= htmlReady($mentee['user']->nachname). ', ' . htmlReady($mentee['user']->vorname)?></td>
                <td><?= htmlReady($mentee['fach']) ?></td>
                <td style="text-align: center">
                    <?= ($mentee['has_tutor']) ? Icon::create('accept', 'accept') :Icon::create('decline', 'attention')  ?>
                </td>
                <td class="actions">
                    <? $actionMenu = ActionMenu::get() ?>
                    <? $actionMenu->addLink(
                        URLHelper::getURL('dispatch.php/messages/write', [
                            'filter'          => 'send_sms_to_all',
                            'emailrequest'    => 1,
                            'rec_uname'       => $mentee['user']->username,
                            'default_subject' => _('Problem lösen'),
                        ]),
                        _('Nachricht an Mentee senden'),
                        Icon::create('mail', 'clickable', [
                            'title' => _('Nachricht schreiben'),
                        ]),
                        ['data-dialog' => '']
                    ) ?>
                    <? if ($mentee['has_tutor'] == 0): ?>
                    <? $actionMenu->addLink(
                        $controller->url_for('admin/set_match',[
                            'mentee_id' => $mentee['user']->user_id
                        ]),
                                _('Match manuell setzen'),
                                Icon::create('community', 'clickable', [
                                    'title' => _('Match manuell setzen'),
                                ]),
                                ['data-dialog' => '']
                    ) ?>
                    <? endif;?>
                    <?= $actionMenu->render() ?>
                </td>
            </tr>
        <? endforeach; ?>
        </tbody>
        <? if ($mentees_counter > $entries_per_page) : ?>
        <tfoot>
            <tr>
                <td colspan="6" class="actions">
                    <?= $GLOBALS['template_factory']->render('shared/pagechooser', [
                        'perPage'      => $entries_per_page,
                        'num_postings' => $mentees_counter,
                        'page'         => $page,
                        'pagelink'     => "plugins.php/OSKA/admin/mentees/%s/",
                    ]) ?>
                </td>
            </tr>
        </tfoot>
    <? endif; ?>
</table>