document.addEventListener('DOMContentLoaded', function() {
    var navLinks = document.querySelectorAll('.menu_links');

    navLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            console.log("click");
            var href = link.getAttribute('href');
            // Extract the ID from the href (remove the # character)
            var targetId = href.substring(1);
            var targetElement = document.getElementById(targetId);

            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
});
