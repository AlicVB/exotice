<?php
  $user = (isset($_GET["user"])) ? $_GET["user"] : "";
  // infos sur le chemin du livre
  $livreid = basename(dirname($_SERVER['PHP_SELF']));
  $cat = basename(dirname(dirname($_SERVER['PHP_SELF'])));
  $root = "../../..";
  if ($cat == "livres")
  {
    $root = "../..";
    $cat = "";
  }
  
  // infos sur le livre
  $titre_livre = "";
  if (file_exists("livre.txt"))
  {
    // on récupère le titre du livre
    $infos = explode("\n", file_get_contents("livre.txt"));
    $titre_livre = $infos[0];
    
    // et les détails
    $coul = $infos[1];
    $aut = $infos[2];
    $img = $infos[3];
    
    //et les détails
    $details = "";
    for ($i=11; $i<count($infos); $i++)
    {
      if ($infos[$i] != "")
      {
        if ($i > 11) $details .= "<br/>";
        $details .= $infos[$i];
      }    
    }
  }
?> 
 
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $titre_livre ?></title>
    <link rel="stylesheet" href="intro.css">
    <script type="text/javascript" src="livre.js"></script>
  </head>
  
  <body style="background-color: <?php echo $coul ?>;" onresize="intro_img_load()">
    <div id="c1">
      <div id="titrelivre"><?php echo $titre_livre ?></div>
      <br/><div id="details"><?php echo $details ?></div>
      <div id="basdiv">
        <div id="gotxt">On y va !</div>
        <a href="livre.php?user=<?php echo $user ?>&exo=0"><img id="goimg" src="<?php echo $root ?>/icons/go-next.svg" /></a>
        <div id="bysa" title="livre créé par <?php echo $aut ?> -- licence Creative Commons CC-BY-SA"><img src="<?php echo $root ?>/icons/cc.svg" /><span> <?php echo $aut ?></span></div>
      </div>
    </div>
    <img id="aideimg" src="<?php echo $img ?>" onload="intro_img_load()"/>
    <div id="gpl"><img id="exotice" src="<?php echo $root ?>/exotice.svg" /><br/><img id="gplimg" src="<?php echo $root ?>/icons/gpl-v3-logo-nb.svg" /><br/>© A. RENAUDIN 2016</div>
  </body>
</html>

