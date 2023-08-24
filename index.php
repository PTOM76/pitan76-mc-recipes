<?php
  if (isset($_GET['id'])) {
    header("Content-Type: image/png");
    readfile("./recipes/" . str_replace(":", "/", $_GET['id']). ".png");
    exit;
  }
?>
<html>
  <head>
    <title>Pitan76 MCRecipes</title>
  </head>
  <body>
    <?php 
      foreach (glob("recipes/*") as $modid) {
        $mod_info = json_decode(file_get_contents($modid . "/mod.json"), true);
        echo "<h1>" . $mod_info['name'] . "</h1>";

        foreach (glob($modid . "/*.png") as $pngpath) {
          $info = pathinfo($pngpath);
          $jsonpath = dirname($pngpath) . '/' . $info['filename'] . ".json";
          $recipe = json_decode(file_get_contents($jsonpath), true);
          
          echo "<img src=\"./" . $pngpath . "\" />";
        }
      }
      
    ?> 
  </body>
</html>