<?php
function dbg()
{
    foreach (func_get_args() as $_key => $_item) { ?>
        <div class="row"><pre class="col dsk-12 tbt-12 mob-12"><?php print_r($_item) ?></pre></div>
        <?php
    }
}   ?>
