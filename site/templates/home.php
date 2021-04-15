<?php namespace ProcessWire;

$homepage = $pages->get(1);

include("./includes/head.php");

include("./includes/hlavicka.php"); 

include("./includes/slider.php");

?>

<div class="container_pozadi ">

<div class="uk-container">

    <div class="uk-card uk-card-body onas">
        <?php echo $page->textovepole ?>
	</div>

    <?php foreach($pages->get(1026)->children as $ch):?> 

        <div class="vyhody uk-grid-match uk-grid-collapse uk-child-width-expand@m uk-text-center" uk-grid>
            <div>
            
        <div class="uk-card uk-card-body"><h2><strong><?php echo $ch->title ?></strong></h2><?php echo $ch->textovepole ?></div>
    </div>
    <div>
        <div class="uk-card uk-card-body"><img src ='<?php echo $ch->obrazek->first()->url ?>' alt='<?php echo $ch->title ?>'></div>
    </div>
    </div>

    <?php endforeach; ?>

    </div>
    </div>

<?php include("./includes/paticka.php"); ?>

	</body>
</html>