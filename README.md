# Telegram Menu
Telegram Inlinne Keyboard Menu 

Facts
  Button menu:
   - each Bm can return to caller menu
   - each Bm has own rows and cols configuration
  
  - the buttons have caption and action.
   
  - the action can be 
        cmd      --> run a command 
        mnu      --> Displays a new menu button
        url      
        text
        login_url
          
  - callbackquerycmd receive the action in a switch and parse others params 
        
Bmnu.php is a class that
      creates and returns a set of buttons 
      ready to be assigned to $data['reply_markup']
      
BmnuCommand : Command for create menus and to 'start' the first default menu 

Usage: 
  /bmnu
  

inside callbackquery 

	$pizza = $callback_data; 
	
	$porciones = explode(";", $pizza); 
	
	$i=0; foreach ( $porciones as $parametro ) { 
		if ( $i == 0 ) $func = $parametro; 
		$var = 'par'.$i; 
		$$var = $parametro; $i++; 
		} 
		
	// now we have $par1,$par2..........
	

   case "bmnu":	// the action was bmnu;WEATHER
   
				 $data['parse_mode'] = 'HTML';				
				 $data['callback_query_id'] = $callback_query_id;
				 $data['message_id']  = $callback_query->getMessage()->getMessageId();
				
				$menu = BmnuCommand::armar_menu( $par1, $par2, $par3 );
            $data['reply_markup']= $menu->show(); 
				
            /*
            at this point, reply_markup have all buttons we need
            add text or photo or whatever and 
            return
            */
            $data['text']= ......
				//return Request::editMessageMedia($data);				
				//return Request::sendMessage($data); 
   
   case "pag":   // Wants to paginating, sending menu name and page number
			     
				$data['text']='showing pag '.$par2.'';		  
				Request::answerCallbackQuery($data);					 
							 
				$data['parse_mode'] = 'HTML';
				$data['callback_query_id'] = $callback_query_id;
				$data['message_id']  = $callback_query->getMessage()->getMessageId();		
            
            
				$menu = BmnuCommand::armar_menu($cat,intval($par2),0);				
				$menu->actual_item = $nro;
				$menu->item_id = $nro;
				
				$data['reply_markup']= $menu->show(); 
             $data['text']= ......
				//return Request::editMessageMedia($data);				
				//return Request::sendMessage($data); 
            
            
            
   Functions
     
