document.addEventListener('DOMContentLoaded',function (){
    let inputStartDate = document.getElementById('event_startDate');
    let registrationDeadline = document.getElementById('event_registrationDeadline');

    function changeRegistrationDeadlineAccordingToStartDate() {
        let startDate = new Date(inputStartDate.value);
        let maxEndDate = new Date(startDate.getTime() - 2 * 60 * 60 * 1000);

        let year = maxEndDate.getFullYear();
        let month = ("0" + (maxEndDate.getMonth() + 1)).slice(-2);
        let day = ("0" + maxEndDate.getDate()).slice(-2);
        let hours = ("0" + maxEndDate.getHours()).slice(-2);
        let minutes = ("0" + maxEndDate.getMinutes()).slice(-2);

        let formattedDate = year + '-' + month + '-' + day + 'T' + hours + ':' + minutes;

        registrationDeadline.setAttribute('max', formattedDate);
        registrationDeadline.value = formattedDate;
    }

    inputStartDate.addEventListener('change',function(){
        changeRegistrationDeadlineAccordingToStartDate();
    })

    changeRegistrationDeadlineAccordingToStartDate();
})

