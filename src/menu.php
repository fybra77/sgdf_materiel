        <nav class="navbar navbar-expand-lg navbar-dark py-lg-4" id="mainNav">
            <div class="container">
                <a class="navbar-brand text-uppercase fw-bold d-lg-none" href="index.php">
					<img height="50" class="mb-3 mb-lg-0 rounded" src="assets/img/logo_complet_mobile.png" alt="..." />
				</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
				</button>
                <div align="right" class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mx-auto">
					<?php 
					if (is_user())
						echo '<li class="nav-item px-lg-4"><a class="nav-link text-uppercase" href="index.php">Consulter-Gérer</a></li>';
					elseif( (isset($_SESSION['comefromexternal'])) && ($_SESSION['comefromexternal']==true))
					{
						echo '<li class="nav-item px-lg-4"><a class="nav-link text-uppercase" href="index.php">Consulter</a></li>';
						echo '<li class="nav-item px-lg-4"><a class="nav-link text-uppercase" href="add_message.php">Envoie Messages</a></li>';
					}
					if (is_user()) echo '<li class="nav-item px-lg-4"><a class="nav-link text-uppercase" href="reservations.php">Réservation</a></li>';
					if (is_user()) echo '<li class="nav-item px-lg-4"><a class="nav-link text-uppercase" href="messages.php">Messages</a></li>';
					if (is_admin())	echo '<li class="nav-item px-lg-4"><a class="nav-link text-uppercase" href="gestion.php">Ajouter-Supprimer</a></li>';
					if (is_user())	echo '<li class="nav-item px-lg-4"><a class="nav-link text-uppercase" href="parameters.php">Paramètres</a></li>';
					echo '<li class="nav-item px-lg-4"><a class="nav-link text-uppercase" href="login.php">Login</a></li>';
					?>
                    </ul>
                </div>
            </div>
        </nav>