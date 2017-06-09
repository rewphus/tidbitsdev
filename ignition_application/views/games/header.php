<!-- Modal -->
<div class="modal fade" id="removeGameModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">This is serious business duder.</h4>
      </div>
      <div class="modal-body">
        Are you sure you want to remove this game from your collection? If you have sold it, consider changing its status to "Played".
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <span id="removeGameButtonPlaceholder"></span>
      </div>
    </div>
  </div>
</div>

<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $game->name ?></span></li>
</ul>

<h2><?php echo $game->name ?></h2>

<div class="row">
    <div class="col-sm-4">
        <img class="imageShadow gamePageBoxArt" src="<?php echo $game->image ?>">
        <div class="panel panel-default"> 
            <div class="panel-body">
              <p><?php echo $game->deck; ?></p>
                <?php if ($game->GBLink != null) {
                    echo '<p><a href="' . $game->GBLink . '" target="_blank">Read more on GiantBomb.com.</a></p>';
} ?>
                <?php if ($sessionUserID > 0) { ?>
                  <div class="pull-right">
                   <!--<?php echo var_export($game) ?> -->
                        <div id="inCollectionControlsBtn<?php echo $game->GBID ?>" class="btn-group <?php if ($game->listID != 0) {
                            echo 'hidden';
} ?>">
                                                        <button type="button" class="btn btn-default" onclick="javascript:addGame(<?php echo $game->GBID ?>, 1, true);"><span class="icon-large icon-gamepad"  style="height: 18px" aria-hidden="true"></span><br>Played</button>
                                                        <button type="button" class="btn btn-default" onclick="javascript:addGame(<?php echo $game->GBID ?>, 2, true);"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span><br>Watched</button>
                                                        <button type="button" class="btn btn-default" onclick="javascript:addGame(<?php echo $game->GBID ?>, 3, true);"><span class="glyphicon glyphicon-cloud" aria-hidden="true"></span><br>Familiar</button>
                                                    </div>
  
                                                    <div class='btn-group <?php if ($game->listID == 0) {
                                                        echo 'hidden';
} ?>'>
                                                        <button id='gameButton<?php echo $game->GBID ?>' data-toggle='dropdown' class='btn btn-<?php echo $game->listStyle ?> dropdown-toggle'><?php echo $game->listLabel ?> <span class='caret'></span></button>
                                                        <ul class="dropdown-menu">
                                                            <li><a onclick="javascript:addGame(<?php echo $game->GBID ?>, 1, true);">Played</a></li>
                                                            <li><a onclick="javascript:addGame(<?php echo $game->GBID ?>, 2, true);">Watched</a></li>
                                                            <li><a onclick="javascript:addGame(<?php echo $game->GBID ?>, 3, true);">Familiar</a></li>
                                                        </ul>
                                                    </div> 
                        <span id="inCollectionControls<?php echo $game->GBID ?>" class="<?php if ($game->listID == 0) {
                            echo "hidden";
} ?>">
                            <div id='statusButtonGroup<?php echo $game->GBID ?>' class='btn-group'>
                                <button id='statusButton<?php echo $game->GBID ?>' data-toggle='dropdown' class='btn btn-<?php echo $game->statusStyle ?> dropdown-toggle'><?php echo $game->statusLabel  ?> <span class='caret'></span></button>
                                                            <ul id="statusDropdown1" class='dropdown-menu <?php if ($game->listID != 1) {
                                                                echo "hidden";
} ?>'>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->GBID ?>, 1);">Still Learning</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->GBID ?>, 2);">Basic Understanding</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->GBID ?>, 3);">Improving</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->GBID ?>, 4);">Perfecting</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->GBID ?>, 5);">Mastered</a></li>
                                                            </ul>
                                                            <ul id="statusDropdown2" class='dropdown-menu <?php if ($game->listID != 2) {
                                                                echo "hidden";
} ?>'>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->GBID ?>, 6);">Trailer</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->GBID ?>, 7);">Promotional Gameplay</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->GBID ?>, 8);">Raw Gameplay</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->GBID ?>, 9);">Full Playthrough</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->GBID ?>, 10);">Speedrun</a></li>
                                                            </ul>
                                                            <ul id="statusDropdown3" class='dropdown-menu <?php if ($game->listID != 3) {
                                                                echo "hidden";
} ?>'>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->GBID ?>, 11);">Still Learning</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->GBID ?>, 12);">Basic Understanding</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->GBID ?>, 13);">Improving</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->GBID ?>, 14);">Perfecting</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->GBID ?>, 15);">Mastered</a></li>
                                                            </ul>
                            </div>
                                                        <div id="futureButtonGroup<?php echo $game->GBID ?>" class="btn-group">
                                                            <button id='futureButton<?php echo $game->GBID ?>' data-toggle='dropdown' class='btn btn-<?php echo $game->futureStyle ?> dropdown-toggle'><?php echo $game->futureLabel  ?> <span class='caret'></span></button>
                                                            <ul id="futureDropdown1" class='dropdown-menu <?php if($game->listID != 1) echo "hidden" ?>'>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->GBID ?>, 1);setValue(<?php echo $game->GBID ?>, <?php echo $game->statusID ?>, 1);">Unlikely</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->GBID ?>, 2);">Maybe Someday</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->GBID ?>, 3);">Will make time</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->GBID ?>, 4);">Currently playing</a></li>
                                                            </ul>
                                                            <ul id="futureDropdown2" class='dropdown-menu <?php if($game->listID != 2) echo "hidden" ?>'>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->GBID ?>, 6);">Trailer</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->GBID ?>, 7);">Promotional Gameplay</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->GBID ?>, 8);">Raw Gameplay</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->GBID ?>, 9);">Full Playthrough</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->GBID ?>, 10);">Speedrun</a></li>
                                                            </ul>
                                                            <ul id="futureDropdown3" class='dropdown-menu <?php if($game->listID != 3) echo "hidden" ?>'>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->GBID ?>, 11);">Still Learning</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->GBID ?>, 12);">Basic Understanding</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->GBID ?>, 13);">Improving</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->GBID ?>, 14);">Perfecting</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->GBID ?>, 15);">Mastered</a></li>
                                                            </ul>
                                                        </div>
                                                        <div id="valueButtonGroup<?php echo $game->GBID ?>" class="btn-group">
                                                            <button id='valueButton<?php echo $game->GBID ?>' data-toggle='dropdown' class='btn btn-<?php echo $game->valueStyle ?> dropdown-toggle'><?php echo $game->valueLabel  ?> <span class='caret'></span></button>
                                                            <ul id="valueDropdown1" class='dropdown-menu <?php if($game->listID != 1) echo "hidden" ?>'>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->GBID ?>, 1);">Not for me</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->GBID ?>, 6);">Had my fill</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->GBID ?>, 2);">Will keep in mind</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->GBID ?>, 4);">Verdict is still out</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->GBID ?>, 3);">Shows potential</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->GBID ?>, 8);">Wish I had more time</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->GBID ?>, 5);">Instant classic</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->GBID ?>, 9);">Well worth it</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->GBID ?>, 7);">I need MORE</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->GBID ?>, 10);">Flowstate</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->GBID ?>, 11);">Dedicated</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->GBID ?>, 12);">Addicted</a></li>
                                                            </ul>
                                                        </div>  
                            
                        </span>
                    </div>   
                <?php } ?>   
            </div>  
                <?php
                if ($game->platforms != null) {
                    echo "<div id='platforms" . $game->GBID . "' class='panel-footer'>Platform<br>";
                    foreach ($game->platforms as $platform) {
                        echo "<label><input id='platform_" . $game->GBID . "_" . $platform->GBID . "' type='checkbox'";
                        if ($platform->inCollection) {
                            echo " checked";
                        }
                        if ($game->listID == 0) {
                            echo " readonly";
                        }
                        echo "> <span class='label label-info'>" . $platform->name . "</span></label> ";
                    }
                    echo "</div>";
                }
                
            ?>                                
          
        <?php
        if ($game->developers != null) {
            echo "<div id='developers" . $game->GBID . "' class='panel-footer'>Developer<br>";
            foreach ($game->developers as $developer) {
                echo "<label><div id='developer_" . $game->GBID . "_" . $developer->GBID . "' ";
                echo "> <span class='label label-info'>" . $developer->name . "</span></div></label> ";
            }
            echo "</div>";
        }
                
            ?>                                                   
        <?php
        if ($game->publishers != null) {
            echo "<div id='publishers" . $game->GBID . "' class='panel-footer'>Publisher<br>";
            foreach ($game->publishers as $publisher) {
                echo "<label><div id='publisher_" . $game->GBID . "_" . $publisher->GBID . "' ";
                echo "> <span class='label label-info'>" . $publisher->name . "</span></div></label> ";
            }
            echo "</div>";
        }
                
            ?>                                
        <?php
        if ($game->genres != null) {
            echo "<div id='genres" . $game->GBID . "' class='panel-footer'>Genre<br>";
            foreach ($game->genres as $genre) {
                echo "<label><div id='genre_" . $game->GBID . "_" . $genre->GBID . "' ";
                echo "> <span class='label label-info'>" . $genre->name . "</span></div></label> ";
            }
            echo "</div>";
        }
                
            ?>                                         
        <?php
        if ($game->themes != null) {
            echo "<div id='themes" . $game->GBID . "' class='panel-footer'>Theme<br>";
            foreach ($game->themes as $theme) {
                echo "<label><div id='theme_" . $game->GBID . "_" . $theme->GBID . "' ";
                echo "> <span class='label label-info'>" . $theme->name . "</span></div></label> ";
            }
            echo "</div>";
        }

            ?>                                
        <?php
        if ($game->franchises != null) {
            echo "<div id='franchises" . $game->GBID . "' class='panel-footer'>Franchise<br>";
            foreach ($game->franchises as $franchise) {
                echo "<label><div id='franchise_" . $game->GBID . "_" . $franchise->GBID . "' ";
                echo "> <span class='label label-info'>" . $franchise->name . "</span></div></label> ";
            }
            echo "</div>";
        }
            ?>                                
                <?php
                if ($game->concepts != null) {
                    echo "<div id='concepts" . $game->GBID . "' class='panel-footer'>Concept<br>";
                    foreach ($game->concepts as $concept) {
                        echo "<label><input id='concept_" . $game->GBID . "_" . $concept->GBID . "' type='checkbox'";
                        if ($concept->inCollection) {
                            echo " checked";
                        }
                        if ($game->listID == 0) {
                            echo " readonly";
                        }
                        echo "> <span class='label label-info'>" . $concept->name . "</span></label> ";
                    }
                    echo "</div>";
                }
                
            ?>                                 
                <?php
                if ($game->locations != null) {
                    echo "<div id='locations" . $game->GBID . "' class='panel-footer'>Location<br>";
                    foreach ($game->locations as $location) {
                        echo "<label><div id='location_" . $game->GBID . "_" . $location->GBID . "' ";
                        echo "> <span class='label label-info'>" . $location->name . "</span></div></label> ";
                    }
                     echo "</div>";
                }
            ?>                                 
                <?php
                if ($game->characters != null) {
                    echo "<div id='characters" . $game->GBID . "' class='panel-footer'>Character<br>";
                    foreach ($game->characters as $character) {
                        echo "<label><div id='character_" . $game->GBID . "_" . $character->GBID . "' ";
                        echo "> <span class='label label-info'>" . $character->name . "</span></div></label> ";
                    }
                     echo "</div>";
                }
            ?>                                
        </div>                     


        <?php if ($sessionUserID > 0 && $game->listID > 0) { ?>
            <div class="panel panel-default"> 
                <div class="panel-body">
                    <div class="form-group">
                        <label for="currentlyPlayingInput">Currently Playing?</label>
                        <select class="form-control" id="currentlyPlayingInput">
                            <option value="false" <?php if (!$game->currentlyPlaying) {
                                echo "selected";
} ?>>No</option>
                            <option value="true" <?php if ($game->currentlyPlaying) {
                                echo "selected";
} ?>>Yes</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="hoursPlayedInput">Hours Played</label>
                        <input type="text" class="form-control" id="hoursPlayedInput" value="<?php echo $game->hoursPlayed; ?>">
                    </div>
                    <div class="form-group">
                        <label for="dateCompletedInput">Date Completed</label>
                        <input type="date" class="form-control" id="dateCompletedInput" placeholder="dd/mm/yyyy" value="<?php echo $game->dateComplete; ?>">
                    </div>
                    <a onclick="javascript:saveProgression(<?php echo $game->GBID ?>);" class='btn btn-success progressionSaveButton' id='progressionSaveButton'>Save</a>                
                </div>
            </div>
            <a onclick='javascript:showRemoveGameWarning(<?php echo $game->GBID; ?>);' class='btn btn-danger btn-fullWidth'>Remove from Collection</a>
        <?php } ?> 
    </div>
    <div class="col-sm-8">
        <?php
        if (isset($users) && $users != null) {
            echo '<h4>Who\'s played this?</h4>

                <div class="itemGrid clearfix">';

            foreach ($users as $user) {
                echo '<div class="itemGridImage pull-left">
                        <a href="/user/'. $user->UserID . '">
                            <img class="itemGridImage imageShadow" src="/uploads/' . $user->ProfileImage . '" alt="' . $user->UserName . '" title="' . $user->UserName . '">
                            <span class="label label-' . $user->StatusStyle . ' itemGridLabel">' . $user->StatusNameShort . '</span>
                        </a>
                    </div>';
            }

            echo '</div>';
        }
        ?>

        <h4>What's Happening?</h4>