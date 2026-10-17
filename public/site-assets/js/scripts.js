var map;
var LAT = "28.644800";
var LNG = "77.216721";
var myLatlng = new google.maps.LatLng(LAT, LNG);
var markerA = new google.maps.MarkerImage(base_url + '/site-assets/images/marker.png');
var marker = new google.maps.Marker({ map: map, anchorPoint: new google.maps.Point(0, -29) });

function getFormData($form){
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};
    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = $.trim(n['value']);
    });
    return indexed_array;
}

function makeNameEditable() {
    $("#no_edit_name").hide();
    $("#edit_name").show();
}

function noNameEditable() {
    $("#no_edit_name").show();
    $("#edit_name").hide();
}

function makeEmailEditable() {
    $("#no_edit_email").hide();
    $("#edit_email").show();
}

function noEmailEditable() {
    $("#no_edit_email").show();
    $("#edit_email").hide();
}

function makePhoneEditable() {
    $("#no_edit_phone").hide();
    $("#edit_phone").show();
}

function noPhoneEditable() {
    $("#no_edit_phone").show();
    $("#edit_phone").hide();
}

function makePasswordEditable() {
    $("#no_edit_password").hide();
    $("#edit_password").show();
}

function noPasswordEditable() {
    $("#no_edit_password").show();
    $("#edit_password").hide();
}

function showCardForm() {
    $("#addCardForm").show();
    $("html, body").animate(
        {
            scrollTop: $("#addCardForm").offset().top,
        },
        300
    );
}

function showAddressForm() {
    $("#addAddressForm").show();
}

function hideAddressForm() {
    $("#addAddressForm").hide();
}

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else {
        // x.innerHTML = "Geolocation is not supported by this browser.";
        // $("#latitude").val('28.644800');
        // $("#longitude").val("77.216721");
        displayMap();
        // navigator.geolocation.getCurrentPosition(showPosition);
    }
    displayMap();
}

function showPosition(position) {
    $("#latitude").val(position.coords.latitude);
    $("#longitude").val(position.coords.longitude);
    var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
    geocoder = new google.maps.Geocoder();
    geocoder.geocode({ latLng: latlng }, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if (results[0]) {
                var place = results[0];
                for (var i=0; i<place.address_components.length; i++){
                    for (var b=0;b<place.address_components[i].types.length;b++){
                        if (place.address_components[i].types[b] == "sublocality_level_1" || place.address_components[i].types[b] == "sublocality"){
                            $('#location_name').val(place.address_components[i].long_name);
                        }
                        if (place.address_components[i].types[b] == "postal_code"){
                            if (document.getElementById("zipcode")) 
                                $('#zipcode').val(place.address_components[i].long_name);
                        }
                        if (place.address_components[i].types[b] == "administrative_area_level_2" || place.address_components[i].types[b] == 'locality'){
                            $('#city').val(place.address_components[i].long_name);
                        }
                        if (place.address_components[i].types[b] == "administrative_area_level_1"){
                            $('#state').val(place.address_components[i].long_name);
                        }
                        if (place.address_components[i].types[b] == "country"){
                            $('#country').val(place.address_components[i].long_name);
                        }
                    }
                }
                var fromAddress = results[0].formatted_address;
                document.getElementById("google_address").value = fromAddress;
                displayMap();
            } else {
                displayMap();
                alert("No results found");
            }
        } else {
            displayMap();
            alert("Geocoder failed due to: " + status);
        }
    });
}

function displayMap() {
    var LAT = $("#latitude").val() != "" ? $("#latitude").val() : "28.644800";
    var LNG = $("#longitude").val() != "" ? $("#longitude").val() : "77.216721";
    console.log(LAT);
    console.log(LNG);
    var myLatlng = new google.maps.LatLng(LAT, LNG);
    var mapOptions = {
        zoom: 17,
        panControl: false,
        mapTypeControl: true,
        fullscreenControl: false,
        streetViewControl: false,
        gestureHandling: "greedy",
        zoomControl: true,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.HYBRID,
    };
    map = new google.maps.Map(document.getElementById("myMap"), mapOptions);
    marker.setVisible(false);
    marker = new google.maps.Marker({
        map: map,
        icon: markerA,
        position: myLatlng,
        draggable: true,
    });
    marker.setVisible(true);
    var input = document.getElementById("google_address");
    var autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.addListener("place_changed", function () {
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            return;
        }
        var latlng = {
            lat: place.geometry.location.lat(),
            lng: place.geometry.location.lng(),
        };
        marker.setVisible(false);
        marker = new google.maps.Marker({
            position: latlng,
            icon: markerA,
            map: map,
            draggable: true,
        });
        map.setCenter(latlng);
        updateAddress(latlng, false);
    });

    google.maps.event.addListener(map, "click", function (event) {
        var lat = event.latLng.lat();
        var lng = event.latLng.lng();
        console.log(lat);
        marker.setVisible(false);
        var latlng = new google.maps.LatLng(lat, lng);
        marker = new google.maps.Marker({
            position: latlng,
            icon: markerA,
            map: map,
            draggable: true,
        });
        marker.setVisible(true);
        updateAddress(latlng, true);
    });
}

function updateAddress(latlng, updateAddress) {
    // var latlng = new google.maps.LatLng('28.644800', '77.216721');
    geocoder = new google.maps.Geocoder();
    geocoder.geocode({ latLng: latlng }, function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            if (results[0]) {
                if (updateAddress) {
                    $("#google_address").val(results[0].formatted_address);
                }
                if (document.getElementById("latitude")) $("#latitude").val(marker.getPosition().lat());
                if (document.getElementById("longitude")) $("#longitude").val(marker.getPosition().lng());
                if (document.getElementById("city")) $("#city").val("");
                if (document.getElementById("state")) $("#state").val("");
                if (document.getElementById("zipcode")) $("#zipcode").val("");
                if (document.getElementById("location_name")) $("#location_name").val("");
                $("#country").val("");
                var place = results[0];
                for (var i=0; i<place.address_components.length; i++){
                    for (var b=0;b<place.address_components[i].types.length;b++){
                        if (place.address_components[i].types[b] == "sublocality_level_1" || place.address_components[i].types[b] == "sublocality"){
                            $('#location_name').val(place.address_components[i].long_name);
                        }
                        if (place.address_components[i].types[b] == "postal_code"){
                            if (document.getElementById("zipcode")) 
                                $('#zipcode').val(place.address_components[i].long_name);
                        }
                        if (place.address_components[i].types[b] == "administrative_area_level_2" || place.address_components[i].types[b] == 'locality'){
                            $('#city').val(place.address_components[i].long_name);
                        }
                        if (place.address_components[i].types[b] == "administrative_area_level_1"){
                            $('#state').val(place.address_components[i].long_name);
                        }
                        if (place.address_components[i].types[b] == "country"){
                            $('#country').val(place.address_components[i].long_name);
                        }
                    }
                }
            }
        }
    });
}

function decrement(e) {
    enableCalculation();
    const btn = e.target.parentNode.parentElement.querySelector('button[data-action="decrement"]');
    const target = btn.nextElementSibling;
    let minQty = $("#product_min_qty_" + target.id).val() ? $("#product_min_qty_" + target.id).val() : 1;
    let value = Number(target.value);
    if (parseInt(value) > parseInt(minQty)) {
        value--;
        target.value = value;
    } else {
        alert("For this product, you require to purchase at least " + minQty + " quantity.");
    }
}

function increment(e) {
    enableCalculation();
    const btn = e.target.parentNode.parentElement.querySelector('button[data-action="decrement"]');
    const target = btn.nextElementSibling;
    let value = Number(target.value);
    value++;
    target.value = value;
}

const decrementButtons = document.querySelectorAll(`button[data-action="decrement"]`);
const incrementButtons = document.querySelectorAll(`button[data-action="increment"]`);
decrementButtons.forEach((btn) => {
    btn.addEventListener("click", decrement);
});

incrementButtons.forEach((btn) => {
    btn.addEventListener("click", increment);
});

function selectFrequency(type, id) {
    enableCalculation();
    var currentDate = new Date();
    currentDate.setDate(currentDate.getDate() + 1);
    if (type == "weekly") {
        $(".days_option_" + id).removeClass("hide");
    } else {
        $(".days_option_" + id).addClass("hide");
    }
    if (type == "once") {
        mobiscrollObj[id] = mobiscroll.setOptions({
            locale: mobiscroll.localeEn, // Specify language like: locale: mobiscroll.localePl or omit setting to use default
            theme: "ios", // Specify theme like: theme: 'ios' or omit setting to use default
            themeVariant: "light", // More info about themeVariant: https://docs.mobiscroll.com/5-7-1/calendar#opt-themeVariant
        });
        $("#delivery_dates_" + id)
            .mobiscroll()
            .datepicker({
                controls: ["calendar"],
                invalid: invalidObj,
                colors : highlightedDates,
                display: "inline",
                min: currentDate,
            });
    } else if (type == "monthly") {
        mobiscrollObj[id] = mobiscroll.setOptions({
            locale: mobiscroll.localeEn, // Specify language like: locale: mobiscroll.localePl or omit setting to use default
            theme: "ios", // Specify theme like: theme: 'ios' or omit setting to use default
            themeVariant: "light", // More info about themeVariant: https://docs.mobiscroll.com/5-7-1/calendar#opt-themeVariant
        });
        $("#delivery_dates_" + id)
            .mobiscroll()
            .datepicker({
                controls: ["calendar"],
                invalid: invalidObj,
                colors : highlightedDates,
                display: "inline", // Specify display mode like: display: 'bottom' or omit setting to use default
                selectMultiple: true,
                min: currentDate,
            });
    } else {
        mobiscrollObj[id] = mobiscroll.setOptions({
            locale: mobiscroll.localeEn, // Specify language like: locale: mobiscroll.localePl or omit setting to use default
            theme: "ios", // Specify theme like: theme: 'ios' or omit setting to use default
            themeVariant: "light", // More info about themeVariant: https://docs.mobiscroll.com/5-7-1/calendar#opt-themeVariant
        });
        $("#delivery_dates_" + id)
            .mobiscroll()
            .datepicker({
                controls: ["calendar"],
                display: "inline", // Specify display mode like: display: 'bottom' or omit setting to use default
                select: "range",
                colors : highlightedDates,
                showRangeLabels: true,
                selectMultiple: true,
                min: currentDate,
            });
    }
}

function selectQuickSelection(type, id) {
    enableCalculation();
    if (type == "WEEKDAYS") {
        document.getElementById("days_mon_" + id).checked = true;
        document.getElementById("days_tue_" + id).checked = true;
        document.getElementById("days_wed_" + id).checked = true;
        document.getElementById("days_thu_" + id).checked = true;
        document.getElementById("days_fri_" + id).checked = false;
        document.getElementById("days_sat_" + id).checked = false;
        document.getElementById("days_sun_" + id).checked = true;
    } else {
        document.getElementById("days_mon_" + id).checked = false;
        document.getElementById("days_tue_" + id).checked = false;
        document.getElementById("days_wed_" + id).checked = false;
        document.getElementById("days_thu_" + id).checked = false;
        document.getElementById("days_fri_" + id).checked = true;
        document.getElementById("days_sat_" + id).checked = true;
        document.getElementById("days_sun_" + id).checked = false;
    }
}

function enableCalculation() {
    $(".calculation").addClass("hide");

    $(".calculate").removeClass("hide");
}

function selectTimeSlot(id) {
    $("#timeslot_id").val(id);
    if($("#timeslot_id_2")){
        $("#timeslot_id_2").val(id);
    }
}

function addOrder(id){
    var $form = $("#"+id);
    var data = getFormData($form);
    // var stripe = Stripe($('#publishable_key').val());

    // stripe.createToken(cardElement).then(function(result) {
    //     if (result.error) {
    //         // Handle errors (e.g., display an error message to the user)
    //         console.error(result.error.message);
    //     } else {
    //         // Send the token to your server
    //         console.log(result.token.id);
    //         var token = result.token.id;
    //         $('#stripeToken').val(token); 
    //         data.stripeToken = token;
    //         $.ajaxSetup({
    //             headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             }
    //         });
    //         $.ajax({
    //             url: base_url+'/save-token',
    //             type: 'POST',
    //             data: data,
    //             dataType:'json',
    //             success: function (response) {                
    //                 if(response.success){
    //                     location.href = base_url+'/order-details/'+response.data;
    //                 }
    //             },
    //         });
    //     }
    // });
    Stripe.setPublishableKey($('#publishable_key').val());
    Stripe.createToken({
    number: $('#card_number').val(),
    cvc: $('#cvv').val(),
    exp_month: $('#month').val(),
    exp_year: $('#year').val()
    },function(status, result) {
        console.log(status);
        console.log(result);
        var token = result['id'];
        $('#stripeToken').val(token); 
        data.stripeToken = token;
        $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: base_url+'/save-token',
            type: 'POST',
            data: data,
            dataType:'json',
            success: function (response) {                
                if(response.success){
                    location.href = base_url+'/order-details/'+response.data;
                }
            },
        });
    });
}

function addWalletAmount(id){
    var $form = $("#"+id);
    var data = getFormData($form);
    Stripe.setPublishableKey($('#publishable_key').val());
    Stripe.createToken({
    number: $('#card_number').val(),
    cvc: $('#cvv').val(),
    exp_month: $('#month').val(),
    exp_year: $('#year').val()
    },function(status, result) {
        console.log(status);
        console.log(result);
        var token = result['id'];
        $('#stripeToken').val(token); 
        data.stripeToken = token;
        $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: base_url+'/save-token-wallet',
            type: 'POST',
            data: data,
            dataType:'json',
            success: function (response) {                
                if(response.success){
                    location.href = base_url+'/wallet';
                }
            },
        });
    });
}

function closeModal(){
    
}
