<?php
include 'db_connect.php';
include 'includes/header.php';

// Handle deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM vehicule WHERE id_vehicule = $id";
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>Véhicule supprimé avec succès!</p>";
    } else {
        echo "<p style='color:red;'>Erreur lors de la suppression: " . $conn->error . "</p>";
    }
}

// Fetch all vehicles
$sql = "SELECT * FROM vehicule";
$result = $conn->query($sql);
?>

<h2>Liste des Véhicules</h2>
<a href="vehicule_add.php" class="button">Ajouter un nouveau véhicule</a>
<a href="vehicule_garage.php?id_vehicule_filter=<?php echo $row['id_vehicule']; ?>" class="button" style="background-color: #6c757d;">Historique Garage</a>

<?php if ($result->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Genre</th>
                <th>Marque</th>
                <th>Modèle</th>
                <th>Année Mise en Circulation</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_vehicule']; ?></td>
                    <td><?php echo $row['genre']; ?></td>
                    <td><?php echo $row['marque']; ?></td>
                    <td><?php echo $row['model']; ?></td>
                    <td><?php echo $row['annee_de_mise_en_cir']; ?></td>
                    <td>
                        <a href="vehicule_edit.php?id=<?php echo $row['id_vehicule']; ?>" class="button">Modifier</a>
                        <a href="vehicule.php?delete=<?php echo $row['id_vehicule']; ?>" class="button delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?');">Supprimer</a>
                        
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucun véhicule trouvé.</p>
<?php endif; ?>

<?php
$conn->close();
include 'includes/footer.php';
?>