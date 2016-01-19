<html>
<head>
  <title>Template für die Personenseiten der Hochschule Esslingen</title>
</head>
<body>
<!-- ###LISTVIEW### begin -->
<div class="bereichs_ueberschrift">###bereich###</div>
<table>
<!-- ###LISTVIEW### end -->

<!-- ###SCHLEIFE### begin -->

<tr class="zeile###zeilen_nummer###" ###umleitung###>
	<td class="akagrad">###akagrad###</td>
	<td class="nachname"><b>###name###</b></td>
	<td class="vorname">###vorname###</td>
	<td class="raum">###adresse###</td>
	<td class="telefon"><b>###telephone###</b></td>
</tr>
<tr class="zeile###zeilen_nummer###">
<td colspan="2" class="listenfkt">###listenfkt###</td>
<td colspan="3" class="email3">###email###</td>
</tr>
<!-- ###SCHLEIFE### end -->



<!-- ###SINGLEVIEW### begin -->
		<div class="name_profil">###vorname_profil### ###name_profil###, ###akagrad_profil###</div>
<!-- 
		<div class="taetigkeit">###taetigkeit###</div>
-->

<!-- ###bild_ausblenden### begin -->	
		###profilbild###
<!-- ###bild_ausblenden### end -->

<!-- ###funktion_ausblenden### begin -->	
		<div class="profil_ueberschrift">Funktionen</div><br/>
		<ul><div class="profil_funktionen">###funktion###</div></ul>
<!-- ###funktion_ausblenden### end -->

<!-- ###fachgebiete_ausblenden### begin -->	
		<div class="profil_ueberschrift">Fachgebiete</div>
		<div class="profil_fachgebiete">###fachgebiete###</div>
<!-- ###fachgebiete_ausblenden### end -->

<!-- ###werdegang_ausblenden### begin -->	
		<div class="profil_ueberschrift">Werdegang</div>
		<div class="profil_werdegang">###werdegang###</div>
<!-- ###werdegang_ausblenden### end -->

<!-- ###veroeffentlichungen_ausblenden### begin -->	
		<div class="profil_ueberschrift">Veröffentlichungen</div>
		<div class="profil_werdegang">###veroeffentlichungen###</div>
<!-- ###veroeffentlichungen_ausblenden### end -->

<!-- ###sprechstunde_ausblenden### begin -->	
		<div class="profil_ueberschrift">Sprechstunde</div>
		<div class="profil_sprechstunde">###sprechstunde###</div>
<!-- ###sprechstunde_ausblenden### end -->

<!-- ###weitere_infos_ausblenden### begin -->	
		<div class="profil_ueberschrift">Weitere Informationen</div>
		<div class="profil_weitere_infos">###weitere_infos###</div>
<!-- ###weitere_infos_ausblenden### end -->

<!-- ###sonderfkt_ausblenden### begin -->	
		<div class="profil_ueberschrift">Sonderfunktionen</div>
		<div class="profil_sonderfkt">###sonderfkt###</div><br/>
<!-- ###sonderfkt_ausblenden### end -->

<!-- ###mobil_tel_ausblenden### begin -->	
		<div class="profil_ueberschrift">Mobiltelefon</div>
		<div class="profil_mobil_tel">###mobil_tel###</div>
<!-- ###mobil_tel_ausblenden### end -->

<!-- ###raumnr_display_ausblenden### begin -->	
		<div class="profil_ueberschrift">Raumnummer anzeigen?</div>
		<div class="profil_raumnr_display">###raumnr_display###</div>
<!-- ###raumnr_display_ausblenden### end -->

<!-- ###fax_ausblenden### begin -->	
		<div class="profil_ueberschrift">Faxnummer</div>
		<div class="profil_fax">###fax###</div>
<!-- ###fax_ausblenden### end -->

<!-- ###www_ausblenden### begin -->	
		<div class="profil_ueberschrift">Internetseite</div>
		<div class="profil_www">###www###</div>
<!-- ###www_ausblenden### end -->

		<div class="profil_ueberschrift">Adresse</div>
		<div class="adressinhalt">
			<table>
<!-- ###raum_ausblenden### begin -->	
				<tr class="titel">
					<td>Raum</td>
					<td class="adresse_profil">###adresse_profil###</td>
				</tr>
<!-- ###raum_ausblenden### end -->	
				<tr class="titel">
					<td>E-Mail</td>
					<td class="email_profil">###email_profil###</td>
				</tr>
				<tr class="titel">
					<td>Telefon</td>
					<td class="telephone_profil">###telephone_profil###</td>					
				</tr>
			</table>
		</div>
		

<!-- ###SINGLEVIEW### end -->

</body>
</html>
