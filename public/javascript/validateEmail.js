function validateEmail() {
    let emailInput = document.getElementById('registration_form_email');
    let emailValue = emailInput.value;

    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if(!emailPattern.test(emailValue)) {
        alert('Please enter a valid email adress !');
        return false;
    }

    return true;

}