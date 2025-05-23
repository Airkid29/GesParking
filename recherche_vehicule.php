<?php
include 'db_connect.php';
include 'includes/header.php';

$vehicule_found = null;
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['immatriculation_recherche'])) {
    $immatriculation_recherche = $conn->real_escape_string($_POST['immatriculation_recherche']); // Sécurisation de l'entrée

    // 1. Rechercher le véhicule par immatriculation
    $sql_vehicule = "SELECT * FROM vehicule WHERE immatriculation = '$immatriculation_recherche'";
    $result_vehicule = $conn->query($sql_vehicule);

    if ($result_vehicule->num_rows > 0) {
        $vehicule_found = $result_vehicule->fetch_assoc();
        $id_vehicule_found = $vehicule_found['id_vehicule'];

        // --- 2. Vérifier son statut actuel (au garage ou non) ---
        $current_garage_info = null;
        $sql_current_garage = "SELECT
                                    g.travaux_entretien AS nom_garage,
                                    vge.date_entree
                                FROM
                                    vehicule_garage_entree vge
                                JOIN
                                    garage g ON vge.id_garage = g.id_garage
                                WHERE
                                    vge.id_vehicule = $id_vehicule_found AND vge.date_sortie IS NULL
                                ORDER BY
                                    vge.date_entree DESC
                                LIMIT 1"; // Le plus récent s'il y en a plusieurs (ce qui ne devrait pas arriver si bien géré)
        $result_current_garage = $conn->query($sql_current_garage);
        if ($result_current_garage->num_rows > 0) {
            $current_garage_info = $result_current_garage->fetch_assoc();
        }

        // --- 3. Quel chauffeur l'a conduit en dernier (ou actuellement) ---
        $last_chauffeur_info = null;
        // Ceci est une simplification. Pour un vrai historique, il faudrait une table avec date de début/fin d'attribution.
        // Ici, on va chercher les chauffeurs actuellement associés
        $sql_current_chauffeur = "SELECT
                                    c.nom_chauffeur,
                                    c.prenom_chauffeur
                                FROM
                                    chauffeur_vehicule cv
                                JOIN
                                    chauffeur c ON cv.id_chauffeur = c.id_chauffeur
                                WHERE
                                    cv.id_vehicule = $id_vehicule_found
                                ORDER BY
                                    c.nom_chauffeur, c.prenom_chauffeur";
        $result_current_chauffeur = $conn->query($sql_current_chauffeur);


        // --- 4. Les dossiers auxquels il est associé ---
        $dossiers_associes_query = "SELECT
                                        d.id_dossier,
                                        d.date
                                    FROM
                                        dossier_vehicule dv
                                    JOIN
                                        dossier d ON dv.id_dossier = d.id_dossier
                                    WHERE
                                        dv.id_vehicule = $id_vehicule_found
                                    ORDER BY
                                        d.date DESC";
        $dossiers_associes = $conn->query($dossiers_associes_query);

        // --- 5. Son historique d'entrée/sortie de garage ---
        $historique_garage_query = "SELECT
                                        g.travaux_entretien AS nom_garage,
                                        vge.date_entree,
                                        vge.date_sortie
                                    FROM
                                        vehicule_garage_entree vge
                                    JOIN
                                        garage g ON vge.id_garage = g.id_garage
                                    WHERE
                                        vge.id_vehicule = $id_vehicule_found
                                    ORDER BY
                                        vge.date_entree DESC";
        $historique_garage = $conn->query($historique_garage_query);

    } else {
        $message = "<p style='color:orange;'>Aucun véhicule trouvé avec l'immatriculation: <strong>" . htmlspecialchars($immatriculation_recherche) . "</strong></p>";
    }
}
?>

<h2>Recherche de Véhicule</h2>

<form action="recherche_vehicule.php" method="post" class="search-form">
    <label for="immatriculation_recherche">Entrez l'immatriculation du véhicule:</label>
    <input type="text" id="immatriculation_recherche" name="immatriculation_recherche" required placeholder="Ex: AB-123-CD">
    <input type="submit" value="Rechercher">
</form>

<?php echo $message; ?>

<?php if ($vehicule_found): ?>
    <div class="vehicule-detail">
        <h3>Détails du Véhicule: <?php echo htmlspecialchars($vehicule_found['immatriculation']); ?></h3>
        <p><strong>Genre:</strong> <?php echo htmlspecialchars($vehicule_found['genre']); ?></p>
        <p><strong>Marque:</strong> <?php echo htmlspecialchars($vehicule_found['marque']); ?></p>
        <p><strong>Modèle:</strong> <?php echo htmlspecialchars($vehicule_found['model']); ?></p>
        <p><strong>Année de mise en circulation:</strong> <?php echo htmlspecialchars($vehicule_found['annee_de_mise_en_cir']); ?></p>
<br>
        <h4>Position et Statut Actuel:</h4>
        <?php if ($current_garage_info): ?>
            <p style="color: blue; font-weight: bold;">Actuellement au garage: <?php echo htmlspecialchars($current_garage_info['nom_garage'] ?? ''); ?> depuis le <?php echo $current_garage_info['date_entree']; ?></p>
            <p><a href="vehicule_garage.php?id_vehicule_filter=<?php echo $id_vehicule_found; ?>" class="button" style="background-color: #3498db;">Voir l'historique complet au garage</a></p>
        <?php else: ?>
            <p style="color: green; font-weight: bold;">Le véhicule n'est plus au garage.</p>
        <?php endif; ?>
<br>
        <h4>Chauffeurs attribués:</h4>
        <?php if ($result_current_chauffeur->num_rows > 0): ?>
            <ul>
                <?php while ($chauffeur = $result_current_chauffeur->fetch_assoc()): ?>
                    <li><?php echo htmlspecialchars($chauffeur['prenom_chauffeur'] . ' ' . $chauffeur['nom_chauffeur']); ?></li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>Ce véhicule n'est actuellement attribué à aucun chauffeur.</p>
        <?php endif; ?>
<br>
        <h4>Dossiers associés:</h4>
        <?php if ($dossiers_associes->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Dossier</th>
                        <th>Date du Dossier</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($dossier = $dossiers_associes->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $dossier['id_dossier']; ?></td>
                            <td><?php echo $dossier['date']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Ce véhicule n'est associé à aucun dossier.</p>
        <?php endif; ?>
<br>
        <h4>Historique des Entrées/Sorties de Garage:</h4>
        <?php if ($historique_garage->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Garage</th>
                        <th>Date Entrée</th>
                        <th>Date Sortie</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($entry = $historique_garage->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($entry['nom_garage'] ?? ''); ?></td>
                            <td><?php echo $entry['date_entree']; ?></td>
                            <td><?php echo $entry['date_sortie'] ?? 'Toujours au garage'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun historique d'entrée en garage pour ce véhicule.</p>
        <?php endif; ?>

    </div>
<?php endif; ?>

<?php
$conn->close();
include 'includes/footer.php';
?>