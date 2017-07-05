<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $theme->name ?></span></li>
</ul>

<h2><?php echo $theme->name ?></h2>

<div class="row">
    <div class="col-sm-4">
        <img class="imageShadow gamePageBoxArt" src="<?php echo $theme->image ?>">
        <div class="panel panel-default"> 
            <div class="panel-body">
              <p><?php echo $theme->deck; ?></p>
                <?php if ($theme->GBLink != null) {
                    echo '<p><a href="' . $theme->GBLink . '" target="_blank">Read more on GiantBomb.com.</a></p>';
} ?>
            </div>
        </div>
    </div>
</div>