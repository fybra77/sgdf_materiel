<?php 
/**
 * SGDF Matos - Site de Gestion materiel pour les scouts
 * PHP Version 5.6 ou au dessus
 *
 * @see https://github.com/fybra77/sgdf_matos.git The SGDF Matos Website project
 *
 * @author    Franck BRICOUT
 * @copyright 2016 - 2022 Franck BRICOUT
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */
 ?>
 
 <?php 
function  template_debut_bloc_normal($marge=5)
{
	echo '<section class="page-section clearfix">
		<div class="about-heading-content">
			<div class="row">
				<div class="col-xl-9 col-lg-10 mx-auto">
					<div class="bg-faded rounded p-'.$marge.'">';
}
function  template_fin_bloc_normal()
{
echo '			</div>
		</div>
	</div>
</div>
</section>';
}

function  template_debut_bloc_info($marge=5)
{
	echo '<section class="page-section cta">
	<div class="container">
		<div class="row">
			<div class="col-xl-9 mx-auto">
				<div class="cta-inner bg-faded text-center rounded p-'.$marge.'">';
}
function  template_fin_bloc_info()
{
echo '			</div>
		</div>
	</div>
</div>
</section>';
}