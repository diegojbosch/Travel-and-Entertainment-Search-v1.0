<!DOCTYPE html>
<html>
    <head>
        <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
        <meta content="utf-8" http-equiv="encoding">
        <title>Travel and Entertainment</title>
        
        <!-- CSS -->
        <link rel="stylesheet" href="css/style.css" media="screen" />
        
    </head>
    
    <body>  
        <script type="text/javascript">
            
            function setFrame(display) {
                var iframe = document.getElementById('hiddenFrame');
                iframe.style.visibility = display;
                
                if(document.getElementById('radio_here').checked && display == "visible"){
                    
                    var xhr = new XMLHttpRequest();
                    // hardcode USC campus location in case something goes wrong
                    var lat = 34.021824;
                    var lon = -118.286662;
                    document.forms["search_form"].lat_hidden.value = lat;
                    document.forms["search_form"].lon_hidden.value = lon;
                    
                    xhr.onreadystatechange = function() {
                      if (this.readyState == 4 && this.status == 200) {
                          var json_response = JSON.parse(xhr.response);
                          lat = json_response.lat;
                          lon = json_response.lon;
                          
                          document.forms["search_form"].lat_hidden.value = lat;
                          document.forms["search_form"].lon_hidden.value = lon;
                          
                      }
                    };
                    xhr.open("GET", "http://ip-api.com/json", false);
                    xhr.send();
                    
                }
                
                if(display == "hidden"){
                    //clear iframe
                    iframe.src = "about:blank";
                }
                
                var distance = document.getElementById('distance');
                if(distance.value.length == 0){
                    distance.value = 10;
                    distance.style.color = "gray";
                }
                
                if(display == "hidden"){
                    var radio_location = document.getElementById('radio_location');
                    if(radio_location.checked == true){
                        radio_location.checked = false;
                        
                        var location = document.getElementById('location');
                        location.required = false;
                    }
                }
                
            }
        </script>
        
        <?php 
        
            if(isset($_GET['init'])): ?>
                
                <div id="dom-target-place" style="display: none;">
                    
                <?php
                    $place_id = $_GET['init'];                 
                    $url_place = 'https://maps.googleapis.com/maps/api/place/details/json?placeid=' . $place_id . '&key=xxx';
                    $data = file_get_contents($url_place);
                    $characters = json_decode($data);

                    if(isset($characters->result)){
                        $results = $characters->result;
                    }
                    
                    if(isset($results->photos)){
                        $photos = $results->photos;
                    }

                    if(!empty($photos)){
                        $count = 1;
                        foreach($photos as $photo){
                            
                            $photo_reference = $photo->photo_reference;
                            $maxwidth = $photo->width;

                            $url_photo = 'https://maps.googleapis.com/maps/api/place/photo?maxwidth='. $maxwidth .'&photoreference='. $photo_reference .'&key=xxx';
                            $img = 'img' . $count . '.jpg';
                            file_put_contents($img, file_get_contents($url_photo));

                            if(++$count > 5) break;
                        }
                    }

                    echo htmlspecialchars($data);

                ?>
                </div>
        
                <script type="text/javascript">
                    
                    function toggleReviews() {
                        
                        var xr = document.getElementById("reviewsDiv");
                        if (xr.style.display === "none") {
                            
                            var algo = document.getElementById("hidePhotos").style.display;
                            if(algo != "none"){
                               togglePhotos();
                            }
                            
                            xr.style.display = "block";                     
                            
                        } else {
                            xr.style.display = "none";
                        }
                        
                        var yr = document.getElementById("showReviews");
                        if (yr.style.display === "none") {
                            yr.style.display = "block";
                        } else {
                            yr.style.display = "none";
                        }
                        
                        var zr = document.getElementById("hideReviews");
                        if (zr.style.display === "none") {
                            zr.style.display = "block";
                        } else {
                            zr.style.display = "none";
                        }
                    }
                    
                    function togglePhotos() {
                        
                        var xp = document.getElementById("photosDiv");
                        if (xp.style.display === "none") {
                            
                            var algop = document.getElementById("hideReviews").style.display;
                            if(algop != "none"){
                               toggleReviews();
                            }
                            
                            xp.style.display = "block";
                        } else {
                            xp.style.display = "none";
                        }
                        
                        var yp = document.getElementById("showPhotos");
                        if (yp.style.display === "none") {
                            yp.style.display = "block";                            
                        } else {
                            yp.style.display = "none";
                        }
                        
                        var zp = document.getElementById("hidePhotos");
                        if (zp.style.display === "none") {
                            zp.style.display = "block";
                        } else {
                            zp.style.display = "none";
                        }
                        
                    }

                    var div_place = document.getElementById("dom-target-place");
                    var data_place = div_place.textContent;
                    var json = JSON.parse(data_place);

                    var html_text = "<html><head></head><body>";
                    
                    html_text += "<h4 style='text-align:center'>" + json.result.name + "</h4><br><div id='big_div_reviews'>";
                    html_text += "<div id='showReviews'><p style='text-align:center;'>click to show reviews</p>";
                    html_text += "<img src='http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png' alt='arrow down review' onclick='toggleReviews();' style='width:42px;height:20px;border:0; display: block; margin-left: auto; margin-right: auto;'></div>";
                    html_text += "<div id='hideReviews' style='display:none;'><p style='text-align:center;'>click to hide reviews</p>";
                    html_text += "<img src='http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png' alt='arrow up review' onclick='toggleReviews();' style='width:42px;height:20px;border:0; display: block; margin-left: auto; margin-right: auto;'></div>";  
                    
                    //Start table
                    html_text += "<div id='reviewsDiv' style='display:none;'><table width='600' align='center'>";
                    html_text += "<tbody>";
                    
                    var reviews = json.result.reviews;

                    if(reviews != null){
                        if(reviews.length > 0){
                            //top 5 reviews
                            for(var j=0; j<reviews.length && j<5; j++){                      
                                html_text += "<tr><th><img src='" + reviews[j].profile_photo_url + "' alt='' style='width:15px;height:15px;'>" + reviews[j].author_name + "</th></tr>";
                                html_text += "<tr><td>" + reviews[j].text + "</td></tr>";
                            }
                        }
                    }else{
                        html_text += "<tr><th>No Reviews Found</th></tr>";
                    }
                    
                    html_text += "</tbody>"; 
                    html_text += "</table></div></div><br><div id='big_div_photos'>";

                    html_text += "<div id='showPhotos'><p style='text-align:center;'>click to show photos</p>";
                    html_text += "<img src='http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png' alt='arrow down photo' onclick='togglePhotos();' style='width:42px;height:20px;border:0; display: block; margin-left: auto; margin-right: auto;'></div>";
                    html_text += "<div id='hidePhotos' style='display:none;'><p style='text-align:center;'>click to hide photos</p>";
                    html_text += "<img src='http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png' alt='arrow up photo' onclick='togglePhotos();' style='width:42px;height:20px;border:0; display: block; margin-left: auto; margin-right: auto;'></div>";
                    
                    //Start table
                    html_text += "<div id='photosDiv' style='display:none;'><table width='600' align='center'>";
                    html_text += "<tbody>";
                    
                    var photos = json.result.photos;
                    
                    if(photos != null){
                        var limit = 0;
                        if(photos.length > 5){
                           limit = 5;
                        }else{
                            limit = photos.length;
                        }
                        for(var j=1; j<=limit; j++){
                            html_text += "<tr><td><a href='img" + j + ".jpg' target='_blank'><img src='img" + j + ".jpg' alt='' style='max-height:100%; max-width:100%'></a></td></tr>";
                        }
                    }else{
                        html_text += "<tr><th>No Photos Found</th></tr>";
                    }
                    
                    html_text += "</tbody>"; 
                    html_text += "</table></div></div>";
                    
                    html_text += "</body></html>";
                    
                    document.write(html_text);

                </script>
        
            <?php
            
            elseif(isset($_POST["Search"])): ?>
    
                <div id="dom-target" style="display: none;">
                    <?php
                    
                        $lat = 0;
                        $lon = 0;
                    
                        if($_POST["destination"] == "Here"){    
                            
                            $lat = $_POST["lat_hidden"];
                            $lon = $_POST["lon_hidden"];

                        }elseif($_POST["destination"] == "Location"){

                            $location = preg_replace('/\s+/', '_', $_POST["location"]);
                            
                            $url_location = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $location . "&key=xxx";
                            $response_location = file_get_contents($url_location);

                            $obj_location = json_decode($response_location, true);

                            $lat = $obj_location['results'][0]['geometry']['location']['lat'];
                            $lon = $obj_location['results'][0]['geometry']['location']['lng'];

                        }
                    
                        $keyword = $_POST["keyword"];
                        
                        //Calculate radius from miles to meters
                        $radius = $_POST["distance"] * 1609.344;

                        //Type of category
                        $category_type = $_POST["category"];
                    
                        $url_nearby = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=" . $lat . "," . $lon ."&radius=" . $radius . "&type=" . $category_type . "&keyword=" . $keyword . "&key=xxx";
                        
                        $response_nearby = file_get_contents($url_nearby);

                        echo htmlspecialchars($response_nearby);


                    ?>
                </div>
        
                <div id="start_lat" style="display: none;">
                    <?php echo htmlspecialchars($lat); ?>
                </div>
        
                <div id="start_lng" style="display: none;">
                    <?php echo htmlspecialchars($lon); ?>
                </div>
        
                
                <script async defer
                    src="https://maps.googleapis.com/maps/api/js?key=xxx">
                </script>
        
                <script type="text/javascript">
                    
                    function initMap(p_lat, p_lng) {
                        
                        var uluru = {lat: p_lat, lng: p_lng};
                        
                        var map = new google.maps.Map(document.getElementById('map'), {
                                    zoom: 12,
                                    center: uluru });
                        var marker = new google.maps.Marker({
                            position: uluru,
                            map: map
                        });
                    }
                    
                    function initDirection(start_lat, start_lng, end_lat, end_lng, travel_mode/*, row*/) {
                        
                        var directionsService = new google.maps.DirectionsService;
                        var directionsDisplay = new google.maps.DirectionsRenderer;
                       
                        var start = new google.maps.LatLng(start_lat, start_lng);
                        var end = new google.maps.LatLng(end_lat, end_lng);
                        
                        var mapDirection = new google.maps.Map(document.getElementById('map'), {
                            zoom: 12,
                            center: start
                        });
                        
                        directionsDisplay.setMap(mapDirection);
                        
                        calculateAndDisplayRoute(directionsService, directionsDisplay, start, end, travel_mode);

                    }

                    function calculateAndDisplayRoute(directionsService, directionsDisplay, start, end, travel_mode) {
                        
                        var request = {
                            origin: start,
                            destination: end,
                            travelMode: travel_mode
                        };
                        
                        directionsService.route(request, function(response, status) {
                            if (status === 'OK') {
                                directionsDisplay.setDirections(response);
                            } else {
                                window.alert('Directions request failed due to ' + status);
                            }
                        });
                    }
                    
                    function get_bottom_pos(element) {
                        var rect = element.getBoundingClientRect();
                        return {left:rect.left, bottom:rect.bottom}
                    }
                    
                    function close_other_panels_map(current_row, current_elem_pos){
                        
                        var m = document.getElementById('map');
                        var map_pos_top = current_elem_pos.bottom + "px";
                        
                        //if not the same row
                        if (m.style.top !== map_pos_top && m.style.display === "block") {
                            m.style.display = "none";
                        }
                        
                        var row = 0;
                        var continue_checking = true;
                        
                        while(continue_checking){
                            //avoid checking same row
                            if(row == current_row){
                                row++;
                            }
                            
                            var floating_panel = "floating-panel" + row;
                            y = document.getElementById(floating_panel);
                            
                            if(y !== null){ 
                                if(y.style.display == "block"){
                                    y.style.display = "none";
                                }
                            }else{
                                continue_checking = false;
                            }
                            row++;
                        }
                        
                    }
                    
                    function toggleMaps(p_lat, p_lng, row) {
                        
                        var toogle_map_id = 'toggleMaps' + row;
                        var element_pos = get_bottom_pos(document.getElementById(toogle_map_id));
                        
                        close_other_panels_map(row, element_pos);
                        
                        var x = document.getElementById('map');
                        if (x.style.display === "none") {
                            
                            initMap(p_lat, p_lng);
                            x.style.top = element_pos.bottom + "px";
                            x.style.left = element_pos.left + "px";
                            x.style.display = "block";
                        } else {
                            x.style.display = "none";
                        }
                        
                        var floating_panel = "floating-panel" + row;
                        var y = document.getElementById(floating_panel);
                        if (y.style.display === "none") {
                            
                            y.style.top = element_pos.bottom + "px";
                            y.style.left = element_pos.left + "px";
                            y.style.display = "block";
                        } else {
                            y.style.display = "none";
                        }
                        
                    }

                    var div = document.getElementById("dom-target");
                    var data_places = div.textContent;
                    
                    var json = JSON.parse(data_places);

                    var results = json.results;
                        
                    var html_text;
                    
                    html_text = "<html><head></head><body>";
                    
                    if(results.length > 0){
                        
                        var div_start_lat = document.getElementById("start_lat");
                        var start_lat = div_start_lat.textContent;
                        var div_start_lng = document.getElementById("start_lng");
                        var start_lng = div_start_lng.textContent;
                        
                        //Start table
                        html_text += "<div style='position: relative'><table style='line-height: 30px;'>";

                        html_text += "<tbody>"; 
                        html_text += "<tr><th style='width:100px'>Category</th><th style='width:2000px'>Name</th><th style='width:2000px'>Address</th></tr>";

                        var end_lat = 0;
                        var end_lng = 0;

                        for(var j=0; j<results.length && j<20; j++){
                            end_lat = results[j]["geometry"]["location"]["lat"];
                            end_lng = results[j]["geometry"]["location"]["lng"];
                            
                            html_text += "<tr><td><img src='" + results[j]["icon"] + "' style = 'width:20px;height:20px;'></td>";
                            html_text += "<td><a href='place.php?init=" + results[j]["place_id"] + "' target='hiddenFrame'>" + results[j]["name"] + "</a></td>";
                            html_text += "<td><div id='map' style='width: 300px;height: 300px; background-color: gray; display: none; position: absolute; z-index: 1;'></div><div id='floating-panel" + j + "' style='position: absolute; display: none; background-color: gainsboro; z-index: 2;'><a onclick='initDirection(" + start_lat + "," + start_lng + "," + end_lat + "," + end_lng + ",\"WALKING\");'>Walk there</a><br><a onclick='initDirection(" + start_lat + "," + start_lng + "," + end_lat + "," + end_lng + ",\"BICYCLING\");'>Bike there</a><br><a onclick='initDirection(" + start_lat + "," + start_lng + "," + end_lat + "," + end_lng + ",\"DRIVING\");'>Drive there</a></div><a id='toggleMaps" + j + "' onclick='toggleMaps(" + end_lat + "," + end_lng + "," + j + ");'>" + results[j]["vicinity"] + "</a></td></tr>";
                        }

                        html_text += "</tbody>"; 
                        html_text += "</table></div>";                        
                        
                    }else{
                        
                        html_text += "<div id='no-records'>No Records have been found</div>";
                    }
                    
                    html_text += "</body></html>";

                    document.write(html_text);

                </script>
        
        <?php else: ?>
        
            <form name="search_form" method="POST" action="" target="hiddenFrame" onreset="setFrame('hidden')" onsubmit="setFrame('visible')">
                
                <h1><i>Travel and Entertainment Search</i></h1>
                <hr width="580px">
                
                <input type="hidden" name="lat_hidden" id="lat_hidden">
                <input type="hidden" name="lon_hidden" id="lon_hidden">             

                <div style="margin-left: 20px; margin-top: 20px">
                    <b>Keyword</b>
                    <input name=keyword required>
                    <br>
                    <b>Category</b>
                    <select name="category">
                        <option selected=selected value="default">default</option>
                        <option value="cafe">cafe</option>
                        <option value="bakery">bakery</option>
                        <option value="restaurant">restaurant</option>
                        <option value="beauty_salon">beauty salon</option>
                        <option value="casino">casino</option>
                        <option value="movie_theater">movie theater</option>
                        <option value="lodging">lodging</option>
                        <option value="airport">airport</option>
                        <option value="train_station">train station</option>
                        <option value="subway_station">subway station</option>
                        <option value="bus_station">bus station</option>                 
                    </select>

                    <br>
                    <b>Distance</b>
                    <input type="number" name="distance" id="distance" placeholder="10">
                    <b>from</b>
                    <input onclick="document.getElementById('location').disabled = true;" type="radio" name="destination" id="radio_here" value="Here" checked>
                    Here
                    <br>
                    <input onclick="document.getElementById('location').disabled = false;" style="margin-left: 267px" type="radio" name="destination" value="Location" id="radio_location">
                    <input type="text" name="location" id="location" placeholder="location" disabled="disabled" required>

                    <br><br>
                </div>
                
                <div style="margin-left: 100px">
                    <input type="submit" name="Search" value="Search">
                    <input type="reset" name="Clear" value="Clear">
                </div>
            </form>
        
            <div id="divFrame">
                <iframe id="hiddenFrame" name="hiddenFrame" class="hide" style="border:none; visibility:visible;" onload="this.width=window.innerWidth;this.height=window.innerHeight;"></iframe>
            </div>
            
        <?php            
            endif;    
        ?>     
    
    </body>
    
</html>