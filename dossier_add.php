<?php
include 'db_connect.php';
include 'includes/header.php';

// Récupérer la liste de tous les véhicules pour la sélection
$vehicules_disponibles = $conn->query("SELECT id_vehicule, CONCAT(marque, ' ', model, ' (', annee_de_mise_en_cir, ')') as nom_complet FROM vehicule ORDER BY marque, model");


// --- Traitement de l'ajout (quand le formulaire est soumis) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date_dossier = $_POST['date'];
    $vehicules_selectionnes = isset($_POST['vehicules']) ? $_POST['vehicules'] : []; // Récupère les IDs des véhicules sélectionnés

    // Démarrer une transaction pour s'assurer que tout est inséré ou rien
    $conn->begin_transaction();
    $success = true;

    // 1. Insérer le nouveau dossier
    $sql_dossier = "INSERT INTO dossier (date) VALUES ('$date_dossier')";
    if ($conn->query($sql_dossier) === TRUE) {
        $new_dossier_id = $conn->insert_id; // Récupère l'ID du dossier nouvellement créé

        // 2. Associer les véhicules sélectionnés au dossier
        if (!empty($vehicules_selectionnes)) {
            foreach ($vehicules_selectionnes as $vehicule_id) {
                $sql_association = "INSERT INTO dossier_vehicule (id_dossier, id_vehicule) VALUES ($new_dossier_id, $vehicule_id)";
                if (!$conn->query($sql_association)) {
                    $success = false;
                    break; // Sortir de la boucle si une insertion échoue
                }
            }
        }
    } else {
        $success = false;
    }

    // Gérer la transaction
    if ($success) {
        $conn->commit(); // Tout est bon, valider les changements
        echo "<p style='color:green;'>Nouveau dossier et associations ajoutés avec succès!</p>";
    } else {
        $conn->rollback(); // Annuler toutes les opérations si une erreur est survenue
        echo "<p style='color:red;'>Erreur lors de l'ajout du dossier ou des associations: " . $conn->error . "</p>";
    }
}
?>

<h2>Ajouter un Dossier</h2>
<form action="dossier_add.php" method="post">
    <label for="date">Date du dossier:</label>
    <input type="date" id="date" name="date" required><br>

    <label for="vehicules">Véhicules associés (maintenez Ctrl/Cmd pour sélectionner plusieurs):</label>
    <select name="vehicules[]" id="vehicules" multiple size="5">
        <?php
        if ($vehicules_disponibles->num_rows > 0) {
            while($vehicule = $vehicules_disponibles->fetch_assoc()) {
                echo '<option value="' . $vehicule['id_vehicule'] . '">' . htmlspecialchars($vehicule['nom_complet']) . '</option>';
            }
        } else {
            echo '<option value="" disabled>Aucun véhicule disponible</option>';
        }
        ?>
    </select><br>
    <small>Si aucun véhicule n'est sélectionné, le dossier sera créé sans véhicule associé.</small>
    <br><br>

    <input type="submit" value="Ajouter le Dossier">
</form>

<p><a href="dossier.php">Retour à la liste des dossiers</a></p>

<?php
$conn->close();
include 'includes/footer.php';
?>