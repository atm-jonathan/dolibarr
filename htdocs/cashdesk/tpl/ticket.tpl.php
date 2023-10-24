<?php
/* Copyright (C) 2007-2008 Jeremie Ollivier    <jeremie.o@laposte.net>
 * Copyright (C) 2011      Laurent Destailleur <eldy@users.sourceforge.net>
 * Copyright (C) 2012      Marcos García       <marcosgdf@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
include_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';

$langs->load("main");
$langs->load('cashdesk');
header("Content-type: text/html; charset=".$conf->file->character_set_client);

$facid=GETPOST('facid','int');
$object=new Facture($db);
$object->fetch($facid);

?>
<html>
<head>
<title><?php echo $langs->trans('PrintTicket') ?></title>

<style type="text/css">
body {
        font-size: 2em;
        width: 300px;
}

.entete { /* 		position: relative; */

}

.address { /* 			float: left; */
        font-size: 16px;
        float: left;
	vertical-align: top;
//	position: absolute;
}

.date_heure {
        text-align: right;
        width: 300px;
	font-size: 16px;
	position: absolute;
}

.infos {
}

.liste_articles {
        position: relative;
        float: left;
	width: 100%;
	border-bottom: 1px solid #000;
	text-align: center;
}

.liste_articles tr.titres th {
	border-bottom: 1px solid #000;
}

.liste_articles td.total {
	text-align: right;
}

.totaux {
        font-size: 16px;
        margin-top: 0px;
        width: 60%;
	float: right;
	text-align: right;
}

.bas-page {
        position: relative;
        float: left;
        text-align: center;
        width: 100%;
        font-size: 14px;
        margin-top: 10px;
}

.paymentMode {
        position: relative;
        float: left;
        text-align: center;
        width: 40%;
        font-size: 14px;
}

.lien {
	position: absolute;
	top: 0;
	left: 0;
	display: none;
}

@media print {
	.lien {
		display: none;
	}
}
</style>

</head>

<body>
<div class="address">
	<div class="logo"><?php print '<img src="'.DOL_URL_ROOT.'/viewimage.php?modulepart=mycompany&amp;file='.urlencode('logos/thumbs/'.$mysoc->logo_small).'">'; ?></div>
        <?php print dol_nl2br(dol_format_address($mysoc)); ?><br>
        contact@bateau-concept.com</br>
        Tel : 03 85 23 12 52
</div>

<div class="date_heure"><?php
// Recuperation et affichage de la date et de l'heure
$now = dol_now();
print dol_print_date($now,'dayhourtext').'<br>';
print $object->ref;
?>
</div>

<br>

<div class="liste_articles"><table>
        <tr class="titres">
                <th><?php print $langs->trans("Code"); ?></th>
                <th><?php print $langs->trans("Label"); ?></th>
                <th><?php print $langs->trans("Qty"); ?></th>
                <th><?php print $langs->trans("TotalHT"); ?></th>
        </tr>

        <?php

        $tab=array();
    $tab = $_SESSION['poscart'];

    $tab_size=count($tab);
    for($i=0;$i < $tab_size;$i++)
    {
        $remise = $tab[$i]['remise'];
        echo ('<tr><td>'.$tab[$i]['ref'].'</td><td>'.$tab[$i]['label'].'</td><td>'.$tab[$i]['qte'].'</td><td class="total">'.price2num($tab[$i]['total_ht'],'MT').' '.$conf->currency.'</td></tr>'."\n");
    }

        ?>
</table>
</div>
<div class="paymentMode"><?php echo 'Mode de réglement : '. $obj_facturation->getSetPaymentMode(); ?></div>
<div><table class="totaux">
        <?php
        echo '<tr><th class="nowrap">'.$langs->trans("TotalHT").'</th><td class="nowrap">'.price2num($obj_facturation->prixTotalHt(),'MT')." ".$conf->currency."</td></tr>\n";
        echo '<tr><th class="nowrap">'.$langs->trans("TotalVAT").'</th><td class="nowrap">'.price2num($obj_facturation->montantTva(),'MT')." ".$conf->currency."</td></tr>\n";
        echo '<tr><th class="nowrap">'.$langs->trans("TotalTTC").'</th><td class="nowrap">'.price2num($obj_facturation->prixTotalTtc(),'MT')." ".$conf->currency."</td></tr>\n";
        ?>
</table></div>

<div class="bas-page">Garantie légale de conformité de 2 ans</BR>Nous vous remercions pour votre visite</div>

<script type="text/javascript">
        window.print();
</script>

<a class="lien" href="#"
        onclick="javascript: window.close(); return(false);"><?php echo $langs->trans("Close"); ?></a>


</body>
</html>
