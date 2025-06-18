document.addEventListener("DOMContentLoaded", () => {
    fetch("api/get_matchs.php")
      .then((res) => res.json())
      .then((matchs) => {
        const container = document.getElementById("liste-matchs") || document.getElementById("calendrier-matchs");
  
        if (container) {
          matchs.forEach((match) => {
            const div = document.createElement("div");
            div.classList.add("match-item");
            div.innerHTML = `
              <h3>${match.equipe1} vs ${match.equipe2}</h3>
              <p>Date : ${match.date_match} à ${match.heure_match}</p>
              <p>Lieu : ${match.lieu}</p>
              <a href="reserver.php?id=${match.id}" class="btn">Réserver</a>
            `;
            container.appendChild(div);
          });
        }
      })
      .catch((err) => {
        console.error("Erreur de chargement des matchs :", err);
      });
  });
  