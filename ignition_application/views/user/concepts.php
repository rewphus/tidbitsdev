		<?php
			if(count($concepts) == 0)
			{
				echo "<div class='alert alert-warning'>No games found handsome.</div>";
			} else {
				foreach($concepts as $concept)
				{
					echo '<div class="row">
						<div class="col-xs-4">
							<p><b>' . $concept->Name . '</b></p>
						</div>


					<div id="conceptCollection" class="collectionStats">
						<div class="col-sm-8">
							<div class="row">
								<div class="col-xs-3">
									<span>' . $concept->Collection . '</span>
									<p>Collection</p>
								</div>
								<div class="col-xs-3">
									<span>' . $concept->Completed . '</span>
									<p>Completed</p>
								</div>
								<div class="col-xs-3">
									<span>' . $concept->Backlog . '</span>
									<p>Backlog</p>
								</div>
								<div class="col-xs-3">
									<span>' . $concept->Want . '</span>
									<p>Want</p>
								</div>
							</div>
						</div>
					</div>
					</div>

					<div class="progress">
						<div style="width: 0%" class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-percentage="' . $concept->Percentage . '">
							' . $concept->Percentage . '% Complete
						</div>
					</div>';
				}
			}
		?>
	</div>
</div>