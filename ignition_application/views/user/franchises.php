		<?php
			if(count($franchises) == 0)
			{
				echo "<div class='alert alert-warning'>No games found handsome.</div>";
			} else {
				foreach($franchises as $franchise)
				{
					echo '<div class="row">
						<div class="col-xs-4">
							<p><b>' . $franchise->Name . '</b></p>
						</div>


					<div id="franchiseCollection" class="collectionStats">
						<div class="col-sm-8">
							<div class="row">
								<div class="col-xs-3">
									<span>' . $franchise->Collection . '</span>
									<p>Collection</p>
								</div>
								<div class="col-xs-3">
									<span>' . $franchise->Completed . '</span>
									<p>Completed</p>
								</div>
								<div class="col-xs-3">
									<span>' . $franchise->Backlog . '</span>
									<p>Backlog</p>
								</div>
								<div class="col-xs-3">
									<span>' . $franchise->Want . '</span>
									<p>Want</p>
								</div>
							</div>
						</div>
					</div>
					</div>

					<div class="progress">
						<div style="width: 0%" class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-percentage="' . $franchise->Percentage . '">
							' . $franchise->Percentage . '% Complete
						</div>
					</div>';
				}
			}
		?>
	</div>
</div>