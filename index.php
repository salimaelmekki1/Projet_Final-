

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - Réservation de Tickets</title>
    <link rel="stylesheet" href="css/index1.css">
    <link rel="stylesheet" href="css/header1.css">
    
    
</head>
<body>
<?php include 'header1.php'; ?>


<main class="hero">
    <div class="hero-content">
        <div class="hero-text">
            <h1>
                Réservez Vos 
                <span class="highlight">Matchs de Rêve</span>
                Maintenant!
            </h1>
            <p>
                Découvrez le monde de football avec nos offres exceptionnelles. 
                Des matchs inoubliables vous attendent avec des prix 
                qui défient toute concurrence.
            </p>
            <?php if (isset($_SESSION['user_id'])): ?>
            <a href="calendrier.php" class="cta-button">Réserver Maintenant</a>
            <?php else: ?>
            <a href="login.php" class="cta-button">
                Réserver Maintenant</a>
            <?php endif; ?>
        </div>
        <div class="hero-image">
            <img src="images/image_player.png" alt="Téléphone billet football" class="phone-image">
        </div>
    </div>


</main>

<section class="who-we-are">
    <div class="who-container">
        <div class="who-text">
            <h2>À PROPOS DE NOUS</h2>
            <div class="underline"></div>
            <p>
                Bienvenue dans notre univers dédié aux passionnés du football. Nous vous offrons 
                une plateforme facile, rapide et fiable pour réserver vos tickets et vivre 
                des matchs palpitants. Rejoignez-nous et ne ratez plus aucun grand événement !
            </p>
            <a href="savoir_plus.php" class="cta-button">Lire Plus</a>
        </div>
        <div class="who-images">
            <img src="images/img1.png" alt="Match de football" class="main-image">
        </div>
    </div>
</section>

<section class="match-gallery">
    <h2 class="gallery-title">MATCH GALLERY</h2>
    <div class="gallery-grid">
        <img src="images/gallery1.jpg" alt="Fans celebrating">
        <img src="images/gallery2.jpg" alt="Soccer match">
        <img src="images/gallery3.jpg" alt="American football">
        <img src="images/gallery4.jpg" alt="Soccer player">
        <img src="images/gallery5.jpg" alt="Heading the ball">
        <img src="images/gallery6.jpg" alt="Penalty moment">
    </div>
</section>


<!-- SECTION CONTACT -->
<section class="contact-section" id="contact">
    <h2 class="contact-title">Contactez-nous</h2>

    <!-- Infos directes -->
    <div class="contact-infos">
        <p><strong>Téléphone :</strong> <a href="tel:+212612345678">+212 6 12 34 56 78</a></p>
        <p><strong>Email :</strong> <a href="mailto:support@ticketball.ma">support@ticketball.ma</a></p>
        <p><strong>Adresse :</strong> Boulevard du Football, Casablanca, Maroc</p>
    </div>

    <!-- Formulaire -->
    <div class="contact-container">
        <div class="message_content"></div>
        <form id="contactForm" method="post">
    <div class="form-group">
        <input type="text" name="name" placeholder="Nom" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="tel" name="phone" placeholder="Téléphone">
        <textarea name="message" placeholder="Votre message" required></textarea>
    </div>
    <button type="submit" class="cta-button">Envoyer</button>
</form>

        

    </div>
</section>
<script src="js/index.js"></script>
<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Empêche le rechargement de la page

    const form = e.target;
    const formData = new FormData(form);

    fetch('traitement_contact.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const msgDiv = document.querySelector('.message_content');
        msgDiv.innerHTML = `<div class="message-box ${data.status}">${data.message}</div>`;

        if (data.status === 'success') {
            form.reset();
        }
    })
    .catch(error => {
        document.querySelector('.message_content').innerHTML =
            `<div class="message-box error">Erreur réseau : ${error.message}</div>`;
    });
});
</script>

</body>
</html>
