<?php namespace ProcessWire;

$homepage = $pages->get(1);

include("./includes/head.php");

include("./includes/hlavicka.php"); 

include("./includes/nadpis.php");

?>

<div class="container_pozadi">

<div class="uk-container">

	<div class="uk-child-width uk-text-center">
    <div>
        <div class="blok_informaci uk-card uk-card-body">
			<?php echo $page->informace; ?>
		</div>
    </div>
    <div class="uk-card uk-card-body">
	<style>.embed-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; } .embed-container iframe, .embed-container object, .embed-container embed { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }</style><div class='embed-container'><iframe src='<?php echo $page->mapa;?>' width='600' height='450' frameborder='0' style='border:0'></iframe></div>
    </div>
	</div>

</div>
</div>

	<?php include("./includes/paticka.php"); ?>

	</body>
</html>
