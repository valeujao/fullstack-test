// GET AND SET POSITION //

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(getTemp);
} else {
    alert("Geolocation is not supported by this browser.");
}    

function getTemp(position) {
    
    $("#main_div").hide();
    $("#spotify_content").html('');
    $("#div_not_found").hide();    
    $("#spin_load").fadeIn();
    $("#temp_span").html('-');
    $("#temp_icon").attr('src','');
    $("#temp_city").html('-');   

    var lat = 0;
    var lon = 0;
    var valor = 0;

    if(position != 0){   
        lat = position.coords.latitude;
        lon = position.coords.longitude;        
    } else {    
        valor = $("#search_field").val(); 
    }

    $.ajax({
        type: 'POST',
        url: '/musicbyweather/Main/find_temp/',
        dataType: 'JSON',
        data: "lat=" + lat + "&lon=" + lon + "&val=" + valor,
        success: function (response) {
            
            if(response.code_return == 1){ 

                var new_temp = response.temp.toFixed(1);
                var new_icon = response.icon;
                var new_city = response.city;

                $("#temp_span").html(new_temp+' °C');
                $("#temp_icon").attr('src',new_icon);
                $("#temp_city").html(new_city);                
                $("#main_div").fadeIn('slow');
                $("#spotify_content").html('<p class="f01 h7 mt-3 mb-2">Playlists recomendadas:</p>'+response.spotify);

            } else {
                $("#div_not_found").fadeIn('slow');
            }

            $("#spin_load").hide();

        },
        error: function (req, status, err) {                                  
            console.log('Something went wrong', status, err);
        }
    });
}

// FORM GET NEW TEMP //

$("#form_search").on('submit', function(){

    getTemp(0);
    
    event.preventDefault();    

});