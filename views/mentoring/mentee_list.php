<h1><?= $title ?></h1>
<table class="sortable-table default">
    <colgroup>
        <col width="20">
        <col>
        <col>
        <col width="80">
    </colgroup>
        <thead>
            <tr>
                <th></th>
                <th data-sort="text">Nachname, Vorname</th>
                <th data-sort="text">Studiengang</th>
                <th style="text-align: right">Aktionen</th>
            </tr>
        </thead>
    <? foreach ($mentees as $mentee): ?>
        <tr>
            <td>
                <? if($mentee['issue'] == 1): ?>
                    <?= Icon::create('support', 'attention',  ['title' => sprintf(_('Problem mit %s wurde gemeldet'), $mentee['vorname'].' '.$mentee['name'])]) ?>
                <? endif; ?>
            </td>
            <td><?= htmlReady($mentee['nachname']). ', ' . htmlReady($mentee['vorname'])?></td>
            <td><?= htmlReady($mentee['studycourse'])?></td>
            <td class="actions">
                <? $actionMenu = ActionMenu::get() ?>
                <? $actionMenu->addLink(
                                URLHelper::getURL('dispatch.php/messages/write', [
                                    'filter'           => 'send_sms_to_all',
                                    'emailrequest'    => 1,
                                    'rec_uname'       => $mentee['username'],
                                    'default_subject' => $subject,
                                ]),
                                sprintf(_('Nachricht an %s senden'), $mentee['vorname'].' '.$mentee['nachname']),
                                Icon::create('mail', 'clickable', [
                                    'title' => sprintf(_('Nachricht an %s senden'), $mentee['vorname'].' '.$mentee['nachname']),
                                ]),
                                ['data-dialog' => '']
                ) ?>
                <? if($mentee['issue'] == 0) {
                    $actionMenu->addLink(
                        $controller->url_for('mentoring/support',[
                            'mentee_name' => $mentee['username'],
                            'mentor_name' => $mentor->username
                        ]),
                        sprintf(_('Problem mit %s melden'), $mentee['vorname'].' '.$mentee['name']),
                        Icon::create('support', 'clickable', ['title' => sprintf(_('Problem mit %s melden'), $mentee['vorname'].' '.$mentee['name']),])
                    ); 
                }?>
                <?= $actionMenu->render() ?>
            </td>
        </tr>
     <? endforeach; ?>
</table>
