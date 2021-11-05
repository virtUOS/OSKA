<h1 class="oska-page-title"><?= htmlReady($title) ?> (<?= htmlReady($mentors_counter) ?>)</h1>

<form action="<?= $controller->link_for('admin/fach_filter_mentor');?>" class="default" method="post">
    <label>
        <?= _('Namenssuche'); ?>
        <div>
            <input name="search_term" placeholder="<?=_('Suchbegriff')?>" value="<?= $search_term ? htmlReady($search_term) : '' ?>"/>
        </div>
    </label>
    <section class="col-2">
        <span class="label-text"><?= _('Studienfach') ?></span>
        <label>
            <select name="fach_filter">
                <option value="" <? if($fach_filter == null) {echo 'selected';}?>><?=_('kein Filter') ?></option>
                <? foreach ($fächer as $fach): ?>
                <option value="<?= $fach['fach_id'] ?>" <? if($fach_filter == $fach['fach_id']) {echo 'selected';}?>><?= htmlReady($fach['name']); ?></option>
                <? endforeach; ?>
            </select>
        </label>
    </section>
    <section class="col-1">
        <span class="label-text"><?= _('Anzahl Mentees') ?></span>
        <label>
            <input type="checkbox" name="mentee_count_activate" class="oska-filter-checkbox" 
                <?= isset($mentee_count) ? 'checked' : '' ?>>
            <input type="number" name="mentee_count_filter" min="0" max="8" 
                value="<?= isset($mentee_count) ? htmlReady($mentee_count) : '' ?>" 
                <?= isset($mentee_count) ? '' : 'disabled' ?>>
        </label>
    </section>
    <section class="col-1" style="vertical-align: bottom">
        <button type="submit" class="button" style="margin:1em;"><?= _('Filter anwenden')?></button>
    </section>
    
</form>

<table class="sortable-table default">
    <colgroup>
        <col width="600">
        <col>
        <col>
        <col width="120">
        <col width="80">
    </colgroup>
        <thead>
            <tr>
                <th><?= _('Nachname, Vorname') ?></th>
                <th><?= _('Fach') ?></th>
                <th><?= _('ausgewähltes Fach') ?></th>
                <th style="text-align: center"><?= _('Anzahl Mentees') ?></th>
                <th style="text-align: right"><?= _('Aktionen') ?></th>
            </tr>
        </thead>
        <tbody>
        <? foreach ($mentors as $mentor): ?>
            <tr>
                <td><?= htmlReady($mentor['user']->nachname). ', ' . htmlReady($mentor['user']->vorname)?></td>
                <td><?= htmlReady($mentor['fach']) ?></td>
                <td><?= htmlReady($mentor['fach_selected']) ?></td>
                <td style="text-align: center">
                    <? if ($mentor['mentee_counter'] > 0) : ?>
                        <a href="<?= $controller->url_for('admin/mentors_mentees/' . $mentor['user']->id) ?>">
                            <?= htmlReady($mentor['mentee_counter']) ?>
                        </a>
                    <? else: ?>
                        <?= htmlReady($mentor['mentee_counter']) ?>
                    <? endif; ?>
                </td>
                <td class="actions">
                    <? $actionMenu = ActionMenu::get() ?>
                    <? $actionMenu->addLink(
                        URLHelper::getURL('dispatch.php/messages/write', [
                            'filter'          => 'send_sms_to_all',
                            'emailrequest'    => 1,
                            'rec_uname'       => $mentor['user']->username,
                            'default_subject' => _(''),
                        ]),
                        _('Nachricht an Mentor senden'),
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
        <? if ($mentors_counter > $entries_per_page) : ?>
        <tfoot>
            <tr>
                <td colspan="6" class="actions">
                    <?= $GLOBALS['template_factory']->render('shared/pagechooser', [
                        'perPage'      => $entries_per_page,
                        'num_postings' => $mentors_counter,
                        'page'         => $page,
                        'pagelink'     => "plugins.php/OSKA/admin/mentors/%s/".$fach_filter."/".$mentee_count."/",
                    ]) ?>
                </td>
            </tr>
        </tfoot>
    <? endif; ?>
</table>
