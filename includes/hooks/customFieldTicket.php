<?php

use WHMCS\Support\Ticket;
use WHMCS\CustomField\CustomFieldValue;

/**
 * WHMCS Decrypt Custom Fields in Tickets
 *
 * A hack to show the decrypted version of password custom fields in tickets to the client
 * 
 * Optionally you can also exclude certain custom field names if you wish.
 *
 * @package    WHMCS
 * @author     Lee Mahoney <lee@leemahoney.dev>
 * @copyright  Copyright (c) Lee Mahoney 2022
 * @license    MIT License
 * @version    1.0.2
 * @link       https://leemahoney.dev
 */


function decrypt_custom_fields_in_tickets($vars) {

    # Custom Field Names to exclude if needed
    $excludeFields = [];
    
    # Grab the actual ID of the ticket (thanks WHMCS, you'd think a reference to this would be made on a 'view ticket' page)
    $ticketID = Ticket::where('tid', $_GET['tid'])->first()->id;

    # Start the JavaScript code
    $script = "<script type='text/javascript'>
        $(document).ready(function() {
    ";

    # Loop through each of the custom fields related to the ticket
    foreach ($vars['customfields'] as $field) {

        # If the field is not a password, it won't be encrypted, abort!
        if ($field['type'] !== 'password') {
            continue;
        }

        # If the custom field is in the exclude list, abort!
        if (in_array($field['name'], $excludeFields)) {
            continue;
        }

        # Grab the actual encrypted data for that field (in the smarty template variables it will just be ****** which is of no good)
        $value = CustomFieldValue::where(['fieldid' => $field['id'], 'relid' => $ticketID])->first();

        # Only continue if we have a result
        if (count($value)) {

            # Decrypt the value
            $request = localAPI('DecryptPassword', ['password2' => $value->value]);
            
            # Let's call it a password in this case
            $password = $request['password'];

            # As we are looping through multiple fields, use JavaScript to alter the value on the page
            $script .= "
                $('div[menuitemname=\"{$field['name']}\"]').children('div').eq(1).html('{$password}');
            ";

        } else {

            # No value returned so let's just display N/A
            $script .= "
                $('div[menuitemname=\"{$field['name']}\"]').children('div').eq(1).html('N/A');
            ";

        }

    }

    # Close the JavaScript
    $script .= " });
        </script>
    ";

    # Pass the JavaScript to a variable that we can use on the template
    $output['customfieldjavascript'] = $script;

    return $output;

}

# Add the hook
add_hook('ClientAreaPageViewTicket', 1, 'decrypt_custom_fields_in_tickets');
