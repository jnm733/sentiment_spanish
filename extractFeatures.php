<?php
$tweets = array();
//$xml = file_get_contents("training.xml");
$xml = file_get_contents("test-tagged.xml");

$corpus = new SimpleXMLElement($xml);

$features = array();
$stopwords = array('un','una','unas','unos','uno','sobre','todo','tambien','tras','otro','algun','alguno','alguna','algunos','algunas','ser','es','soy','eres','somos','sois','estoy','esta','estamos','estais','estan','como','en','para','atras','porque','por que','estado','estaba','ante','antes','siendo','ambos','pero','por','poder','puede','puedo','podemos','podeis','pueden','fui','fue','fuimos','fueron','hacer','hago','hace','hacemos','haceis','hacen','cada','fin','incluso','primero','desde','conseguir','consigo','consigue','consigues','conseguimos','consiguen','ir','voy','va','vamos','vais','van','vaya','gueno','ha','tener','tengo','tiene','tenemos','teneis','tienen','el','la','lo','las','los','su','aqui','mio','tuyo','ellos','ellas','nos','nosotros','vosotros','vosotras','si','dentro','solo','solamente','saber','sabes','sabe','sabemos','sabeis','saben','ultimo','largo','bastante','haces','muchos','aquellos','aquellas','sus','entonces','tiempo','verdad','verdadero','verdadera','cierto','ciertos','cierta','ciertas','intentar','intento','intenta','intentas','intentamos','intentais','intentan','dos','bajo','arriba','encima','usar','uso','usas','usa','usamos','usais','usan','emplear','empleo','empleas','emplean','ampleamos','empleais','valor','muy','era','eras','eramos','eran','modo','bien','cual','cuando','donde','mientras','quien','con','entre','sin','trabajo','trabajar','trabajas','trabaja','trabajamos','trabajais','trabajan','podria','podrias','podriamos','podrian','podriais','yo','aquel');
$pronombres = array('adonde','adonde','algo','alguien','alguna','algunas','alguno','algunos','ambas','ambos','aquel','aquel','aquella','aquella','aquellas','aquellas','aquello','aquellos','aquellos','bastante','bastantes','como','como','conmigo','consigo','contigo','cual','cual','cual','cuales','cuales','cualesquiera','cualquiera','cuando','cuando','cuanta','cuanta','cuantas','cuantas','cuanto','cuanto','cuantos','cuantos','cuya','cuyas','cuyo','cuyos','demas','demasiada','demasiadas','demasiado','demasiados','donde','donde','el','ella','ellas','ello','ellos','esa','esa','esas','esas','ese','ese','eso','esos','esos','esta','esta','estas','estas','este','este','esto','estos','estos','estotra','estotro','idem','idem','la','las','le','les','lo','lo','los','me','media','medias','medio','medios','mi','misma','mismas','mismo','mismos','mucha','muchas','mucho','muchos','nada','nadie','ninguna','ningunas','ninguno','ningunos','nos','nosotras','nosotros','os','otra','otras','otro','otros','poca','pocas','poco','pocos','que','que','que','quien','quien','quienes','quienes','quienesquiera','quienquier','quienquiera','se','si','tal','tales','tanta','tantas','tanto','tantos','te','ti','toda','todas','todo','todos','tu','una','unas','uno','unos','usted','ustedes','varias','varios','vos','vosotras','vosotros','yo');
$preposiciones = array('a','al','ante','bajo','cabe','con','contra','de','del','desde','en','entre','hacia','hasta','para','por','segun','sin','so','sobre','tras');

foreach($corpus as $tweet)
{
    if($tweet->sentiments->polarity->value->__toString() != 'NONE'){
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
        //array_push($tweets, array('content' => $content, 'class' => $tweet->sentiments->polarity->value->__toString()));
        foreach($content as $word) {
            if(strlen($word) > 1 and !in_array($word, $stopwords) and !in_array($word, $pronombres) and !in_array($word, $preposiciones)){
                if(isset($features[$word]))
                    $features[$word] += 1;
                else
                    $features[$word] = 1;
            }
        };
    }
}

asort($features);
$umbral = 20;
foreach($features as $it => $feature)
{
    if($feature < $umbral)
        unset($features[$it]);
    else
        break;
}

//var_dump($features);die;
$myfile = fopen("features.txt", "w") or die("Unable to open file!");
foreach($features as $it => $feature)
{
    fwrite($myfile, $it.'-');
}
fclose($myfile);



