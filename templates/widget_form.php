<div id="oska-widget">
    <div class="oska-form">
        <p>Herzlichen Willkommen auf der Anmeldeplattform zu deinem OSKA.
        Um einen passenden OSKA für dich ausfindig zu machen, bitten wir dich 
        die folgenden Felder auszufüllen. Deine Angaben werden dazu verwendet, 
        einen passenden OSKA für dich zu finden.</p>
        
        <form class="default" action="<?= PluginEngine::getLink('OSKA/widget/add_mentee') ?>" 
            method="post" id="oska-add-mentee">
        <?= CSRFProtection::tokenTag() ?>
        
        <fieldset class="oska-form-general">
        
            <div class="oska-form-flex oska-form-flex-big">
                <label for="studycourse"><?= _('Dein Studiengang') ?><br>
                <select name="studycourse">
                <? foreach($studycourses as $studycourse): ?>
                    <option value="<?= $studycourse->fach_id ?>"><?= htmlReady(_($studycourse->studycourse_name)) ?></option>
                <? endforeach ?>
                </select>
                </label>
            </div>
   
            <div class="oska-form-flex">
                <label for="teacher"><?= _('Studierst du mit dem Ziel Lehramt?') ?><br>
                <input type="radio" name="teacher" value="1">Ja 
                <input type="radio" name="teacher" value="0" checked>Nein</label>
            </div>
            
            <div class="oska-form-flex" id="teacher-type">
                <label for="teacher_type"><?= _('Wenn ja, welches Lehramt strebst du an?') ?><br>
                <input type="radio" name="teacher_type" value="beruf" disabled><?= _('Berufliche Bildung') ?> 
                <input type="radio" name="teacher_type" value="erziehung" disabled><?= _('Bildung, Erziehung und Unterricht') ?>
                <input type="radio" name="teacher_type" value="schule" disabled><?= _('Gymnasium, Haupt- und Realschule') ?> 
                </label>
            </div>
            
            <div class="oska-form-flex">
                <label for="gender"><?= _('Welches Geschlecht sollte dein OSKA haben?') ?><br>
                <input type="radio" name="gender" value="0" checked>egal 
                <input type="radio" name="gender" value="1">männlich 
                <input type="radio" name="gender" value="2">weiblich 
                <input type="radio" name="gender" value="3">divers </label>
            </div>
        </fieldset>
        
        <fieldset class="oska-form-general oska-form-prefs">
        <label>
            Bei meinem OSKA ist mir besonders wichtig: 
            (Wenn dir einzelne Eigenschaften bei deinem OSKA wichtig sind, 
            kannst du diese in die untenstehende Box ziehen.)
        </label>
        <table class="oska-pref-table">
            <tr>
            <td class="oska-pref-list" data-group="1" title="<?= _('Element hier ablegen') ?>">
                
            </td>
            <td class="oska-pref-list" data-group="0">
                <div class="oska-pref-item">
                ... dass er/sie einen Migrationshintergrund hat
                <input type="hidden" name="migrant" value="0">
                </div>
                <div class="oska-pref-item">
                    ... dass er/sie als Erste*r in seiner/ihrer Familie studiert
                    <input type="hidden" name="first_generation" value="0">
                </div>
                <div class="oska-pref-item">
                    ... dass er/sie bereits Kinder hat
                    <input type="hidden" name="children" value="0">
                </div>
                <div class="oska-pref-item">
                    ... dass er/sie vor dem Studium eine duale Ausbildung gemacht hat
                    <input type="hidden" name="apprentice" value="0">
                </div>
            </td>
            </tr>
        </table>
        
        </fieldset>
        <?= Studip\Button::create(_('OSKA für mich finden'), 'oska_matching_button', 
            ['class' => 'oska-find']) ?>
        </form>
    </div>
</div>
