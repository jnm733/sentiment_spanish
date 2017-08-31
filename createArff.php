<?php
$features = file_get_contents("features.txt");
$features = explode("-", $features);
$myfile = fopen("data-bool.arff", "w") or die("Unable to open file!");
fwrite($myfile, "@relation Rel".PHP_EOL);
foreach($features as $feature)
    fwrite($myfile, "@attribute ". $feature. " {0,1}".PHP_EOL);
fwrite($myfile, '@attribute classPolarity {P,NEU,N}'.PHP_EOL);
fwrite($myfile, '@data'.PHP_EOL);

$tweets = array();
$xml = file_get_contents("training.xml");

$corpus = new SimpleXMLElement($xml);

$data = array();
foreach($corpus as $tweet)
{
    if($tweet->sentiments->polarity->value->__toString() != 'NONE' and $tweet->sentiments->polarity->type->__toString() != 'DISAGREEMENT'){
        //Caracteres a minúscula
        $content = mb_strtolower($tweet->content->__toString());
        $content = explode(" ", $content);
        foreach($content as $it => $word){
            //Eliminamos acentos y signos de puntuación
            $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y',
                '(' => '', ')' => '', '¿' => '', '?' => '', '!' => '', '¡' => '', "'" => '', '"' => '', ',' => '', '.' => '', ';' => '', ':' => '', '-' => '', '+' => '', '/' => '', '*' => '', ']' => '', '[' => '', '^' => '', '`' => '', '´' => '', '{' => '', '}' => '', '|' => '');
            $content[$it] = strtr($word, $unwanted_array );
            //Eliminamos caracteres sueltos
            if(strlen($word) < 2)
                unset($content[$it]);
            //Eliminamos menciones
            else if(strpos($word, '@') !== false)
                unset($content[$it]);
            //Eliminamos Hashtags
            else if(strpos($word, '#') !== false)
                unset($content[$it]);
            //Eliminamos urls
            else if(strpos($word, 'http') !== false)
                unset($content[$it]);
            //Eliminamos etiquetas rt
            else if($word == "RT" or $word == "rt" or $word == "rt:" or $word == "RT:")
                unset($content[$it]);
            //Eliminamos dígitos
            else if(strpos($word, '1') !== false or strpos($word, '2') !== false or strpos($word, '3') !== false or strpos($word, '4') !== false or strpos($word, '5') !== false or strpos($word, '6') !== false or strpos($word, '7') !== false or strpos($word, '8') !== false or strpos($word, '9') !== false)
                unset($content[$it]);
        }
        //$class = $tweet->sentiments->polarity->value->__toString();
        if($tweet->sentiments->polarity->value->__toString() == 'P+' or $tweet->sentiments->polarity->value->__toString() == 'P')
            $class = 'P';
        else if($tweet->sentiments->polarity->value->__toString() == 'N+' or $tweet->sentiments->polarity->value->__toString() == 'n')
            $class = 'N';
        else
            $class = 'NEU';

        $line = "";
        //BOOLEANO
        foreach($features as $feature)
        {
            if(in_array($feature, $content) == false)
                $line .= '0,';
            else
                $line .= '1,';
        }
        //FRECUENCIA DE PALABRA
        /*foreach($features as $feature)
        {
            $cont = 0;
            foreach($content as $word){
                if($word == $feature)
                    $cont += 1;
            }
            $line .= $cont.',';
        }*/
        $line .= $class;
        fwrite($myfile, $line.PHP_EOL);
    }
}

fclose($myfile);

