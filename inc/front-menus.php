<section>
    <select class="cs-select cs-skin-slide">
    <?php

    if(! empty( $post_analytics_settings_front )){

        if (is_array( $post_analytics_settings_front )){
            ?>
            <option value="general" data-model="#general">General Statistics</option>
            <?php 
        }
    }

    ?>
    </select>
</section>