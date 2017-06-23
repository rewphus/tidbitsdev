<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $pagetitle ?></span></li>
</ul>

<!--<div class="row">-->
    <div>
    <div class="col-sm-8">
        <h2>Search</h2>
        <form role="form-inline" method="post" action="/search/">
            <div class="input-group">
                <input type="search" class="form-control" name="query" placeholder="Search" value="<?php echo $searchQuery ?>">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-default">Submit</button>
                </span>
            </div>
        </form>  
        <div class="searchResults">
            <?php
                if($searchQuery != '') 
                {
                    if($searchResults != null){    
                        // search results                                                                                                   
                        foreach($searchResults->results as $game)
                        {     
                            ?>

                            <div class="clearfix">    
                                <a href='/game/<?php echo $game->id ?>'>
                                    <img class="media-object pull-left imageShadow searchResultImage" src="<?php if(is_object($game->image)) echo $game->image->small_url; ?>">
                                </a>    
                                <div class="pull-left searchResultBody">  
                                    <h4><?php echo "<a href='/game/" . $game->id . "'>" . $game->name . "</a>" ?></h4>
                                    <div class="panel panel-default"> 
                                        <div class="panel-body">
                                            <p><?php echo $game->deck; ?></p>   
                                            <p><a href="<?php echo $game->site_detail_url; ?>" target="_blank">Read more on GiantBomb.com.</a></p>
                                            <?php if($sessionUserID > 0) { ?>
                                                <div class="pull-left">
                                                <!--<?php echo var_export($game) ?>-->
                                                    <div id="inCollectionControlsBtn<?php echo $game->id ?>" class="btn-group <?php if($game->listID != 0) echo 'hidden' ?>">
                                                        <button type="button" class="btn btn-default" onclick="javascript:addGame(<?php echo $game->id ?>, 1, true);"><i class="fa fa-gamepad" aria-hidden="true"></i><br>Played</button>
                                                        <button type="button" class="btn btn-default" onclick="javascript:addGame(<?php echo $game->id ?>, 2, true);"><i class="fa fa-eye" aria-hidden="true"></i><br>Watched</button>
                                                        <button type="button" class="btn btn-default" onclick="javascript:addGame(<?php echo $game->id ?>, 3, true);"><span class="glyphicon glyphicon-cloud" aria-hidden="true"></span><br>Familiar</button>
                                                    </div>
  
                                                    <div class='btn-group <?php if($game->listID == 0) echo 'hidden' ?>'>
                                                        <button id='gameButton<?php echo $game->id ?>' data-toggle='dropdown' class='btn btn-<?php echo $game->listStyle ?> dropdown-toggle'><?php echo $game->listLabel ?> <span class='caret'></span></button>
                                                        <ul class="dropdown-menu">
                                                            <li><a onclick="javascript:addGame(<?php echo $game->id ?>, 1, true);"><i class="fa fa-gamepad" aria-hidden="true"></i> Played</a></li>
                                                            <li><a onclick="javascript:addGame(<?php echo $game->id ?>, 2, true);"><i class="fa fa-eye" aria-hidden="true"></i> Watched</a></li>
                                                            <li><a onclick="javascript:addGame(<?php echo $game->id ?>, 3, true);">Familiar</a></li>
                                                        </ul>
                                                    </div> 
                                                    <span id="inCollectionControls<?php echo $game->id ?>" class="<?php if($game->listID == 0) echo "hidden" ?>">
                                                        <div id="motivationButtonGroup<?php echo $game->id ?>" class="btn-group">
                                                            <button id='motivationButton<?php echo $game->id ?>' data-toggle='dropdown' class='btn btn-<?php echo $game->motivationStyle ?> dropdown-toggle'><?php echo $game->motivationLabel  ?> <span class='caret'></span></button>
                                                            <ul id="motivationDropdown1" class='dropdown-menu <?php if($game->listID != 1) echo "hidden" ?>'>
                                                                    <li><a onclick="javascript:changeMotivation(<?php echo $game->id ?>, 1);"><i class="fa fa-bomb" aria-hidden="true"></i> Action</a></li>
                                                                    <li><a onclick="javascript:changeMotivation(<?php echo $game->id ?>, 2);"><i class="fa fa-comments-o" aria-hidden="true"></i> Social</a></li>
                                                                    <li><a onclick="javascript:changeMotivation(<?php echo $game->id ?>, 3);"><i class="fa fa-puzzle-piece" aria-hidden="true"></i> Mastery</a></li>
                                                                    <li><a onclick="javascript:changeMotivation(<?php echo $game->id ?>, 4);"><i class="fa fa-trophy" aria-hidden="true"></i> Accomplishment</a></li>
                                                                    <li><a onclick="javascript:changeMotivation(<?php echo $game->id ?>, 5);"><i class="fa fa-globe" aria-hidden="true"></i> Immersion</a></li>
                                                                    <li><a onclick="javascript:changeMotivation(<?php echo $game->id ?>, 6);"><i class="fa fa-wrench" aria-hidden="true"></i> Creativity</a></li>
                                                                    <li><a onclick="javascript:changeMotivation(<?php echo $game->id ?>, 7);"><i class="fa fa-graduation-cap" aria-hidden="true"></i> Academic</a></li>
                                                            </ul>
                                                        </div> 
                                                        <div id="statusButtonGroup<?php echo $game->id ?>" class="btn-group <?php if($game->motivationID == 99) echo "hidden" ?>">
                                                            <button id='statusButton<?php echo $game->id ?>' data-toggle='dropdown' class='btn btn-<?php echo $game->statusStyle ?> dropdown-toggle'><?php echo $game->statusLabel  ?> <span class='caret'></span></button>
                                                            <ul id="statusActionDropdown" class='dropdown-menu <?php if( $game->motivationID != 1) echo "hidden" ?>'>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 1);">Still Learning</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 2);">Basic Understanding</a></li>
                                                                    <div class="dropdown-divider"></div>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 3);">Getting Confident</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 4);">Action Star</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 5);">Action Hero</a></li>
                                                            </ul>
                                                            <ul id="statusSocialDropdown" class='dropdown-menu <?php if( $game->motivationID != 2) echo "hidden" ?>'>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 1);">Still Learning</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 2);">Basic Understanding</a></li>
                                                                    <div class="dropdown-divider"></div>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 6);">Regular</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 7);">Veteran</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 8);">Leader</a></li>
                                                            </ul>
                                                            <ul id="statusMasteryDropdown" class='dropdown-menu <?php if( $game->motivationID != 3) echo "hidden" ?>'>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 1);">Still Learning</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 2);">Basic Understanding</a></li>
                                                                    <div class="dropdown-divider"></div>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 9);">Improving</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 10);">Perfecting</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 11);">Mastered</a></li>
                                                            </ul>
                                                            <ul id="statusAchievementDropdown" class='dropdown-menu <?php if( $game->motivationID != 4) echo "hidden" ?>'>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 1);">Still Learning</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 2);">Basic Understanding</a></li>
                                                                    <div class="dropdown-divider"></div>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 12);">Noteworthy</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 13);">Storied</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 14);">Legendary</a></li>
                                                            </ul>
                                                            <ul id="statusImmersionDropdown" class='dropdown-menu <?php if( $game->motivationID != 5) echo "hidden" ?>'>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 1);">Still Learning</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 2);">Basic Understanding</a></li>
                                                                    <div class="dropdown-divider"></div>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 15);">Relatable</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 16);">Immersed</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 17);">Alternate Reality</a></li>
                                                            </ul>
                                                            <ul id="statusCreativityDropdown" class='dropdown-menu <?php if( $game->motivationID != 6) echo "hidden" ?>'>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 1);">Still Learning</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 2);">Basic Understanding</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 18);">Tinkerer</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 19);">Inventor</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 20);">Mad Scientist</a></li>
                                                            </ul>
                                                            <ul id="statusAcademicDropdown" class='dropdown-menu <?php if( $game->motivationID != 7) echo "hidden" ?>'>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 1);">Still Learning</li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 2);">Basic Understanding</a></li>
                                                                    <div class="dropdown-divider"></div>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 21);">Pop Quiz</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 22);">Final Exam</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 23);">Thesis</a></li>
                                                            </ul>
                                                            <ul id="statusWatchedDropdown" class='dropdown-menu <?php if($game->listID != 2) echo "hidden" ?>'>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 24);">Trailer</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 25);">Promotional Gameplay</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 26);">Raw Gameplay</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 27);">Full Playthrough</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 28);">Speedrun</a></li>
                                                            </ul>
                                                            <ul id="statusFamiliarDropdown" class='dropdown-menu <?php if($game->listID != 3) echo "hidden" ?>'>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 29);">Still Learning</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 30);">Basic Understanding</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 31);">Improving</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 32);">Perfecting</a></li>
                                                                    <li><a onclick="javascript:changeStatus(<?php echo $game->id ?>, 33);">Mastered</a></li>
                                                            </ul>
                                                        </div> 
                                                        <div id="futureButtonGroup<?php echo $game->id ?>" class="btn-group <?php if($game->statusID == 99) echo "hidden" ?>">
                                                            <button id='futureButton<?php echo $game->id ?>' data-toggle='dropdown' class='btn btn-<?php echo $game->futureStyle ?> dropdown-toggle'><?php echo $game->futureLabel  ?> <span class='caret'></span></button>
                                                            <ul id="futureDropdown1" class='dropdown-menu <?php if($game->listID != 1) echo "hidden" ?>'>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->id ?>, 1);">Unlikely</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->id ?>, 2);">Maybe Someday</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->id ?>, 3);">Will make time</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->id ?>, 4);">Currently playing</a></li>
                                                            </ul>
                                                            <ul id="futureDropdown2" class='dropdown-menu <?php if($game->listID != 2) echo "hidden" ?>'>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->id ?>, 6);">Trailer</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->id ?>, 7);">Promotional Gameplay</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->id ?>, 8);">Raw Gameplay</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->id ?>, 9);">Full Playthrough</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->id ?>, 10);">Speedrun</a></li>
                                                            </ul>
                                                            <ul id="futureDropdown3" class='dropdown-menu <?php if($game->listID != 3) echo "hidden" ?>'>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->id ?>, 11);">Still Learning</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->id ?>, 12);">Basic Understanding</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->id ?>, 13);">Improving</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->id ?>, 14);">Perfecting</a></li>
                                                                    <li><a onclick="javascript:changeFuture(<?php echo $game->id ?>, 15);">Mastered</a></li>
                                                            </ul>
                                                        </div>
                                                        <!--<div id="valueButtonGroup<?php echo $game->id ?>" class="btn-group <?php if($game->futureID == 99) echo "hidden" ?>">
                                                            <button id='valueButton<?php echo $game->id ?>' data-toggle='dropdown' class='btn btn-<?php echo $game->valueStyle ?> dropdown-toggle'><?php echo $game->valueLabel  ?> <span class='caret'></span></button>
                                                            <ul id="valueDropdown1" class='dropdown-menu <?php if($game->listID != 1) echo "hidden" ?>'>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->id ?>, 1);">Not for me</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->id ?>, 6);">Had my fill</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->id ?>, 2);">Will keep in mind</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->id ?>, 4);">Verdict is still out</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->id ?>, 3);">Shows potential</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->id ?>, 8);">Wish I had more time</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->id ?>, 5);">Instant classic</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->id ?>, 9);">Well worth it</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->id ?>, 7);">I need MORE</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->id ?>, 10);">Flowstate</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->id ?>, 11);">Dedicated</a></li>
                                                                    <li><a onclick="javascript:changeValue(<?php echo $game->id ?>, 12);">Addicted</a></li>
                                                            </ul>
                                                        </div>  -->
                                                    </span>
                                                </div>
                                            <?php } ?>
                                        </div>  
                                        <?php    
                                            if(property_exists($game, "platforms") && $game->platforms != null)
                                            {
                                                echo "<div id='platforms" . $game->id . "' class='panel-footer'>";
                                                foreach($game->platforms as $platform)
                                                {
                                                    echo "<label><input id='platform_" . $game->id . "_" . $platform->id . "' type='checkbox'";
                                                    if($platform->inCollection) echo " checked";
                                                    if($game->listID == 0) echo " readonly";
                                                    echo "> <span class='label label-info'>" . $platform->name . "</span></label> ";  
                                                }
                                                echo "</div>";  
                                            }
                                        ?>                                              
                                    </div>   
                                </div>
                            </div>                            
                            <hr>

                            <?php
                        } 

                        // paging
                        $numberOfPages = ceil($searchResults->number_of_total_results/10); 
                        echo '<ul class="pagination">';
                        if($searchPage-1 > 0)
                        {
                            echo "<li><a href='/search/" . $searchQuery . "/" . ($searchPage-1) . "/'>«</a></li>";    
                        }
                        $i = 0;
                        while($i < $numberOfPages)
                        {    
                            $i++; 
                            if($i == $searchPage){            
                                echo "<li class='active'>";
                            } else {                  
                                echo "<li>";
                            }     
                            echo "<a href='/search/" . $searchQuery . "/" . $i . "/'>" . $i . "</a></li>";                                                                                           
                        }
                        if($searchPage+1 <= $numberOfPages)
                        {
                            echo "<li><a href='/search/" . $searchQuery . "/" . ($searchPage+1) . "/'>»</a></li>";    
                        }
                        echo '</ul>';
                    } else {
                        echo '<div class="alert alert-danger">Sorry duder, nothing was found.' . $searchQuery . '<a class="close" data-dismiss="alert" href="#">&times;</a></div>';                    
                    }   
                }
            ?>
        </div>
    </div>
    <!--<div class="col-sm-4">
        <h2>Build your collection!</h2>
        <p>Keep track of the games you've completed and your backlog of shame by building your collection. It's real simple, just tag a game under one of our five categories.</p>
        
        <div class="media">
          <a class="pull-left" href="#">
            <span class='btn btn-success collectionStatusBadge media-object'>Own</span> 
          </a>
          <div class="media-body">
            You own the game. It's sitting right over there, on your shelf. You're pretty pleased with yourself.
          </div>
        </div>

        <div class="media">
          <a class="pull-left" href="#">
            <span class='btn btn-warning collectionStatusBadge media-object'>Want</span> 
          </a>
          <div class="media-body">
            Your wish list. You can't wait to get hold of this hot puppy. Why don't you have it yet?
          </div>
        </div>

        <div class="media">
          <a class="pull-left" href="#">
            <span class='btn btn-info collectionStatusBadge media-object'>Borrowed</span> 
          </a>
          <div class="media-body">
            You're borrowing this game from a friend. You wanted to play it, but not enough to buy it apparently.
          </div>
        </div>

        <div class="media">
          <a class="pull-left" href="#">
            <span class='btn btn-danger collectionStatusBadge media-object'>Lent</span> 
          </a>
          <div class="media-body">
            You've lent this game out to someone. You are going to get it back right?
          </div>
        </div>
        
        <div class="media">
          <a class="pull-left" href="#">
            <span class='btn btn-primary collectionStatusBadge media-object'>Played</span> 
          </a>
          <div class="media-body">
            You played this game, but you don't own it. Maybe you traded it in for the new hot jam. Or you just flushed it down a toilet, because it was that terrible.
          </div>
        </div>
    </div>-->
</div>