<?php namespace ProcessWire;

$homepage = $pages->get(1);

include("./includes/head.php");

include("./includes/hlavicka.php"); 

include("./includes/nadpis.php");
    
?>

<div class="container_pozadi">

<div class="uk-container">
           
    <div class="uk-card uk-card-body tabulka_a_pravidla">
        <?php echo $page->textovepole ?>
	</div>


</div>

</div>

<?php include("./includes/paticka.php"); ?>

<script>
    var x = document.getElementsByTagName("TABLE");
    var i;
    for (i = 0; i < x.length; i++) {
        x[i].classList.add("uk-table");
        x[i].classList.add("uk-table-responsive");
        // x[i].classList.add("uk-table-striped");
    }
</script>

	</body>
</html>