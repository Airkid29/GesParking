<?php
include 'db_connect.php';
include 'includes/header.php';

$vehicule = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM vehicule WHERE id_vehicule = $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $vehicule = $result->fetch_assoc();
    } else {
        echo "<p style='color:red;'>Véhicule non trouvé.</p>";
        $vehicule = null;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_vehicule'])) {
    $id = $_POST['id_vehicule'];
    $genre = $_POST['genre'];
    $marque = $_POST['marque'];
    $model = $_POST['model'];
    $immatriculation = $_POST['immatriculation']; // Nouveau champ
    $annee_de_mise_en_cir = $_POST['annee_de_mise_en_cir'];

    // Requête SQL pour mettre à jour le véhicule
    $sql = "UPDATE vehicule SET genre='$genre', marque='$marque', model='$model', immatriculation='$immatriculation', annee_de_mise_en_cir=$annee_de_mise_en_cir WHERE id_vehicule=$id";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>Véhicule mis à jour avec succès!</p>";
        // Re-récupérer les données pour que le formulaire affiche la mise à jour immédiate
        $sql = "SELECT * FROM vehicule WHERE id_vehicule = $id";
        $result = $conn->query($sql);
        $vehicule = $result->fetch_assoc();
    } else {
        // Gérer les erreurs, notamment pour l'immatriculation UNIQUE
        if ($conn->errno == 1062) {
            echo "<p style='color:red;'>Erreur: Cette immatriculation existe déjà. Veuillez en choisir une autre.</p>";
        } else {
            echo "<p style='color:red;'>Erreur lors de la mise à jour: " . $conn->error . "</p>";
        }
    }
}
?>

<h2>Modifier le Véhicule</h2>

<?php if ($vehicule): ?>
    <form action="vehicule_edit.php" method="post">
        <input type="hidden" name="id_vehicule" value="<?php echo $vehicule['id_vehicule']; ?>">

        <label for="genre">Genre:</label>
        <input type="text" id="genre" name="genre" value="<?php echo htmlspecialchars($vehicule['genre']); ?>" required><br>

        <label for="marque">Marque:</label>
        <input type="text" id="marque" name="marque" value="<?php echo htmlspecialchars($vehicule['marque']); ?>" required><br>

        <label for="model">Modèle:</label>
        <input type="text" id="model" name="model" value="<?php echo htmlspecialchars($vehicule['model']); ?>" required><br>

        <label for="immatriculation">Immatriculation:</label>
        <input type="text" id="immatriculation" name="immatriculation" value="<?php echo htmlspecialchars($vehicule['immatriculation']); ?>" required><br> <label for="annee_de_mise_en_cir">Année de mise en circulation:</label>
        <input type="number" id="annee_de_mise_en_cir" name="annee_de_mise_en_cir" value="<?php echo htmlspecialchars($vehicule['annee_de_mise_en_cir']); ?>" required><br>

        <input type="submit" value="Mettre à jour le Véhicule">
    </form>
<?php else: ?>
    <p>Véhicule introuvable pour modification.</p>
<?php endif; ?>

<p><a href="vehicule.php">Retour à la liste des véhicules</a></p>

<?php
$conn->close();
include 'includes/footer.php';
?>