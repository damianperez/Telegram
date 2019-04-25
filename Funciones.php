<?php
namespace Longman\TelegramBot;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\InlineKeyboard;
use PDO;

class Menu_item
			{
				public $caption = 'opcion x';
				public $action = 'lnk x';			
				public $tipo =   'mnu';
				public $regresa = '';

			};
class Funciones {
public function titulo_menu( $level )
{
	$menues =Funciones::armo_menu($level);
	foreach ( $menues as $mnu=>$item) 
	 {			 
			 $tit = $item['tit'];						
	}
	return $tit;
}
public function botones_reply( $level )
{
	
	$menues =Funciones::armo_menu($level);
	foreach ( $menues as $mnu=>$item) 
	 {
			 //Funciones::dump($item, $chat_id );
			 $tit = $item['tit'];				
			 $items_mnu[]= ['text' => $item['text']   , 'callback_data' => $item['action'].';""'];	
	}
	$data['text']=$tit;
	//Request::sendMessage($data); 
	 $items = array_map(function ($bot) {return [ 'text' => $bot['text'],'callback_data' => $bot['callback_data'],];}, $items_mnu);
	
	//Funciones::dump($items_mnu, $chat_id );
	$max_per_row  = 2; // or however many you want!
	$per_row      = sqrt(count($items));
	$rows         = array_chunk($items, $per_row === floor($per_row) ? $per_row : $max_per_row,true);
	array_unshift( $rows, $principal);
	$reply_markup = new InlineKeyboard([]);
		foreach($rows as $keyboard_button) {
				call_user_func_array([$reply_markup, 'addRow'], $keyboard_button);
		}
	return ( $reply_markup ); 
}
public function armo_menu( $level )
{
	$M1= new Menu_item;		$M1->caption='Publicar'; $M1->action='cmd;publicar'; 
	$M2= new Menu_item;		$M2->caption='Solo FOto'; $M2->action='cmd;solofoto'; 
	$M3= new Menu_item;		$M3->caption='Configuracion'; $M3->action='mnu;M_CONF'; 
	$M4= new Menu_item;		$M4->caption='Help'; 	 $M4->action='cmd;help'; 
	
	$volver= new Menu_item;	$volver->caption='Volver'; $volver->action ='mnu;M'; 
	$pos= new Menu_item;	$pos->caption='Posicion'; $pos->action ='cmd;posicion'; 
	$leng= new Menu_item;	$leng->caption='Lenguaje'; $leng->action='cmd;lang'; 
	$avisos= new Menu_item;	$avisos->caption='Avisos'; $avisos->action='mnu;M_AVISOS'; 
	
	$reconstruir= new Menu_item;	
	$reconstruir->caption='Reconstruir'; $reconstruir->action='cmd;reconstruir'; 
	$borrar= new Menu_item;	$borrar->caption='Borrar'; $borrar->action='cmd;borrar'; 		

	$M=array('tit'=>'Menu Principal','ret'=>'','items'=>array($M1,$M2,$M3,$M4));
	$M_CONF=array('tit'=>'Configuracion','ret'=>'M','items'=>array($leng,$pos,$avisos,$volver));
	$M_AVISOS=array('tit'=>'Avisos','ret'=>'Avisos','items'=>array($reconstruir,$borrar,$volver));
	
	
	$resultado=array();;
	$elegido = $M;
	if (isset($$level)) $elegido = $$level; 
	foreach ( $elegido['items'] as $obj )
		{
		$resultado[]=array('tit'=>$elegido['tit'],'text'=>$obj->caption,'action'=>$obj->action);
		}
	return $resultado;	 
}

public function test($string)
{
	
	
	return 'Te devuelvo tu string '.$string;	
}

public function listado_dengo()
{	
$pdo = DB::getPdo();     if (! DB::isDbConnected()) {  return false;      }
$sql = "select * from dengo ";   
$sth =   $pdo->prepare( $sql );
$sth->execute();
$result = $sth->fetchall(PDO::FETCH_ASSOC);
return $result;


}
public function crear_thumb( $original, $porcentaje = 0.5 )
{	
	list($ancho, $alto) = getimagesize($original);
	
	 
$ratio = $ancho / $alto;
if( $ratio > 1) {
    $nuevo_ancho = 100; //suppose 500 is max width or height
    $nuevo_alto = 100/$ratio;
}
else {
    $nuevo_ancho = 100*$ratio;
    $nuevo_alto = 100;
}

	
	//$nuevo_ancho = $ancho * $porcentaje;
	//$nuevo_alto = $alto * $porcentaje;
	// Cargar
	$thumb = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
	$origen = imagecreatefromjpeg($original);
	// Cambiar el tamaño
	imagecopyresized($thumb, $origen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
	// guardar
	$original_ch = substr( $original,0,-4).'_dengo_ch.jpg';
	if ( imagejpeg($thumb,$original_ch )) return ($original_ch); 
	return false;
}
function unichr($i) {
    return iconv('UCS-4LE', 'UTF-8', pack('V', $i));
}

  
public function get_lenguaje_actual( $id_usuario  )
{	
$pdo = DB::getPdo();     if (! DB::isDbConnected()) {  return false;      }
$sql = "select lenguaje_actual,language_code from  user  where  id =  ".$id_usuario;   
$sth =   $pdo->prepare( $sql );
$sth->execute();
$result = $sth->fetch(PDO::FETCH_ASSOC);
$lenguaje = $result['lenguaje_actual']; 
$lang_orig= $result['language_code'];

if ( empty($lenguaje) || $lenguaje=='') 
	{
	$lenguaje = $this->lenguaje_x_defecto( $lang_orig );
	}
//Funciones::debug_a_admins_php( $sql, $lang_orig );
return $lenguaje ;

}
public function lenguaje_x_defecto( $lang_orig )
{
$retorno='es-ES';
switch ($lang_orig) {
    case "en":
        $retorno='en-US';
        break;
    case "es":
        $retorno='es-ES';
        break;
	
    case "pt":
        $retorno='pt-BR';
        break;
    case "ru":
        $retorno='ru-RU';
        break;
	 case "fr":
        $retorno='en-US';
        break;
	 case "it":
        $retorno='en-US';
        break;
	 case "en":
        $retorno='en-US';
        break;
    default:
		$retorno='es-ES';
}
return $retorno;
	
}

public function set_lenguaje_actual( $id_usuario , $lenguaje )
{
$pdo = DB::getPdo();     if (! DB::isDbConnected()) {  return false;      }
$sql = "update user set lenguaje_actual = '".$lenguaje."' where  id =  ".$id_usuario;  	
$sth =   $pdo->prepare( $sql );
$status = $sth->execute();
if ( $status ) return $lenguaje;
return ('No pude cambiar '.$sql );
}
public function ListadoDengo()
{
$pdo = DB::getPdo();     if (! DB::isDbConnected()) {  return false;      }
$sql = "select * from dengo  ";
$sth =   $pdo->prepare( $sql );
$sth->execute();
return   $sth->fetchAll(PDO::FETCH_ASSOC);
}
public static function extract_emails_from($string){
  $pattern = '/[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}/';
  preg_match_all($pattern, $string, $matches);  $mails[]=$matches[0];  $matches=array();  
  $pattern = '/[A-Za-z0-9._%+-]+ @ [A-Za-z0-9.-]+ \. [A-Za-z]{2,4}[[:space:]][^\.]/';
  preg_match_all($pattern, $string, $matches);  $mails[]=$matches[0];  $matches=array();  
  $pattern = '/[A-Za-z0-9._%+-]+ @ [A-Za-z0-9.-]+ \. [A-Za-z]{2,4}[[:space:]][\.] [A-Za-z]{2,4}[[:space:]]/';
  preg_match_all($pattern, $string, $matches);  $mails[]=$matches[0];  $matches=array();  
  $pattern = '/[A-Za-z0-9._%+-]+ @[[:space:]]\w+ (\.)+ \w* (\.)+ \w* (\.)+ \w*/';
  preg_match_all($pattern, $string, $matches);  $mails[]=$matches[0];  $matches=array();  
  $final=array();
  foreach ( $mails as $encontrado )
       foreach ( $encontrado as $este=>$email )
	   		{
	        $email = str_replace(" ", "", $email );
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) 
 				$final[$email]=$email;			
            
			}
  
  return $final;
}
public static function extract_urls_from($string){
$matches=array();
$urls=array();
$pattern= '/( www \. )+[a-z0-9]+ \. [a-z]{2,5}/';
preg_match_all($pattern,$string, $matches); $urls[]=$matches[0];  $matches=array();  

$pattern= '/[^\@](www\.|http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)+[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?/';
preg_match_all($pattern,$string, $matches); $urls[]=$matches[0];  $matches=array();  

$final=array();
foreach ( $urls as $encontrado )
       foreach ( $encontrado as $este=>$email )
	   		{
	        $email = str_replace(" ", "", $email );
//			if (filter_var($email, FILTER_VALIDATE_URL)) 
 				$final[$email]=$email;			
			}
  return $final;
}
public static function dump($data, $chat_id = null)
{
    $dump = var_export($data, true);

    // Write the dump to the debug log, if enabled.
    TelegramLog::debug($dump);

    // Send the dump to the passed chat_id.
    if ($chat_id !== null || (property_exists(self::class, 'dump_chat_id') && $chat_id = self::$dump_chat_id)) {
        $result = Request::sendMessage([
            'chat_id'                  => $chat_id,
            'text'                     => $dump,
            'disable_web_page_preview' => true,
            'disable_notification'     => true,
        ]);

        if ($result->isOk()) {
            return $result;
        }

        TelegramLog::error('Var not dumped to chat_id %s; %s', $chat_id, $result->printError());
    }

    return Request::emptyResponse();
}
function msj_a_admins_php(   $quien, $msg )
{


$chatIds = array("662767623","480434336"); // Los destinatarios 

foreach ($chatIds as $chatId) {
	$data = array(   'chat_id' => $chatId,
	'text' => $quien. '==>>'.$msg ,
	'parse_mode' => 'HTML',
	'disable_web_page_preview'=> true);
	 $response = file_get_contents("https://api.telegram.org/bot$bot_api_key/sendMessage?" . http_build_query($data) );
}

return (  $obj->{'ok'} ) ; 

}
function debug_a_admins_php(   $quien, $msg )
{

//$chatIds = array("662767623","480434336"); // Los destinatarios 

foreach ($chatIds as $chatId) {
	$data = array(   'chat_id' => $chatId,
	'text' => 'Debug '.$quien. '  '.var_export($msg,true) ,
	'parse_mode' => 'HTML' );
	 $response = file_get_contents("https://api.telegram.org/bot$bot_api_key/sendMessage?" . http_build_query($data) );
}

return (  $obj->{'ok'} ) ; 

}
function msj_a_admins_bot(   $quien, $msg )
{

$chatIds = array("662767623","-1001354941719","-217218123"); // Los destinatarios  

foreach ($chatIds as $chatId) {
	$data = array(   'chat_id' => $chatId,
	'text' => $chatId.' ==>> '.$msg ,
	'parse_mode' => 'HTML' );

	$result = Request::sendMessage([
            'chat_id'                  => $chatId,
            'text'                     =>  'Dev::'.$chatId.' ==>> '.$msg ,
            'disable_web_page_preview' => true,
            'disable_notification'     => true,
        ]);

}
return Request::emptyResponse();
}
}
?>
