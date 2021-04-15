<div class='div_hlavniho_nadpisu uk-flex uk-flex-middle uk-flex-center'>
	<?php echo "<img class='logo' src ='{$homepage->obrazek->first()->url}' alt='logo' ?>"; ?>
	<h1 class="hlavni_nadpis"><strong><?php echo $homepage->title;?></strong></h1>
</div>

<?php

$stranky = $homepage->children->prepend($homepage);

$menu = "<div>";
$menu .= "<nav class='barva_menu_pozadi uk-navbar-container' uk-navbar>";
$menu .= "<div class='uk-navbar-center'>";
$menu .= "<ul class='uk-navbar-nav uk-visible@m'>";


foreach ($stranky as $p) {
    if ($page->id == $p->id) {
        $menu .= "<li class='uk-active'><a href ='$p->url'>$p->title</a></li>";
    } else {
        $menu .= "<li><a class='barva_menu_odkazu' href ='$p->url'>$p->title</a></li>";
    }
};

$menu .= "</ul>";
$menu .= "<button class='uk-hidden@m uk-button uk-button-default' type='button' uk-toggle='target: #offcanvas-usage'>Menu</button>";
$menu .= "</div>";
$menu .= "</nav>";
$menu .= "</div>";


echo $menu;

?>

<div id="offcanvas-usage" uk-offcanvas>
	<div class="offcanvas_barva uk-offcanvas-bar">
	
		<button class="uk-offcanvas-close" type="button" uk-close></button>

		<h3>Menu</h3>
		<ul class='uk-nav'>

<?php
foreach($stranky as $p){
	if($page->id == $p->id){
		echo "<li class='uk-active'><a href='$p->url'>$p->title</a></li>";
	} else {
		echo "<li><a class='barva_menu_odkazu' href = '$p->url'>$p->title</a></li>";
	}
};
?>
		</ul>
	</div>
</div>
