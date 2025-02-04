<?php
$host = 'mariadb';
$user = 'root';
$pass = 'example';
$db = 'gestion_salles';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo '<link rel="stylesheet" href="style.css">';
// Si une requête AJAX demande la liste des salles pour une date spécifique
if (isset($_GET['date']) && isset($_GET['ajax'])) {
    $date = $_GET['date'];
    $result = $conn->query("
        SELECT s.id, s.nom, s.capacite, 
               (SELECT COUNT(*) FROM reservations r WHERE r.id_salle = s.id AND r.date_reservation = '$date') AS est_reservee
        FROM salles_de_classe s
    ");
    $salles = [];
    while ($row = $result->fetch_assoc()) {
        $row['est_reservee'] = $row['est_reservee'] == 0 ? 'Disponible' : 'Réservée';
        $salles[] = $row;
    }
    echo json_encode($salles);
    exit;
}

// Si une réservation est effectuée via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    $id_salle = intval($_POST['id_salle']);
    $nom_personne = $_POST['nom_personne'];
    $date_reservation = $_POST['date_reservation'];

    $stmt = $conn->prepare("INSERT INTO reservations (id_salle, nom_personne, date_reservation) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $id_salle, $nom_personne, $date_reservation);
    $stmt->execute();

    echo json_encode(["status" => "success", "message" => "Réservation effectuée avec succès pour $nom_personne le $date_reservation."]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver une salle</title>
    <link rel="stylesheet" href="style.css">
    <script>
        // Charger les salles pour une date spécifique
        function loadSalles(date) {
            fetch(`index.php?ajax=1&date=${date}`)
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('salles-table-body');
                    tableBody.innerHTML = '';
                    data.forEach(salle => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${salle.id}</td>
                            <td>${salle.nom}</td>
                            <td>${salle.capacite}</td>
                            <td>${salle.est_reservee}</td>
                            <td>
                                ${salle.est_reservee === 'Disponible' ? `
                                <form onsubmit="makeReservation(event, ${salle.id}, '${date}')">
                                    <input type="text" name="nom_personne" placeholder="Votre nom" required>
                                    <button type="submit">Réserver</button>
                                </form>` : ''}
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                });
        }

        // Effectuer une réservation
        function makeReservation(event, idSalle, dateReservation) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            formData.append('id_salle', idSalle);
            formData.append('date_reservation', dateReservation);
            formData.append('ajax', 1);

            fetch('index.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                loadSalles(dateReservation); // Recharger les salles après la réservation
            });
        }

        // Charger les salles pour la date sélectionnée au chargement de la page
        document.addEventListener('DOMContentLoaded', () => {
            const dateInput = document.getElementById('date');
            dateInput.addEventListener('change', () => loadSalles(dateInput.value));
            loadSalles(dateInput.value); // Charger pour la date actuelle
        });
    </script>
</head>
<body>
    <h1>Réserver une salle de classe</h1>
    <label for="date">Sélectionnez une date :</label>
    <input type="date" id="date" value="<?= date('Y-m-d') ?>">

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Capacité</th>
                <th>Disponibilité</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="salles-table-body">
            <!-- Contenu généré dynamiquement via JavaScript -->
        </tbody>
    </table>
</body>
</html>
