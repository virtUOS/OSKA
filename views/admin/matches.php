<h1 class="oska-page-title"><?= htmlready($title)?> (<?= htmlReady($matches_counter) ?>)</h1>

<form action="<?= $controller->link_for('admin/matches_filter');?>" class="default" method="post">
    <label>
        <?= _('Fach-Filter'); ?>
        <select name="fach_filter">
        <option value="" <? if($fach_filter === null) {echo 'selected';}?>><?=_('kein Filter') ?></option>
        <? foreach ($fächer as $fach): ?>
        <option value="<?= $fach['fach_id'] ?>" <? if($fach_filter === $fach['fach_id']) {echo 'selected';}?>><?= htmlReady($fach['name']); ?></option>
        <? endforeach; ?>
        </select>
    </label>
    <button type="submit" class="button"><?= _('Filter anwenden')?></button>
</form>

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
                <th data-sort="text"><?= _('Gemeinsames Fach im Matching') ?></th>
                <th style="text-align: right"><?= _('Aktionen') ?></th>
            </tr>
        </thead>
        <? foreach ($matches as $match): ?>
            <tr>
                <td><?= htmlReady($match['mentor']->nachname). ', ' . htmlReady($match['mentor']->vorname)?>
                    <?= tooltipIcon(_('Fächer: ' . $match['mentor_studycourses'])) ?></td>
                <td><?= htmlReady($match['mentee']->nachname). ', ' . htmlReady($match['mentee']->vorname)?>
                    <?= tooltipIcon(_('Fächer: ' . $match['mentee_studycourses'])) ?></td>
                <td><?= htmlReady($match['matched_course']) ?></td>
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
        <tfoot>
            <tr>
                <td colspan="4" class="actions">
                    <?= $GLOBALS['template_factory']->render('shared/pagechooser', [
                            'perPage'      => $entries_per_page,
                            'num_postings' => $matches_counter,
                            'page'         => $page,
                            'pagelink'     => "plugins.php/OSKA/admin/matches/%s/" . $fach_filter . "/",
                        ]) ?>
                </td>
            </tr>
        </tfoot>
</table>
