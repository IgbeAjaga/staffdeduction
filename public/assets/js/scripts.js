(function (window, undefined) {
  'use strict';

  /*
  NOTE:
  ------
  PLACE HERE YOUR OWN JAVASCRIPT CODE IF NEEDED
  WE WILL RELEASE FUTURE UPDATES SO IN ORDER TO NOT OVERWRITE YOUR JAVASCRIPT CODE PLEASE CONSIDER WRITING YOUR SCRIPT HERE.  */

})(window);



function resetFilter() {
  var newURL = location.href.split("?")[0];
    window.history.pushState('object', document.title, newURL);
    location.reload();
}


function googleAutoComplete() {
    var input = (document.getElementById('location_name'));
    var autocomplete = new google.maps.places.Autocomplete(input);
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            return;
        }
        var pos = {
          lat: place.geometry.location.lat(),
          lng: place.geometry.location.lng()
        };
        if (place) {
          for (var i=0; i<place.address_components.length; i++){
            for (var b=0;b<place.address_components[i].types.length;b++){
              if (place.address_components[i].types[b] == "sublocality_level_1" || place.address_components[i].types[b] == "sublocality"){
                $('#location_name').val(place.address_components[i].long_name);
              }
              if (place.address_components[i].types[b] == "postal_code"){
                $('#zip_code').val(place.address_components[i].long_name);
              }
              if (place.address_components[i].types[b] == "administrative_area_level_2" || place.address_components[i].types[b] == 'locality'){
                  $('#city').val(place.address_components[i].long_name);
              }
              if (place.address_components[i].types[b] == "administrative_area_level_1"){
                  $('#region').val(place.address_components[i].long_name);
              }
              if (place.address_components[i].types[b] == "country"){
                  $('#country').val(place.address_components[i].long_name);
              }
            }
          }
        }
    });
}

function googleAutoComplete() {
    var input = document.getElementById('location_name');
    var autocomplete = new google.maps.places.Autocomplete(input);

    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            return;
        }

        var pos = {
            lat: place.geometry.location.lat(),
            lng: place.geometry.location.lng()
        };

        if (place) {
            for (var i = 0; i < place.address_components.length; i++) {
                for (var b = 0; b < place.address_components[i].types.length; b++) {
                    if (place.address_components[i].types[b] == "sublocality_level_1" || place.address_components[i].types[b] == "sublocality") {
                        $('#location_name').val(place.address_components[i].long_name);
                    }
                    if (place.address_components[i].types[b] == "postal_code") {
                        $('#zip_code').val(place.address_components[i].long_name);
                    }
                    if (place.address_components[i].types[b] == "administrative_area_level_2" || place.address_components[i].types[b] == 'locality') {
                        $('#city').val(place.address_components[i].long_name);
                    }
                    if (place.address_components[i].types[b] == "administrative_area_level_1") {
                        $('#region').val(place.address_components[i].long_name);
                    }
                    if (place.address_components[i].types[b] == "country") {
                        $('#country').val(place.address_components[i].long_name);
                    }
                }
            }

            // Populate latitude and longitude inputs
            $('#latitude').val(pos.lat);
            $('#longitude').val(pos.lng);
        }
    });
}
