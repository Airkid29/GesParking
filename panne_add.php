<?php
include 'db_connect.php';
include 'includes/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $libelle = $_POST['libelle'];
    // Optionnel: Si vous voulez lier à un garage dès la création
    $id_garage = isset($_POST['id_garage']) && $_POST['id_garage'] != '' ? $_POST['id_garage'] : 'NULL';

    $sql = "INSERT INTO panne (libelle, id_garage) VALUES ('$libelle', $id_garage)";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>Nouvelle panne ajoutée avec succès!</p>";
    } else {
        echo "<p style='color:red;'>Erreur: " . $sql . "<br>" . $conn->error . "</p>";
    }
}

// Récupérer la liste des garages pour le select (si on veut l'inclure)
$garages = $conn->query("SELECT id_garage, travaux_entretien FROM garage");

?>

<h2>Ajouter une Panne</h2>
<form action="panne_add.php" method="post">
    <label for="libelle">Libellé de la Panne:</label>
    <input type="text" id="libelle" name="libelle" required><br>

    <label for="id_garage">Attribuer à un Garage (Optionnel):</label>
    <select id="id_garage" name="id_garage">
        <option value="">-- Sélectionner un garage --</option>
        <?php while ($garage = $garages->fetch_assoc()): ?>
            <option value="<?php echo $garage['id_garage']; ?>"><?php echo htmlspecialchars($garage['travaux_entretien']); ?></option>
        <?php endwhile; ?>
    </select><br>

    <input type="submit" value="Ajouter la Panne">
</form>

<p><a href="panne.php">Retour à la liste des pannes</a></p>

<?php
$conn->close();
include 'includes/footer.php';
?>