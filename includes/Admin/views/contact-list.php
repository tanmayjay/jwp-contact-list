<div class="wrap">
    <div class="">
        <h1 class="wp-heading-inline"><?php _e( 'Contact List', JWP_CL_DOMAIN ) ?></h1>
        <a href="<?php echo admin_url( 'admin.php?page=jwp-contacts&action=new' ) ?>" class="page-title-action">
            <?php _e( 'Add New', JWP_CL_DOMAIN ) ?>
        </a>
    </div>
    <?php if ( isset( $_GET['updated'] ) ) { ?>
        <div class="notice notice-success">
            <p><?php _e( 'Contact Updated Successfully', JWP_CL_DOMAIN ); ?></p>
        </div>
    <?php } ?>
    <?php if ( isset( $_GET['contact-deleted'] ) && $_GET['contact-deleted'] == 'true' ) { ?>
        <div class="notice notice-success">
            <p><?php _e( 'Contact Deleted Successfully', JWP_CL_DOMAIN ); ?></p>
        </div>
    <?php } ?>
    <?php if ( isset( $_GET['inserted'] ) ) { ?>
        <div class="notice notice-success">
            <p><?php _e( 'Contact Inserted Successfully', JWP_CL_DOMAIN ); ?></p>
        </div>
    <?php } ?>
    <form action="" method="post">
        <?php
        $table = new JWP\JCL\Admin\List_Contact();
        $table->prepare_items();
        $table->display();
        ?>
    </form>
</div>