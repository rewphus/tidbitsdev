		<?php
			if(count($publishers) == 0)
			{
				echo "<div class='alert alert-warning'>No games found handsome.</div>";
			} else {
				foreach($publishers as $publisher)
				{
					echo '<div class="row">
						<div class="col-xs-4">
							<p><b>' . $publisher->Name . '</b></p>
						</div>


					<div id="publisherCollection" class="collectionStats">
						<div class="col-sm-8">
							<div class="row">
								<div class="col-xs-3">
									<span>' . $publisher->Collection . '</span>
									<p>Collection</p>
								</div>
								<div class="col-xs-3">
									<span>' . $publisher->Completed . '</span>
									<p>Completed</p>
								</div>
								<div class="col-xs-3">
									<span>' . $publisher->Backlog . '</span>
									<p>Backlog</p>
								</div>
								<div class="col-xs-3">
									<span>' . $publisher->Want . '</span>
									<p>Want</p>
								</div>
							</div>
						</div>
					</div>
					</div>

					<div class="progress">
						<div style="width: 0%" class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-percentage="' . $publisher->Percentage . '">
							' . $publisher->Percentage . '% Complete
						</div>
					</div>';
				}
			}
		?>
	</div>
</div>