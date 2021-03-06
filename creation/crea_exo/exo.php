<?php
 /*
    --En-tête officielle pour dire que ce code est sous une licence "libre" (plus d'infos: https://fr.wikipedia.org/wiki/Licence_publique_g%C3%A9n%C3%A9rale_GNU)--
    
    Copyright (C) A.RENAUDIN  Developer

    The JavaScript code in this page is free software: you can
    redistribute it and/or modify it under the terms of the GNU
    General Public License (GNU GPL) as published by the Free Software
    Foundation, either version 3 of the License, or (at your option)
    any later version.  The code is distributed WITHOUT ANY WARRANTY;
    without even the implied warranty of MERCHANTABILITY or FITNESS
    FOR A PARTICULAR PURPOSE.  See the GNU GPL for more details.

    As additional permission under GNU GPL version 3 section 7, you
    may distribute non-source (e.g., minimized or compacted) forms of
    that code without the copy of the GNU GPL normally required by
    section 4, provided you include this license notice and a URL
    through which recipients can access the Corresponding Source.
*/

  include("../../core/maj.php");
  // on récupère les paramètres
  if (!isset($_GET['exo']) || !isset($_GET['cat']) || !isset($_GET['livre']))
  {
    exit("imossible de récupérer les paramètres...");
  }
  $exo = $_GET['exo'];
  $livre = $_GET['livre'];
  $cat = $_GET['cat'];
  $dos_l = "../../livres/";
  if ($cat != "") $dos_l .= "$cat/";
  $dos_l .= "$livre";
  
  //si besoin, on crée un nouvel exercice
  if ($exo == "")
  {
    $exos = glob("$dos_l/exos/*" , GLOB_ONLYDIR);
    if (count($exos)>0) $exo = chr(ord(basename($exos[count($exos)-1])) + 1);
    else $exo = "A";
  }
  $dos_e = "$dos_l/exos/$exo";
  
  if (!file_exists($dos_e)) mkdir("$dos_e", 0777, true);
  if (!file_exists("$dos_e/charge.php")) file_put_contents("$dos_e/charge.php", "<?php include(\"../../../core/charge.php\"); ?>");
  if (!file_exists("$dos_e/sauve.php")) file_put_contents("$dos_e/sauve.php", "<?php include(\"../../../core/sauve.php\"); ?>");
  if (!file_exists("$dos_e/exo.php")) file_put_contents("$dos_e/exo.php", "");
  if (!file_exists("$dos_e/exo.txt")) file_put_contents("$dos_e/exo.txt", "");
  if (!file_exists("$dos_e/version")) copy("../../VERSION", "$dos_e/version");
  
  //on récupère la couleur de fond du livre et de l'exo
  $exo_coul = "#EEEFE4";
  $livre_coul = "#6D7BCF";
  $vals = explode("\n", file_get_contents("$dos_e/exo.txt"));
  if (count($vals)>10) $exo_coul = $vals[10];
  $vals = explode("\n", file_get_contents("$dos_l/livre.txt"));
  if (count($vals)>1) $livre_coul = $vals[1];
?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Création d'exercice</title>
  <link rel="stylesheet" href="exo.css">
  <link rel="stylesheet" href="base_exos.css">
  <link rel="shortcut icon" href="../../icons/gnome-palm.png" >
  <script type="text/javascript" src="../../libs/interact.min.js"></script>
  <script type="text/javascript" src="../../libs/jscolor.min.js"></script>
  
  <script type="text/javascript" src="exo.js"></script>
  <script type="text/javascript" src="evt.js"></script>
</head>

<body onload="start('<?php echo "$dos_e"; ?>')" style="background-color:<?php echo "$livre_coul"; ?>;">
<div id="main_div">
  <div id="cr_btn_div2">
    <img class="cr_btn" src="../../icons/edit-undo.svg" onclick="window.location.href='../livre.php?cat=<?php echo $cat ?>&livre=<?php echo $livre ?>'" title="retour au livre"/>
    <img class="cr_btn" src="../../icons/dialog-information.svg" onclick="window.location.href='infos.php?cat=<?php echo $cat ?>&livre=<?php echo $livre ?>&exo=<?php echo $exo ?>'" title="informations générales de l'exercice"/>
    <img class="cr_btn" src="../../icons/document-save.svg" onclick="g_exporter()" title="sauvegarder l'exercice"/>
  </div>
  <div id="cr_btn_div">
    <img class="cr_btn" src="../../icons/radio.svg" onclick="radio_new()" title="un seul choix possible (ronds)"/>
    <img class="cr_btn" src="../../icons/radiobtn.svg" onclick="radiobtn_new()" title="un seul choix possible (boutons)"/>
    <img class="cr_btn" src="../../icons/check.svg" onclick="check_new()" title="cases à cocher"/>
    <img class="cr_btn" src="../../icons/combo.svg" onclick="combo_new()" title="liste déroulante"/>
    <img class="cr_btn" src="../../icons/texte.svg" onclick="texte_new()" title="zone de texte à remplir"/>
    <img class="cr_btn" src="../../icons/multi.svg" onclick="multi_new()" title="boutons multipositions"/>
    <img class="cr_btn" src="../../icons/cible.svg" onclick="cible_new()" title="zone cible pour déplacer des objets"/>
    <div class="sep">&nbsp;</div>
    <img class="cr_btn" src="../../icons/image.svg" onclick="image_new()" title="ajouter une image"/>
    <img class="cr_btn" src="../../icons/texte_simple.svg" onclick="texte_simple_new(false)" title="ajouter du texte"/>
    <img class="cr_btn" src="../../icons/audio.svg" onclick="audio_new()" title="ajouter un son"/>
    <div class="sep">&nbsp;</div>
    <img class="cr_btn" src="../../icons/rect.svg" onclick="texte_simple_new(true)" title="ajouter un rectangle"/>
    <img class="cr_btn" src="../../icons/cercle.svg" onclick="cercle_new()" title="ajouter une éllipse"/>
    <img class="cr_btn" src="../../icons/ligne.svg" onclick="ligne_new()" title="ajouter une ligne"/>
    <div class="sep">&nbsp;</div>
    <img class="cr_btn" id="btn_aligne" src="../../icons/aligne.svg" onclick="cm_show_aligne(this)" title="alignements"/>
    <img class="cr_btn" id="btn_repart" src="../../icons/repartit.svg" onclick="cm_show_repart(this)" title="répartitions"/>
    <img class="cr_btn" id="btn_plans" src="../../icons/plans.svg" onclick="cm_show_plans(this)" title="positions"/>
  </div>
  
  <div id="cr_rendu" onmousedown="bloc_mousedown(this, event)" style="background-color:<?php echo "$exo_coul"; ?>;"></div>
    
  <div id="cr_opt">
    <div class="cr_opt_bloc">
      sélection :&nbsp;
      <select id="cr_bloc_liste" onchange="cr_bloc_liste_change(this)"><option value="">&nbsp;</option>aucune</select>
      <span id="cr_selection"></span>
    </div>
    
    <div class="sep">&nbsp;</div>
    
    <div class="cr_opt_bloc">
      <div id="cr_expl">
      </div>
      <div id="cr_txt_ini_div">
        <textarea id="cr_txt_ini" onkeypress="cr_txt_ini_keypress(event)" onkeyup="cr_txt_ini_change(this)"></textarea>
      </div>
      <div id="cr_img_get_div">
        <select id="cr_img_select" onchange="cr_img_select_change(this)">
          <option value="" selected></option>
          <option value="****">Parcourir...</options>
          <?php
            $imgs = glob("$dos_l/img/*");
            for ($i=0; $i<count($imgs); $i++)
            {
              echo "<option value=\"".basename($imgs[$i])."\">".basename($imgs[$i])."</option>";
            }
          ?>
        </select>
        <form enctype="multipart/form-data">
          <input name="cr_img_get" type="file" id="cr_img_get" accept="image/*" onchange="cr_img_get_change(this)"/>
        </form>
      </div>
      <div class="cr_opt_ligne" id="cr_audio_get_div">
        <select id="cr_audio_select" onchange="cr_audio_select_change(this)">
          <option value="" selected></option>
          <option value="******">Enregistrer...</options>
          <option value="****">Parcourir...</options>
          <?php
            $imgs = glob("$dos_l/sons/*");
            for ($i=0; $i<count($imgs); $i++)
            {
              echo "<option value=\"".basename($imgs[$i])."\">".basename($imgs[$i])."</option>";
            }
          ?>
        </select>
        <form enctype="multipart/form-data">
          <input name="cr_audio_get" type="file" id="cr_audio_get" accept="audio/*" onchange="cr_audio_get_change(this)"/>
        </form>
        <div id="cr_record_div">
          <img id="cr_record_start" etat="0" src="../../icons/media-record.svg" onclick="cr_record_start(this)"/>
          <img id="cr_record_save" src="../../icons/document-save.svg" onclick="cr_record_save(this)"/>
          <img id="cr_record_delete" src="../../icons/window-close.svg" onclick="cr_record_delete(this)"/>
          <audio id="cr_record_audio"></audio>
        </div>
      </div>
    </div>
    
    <div class="sep">&nbsp;</div>
    
    <div class="cr_opt_bloc">
      <div class="cr_coul">Nombre de positions :
        <select id="cr_coul_nb" onChange="cr_coul_nb_change(this, 'true')"><option value="1">1</option><option value="2" selected>2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select>
      </div>
      <div class="cr_coul" id="cr_div_coul1">Etat 1
        <input class="jscolor {hash:true, onFineChange: 'cr_coul_change(this)'}" type="text" id="cr_coul1" value="#00ff00" />
        <input type="checkbox" name="cr_coul1_barre" id="cr_coul1_barre" onchange="cr_coul_change(this)"/><label for="cr_coul1_barre">barré</label>
        <input type="checkbox" name="cr_coul1_maj" id="cr_coul1_maj" onchange="cr_coul_change(this)"/><label for="cr_coul1_maj">maj.</label>
        <input type="checkbox" name="cr_coul1_suff" id="cr_coul1_suff" onchange="cr_coul_change(this)"/><label for="cr_coul1_suff">suffixe</label>
        <input type="text" size="3" id="cr_coul1_suff_txt" onchange="cr_coul_change(this)" disabled/>
      </div>
      <div class="cr_coul" id="cr_div_coul2">Etat 2
        <input class="jscolor {hash:true, onFineChange: 'cr_coul_change(this)'}" type="text" id="cr_coul2" value="#ff0000" />
        <input type="checkbox" name="cr_coul2_barre" id="cr_coul2_barre" onchange="cr_coul_change(this)"/><label for="cr_coul2_barre">barré</label>
        <input type="checkbox" name="cr_coul2_maj" id="cr_coul2_maj" onchange="cr_coul_change(this)"/><label for="cr_coul2_maj">maj.</label>
        <input type="checkbox" name="cr_coul2_suff" id="cr_coul2_suff" onchange="cr_coul_change(this)"/><label for="cr_coul2_suff">suffixe</label>
        <input type="text" size="3" id="cr_coul2_suff_txt" onchange="cr_coul_change(this)" disabled/>
      </div>
      <div class="cr_coul" id="cr_div_coul3">Etat 3
        <input class="jscolor {hash:true, onFineChange: 'cr_coul_change(this)'}" type="text" id="cr_coul3" value="#0000ff" />
        <input type="checkbox" name="cr_coul3_barre" id="cr_coul3_barre" onchange="cr_coul_change(this)"/><label for="cr_coul2_barre">barré</label>
        <input type="checkbox" name="cr_coul3_maj" id="cr_coul3_maj" onchange="cr_coul_change(this)"/><label for="cr_coul3_maj">maj.</label>
        <input type="checkbox" name="cr_coul3_suff" id="cr_coul3_suff" onchange="cr_coul_change(this)"/><label for="cr_coul3_suff">suffixe</label>
        <input type="text" size="3" id="cr_coul3_suff_txt" onchange="cr_coul_change(this)" disabled/>
      </div>
      <div class="cr_coul" id="cr_div_coul4">Etat 4
        <input class="jscolor {hash:true, onFineChange: 'cr_coul_change(this)'}" type="text" id="cr_coul4" value="#ffff00" />
        <input type="checkbox" name="cr_coul4_barre" id="cr_coul4_barre" onchange="cr_coul_change(this)"/><label for="cr_coul4_barre">barré</label>
        <input type="checkbox" name="cr_coul4_maj" id="cr_coul4_maj" onchange="cr_coul_change(this)"/><label for="cr_coul4_maj">maj.</label>
        <input type="checkbox" name="cr_coul4_suff" id="cr_coul4_suff" onchange="cr_coul_change(this)"/><label for="cr_coul4_suff">suffixe</label>
        <input type="text" size="3" id="cr_coul4_suff_txt" onchange="cr_coul_change(this)" disabled/>
      </div>
      <div class="cr_coul" id="cr_div_coul5">Etat 5
        <input class="jscolor {hash:true, onFineChange: 'cr_coul_change(this)'}" type="text" id="cr_coul5" value="#00ffff" />
        <input type="checkbox" name="cr_coul5_barre" id="cr_coul5_barre" onchange="cr_coul_change(this)"/><label for="cr_coul4_barre">barré</label>
        <input type="checkbox" name="cr_coul5_maj" id="cr_coul5_maj" onchange="cr_coul_change(this)"/><label for="cr_coul5_maj">maj.</label>
        <input type="checkbox" name="cr_coul5_suff" id="cr_coul5_suff" onchange="cr_coul_change(this)"/><label for="cr_coul4_suff">suffixe</label>
        <input type="text" size="3" id="cr_coul5_suff_txt" onchange="cr_coul_change(this)" disabled/>
      </div>
      <div id="cr_texte_div">
        Défaut <input type="text" size="49" id="cr_texte_defaut" onchange="cr_texte_change(this)"/><br/>
        Largeur (0=infini) :
        <input type="number" value="5" min="0" max="100" id="cr_texte_l" onchange="cr_texte_change(this)" title="nombre de charactères maxi" />
        Hauteur :
        <input type="number" value="1" min="1" max="100"  id="cr_texte_h" onchange="cr_texte_change(this)" title="nombre de lignes" />
        <br/>Retour à la ligne :
        <select id="cr_texte_e" onchange="cr_texte_change(this)"><option value="1" selected>autorisé</option><option value="0">interdit</option></select>
        <br/>Comparaison :
        <select id="cr_texte_c" onchange="cr_texte_change(this)"><option value="0" selected>strict</option><option value="1">Sans Maj. au début</option><option value="2">Sans Maj.</option></select>
        <br/><input type="checkbox" name="cr_texte_corr" id="cr_texte_corr" onchange="cr_texte_change(this)"/><label for="cr_texte_corr">afficher la correction</label>
      </div>
    </div>
    
    <div class="sep">&nbsp;</div>
    
    <div class="cr_opt_bloc">      
      <div class="cr_opt_ligne cr_font">
        Police
        <select class="cr_" id="cr_font_fam" onchange="cr_font_fam_change(this)" title="nom de la police">
          <option value="serif">Serif</option>
          <option value="sans-serif">Sans Serif</option>
          <option value="monospace">Mono</option>
          <option value="handfont">handfont</option>
          <option value="staypuft">Staypuf</option>
          <option value="ABC cursive">ABCcursive</option>
        </select>
        <input class="cr_" id="cr_font_size" type="number" value="20" min="1" max="800" onchange="cr_font_size_change(this)" title="taille de la police (initial=20)" step="any"/>
        <input id="cr_font_coul" type="text" class="cr_ jscolor {hash:true, onFineChange: 'cr_font_coul_change(this)'}" title="couleur de la police"></button>
        <input class="cr_" type="checkbox" id="cr_font_g" onchange="cr_font_g_change(this)"/>
        <label for="cr_font_g" style="font-weight: bold;" title="gras">G</label>
        <input class="cr_" type="checkbox" id="cr_font_i" onchange="cr_font_i_change(this)"/>
        <label for="cr_font_i" style="font-style: italic;" title="italique">I</label>
        <input class="cr_" type="checkbox" id="cr_font_s" onchange="cr_font_s_change(this)"/>
        <label for="cr_font_s" style="text-decoration: underline;" title="souligné (exclu barré)">S</label>
        <input class="cr_" type="checkbox" id="cr_font_b" onchange="cr_font_b_change(this)"/>
        <label for="cr_font_b" style="text-decoration: line-through;" title="barré (exclu souligné)">B</label>
        <input class="cr_" type="radio" id="cr_align_l" name="cr_align" onchange="cr_align_change(this)"/>
        <label for="cr_align_l" title="aligner à gauche"><img src="../../icons/align_l.svg" /></label>
        <input class="cr_" type="radio" id="cr_align_c" name="cr_align" onchange="cr_align_change(this)"/>
        <label for="cr_align_c" title="centrer"><img src="../../icons/align_c.svg" /></label>
        <input class="cr_" type="radio" id="cr_align_r" name="cr_align" onchange="cr_align_change(this)"/>
        <label for="cr_align_r" title="aligner à droite"><img src="../../icons/align_r.svg" /></label>
      </div>
      <div class="cr_opt_ligne">
        Taille
        <input class="cr_" id="cr_tp_w" type="number" value="20" min="0" max="500" onchange="cr_tp_w_change(this)" title="largeur" step="any"/>
        x
        <input class="cr_" id="cr_tp_h" type="number" value="20" min="0" max="700" onchange="cr_tp_h_change(this)" title="hauteur" step="any"/>
        &nbsp;&nbsp;Pos.<input id="cr_tp_l" type="number" value="20" min="0" max="500" onchange="cr_tp_l_change(this)" title="position horizontale" step="any"/>
        x
        <input class="cr_" id="cr_tp_t" type="number" value="20" min="0" max="700" onchange="cr_tp_t_change(this)" title="position verticale" step="any"/>
        &nbsp;&nbsp;Rot.<input id="cr_tp_r" type="number" value="0" min="-180" max="180" onchange="cr_tp_r_change(this)" title="rotation de l'objet" step="any"/>
      </div>
      <div class="cr_opt_ligne">
        Bordure
        <select class="cr_" id="cr_bord" onchange="cr_bord_change(this)" title="type de bordure">
          <option value="hidden">Aucune</option>
          <option value="solid">Normale</option>
          <option value="dashed">Tirets</option>
          <option value="dotted">Points</option>
          <option value="double">Double</option>
        </select>
        <input class="cr_" id="cr_bord_size" type="number" value="1" min="1" max="800" onchange="cr_bord_size_change(this)" title="épaisseur de la bordure" step="any"/>
        <input id="cr_bord_coul" type="text" class="cr_ jscolor {hash:true, onFineChange: 'cr_bord_coul_change(this)'}" title="couleur de la bordure"></button>
        &nbsp;arrondi
        <input class="cr_" id="cr_bord_rond" type="number" value="0" min="0" max="500" onchange="cr_bord_rond_change(this)" title="arrondi de la bordure" step="any"/>
      </div>
      <div class="cr_opt_ligne">
        Fond
        <input id="cr_fond_coul" type="text" class="cr_ jscolor {hash:true, value:'#ffffff', onFineChange: 'cr_fond_coul_change(this)'}"  title="couleur de fond"></button>
        <input class="cr_" id="cr_fond_alpha" type="number" value="0" min="0" max="100" onchange="cr_fond_alpha_change(this)" title="opacité (100 = opaque)"/>%
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Marges
        <input class="cr_" id="cr_marges" type="number" value="0" min="0" max="500" onchange="cr_marges_change(this)" title="marges intérieures" step="any"/>
      </div>
    </div>
    
    <div class="sep">&nbsp;</div>
    
    <div class="cr_opt_bloc">
      <div class="cr_opt_ligne">
        Interaction :
        <div>
          <input type="radio" value="0" name="cr_inter" id="cr_inter_0" checked="true" onchange="cr_inter_change(this)"/>
          <label for="cr_inter_0">Aucune</label>
          <br/>
          <input type="radio" value="1" name="cr_inter" id="cr_inter_1" onchange="cr_inter_change(this)" title="Cet élément peut être relié avec un autre."/>
          <label for="cr_inter_1" title="Cet élément peut être relié avec un autre.">Relier</label>
          <br/>
          <input type="radio" value="2" name="cr_inter" id="cr_inter_2" onchange="cr_inter_change(this)" title="Autoriser l'élément à être déplacé sur une zone cible"/>
          <label for="cr_inter_2" title="Autoriser l'élément à être déplacé sur une zone cible">Déplacer</label>
        </div>
        avec
        <input type="text" value="" id="cr_relie_id" onchange="cr_relie_id_change(this)" size="8" title="identifiant des autres éléments séparés par '|'"/>
      </div>
      <div class="cr_opt_ligne">
        nombre de points de l'item :
        <input id="cr_points" type="number" value="1" min="0" max="50" onchange="cr_points_change(this)" title="nombre de points de l'item"/>
      </div>
    </div>
    
    <div class="sep">&nbsp;</div>
    
    <div class="cr_opt_bloc">
      Code html :<br/>
      <textarea id="cr_html" readonly></textarea>
    </div>
    
    <div class="sep">&nbsp;</div>
    
    <div id="gpl"><img id="exotice2" src="../../exotice.svg" /><br/><img id="gplimg" src="../../icons/gpl-v3-logo-nb.svg" /><br/>© A. RENAUDIN 2016<br />v <?php echo VERSION() ?></div>
  </div>
</div>
<div id="cm_aligne" class="cm">
  <img src="../../icons/aligne_v_g.svg" title="aligner les objets à gauche" onmousedown="cr_aligne_change('1')"/>
  <img src="../../icons/aligne.svg" title="centrer les objets" onmousedown="cr_aligne_change('2')"/>
  <img src="../../icons/aligne_v_d.svg" title="aligner les objets à droite" onmousedown="cr_aligne_change('3')"/><br/>
  <img src="../../icons/aligne_h_h.svg" title="aligner les objets en haut" onmousedown="cr_aligne_change('4')"/>
  <img src="../../icons/aligne_h_c.svg" title="centrer les objets" onmousedown="cr_aligne_change('5')"/>
  <img src="../../icons/aligne_h_b.svg" title="aligner les objets en bas" onmousedown="cr_aligne_change('6')"/>
</div>
<div id="cm_repart" class="cm">
  <img src="../../icons/repartit.svg" title="répartir verticalement" onmousedown="cr_repart_change('1')"/>
  <img src="../../icons/repartit_h.svg" title="répartir horizontalement" onmousedown="cr_repart_change('2')"/>
</div>
<div id="cm_plans" class="cm">
  <img src="../../icons/plans_0.svg" title="mettre en arrière-plan" onmousedown="cr_plans_change('2')"/>
  <img src="../../icons/plans_1.svg" title="mettre au premier plan" onmousedown="cr_plans_change('1')"/>
  <img src="../../icons/plans_2.svg" title="reculer d'un plan" onmousedown="cr_plans_change('3')"/>
  <img src="../../icons/plans_3.svg" title="avancer d'un plan" onmousedown="cr_plans_change('4')"/>
</div>
</body>
</html>
