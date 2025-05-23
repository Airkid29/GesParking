<?php
include 'db_connect.php';
include 'includes/header.php';

// --- Traitement de l la sortie du véhicule (clôturer l'entrée) ---
if (isset($_GET['cloturer'])) {
    $id_entree_a_cloturer = $_GET['cloturer'];
    $current_datetime = date('Y-m-d H:i:s'); // Date et heure actuelles

    $sql_cloture = "UPDATE vehicule_garage_entree SET date_sortie = '$current_datetime' WHERE id_entree = $id_entree_a_cloturer AND date_sortie IS NULL";
    
    if ($conn->query($sql_cloture) === TRUE) {
        if ($conn->affected_rows > 0) {
            echo "<p style='color:green;'>Entrée en garage clôturée avec succès!</p>";
        } else {
            echo "<p style='color:orange;'>L'entrée a déjà été clôturée ou n'existe pas.</p>";
        }
    } else {
        echo "<p style='color:red;'>Erreur lors de la clôture de l'entrée: " . $conn->error . "</p>";
    }
}


// --- Traitement de la suppression ---
if (isset($_GET['delete'])) {
    $id_entree_a_supprimer = $_GET['delete'];
    $sql_delete = "DELETE FROM vehicule_garage_entree WHERE id_entree = $id_entree_a_supprimer";

    if ($conn->query($sql_delete) === TRUE) {
        echo "<p style='color:green;'>Entrée en garage supprimée avec succès!</p>";
    } else {
        echo "<p style='color:red;'>Erreur lors de la suppression: " . $conn->error . "</p>";
    }
}


// --- Traitement de l'ajout d'une nouvelle entrée (quand le formulaire est soumis) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_vehicule = $_POST['id_vehicule'];
    $id_garage = $_POST['id_garage'];
    $date_entree = $_POST['date_entree']; // Optionnel: laisser l'utilisateur choisir, sinon CURRENT_TIMESTAMP

    // Si date_entree est vide, utiliser la date et heure actuelles
    if (empty($date_entree)) {
        $date_entree = date('Y-m-d H:i:s');
    }

    $sql_insert = "INSERT INTO vehicule_garage_entree (id_vehicule, id_garage, date_entree) VALUES ($id_vehicule, $id_garage, '$date_entree')";

    if ($conn->query($sql_insert) === TRUE) {
        echo "<p style='color:green;'>Nouvelle entrée en garage enregistrée avec succès!</p>";
    } else {
        echo "<p style='color:red;'>Erreur lors de l'enregistrement de l'entrée: " . $conn->error . "</p>";
    }
}


// --- Récupérer les listes des véhicules et garages pour le formulaire ---
$vehicules_disponibles = $conn->query("SELECT id_vehicule, CONCAT(marque, ' ', model) as nom_complet FROM vehicule ORDER BY marque, model");
$garages_disponibles = $conn->query("SELECT id_garage, travaux_entretien FROM garage ORDER BY travaux_entretien");


$filter_vehicule_id = '';
if (isset($_GET['id_vehicule_filter']) && is_numeric($_GET['id_vehicule_filter'])) {
    $filter_vehicule_id = (int)$_GET['id_vehicule_filter'];
}

$sql_list_entries = "SELECT
                        vge.id_entree,
                        v.marque,
                        v.model,
                        g.travaux_entretien AS nom_garage,
                        vge.date_entree,
                        vge.date_sortie
                      FROM
                        vehicule_garage_entree vge
                      JOIN
                        vehicule v ON vge.id_vehicule = v.id_vehicule
                      JOIN
                        garage g ON vge.id_garage = g.id_garage";
                      
if (!empty($filter_vehicule_id)) {
    $sql_list_entries .= " WHERE vge.id_vehicule = $filter_vehicule_id";
    echo "<h3>Historique des Entrées/Sorties pour le véhicule " . htmlspecialchars($filter_vehicule_id) . "</h3>";
    echo "<p><a href='vehicule_garage.php' class='button' style='background-color: #4CAF50;'>Voir tout l'historique</a></p>";
} else {
    echo "<h3>Historique des Entrées/Sorties</h3>";
}

$sql_list_entries .= " ORDER BY vge.date_entree DESC"; // Ordre final après le WHERE

$result_entries = $conn->query($sql_list_entries);

// --- Récupération de toutes les entrées en garage pour affichage ---
$sql_list_entries = "SELECT
                        vge.id_entree,
                        v.marque,
                        v.model,
                        g.travaux_entretien AS nom_garage,
                        vge.date_entree,
                        vge.date_sortie
                      FROM
                        vehicule_garage_entree vge
                      JOIN
                        vehicule v ON vge.id_vehicule = v.id_vehicule
                      JOIN
                        garage g ON vge.id_garage = g.id_garage
                      ORDER BY
                        vge.date_entree DESC";
$result_entries = $conn->query($sql_list_entries);
?>

<h2>Gestion des Entrées/Sorties Véhicules - Garage</h2>

<h3>Enregistrer une nouvelle entrée</h3>
<form action="vehicule_garage.php" method="post">
    <label for="id_vehicule">Véhicule:</label>
    <select name="id_vehicule" id="id_vehicule" required>
        <option value="">-- Sélectionner un véhicule --</option>
        <?php while($vehicule = $vehicules_disponibles->fetch_assoc()): ?>
            <option value="<?php echo $vehicule['id_vehicule']; ?>"><?php echo htmlspecialchars($vehicule['nom_complet']); ?></option>
        <?php endwhile; ?>
    </select><br>

    <label for="id_garage">Garage:</label>
    <select name="id_garage" id="id_garage" required>
        <option value="">-- Sélectionner un garage --</option>
        <?php while($garage = $garages_disponibles->fetch_assoc()): ?>
            <option value="<?php echo $garage['id_garage']; ?>"><?php echo htmlspecialchars($garage['travaux_entretien'] ?? ''); ?></option>
        <?php endwhile; ?>
    </select><br>

    <label for="date_entree">Date et heure d'entrée (laisser vide pour maintenant):</label>
    <input type="datetime-local" id="date_entree" name="date_entree"><br>

    <input type="submit" value="Enregistrer l'entrée">
</form>

<h3>Historique des Entrées/Sorties</h3>

<?php if ($result_entries->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID Entrée</th>
                <th>Véhicule</th>
                <th>Garage</th>
                <th>Date Entrée</th>
                <th>Date Sortie</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result_entries->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_entree']; ?></td>
                    <td><?php echo htmlspecialchars($row['marque'] . ' ' . $row['model']); ?></td>
                    <td><?php echo htmlspecialchars($row['nom_garage'] ?? ''); ?></td>
                    <td><?php echo $row['date_entree']; ?></td>
                    <td><?php echo $row['date_sortie'] ?? 'Toujours au garage'; ?></td>
                    <td>
                        <?php
                        if ($row['date_sortie'] === NULL) {
                            echo '<span style="color: blue; font-weight: bold;">Au garage</span>';
                        } else {
                            echo 'Terminé';
                        }
                        ?>
                    </td>
                    <td>
                        <?php if ($row['date_sortie'] === NULL): ?>
                            <a href="vehicule_garage.php?cloturer=<?php echo $row['id_entree']; ?>" class="button" onclick="return confirm('Confirmer la sortie de ce véhicule du garage ?');">Clôturer sortie</a>
                        <?php endif; ?>
                        <a href="vehicule_garage.php?delete=<?php echo $row['id_entree']; ?>" class="button delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entrée ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucune entrée/sortie de véhicule enregistrée.</p>
<?php endif; ?>

<?php
$conn->close();
include 'includes/footer.php';
?>