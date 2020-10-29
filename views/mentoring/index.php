<? use Studip\Button; ?>

<form class="default" action="<?= $controller->link_for('mentoring/store_profile') ?>" method="post">
    <fieldset>
        <legend>
            <?= htmlReady($title) ?>
        </legend>

        <label>
            <?= _('Studiengang, in dem du als OSKA-Mentor*in tätig bist') ?><br>
            <? if (count($studycourses) > 1): ?>
                <select name="studycourse[]" class="oska-mentor-fach" multiple="multiple" required>
                    <? foreach($studycourses as $studycourse): ?>
                        <? if ($studycourse->countUser()): ?>
                            <option value="<?= $studycourse->fach_id ?>" 
                                <?= in_array($studycourse->fach_id, $mentor->studycourse) ? 'selected' : '' ?>>
                                <?= htmlReady($studycourse->name) ?>
                            </option>
                        <? endif ?>
                    <? endforeach ?>
                </select>
            <? else: ?>
                <input type="hidden" name="studycourse[]" value="<?= $studycourses[0]->fach_id ?>" />
                <input type="text" name="studycoursename" value="<?= htmlReady($studycourses[0]->name) ?>" disabled />
            <? endif?>
        </label>

        <div class="label-text">
            <?= _('Studierst du mit dem Ziel Lehramt?') ?>
        </div>
        <label>
            <input name="lehramt" type="radio" value="1" <? if ($mentor->lehramt == 1) echo 'checked'; ?>>
            <?= _('ja')?>
        </label>
        <label>
            <input name="lehramt" type="radio" value="0" <? if ($mentor->lehramt == 0) echo 'checked'; ?>>
            <?= _('nein')?>
        </label>

        <div class="lehramt-details" style="display: none;">
            <div class="label-text">
                <?= _('Welches Lehramt strebst du an?') ?>
            </div>
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

        <div class="label-text">
            <?= _('Bist du der/die erste in deiner Familie, der/die studiert?') ?>
        </div>
        <label>
            <input name="firstgen" type="radio" value="1" <? if ($mentor->firstgen == 1) echo 'checked'; ?>>
            <?= _('Ja')?>
        </label>
        <label>
            <input name="firstgen" type="radio" value="0" <? if ($mentor->firstgen == 0) echo 'checked'; ?>>
            <?= _('Nein')?>
        </label>
        <label>
            <input name="firstgen" type="radio" value="2" <? if ($mentor->firstgen == 2) echo 'checked'; ?>>
            <?= _('Keine Angabe')?>
        </label>

        <div class="label-text">
            <?= _('Hast du Familienverantwortung?') ?>
        </div>
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

        <div class="label-text">
            <?= _('Hast du vor deinem Studium eine Ausbildung abgeschlossen?') ?>
        </div>
        <label>
            <input name="apprentice" type="radio" value="1" <? if ($mentor->apprentice == 1) echo 'checked'; ?>>
            <?= _('Ja')?>
        </label>
        <label>
            <input name="apprentice" type="radio" value="0" <? if ($mentor->apprentice == 0) echo 'checked'; ?>>
            <?= _('Nein')?>
        </label>
        <label>
            <input name="apprentice" type="radio" value="2" <? if ($mentor->apprentice == 2) echo 'checked'; ?>>
            <?= _('Keine Angabe')?>
        </label>

        <div class="label-text">
            <?= _('Hast du einen Migrationshintergrund, ist also einer deiner beiden Elternteile nicht in Deutschland geboren?') ?>
        </div>
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

        <label>
            <?= _('Beschreibe dich für deine/deinen Mentee kurz:') ?>
            <textarea name="description"><?= htmlReady($mentor->description) ?></textarea>
        </label>
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
    $(".oska-mentor-fach").select2({
        width: '50%'
    });
</script>
