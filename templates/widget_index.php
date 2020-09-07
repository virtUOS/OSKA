<div id="oska-widget">
    <img src="<?= $oska_image_url ?>" width="55%" height="55%" class="oska-float">
    <p><?= _('OSKA? Was ist das überhaupt? OSKA steht für Osnabrücker Kommiliton*innen für 
Studien-Anfänger*innen und ist die Bezeichnung für ausgebildete Studierende der 
Universität Osnabrück, welche dich zum Start deines Studiums an der Universität 
Osnabrück unterstützen. Wie wird mein Studienalltag in Osnabrück werden? Lerne 
ich schnell neue Leute aus meinem Studiengang kennen? Wie komme ich am besten 
durch das erste Semester und das unter den Bedingungen der hybriden bzw. 
digitalen Lehre?</p>
<p>All das sind vermutlich Fragen, die du dir zu Beginn deines Studiums bereits 
gestellt hast. Aber keine Sorge, wir - die Universität Osnabrück - lassen dich 
nicht allein. Für dein erstes Semester an der Universität Osnabrück besteht die 
Möglichkeit, eine*n persönlichen Ansprechpartner*in, eine*n sogenannten OSKA 
(Osnabrücker Kommiliton*innen für Studien-Anfänger*innen), zu erhalten. Unsere 
ausgebildeten OSKAs sind erfahrene Studierende, die dich das komplette erste 
Semester an der Universität Osnabrück begleiten. Die OSKAs werden individuell 
nach deinem Studiengang und deinen Bedürfnissen ausgewählt, sodass du hilfreiche 
Tipps und Tricks von erfahrenen Studierenden erhältst und dir zugleich jemand bei der 
Orientierung im Universitätsdschungel zur Seite steht.</p>
<p>Neben der Beantwortung zentraler Fragen rund ums Studium, hilft dir dein OSKA 
in einer Kleingruppe mit anderen Studienanfänger*innen (Mentees) den Campus und 
das Studentenleben kennen zu lernen. Durch den Austausch mit dem OSKA innerhalb 
einer Kleingruppe, knüpfst du direkt Kontakte mit weiteren Studienanfänger*innen 
deiner Fachrichtung.</p>
<p>Haben wir dein Interesse geweckt? Dann melde dich gleich unter dem Button „OSKA 
für mich finden“ an. Die OSKAs werden individuell auf deinen Studiengang und 
deinen Bedürfnissen mittels automatisiertem Auswahlverfahren (Matching) 
zugeteilt. Dafür benötigen wir im nächsten Schritt ein paar Informationen zu 
deiner Person sowie deinen Wünschen an deinen OSKA.') ?></p>
        <center><?= Studip\Button::create(_('OSKA für mich suchen'), 'oska_search_button', 
        ['class' => 'search', 'id' => 'open-oska-form']) ?></center>
</div>
