<?php namespace ProcessWire;

$homepage = $pages->get(1);

include("./includes/head.php");

include("./includes/hlavicka.php"); 

include("./includes/nadpis.php");
    
?>

<div class="container_pozadi">

<div class="uk-container">

	<div class="druhy_sluzeb uk-grid-match uk-grid-collapse uk-child-width-expand@m uk-text-center" uk-grid>
    <div>
        <div class="uk-card uk-card-body"><?php echo $page->sluzby ?></div>
    </div>
    <div>
        <div class="uk-card uk-card-body"><?php echo "<img src ='{$page->obrazek->first()->url}' alt='pes' ?>"; ?></div>
    </div>
    </div>

	<div class="uk-card uk-card-body popis_fungovani">
        <?php echo $page->popis ?>
	</div>

    </div>
    </div>
		
    <?php include("./includes/paticka.php"); ?>

	</body>
</html>
