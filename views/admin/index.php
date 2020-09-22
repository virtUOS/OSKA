<h1 class="oska-page-title"><?= $title ?></h1>

<div class="oska-pie-wrapper">
    <h2>Mentee (<?= htmlReady($mentees_all)?>)</h2>
    <? if($mentees_all != 0): ?>
        <div class="oska-pie">
            <div 
                class="oska-pie__segment"
                style="--offset: 0; --value: <?= htmlReady($mentees_with_percentage)?>; --bg: <?= $pie_bg_colors[0]?>; <? if($mentees_with_percentage > 50): ?>--over50: 1;<? endif ?>" 
                title="<?= htmlReady($mentees_all - $mentees_without) . ' ' ._('Mentee mit OSKA') . ' (' . htmlReady($mentees_with_percentage) . '%)'?>">
                <label class="oska-pie__label"><?= _('mit OSKA') ?></label>
            </div>
            <div 
                class="oska-pie__segment"
                style="--offset: <?= htmlReady($mentees_with_percentage)?>; --value: <?= htmlReady($mentees_without_percentage)?>; --bg: <?= $pie_bg_colors[1]?>; <? if($mentees_without_percentage > 50): ?>--over50: 1;<? endif ?>"
                title="<?= htmlReady($mentees_without).' ' ._('Mentee ohne OSKA') . ' (' . htmlReady($mentees_without_percentage) . '%)'?>">
                <label class="oska-pie__label"><?= _('ohne OSKA') ?></label>
            </div>
        </div>
    <? endif; ?>
</div>
<div class="oska-pie-wrapper">
    <h2>OSKA (<?= htmlReady($oska_all)?>)</h2>
    <? if ($oska_all != 0): ?>
        <div class="oska-pie">
            <? $offset = 0; ?>
            <? foreach ($oska_counters as $key => $counter): ?>
                <?  $value = $counter['count'] / $oska_all * 100; ?>
            <div 
                class="oska-pie__segment"
                style="--offset: <?= $offset ?>; --value: <?= $value ?>; --bg: <?= $pie_bg_colors[$key]?>; <? if($value > 50):?>--over50: 1;<? endif;?>" 
                title="<?= htmlReady($counter['count'])._(' OSKA mit ') . htmlReady($counter['mentee_counter']) . _(' Mentee') . '(' . htmlReady($value) . '%)'?>">
                    <label class="oska-pie__label"><?= _('mit ') . htmlReady($counter['mentee_counter']) .  _(' Mentee') ?></label>
                </div>
                <? $offset = $offset + $value; ?>
            <? endforeach; ?>

        </div>
    <? endif; ?>
</div>
<? if(sizeof($issues) > 0): ?>
    <h2><?= _('Gemeldete Probleme') ?></h2>
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
                                    'title' => _('Nachricht an Mentor schreiben'),
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
                                    'title' => _('Nachricht an Mentee schreiben'),
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
<? endif; ?>
</table>
