<form method="POST" enctype="multipart/form-data">
  <input type="file" name="file" /><br />
  レシピID (任意): <input type="text" name="filename" /><br />
  MOD ID: <input type="text" name="modid" /><br />
  アイテム(完成後) ID: <input type="text" name="itemid" /><br />

  <textarea name="data" rows="3" cols="50"></textarea>
  <input type="submit" />
</form>
<?php
  if (!isset($_FILES['file'])) {
    exit;
  }
  $dir = "./recipes/" . (isset($_POST['modid']) ? $_POST['modid'] : "free");
  if (!file_exists($dir) && !mkdir($dir)) {
    echo "Failed Making directory...";
    exit;
  }
  $path = $dir . "/" . (isset($_POST['filename']) && !empty($_POST['filename']) ? $_POST['filename'] . ".png" : $_FILES['file']['name']);
  
  if (!move_uploaded_file($_FILES['file']['tmp_name'], $path)) {
    echo 'Failed Uploading file...';
    exit;
  }

  $src_img = imagecreatefrompng($path);
  $width = 300 - 16;
  $height = 132 - 16;
  $create_img = imagecreatetruecolor($width, $height);
  imagecopyresampled($create_img, $src_img, 0, 0, 8, 8, $width, $height, $width, $height);
  $color = imagecolorat($create_img, 0, 0);
  imagefilledrectangle($create_img, $width - 32, $height - 32, $width, $height, $color);
  imagepng($create_img, $path);

  $data = $_POST['data'];
  $data = str_replace("\r\n", ",", $data);
  $data = str_replace("\n", ",", $data);
  $data = str_replace("\r", ",", $data);
  $arr = explode(",", $data);
  $recipe = [];
  $c = 0;
  foreach ($arr as $item) {
    ++$c;
    if (preg_match("/^[1-9]+$/", $item)) {
      $i = intval($item) - 1;
      $item = $arr[$i];
    }
    $recipe[$c] = $item;
  }
  $recipe['result'] = $_POST['itemid'];
  $info = pathinfo($path);
  file_put_contents(dirname($path) . '/' . $info['filename'] . '.json', json_encode($recipe, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
?>