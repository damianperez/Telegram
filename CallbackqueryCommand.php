<?php
namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Funciones;
use Longman\TelegramBot\Entities\InputMedia\InputMediaPhoto;

use Longman\TelegramBot\Entities;

use Cmfcmf\OpenWeatherMap;
use Cmfcmf\OpenWeatherMap\Exception as OWMException;
use Http\Factory\Guzzle\RequestFactory;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

use BMenu\BMnu;

error_reporting(E_ALL);
class CallbackqueryCommand extends SystemCommand
{
    protected $name = 'callbackquery';
    protected $description = 'Reply to callback query';
    protected $version = '1.1.1';

	
	public function text_weather($weather,$style=0)
	{
		//Funciones::debug_a_admins_php(   'yo', $weather ); 
			$weather->tz = (float) $weather->tz;
								$tz = abs( $weather->tz );
								$i = new \DateInterval('PT'.$tz.'S');
								if ( $weather->tz  > 0 )
								{
									$weather->lastUpdate->add($i);
									$weather->sun->rise->add($i);
									$weather->sun->set->add($i);
								}
								else
								{
									$weather->lastUpdate->sub($i);
									$weather->sun->rise->sub($i);
									$weather->sun->set->sub($i);
								}
								
								
								$actual_caption =  $weather->city->name. ' ' . $weather->city->country.'  '. $weather->weather->description.PHP_EOL;
								$actual_caption .= "Current at <b>".$weather->time->from->format('H:i').'</b>'.PHP_EOL;	
								//$actual_caption .= $weather->time->from->format('H:i'). ' '.$weather->time->to->format('H:i');
								
								//$actual_caption .='Tz: '.$weather->tz.PHP_EOL;
								$actual_caption .='<pre>';
								$actual_caption .='Temp:     '.$weather->temperature->now.PHP_EOL;
								$actual_caption .= 'Minimum:  '.$weather->temperature->min.PHP_EOL;
								$actual_caption .= 'Maximum:  '.$weather->temperature->max.PHP_EOL;
								
								$actual_caption .= 'Pressure: '.$weather->pressure.PHP_EOL;
								$actual_caption .= 'Prcipitation: '.$weather->precipitation.'mm.'.PHP_EOL;
								$actual_caption .= 'Humidity: '.$weather->humidity.PHP_EOL;
								$actual_caption .= 'Wind: '.round($weather->wind->speed->getValue() * 1.943 ,1).' knts. '.
								$weather->wind->direction->getValue().'° '.$weather->wind->direction->getUnit().PHP_EOL;
								
								$actual_caption .='</pre>';
								$actual_caption .= 'Sun rise/set: '.$weather->sun->rise->format('H:i').'/'.$weather->sun->set->format('H:i').PHP_EOL;
								
								$actual_foto =  __DIR__."/../imagenes/weather/". $weather->weather->icon.'.png';			
				$actual_caption .= '<i>Last update: </i><b>'.$weather->lastUpdate->format('d.m.Y H:i').'</b>'.PHP_EOL;
				if (!file_exists( $actual_foto )) 	$actual_foto=__DIR__."/../imagenes/logo.jpg";
				
				//$actual_caption = var_export($weather);
				$data['photo']   = Request::encodeFile($actual_foto);		
				
				$data['text']=$actual_caption;
				
				$data['media'] = new InputMediaPhoto([
							'type' => 'photo',
							'caption' => $actual_caption,
							'parse_mode' => 'HTML',
							'media'   => Request::encodeFile($actual_foto ),
					      ]);
				return $data;
												
	}
	public function text_forecast($forecast,$nro,$style=0)
	{
		$tot = 0;
		$j=0;
		foreach ($forecast as $weather)
		{   
		    $tot = $tot + 1;			
			if ( $j==0 ) $current = $weather;
		    $weather->tz = $forecast->tz;
			$weather->tz = (float) $weather->tz;
			$tz = abs( $weather->tz );
			$i = new \DateInterval('PT'.$tz.'S');
			if ( $weather->tz  > 0 )
			{
				$weather->lastUpdate->add($i);
				$weather->sun->rise->add($i);
				$weather->sun->set->add($i);
				$weather->time->from->add($i);
			}
			else
			{
				$weather->lastUpdate->sub($i);
				$weather->sun->rise->sub($i);
				$weather->sun->set->sub($i);
				$weather->time->from->sub($i);
			}
			$f[$j]['day']= $weather->time->from->format('l jS F Y');
			$f[$j]['desc']= $current->city->name. ' ' . $current->city->country.' <b>'. $weather->weather->description.'</b>'.PHP_EOL;
			
			$f[$j]['from']= $weather->time->from->format('H:i');
			$f[$j]['Temp']= str_pad($weather->temperature->now->getValue(),5,' ',STR_PAD_BOTH); 
			$f[$j]['Minimum']=str_pad($weather->temperature->min->getValue(),5,' ',STR_PAD_BOTH); 
			$f[$j]['Maximum']=str_pad($weather->temperature->max->getValue(),5,' ',STR_PAD_BOTH); 
			
			$f[$j]['Pressure']=str_pad($weather->pressure->getValue(),5,' ',STR_PAD_LEFT); 
			$f[$j]['Precipitation']=str_pad($weather->precipitation->getValue(),5,' ',STR_PAD_LEFT); 
			$f[$j]['Humidity']=str_pad($weather->humidity->getValue(),5,' ',STR_PAD_LEFT); 
			$f[$j]['Windspeed']=str_pad(round($weather->wind->speed->getValue() * 1.943 ,1),5,' ',STR_PAD_BOTH); 
			$f[$j]['Winddir'] =str_pad($weather->wind->direction->getValue(),5,' ',STR_PAD_BOTH); 
			$f[$j]['Windunit']=str_pad($weather->wind->direction->getUnit(),5,' ',STR_PAD_BOTH); 
			
			$f[$j]['icon']= $weather->weather->icon;	
			$j = $j + 1;
		}
		/*
		1   1-3
		2   4-6
		3   7-9
		4  10-12
		*/
		
		
		
		
		
		
		
		$i = ( $nro * 3 ) - 2;			
			
			$actual_caption  ='<pre>';	
			$actual_caption .='     '. $f[$i]['from']      .'  '.$f[$i+1]['from']     . '  '.$f[$i+2]['from']. PHP_EOL;
			$actual_caption .='Temp '. $f[$i]['Temp']      .'  '.$f[$i+1]['Temp']     . '  '.$f[$i+2]['Temp'].' °C'. PHP_EOL;
			$actual_caption .='Pres '. $f[$i]['Pressure']      .'  '.$f[$i+1]['Pressure']  .'  '.$f[$i+2]['Pressure'].' hPa'. PHP_EOL;
			$actual_caption .='Prec '. $f[$i]['Precipitation'].'  '.$f[$i+1]['Precipitation'].'  '.$f[$i+2]['Precipitation'].' mm'. PHP_EOL;
			$actual_caption .='Hum  '. $f[$i]['Humidity'].'  '.$f[$i+1]['Humidity'].'  '.$f[$i+2]['Humidity'].' %'. PHP_EOL;
			
			$actual_caption .='Wind  '.$f[$i]['Windspeed'] .'  '.$f[$i+1]['Windspeed']. '  '.$f[$i+2]['Windspeed'].' kns'. PHP_EOL;
			$actual_caption .='      '.$f[$i]['Winddir']   .'  '.$f[$i+1]['Winddir']  . '  '.$f[$i+2]['Winddir'].' °'. PHP_EOL;
			$actual_caption .='      '.$f[$i]['Windunit']  .'  '.$f[$i+1]['Windunit'] . '  '.$f[$i+2]['Windunit']. PHP_EOL;
			
			$actual_iconset = $f[$i]['icon'].$f[$i+1]['icon'].$f[$i+2]['icon'];
			
			$actual_caption .='</pre>';
		
								
	$actual_iconset =  __DIR__."/../imagenes/weather/".$actual_iconset.".png";
	
	$actual_foto=__DIR__.'/../imagenes/weather/owm.png';								
	//$actual_foto =  __DIR__."/../imagenes/weather/10d10d10d.png";	
	
	$actual_foto  = $actual_iconset;
	//$actual_foto = $actual_iconset;
	/* Las fontos andan bien de 300*100
	   asi que las 5 columnas podrian ser iconos de (60*100)
	   */
			$actual_caption .=  $f[$i]['desc'];
			$actual_caption .= "Current at <b>".$f[$i]['day'].'</b>'.PHP_EOL;	
	   
				$actual_caption .= '<i>Last update: </i><b>'.$weather->lastUpdate->format('d.m.Y H:i').'</b>'.PHP_EOL;
				//$actual_caption .= $actual_iconset.PHP_EOL;
				if (!file_exists( $actual_foto )) 	$actual_foto=__DIR__."/../imagenes/logo.jpg";
				

				//$actual_caption = var_export($weather);
				$data['photo']   = Request::encodeFile($actual_foto);		
				
				$data['text']=$actual_caption;
				
				$data['media'] = new InputMediaPhoto([
							'type' => 'photo',
							'caption' => $actual_caption,
							'parse_mode' => 'HTML',
							'media'   => Request::encodeFile($actual_foto ),
					      ]);
						  
						  
/*						  
La Plata AR  clear sky
Current at 13:04
Sun rise/set: 05:31/19:57

       13:04  15:00 18:00 hs
Temp : 29.9   34.5  33.7 °C
Min  :  28     28    21  °C
Max  :  32     32    33  °C
Press: 1010   1020  1000 hPa
Prec :  0       0   0.12 mm.
Hum  :  38     39    35  %
Wind : 11.1   11.2  11.1 knts
       300     310   300 °
	   WNW     WNW   ENE
Last update: 09.12.2019 13:04
*/

				return $data;
												
	}
	
    public function execute()
    {
			
        $update         = $this->getUpdate();
        $callback_query = $update->getCallbackQuery();
        $callback_data  = $callback_query->getData();
		$callback_query_id = $callback_query->getId();
		
		$message = $callback_query->getMessage();
		$chat    = $message->getChat();
		$user    = $message->getFrom();
		$user_id   = $message->getFrom()->getId();
		$chat_id    =  $callback_query->getMessage()->getChat()->getId();
		
		$texto =  $callback_data;
		
		$par3 = '';
		
		$pizza  = $callback_data;
		$porciones = explode(";", $pizza);
		$i=0;
		foreach ( $porciones as $parametro )
		{	
			if ( $i == 0 ) $func = $parametro;			
			$var = 'par'.$i;
			$$var = $parametro;
			$i++;
		}
        // Only do something for the 'category' selection.
		$data = [
					'chat_id'    => $callback_query->getMessage()->getChat()->getId(),
					'message_id' => $callback_query->getMessage()->getMessageId(),
					  
				];
		 $data['text']='Ingresamos a :'.$func.';'.$par1.';'.$par2.';'.$par3.'';		 
		//Funciones::dump(   $message, 662767623 ); 
		 $data['callback_query_id'] = $callback_query_id;
		 $data['message_id']  = $callback_query->getMessage()->getMessageId();
		 //Request::answerCallbackQuery($data);	
		 
		 $actual_foto = __DIR__.'/../imagenes/weather/owm.png';
		switch ($func) {
			case "bmnu":				 
			     /* LLama a un menú. Como se supone que viene de un menú, ya tiene que tener foto
				 Faltaría agregar que foto lleva ese menú con ->getfoto y getcaption
				 */
				 //Func bmnu - ITM - WEATHER - 26 - ""'
				 $data['parse_mode'] = 'HTML';
				 $data['text']=$texto;
				 $data['callback_query_id'] = $callback_query_id;
				 $data['message_id']  = $callback_query->getMessage()->getMessageId();
				
				$menu = BmnuCommand::armar_menu( $par1, $par2, $par3 );

				
				 $item_id=0;
				 $actual_caption='Menu';
				 
				 $data['reply_markup']= $menu->show(); 
				//Funciones::debug_a_admins_php(   'yo', $data ); 
				
				 $data['photo']   = Request::encodeFile($actual_foto);		
				 $data['media'] = new InputMediaPhoto([
								  'type' => 'photo',
									'caption' => $actual_caption,
								   'parse_mode' => 'HTML',
								  'media'   => Request::encodeFile($actual_foto ),
							  ]);
				return Request::editMessageMedia($data);				
				//return Request::sendMessage($data); 
				  break;			
					
			case "pag":
			     /* Tiene un tiem seleccionado, pasar al proximo  ( par1 no se usa?*/			
				 // pag,WEATHER,4
				$data['text']='showing pag '.$par2.'';		  
				Request::answerCallbackQuery($data);					 
							 
				$data['parse_mode'] = 'HTML';
				$data['callback_query_id'] = $callback_query_id;
				$data['message_id']  = $callback_query->getMessage()->getMessageId();				 
				 
				 $cat = $par1;
				 $nro = $par2;
				 /****
				 Dado que el menu esta compuesto por 
				    foto 
					caption
					reply_markup
					
					Cuando llamo a un mnu como WEATHER me tiene que devolver las 3 cosas.
					Actualmente solo devuelve reply_markup y el resto se hace a mano
					Si no se hace esto, no se puede llamar al menu desde otros lugars.
					Implementar metodo getmenuphoto y getmenucaption
					
				*****/
				// Language of data (try your own language here!):
				

				$weather='';
				$query=array();
				//$query['lat']="-34.3320165";
				//$query['lon']= "-58.8415349";

				$latlon = Funciones::get_latlon_guardado_array( $chat_id   );
				$query['lat']=$latlon[0];
				$query['lon']=$latlon[1];
				$lang = 'en';

				// Units (can be 'metric' or 'imperial' [default]):
				$units = 'metric';
				//if ( $nro > 3 ) $nro = 3 ;
				if ( $nro < 0 ) $nro = 0 ;
				
				$httpRequestFactory = new RequestFactory();
				$httpClient = GuzzleAdapter::createWithConfig([]);
				// Create OpenWeatherMap object.
				$apikey="f7577c33470559de7baaee9db157f56a";
				$owm = new OpenWeatherMap($apikey, $httpClient, $httpRequestFactory);
				try {		
								//Funciones::dump(   $query, 662767623 ); 				
								if ( $nro == 0 )	
								{
									$weather = $owm->getWeather($query,$units, $lang);								 								
									/*
									$weather->time->from = $weather->lastUpdate;
									$weather->time->to = $weather->lastUpdate;
									*/
									//Funciones::dump(   $weather, 662767623 ); 		
									
									$weather->time->from =$weather->lastUpdate;
									$weather->time->to =  $weather->lastUpdate;
									$datos = $this->text_weather($weather,0);
								}
								else
								{
									$forecast = $owm->getWeatherForecast($query, $units, $lang, '', 5);							
									$datos = $this->text_forecast($forecast,$nro,0);
									
									
								}
								
					} 
					catch(OWMException $e) 
					{
								$actual_caption =  'OpenWeatherMap exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
					} 
					catch(\Exception $e) 
					{
								$actual_caption = 'General exception: ' . $e->getMessage() . ' (Code ' . $e->getCode() . ').';
					}
					
				
				
				
				$data['photo'] = $datos['photo'];
				$data['media'] = $datos['media'];								
				$data['text']= $datos['text'];	
				
				$menu = BmnuCommand::armar_menu($cat,intval($nro),0);				
				$menu->actual_item = $nro;
				$menu->item_id = $nro;
				
				$data['reply_markup']= $menu->show(); 
				//var_dump( $weather );
				
				return Request::editMessageMedia($data);
				//return Request::editMessageCaption($data);				
				//return Request::editMessageText($data);
				break;	
			case "cmd":	
				 $data['text']='Cmd '.$par1;
				 $data['callback_query_id'] = $callback_query_id;
				 $data['message_id']  = $callback_query->getMessage()->getMessageId();
				 Request::answerCallbackQuery($data);	
			 	return $this->telegram->executeCommand($par1);				
				break;
			case "url":				
				/*Por aca NUNCA pasa */
				 $data['text']='Acá va un link a '.$par1;
				 $data['callback_query_id'] = $callback_query_id;
				 $data['message_id']  = $callback_query->getMessage()->getMessageId();
			 	 Request::editMessageText($data);
				 return $this->telegram->executeCommand('mnu');				
				break;				  
			case "iq":
			     
				 $data['text']=$texto;
				 $data['callback_query_id'] = $callback_query_id;
				 $data['message_id']  = $callback_query->getMessage()->getMessageId();

				//return Request::sendMessage($data); 
				  break;			
			default:       			   
				$data['text']="No encontre datos de  $texto $par1";
				//return Request::editMessageText($data); 
				return Request::answerCallbackQuery($data);
				//return $this->telegram->executeCommand('menu');
				
			 	   
		}
		//return $this->telegram->executeCommand('menu');

		//return $this->telegram->executeCommand('iMenubutton');
		Funciones::dump('NO DEBERIA HABER LLEGADO', $chat_id );
		return Request::emptyResponse();
	
    }
	
}
