<?
$url	= urldecode($_GET['url']);
$data	= file_get_contents($url);
if(substr_count($url,'http://www.flightradar24.com')>0){
	//let's grab it!
	$a	= strpos($data,'<article>');
	$b	= strrpos($data,'</article>');
	$grab = substr($data,$a,$b-$a);
	$header	= "<head>
	<meta charset=\"utf-8\">

	<title>Airline fleets - Airline fleet database - Flightradar24</title>
	<meta name=\"description\" content=\"A comprehensive guide to the fleets of the world's airlines. Get extensive information about individual airplanes.\">
	<meta name=\"keywords\" content=\"flight flights flight24 flightradar database aviation\">

	<meta name=\"viewport\" content=\"width=device-width\">

   <link rel=\"stylesheet\" href=\"http://flightradar24static.appspot.com/static/_fr24/css/screen_new.css?data2\">
   <link rel=\"stylesheet\" href=\"http://www.flightradar24.com/data/styles/style.css\">
   
   
	<link rel=\"stylesheet\" type=\"text/css\"  media=\"screen\" href=\"http://www.flightradar24.com/data/styles/wasexternalbefore/destination.css\" />
	<link rel=\"stylesheet\" type=\"text/css\"	media=\"screen\" href=\"http://www.flightradar24.com/data/styles/wasexternalbefore/autocomplete.css\" />

	<script type=\"text/javascript\" src=\"http://www.flightradar24.com/data/javascripts/libs/modernizr-2.5.2.min.js\"></script>
	<script type=\"text/javascript\" src=\"http://www.flightradar24.com/data/javascripts/prototype.js\"></script>
	<script type=\"text/javascript\" src=\"http://www.flightradar24.com/data/javascripts/autocomplete2.js\"></script>
	
	<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js\"></script>
	<script type=\"text/javascript\">
		window.jQuery || document.write('<script src=\"http://www.flightradar24.com/data/javascripts/libs/jquery-1.7.1.min.js\"><\/script>');
      $().ready(function() {
        $$(\"a#chat-link\").live(\"click\", function() {
            window.open(\"http://www.flightradar24.com/chat/index.php\", \"Chat\", \"menubar=no,width=550,height=600,toolbar=no\" );
        }); 
      });      
	</script>
	
	<!-- scripts concatenated and minified via ant build script-->
	<script src=\"http://www.flightradar24.com/data/javascripts/plugins.js\"></script>
	<script src=\"http://www.flightradar24.com/data/javascripts/script.js\"></script> 
</head>

<body id=\"data\" class=\"lightNoise\">";
	$array_a	= array(
						"/data/_ajaxcalls/autocomplete_airplanes.php",
						'/data/_ajaxcalls/autocomplete_airports.php',
						'href="/data/',
						'<iframe src="/simple_index.php',
						'<a href="/airport/',
						'<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>',
						'<a href="/filter/aal" class="map_fullscreen_link" target="_blank">View full-screen version of map</a>'
						);
	$array_b	= array(
						"http://www.flightradar24.com/data/_ajaxcalls/autocomplete_airplanes.php",
						'http://www.flightradar24.com/data/_ajaxcalls/autocomplete_airports.php',
						"href=\"$app/gateway?url=http://www.flightradar24.com/data/",
						'<iframe src="http://www.flightradar24.com/simple_index.php',
						'<a href="'.$app.'/gateway?url=/airport/',
						'',
						''
						);
	$grab	= str_replace($array_a,$array_b,$grab);							
	echo $header.$grab;
	die();
}

#echo $url;

echo $data;
?>