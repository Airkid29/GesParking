<?php
include 'db_connect.php';
include 'includes/header.php';

$panne = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM panne WHERE id_panne = $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $panne = $result->fetch_assoc();
    } else {
        echo "<p style='color:red;'>Panne non trouvée.</p>";
        $panne = null;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_panne'])) {
    $id = $_POST['id_panne'];
    $libelle = $_POST['libelle'];
    $id_garage = isset($_POST['id_garage']) && $_POST['id_garage'] != '' ? $_POST['id_garage'] : 'NULL';

    $sql = "UPDATE panne SET libelle='$libelle', id_garage=$id_garage WHERE id_panne=$id";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>Panne mise à jour avec succès!</p>";
        $sql = "SELECT * FROM panne WHERE id_panne = $id";
        $result = $conn->query($sql);
        $panne = $result->fetch_assoc();
    } else {
        echo "<p style='color:red;'>Erreur lors de la mise à jour: " . $conn->error . "</p>";
    }
}

// Récupérer la liste des garages pour le select
$garages = $conn->query("SELECT id_garage, travaux_entretien FROM garage");

?>

<h2>Modifier la Panne</h2>

<?php if ($panne): ?>
    <form action="panne_edit.php" method="post">
        <input type="hidden" name="id_panne" value="<?php echo $panne['id_panne']; ?>">

        <label for="libelle">Libellé de la Panne:</label>
        <input type="text" id="libelle" name="libelle" value="<?php echo htmlspecialchars($panne['libelle']); ?>" required><br>

        <label for="id_garage">Attribuer à un Garage (Optionnel):</label>
        <select id="id_garage" name="id_garage">
            <option value="">-- Sélectionner un garage --</option>
            <?php while ($garage = $garages->fetch_assoc()): ?>
                <option value="<?php echo $garage['id_garage']; ?>" <?php echo ($panne['id_garage'] == $garage['id_garage']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($garage['travaux_entretien']); ?>
                </option>
            <?php endwhile; ?>
        </select><br>

        <input type="submit" value="Mettre à jour la Panne">
    </form>
<?php else: ?>
    <p>Panne introuvable pour modification.</p>
<?php endif; ?>

<p><a href="panne.php">Retour à la liste des pannes</a></p>

<?php
$conn->close();
include 'includes/footer.php';
?>