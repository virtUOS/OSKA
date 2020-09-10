<div id="oska-widget">
    <div class="oska-found">Wir haben eine*n OSKA für dich gefunden. Dein*e OSKA wird ebenfalls 
        benachrichtigt und nimmt in den ersten beiden Semesterwochen Kontakt zu 
        dir auf. Wir wünschen dir viel Erfolg in deinem ersten Semester und viel 
        Spaß mit deinem*deiner OSKA.
    </div>
    <div class="oska-float oska-avatar"><?= $avatar->getImageTag(Avatar::MEDIUM) ?></div>
    
    <p>
        <b><?= htmlReady($mentor_name) ?></b><br>
        <? foreach($study_institutes as $institute): ?>
            <?= htmlReady($institute->institute->name) ?>
        <? endforeach ?>
    </p>
        
    <p><?= htmlReady($mentor_desc) ?></p>
</div>
