<h2><?php echo $user->Username ?></h2>

	<div class="col-sm-4">
		<img src="/uploads/<?php echo $user->ProfileImage ?>" class="smallProfileImage imageShadow" />
		<ul class="nav nav-pills nav-stacked profileNav">
			<li id="navCollection"><a href="/user/<?php echo $user->UserID ?>/collection">Collection</a></li>
			<li id="navPlatforms"><a href="/user/<?php echo $user->UserID ?>/platforms">Platforms</a></li>
			<li id="navConcepts"><a href="/user/<?php echo $user->UserID ?>/concepts">Concepts</a></li>
			<?php 
				// if logged in and this user
				if($sessionUserID != null && $sessionUserID == $user->UserID) 
				{
					echo "<li id='navSettings'><a href='/user/settings'>Settings</a></li>";
				} 
			?>
		</ul>
        <!--<div class="row collectionStats">
			<div class="col-xs-3">
				<span id="collectionCount"></span>
				<p>Collection</p>
			</div>
			<div class="col-xs-3">
				<span id="completeCount"></span>
				<p>Completed</p>
			</div>
			<div class="col-xs-3">
				<span id="backlogCount"></span>
				<p>Backlog</p>
			</div>
			<div class="col-xs-3">
				<span id="wantCount"></span>
				<p>Want</p>
			</div>
		</div>

		<div class="progress">
			<div id="completionPercentage" class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100">
				<span id="completionPercentageLabel"></span>% Complete
			</div>
		</div>-->

        		<?php
			if(count($platforms) == 0)
			{
				echo "<div class='alert alert-warning'>No games found handsome.</div>";
			} else {
                $platform = $platforms[0];
					echo '<div class="row">
						<div class="col-xs-12">
							<p><b>' . $platform->Name . '</b></p>
						</div>
					</div>

					<div class="row collectionStats">
						<div class="col-xs-4">
								<img src="/images/platforms/' . $platform->Image . '" class="imageShadow platformLogo" />
						</div>
						<div class="col-sm-8">
							<div class="row collectionStats">
								<div class="col-xs-3">
									<span>' . $platform->Collection . '</span>
									<p>Collection</p>
								</div>
								<div class="col-xs-3">
									<span>' . $platform->Completed . '</span>
									<p>Completed</p>
								</div>
								<div class="col-xs-3">
									<span>' . $platform->Backlog . '</span>
									<p>Backlog</p>
								</div>
								<div class="col-xs-3">
									<span>' . $platform->Want . '</span>
									<p>Want</p>
								</div>
							</div>
						</div>
					</div>

					<div class="progress">
						<div style="width: 0%" class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-percentage="' . $platform->Percentage . '">
							' . $platform->Percentage . '% Complete
						</div>
					</div>';
			}
            if(count($concepts) == 0)
			{
				echo "<div class='alert alert-warning'>No games found handsome.</div>";
			} else {
                $concept = $concepts[0];
					echo '<div class="row">
						<div class="col-xs-12">
							<p><b>' . $concept->Name . '</b></p>
						</div>
					</div>

					<div class="row collectionStats">
						<div class="col-xs-4">
								<img src="/images/platforms/' . $concept->Image . '" class="imageShadow platformLogo" />
						</div>
						<div class="col-sm-8">
							<div class="row collectionStats">
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

					<div class="progress">
						<div style="width: 0%" class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-percentage="' . $concept->Percentage . '">
							' . $concept->Percentage . '% Complete
						</div>
					</div>';
			}
		?>
	</div>
</div>
    </div>
