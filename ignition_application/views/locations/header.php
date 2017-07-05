<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $location->name ?></span></li>
</ul>

<h2><?php echo $location->name ?></h2>

<div class="row">
    <div class="col-sm-4">
        <img class="imageShadow gamePageBoxArt" src="<?php echo $location->image ?>">
        <div class="panel panel-default"> 
            <div class="panel-body">
              <p><?php echo $location->deck; ?></p>
                <?php if ($location->GBLink != null) {
                    echo '<p><a href="' . $location->GBLink . '" target="_blank">Read more on GiantBomb.com.</a></p>';
} ?>
            </div>
        </div>
    </div>
</div>