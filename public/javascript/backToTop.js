window.onscroll = function () {scrollFunction()};

function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("backToTop").style.display = "block";
    } else {
        document.getElementById("backToTop").style.display = "none";
    }
}

function topFunction() {

    const scrollDistance = window.pageYOffset || document.documentElement.scrollTop;

    const scrollDuration = Math.min(scrollDistance / 2, 1000);

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    })

}