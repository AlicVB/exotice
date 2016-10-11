<?php
  header("Content-Type: text/plain"); // Utilisation d'un header pour spécifier le type de contenu de la page. Ici, il s'agit juste de texte brut (text/plain). 
  $user = (isset($_GET["user"])) ? $_GET["user"] : NULL;
  $exoid = (isset($_GET["exoid"])) ? $_GET["exoid"] : NULL;
  $v = (isset($_GET["v"])) ? $_GET["v"] : NULL;
  
  if ($user && $exoid) 
  {
    // nom du fichier de sauvegarde
    $exoid = basename($exoid);
    $fic = "../../sauvegardes/$user/$exoid.txt";
    //et on réécrit le tout dans le fichier
    file_put_contents($fic, $v);
  }
?>