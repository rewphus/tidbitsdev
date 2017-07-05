		<?php
			if(count($locations) == 0)
			{
				echo "<div class='alert alert-warning'>No games found handsome.</div>";
			} else {
				foreach($locations as $location)
				{
					echo '<div class="row">
						<div class="col-xs-4">
							<p><b>' . $location->Name . '</b></p>
						</div>


					<div id="locationCollection" class="collectionStats">
						<div class="col-sm-8">
							<div class="row">
								<div class="col-xs-3">
									<span>' . $location->Collection . '</span>
									<p>Collection</p>
								</div>
								<div class="col-xs-3">
									<span>' . $location->Completed . '</span>
									<p>Completed</p>
								</div>
								<div class="col-xs-3">
									<span>' . $location->Backlog . '</span>
									<p>Backlog</p>
								</div>
								<div class="col-xs-3">
									<span>' . $location->Want . '</span>
									<p>Want</p>
								</div>
							</div>
						</div>
					</div>
					</div>

					<div class="progress">
						<div style="width: 0%" class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-percentage="' . $location->Percentage . '">
							' . $location->Percentage . '% Complete
						</div>
					</div>';
				}
			}
		?>
	</div>
</div>