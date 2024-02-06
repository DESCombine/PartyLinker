import { request_path } from "/static/js/config.js?v=11";

// Loads all the future and recent events to display on the map
async function loadEvents() {
    const response = await fetch(request_path + "/event/load_recent_future_events.php", {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
    });
    const events = await response.json();
    let events_location = await Promise.all(events.map(async event => {
        event.location = event.location.replaceAll(" ", "%20");
        const response = await fetch("https://api.mapbox.com/geocoding/v5/mapbox.places/" + event.location + ".json?access_token=pk.eyJ1IjoiZGFuaWxvbWFnbGlhIiwiYSI6ImNscmdtYzVqYTAyejIya21rZnJrOWtsazIifQ.4iM5ZZ26Y945WvEawTztOQ")
        const data = await response.json();
        return {
            "id": event.event_id,
            "lng": data.features[0].center[0],
            "lat": data.features[0].center[1],
            "title": event.name,
            "start": event.starting_date,
            "end": event.ending_date,
        };
    }));
    return events_location;

}

/**
 * Loads the map with the events
 */
async function loadMap() {
    mapboxgl.accessToken = 'pk.eyJ1IjoiZGFuaWxvbWFnbGlhIiwiYSI6ImNscmdtYzVqYTAyejIya21rZnJrOWtsazIifQ.4iM5ZZ26Y945WvEawTztOQ';
    let events = await loadEvents();
    console.log(events)
    console.log(events[0]) 
    var map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [12.5, 42],
        zoom: 5.8
    });
    events.forEach(event => {
        const popup = new mapboxgl.Popup({ offset: 25 }).setHTML(
            '<h3>' + event.title + '</h3>' + '<a class="text-black" href="/event/eventpage.html?id=' + event.id + '"><em class="fa-solid fa-circle-info"></em></a>' +
            '<p> Starting: ' + event.start + '<br/>Ending: ' + event.end + '</p>'
        );
        new mapboxgl.Marker({
            color: "#FF0000",
            draggable: false
        })
        .setLngLat([event.lng, event.lat])
        .setPopup(popup)
        .addTo(map);
        
    })

    const geolocation = new mapboxgl.GeolocateControl({
        positionOptions: {
            enableHighAccuracy: true
        },
        trackUserLocation: true
    });


    const nav = new mapboxgl.NavigationControl({
        visualizePitch: true
    });
    map.addControl(nav);
    map.addControl(geolocation);
}

loadMap();