function dashboardInit(){
	blockUI('Loading..');
	tutupAllWindow();
	/*
	jQuery("#container_aslidashboard").load('form/map.html'+'?'+mktime(), function() { 
		//logger(detailairport['name']);
		var src	= "http://planefinder.net/";
		//logger(src);
		jQuery('#closemap').html('CLOSE');
		jQuery('#1_airport').html("OMG, THE CROWDED SKY.. ");
		jQuery('#diviframe').html("<iframe src='"+src+"' style=\"border: 0; width: 100%; height: 90%;margin: 0;\"></iframe>");
	}).show();*/
}

function showDetailAirport(detailairport){
	jQuery("#container_aslidashboard").load('form/airport.html'+'?'+mktime(), function() { 
		var spek	= "Country: "+detailairport['countryName'] + "<br>Latitude: "+detailairport['latitude']+"<br>Longitude: "+detailairport['longitude'];
		jQuery('#1name').html(detailairport['name'] + ' (' + detailairport['fs'] + ')');
		jQuery('#1detail_name').html(detailairport['city']);
		var	ll = detailairport['latitude']+","+detailairport['longitude'];
		jQuery('#gambarairport').html("<img src='http://maps.googleapis.com/maps/api/staticmap?center="+ll+"&zoom=12&size=300x300&maptype=roadmap&sensor=false&markers=size:tiny|"+ll+"&key=AIzaSyBQ9gKPl8b0sUN8pT558y4b83KSSLc7En4'>");
		jQuery('#1detail_price').html(detailairport['cityCode']);
		jQuery('#1detail_btnbeli').html(detailairport['countryCode']);
		jQuery('#1detail_spek').html(spek);
		var src_departure	= "http://www.flightstats.com/go/weblet?guid=34b64945a69b9cac:4bf2fb3c:1246346ea7d:4597&weblet=status&action=AirportFlightStatus&airportCode="+detailairport['fs']+"&airportQueryType=0&callback=?";
		var src_arrival		= "http://www.flightstats.com/go/weblet?guid=34b64945a69b9cac:4bf2fb3c:1246346ea7d:4597&weblet=status&action=AirportFlightStatus&airportCode="+detailairport['fs']+"&airportQueryType=1&callback=?";
		var src_weather		= api_url + "gateway?url=http://www.flightstats.com/go/Airport/weather.do?airportCode="+detailairport['fs'];
		if(!isBrowser()){
			jQuery("#iframedeparture").load(src_departure);
			jQuery('#iframearrival').load(src_arrival);
			//jQuery('#iframeweather').load(src_weather);
		}else{
			jQuery('#iframedeparture').html("<iframe src='"+src_departure+"' style=\"border: 0; width: 100%; height: 90%;margin: 0;\"></iframe>");
			jQuery('#iframearrival').html("<iframe src='"+src_arrival+"' style=\"border: 0; width: 100%; height: 90%;margin: 0;\"></iframe>");
			//jQuery('#iframeweather').html("<iframe src='"+src_weather+"' style=\"border: 0; width: 100%; height: 90%;margin: 0;\"></iframe>");
		}
		
	}).show();
}

jQuery('body').on('click', 'a#tabairport a[href="#arrival"]', function() {
	logger('xxx');
});

function displayTimeTickerDashboard(){
	jQuery('#dashboard_time').html(date('d/m/Y H:i:s'));
}


jQuery(document).ready(function() {

	dashboardInit();
	setInterval(displayTimeTickerDashboard, 1000);
	jQuery('body').on('click', 'a#airportmenu', function() {
		var fs		= jQuery(this).attr('airportid');
		var urutan	= jQuery(this).attr('urutan');
		src	= jQuery(this).attr('url');
		//logger(fs);
		if(urutan!=7 ){
			jQuery.each(airport[urutan], function(key,val){
				if(val['fs']==fs){
					detailairport = val;
					showDetailAirport(detailairport);
				}
			})
		}else{
			src	= fs=='CROWDEDSKY' ?  src : api_url + "/gateway?url=" + urlencode(src);
			jQuery("#container_aslidashboard").load('form/map.html'+'?'+mktime(), function() { 
			//logger(detailairport['name']);
			//logger(src);
			jQuery('#closemap').html('CLOSE');
			jQuery('#1_airport').html("SEARCH");
			jQuery('#diviframe').html("<iframe src='"+src+"' style=\"border: 0; width: 100%; height: 90%;margin: 0;\"></iframe>");
		}).show();
		}
		
	});
	jQuery('body').on('click', 'a#btnMap', function() {
		jQuery("#container_aslidashboard").load('form/map.html'+'?'+mktime(), function() {
			jQuery('#closemap').html('< BACK'); 
			//logger(detailairport['name']);
			var src	= "http://www.flightradar24.com/simple_index.php?lat="+detailairport['latitude']+"&lon="+detailairport['longitude']+"&z=9&airports=1&clean=1";
			//logger(src);
			jQuery('#1_airport').html(detailairport['name'] + '(' + detailairport['fs'] + '): Plane Tracker on Map');
			jQuery('#diviframe').html("<iframe src='"+src+"' style=\"border: 0; width: 100%; height: 90%;margin: 0;\"></iframe>");
		}).show();

	});	
	jQuery('body').on('click', 'a#closemap', function() {
		var x	= jQuery('#closemap').html();
		if(x=='CLOSE'){
			tutupAllWindow();
		}else{
			showDetailAirport(detailairport);		
		}
		
	});	
	jQuery('body').on('click', 'a#btnRoute', function() {
		jQuery("#container_aslidashboard").load('form/map.html'+'?'+mktime(), function() { 
			jQuery('#closemap').html('< BACK'); 
			//logger(detailairport['name']);
			var src	= "http://planefinder.net/route/"+detailairport['fs'];
			//logger(src);
			jQuery('#1_airport').html(detailairport['name'] + ' (' + detailairport['fs'] + '): Route');
			jQuery('#diviframe').html("<iframe src='"+src+"' style=\"border: 0; width: 100%; height: 90%;margin: 0;\"></iframe>");
		}).show();

	});	
	jQuery('body').on('click', 'a#btnRadar', function() {
		jQuery("#container_aslidashboard").load('form/map.html'+'?'+mktime(), function() { 
			jQuery('#closemap').html('< BACK'); 
			//logger(detailairport['name']);
			var src	= "http://www.radarbox24.com/?widget=1&mapid=66&z=4&lat="+detailairport['latitude']+"&lng="+detailairport['longitude'];
			//logger(src);
			jQuery('#1_airport').html(detailairport['name'] + ' (' + detailairport['fs'] + '): Radar Box');
			jQuery('#diviframe').html("<iframe src='"+src+"' style=\"border: 0; width: 100%; height: 90%;margin: 0;\"></iframe>");
		}).show();

	});	
	jQuery('body').on('click', 'a#closeairport', function() {
		tutupAllWindow();
	});
});	
function tutupAllWindow(){
	jQuery("#container_aslidashboard").html('');
	jQuery("#container_aslidashboard").attr('style="height:100%;"');
	var isi	= "<p style='height:100%;'><br><br><br><center><img src='images/appschallenge-light.png'><br><br><br><br><img src='images/logo.png'><br><br><br><font color='white'>Developed by @akangaziz</a></center>";
	jQuery('#container_aslidashboard').html(isi);
}

