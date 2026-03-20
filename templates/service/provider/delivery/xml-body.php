<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 */
/* Available data:
 * - orderItems : List of order items
 * - baseItems : List of order base items
 */
echo '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>';
?>


<orders>
<?php 
echo $this->partial('service/provider/delivery/xml-item', ['orderItems' => $this->order_items]);
?>

</orders>