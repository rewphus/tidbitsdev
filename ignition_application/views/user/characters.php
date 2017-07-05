		<?php
			if(count($characters) == 0)
			{
				echo "<div class='alert alert-warning'>No games found handsome.</div>";
			} else {
				foreach($characters as $character)
				{
					echo '<div class="row">
						<div class="col-xs-4">
							<p><b>' . $character->Name . '</b></p>
						</div>


					<div id="characterCollection" class="collectionStats">
						<div class="col-sm-8">
							<div class="row">
								<div class="col-xs-3">
									<span>' . $character->Collection . '</span>
									<p>Collection</p>
								</div>
								<div class="col-xs-3">
									<span>' . $character->Completed . '</span>
									<p>Completed</p>
								</div>
								<div class="col-xs-3">
									<span>' . $character->Backlog . '</span>
									<p>Backlog</p>
								</div>
								<div class="col-xs-3">
									<span>' . $character->Want . '</span>
									<p>Want</p>
								</div>
							</div>
						</div>
					</div>
					</div>

					<div class="progress">
						<div style="width: 0%" class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-percentage="' . $character->Percentage . '">
							' . $character->Percentage . '% Complete
						</div>
					</div>';
				}
			}
		?>
	</div>
</div>