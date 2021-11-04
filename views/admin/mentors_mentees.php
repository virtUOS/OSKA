<h1 class="oska-page-title"><?= htmlReady($title) ?> (<?= htmlReady($subject) ?>)</h1>
<h3><?=_('Mentees:')?></h3>

<table class="sortable-table default">
    <colgroup>
        <col width="600">
        <col width="120">
        <col width="80">
    </colgroup>
        <thead>
            <tr>
                <th><?= _('Nachname, Vorname') ?></th>
                <th><?= _('Fach') ?></th>
                <th style="text-align: right"><?= _('Aktionen') ?></th>
            </tr>
        </thead>
        <tbody>
        <? foreach ($mentees as $mentee): ?>
            <tr>
                <td><?= htmlReady($mentee['nachname']). ', ' . htmlReady($mentee['vorname'])?></td>
                <td><?= htmlReady($mentee['studycourse']) ?></td>
                <td class="actions">
                    <? $actionMenu = ActionMenu::get() ?>
                    <? $actionMenu->addLink(
                        URLHelper::getURL('dispatch.php/messages/write', [
                            'filter'          => 'send_sms_to_all',
                            'emailrequest'    => 1,
                            'rec_uname'       => $mentee['username'],
                            'default_subject' => _(''),
                        ]),
                        _('Nachricht an Mentee senden'),
                        Icon::create('mail', 'clickable', [
                            'title' => _('Nachricht schreiben'),
                        ]),
                        ['data-dialog' => '']
                    ) ?>
                    <?= $actionMenu->render() ?>
                </td>
            </tr>
        <? endforeach; ?>
        </tbody>
</table>
