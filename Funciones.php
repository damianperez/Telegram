public static  function botones_reply( $level )
{
	
	$menues =Funciones::armo_menu($level);
	foreach ( $menues as $mnu=>$item) 
	 {
			 //Funciones::dump($item, $chat_id );
			 $tit = $item['tit'];				
			 $items_mnu[]= ['text' => $item['text']   , 'callback_data' => $item['action'].';""'];	
	}
	$data['text']=$tit;
	//Funciones::dump( $items_mnu, 0 );
	//Request::sendMessage($data); 
	 $items = array_map(function ($bot) {
		 
		 return [ 'text' => $bot['text'],'callback_data' => $bot['callback_data'],];}, $items_mnu);
	
	//Funciones::dump($items_mnu, $chat_id );
	$max_per_row  = 2; // or however many you want!
	$per_row      = sqrt(count($items));
	$rows         = array_chunk($items, $per_row === floor($per_row) ? $per_row : $max_per_row,true);
	//array_unshift( $rows, $principal);
	//Funciones::dump( $rows, 0 );
	$reply_markup = new InlineKeyboard([]);
		foreach($rows as $keyboard_button) {
				call_user_func_array([$reply_markup, 'addRow'], $keyboard_button);
		}
	return ( $reply_markup ); 
}
public function armo_menu( $level )
{
	/* Emojis */
	$emj_back="\xF0\x9F\x94\x99";
	$emj_id  = "\xF0\x9F\x86\x94";
	$emj_herramienta = "\xF0\x9F\x94\xA7";
	$emj_actualizar = "\xF0\x9F\x94\x84";
	$emj_cancelar = "\xE2\x9D\x8C";
	$emj_sos = "\xF0\x9F\x86\x98";
	$emj_satelite = "\xF0\x9F\x93\xA1";
	/* Items */
	$volver 	= array('caption'=>"$emj_back ".Translation::of("Volver") 	,'action'=>'mnu;M');
	$cancelar 	= array('caption'=>"$emj_cancelar Cancelar", 'action'=>'cmd;cancel');
	$precios 	= array('caption'=>"$emj_id Precios" 	,'action'=>'cmd;precios');
	
	
	$publicar 	= array('caption'=>Translation::of("publicar") 		,'action'=>'cmd;publicar');
	$help	  	= array('caption'=>"$emj_sos ".Translation::of("help"),'action'=>'cmd;help');	
	$lang		= array('caption'=>Translation::of("Lang")			,'action'=>'cmd;lang');
	
	
	$config   = array('caption'=>"$emj_herramienta Cofiguracion" ,'action'=>'mnu;M_CONF');
	$pos	= array('caption'=>"$emj_satelite Posicion",  'action'=>'cmd;posicion');
	$avisos	= array('caption'=>'Avisos', 	'action'=>'mnu;M_AVISOS'); 
	$reconstruir= array('caption'=>'Reconstruir', 'action'=>'cmd;reconstruir'); 
	$borrar		= array('caption'=>'Borrar', 'action'=>'cmd;borrar'); 
    /* Menu's */
	$M		=array('tit'=>'Menu Principal',
					'ret'=>'',
					'items'=>array($publicar,$help,$lang) );
	$M_CONF	=array('tit'=>'Configuracion',
					'ret'=>'M',
					'items'=>array($lang,$pos,$avisos,$volver));
	$M_AVISOS=array('tit'=>'Avisos',
					'ret'=>'M_CONF',
					'items'=>array($reconstruir,$borrar,$volver));
	
	
	$resultado=array();;
	$elegido = $M;
	if (isset($$level)) $elegido = $$level; 
	 
	foreach ( $elegido['items'] as $it=>$obj )
		{
	$resultado[]=array('tit'=>$elegido['tit'],'text'=>$obj['caption'],'action'=>$obj['action']);
		}
	
	return $resultado;	 
}
