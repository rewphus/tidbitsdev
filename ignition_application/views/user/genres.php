		<?php
			if(count($genres) == 0)
			{
				echo "<div class='alert alert-warning'>No games found handsome.</div>";
			} else {
				foreach($genres as $genre)
				{
					echo '<div class="row">
						<div class="col-xs-4">
							<p><b>' . $genre->Name . '</b></p>
						</div>


					<div id="genreCollection" class="collectionStats">
						<div class="col-sm-8">
							<div class="row">
								<div class="col-xs-3">
									<span>' . $genre->Collection . '</span>
									<p>Collection</p>
								</div>
								<div class="col-xs-3">
									<span>' . $genre->Completed . '</span>
									<p>Completed</p>
								</div>
								<div class="col-xs-3">
									<span>' . $genre->Backlog . '</span>
									<p>Backlog</p>
								</div>
								<div class="col-xs-3">
									<span>' . $genre->Want . '</span>
									<p>Want</p>
								</div>
							</div>
						</div>
					</div>
					</div>

					<div class="progress">
						<div style="width: 0%" class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-percentage="' . $genre->Percentage . '">
							' . $genre->Percentage . '% Complete
						</div>
					</div>';
				}
			}
		?>
	</div>
</div>