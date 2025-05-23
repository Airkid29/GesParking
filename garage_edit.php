<?php
include 'db_connect.php';
include 'includes/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $travaux_entretien = $_POST['travaux_entretien'];
    $travaux_reparation = $_POST['travaux_reparation'];
    $date_entretien = $_POST['date_entretien'];

    $sql = "INSERT INTO garage (travaux_entretien, travaux_reparation, date_entretien) VALUES ('$travaux_entretien', '$travaux_reparation', '$date_entretien')";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>Nouveau garage ajouté avec succès!</p>";
    } else {
        echo "<p style='color:red;'>Erreur: " . $sql . "<br>" . $conn->error . "</p>";
    }
}
?>

<h2>Ajouter un Garage</h2>
<form action="garage_add.php" method="post">
    <label for="travaux_entretien">Nom du garage:</label>
    <textarea id="travaux_entretien" name="travaux_entretien"></textarea><br>

    <label for="travaux_reparation">Travaux de Réparation:</label>
    <textarea id="travaux_reparation" name="travaux_reparation"></textarea><br>

    <label for="date_entretien">Date du dernier Entretien/Réparation:</label>
    <input type="date" id="date_entretien" name="date_entretien"><br>

    <input type="submit" value="Ajouter le Garage">
</form>

<p><a href="garage.php">Retour à la liste des garages</a></p>

<?php
$conn->close();
include 'includes/footer.php';
?>