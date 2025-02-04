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
// Gestion de la date sélectionnée
$date = $_GET['date'] ?? date('Y-m-d');

// Afficher les salles avec leur disponibilité pour la date sélectionnée
$result = $conn->query("
    SELECT s.id, s.nom, s.capacite, 
           (SELECT COUNT(*) FROM reservations r WHERE r.id_salle = s.id AND r.date_reservation = '$date') AS est_reservee
    FROM salles_de_classe s
");

echo "<h1>Réserver une salle de classe</h1>";
echo "<form method='GET'>
    <label for='date'>Sélectionnez une date :</label>
    <input type='date' name='date' id='date' value='$date'>
    <button type='submit'>Voir les disponibilités</button>
</form>";

echo "<table border='1'>
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Capacité</th>
        <th>Disponibilité</th>
        <th>Action</th>
    </tr>";

while ($row = $result->fetch_assoc()) {
    $disponibilite = $row['est_reservee'] == 0 ? 'Disponible' : 'Réservée';
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['nom']}</td>
        <td>{$row['capacite']}</td>
        <td>$disponibilite</td>
        <td>";
    if ($disponibilite === 'Disponible') {
        echo "<form method='POST'>
            <input type='hidden' name='id_salle' value='{$row['id']}' />
            <input type='hidden' name='date_reservation' value='$date' />
            <input type='text' name='nom_personne' placeholder='Votre nom' required />
            <button type='submit'>Réserver</button>
        </form>";
    }
    echo "</td></tr>";
}
echo "</table>";

// Gérer une réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_salle = intval($_POST['id_salle']);
    $nom_personne = $_POST['nom_personne'];
    $date_reservation = $_POST['date_reservation'];

    // Ajouter la réservation
    $stmt = $conn->prepare("INSERT INTO reservations (id_salle, nom_personne, date_reservation) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $id_salle, $nom_personne, $date_reservation);
    $stmt->execute();

    echo "<p>Réservation effectuée avec succès pour $nom_personne le $date_reservation.</p>";
}
?>
