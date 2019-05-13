<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Funciones;

/**
 * Callback query command
 *
 * This command handles all callback queries sent via inline keyboard buttons.
 *
 * @see InlinekeyboardCommand.php
 */
 error_reporting(E_ERROR);
class CallbackqueryCommand extends SystemCommand
{
    
    protected $name = 'callbackquery';

    protected $description = 'Reply to callback query';
    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
  
     public function execute()
    {
        $update         = $this->getUpdate();
        $callback_query = $update->getCallbackQuery();
        $callback_data  = $callback_query->getData();
		$callback_query_id = $callback_query->getId();
		$par='';
		$message = $callback_query->getMessage();
		$chat    = $message->getChat();
		$user    = $message->getFrom();
		$user_id    = $message->getFrom()->getId();
		$chat_id =  $this->getCallbackQuery()->getMessage()->getChat()->getId();
		
		
		
		list($func, $par, $par2 ) = explode(";", $callback_data);
		/*Request::answerCallbackQuery([
					'callback_query_id'    => $callback_query_id,
					'message_id' => $callback_query->getMessage()->getMessageId(),
					'text'       => 'Buscando datos de '.$par. ' '. $func , 
				]);
		*/
		
        // Only do something for the 'category' selection.
		$data = [
					'chat_id'    => $callback_query->getMessage()->getChat()->getId(),
					'message_id' => $callback_query->getMessage()->getMessageId(),
					'text'       => 'te devolvi la category'. $par,  
				];
		$this->Conversation = new Conversation($user_id, $chat_id, $par);
		switch ($func) {
			case "mnu":
			 	 $texto=Funciones::titulo_menu( $par );
				 $data['text']=$texto;
				 $data['callback_query_id'] = $callback_query_id;
				 $data['message_id']  = $callback_query->getMessage()->getMessageId();
				 Request::answerCallbackQuery($data);
  
				$reply_markup=Funciones::botones_reply( $par );
				//Menu title
				$data['text']=Funciones::titulo_menu( $par );
				$data['reply_markup']= $reply_markup; 
				return Request::editMessageText($data);
				//return Request::sendMessage($data); 
				  break;				
			 	  
			case "cmd":				  
					$data['text']="Ejecutamos $par";
					$data['callback_query_id'] = $callback_query_id;
					$data['message_id']  = $callback_query->getMessage()->getMessageId();
					Request::answerCallbackQuery($data);
					return $this->telegram->executeCommand($par);				  
				  break;
			case "Lng":
				        //Translations
					$f=new Funciones();
			 		$f->set_lenguaje_actual($par,$par2 );
					$text =  $f->get_lenguaje_actual($par );
					Request::answerCallbackQuery([
							'callback_query_id'    => $callback_query_id,
							'message_id' => $callback_query->getMessage()->getMessageId(),
							'text'       => 'Lenguaje cambiado a '.$text , 
						]);
					/*  Request::editMessageText([
					'chat_id'    => $callback_query->getMessage()->getChat()->getId(),
					'message_id' => $callback_query->getMessage()->getMessageId(),
					'text'       =>$text,  
				]);*/
				 	//return $this->telegram->executeCommand('iMenubutton');
			 	   break;
				
			default:
       			  //return $this->telegram->executeCommand('iMenubutton');
				 return Request::editMessageText([
					'chat_id'    => $callback_query->getMessage()->getChat()->getId(),
					'message_id' => $callback_query->getMessage()->getMessageId(),
					'text'       => 'No encontre datos '. $par ,  
				]); 
				
			 	   
		}
		//return $this->telegram->executeCommand('iMenubutton');
		return Request::emptyResponse();
		
    }
}
