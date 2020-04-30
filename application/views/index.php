<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
	<meta charset="utf-8">
    <title>musicbyWeather</title> 
    <meta name="theme-color" content="#bae1ff">  
    <link rel="shortcut icon" href="assets/favicon.png"/>
    <meta name="author" content="@valeujao" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all" />
    <link href="assets/css/main.css" rel="stylesheet" type="text/css" media="all" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;300;500;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/e7c51f91fe.js" crossorigin="anonymous"></script>

</head>
    <body>

        <div id="main" class="d-flex align-items-center justify-content-center">   

            <div class="row w-100 d-flex justify-content-center">

                <div class="col-lg-5 col-md-6 col-sm-8 d-flex justify-content-center">
                    <div class="text-center p-3 div_box w-100">
                    

                        <form id="form_search" class="mx-auto">

                            <div class="input-group">
                                <input type="text" autocomplete="off" id="search_field" class="form-control border-0" placeholder="Pesquise pela cidade ou coordenadas">
                                <div class="input-group-append">
                                    <button class="border-0 bg-white px-2" type="submit">
                                        <i class="fas fa-search color_1"></i>
                                    </button>
                                </div>
                            </div>
                        </form>


                        <div id="main_div" style="display: none">
                            <!-- icon provide by OpenWeatherMap -->
                            <img src="" id="temp_icon"/>

                            <!-- temp provide by OpenWeatherMap -->
                            <p id="temp_span" class="mb-0 font-weight-bold h3 f01 f700">
                                
                            </p>
                            <p class="h6 mb-0 font-weight-normal f01">
                                em <span id="temp_city" class="font-weight-bold">
                                    
                                </span>
                            </p>
                        </div>
                        
                        <div id="div_not_found" class="mt-3" style="display: none">
                            <i class="far fa-times-circle text-danger fa-3x"></i>
                            <p class="h6 mb-0 mt-3 font-weight-normal f01">
                                Cidade não encontrada.<br>Tente novamente.
                            </p>
                        </div>

                        <div id="spotify_content">
                        </div>

                        <i class="fas fa-circle-notch fa-spin color_1 display-4 mt-3" id="spin_load" style="display: none"></i>

                    </div>
                </div>

            </div>     
               
        </div>

    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>


    </body>
</html>