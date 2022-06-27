<?php
    /**
     * Plugin Name:       Register form custom fields
     * Plugin URI:        #
     * Description:       simple plugin to add custom fields to register form
     * Version:           1.0.0
     * Requires at least: 5.2
     * Requires PHP:      7.2
     * Author:            Noruzzaman
     * Author URI:        #
     * License:           GPL v2 or later
     * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
     * Update URI:        https://example.com/my-plugin/
     * Text Domain:       register
     * Domain Path:       /languages
     */

    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly.
    }

    function register_user_action() {
        $first_name = $_POST['first_name'] ?? '';
        $last_name  = $_POST['last_name'] ?? '';
        $phone      = $_POST['phone'] ?? '';
        ?>

        <p>
            <label for="first_name"><?php _e( 'First Name', 'register' );?>
            </label>
            <input type="text" name="first_name" id="first_name" value="<?php echo esc_attr( $first_name ) ?>"/>
        </p>
        <p>
            <label for="last_name"><?php _e( 'Last Name', 'register' );?>
            </label>
            <input type="text" name="last_name" id="last_name" value="<?php echo esc_attr( $last_name ) ?>"/>
        </p>
        <p>
            <label for="phone"><?php _e( 'Phone', 'register' );?>
            </label>
            <input type="text" name="phone" id="phone" value="<?php echo esc_attr( $phone ) ?>"/>
        </p>

        <?php
        }

        add_action( 'register_form', 'register_user_action', 10, 2 );

        function register_user_error( $errors, $sanitized_user_login, $user_email ) {

            if ( isset( $_POST['first_name'] ) && empty( $_POST['first_name'] ) ) {
                $errors->add( 'first_name_blank', __( '<strong>ERROR</strong>: Please enter your first name.' ) );
            }

            if ( isset( $_POST['last_name'] ) && empty( $_POST['last_name'] ) ) {
                $errors->add( 'last_name_blank', __( '<strong>ERROR</strong>: Please enter your last name.' ) );
            }

            if ( isset( $_POST['phone'] ) && empty( $_POST['phone'] ) ) {
                $errors->add( 'phone_blank', __( '<strong>ERROR</strong>: Please enter your phone.' ) );
            }

            return $errors;
        }

        add_filter( 'registration_errors', 'register_user_error', 10, 3 );

        function user_register_action( $user_id ) {

            if ( ! empty( $_POST['first_name'] ) ) {
                update_user_meta( $user_id, 'first_name', $_POST['first_name'] );
            }

            if ( ! empty( $_POST['last_name'] ) ) {
                update_user_meta( $user_id, 'last_name', $_POST['last_name'] );
            }

            if ( ! empty( $_POST['phone'] ) ) {
                update_user_meta( $user_id, 'phone', $_POST['phone'] );
            }

        }

        add_action( 'user_register', 'user_register_action', 10, 1 );

        function custom_phone_action( $user ) {
            ?>
        <table class="form-table">
            <tr>
                <th>
                    <label for="phone">Phone</label>
                </th>
                <td>

                <input type="text"
                        class="regular-text ltr"
                        id="phone"
                        name="phone"
                        value="<?php echo esc_attr( get_user_meta( $user->ID, 'phone', true ) ) ?>"
                        title="Phone Number"
                        >
                    <p class="description">
                        <?php _e( 'Please enter number here sasd.', 'register' );?>

                    </p>
                </td>
            </tr>
        </table>
        <?php
        }

        /**
         * The save action.
         *
         * @param $user_id int the ID of the current user.
         *
         * @return bool Meta ID if the key didn't exist, true on successful update, false on failure.
         */
        function custom_phone_action_update( $user_id ) {

        // check that the current user have the capability to edit the $user_id
            if ( ! current_user_can( 'edit_user', $user_id ) ) {
                return false;
            }

            // create/update user meta for the $user_id

            return update_user_meta(
                $user_id,
                'phone',
                sanitize_text_field( $_POST['phone'] )
            );
        }

        // Add the field to user's own profile editing screen.
        add_action(
            'show_user_profile',
            'custom_phone_action'
        );

        // Add the field to user profile editing screen.
        add_action(
            'edit_user_profile',
            'custom_phone_action'
        );

        // Add the save action to user's own profile editing screen update.
        add_action(
            'personal_options_update',
            'custom_phone_action_update'
        );

        // Add the save action to user profile editing screen update.
        add_action(
            'edit_user_profile_update',
            'custom_phone_action_update'
        );
