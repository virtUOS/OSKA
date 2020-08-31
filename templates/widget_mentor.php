<div id="oska-widget">
    <div class="oska-found">Wir haben einen OSKA für dich gefunden. Dein OSKA wird ebenfalls 
        benachrichtigt und nimmt in den ersten beiden Semesterwochen Kontakt zu 
        dir auf. Wir wünschen dir viel Erfolg in deinem ersten Semester und viel 
        Spaß mit deinem OSKA.
    </div>
    <div class="oska-float"><?= $avatar->getImageTag(Avatar::NORMAL) ?></div>
    
    <p>
        <b><?= htmlReady($mentor_name) ?></b><br>
        <? foreach($study_institutes as $institute): ?>
            <?= htmlReady($institute->institute->name) ?>
        <? endforeach ?>
    </p>
        
    <p><?= htmlReady($mentor_desc) ?></p>
</div>
