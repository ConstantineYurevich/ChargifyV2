<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once realpath(dirname(__DIR__)) . '/vendor/autoload.php';

$direct = new \ChargifyV2\DirectHelper(
    '{{your api_id}}',
    '{{your api_secret}}',
    '{{your redirect_url}}'
);
?>
<html>
<head>
    <title>Sign up form</title>
</head>
<body>
<form method="post" action="<?php echo $direct->getSignUpAction() ?>">
    <?php foreach ($direct->getSecureFields() as $name => $value): ?>
        <input type="hidden" name="secure[<?php echo $name ?>]" value="<?php echo $value ?>"/>
    <?php endforeach; ?>
    <!-- For brevity, this form contains no labels, only inputs -->
    <input type="hidden" name="signup[product][handle]" value="small-plan" />

    <label for="signup_users">How Many Users?</label>
    <select name="signup[components][113479]" id="signup_users">
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
    </select>

    <h2>Customer Info</h2>

    <input type="text" name="signup[customer][first_name]" placeholder="First Name" value="John"/><br/>
    <input type="text" name="signup[customer][last_name]" placeholder="Last Name" value="Dow"/><br/>
    <input type="text" name="signup[customer][email]" placeholder="Email" value="example@email.local"/><br/>
    <input type="text" name="signup[customer][organization]" placeholder="Organization" value="Nice Company LLC" /><br/>
    <input type="text" name="signup[customer][address]" placeholder="Address" value="123123, Good Street 27" /><br/>
    <input type="text" name="signup[customer][address_2]" placeholder="Address 2" /><br/>
    <input type="text" name="signup[customer][city]" placeholder="City" value="New York" /><br/>
    <input type="text" name="signup[customer][state]" placeholder="State" value="NY" /><br/>
    <input type="text" name="signup[customer][zip]" placeholder="Zip" value="123123" /><br/>
    <input type="text" name="signup[customer][country]" placeholder="Country" value="US" /><br/>
    <input type="text" name="signup[customer][phone]" placeholder="Phone" value="+111111111111" /><br/>


    <h2>Payment Info</h2>

    <input type="text" name="signup[payment_profile][first_name]" placeholder="First Name" value="John" /><br/>
    <input type="text" name="signup[payment_profile][last_name]" placeholder="Last Name" value="Dow" /><br/>

    <!-- begin credit card fields -->
    <input type="text" name="signup[payment_profile][card_number]" placeholder="Card Number" value="1" /><br/>
    <input type="text" name="signup[payment_profile][expiration_month]" placeholder="Expiration Month" value="12" /><br/>
    <input type="text" name="signup[payment_profile][expiration_year]" placeholder="Expiration Year" value="2016" /><br/>
    <!-- end credit card fields -->

    <!-- begin bank account fields -->
    <input type="text" name="signup[payment_profile][bank_name]" placeholder="Bank Name" /><br/>
    <input type="text" name="signup[payment_profile][bank_routing_number]" placeholder="Bank Routing Number" /><br/>
    <input type="text" name="signup[payment_profile][bank_account_number]" placeholder="Bank Account Number" /><br/>
    <input type="text" name="signup[payment_profile][bank_account_type]" placeholder="Bank Account Type" /><br/>
    <input type="text" name="signup[payment_profile][bank_account_holder_type]" placeholder="Bank Account Holder Type" /><br/>
    <!-- end bank account fields -->

    <input type="text" name="signup[payment_profile][billing_address]" placeholder="Billing Address" /><br/>
    <input type="text" name="signup[payment_profile][billing_address_2]" placeholder="Billing Address 2" /><br/>
    <input type="text" name="signup[payment_profile][billing_city]" placeholder="Billing City" /><br/>
    <input type="text" name="signup[payment_profile][billing_state]" placeholder="Billing State" /><br/>
    <input type="text" name="signup[payment_profile][billing_country]" placeholder="Billing Country" /><br/>
    <input type="text" name="signup[payment_profile][billing_zip]" placeholder="Billing Zip" /><br/>
    <input type="text" name="signup[payment_profile][payment_type]" placeholder="Payment Type" /><br/>
    <input type="text" name="signup[agreement_terms]" placeholder="Agreement Terms" /><br/><br/>
    <input type="submit" value="Sign Up" />
</form>
</body>
</html>