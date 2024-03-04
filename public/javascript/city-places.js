document.addEventListener("DOMContentLoaded", function() {
    var citySelect = document.querySelector('.city-select');
    var placeSelect = document.querySelector('.place-select');
    var placeStreet = document.getElementById('place-street');
    var placeZip = document.getElementById('place-zip');
    var placeLat = document.getElementById('place-latitude');
    var placeLong = document.getElementById('place-longitude');

    // Désactiver le champ de sélection de lieu au chargement de la page
    placeSelect.disabled = true;

    // Ajouter un écouteur d'événements pour détecter les changements dans le champ de sélection de ville
    citySelect.addEventListener('change', function() {
        var cityId = this.value; // Récupérer l'ID de la ville sélectionnée

        // Effectuer une requête AJAX pour récupérer les lieux par ville
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '/place/ByCity/' + cityId);
        xhr.onload = function() {
            if (xhr.status === 200) {
                var placesData = JSON.parse(xhr.responseText);

                // Mettre à jour les options du champ de sélection de lieu avec les lieux récupérés
                placeSelect.innerHTML = '';
                for (var placeId in placesData) {
                    if (placesData.hasOwnProperty(placeId)) {
                        var place = placesData[placeId];
                        var option = document.createElement('option');
                        option.value = placeId;
                        option.textContent = place.name;
                        placeSelect.appendChild(option);
                    }
                }

                // Activer le champ de sélection de lieu
                placeSelect.disabled = false;

                // Sélectionner le premier lieu par défaut après avoir rempli le champ de sélection de lieu
                if (placeSelect.options.length > 0) {
                    var firstPlaceId = placeSelect.options[0].value;
                    loadPlaceDetails(firstPlaceId);
                }
            } else {
                console.error('Request failed. Status: ' + xhr.status);
            }
        };
        xhr.send();
    });

    // Ajouter un écouteur d'événements pour détecter les changements dans le champ de sélection de lieu
    placeSelect.addEventListener('change', function() {
        var placeId = this.value; // Récupérer l'ID du lieu sélectionné
        loadPlaceDetails(placeId);
    });

    // Fonction pour charger les détails du lieu par son ID
    function loadPlaceDetails(placeId) {
        // Effectuer une requête AJAX pour récupérer les détails du lieu sélectionné
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '/place/ajax/' + placeId);
        xhr.onload = function() {
            if (xhr.status === 200) {
                var placeDetails = JSON.parse(xhr.responseText);
                console.log(placeDetails)

                placeStreet.innerHTML = placeDetails['street'];
                placeZip.innerHTML = placeDetails['zipcode'];
                placeLat.innerHTML = placeDetails['latitude'].toString();
                placeLong.innerHTML = placeDetails['longitude'].toString();

            } else {
                console.error('Request failed. Status: ' + xhr.status);
            }
        };
        xhr.send();
    }
});
