<? use Studip\Button; ?>

<form class="default" action="<?= $controller->link_for('mentoring/store_profile') ?>" method="post">
    <fieldset>
        <legend>
        <?= $title ?>
        </legend>
        <label class="col-1">
            <span>
                <?= _('Geschlecht')?>
                <a href="<?= URLHelper::getURL('dispatch.php/settings/account') ?>" target="_blank">
                    <?= Icon::create('edit', 'clickable', ['title'=> _('Wert im Bereich Persöniche Angaben ändern')])?>
                </a>
            </span>
            <? switch($gender) {
                case 0:
                    $gender_name = _('unbekannt');
                    break;
                case 1:
                    $gender_name = _('männlich');
                    break;
                case 2:
                    $gender_name = _('weiblich');
                    break;
                case 3:
                    $gender_name = _('divers');
                    break;
                
            } ?>
            <input name="gender" value="<?= $gender_name?>" type="text" disabled>
        </label>

        <label class="col-4">
        </label>

        <section class="col-4">
            <span class="label-text"><?= _('Studiengang, in dem du als OSKA-Mentor*in tätig bist') ?></span>
            <div class="hgroup">
                <label>
                    <? if (count($studycourses) > 1): ?>
                        <select name="studycourse">
                            <? foreach($studycourses as $studycourse): ?>
                                <option value="<?= $studycourse->fach_id ?>" 
                                    <?= $mentor->studycourse == $studycourse->fach_id ? 'selected' : '' ?>>
                                    <?= htmlReady(_($studycourse->studycourse_name)) ?>
                                </option>
                            <? endforeach ?>
                        </select>
                    <? else: ?>
                    <input type="hidden" name="studycourse" value="<?= $studycourses[0]->fach_id ?>" />
                    <input type="text" name="studycoursename" value="<?= $studycourses[0]->studycourse_name ?>" disabled />
                    <? endif?>
                </label>
            </div>
        </section>

        <label class="col-1">
        </label>

        <section class="col-2">
            <span class="label-text"><?= _('Studierst du mit dem Ziel Lehramt?') ?></span>
            <div class="hgroup">
                <label>
                    <input name="lehramt" type="radio" value="1" <? if ($mentor->lehramt == 1) echo 'checked'; ?>>
                    <?= _('ja')?>
                </label>
                <label>
                    <input name="lehramt" type="radio" value="0" <? if ($mentor->lehramt == 0) echo 'checked'; ?>>
                    <?= _('nein')?>
                </label>
            </div>
        </section>

        <section class="col-4 lehramt-details" style="display: none;">
            <span class="label-text"><?= _('Welches Lehramt strebst du an?') ?></span>
            <div class="hgroup">
                <label>
                    <input name="lehramt_detail" type="radio" value="0" <? if ($mentor->lehramt_detail == 0) echo 'checked'; ?>>
                    <?= _('Berufliche Bildung')?>
                </label>
                <label>
                    <input name="lehramt_detail" type="radio" value="1" <? if ($mentor->lehramt_detail == 1) echo 'checked'; ?>>
                    <?= _('Bildung, Erziehung und Unterricht')?>
                </label>
                <label>
                    <input name="lehramt_detail" type="radio" value="2" <? if ($mentor->lehramt_detail == 2) echo 'checked'; ?>>
                    <?= _('Gymnasium, Haupt- und Realschule')?>
                </label>
            </div>
        </section>

        <section>
            <span class="label-text"><?= _('Bist du der/die erste in deiner Familie, der/die studiert?') ?></span>
            <div class="hgroup">
                <label>
                    <input name="firstgen" type="radio" value="1" <? if ($mentor->firstgen == 1) echo 'checked'; ?>>
                    <?= _('ja')?>
                </label>
                <label>
                    <input name="firstgen" type="radio" value="0" <? if ($mentor->firstgen == 0) echo 'checked'; ?>>
                    <?= _('nein')?>
                </label>
                <label>
                    <input name="firstgen" type="radio" value="2" <? if ($mentor->firstgen == 2) echo 'checked'; ?>>
                    <?= _('keine Angabe')?>
                </label>
            </div>
        </section>

        <section>
            <span class="label-text"><?= _('Hast du Familienverantwortung?') ?></span>
            <div class="hgroup">
                <label>
                    <input name="children" type="radio" value="1" <? if ($mentor->children == 1) echo 'checked'; ?>>
                    <?= _('Ja, ich habe Kinder')?>
                </label>
                <label>
                    <input name="children" type="radio" value="0" <? if ($mentor->children == 0) echo 'checked'; ?>>
                    <?= _('Nein, ich habe keine Kinder')?>
                </label>
                <label>
                    <input name="children" type="radio" value="2" <? if ($mentor->children == 2) echo 'checked'; ?>>
                    <?= _('keine Angabe')?>
                </label>
            </div>
        </section>

        <section>
            <span class="label-text"><?= _('Hast du vor deinem Studium eine Ausbildung abgeschlossen?') ?></span>
            <div class="hgroup">
                <label>
                    <input name="apprentice" type="radio" value="1" <? if ($mentor->apprentice == 1) echo 'checked'; ?>>
                    <?= _('ja')?>
                </label>
                <label>
                    <input name="apprentice" type="radio" value="0" <? if ($mentor->apprentice == 0) echo 'checked'; ?>>
                    <?= _('nein')?>
                </label>
                <label>
                    <input name="apprentice" type="radio" value="2" <? if ($mentor->apprentice == 2) echo 'checked'; ?>>
                    <?= _('keine Angabe')?>
                </label>
            </div>
        </section>

        <section>
            <span class="label-text"><?= _('Hast du einen Migrationshintergrund, ist also einer deiner beiden Elternteile nicht in Deutschland geboren?') ?></span>
            <label>
                <input name="migration" type="radio" value="1"  <? if ($mentor->migration == 1) echo 'checked'; ?>>
                <?= _('Ja, ich habe einen Migrationshintergrund')?>
            </label>
            <label>
                <input name="migration" type="radio" value="0" <? if ($mentor->migration == 0) echo 'checked'; ?>>
                <?= _('Nein, ich habe keinen Migrationshintergrund')?>
            </label>
            <label>
                <input name="migration" type="radio" value="2" <? if ($mentor->migration == 2) echo 'checked'; ?>>
                <?= _('keine Angabe')?>
            </label>
        </section>

        <section class="col-2">
            <span class="label-text"><?= _('Beschreibe dich für deine/deinen Mentee kurz:') ?></span>
            <textarea name="description"><?= htmlReady($mentor->description) ?></textarea>
        </section>
    </fieldset>
    <footer>
    <? if ($default_data) : ?>
        <?= Button::create(_('Speichern'))?>
    <? else: ?>
        <?= Button::create(_('Übernehmen'))?>
    <? endif; ?>
    </footer>
</form>

<script>
    $(document).ready(function(){
        $('input[name="lehramt"]').change(function(){
            if($('input[name="lehramt"]:checked').val() == 1) {
                $('.lehramt-details').show();
                if($('input[name="lehramt_detail"]:checked').length == 0) {
                    $('input[name="lehramt_detail"][value="2"]').attr('checked', 'true');
                }
            } else {
                $('.lehramt-details').hide();
            }
        });
        $('input[name="lehramt"]').trigger('change');
    });
</script>
