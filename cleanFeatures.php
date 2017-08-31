<?php
$tweets = array();
$xml = file_get_contents("training.xml");

$corpus = new SimpleXMLElement($xml);

$features = file_get_contents("features.txt");
$features = explode("-", $features);

foreach($corpus as $tweet)
{
    //Caracteres a minúscula
    $content = mb_strtolower($tweet->content->__toString());
    //Eliminamos menciones
    $content = preg_replace('(\@\w+)', '', $content);
    $content = str_replace('@', '', $content);
    //Eliminamos urls
    $content = preg_replace('/(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $content);
    //Eliminamos RTS
    $content = str_replace("rt :", "", $content);
    $content = str_replace("rt ", "", $content);
    //Eliminamos Hashtags
    $content = preg_replace('(\#\w+)', '', $content);
    //Eliminamos signos de puntuación
    $content = preg_replace("#[[:punct:]]#", "", $content);
    $content = str_replace('”', '', $content);
    $content = str_replace('“', '', $content);
    $content = str_replace('…', '', $content);
    //Eliminamos dígitos
    $content = preg_replace('/\d/', '', $content);
    //Eliminamos acentos
    $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
        'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
        'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
        'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
    $content = strtr($content, $unwanted_array );


}


