<h1><?= $title ?></h1>
<h2><?= _('Gemeldete Probleme') ?></h2>
<table class="sortable-table default">
    <colgroup>
        <col>
        <col>
        <col width="80">
    </colgroup>
        <thead>
            <tr>
                <th data-sort="text">Mentor (Nachname, Vorname)</th>
                <th data-sort="text">Mentee (Nachname, Vorname)</th>
                <th style="text-align: right">Aktionen</th>
            </tr>
        </thead>
    <? foreach ($issues as $issue): ?>
        <tr>
                <td><?= htmlReady($issue['mentor']->nachname). ', ' . htmlReady($issue['mentor']->vorname)?></td>
                <td><?= htmlReady($issue['mentee']->nachname). ', ' . htmlReady($issue['mentee']->vorname)?></td>
                
                <td class="actions">
                    <? $actionMenu = ActionMenu::get() ?>
                    <? $actionMenu->addLink(
                                    URLHelper::getURL('dispatch.php/messages/write', [
                                        'filter'           => 'send_sms_to_all',
                                        'emailrequest'    => 1,
                                        'rec_uname'       => $issue['mentor']->username,
                                        'default_subject' => _('Problem lösen'),
                                    ]),
                                    _('Nachricht an Mentor senden'),
                                    Icon::create('mail', 'clickable', [
                                        'title' => _('Nachricht an Mentor senden'),
                                    ]),
                                    ['data-dialog' => '']
                    ) ?>
                    <? $actionMenu->addLink(
                                    URLHelper::getURL('dispatch.php/messages/write', [
                                        'filter'           => 'send_sms_to_all',
                                        'emailrequest'    => 1,
                                        'rec_uname'       => $issue['mentee']->username,
                                        'default_subject' => _('Problem lösen'),
                                    ]),
                                    _('Nachricht an Mentee senden'),
                                    Icon::create('mail', 'clickable', [
                                        'title' => _('Nachricht an Mentee senden'),
                                    ]),
                                    ['data-dialog' => '']
                    ) ?>
                    <? $actionMenu->addLink(
                            $controller->url_for('admin/delete_match',[
                                'mentee_id' => $issue['mentee']->user_id,
                                'mentor_id' => $issue['mentor']->user_id
                            ]),
                            _('Match auflösen'),
                            Icon::create('decline', 'clickable', ['title' => _('Match auflösen')])
                        ); 
                    ?>
                    <? $actionMenu->addLink(
                            $controller->url_for('admin/remove_issue',[
                                'mentee_id' => $issue['mentee']->user_id,
                                'mentor_id' => $issue['mentor']->user_id
                            ]),
                            _('Problem entfernen'),
                            Icon::create('remove-circle-full', 'clickable', ['title' => _('Problem entfernen')])
                        ); 
                    ?>
                    <?= $actionMenu->render() ?>
                </td>
            </tr>
        <? endforeach; ?>
    </table>

