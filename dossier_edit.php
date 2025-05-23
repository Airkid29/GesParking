<?php
include 'db_connect.php';
include 'includes/header.php';

$dossier = null;
$vehicules_selectionnes_ids = []; // Pour stocker les IDs des véhicules déjà associés

// --- Récupération des données du dossier à modifier ---
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM dossier WHERE id_dossier = $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $dossier = $result->fetch_assoc();

        // Récupérer les véhicules déjà associés à ce dossier
        $sql_associated_vehicules = "SELECT id_vehicule FROM dossier_vehicule WHERE id_dossier = $id";
        $result_associated = $conn->query($sql_associated_vehicules);
        while ($row = $result_associated->fetch_assoc()) {
            $vehicules_selectionnes_ids[] = $row['id_vehicule'];
        }

    } else {
        echo "<p style='color:red;'>Dossier non trouvé.</p>";
        $dossier = null;
    }
}

// Récupérer la liste de tous les véhicules disponibles pour la sélection
$vehicules_disponibles = $conn->query("SELECT id_vehicule, CONCAT(marque, ' ', model, ' (', annee_de_mise_en_cir, ')') as nom_complet FROM vehicule ORDER BY marque, model");


// --- Traitement de la modification (quand le formulaire est soumis) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_dossier'])) {
    $id = $_POST['id_dossier'];
    $date_dossier = $_POST['date'];
    $vehicules_choisis = isset($_POST['vehicules']) ? $_POST['vehicules'] : [];

    $conn->begin_transaction();
    $success = true;

    // 1. Mettre à jour les informations du dossier
    $sql_update_dossier = "UPDATE dossier SET date='$date_dossier' WHERE id_dossier=$id";
    if (!$conn->query($sql_update_dossier)) {
        $success = false;
    }

    // 2. Mettre à jour les associations de véhicules
    if ($success) {
        // Supprimer toutes les anciennes associations pour ce dossier
        $sql_delete_associations = "DELETE FROM dossier_vehicule WHERE id_dossier = $id";
        if (!$conn->query($sql_delete_associations)) {
            $success = false;
        }
    }

    if ($success) {
        // Insérer les nouvelles associations
        if (!empty($vehicules_choisis)) {
            foreach ($vehicules_choisis as $vehicule_id) {
                $sql_insert_association = "INSERT INTO dossier_vehicule (id_dossier, id_vehicule) VALUES ($id, $vehicule_id)";
                if (!$conn->query($sql_insert_association)) {
                    $success = false;
                    break;
                }
            }
        }
    }

    // Gérer la transaction
    if ($success) {
        $conn->commit();
        echo "<p style='color:green;'>Dossier et associations mis à jour avec succès!</p>";
        // Re-récupérer les données pour que le formulaire affiche la mise à jour immédiate
        $sql = "SELECT * FROM dossier WHERE id_dossier = $id";
        $result = $conn->query($sql);
        $dossier = $result->fetch_assoc();
        // Re-récupérer les IDs des véhicules associés après la mise à jour
        $vehicules_selectionnes_ids = [];
        $sql_associated_vehicules = "SELECT id_vehicule FROM dossier_vehicule WHERE id_dossier = $id";
        $result_associated = $conn->query($sql_associated_vehicules);
        while ($row = $result_associated->fetch_assoc()) {
            $vehicules_selectionnes_ids[] = $row['id_vehicule'];
        }

    } else {
        $conn->rollback();
        echo "<p style='color:red;'>Erreur lors de la mise à jour du dossier ou des associations: " . $conn->error . "</p>";
    }
}
?>

<h2>Modifier le Dossier</h2>

<?php if ($dossier): ?>
    <form action="dossier_edit.php" method="post">
        <input type="hidden" name="id_dossier" value="<?php echo $dossier['id_dossier']; ?>">

        <label for="date">Date du dossier:</label>
        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($dossier['date']); ?>" required><br>

        <label for="vehicules">Véhicules associés (maintenez Ctrl/Cmd pour sélectionner plusieurs):</label>
        <select name="vehicules[]" id="vehicules" multiple size="5">
            <?php
            if ($vehicules_disponibles->num_rows > 0) {
                while($vehicule = $vehicules_disponibles->fetch_assoc()) {
                    $selected = in_array($vehicule['id_vehicule'], $vehicules_selectionnes_ids) ? 'selected' : '';
                    echo '<option value="' . $vehicule['id_vehicule'] . '" ' . $selected . '>' . htmlspecialchars($vehicule['nom_complet']) . '</option>';
                }
            } else {
                echo '<option value="" disabled>Aucun véhicule disponible</option>';
            }
            ?>
        </select><br>
        <small>Les véhicules déjà associés sont pré-sélectionnés. Modifiez la sélection pour mettre à jour.</small>
        <br><br>

        <input type="submit" value="Mettre à jour le Dossier">
    </form>
<?php else: ?>
    <p>Dossier introuvable pour modification.</p>
<?php endif; ?>

<p><a href="dossier.php">Retour à la liste des dossiers</a></p>

<?php
$conn->close();
include 'includes/footer.php';
?>