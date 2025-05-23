<?php
include 'db_connect.php';
include 'includes/header.php';

// --- Traitement de la suppression ---
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM garage WHERE id_garage = $id";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>Garage supprimé avec succès!</p>";
    } else {
        echo "<p style='color:red;'>Erreur lors de la suppression: " . $conn->error . "</p>";
    }
}

// --- Récupération de tous les garages pour affichage ---
$sql = "SELECT * FROM garage ORDER BY date_entretien DESC";
$result = $conn->query($sql);
?>

<h2>Liste des Garages</h2>
<a href="garage_add.php" class="button">Ajouter un nouveau garage</a>

<?php if ($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom Garage</th>
                <th>Travaux Réparation</th>
                <th>Date Entretien</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_garage']; ?></td>
                    <td><?php echo htmlspecialchars($row['travaux_entretien'] ?? '' ); ?></td>
                    <td><?php echo htmlspecialchars($row['travaux_reparation'] ?? '' ); ?></td>
                    <td><?php echo $row['date_entretien']; ?></td>
                    <td>
                        <a href="garage_edit.php?id=<?php echo $row['id_garage']; ?>" class="button">Modifier</a>
                        <a href="garage.php?delete=<?php echo $row['id_garage']; ?>" class="button delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce garage ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucun garage trouvé.</p>
<?php endif; ?>

<?php
$conn->close();
include 'includes/footer.php';
?>