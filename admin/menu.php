<ul class="section-tabs">    
    <li <?php if (strstr($_SERVER['PHP_SELF'], "add_image.php") != '') { ?>class="active"<?php } ?>><a href="add_image.php?shop=<?= $shop; ?>">Upload Image</a></li>
    <li <?php if (strstr($_SERVER['PHP_SELF'], "bundle_images.php") != '') { ?>class="active"<?php } ?>><a href="bundle_images.php?shop=<?= $shop; ?>">Bundle Images List</a></li>    
</ul>