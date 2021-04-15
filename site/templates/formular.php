<?php namespace ProcessWire;

$homepage = $pages->get(1);

include("./includes/head.php");

include("./includes/hlavicka.php"); 
    
include("./includes/nadpis.php"); 
	
?>

<div class='container_pozadi'>

<div class="uk-container">

<div class='odsazeni_formulare'>
<?php echo $page->textovepole?>
</div>

</div>

</div>

<?php include("./includes/paticka.php"); ?>

	</body>
</html>
