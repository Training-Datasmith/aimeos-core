<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 */
/* Available data:
 * - orderItems : List of order items
 */
$enc = $this->encoder();
foreach ($this->get('orderItems', []) as $id => $item) {
    ?>

	<orderitem ref="<?php 
    echo $enc->attr($id);
    ?>">
		<order.ordernumber><![CDATA[<?php 
    echo $item->get_order_number();
    ?>]]></order.ordernumber>

		<?php 
    foreach ($item->to_array() as $key => $value) {
        ?>
			<<?php 
        echo $key;
        ?>><![CDATA[<?php 
        echo !is_scalar($value) ? json_encode($value) : $value;
        ?>]]></<?php 
        echo $key;
        ?>>
		<?php 
    }
    ?>

		<address>
			<?php 
    foreach ($item->get_addresses() as $type => $list) {
        ?>
				<?php 
        foreach ($list as $address_item) {
            ?>
					<addressitem type="<?php 
            echo $enc->attr($address_item->get_type());
            ?>" position="<?php 
            echo $enc->attr($address_item->get_position());
            ?>">
						<?php 
            foreach ($address_item->to_array() as $key => $value) {
                ?>
							<<?php 
                echo $key;
                ?>><![CDATA[<?php 
                echo !is_scalar($value) ? json_encode($value) : $value;
                ?>]]></<?php 
                echo $key;
                ?>>
						<?php 
            }
            ?>
					</addressitem>
				<?php 
        }
        ?>
			<?php 
    }
    ?>
		</address>

		<product>
			<?php 
    foreach ($item->get_products() as $product_item) {
        ?>
				<productitem position="<?php 
        echo $enc->attr($product_item->get_position());
        ?>">
					<?php 
        foreach ($product_item->to_array() as $key => $value) {
            ?>
						<<?php 
            echo $key;
            ?>><![CDATA[<?php 
            echo !is_scalar($value) ? json_encode($value) : $value;
            ?>]]></<?php 
            echo $key;
            ?>>
					<?php 
        }
        ?>
					<attribute>
						<?php 
        foreach ($product_item->get_attribute_items() as $attribute_item) {
            ?>
							<attributeitem>
								<?php 
            foreach ($attribute_item->to_array() as $key => $value) {
                ?>
									<<?php 
                echo $key;
                ?>><![CDATA[<?php 
                echo !is_scalar($value) ? json_encode($value) : $value;
                ?>]]></<?php 
                echo $key;
                ?>>
								<?php 
            }
            ?>
							</attributeitem>
						<?php 
        }
        ?>
					</attribute>
					<product>
						<?php 
        foreach ($product_item->get_products() as $subprod_item) {
            ?>
							<productitem position="<?php 
            echo $enc->attr($subprod_item->get_position());
            ?>">
								<?php 
            foreach ($subprod_item->to_array() as $key => $value) {
                ?>
									<<?php 
                echo $key;
                ?>><![CDATA[<?php 
                echo !is_scalar($value) ? json_encode($value) : $value;
                ?>]]></<?php 
                echo $key;
                ?>>
								<?php 
            }
            ?>
								<attribute>
									<?php 
            foreach ($subprod_item->get_attribute_items() as $attribute_item) {
                ?>
										<attributeitem>
											<?php 
                foreach ($attribute_item->to_array() as $key => $value) {
                    ?>
												<<?php 
                    echo $key;
                    ?>><![CDATA[<?php 
                    echo !is_scalar($value) ? json_encode($value) : $value;
                    ?>]]></<?php 
                    echo $key;
                    ?>>
											<?php 
                }
                ?>
										</attributeitem>
									<?php 
            }
            ?>
								</attribute>
								<product>
								</product>
							</productitem>
						<?php 
        }
        ?>
					</product>
				</productitem>
			<?php 
    }
    ?>
		</product>

		<service>
			<?php 
    foreach ($item->get_services() as $type => $list) {
        ?>
				<?php 
        foreach ($list as $service_item) {
            ?>
					<serviceitem type="<?php 
            echo $enc->attr($service_item->get_type());
            ?>" position="<?php 
            echo $enc->attr($service_item->get_position());
            ?>">
						<?php 
            foreach ($service_item->to_array() as $key => $value) {
                ?>
							<<?php 
                echo $key;
                ?>><![CDATA[<?php 
                echo !is_scalar($value) ? json_encode($value) : $value;
                ?>]]></<?php 
                echo $key;
                ?>>
						<?php 
            }
            ?>
						<attribute>
							<?php 
            foreach ($service_item->get_attribute_items() as $attribute_item) {
                ?>
								<attributeitem>
									<?php 
                foreach ($attribute_item->to_array() as $key => $value) {
                    ?>
										<<?php 
                    echo $key;
                    ?>><![CDATA[<?php 
                    echo !is_scalar($value) ? json_encode($value) : $value;
                    ?>]]></<?php 
                    echo $key;
                    ?>>
									<?php 
                }
                ?>
								</attributeitem>
							<?php 
            }
            ?>
						</attribute>
					</serviceitem>
				<?php 
        }
        ?>
			<?php 
    }
    ?>
		</service>

		<coupon>
			<?php 
    foreach ($item->get_coupons() as $coupon => $list) {
        ?>
				<couponitem>
					<order.base.coupon.code><![CDATA[<?php 
        echo $coupon;
        ?>]]></order.base.coupon.code>
				</couponitem>
			<?php 
    }
    ?>
		</coupon>

	</orderitem>

<?php 
}