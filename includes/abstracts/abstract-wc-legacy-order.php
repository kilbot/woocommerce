<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Legacy Abstract Order
 *
 * Legacy and deprecated functions are here to keep the WC_Abstract_Order clean.
 * This class will be removed in future versions.
 *
 * @class       WC_Abstract_Order
 * @version     2.6.0
 * @package     WooCommerce/Classes
 * @category    Class
 * @author      WooThemes
 */
abstract class WC_Abstract_Legacy_Order {

    /**
     * Set the customer address.
     * @since 2.2.0
     * @param array $address Address data.
     * @param string $type billing or shipping.
     */
    public function set_address( $address, $type = 'billing' ) {
        foreach ( $address as $key => $value ) {
            update_post_meta( $this->get_id(), "_{$type}_" . $key, $value );
            if ( is_callable( array( $this, "set_{$type}_{$key}" ) ) ) {
                $this->{"set_{$type}_{$key}"}( $value );
            }
        }
    }

    /**
     * Set an order total.
     * @since 2.2.0
     * @param float $amount
     * @param string $total_type
     * @return bool
     */
    public function set_total( $amount, $total_type = 'total' ) {
        if ( ! in_array( $total_type, array( 'shipping', 'tax', 'shipping_tax', 'total', 'cart_discount', 'cart_discount_tax' ) ) ) {
            return false;
        }

        switch ( $total_type ) {
            case 'total' :
                $amount = wc_format_decimal( $amount, wc_get_price_decimals() );
                $this->set_order_total( $amount );
                update_post_meta( $this->get_id(), '_order_total', $amount );
                break;
            case 'cart_discount' :
                $amount = wc_format_decimal( $amount );
                $this->set_discount_total( $amount );
                update_post_meta( $this->get_id(), '_cart_discount', $amount );
                break;
            case 'cart_discount_tax' :
                $amount = wc_format_decimal( $amount );
                $this->set_discount_tax( $amount );
                update_post_meta( $this->get_id(), '_cart_discount_tax', $amount );
                break;
            case 'shipping' :
                $amount = wc_format_decimal( $amount );
                $this->set_shipping_total( $amount );
                update_post_meta( $this->get_id(), '_order_shipping', $amount );
                break;
            case 'shipping_tax' :
                $amount = wc_format_decimal( $amount );
                $this->set_shipping_tax( $amount );
                update_post_meta( $this->get_id(), '_order_shipping_tax', $amount );
                break;
            case 'tax' :
                $amount = wc_format_decimal( $amount );
                $this->set_cart_tax( $amount );
                update_post_meta( $this->get_id(), '_order_tax', $amount );
                break;
        }

        return true;
    }

    /**
     * Magic __isset method for backwards compatibility.
     * @param string $key
     * @return bool
     */
    public function __isset( $key ) {
        // Legacy properties which could be accessed directly in the past.
        $legacy_props = array( 'completed_date', 'id', 'order_type', 'post', 'status', 'post_status', 'customer_note', 'customer_message', 'user_id', 'customer_user', 'prices_include_tax', 'tax_display_cart', 'display_totals_ex_tax', 'display_cart_ex_tax', 'order_date', 'modified_date', 'cart_discount', 'cart_discount_tax', 'order_shipping', 'order_shipping_tax', 'order_total', 'order_tax', 'billing_first_name', 'billing_last_name', 'billing_company', 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state', 'billing_postcode', 'billing_country', 'billing_phone', 'billing_email', 'shipping_first_name', 'shipping_last_name', 'shipping_company', 'shipping_address_1', 'shipping_address_2', 'shipping_city', 'shipping_state', 'shipping_postcode', 'shipping_country', 'customer_ip_address', 'customer_user_agent', 'payment_method_title', 'payment_method', 'order_currency' );
        return $this->get_id() ? ( in_array( $key, $legacy_props ) || metadata_exists( 'post', $this->get_id(), '_' . $key ) ) : false;
    }

    /**
     * Magic __get method for backwards compatibility.
     * @param string $key
     * @return mixed
     */
    public function __get( $key ) {
        /**
         * Maps legacy vars to new getters.
         */
        if ( 'completed_date' === $key ) {
            _doing_it_wrong( $key, 'Order properties should not be accessed directly.', '2.6' );
            return $this->get_date_completed();
		} elseif ( 'paid_date' === $key ) {
            _doing_it_wrong( $key, 'Order properties should not be accessed directly.', '2.6' );
            return $this->get_date_paid();
        } elseif ( 'modified_date' === $key ) {
            _doing_it_wrong( $key, 'Order properties should not be accessed directly.', '2.6' );
            return $this->get_date_modified();
        } elseif ( 'order_date' === $key ) {
            _doing_it_wrong( $key, 'Order properties should not be accessed directly.', '2.6' );
            return $this->get_date_created();
        } elseif ( 'id' === $key ) {
            _doing_it_wrong( $key, 'Order properties should not be accessed directly.', '2.6' );
            return $this->get_id();
		} elseif ( 'post' === $key ) {
            _doing_it_wrong( $key, 'Order properties should not be accessed directly.', '2.6' );
            return get_post( $this->get_id() );
		} elseif ( 'status' === $key || 'post_status' === $key ) {
            _doing_it_wrong( $key, 'Order properties should not be accessed directly.', '2.6' );
            return $this->get_status();
		} elseif ( 'customer_message' === $key || 'customer_note' === $key ) {
            _doing_it_wrong( $key, 'Order properties should not be accessed directly.', '2.6' );
            return $this->get_customer_note();
		} elseif ( in_array( $key, array( 'user_id', 'customer_user' ) ) ) {
            _doing_it_wrong( $key, 'Order properties should not be accessed directly.', '2.6' );
            return $this->get_customer_id();
		} elseif ( 'tax_display_cart' === $key ) {
            _doing_it_wrong( $key, 'Order properties should not be accessed directly.', '2.6' );
			return get_option( 'woocommerce_tax_display_cart' );
		} elseif ( 'display_totals_ex_tax' === $key ) {
            _doing_it_wrong( $key, 'Order properties should not be accessed directly.', '2.6' );
			return 'excl' === get_option( 'woocommerce_tax_display_cart' );
		} elseif ( 'display_cart_ex_tax' === $key ) {
            _doing_it_wrong( $key, 'Order properties should not be accessed directly.', '2.6' );
			return 'excl' === get_option( 'woocommerce_tax_display_cart' );
        } elseif ( 'cart_discount' === $key ) {
            _doing_it_wrong( $key, 'Order properties should not be accessed directly.', '2.6' );
			return $this->get_discount();
        } elseif ( 'cart_discount_tax' === $key ) {
            _doing_it_wrong( $key, 'Order properties should not be accessed directly.', '2.6' );
			return $this->get_discount_tax();
        } elseif ( 'order_tax' === $key ) {
            _doing_it_wrong( $key, 'Order properties should not be accessed directly.', '2.6' );
			return $this->get_cart_tax();
        } elseif ( 'order_shipping_tax' === $key ) {
            _doing_it_wrong( $key, 'Order properties should not be accessed directly.', '2.6' );
            return $this->get_shipping_tax();
        } elseif ( 'order_shipping' === $key ) {
            _doing_it_wrong( $key, 'Order properties should not be accessed directly.', '2.6' );
            return $this->get_total_shipping();
        /**
         * Map vars to getters with warning.
         */
	 	} elseif ( is_callable( array( $this, "get_{$key}" ) ) ) {
			_doing_it_wrong( $key, 'Order properties should not be accessed directly Use get_' . $key . '().', '2.6' );
            return $this->{"get_{$key}"}();
        /**
         * Handle post meta
         */
        } else {
            _doing_it_wrong( $key, 'Meta should not be accessed directly. Use WC_Order::get_order_meta( $key )', '2.6' );
			$value = get_post_meta( $this->get_id(), '_' . $key, true );
		}

        return $value;
    }

	/**
     * Display meta data belonging to an item. @todo
     * @param  array $item
     */
    public function display_item_meta( $item ) {
		_deprecated_function( 'get_item_meta', '2.6', 'wc_display_item_meta' );
        $product   = $this->get_product_from_item( $item );
        $item_meta = new WC_Order_Item_Meta( $item, $product );
        $item_meta->display();
    }

	/**
     * Display download links for an order item.
     * @param  array $item
     */
    public function display_item_downloads( $item ) {
        $product = $this->get_product_from_item( $item );

        if ( $product && $product->exists() && $product->is_downloadable() && $this->is_download_permitted() ) {
            $download_files = $this->get_item_downloads( $item );
            $i              = 0;
            $links          = array();

            foreach ( $download_files as $download_id => $file ) {
                $i++;
                $prefix  = count( $download_files ) > 1 ? sprintf( __( 'Download %d', 'woocommerce' ), $i ) : __( 'Download', 'woocommerce' );
                $links[] = '<small class="download-url">' . $prefix . ': <a href="' . esc_url( $file['download_url'] ) . '" target="_blank">' . esc_html( $file['name'] ) . '</a></small>' . "\n";
            }

            echo '<br/>' . implode( '<br/>', $links );
        }
    }

	/**
     * Get the Download URL.
     *
     * @param  int $product_id
     * @param  int $download_id
     * @return string
     */
    public function get_download_url( $product_id, $download_id ) {
        return add_query_arg( array(
            'download_file' => $product_id,
            'order'         => $this->get_order_key(),
            'email'         => urlencode( $this->get_billing_email() ),
            'key'           => $download_id
        ), trailingslashit( home_url() ) );
    }

	/**
     * Get the downloadable files for an item in this order.
     *
     * @param  array $item
     * @return array
     */
    public function get_item_downloads( $item ) {
        global $wpdb;

        $product_id   = $item['variation_id'] > 0 ? $item['variation_id'] : $item['product_id'];
        $product      = wc_get_product( $product_id );
        if ( ! $product ) {
            /**
             * $product can be `false`. Example: checking an old order, when a product or variation has been deleted.
             * @see \WC_Product_Factory::get_product
             */
            return array();
        }
        $download_ids = $wpdb->get_col( $wpdb->prepare("
            SELECT download_id
            FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions
            WHERE user_email = %s
            AND order_key = %s
            AND product_id = %s
            ORDER BY permission_id
        ", $this->get_billing_email(), $this->get_order_key(), $product_id ) );

        $files = array();

        foreach ( $download_ids as $download_id ) {

            if ( $product->has_file( $download_id ) ) {
                $files[ $download_id ]                 = $product->get_file( $download_id );
                $files[ $download_id ]['download_url'] = $this->get_download_url( $product_id, $download_id );
            }
        }

        return apply_filters( 'woocommerce_get_item_downloads', $files, $item, $this );
    }
	
    /**
     * Get order item meta.
     * @deprecated 2.6.0
     * @param mixed $order_item_id
     * @param string $key (default: '')
     * @param bool $single (default: false)
     * @return array|string
     */
    public function get_item_meta( $order_item_id, $key = '', $single = false ) {
        _deprecated_function( 'get_item_meta', '2.6', 'wc_get_order_item_meta' );
        return get_metadata( 'order_item', $order_item_id, $key, $single );
    }

    /**
     * Get all item meta data in array format in the order it was saved. Does not group meta by key like get_item_meta().
     *
     * @param mixed $order_item_id
     * @return array of objects
     */
    public function get_item_meta_array( $order_item_id ) {
        _deprecated_function( 'get_item_meta_array', '2.6', 'WC_Order_Item::get_meta_data()' );
        $item = $this->get_item( $order_item_id );
        return $item->get_meta_data();
    }

    /**
     * Expand item meta into the $item array.
     * @deprecated 2.6.0 Item meta no longer expanded due to new order item
     *        classes. This function now does nothing to avoid data breakage.
     * @since 2.4.0
     * @param array $item before expansion.
     * @return array
     */
    public function expand_item_meta( $item ) {
        _deprecated_function( 'expand_item_meta', '2.6', '' );
        return $item;
    }

	/**
     * Load the order object. Called from the constructor.
     * @deprecated 2.6.0 Logic moved to constructor
     * @param int|object|WC_Order $order Order to init.
     */
    protected function init( $order ) {
		_deprecated_function( 'init', '2.6', 'Logic moved to constructor' );
        if ( is_numeric( $order ) ) {
            $this->read( $order );
        } elseif ( $order instanceof WC_Order ) {
            $this->read( absint( $order->get_id() ) );
        } elseif ( isset( $order->ID ) ) {
            $this->read( absint( $order->ID ) );
        }
    }

    /**
     * Gets an order from the database.
     * @deprecated 2.6
     * @param int $id (default: 0).
     * @return bool
     */
    public function get_order( $id = 0 ) {
        _deprecated_function( 'get_order', '2.6', 'read' );
        if ( ! $id ) {
            return false;
        }
        if ( $result = get_post( $id ) ) {
            $this->populate( $result );
            return true;
        }
        return false;
    }

    /**
     * Populates an order from the loaded post data.
     * @deprecated 2.6
     * @param mixed $result
     */
    public function populate( $result ) {
        _deprecated_function( 'populate', '2.6', 'read' );
        $this->read( $result->ID );
    }

	/**
     * Cancel the order and restore the cart (before payment).
     * @deprecated 2.6.0 Moved to event handler.
     * @param string $note (default: '') Optional note to add.
     */
    public function cancel_order( $note = '' ) {
		_deprecated_function( 'cancel_order', '2.6', 'update_status' );
        WC()->session->set( 'order_awaiting_payment', false );
        $this->update_status( 'cancelled', $note );
    }

	/**
     * Record sales.
     * @deprecated 2.6.0
     */
    public function record_product_sales() {
		_deprecated_function( 'record_product_sales', '2.6', 'wc_update_total_sales_counts' );
		wc_update_total_sales_counts( $this->get_id() );
    }

	/**
     * Increase applied coupon counts.
     * @deprecated 2.6.0
     */
    public function increase_coupon_usage_counts() {
		_deprecated_function( 'increase_coupon_usage_counts', '2.6', 'wc_update_coupon_usage_counts' );
		wc_update_coupon_usage_counts( $this->get_id() );
    }

    /**
     * Decrease applied coupon counts.
     * @deprecated 2.6.0
     */
    public function decrease_coupon_usage_counts() {
		_deprecated_function( 'decrease_coupon_usage_counts', '2.6', 'wc_update_coupon_usage_counts' );
		wc_update_coupon_usage_counts( $this->get_id() );
    }

	/**
     * Reduce stock levels for all line items in the order.
	 * @deprecated 2.6.0
     */
    public function reduce_order_stock() {
        _deprecated_function( 'reduce_order_stock', '2.6', 'wc_reduce_stock_levels' );
		wc_reduce_stock_levels( $this->get_id() );
    }

	/**
     * Send the stock notifications.
	 * @deprecated 2.6.0 No longer needs to be called directly.
     */
    public function send_stock_notifications( $product, $new_stock, $qty_ordered ) {
        _deprecated_function( 'send_stock_notifications', '2.6' );
    }

	/**
	 * Output items for display in html emails.
	 * @deprecated 2.6.0 Moved to template functions.
	 * @param array $args Items args.
	 * @return string
	 */
	public function email_order_items_table( $args = array() ) {
		return wc_get_email_order_items( $this, $args );
	}
}