<?php
$host = 'mariadb';
$user = 'root';
$pass = 'example';
$db = 'gestion_salles';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Gestion de la réservation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_salle = intval($_POST['id_salle']);
    $nom_personne = $_POST['nom_personne'];

    $stmt = $conn->prepare("INSERT INTO reservations (id_salle, nom_personne) VALUES (?, ?)");
    $stmt->bind_param("is", $id_salle, $nom_personne);
    $stmt->execute();

    $conn->query("UPDATE salles_de_classe SET disponible = 0 WHERE id = $id_salle");

    echo "<p>Réservation effectuée avec succès pour $nom_personne.</p>";
}

// Récupérer les salles disponibles
$result = $conn->query("SELECT * FROM salles_de_classe");

echo "<h1>Réserver une salle de classe</h1>";
echo "<table border='1'>
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Capacité</th>
        <th>Disponibilité</th>
        <th>Action</th>
    </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['nom']}</td>
        <td>{$row['capacite']}</td>
        <td>" . ($row['disponible'] ? 'Disponible' : 'Réservée') . "</td>
        <td>";
    if ($row['disponible']) {
        echo "<form method='POST'>
            <input type='hidden' name='id_salle' value='{$row['id']}' />
            <input type='text' name='nom_personne' placeholder='Votre nom' required />
            <button type='submit'>Réserver</button>
        </form>";
    }
    echo "</td></tr>";
}
echo "</table>";
?>
