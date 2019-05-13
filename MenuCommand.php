<?php 
namespace Longman\TelegramBot\Commands\UserCommands;
use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

//	use Longman\TelegramBot\Entities\Keyboard;
//use Longman\TelegramBot\Entities\KeyboardButton;

use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Translation\Translation; 
use Longman\TelegramBot\Funciones;
error_reporting(E_ALL);

class MenuCommand extends UserCommand
{
   
    protected $name = 'Menu';
    protected $description = 'Menu de opciones';
    protected $usage = '/Menu';
    protected $version = '0.1.0';

    public function execute()
    {
		
		if ($this->getCallbackQuery() !== null) {
			 //$message =  $update->getMessage();
             $message  = $this->getCallbackQuery()->getMessage();
			 $chat    =$this->getCallbackQuery()->getMessage()->getChat();
			 $user    = $chat;
			 $chat_id =  $this->getCallbackQuery()->getMessage()->getChat()->getId();
			 $user_id = $chat_id;
			 $text = '';
		}
		else
		{
				$message = $this->getMessage() ?: $this->getEditedMessage();
				$chat    = $message->getChat();
				$user    = $message->getFrom();
				$chat_id = $chat->getId();
				$user_id = $user->getId();
				$text    = trim($message->getText(true));
        }
		
        $update         = $this->getUpdate();
		$data = [
            'chat_id'      => $chat_id,
			'parse_mode' => 'markdown',
            'text'         => "\xf0\x9f\x91\x87".' *Menu* '."\xf0\x9f\x91\x87",
           
        ];
		 
	  	$f=new Funciones();
	  	$lenguaje_actual= $f->get_lenguaje_actual( $user_id );
	  	Translation::forceLanguage($lenguaje_actual);
		
	  	
		$data['text'] = Funciones::titulo_menu( 'M' );
		$reply_markup=Funciones::botones_reply( 'M' ); 

 		$data['reply_markup']= $reply_markup; 
        
		
	  //Funciones::debug_a_admins_php('yo',$reply_markup );

		return Request::sendMessage($data); 
       
    }
}

