<?php
if (isset($_GET['mod'])) {
  $langname = "en_us";
  $modid = $_GET['mod'];
  $moden_path = "data/lang/" . $modid . "/en_us.json";
  $modja_path = "data/lang/" . $modid . "/$langname.json";
  $mcen_path = "data/lang/minecraft/en_us.json";
  $mcja_path = "data/lang/minecraft/$langname.json";
  $recipepath = "recipes/" . $modid . "/";

  $langmap = [];
  
  $langmap[$modid]['en'] = json_decode(file_get_contents($moden_path), true);
  $langmap[$modid]['ja'] = json_decode(file_get_contents($modja_path), true);
  $langmap["minecraft"]['en'] = json_decode(file_get_contents($mcen_path), true);
  $langmap["minecraft"]['ja'] = json_decode(file_get_contents($mcja_path), true);
  
  if (file_exists($recipepath)) {
    $mod_info = json_decode(file_get_contents($recipepath . "/mod.json"), true);
    echo "<h1>" . $mod_info['name'] . "</h1>";
    echo "<textarea cols='250' rows='50'>";
    foreach (glob($recipepath . "/*.png") as $pngpath) {
      $info = pathinfo($pngpath);
      $jsonpath = dirname($pngpath) . '/' . $info['filename'] . ".json";
      $recipe = json_decode(file_get_contents($jsonpath), true);
      $arr = explode(':', $recipe['result']);
      $namespace = $arr[0];
      $namepath = $arr[1];

      $itemids = [];
      $lang = [];
      $count = [];
      foreach ($recipe as $key => $itemid) {
        if ($key == "result") continue;
        if ($itemid == "air") continue;
        $arr = explode(':', $itemid);
        if (!isset($arr[1])) {
          $arr[1] = $arr[0];
          $arr[0] = "minecraft";
        }

        if (!in_array($itemid, $itemids))
          $itemids[] = $itemid;

        if (!isset($langmap[$arr[0]])) {
          $lang[$itemid]['ja'] = json_decode(file_get_contents("data/lang/$arr[0]/$langname.json"), true);
          $lang[$itemid]['en'] = json_decode(file_get_contents("data/lang/$arr[0]/en_us.json"), true);
        }
        
        $lang[$itemid] = $langmap[$arr[0]]['ja'][$arr[0] . "." . $arr[1]];
        
        if (!isset($count[$itemid])) $count[$itemid] = 0; 
        ++$count[$itemid];
      }

      $material = '';
      foreach ($itemids as $itemid) {
        $material .= $lang[$itemid] . ($count[$itemid] == 1 ? '' : "x" . $count[$itemid]) . "&br;";
      }
      $material = substr($material, 0, -4);
      
      echo "|~" . $langmap[$namespace]['ja'][$namespace . "." . $namepath] . "&br;" . $langmap[$namespace]['en'][$namespace . "." . $namepath] . "|BGCOLOR(#C6C6C6):#img(https://mcrecipe.pitan76.net/?id=" . $modid . ":" . $info['filename'] . ")|$material|説明|\n";
    }
    echo "</textarea>";
  }
}