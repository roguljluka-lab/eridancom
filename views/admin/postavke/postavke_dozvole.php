<div class="wrap">
    <div class="notice notice-error is-dismissible">
        <p>Još uvijek nije u funkciji. Zadnje uređivanje 22.02.2025.</p>
    </div>

    <h2>DC rezervacije - dozvole</h2>
    <p>Prikazuju se samo korisnici sa ulogama <b>Urednik</b> i <b>Administrator</b>. Administratori imaju sve ovlasti. </p>

    <?php if ( current_user_can('administrator') ) : ?>

        <div id="postbox-container-2" class="postbox-container">
            <table class="wp-list-table widefat striped">
                <tr>
                    <th>Ime korisnika</th>
                    <th>Nove uplate</th>
                    <th>Storniranje</th>
                    <th>Popusti</th>
                    <th>Brisanje</th>
                    <th>Uređivanje<br>putovanja</th>
                </tr>
                <?php
                $users = get_users(); // Get all users
                foreach ($users as $user) {
                    if ( user_can($user->ID, 'edit_others_posts') ) : ?>
                        <tr>
                            <td><?php echo esc_html($user->display_name); ?> (<?php echo esc_html($user->user_email); ?>)</td>
                            <td><center><input type="checkbox" <?php if ( user_can($user->ID, 'administrator') ) { echo 'disabled checked'; } ?>></center></td>
                            <td><center><input type="checkbox" <?php if ( user_can($user->ID, 'administrator') ) { echo 'disabled checked'; } ?>></center></td>
                            <td><center><input type="checkbox" <?php if ( user_can($user->ID, 'administrator') ) { echo 'disabled checked'; } ?>></center></td>
                            <td><center><input type="checkbox" <?php if ( user_can($user->ID, 'administrator') ) { echo 'disabled checked'; } ?>></center></td>
                            <td><center><input type="checkbox" <?php if ( user_can($user->ID, 'administrator') ) { echo 'disabled checked'; } ?>></center></td>
                        </tr>
                    <?php endif; 
                }
                ?>
            </table>
        </div>


    <?php else: ?>

        <div class="notice notice-error is-dismissible">
            <p>Nemate pravo pristupa ovom dijelu stranice</p>
        </div>

    <?php endif; ?>

</div>