document.addEventListener("DOMContentLoaded", function() {
    let citySelect = document.querySelector('.city-select');
    let placeSelect = document.querySelector('.place-select');
    let placeStreet = document.getElementById('place-street');
    let placeZip = document.getElementById('place-zip');
    let placeLat = document.getElementById('place-latitude');
    let placeLong = document.getElementById('place-longitude');

    // Désactiver le champ de sélection de lieu au chargement de la page
    placeSelect.disabled = true;

    // Ajouter un écouteur d'événements pour détecter les changements dans le champ de sélection de ville
    citySelect.addEventListener('change', function() {
        let cityId = this.value; // Récupérer l'ID de la ville sélectionnée

        // Effectuer une requête AJAX pour récupérer les lieux par ville
        let xhr = new XMLHttpRequest();
        xhr.open('GET', '/place/ByCity/' + cityId);
        xhr.onload = function() {
            if (xhr.status === 200) {
                let placesData = JSON.parse(xhr.responseText);

                // Mettre à jour les options du champ de sélection de lieu avec les lieux récupérés
                placeSelect.innerHTML = '';
                for (let placeId in placesData) {
                    if (placesData.hasOwnProperty(placeId)) {
                        let place = placesData[placeId];
                        let option = document.createElement('option');
                        option.value = placeId;
                        option.textContent = place.name;
                        placeSelect.appendChild(option);
                    }
                }

                // Activer le champ de sélection de lieu
                placeSelect.disabled = false;

                // Sélectionner le premier lieu par défaut après avoir rempli le champ de sélection de lieu
                if (placeSelect.options.length > 0) {
                    let firstPlaceId = placeSelect.options[0].value;
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
        let placeId = this.value; // Récupérer l'ID du lieu sélectionné
        loadPlaceDetails(placeId);
    });

    // Fonction pour charger les détails du lieu par son ID
    function loadPlaceDetails(placeId) {
        // Effectuer une requête AJAX pour récupérer les détails du lieu sélectionné
        let xhr = new XMLHttpRequest();
        xhr.open('GET', '/place/ajax/' + placeId);
        xhr.onload = function() {
            if (xhr.status === 200) {
                let placeDetails = JSON.parse(xhr.responseText);
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
