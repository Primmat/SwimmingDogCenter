<div class="pozadi_slideru uk-position-relative uk-visible-toggle uk-light" tabindex="-1" uk-slider="clsActivated: uk-transition-active; center: true; autoplay: true">

    <ul class="uk-slider-items uk-grid">

        <?php foreach($pages->get(1030)->children as $obr): ?>

        <li class="uk-width-3-4">
            <div class="uk-panel">
                <img src="<?php echo $obr->obrazek->first()->url ?>" alt="<?php echo $obr->title ?>">
                <div class="uk-overlay uk-overlay-primary uk-position-top uk-text-center uk-transition-slide-top">
                    <h2 class="uk-margin-remove"><?php echo $page->nadpis ?></h2>
                    <p class="uk-margin-remove"><?php echo $obr->textovepole ?></p>
                </div>
            </div>
        </li>

        <?php endforeach ?>
    </ul>

    <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
    <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slider-item="next"></a>

</div>