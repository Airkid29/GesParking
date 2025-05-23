<?php
include 'db_connect.php';
include 'includes/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $genre = $_POST['genre'];
    $marque = $_POST['marque'];
    $model = $_POST['model'];
    $immatriculation = $_POST['immatriculation']; // Nouveau champ
    $annee_de_mise_en_cir = $_POST['annee_de_mise_en_cir'];

    // Requête SQL pour insérer le nouveau véhicule avec l'immatriculation
    $sql = "INSERT INTO vehicule (genre, marque, model, immatriculation, annee_de_mise_en_cir) VALUES ('$genre', '$marque', '$model', '$immatriculation', $annee_de_mise_en_cir)";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>Nouveau véhicule ajouté avec succès!</p>";
    } else {
        // Gérer les erreurs, notamment pour l'immatriculation UNIQUE
        if ($conn->errno == 1062) { // Code d'erreur pour la violation de contrainte UNIQUE
            echo "<p style='color:red;'>Erreur: Cette immatriculation existe déjà. Veuillez en choisir une autre.</p>";
        } else {
            echo "<p style='color:red;'>Erreur: " . $sql . "<br>" . $conn->error . "</p>";
        }
    }
}
?>

<h2>Ajouter un Véhicule</h2>
<form action="vehicule_add.php" method="post">
    <label for="genre">Genre:</label>
    <input type="text" id="genre" name="genre" required><br>

    <label for="marque">Marque:</label>
    <input type="text" id="marque" name="marque" required><br>

    <label for="model">Modèle:</label>
    <input type="text" id="model" name="model" required><br>

    <label for="immatriculation">Immatriculation:</label>
    <input type="text" id="immatriculation" name="immatriculation" required><br> <label for="annee_de_mise_en_cir">Année de mise en circulation:</label>
    <input type="number" id="annee_de_mise_en_cir" name="annee_de_mise_en_cir" required><br>

    <input type="submit" value="Ajouter le Véhicule">
</form>

<p><a href="vehicule.php">Retour à la liste des véhicules</a></p>

<?php
$conn->close();
include 'includes/footer.php';
?>