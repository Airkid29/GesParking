<?php
include 'db_connect.php';
include 'includes/header.php';

// Récupérer la liste de tous les véhicules pour la sélection
$vehicules_disponibles = $conn->query("SELECT id_vehicule, CONCAT(marque, ' ', model, ' (', annee_de_mise_en_cir, ')') as nom_complet FROM vehicule ORDER BY marque, model");

// --- Traitement de l'ajout (quand le formulaire est soumis) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_chauffeur = $_POST['nom_chauffeur'];
    $prenom_chauffeur = $_POST['prenom_chauffeur'];
    $tel_chauffeur = $_POST['tel_chauffeur'] ?? ''; // Utiliser ?? pour gérer les valeurs NULL ou vides
    $vehicules_selectionnes = isset($_POST['vehicules']) ? $_POST['vehicules'] : [];

    // Démarrer une transaction
    $conn->begin_transaction();
    $success = true;

    // 1. Insérer le nouveau chauffeur
    $sql_chauffeur = "INSERT INTO chauffeur (nom_chauffeur, prenom_chauffeur, tel_chauffeur) VALUES ('$nom_chauffeur', '$prenom_chauffeur', " . ($tel_chauffeur === '' ? 'NULL' : "'$tel_chauffeur'") . ")";
    
    if ($conn->query($sql_chauffeur) === TRUE) {
        $new_chauffeur_id = $conn->insert_id; // Récupère l'ID du chauffeur nouvellement créé

        // 2. Associer les véhicules sélectionnés au chauffeur
        if (!empty($vehicules_selectionnes)) {
            foreach ($vehicules_selectionnes as $vehicule_id) {
                $sql_association = "INSERT INTO chauffeur_vehicule (id_chauffeur, id_vehicule) VALUES ($new_chauffeur_id, $vehicule_id)";
                if (!$conn->query($sql_association)) {
                    $success = false;
                    break;
                }
            }
        }
    } else {
        $success = false;
    }

    // Gérer la transaction
    if ($success) {
        $conn->commit();
        echo "<p style='color:green;'>Nouveau chauffeur et associations ajoutés avec succès!</p>";
    } else {
        $conn->rollback();
        echo "<p style='color:red;'>Erreur lors de l'ajout du chauffeur ou des associations: " . $conn->error . "</p>";
    }
}
?>

<h2>Ajouter un Chauffeur</h2>
<form action="chauffeur_add.php" method="post">
    <label for="nom_chauffeur">Nom du Chauffeur:</label>
    <input type="text" id="nom_chauffeur" name="nom_chauffeur" required><br>

    <label for="prenom_chauffeur">Prénom du Chauffeur:</label>
    <input type="text" id="prenom_chauffeur" name="prenom_chauffeur" required><br>

    <label for="tel_chauffeur">Téléphone du Chauffeur:</label>
    <input type="text" id="tel_chauffeur" name="tel_chauffeur"><br>

    <label for="vehicules">Véhicules conduits (maintenez Ctrl/Cmd pour sélectionner plusieurs):</label>
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
    <small>Si aucun véhicule n'est sélectionné, le chauffeur sera créé sans véhicule associé.</small>
    <br><br>

    <input type="submit" value="Ajouter le Chauffeur">
</form>

<p><a href="chauffeur.php">Retour à la liste des chauffeurs</a></p>

<?php
$conn->close();
include 'includes/footer.php';
?>