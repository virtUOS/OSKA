<form action="<?= $controller->link_for('admin/store_mentee');?>" class="default" method="post">
    <fieldset>
        <legend>
            <?= _('Mentee') ?>
        </legend>
        <label>
            <?= _('Mentee')?>
                <?= QuickSearch::get('user_id', new StandardSearch('user_id'))
                    ->withButton()
                    ->setInputStyle('width: 360px')
                    ->render();
                ?>
        </label>
    </fieldset>
    <fieldset>
        <legend>
                <?= _('OSKA-Präferenzen') ?>
        </legend>

        <label class="label_text"><?= _('Welches Fach soll der/die OSKA studieren?') ?><br>
            <? if (count($studycourses) > 1): ?>
                <select name="studycourse" class="nested-select" required>
                    <? foreach($studycourses as $studycourse): ?>
                        <? if ($studycourse->countUser()): ?>
                            <option value="<?= $studycourse->fach_id ?>">
                                <?= htmlReady($studycourse->name) ?>
                            </option>
                        <? endif ?>
                    <? endforeach ?>
                </select>
            <? else: ?>
            <input type="hidden" name="studycourse" value="<?= $studycourses[0]->fach_id ?>" />
            <input type="text" name="studycoursename" value="<?= htmlReady($studycourses[0]->name) ?>" disabled />
            <? endif?>
        </label>

        <section class="col-2">
            <span class="label-text"><?= _('Mentee studiert mit dem Ziel Lehramt?') ?></span>
            <div class="hgroup">
                <label>
                    <input type="radio" name="lehramt" value="1"> <?= _('Ja') ?>
                </label>
                <label>
                    <input type="radio" name="lehramt" value="0" checked> <?= _('Nein') ?>
                </label>
            </div>
        </section>

        <section class="col-4" id="teacher-type">
            <span class="label-text"><?= _('Falls ja, welches Lehramt wird angestrebt?') ?></span>
            <div class="hgroup">
                <label>
                    <input type="radio" name="lehramt_detail" value="0"> <?= _('Berufliche Bildung') ?>
                </label>
                <label>
                    <input type="radio" name="lehramt_detail" value="1"> <?= _('Bildung, Erziehung und Unterricht') ?>
                </label>
                <label>
                    <input type="radio" name="lehramt_detail" value="2"><?= _('Gymnasium, Haupt- und Realschule') ?> 
                </label>
            </div>
        </section>

        <span class="label-text"><?= _('Welches Geschlecht sollte sein/ihr OSKA haben?') ?></span>
        <div class="hgroup">
            <label>
                <input type="radio" name="gender" value="0" checked> <?= _('egal') ?> 
            </label>
            <label>
                <input type="radio" name="gender" value="1"> <?= _('männlich') ?> 
            </label>
            <label>
                <input type="radio" name="gender" value="2"> <?= _('weiblich') ?>
            </label>
            <label> 
                <input type="radio" name="gender" value="3"> <?= _('divers') ?> 
            </label>
        </div>

        <section>
            <label>
                <?= _('OSKA hat Migrationshintergrund') ?>
                <select name="migration">
                    <option value="0"><?= _('nein')?></option>
                    <option value="1"><?= _('ja')?></option>
                </select>
            </label>
            <label>
                <?= _('OSKA hat als Erste*r in seiner/ihrer Familie studiert') ?>
                <select type="hidden" name="firstgen">
                    <option value="0"><?= _('nein')?></option>
                    <option value="1"><?= _('ja')?></option>
                </select>
            </label>
            <label>
                <?= _('OSKA hat bereits (ein) Kind(er)') ?>
                <select type="hidden" name="children" >
                    <option value="0"><?= _('nein')?></option>
                    <option value="1"><?= _('ja')?></option>
                </select>
            </label>
            <label>
                <?= _('OSKA hat eine (duale) Ausbildung abgeschlossen') ?>
                <select type="hidden" name="apprentice">
                    <option value="0"><?= _('nein')?></option>
                    <option value="1"><?= _('ja')?></option>
                </select>
            </label>
        </section>
    </fieldset>
    <footer data-dialog-button>
        <button class="button accept"><?= _('hinzufügen') ?></button>
    </footer>
</form>
