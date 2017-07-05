<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $genre->name ?></span></li>
</ul>

<h2><?php echo $genre->name ?></h2>

<div class="row">
    <div class="col-sm-4">
        <img class="imageShadow gamePageBoxArt" src="<?php echo $genre->image ?>">
        <div class="panel panel-default"> 
            <div class="panel-body">
              <p><?php echo $genre->deck; ?></p>
                <?php if ($genre->GBLink != null) {
                    echo '<p><a href="' . $genre->GBLink . '" target="_blank">Read more on GiantBomb.com.</a></p>';
} ?>
            </div>
        </div>
    </div>
</div>