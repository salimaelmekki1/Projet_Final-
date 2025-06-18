document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const message = document.getElementById('message').value.trim();

    if (!name || !email || !message) {
        alert("Veuillez remplir tous les champs requis.");
        return;
    }

    alert(`Merci ${name} !\n\nVotre message :\n"${message}"\n\nNous vous répondrons à l'adresse ${email}${phone ? ' ou au numéro ' + phone : ''}.`);
    document.getElementById('contactForm').reset();
});
