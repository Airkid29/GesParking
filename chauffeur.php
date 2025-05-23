<?php
include 'db_connect.php';
include 'includes/header.php';

// --- Traitement de la suppression ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // La suppression d'un chauffeur devrait automatiquement supprimer les associations dans chauffeur_vehicule grâce à ON DELETE CASCADE
    $sql = "DELETE FROM chauffeur WHERE id_chauffeur = $id";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>Chauffeur supprimé avec succès!</p>";
    } else {
        echo "<p style='color:red;'>Erreur lors de la suppression: " . $conn->error . "</p>";
    }
}

// --- Récupération de tous les chauffeurs avec leurs véhicules associés ---
$sql = "SELECT
            c.id_chauffeur,
            c.nom_chauffeur,
            c.prenom_chauffeur,
            c.tel_chauffeur,
            GROUP_CONCAT(CONCAT(v.marque, ' ', v.model) SEPARATOR ', ') AS vehicules_conduits
        FROM
            chauffeur c
        LEFT JOIN
            chauffeur_vehicule cv ON c.id_chauffeur = cv.id_chauffeur
        LEFT JOIN
            vehicule v ON cv.id_vehicule = v.id_vehicule
        GROUP BY
            c.id_chauffeur
        ORDER BY
            c.nom_chauffeur ASC";
$result = $conn->query($sql);
?>

<h2>Liste des Chauffeurs</h2>
<a href="chauffeur_add.php" class="button">Ajouter un nouveau chauffeur</a>

<?php if ($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Téléphone</th>
                <th>Véhicules Conduits</th> <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_chauffeur']; ?></td>
                    <td><?php echo htmlspecialchars($row['nom_chauffeur']); ?></td>
                    <td><?php echo htmlspecialchars($row['prenom_chauffeur']); ?></td>
                    <td><?php echo htmlspecialchars($row['tel_chauffeur'] ?? ''); ?></td>
                    <td>
                        <?php echo htmlspecialchars($row['vehicules_conduits'] ?? 'Aucun'); ?>
                    </td>
                    <td>
                        <a href="chauffeur_edit.php?id=<?php echo $row['id_chauffeur']; ?>" class="button">Modifier</a>
                        <a href="chauffeur.php?delete=<?php echo $row['id_chauffeur']; ?>" class="button delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce chauffeur ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucun chauffeur trouvé.</p>
<?php endif; ?>

<?php
$conn->close();
include 'includes/footer.php';
?>