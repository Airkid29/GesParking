<?php
include 'db_connect.php';
include 'includes/header.php';

// --- Traitement de la suppression ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM panne WHERE id_panne = $id";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>Panne supprimée avec succès!</p>";
    } else {
        echo "<p style='color:red;'>Erreur lors de la suppression: " . $conn->error . "</p>";
    }
}

// --- Récupération de toutes les pannes pour affichage (avec le nom du garage associé si existant) ---
$sql = "SELECT p.*, g.travaux_entretien as nom_garage
        FROM panne p
        LEFT JOIN garage g ON p.id_garage = g.id_garage
        ORDER BY p.id_panne DESC";
$result = $conn->query($sql);
?>

<h2>Liste des Pannes</h2>
<a href="panne_add.php" class="button">Ajouter une nouvelle panne</a>

<?php if ($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Libellé</th>
                <th>Garage Associé</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_panne']; ?></td>
                    <td><?php echo htmlspecialchars($row['libelle']); ?></td>
                    <td><?php echo $row['nom_garage'] ? htmlspecialchars($row['nom_garage']) : 'Non attribué'; ?></td>
                    <td>
                        <a href="panne_edit.php?id=<?php echo $row['id_panne']; ?>" class="button">Modifier</a>
                        <a href="panne.php?delete=<?php echo $row['id_panne']; ?>" class="button delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette panne ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucune panne trouvée.</p>
<?php endif; ?>

<?php
$conn->close();
include 'includes/footer.php';
?>