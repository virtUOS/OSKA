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
        
            <section class="col-5">
            <div>
                <label class="label_text"><?= _('Dein Studiengang') ?><br>
                <select name="studycourse">
                <? foreach($studycourses as $studycourse): ?>
                    <option value="<?= $studycourse->fach_id ?>"><?= htmlReady(_($studycourse->studycourse_name)) ?></option>
                <? endforeach ?>
                </select>
                </label>
            </div>
            </section>
              
            <section class="col-2">
                <span class="label-text"><?= _('Studierst du mit dem Ziel Lehramt?') ?></span>
                <div class="hgroup">
                    <label>
                        <input type="radio" name="teacher" value="1"> <?= _('Ja') ?>
                    </label>
                    <label>
                        <input type="radio" name="teacher" value="0" checked> <?= _('Nein') ?>
                    </label>
                </div>
            </section>
            
            <section class="col-4" id="teacher-type" style="display: none;">
                <span class="label-text"><?= _('Wenn ja, welches Lehramt strebst du an?') ?></span>
                <div class="hgroup">
                    <label>
                        <input type="radio" name="teacher_type" value="beruf"> <?= _('Berufliche Bildung') ?>
                    </label>
                    <label>
                        <input type="radio" name="teacher_type" value="erziehung"> <?= _('Bildung, Erziehung und Unterricht') ?>
                    </label>
                    <label>
                        <input type="radio" name="teacher_type" value="schule"><?= _('Gymnasium, Haupt- und Realschule') ?> 
                    </label>
                </div>
            </section>
            
            <section>
                <span class="label-text"><?= _('Welches Geschlecht sollte dein OSKA haben?') ?></span>
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
            </section>
        </fieldset>
        
        <fieldset>
        <label>
            Bei meinem OSKA ist mir besonders wichtig: 
            (Wenn dir einzelne Eigenschaften bei deinem OSKA wichtig sind, 
            kannst du diese in die untenstehende Box ziehen.)
        </label>
        
        <div class="oska-form-prefs">
            <div class="oska-pref-list" data-group="1" title="<?= _('Element hier ablegen') ?>">
            </div>

            <div class="oska-pref-list oska-pref-container" data-group="0">
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
            </div>

        </div>
        
        </fieldset>
        <?= Studip\Button::create(_('OSKA für mich finden'), 'oska_matching_button', 
            ['class' => 'oska-find']) ?>
        </form>
    </div>
</div>
