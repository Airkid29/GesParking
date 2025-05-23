<?php
include 'db_connect.php';
include 'includes/header.php';

// --- Traitement de la suppression ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // La suppression d'un dossier devrait automatiquement supprimer les associations dans dossier_vehicule grâce à ON DELETE CASCADE
    $sql = "DELETE FROM dossier WHERE id_dossier = $id";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>Dossier supprimé avec succès!</p>";
    } else {
        echo "<p style='color:red;'>Erreur lors de la suppression: " . $conn->error . "</p>";
    }
}

// --- Récupération de tous les dossiers avec leurs véhicules associés ---
// Utilisation de GROUP_CONCAT pour regrouper tous les noms de véhicules par dossier
$sql = "SELECT
            d.id_dossier,
            d.date,
            GROUP_CONCAT(CONCAT(v.marque, ' ', v.model) SEPARATOR ', ') AS vehicules_associes
        FROM
            dossier d
        LEFT JOIN
            dossier_vehicule dv ON d.id_dossier = dv.id_dossier
        LEFT JOIN
            vehicule v ON dv.id_vehicule = v.id_vehicule
        GROUP BY
            d.id_dossier
        ORDER BY
            d.date DESC";
$result = $conn->query($sql);
?>

<h2>Liste des Dossiers</h2>
<a href="dossier_add.php" class="button">Ajouter un nouveau dossier</a>

<?php if ($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Véhicules Associés</th> <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_dossier']; ?></td>
                    <td><?php echo $row['date']; ?></td>
                    <td>
                        <?php echo htmlspecialchars($row['vehicules_associes'] ?? 'Aucun'); ?>
                    </td>
                    <td>
                        <a href="dossier_edit.php?id=<?php echo $row['id_dossier']; ?>" class="button">Modifier</a>
                        <a href="dossier.php?delete=<?php echo $row['id_dossier']; ?>" class="button delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce dossier ? Cette action est irréversible.');">Supprimer</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucun dossier trouvé.</p>
<?php endif; ?>

<?php
$conn->close();
include 'includes/footer.php';
?>