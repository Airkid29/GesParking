<?php
include 'db_connect.php'; // Connexion à la base de données
include 'includes/header.php'; // Entête de la page HTML

// --- Récupération des statistiques générales ---
$total_vehicules = $conn->query("SELECT COUNT(id_vehicule) AS total FROM vehicule")->fetch_assoc()['total'];
$total_chauffeurs = $conn->query("SELECT COUNT(id_chauffeur) AS total FROM chauffeur")->fetch_assoc()['total'];
$total_garages = $conn->query("SELECT COUNT(id_garage) AS total FROM garage")->fetch_assoc()['total'];
$total_pannes = $conn->query("SELECT COUNT(id_panne) AS total FROM panne")->fetch_assoc()['total'];
$total_dossiers = $conn->query("SELECT COUNT(id_dossier) AS total FROM dossier")->fetch_assoc()['total'];

// --- Récupération des véhicules actuellement au garage ---
$vehicules_au_garage_query = "SELECT
                                v.marque,
                                v.model,
                                g.travaux_entretien AS nom_garage,
                                vge.date_entree
                              FROM
                                vehicule_garage_entree vge
                              JOIN
                                vehicule v ON vge.id_vehicule = v.id_vehicule
                              JOIN
                                garage g ON vge.id_garage = g.id_garage
                              WHERE
                                vge.date_sortie IS NULL
                              ORDER BY
                                vge.date_entree DESC";
$vehicules_au_garage = $conn->query($vehicules_au_garage_query);


// --- Récupération des pannes non attribuées ou en cours (si vous avez une colonne pour le statut, sinon on considère celles sans garage ou les plus récentes) ---
// Pour l'exemple, on considère les pannes qui n'ont pas encore été attribuées à un garage (id_garage IS NULL)
$pannes_non_attribuees_query = "SELECT
                                    id_panne,
                                    libelle
                                FROM
                                    panne
                                WHERE
                                    id_garage IS NULL
                                ORDER BY
                                    id_panne DESC";
$pannes_non_attribuees = $conn->query($pannes_non_attribuees_query);

// --- Récupération des 5 derniers dossiers ajoutés ---
$derniers_dossiers_query = "SELECT
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
                                d.date DESC
                            LIMIT 5";
$derniers_dossiers = $conn->query($derniers_dossiers_query);
?>

<div class="dashboard-container">
    <h2>Tableau de Bord du Parc Automobile</h2>

    <div class="stats-cards">
        <div class="card">
            <h3>Véhicules</h3>
            <p class="stat-number"><?php echo $total_vehicules; ?></p>
            <a href="vehicule.php">Voir les véhicules</a>
        </div>
        <div class="card">
            <h3>Chauffeurs</h3>
            <p class="stat-number"><?php echo $total_chauffeurs; ?></p>
            <a href="chauffeur.php">Voir les chauffeurs</a>
        </div>
        <div class="card">
            <h3>Garages</h3>
            <p class="stat-number"><?php echo $total_garages; ?></p>
            <a href="garage.php">Voir les garages</a>
        </div>
        <div class="card">
            <h3>Pannes</h3>
            <p class="stat-number"><?php echo $total_pannes; ?></p>
            <a href="panne.php">Voir les pannes</a>
        </div>
        <div class="card">
            <h3>Dossiers</h3>
            <p class="stat-number"><?php echo $total_dossiers; ?></p>
            <a href="dossier.php">Voir les dossiers</a>
        </div>
    </div>

    <div class="dashboard-section">
        <h3>Véhicules Actuellement au Garage (<?php echo $vehicules_au_garage->num_rows; ?>)</h3>
        <?php if ($vehicules_au_garage->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Véhicule</th>
                        <th>Garage</th>
                        <th>Date d'entrée</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $vehicules_au_garage->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['marque'] . ' ' . $row['model']); ?></td>
                            <td><?php echo htmlspecialchars($row['nom_garage']); ?></td>
                            <td><?php echo $row['date_entree']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <p><a href="vehicule_garage.php" class="button" style="background-color: #3498db;">Gérer les entrées/sorties</a></p>
        <?php else: ?>
            <p>Aucun véhicule actuellement au garage.</p>
        <?php endif; ?>
    </div>

    <div class="dashboard-section">
        <h3>Pannes Non Attribuées (<?php echo $pannes_non_attribuees->num_rows; ?>)</h3>
        <?php if ($pannes_non_attribuees->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Panne</th>
                        <th>Libellé</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $pannes_non_attribuees->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id_panne']; ?></td>
                            <td><?php echo htmlspecialchars($row['libelle']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <p><a href="panne.php" class="button" style="background-color: #3498db;">Gérer les pannes</a></p>
        <?php else: ?>
            <p>Aucune panne non attribuée pour le moment.</p>
        <?php endif; ?>
    </div>

    <div class="dashboard-section">
        <h3>5 Derniers Dossiers Ajoutés</h3>
        <?php if ($derniers_dossiers->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Dossier</th>
                        <th>Date</th>
                        <th>Véhicules Associés</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $derniers_dossiers->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id_dossier']; ?></td>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo htmlspecialchars($row['vehicules_associes'] ?? 'Aucun'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <p><a href="dossier.php" class="button" style="background-color: #3498db;">Voir tous les dossiers</a></p>
        <?php else: ?>
            <p>Aucun dossier trouvé.</p>
        <?php endif; ?>
    </div>

</div>

<?php
$conn->close(); // Ferme la connexion à la base de données
include 'includes/footer.php'; // Pied de page de la page HTML
?>