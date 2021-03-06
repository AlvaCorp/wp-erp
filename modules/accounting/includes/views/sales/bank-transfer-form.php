<table class="wp-list-table widefat fixed striped erp-ac-transaction-table" style="margin: 20px 0;">
    <thead>
        <tr>
            <th class="col-ac"><?php _e( 'Invoice ID', 'erp' ); ?></th>
            <th class="col-ac"><?php _e( 'Due', 'erp' ); ?></th>
            <th class="col-ac"><?php _e( 'Total', 'erp' ); ?></th>
            <th class="col-desc"><?php _e( 'Amount', 'erp' ); ?></th>
            <th class="col-action">&nbsp;</th>
        </tr>
    </thead>

    <tbody>
        <?php
        $total_due = 0;
        foreach ( $results as $key => $result  ) {
        $invoice_url = erp_ac_get_slaes_payment_invoice_url( $result['id'] );
        ?>
        <tr>
            <td class="col-ac">
                <a class="erp-tips" title="<?php echo erp_ac_toltip_per_transaction_ledgers( $result ); ?>" href="<?php echo $invoice_url; ?>"><?php echo '#' . $result['id']; ?></a>
            </td>
            <td class="erp-ac-line-due" data-line_due="<?php echo $result['due']; ?>">
            <?php echo erp_ac_get_price( $result['due'] ); ?>
            </td>
            <td>
            <?php echo erp_ac_get_price( $result['trans_total'] ); ?>
            </td>
            <td class="col-desc col-amount">
                <?php erp_html_form_input( array(
                    'type'        => 'text',
                    'name'        => 'line_total[]',
                    'class'       => 'erp-ac-line-due',
                    'value'       => erp_ac_get_price( $result['due'], ['symbol' => false, 'thousand_separator' => false] ),
                    'custom_attr' => array( 'min' => '0' )
                ) ); ?>

                <?php erp_html_form_input( array(
                    'type'        => 'hidden',
                    'name'        => 'partial_id[]',
                    'value'       => esc_attr( $result['id'] ),
                ) ); ?>

                <?php erp_html_form_input( array(
                    'type'        => 'hidden',
                    'name'        => 'line_account[]',
                    'value'       => 1,
                ) ); ?>

                <?php erp_html_form_input( array(
                    'type'        => 'hidden',
                    'name'        => 'line_desc[]',
                    'value'       => '',
                ) ); ?>

                <?php erp_html_form_input( array(
                    'type'        => 'hidden',
                    'name'        => 'line_qty[]',
                    'value'       => '1',
                ) ); ?>

                <?php erp_html_form_input( array(
                    'type'        => 'hidden',
                    'name'        => 'line_unit_price[]',
                    'value'       => '0',
                ) ); ?>

                <?php erp_html_form_input( array(
                    'type'        => 'hidden',
                    'name'        => 'line_discount[]',
                    'value'       => '0',
                ) ); ?>
            </td>
            <td class="col-action">
                <a href="#" class="erp-ac-remove-line"><span class="dashicons dashicons-trash"></span></a>
            </td>

        </tr>
        <?php
        $total_due = $result['due'] + $total_due;
        } ?>
    </tbody>
    <tfoot>
        <tr>
            <th>&nbsp;</th>

            <th class="align-right"></th>
            <th class="col-amount"><?php _e( 'Total', 'erp' ); ?></th>
            <th class="erp-ac-total-due col-amount">


                <?php
                erp_html_form_input( array(
                    'type'        => 'text',
                    'name'        => 'price_total',
                    'value'       => $total_due,
                    'class'       => 'erp-ac-total-due price-total',
                    'custom_attr' => array( 'readonly' => true )
                ) ); ?>
            </th>
            <th>&nbsp;</th>
        </tr>
    </tfoot>
</table>