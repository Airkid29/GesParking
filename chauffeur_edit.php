<?php
include 'db_connect.php';
include 'includes/header.php';

$chauffeur = null;
$vehicules_selectionnes_ids = []; // Pour stocker les IDs des véhicules déjà associés

// --- Récupération des données du chauffeur à modifier ---
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM chauffeur WHERE id_chauffeur = $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $chauffeur = $result->fetch_assoc();

        // Récupérer les véhicules déjà associés à ce chauffeur
        $sql_associated_vehicules = "SELECT id_vehicule FROM chauffeur_vehicule WHERE id_chauffeur = $id";
        $result_associated = $conn->query($sql_associated_vehicules);
        while ($row = $result_associated->fetch_assoc()) {
            $vehicules_selectionnes_ids[] = $row['id_vehicule'];
        }

    } else {
        echo "<p style='color:red;'>Chauffeur non trouvé.</p>";
        $chauffeur = null;
    }
}

// Récupérer la liste de tous les véhicules disponibles pour la sélection
$vehicules_disponibles = $conn->query("SELECT id_vehicule, CONCAT(marque, ' ', model, ' (', annee_de_mise_en_cir, ')') as nom_complet FROM vehicule ORDER BY marque, model");


// --- Traitement de la modification (quand le formulaire est soumis) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_chauffeur'])) {
    $id = $_POST['id_chauffeur'];
    $nom_chauffeur = $_POST['nom_chauffeur'];
    $prenom_chauffeur = $_POST['prenom_chauffeur'];
    $tel_chauffeur = $_POST['tel_chauffeur'] ?? '';
    $vehicules_choisis = isset($_POST['vehicules']) ? $_POST['vehicules'] : [];

    $conn->begin_transaction();
    $success = true;

    // 1. Mettre à jour les informations du chauffeur
    $sql_update_chauffeur = "UPDATE chauffeur SET nom_chauffeur='$nom_chauffeur', prenom_chauffeur='$prenom_chauffeur', tel_chauffeur=" . ($tel_chauffeur === '' ? 'NULL' : "'$tel_chauffeur'") . " WHERE id_chauffeur=$id";
    if (!$conn->query($sql_update_chauffeur)) {
        $success = false;
    }

    // 2. Mettre à jour les associations de véhicules
    if ($success) {
        // Supprimer toutes les anciennes associations pour ce chauffeur
        $sql_delete_associations = "DELETE FROM chauffeur_vehicule WHERE id_chauffeur = $id";
        if (!$conn->query($sql_delete_associations)) {
            $success = false;
        }
    }

    if ($success) {
        // Insérer les nouvelles associations
        if (!empty($vehicules_choisis)) {
            foreach ($vehicules_choisis as $vehicule_id) {
                $sql_insert_association = "INSERT INTO chauffeur_vehicule (id_chauffeur, id_vehicule) VALUES ($id, $vehicule_id)";
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
        echo "<p style='color:green;'>Chauffeur et associations mis à jour avec succès!</p>";
        // Re-récupérer les données pour que le formulaire affiche la mise à jour immédiate
        $sql = "SELECT * FROM chauffeur WHERE id_chauffeur = $id";
        $result = $conn->query($sql);
        $chauffeur = $result->fetch_assoc();
        // Re-récupérer les IDs des véhicules associés après la mise à jour
        $vehicules_selectionnes_ids = [];
        $sql_associated_vehicules = "SELECT id_vehicule FROM chauffeur_vehicule WHERE id_chauffeur = $id";
        $result_associated = $conn->query($sql_associated_vehicules);
        while ($row = $result_associated->fetch_assoc()) {
            $vehicules_selectionnes_ids[] = $row['id_vehicule'];
        }

    } else {
        $conn->rollback();
        echo "<p style='color:red;'>Erreur lors de la mise à jour du chauffeur ou des associations: " . $conn->error . "</p>";
    }
}
?>

<h2>Modifier le Chauffeur</h2>

<?php if ($chauffeur): ?>
    <form action="chauffeur_edit.php" method="post">
        <input type="hidden" name="id_chauffeur" value="<?php echo $chauffeur['id_chauffeur']; ?>">

        <label for="nom_chauffeur">Nom du Chauffeur:</label>
        <input type="text" id="nom_chauffeur" name="nom_chauffeur" value="<?php echo htmlspecialchars($chauffeur['nom_chauffeur']); ?>" required><br>

        <label for="prenom_chauffeur">Prénom du Chauffeur:</label>
        <input type="text" id="prenom_chauffeur" name="prenom_chauffeur" value="<?php echo htmlspecialchars($chauffeur['prenom_chauffeur']); ?>" required><br>

        <label for="tel_chauffeur">Téléphone du Chauffeur:</label>
        <input type="text" id="tel_chauffeur" name="tel_chauffeur" value="<?php echo htmlspecialchars($chauffeur['tel_chauffeur'] ?? ''); ?>"><br>

        <label for="vehicules">Véhicules conduits (maintenez Ctrl/Cmd pour sélectionner plusieurs):</label>
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

        <input type="submit" value="Mettre à jour le Chauffeur">
    </form>
<?php else: ?>
    <p>Chauffeur introuvable pour modification.</p>
<?php endif; ?>

<p><a href="chauffeur.php">Retour à la liste des chauffeurs</a></p>

<?php
$conn->close();
include 'includes/footer.php';
?>