<ul class="breadcrumb">
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb"><span itemprop="title"><a href="/">Home</a></span></li>       
    <li itemscope="itemscope" itemtype="http://data-vocabulary.org/Breadcrumb" class="active"><span itemprop="title"><?php echo $publisher->name ?></span></li>
</ul>

<h2><?php echo $publisher->name ?></h2>

<div class="row">
    <div class="col-sm-4">
        <img class="imageShadow gamePageBoxArt" src="<?php echo $publisher->image ?>">
        <div class="panel panel-default"> 
            <div class="panel-body">
              <p><?php echo $publisher->deck; ?></p>
                <?php if ($publisher->GBLink != null) {
                    echo '<p><a href="' . $publisher->GBLink . '" target="_blank">Read more on GiantBomb.com.</a></p>';
} ?>
            </div>
        </div>
    </div>
</div>