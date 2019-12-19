<?php
namespace BMenu;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;

class Boton
{
	public $caption='';
	public $action='';
	public $url='';
	public $login_url='';
	public $text = false;
	
	public function __construct($caption,$action)
	{		
		//$this->url='';
		//$this->login_url='';
		if (empty($caption)) {  throw new Exception('Sin caption');      }
        if (empty($action))  {  throw new Exception('Sin action');       }
		
		$this->caption=$caption;
		if ( substr($action,0,3)=='url')
		 	{	
				$this->url=substr($action,4);		
			}
		
		if ( substr($action,0,9)=='login_url')   
		 	{	
				$this->login_url=substr($action,10);		
			}
		 
		$this->action=$action;		
	}
	
}
class BMnu 
{
	protected $id;
	protected $tit;
	protected $cols;
	protected $ret;	
	protected $emj_back;
	public $actual_item=0;  //Correlativo, para paginar
	public $item_id=0;      //ID
	protected $buttons=  array();		
	protected $photo= '';		
	protected $caption=  '';
	
    public function __construct($id,$tit='',$cols = array(),$ret='',$buttons=array(),$actual_item=-1)
    	{
		$this->id = $id;
		$this->actual_item = $actual_item;
		$this->emj_back = $this->emoji('U+1F519');
		$this->emj_satellite =$this->emoji("U+1F4E1");
		$this->emj_sos = $this->emoji("U+1F198");
		$this->emj_id  = $this->emoji("U+1F194");	
		
	    return $this;
		}

	public function get_Mnu($id)
   	{
		return json_decode(json_encode($this, true));
		return $this;

	}	
	public function getCod(){
		return $this->cod;
	}
	public function setCod($cod){
		$this->cod = $cod;
	}
	public function getTit(){
		return $this->tit;
	}
	public function setTit($tit){
		$this->tit = $tit;
	}
	public function getCols(){
		return $this->cols;
	}
	public function setCols($cols){
		$this->cols = $cols;
	}
	public function getRet(){
		return $this->ret;
	}
	public function setRet($ret){
		$this->ret = $ret;
	}
	public function getItem_id(){
		return $this->item_id;
	}
	public function setItem_id($item_id ){
		$this->item_id = $item_id ;
	}
	public function getButtons(){
		return $this->buttons;
	}
	public function setButtons($buttons){
		$this->buttons = $buttons;
	}
	public function add_btn( $caption,$action )
		{
			$this->buttons[] = new Boton($caption,$action);			
		}
	public function getPhoto(){
		return $this->photo;
	}
	public function setPhoto($photo){
		$this->cod = $photo;
	}
	public function getCaption(){
		return $this->caption;
	}
	public function setCaption($photo){
		$this->caption = $caption;
	}
 	public function show()
	{
	foreach ( $this->buttons as $item) 
	 {	
		 if ( !empty( $item->url))
			 {
			 $items_mnu[]= ['text' => $item->caption   , 'url' => $item->url];	
			 }
		 
	 	 elseif ( !empty( $item->login_url))
			 {
			 $items_mnu[]= ['text' => $item->caption   , 'login_url' => ['url'=>$item->login_url,'bot_username'=>'pizzero_bot'] ];	
			
			 } 
		 
		
		elseif ( !empty( $item->callback_data))
			 {
			 $items_mnu[]= ['text' => $item->caption   , 'callback_data' => $item->action.';""'];	
			 }
		elseif ($item->text )
			 {
			 $items_mnu[]= ['text' => $item->caption  ];	
			 }
		else
			 {
			  $items_mnu[]= ['text' => $item->caption   , 'callback_data' => $item->action.';""'];
			 }
	 }
	 /* if the menu is a submenu, return  */
	 if ( !empty( $this->ret ))
	 	$items_mnu[]= ['text' => $this->emj_back .' Back'   ,  'callback_data' => "bmnu;".$this->ret.';""'];	
	    
	$principal=array();
	
	$items = array_map(
			function ($bot) 
			{ 
			    
				if ( isset( $bot['url'] )) return [ 'text' => $bot['text'],'url' => $bot['url'],];
				if ( isset( $bot['login_url'] )) return [ 'text' => $bot['text'],'login_url' => $bot['login_url'],];
				if ( isset( $bot['callback_data'] )) return [ 'text' => $bot['text'],'callback_data' => $bot['callback_data'],];
				//if ( $bot['text']) return [ 'text' => $bot['text'],];
				return [ 'text' => $bot['text'],'callback_data' => $bot['callback_data'],];
			}, $items_mnu);

	$van=0;
	
	foreach ( $this->cols as $columna )
		{
		 $una_columna = array();
		 for($i = 0; $i < $columna; ++$i) 
			{				 
			if ( $van > count($items)-1) break;

			$una_columna[] = $items[ $van ];			
			$van = $van + 1;
			}

			$rows[] = $una_columna;
			if ( $van > count($items)-1) break;
			
		}
	//			var_export( $rows );
//		return $rows;
		$reply_markup = new InlineKeyboard([]);
		foreach($rows as $keyboard_button) {
				call_user_func_array([$reply_markup, 'addRow'], $keyboard_button);
		}
	return ( $reply_markup ); 

}
public  function emoji($code)
	{
		return ( $this->utf8(hexdec(str_replace("U+","", $code))));
	}
public   function utf8($num)
	{
		if($num<=0x7F)       return chr($num);
		if($num<=0x7FF)      return chr(($num>>6)+192).chr(($num&63)+128);
		if($num<=0xFFFF)     return chr(($num>>12)+224).chr((($num>>6)&63)+128).chr(($num&63)+128);
		if($num<=0x1FFFFF)   return chr(($num>>18)+240).chr((($num>>12)&63)+128).chr((($num>>6)&63)+128).chr(($num&63)+128);
		return '';
	}
     
	
}
?>
