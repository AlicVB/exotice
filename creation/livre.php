<?php
function recursiveRemoveDirectory($directory)
{
    foreach(glob("{$directory}/*") as $file)
    {
        if(is_dir($file)) { 
            recursiveRemoveDirectory($file);
        } else {
            unlink($file);
        }
    }
    rmdir($directory);
}
function recurse_copy($src,$dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                recurse_copy($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}
function free_path($fic)
{
  if (!file_exists($fic)) return $fic;
  $p1 = substr($fic, 0, -4);
  $p2 = substr($fic, -4);
  $i = 1;
  while (file_exists("$p1_$i_$p2"))
  {
    $i++;
  }
  return "$p1_$i_$p2";
}

  //on traite d'abord des actions à faire
  if (isset($_GET['action']) && isset($_GET['exo']) && isset($_GET['cat']) && isset($_GET['livre']))
  {
    $dos = "../livres";
    $cat = $_GET['cat'];
    if ($cat != "") $dos .= "/$cat";
    $dos1 = "$dos/{$_GET['livre']}";
    $dos .= "/{$_GET['livre']}/exos";
    $dos2 = "$dos/{$_GET['exo']}";
    
    switch ($_GET['action'])
    {
      case "remove":
        if (file_exists($dos2)) recursiveRemoveDirectory($dos2);
        break;
      case "rmimg":
        if (file_exists("$dos1/{$_GET['exo']}")) unlink("$dos1/{$_GET['exo']}");
        $infos = explode("\n", file_get_contents("$dos1/livre.txt"));
        if (count($infos)>3) $infos[3] = "";
        file_put_contents("$dos1/livre.txt", implode("\n", $infos));
        break;
      case "saveimg":
        $dest = free_path("$dos1/img/{$_FILES['iimg']['name']}");
        copy($_FILES['iimg']['tmp_name'], $dest);
        $infos = explode("\n", file_get_contents("$dos1/livre.txt"));
        if (count($infos)>3) $infos[3] = "img/".basename($dest);
        file_put_contents("$dos1/livre.txt", implode("\n", $infos));
        break;
      case "copie":
        $exos = glob("$dos/*" , GLOB_ONLYDIR);
        $id = ord(basename($exos[count($exos)-1]));
        $id += 1;
        $n = chr($id);
        recurse_copy($dos2, "$dos/$n");
        break;
      case "up":
        $exos = glob("$dos/*" , GLOB_ONLYDIR);
        $pos = array_search($dos2, $exos);
        if ($pos > 0)
        {
          $tmp = "$dos/tmp";
          $n = $exos[$pos-1];
          rename($n, $tmp);
          rename($dos2, $n);
          rename($tmp, $dos2);
        }
        break;
      case "down":
        $exos = glob("$dos/*" , GLOB_ONLYDIR);
        $pos = array_search($dos2, $exos);
        if ($pos < count($exos)-1)
        {
          $tmp = "$dos/tmp";
          $n = $exos[$pos+1];
          rename($n, $tmp);
          rename($dos2, $n);
          rename($tmp, $dos2);
        }
        break;
    }
  }
?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>exotice -- livres</title>
  <link rel="stylesheet" href="livre.css">
  <script type="text/javascript" src="../libs/jscolor.min.js"></script>
  <script type="text/javascript" src="livre.js"></script>
</head>

<body>
<?php
  function livre_creation($dos)
  {
    //on crée tout ce qu'il manque
    if (!file_exists("$dos")) mkdir("$dos", 0777, true);
    if (!file_exists("$dos/exos")) mkdir("$dos/exos", 0777, true);
    if (!file_exists("$dos/img")) mkdir("$dos/img", 0777, true);
    if (!file_exists("$dos/sons")) mkdir("$dos/sons", 0777, true);
    if (!file_exists("$dos/livre.php")) copy("livre_type/livre.php", "$dos/livre.php");
    if (!file_exists("$dos/livre.css")) copy("livre_type/livre.css", "$dos/livre.css");
    if (!file_exists("$dos/livre.js")) copy("livre_type/livre.js", "$dos/livre.js");
    if (!file_exists("$dos/bilan.php")) copy("livre_type/bilan.php", "$dos/bilan.php");
    if (!file_exists("$dos/compteur.php")) copy("livre_type/compteur.php", "$dos/compteur.php");
    if (!file_exists("$dos/intro.php")) copy("livre_type/intro.php", "$dos/intro.php");
    if (!file_exists("$dos/intro.css")) copy("livre_type/intro.css", "$dos/intro.css");
  }
  
  if (isset($_GET['cat']) && isset($_GET['livre']))
  {
    $cat = $_GET['cat'];
    $livre = $_GET['livre'];
    $dos = "../livres";
    if ($cat != "") $dos .= "/$cat";
    $dos .= "/$livre";
    livre_creation($dos);
    $titre = $livre;
    $aut = "";
    $coul = "#ffffff";
    $details = "";
    $img = "";
    //on essaie de lire le fichier descriptif
    if (file_exists("$dos/livre.txt"))
    {
      $infos = explode("\n", file_get_contents("$dos/livre.txt"));
      if (count($infos)>0) $titre = $infos[0];
      if (count($infos)>1) $coul = $infos[1];
      if (count($infos)>2) $aut = $infos[2];
      if (count($infos)>3) $img = $infos[3];
      for ($i=11; $i<count($infos); $i++)
      {
        if ($i>11) $details .= "\n";
        $details .= $infos[$i];
      }
    }
    else
    {
      file_put_contents("$dos/livre.txt", "$titre\n$coul\n\n\n\n\n\n\n\n\n");
    }
    echo "<div class=\"col\">\n";
    echo "<div class=\"titre\">informations</div>\n";
    echo "<table>";
    echo "<tr>";
    echo "<td class=\"td1\">Titre du livre</td>";
    echo "<td class=\"td2\"><input type=\"text\" id=\"ititre\" size=\"30\" value=\"$titre\" onchange=\"infos_change('$dos/livre.txt')\"/></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class=\"td1\">Auteur</td>";
    echo "<td class=\"td2\"><input type=\"text\" id=\"iaut\" size=\"30\" value=\"$aut\" onchange=\"infos_change('$dos/livre.txt')\"/></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class=\"td1\">Couleur de fond</td>";
    echo "<td class=\"td2\"><input class=\"jscolor {hash:true}\" type=\"text\" id=\"icoul\" value=\"$coul\" onchange=\"infos_change('$dos/livre.txt')\" /></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class=\"td1\">Détails</td>";
    echo "<td class=\"td2\"><textarea id=\"idetails\" onchange=\"infos_change('$dos/livre.txt')\">$details</textarea></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class=\"td1\">Image principale</td>";
    echo "<td class=\"td2\">";
    if ($img != "" && file_exists("$dos/$img")) echo "<img id=\"iimg\" src=\"$dos/$img\" title=\"$dos/$img\" onload=\"iimg_load(this)\"/><a href=\"livre.php?cat=$cat&livre=$livre&action=rmimg&exo=$img\"><img class=\"eimg\" src=\"../icons/window-close.svg\" title=\"supprimer l'image\"/></a>";
    else echo "<form id=\"iimg_form\" action=\"livre.php?cat=$cat&livre=$livre&action=saveimg&exo=\" method=\"POST\" enctype=\"multipart/form-data\"><input type=\"file\" id=\"iimg\" name=\"iimg\" onchange=\"infos_img_change(this)\"/></form>";
    echo "</td>";
    echo "</tr>";
    echo "</table>";
    echo "</div>\n";
    echo "<div class=\"col\">\n";
    echo "  <div class=\"titre\">exercices</div>\n";
    echo "<table>";
    $exos = glob("$dos/exos/*" , GLOB_ONLYDIR);
    for ($i=0; $i<count($exos); $i++)
    {
      //on lit les détails de l'exo
      if (file_exists("$exos[$i]/exo.txt"))
      {
        $exo = basename("$exos[$i]");
        $et = "";
        $econs = "";
        $ecoul = "transparent";
        $infos = explode("\n", file_get_contents("$exos[$i]/exo.txt"));
        if (count($infos)>0) $et = $infos[0];
        if (count($infos)>1) $econs = $infos[1];
        if (count($infos)>10) $ecoul = $infos[10];
        // on écrit le bloc qui correspond
        echo "<tr style=\"background-color:$ecoul;\">\n";
        echo "<td class=\"td2\"><a class=\"ea\" href=\"crea_exo/exo.php?cat=$cat&livre=$livre&exo=$exo\">";
        echo "$et ($exos[$i])</a></td>\n";
        echo "<td><a href=\"livre.php?cat=$cat&livre=$livre&action=remove&exo=$exo\"><img class=\"eimg\" src=\"../icons/window-close.svg\" title=\"supprimer l'exo\"/></a>\n";
        echo "<a href=\"livre.php?cat=$cat&livre=$livre&action=copie&exo=$exo\"><img class=\"eimg\" src=\"../icons/tab-new.svg\" title=\"copier l'exo\"/></a>\n";
        echo "<a href=\"livre.php?cat=$cat&livre=$livre&action=up&exo=$exo\"><img class=\"eimg\" src=\"../icons/go-up.svg\" title=\"monter l'exo\"/></a>\n";
        echo "<a href=\"livre.php?cat=$cat&livre=$livre&action=down&exo=$exo\"><img class=\"eimg\" src=\"../icons/go-down.svg\" title=\"descendre l'exo\"/></a>\n";
        echo "</td></tr>\n";
      }
    }
    echo "<tr><td class=\"enew\"><a class=\"enewa\" href=\"crea_exo/exo.php?cat=$cat&livre=$livre&exo=\">+ nouvel exercice...</a></td></tr>";
    echo "</table>";
    echo "</div>\n";
  }
?>
</body>
</html>