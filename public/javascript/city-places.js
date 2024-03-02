// Sélectionnez le champ de sélection de la ville
var citySelect = document.querySelector('.city-select');
var placeSelect = document.querySelector('.place-select');

citySelect.addEventListener('change', function() {
    // Désactivez le champ de sélection du lieu si aucune ville n'est sélectionnée
    placeSelect.disabled = (this.value === '');
});

document.addEventListener("DOMContentLoaded", function() {
    // Sélectionnez le champ de sélection du lieu
    var placeSelect = document.querySelector('.place-select');

    // Désactivez le champ de sélection du lieu par défaut
    placeSelect.disabled = true;

    // Sélectionnez le champ de sélection de la ville
    var citySelect = document.querySelector('.city-select');

    // Ajoutez un écouteur d'événements pour détecter les changements dans le champ de sélection de la ville
    citySelect.addEventListener('change', function() {
        // Désactivez le champ de sélection du lieu si aucune ville n'est sélectionnée
        placeSelect.disabled = (this.value === '');
    });
});


// Ajoutez un écouteur d'événements pour détecter les changements dans le champ de sélection de la ville
citySelect.addEventListener('change', function() {
    var cityId = this.value; // Obtenez l'ID de la ville sélectionnée
    var placeSelect = document.querySelector('.place-select'); // Sélectionnez la liste déroulante des lieux


    // Effectuez une requête XMLHttpRequest pour obtenir les lieux associés à la ville sélectionnée
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/place/ByCity/' + cityId); // Remplacez cette URL par celle de votre contrôleur Symfony pour récupérer les lieux par ville
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);

            // Mettez à jour les options de la liste déroulante des lieux avec les données de la réponse
            placeSelect.innerHTML = ''; // Videz d'abord les options existantes
            for (var key in response) {
                if (response.hasOwnProperty(key)) {
                    var option = document.createElement('option');
                    option.text = response[key];
                    option.value = key;
                    placeSelect.appendChild(option);
                }
            }
        } else {
            console.error('Request failed. Status: ' + xhr.status);
        }
    };
    xhr.send();
});
