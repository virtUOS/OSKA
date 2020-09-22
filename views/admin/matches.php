<h1 class="oska-page-title"><?= htmlready($title)?></h1>

<table class="sortable-table default">
    <colgroup>
        <col>
        <col>
        <col width="80">
    </colgroup>
        <thead>
            <tr>
                <th data-sort="text"><?= _('Mentor (Nachname, Vorname)') ?></th>
                <th data-sort="text"><?= _('Mentee (Nachname, Vorname)') ?></th>
                <th style="text-align: right"><?= _('Aktionen') ?></th>
            </tr>
        </thead>
        <? foreach ($matches as $match): ?>
            <tr>
                <td><?= htmlReady($match['mentor']->nachname). ', ' . htmlReady($match['mentor']->vorname)?></td>
                <td><?= htmlReady($match['mentee']->nachname). ', ' . htmlReady($match['mentee']->vorname)?></td>
                <td class="actions">
                <? $actionMenu = ActionMenu::get() ?>
                    <? $actionMenu->addLink(
                                    URLHelper::getURL('dispatch.php/messages/write', [
                                        'filter'           => 'send_sms_to_all',
                                        'emailrequest'    => 1,
                                        'rec_uname'       => $match['mentor']->username,
                                        'default_subject' => _('Problem lösen'),
                                    ]),
                                    _('Nachricht an Mentor senden'),
                                    Icon::create('mail', 'clickable', [
                                        'title' => _('Nachricht an Mentor schreiben'),
                                    ]),
                                    ['data-dialog' => '']
                    ) ?>
                    <? $actionMenu->addLink(
                                    URLHelper::getURL('dispatch.php/messages/write', [
                                        'filter'           => 'send_sms_to_all',
                                        'emailrequest'    => 1,
                                        'rec_uname'       => $match['mentee']->username,
                                        'default_subject' => _('Problem lösen'),
                                    ]),
                                    _('Nachricht an Mentee senden'),
                                    Icon::create('mail', 'clickable', [
                                        'title' => _('Nachricht an Mentee schreiben'),
                                    ]),
                                    ['data-dialog' => '']
                    ) ?>
                    <?= $actionMenu->render() ?>
                </td>
            </tr>
        <? endforeach; ?>
</table>
