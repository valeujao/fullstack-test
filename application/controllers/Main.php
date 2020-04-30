<?php
defined('BASEPATH') OR exit('No direct script access allowed');  

class Main extends CI_Controller {  

    public function index()	{    
        
		$this->carrega_view('index');

    }

    public function find_temp(){        
            
        $api_key = '5f670d09098848589bc5ffd45290fe96';
        $headers = array('Content-Type:application/json');  

        $lat = $this->input->post('lat');
        $lon = $this->input->post('lon');
        $val = $this->input->post('val');        

        if($val == '0'){            
            
            $process = curl_init('api.openweathermap.org/data/2.5/weather?lat='.$lat.'&lon='.$lon.'&appid='.$api_key.'&units=metric');

        } else {

            $aux_val = explode(',', $val);         

            if(count($aux_val) > 1){
                $lat = $aux_val[0];
                $lon = $aux_val[1];            
                $process = curl_init('api.openweathermap.org/data/2.5/weather?lat='.$lat.'&lon='.$lon.'&appid='.$api_key.'&units=metric');
            } else {            
                $process = curl_init('api.openweathermap.org/data/2.5/weather?q='.$val.'&appid='.$api_key.'&units=metric');
            }
        }

		curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($process, CURLOPT_HEADER, 0);
		curl_setopt($process, CURLOPT_TIMEOUT, 30);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
		$return = curl_exec($process);
        curl_close($process);

        $obj = json_decode($return);

        $code_msg = $obj->cod;

        if($code_msg == '200'){

            $data['temp'] = $obj->main->temp;
            $data['city'] = $obj->name;
            $aux_icon = $obj->weather[0]->icon;
            $data['icon'] = 'http://openweathermap.org/img/wn/'.$aux_icon.'@2x.png';

            $aux_tempo = floor($data['temp']);

            if($aux_tempo > 30){
                $data['mood'] = 'party';
            }
            if($aux_tempo >= 15 && $aux_tempo <= 30){            
                $data['mood'] = 'pop';
            }
            if($aux_tempo >= 10 && $aux_tempo <= 14){           
                $data['mood'] = 'rock';
            }
            if($aux_tempo < 10){         
                $data['mood'] = 'classical';
            }

            $data['spotify'] = $this->getSpotify($data['mood']);

            $data['code_return'] = 1;
        } else {
            
            $data['code_return'] = 0;
        }

        echo json_encode($data, true);

    } 

    function getSpotifyToken(){
        $client_id = '892772438c32424ba2f0a468d601797c'; 
        $client_secret = 'babf503e8994400d847c10d316cb4bda'; 

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,            'https://accounts.spotify.com/api/token' );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($ch, CURLOPT_POST,           1 );
        curl_setopt($ch, CURLOPT_POSTFIELDS,     'grant_type=client_credentials' ); 
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Authorization: Basic '.base64_encode($client_id.':'.$client_secret))); 
        $result=curl_exec($ch);        
        curl_close($ch);

        return json_decode($result);
    }

    function getSpotify($type){


        $getToken = $this->getSpotifyToken();
        $auth = $getToken->access_token;

        $headers = array(
            'Content-Type:application/x-www-form-urlencoded',
            'Accept: application/json',
            'Authorization: Bearer '.$auth
        );

        // random offset to prevent same playlists everytime //
        $offset = rand(0,1995);        

        $process = curl_init('https://api.spotify.com/v1/search?q='.$type.'&offset='.$offset.'&type=playlist&limit=5');
		curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($process, CURLOPT_HEADER, 0);
		curl_setopt($process, CURLOPT_TIMEOUT, 30);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
		$return = curl_exec($process);
        curl_close($process);

        $obj = json_decode($return);

        $array_aux = $this->restructureFilesArray($obj);
        $array_playlists = $this->restructureFilesArray($array_aux['items']);

        $cont_pl = 1;
        $list_playlist = array();

        foreach($array_playlists as $item){            

            $new_playlist = array(
                'id'    => $cont_pl,
                'id_pl' => $item['playlists']->id,
                'name'  => $item['playlists']->name,
                'link'  => $item['playlists']->external_urls->spotify,
            );
            
            array_push($list_playlist, $new_playlist);
            $cont_pl++; 
        }
        
        $list_item_playlist = array();

        foreach($list_playlist as $item){

            $id_playlist = $item['id'];

            $process = curl_init('https://api.spotify.com/v1/playlists/'.$item['id_pl'].'/tracks?limit=3');
            curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($process, CURLOPT_HEADER, 0);
            curl_setopt($process, CURLOPT_TIMEOUT, 30);
            curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
            $return = curl_exec($process);
            curl_close($process);

            $obj_musics = json_decode($return);
            $array_aux_musics = $obj_musics->items; 
            $array_musics = $this->restructureFilesArray($array_aux_musics);     
            
            foreach($array_musics['track'] as $item){                

                $new_music = array(
                    'id_pl'     => $id_playlist,
                    'name'      => $item->name,
                    'link'      => $item->external_urls->spotify,
                );

                array_push($list_item_playlist, $new_music);
            }            

        }

        $playlisttoFront = $this->mountPlaylistCode($list_playlist, $list_item_playlist);

        return $playlisttoFront;

    }

    function mountPlaylistCode($list, $musics){

        $html = '<div id="accordion">';

        foreach($list as $playlist){            

            $html   .= '<div>
                            <div>
                                <p class="mb-0 py-2 f01 text-left h6 panel-title" data-toggle="collapse" data-target="#playlist_'.$playlist['id'].'" aria-expanded="false">
                                    <i class="fas fa-play mr-2 color_1"></i> '.$playlist['name'].'
                                </p>
                            </div>  
                            <div id="playlist_'.$playlist['id'].'" class="collapse" data-parent="#accordion">
                                <div class="text-left pl-3 pb-2">';

                                    foreach($musics as $item){

                                        if($item['id_pl'] == $playlist['id']){

                                            $html .= '<i class="fas fa-music mr-2 color_1 h7"></i><a href="'.$item['link'].'" class="f01 link_track" target="_blank">'.$item['name'].'</a><br>';

                                        }

                                    }                            

                            $html .='<div class="float-right"><i class="fas fa-external-link-alt mr-2 color_1 h7"></i><a href="'.$playlist['link'].'" class="f01 text-right link_track" target="_blank">ver playlist completa</a></div><br>
                                </div>
                            </div>
                        </div>'; 

        }

        $html .= '</div>';

        return $html;        

    }

    function restructureFilesArray($aux_array){
        $output = [];
        foreach ($aux_array as $attrName => $valuesArray) {
            foreach ($valuesArray as $key => $value) {
                $output[$key][$attrName] = $value;
            }
        }
        return $output;
    }  
    
    public function carrega_view($view, $data = array()) {

		$data['isMobile'] = (bool)preg_match('#\b(ip(hone|od|ad)|android|opera m(ob|in)i|windows (phone|ce)|blackberry|tablet'.
                    '|s(ymbian|eries60|amsung)|p(laybook|alm|rofile/midp|laystation portable)|nokia|fennec|htc[\-_]'.
                    '|mobile|up\.browser|[1-4][0-9]{2}x[1-4][0-9]{2})\b#i', $_SERVER['HTTP_USER_AGENT'] );
		

		$this->load->view($view, $data);

	}
}
