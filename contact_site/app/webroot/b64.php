<?php
/**
 * Created by PhpStorm.
 * User: masashi.shimizu
 * Date: 2019/01/30
 * Time: 17:07
 */

$img = file_get_contents('http://contact.sinclo/dat.png');

if($img) {
  echo base64_encode($img);
}
