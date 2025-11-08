<div class="box box-success">
        <!-- <div class="box-header with-border">
        <h3 class="box-title">Options</h3>
        </div> -->
        <!-- /.box-header -->
        <div class="box-body">

                
        
        
                <form  enctype="multipart/form-data" class="form-horizontal" action="<?php echo htmlspecialchars($_SERVER['SCRIPT_NAME']); ?>" method="post" >

                        <div class="form-group">
                            <div class="col-sm-6">
                                <label for="select-payment-gateway">Payment Gateways</label>
                                <p>Select and add payment gateways</p>
                                <select class="form-control" id="select-payment-gateway" name="select-payment-gateway">
                                    <option value="none">---</option>
                                    <option style="<?php echo !isset($settings_data2['default-payment-gateway']['paystack']) ? '' : 'display:none;' ?>" value="paystack">Paystack</option>
                                    <option style="<?php echo !isset($settings_data2['default-payment-gateway']['paypal']) ? '' : 'display:none;' ?>" value="paypal">PayPal</option>
                                    <option style="<?php echo !isset($settings_data2['default-payment-gateway']['pesapal']) ? '' : 'display:none;' ?>" value="pesapal">Pesapal</option>
                                    <option style="<?php echo !isset($settings_data2['default-payment-gateway']['stripe']) ? '' : 'display:none;' ?>" value="stripe">Stripe</option>
                                    <option style="<?php echo !isset($settings_data2['default-payment-gateway']['paytr']) ? '' : 'display:none;' ?>" value="paytr">PayTR</option>
                                    <option style="<?php echo !isset($settings_data2['default-payment-gateway']['paytm']) ? '' : 'display:none;' ?>" value="paytm">PayTM</option>
                                    <option style="<?php echo !isset($settings_data2['default-payment-gateway']['phonepe']) ? '' : 'display:none;' ?>" value="phonepe">PhonePe</option>
                                    <option style="<?php echo !isset($settings_data2['default-payment-gateway']['flutterwave']) ? '' : 'display:none;' ?>" value="flutterwave">FlutterWave</option>
                                    <option style="<?php echo !isset($settings_data2['default-payment-gateway']['payku']) ? '' : 'display:none;' ?>" value="payku">Payku</option>
                                    <option style="<?php echo !isset($settings_data2['default-payment-gateway']['paymob']) ? '' : 'display:none;' ?>" value="paymob">Paymob</option>
                                    <option style="<?php echo !isset($settings_data2['default-payment-gateway']['midtrans']) ? '' : 'display:none;' ?>" value="midtrans">Midtrans</option>
                                </select>
                                <br>
                                <button type="button" id="add-payment-gateway" class="btn btn-sm btn-success">Add payment gateway</button>
                            </div>
                        </div>                        
                        <hr>
                        

                        <div class="form-group" style="<?php echo isset($settings_data2['default-payment-gateway']['stripe']) ? '' : 'display:none;' ?>" id="stripe-config-container">
                            <div class="col-sm-12">
                                <div style="display:flex;align-items:center;margin:10px 0;">
                                    <button type="button" class="btn btn-xs btn-danger remove-pg-btn" data-pgremove="stripe" >Remove</button><span style="font-weight:bold;font-size:18px;margin:0 10px;">Stripe</span>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label for="stripe-pub-key"><span style="color:red">*</span>Publishable Key</label>
                                        <p>Enter Stripe Publishable Key</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['stripe']) ? '' : 'disabled' ?> required class="form-control" id="stripe-pub-key" placeholder="" name="stripe-pub-key" value="<?php echo isset($settings_data2['default-payment-gateway']['stripe']) ? $settings_data2['default-payment-gateway']['stripe']['stripe-pub-key'] : '' ?>" >
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="stripe-secret-key"><span style="color:red">*</span>Secret Key</label>
                                        <p>Enter Stripe Secret Key</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['stripe']) ? '' : 'disabled' ?> required class="form-control" id="stripe-secret-key" placeholder="" name="stripe-secret-key" value="<?php echo isset($settings_data2['default-payment-gateway']['stripe']) ? $settings_data2['default-payment-gateway']['stripe']['stripe-secret-key'] : '' ?>" >
                                    </div>
                                </div>                                
                            </div> 
                            <div class="col-sm-12">
                                <p style="font-size:12px;margin-top: 15px;display: inline-block;padding: 10px;border: thin solid red;">In your Stripe account under Developers page, select Webhook and click the Add Endpoint button. <br>Under Endpoint URL, Enter (<b><?php echo SITE_URL . "paynotify.php?pg=stripe"  ?></b>)<br>Under events, Expand Payment Intent and select these events: <b>Payment_intent.canceled</b>, <b>Payment_intent.payment_failed</b>, <b>Payment_intent.succeeded</b></p>
                            </div>                          
                        </div>   


                        <div class="form-group" style="<?php echo isset($settings_data2['default-payment-gateway']['paystack']) ? '' : 'display:none;' ?>" id="paystack-config-container">
                            <div class="col-sm-12">
                                <div style="display:flex;align-items:center;margin:10px 0;">
                                    <button type="button" class="btn btn-xs btn-danger remove-pg-btn" data-pgremove="paystack" >Remove</button><span style="font-weight:bold;font-size:18px;margin:0 10px;">Paystack</span>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label for="paystack-pub-key"><span style="color:red">*</span>Public Key</label>
                                        <p>Enter Paystack Public Key</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['paystack']) ? '' : 'disabled' ?> required class="form-control" id="paystack-pub-key" placeholder="" name="paystack-pub-key" value="<?php echo isset($settings_data2['default-payment-gateway']['paystack']) ? $settings_data2['default-payment-gateway']['paystack']['paystack-pub-key'] : '' ?>" >
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="paystack-secret-key"><span style="color:red">*</span>Secret Key</label>
                                        <p>Enter Paystack Secret Key</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['paystack']) ? '' : 'disabled' ?> required class="form-control" id="paystack-secret-key" placeholder="" name="paystack-secret-key" value="<?php echo isset($settings_data2['default-payment-gateway']['paystack']) ? $settings_data2['default-payment-gateway']['paystack']['paystack-secret-key'] : '' ?>" >
                                    </div>
                                </div>                                
                            </div> 
                            <div class="col-sm-12">
                                <p style="font-size:12px;margin-top: 15px;display: inline-block;padding: 10px;border: thin solid red;">Under your paystack account settings page | API Keys & Webhooks tab, Enter <b><?php echo SITE_URL . "paynotify.php?pg=paystack&callback=true"  ?></b> as your callback URL. Enter <b><?php echo SITE_URL . "paynotify.php?pg=paystack"  ?></b> as your Webhook URL</p>
                            </div>                          
                        </div>


                        <div class="form-group" style="<?php echo isset($settings_data2['default-payment-gateway']['flutterwave']) ? '' : 'display:none;' ?>" id="flutterwave-config-container">
                            <div class="col-sm-12">
                                <div style="display:flex;align-items:center;margin:10px 0;">
                                    <button type="button" class="btn btn-xs btn-danger remove-pg-btn" data-pgremove="flutterwave" >Remove</button><span style="font-weight:bold;font-size:18px;margin:0 10px;">Flutterwave</span>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label for="flutterwave-pub-key"><span style="color:red">*</span>Public Key</label>
                                        <p>Enter Flutterwave Public Key</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['flutterwave']) ? '' : 'disabled' ?> required class="form-control" id="flutterwave-pub-key" placeholder="" name="flutterwave-pub-key" value="<?php echo isset($settings_data2['default-payment-gateway']['flutterwave']) ? $settings_data2['default-payment-gateway']['flutterwave']['flutterwave-pub-key'] : '' ?>" >
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="flutterwave-secret-key"><span style="color:red">*</span>Secret Key</label>
                                        <p>Enter Flutterwave Secret Key</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['flutterwave']) ? '' : 'disabled' ?> required class="form-control" id="flutterwave-secret-key" placeholder="" name="flutterwave-secret-key" value="<?php echo isset($settings_data2['default-payment-gateway']['flutterwave']) ? $settings_data2['default-payment-gateway']['flutterwave']['flutterwave-secret-key'] : '' ?>" >
                                    </div>
                                </div>                                
                            </div> 
                            <div class="col-sm-12">
                                <p style="font-size:12px;margin-top: 15px;display: inline-block;padding: 10px;border: thin solid red;">In your Flutterwave account, under settings, Webhooks tab, Enter <b><?php echo SITE_URL . "paynotify.php?pg=flut-wave"  ?></b> as your  Webhook URL and <b><?php echo sha1(SITE_URL . 'Droptaxi')?></b> as your Secret hash</p>
                            </div>                          
                        </div>


                        <div class="form-group" style="<?php echo isset($settings_data2['default-payment-gateway']['paypal']) ? '' : 'display:none;' ?>" id="paypal-config-container">
                            <div class="col-sm-12">
                                <div style="display:flex;align-items:center;margin:10px 0;">
                                    <button type="button" class="btn btn-xs btn-danger remove-pg-btn" data-pgremove="paypal" >Remove</button><span style="font-weight:bold;font-size:18px;margin:0 10px;">PayPal</span>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label for="paypal-client-id"><span style="color:red">*</span>Paypal Client ID</label>
                                        <p>Enter your PayPal account Client ID</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['paypal']) ? '' : 'disabled' ?> required class="form-control" id="paypal-client-id" placeholder="" name="paypal-client-id" value="<?php echo isset($settings_data2['default-payment-gateway']['paypal']) ? $settings_data2['default-payment-gateway']['paypal']['paypal-client-id'] : '' ?>" >
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="paypal-secret-key"><span style="color:red">*</span>Secret Key</label>
                                        <p>Enter your PayPal account Secret key</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['paypal']) ? '' : 'disabled' ?> required class="form-control" id="paypal-secret-key" placeholder="" name="paypal-secret-key" value="<?php echo isset($settings_data2['default-payment-gateway']['paypal']) ? $settings_data2['default-payment-gateway']['paypal']['paypal-secret-key'] : '' ?>" >
                                    </div>
                                </div>                                
                            </div> 
                            <div class="col-sm-12">
                                <p style="font-size:12px;margin-top: 15px;display: inline-block;padding: 10px;border: thin solid red;">Login to your paypal developer account. Ensure you are in Live mode. In the Apps & Credentials tab, click the Create app button. Enter an App Name then click the Create App button. Copy and paste your Client ID and Secret into the resective fields above. Click the Add Webhook button. Enter <b><?php echo SITE_URL . "paynotify.php?pg=paypal"  ?></b> as your Webhook URL. Under event types, check the following checkboxes: <b>Payment authorization created, Payment authorization voided, Payment capture completed, Payment capture declined, Payment capture denied, Payment capture pending, Payment capture refunded, Payment capture reversed</b>. Click Save.</p>
                            </div>                          
                        </div>

                        <div class="form-group" style="<?php echo isset($settings_data2['default-payment-gateway']['pesapal']) ? '' : 'display:none;' ?>" id="pesapal-config-container">
                            <div class="col-sm-12">
                                <div style="display:flex;align-items:center;margin:10px 0;">
                                    <button type="button" class="btn btn-xs btn-danger remove-pg-btn" data-pgremove="pesapal" >Remove</button><span style="font-weight:bold;font-size:18px;margin:0 10px;">Pesapal</span>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label for="pesapal-consumer-key"><span style="color:red">*</span>Consumer Key</label>
                                        <p>Enter Pesapal Consumer Key</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['pesapal']) ? '' : 'disabled' ?> required class="form-control" id="pesapal-consumer-key" placeholder="" name="pesapal-consumer-key" value="<?php echo isset($settings_data2['default-payment-gateway']['pesapal']) ? $settings_data2['default-payment-gateway']['pesapal']['pesapal-consumer-key'] : '' ?>" >
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="pesapal-consumer-secret"><span style="color:red">*</span>Consumer Secret</label>
                                        <p>Enter Pesapal Consumer Secret</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['pesapal']) ? '' : 'disabled' ?> required class="form-control" id="pesapal-consumer-secret" placeholder="" name="pesapal-consumer-secret" value="<?php echo isset($settings_data2['default-payment-gateway']['pesapal']) ? $settings_data2['default-payment-gateway']['pesapal']['pesapal-consumer-secret'] : '' ?>" >
                                    </div>
                                </div>                                
                            </div> 
                            <div class="col-sm-12">
                                <p style="font-size:12px;margin-top: 15px;display: inline-block;padding: 10px;border: thin solid red;">In your PesaPal account, under IPN Settings menu option, enter this <b><?php echo SITE_URL ?></b> as your Website Domain <br> and this <b><?php echo SITE_URL . "paynotify.php?pg=pesapal"  ?></b> as your Website IPN Listener URL</p>
                            </div>                          
                        </div>

                        <div class="form-group" style="<?php echo isset($settings_data2['default-payment-gateway']['phonepe']) ? '' : 'display:none;' ?>" id="phonepe-config-container">
                            <div class="col-sm-12">
                                <div style="display:flex;align-items:center;margin:10px 0;">
                                    <button type="button" class="btn btn-xs btn-danger remove-pg-btn" data-pgremove="phonepe" >Remove</button><span style="font-weight:bold;font-size:18px;margin:0 10px;">PhonePe</span>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label for="phonepe-merchant-id"><span style="color:red">*</span>Merchant ID</label>
                                        <p>Enter PhonePe Merchant ID</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['phonepe']) ? '' : 'disabled' ?> required class="form-control" id="phonepe-merchant-id" placeholder="" name="phonepe-merchant-id" value="<?php echo isset($settings_data2['default-payment-gateway']['phonepe']) ? $settings_data2['default-payment-gateway']['phonepe']['phonepe-merchant-id'] : '' ?>" >
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="phonepe-merchant-salt"><span style="color:red">*</span>Merchant Salt</label>
                                        <p>Enter your PhonePe Merchant Salt</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['phonepe']) ? '' : 'disabled' ?> required class="form-control" id="phonepe-merchant-salt" placeholder="" name="phonepe-merchant-salt" value="<?php echo isset($settings_data2['default-payment-gateway']['phonepe']) ? $settings_data2['default-payment-gateway']['phonepe']['phonepe-merchant-salt'] : '' ?>" >
                                    </div>
                                </div>                                
                            </div> 
                            <div class="col-sm-12">
                                <p style="font-size:12px;margin-top: 15px;display: inline-block;padding: 10px;border: thin solid red;">Contact PhonePe support after registering and request for your Merhant ID and Salt Key</p>
                            </div>                          
                        </div>

                        <div class="form-group" style="<?php echo isset($settings_data2['default-payment-gateway']['paytr']) ? '' : 'display:none;' ?>" id="paytr-config-container">
                            <div class="col-sm-12">
                                <div style="display:flex;align-items:center;margin:10px 0;">
                                    <button type="button" class="btn btn-xs btn-danger remove-pg-btn" data-pgremove="paytr" >Remove</button><span style="font-weight:bold;font-size:18px;margin:0 10px;">PayTR</span>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label for="paytr-merchant-id"><span style="color:red">*</span>Merchant ID</label>
                                        <p>Enter your PayTR Merchant ID</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['paytr']) ? '' : 'disabled' ?> required class="form-control" id="paytr-merchant-id" placeholder="" name="paytr-merchant-id" value="<?php echo isset($settings_data2['default-payment-gateway']['paytr']) ? $settings_data2['default-payment-gateway']['paytr']['paytr-merchant-id'] : '' ?>" >
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="paytr-merchant-key"><span style="color:red">*</span>Merchant Key</label>
                                        <p>Enter your PayTR Merchant Key</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['paytr']) ? '' : 'disabled' ?> required class="form-control" id="paytr-merchant-key" placeholder="" name="paytr-merchant-key" value="<?php echo isset($settings_data2['default-payment-gateway']['paytr']) ? $settings_data2['default-payment-gateway']['paytr']['paytr-merchant-key'] : '' ?>" >
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="paytr-merchant-salt"><span style="color:red">*</span>Merchant Salt</label>
                                        <p>Enter your PayTR Merchant Salt</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['paytr']) ? '' : 'disabled' ?> required class="form-control" id="paytr-merchant-salt" placeholder="" name="paytr-merchant-salt" value="<?php echo isset($settings_data2['default-payment-gateway']['paytr']) ? $settings_data2['default-payment-gateway']['paytr']['paytr-merchant-salt'] : '' ?>" >
                                    </div>
                                </div>                                
                            </div> 
                            <div class="col-sm-12">
                                <p style="font-size:12px;margin-top: 15px;display: inline-block;padding: 10px;border: thin solid red;">In your PayTR account, under settings, set Callback URL to <b><?php echo SITE_URL . "paynotify.php?pg=paytr"  ?></b></p>
                            </div>                          
                        </div>

                        <div class="form-group" style="<?php echo isset($settings_data2['default-payment-gateway']['paytm']) ? '' : 'display:none;' ?>" id="paytm-config-container">
                            <div class="col-sm-12">
                                <div style="display:flex;align-items:center;margin:10px 0;">
                                    <button type="button" class="btn btn-xs btn-danger remove-pg-btn" data-pgremove="paytm" >Remove</button><span style="font-weight:bold;font-size:18px;margin:0 10px;">PayTM</span>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label for="paytm-merchant-id"><span style="color:red">*</span>Merchant ID</label>
                                        <p>Enter your PayTM Merchant ID</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['paytm']) ? '' : 'disabled' ?> required class="form-control" id="paytm-merchant-id" placeholder="" name="paytm-merchant-id" value="<?php echo isset($settings_data2['default-payment-gateway']['paytm']) ? $settings_data2['default-payment-gateway']['paytm']['paytm-merchant-id'] : '' ?>" >
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="paytm-merchant-key"><span style="color:red">*</span>Merchant Key</label>
                                        <p>Enter your PayTM Merchant Key</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['paytm']) ? '' : 'disabled' ?> required class="form-control" id="paytm-merchant-key" placeholder="" name="paytm-merchant-key" value="<?php echo isset($settings_data2['default-payment-gateway']['paytm']) ? $settings_data2['default-payment-gateway']['paytm']['paytm-merchant-key'] : '' ?>" >
                                    </div>
                                </div>                                
                            </div> 
                            <div class="col-sm-12">
                                <p style="font-size:12px;margin-top: 15px;display: inline-block;padding: 10px;border: thin solid red;">In your PayTM account dashboard, on the left sidebar, Click the developer menu, then API keys. under Production API details, click generate keys, copy and paste your Merchant ID (MID) and Merchant Key into the appropriate fields above. Under Website, set Callback URL to <b><?php echo SITE_URL . "paynotify.php?pg=paytm"  ?></b></p>
                            </div>                          
                        </div>

                        <div class="form-group" style="<?php echo isset($settings_data2['default-payment-gateway']['payku']) ? '' : 'display:none;' ?>" id="payku-config-container">
                            <div class="col-sm-12">
                                <div style="display:flex;align-items:center;margin:10px 0;">
                                    <button type="button" class="btn btn-xs btn-danger remove-pg-btn" data-pgremove="payku" >Remove</button><span style="font-weight:bold;font-size:18px;margin:0 10px;">Payku</span>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label for="payku-public-token"><span style="color:red">*</span>Public token</label>
                                        <p>Enter your Payku public token</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['payku']) ? '' : 'disabled' ?> required class="form-control" id="payku-public-token" placeholder="" name="payku-public-token" value="<?php echo isset($settings_data2['default-payment-gateway']['payku']) ? $settings_data2['default-payment-gateway']['payku']['payku-public-token'] : '' ?>" >
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="payku-private-token"><span style="color:red">*</span>Private token</label>
                                        <p>Enter your Payku private token</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['payku']) ? '' : 'disabled' ?> required class="form-control" id="payku-private-token" placeholder="" name="payku-private-token" value="<?php echo isset($settings_data2['default-payment-gateway']['payku']) ? $settings_data2['default-payment-gateway']['payku']['payku-private-token'] : '' ?>" >
                                    </div>
                                </div>                                
                            </div> 
                            <div class="col-sm-12">
                                <p style="font-size:12px;margin-top: 15px;display: inline-block;padding: 10px;border: thin solid red;">On the sidebar menu in payku dashboard, highlight integrations menu item then select tokens integration and API. Create your tokens by clicking the Create button and entering a name for the tokens. A public and private token will be created. Copy and paste them into the appropriate fields above.</p>
                            </div>                          
                        </div>

                        <div class="form-group" style="<?php echo isset($settings_data2['default-payment-gateway']['paymob']) ? '' : 'display:none;' ?>" id="paymob-config-container">
                            <div class="col-sm-12">
                                <div style="display:flex;align-items:center;margin:10px 0;">
                                    <button type="button" class="btn btn-xs btn-danger remove-pg-btn" data-pgremove="paymob" >Remove</button><span style="font-weight:bold;font-size:18px;margin:0 10px;">Paymob</span>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label for="paymob-iframe-id"><span style="color:red">*</span>Iframe ID</label>
                                        <p>Enter the iframe ID to use</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['paymob']) ? '' : 'disabled' ?> required class="form-control" id="paymob-iframe-id" placeholder="" name="paymob-iframe-id" value="<?php echo isset($settings_data2['default-payment-gateway']['paymob']) ? $settings_data2['default-payment-gateway']['paymob']['paymob-iframe-id'] : '' ?>" >
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="paymob-api-key"><span style="color:red">*</span>API Key</label>
                                        <p>Enter your Paymob API key</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['paymob']) ? '' : 'disabled' ?> required class="form-control" id="paymob-api-key" placeholder="" name="paymob-api-key" value="<?php echo isset($settings_data2['default-payment-gateway']['paymob']) ? $settings_data2['default-payment-gateway']['paymob']['paymob-api-key'] : '' ?>" >
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="paymob-int-id"><span style="color:red">*</span>Payment Integration IDs</label>
                                        <p>Enter your Card and Kiosk payment integration IDs. Separate with a pipe "|" character</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['paymob']) ? '' : 'disabled' ?> required class="form-control" id="paymob-int-id" placeholder="" name="paymob-int-id" value="<?php echo isset($settings_data2['default-payment-gateway']['paymob']) ? $settings_data2['default-payment-gateway']['paymob']['paymob-int-id'] : '' ?>" >
                                    </div>
                                </div>                                
                            </div> 
                            <div class="col-sm-12">
                                <p style="font-size:12px;margin-top: 15px;display: inline-block;padding: 10px;border: thin solid red;">In your Paymob dashboard, on the left sidebar, expand the Developer menu and select payment integrations. Add your payment integrations such as Online card, Accept Kiosk etc. For each payment integration ensure you enter <b><?php echo SITE_URL . 'paynotify.php?pg=paymob'?></b> as the Transaction Processed Callback and for the Transaction Response Callback, enter <b><?php echo SITE_URL . 'paynotify.php?pg=paymob&callback=true'?></b></p>
                            </div>                          
                        </div>

                        <div class="form-group" style="<?php echo isset($settings_data2['default-payment-gateway']['midtrans']) ? '' : 'display:none;' ?>" id="midtrans-config-container">
                            <div class="col-sm-12">
                                <div style="display:flex;align-items:center;margin:10px 0;">
                                    <button type="button" class="btn btn-xs btn-danger remove-pg-btn" data-pgremove="midtrans" >Remove</button><span style="font-weight:bold;font-size:18px;margin:0 10px;">Midtrans</span>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <label for="midtrans-merchant-id"><span style="color:red">*</span>Merchant ID</label>
                                        <p>Enter your Midtrans merchant ID</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['midtrans']) ? '' : 'disabled' ?> required class="form-control" id="midtrans-merchant-id" placeholder="" name="midtrans-merchant-id" value="<?php echo isset($settings_data2['default-payment-gateway']['midtrans']) ? $settings_data2['default-payment-gateway']['midtrans']['midtrans-merchant-id'] : '' ?>" >
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="midtrans-client-key"><span style="color:red">*</span>Client Key</label>
                                        <p>Enter your Midtrans client key</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['midtrans']) ? '' : 'disabled' ?> required class="form-control" id="midtrans-client-key" placeholder="" name="midtrans-client-key" value="<?php echo isset($settings_data2['default-payment-gateway']['midtrans']) ? $settings_data2['default-payment-gateway']['midtrans']['midtrans-client-key'] : '' ?>" >
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="midtrans-server-key"><span style="color:red">*</span>Server Key</label>
                                        <p>Enter your Midtrans server key</p>
                                        <input  type="text" <?php echo isset($settings_data2['default-payment-gateway']['midtrans']) ? '' : 'disabled' ?> required class="form-control" id="midtrans-server-key" placeholder="" name="midtrans-server-key" value="<?php echo isset($settings_data2['default-payment-gateway']['midtrans']) ? $settings_data2['default-payment-gateway']['midtrans']['midtrans-server-key'] : '' ?>" >
                                    </div>
                                </div>                                
                            </div> 
                            <div class="col-sm-12">
                                <p style="font-size:12px;margin-top: 15px;display: inline-block;padding: 10px;border: thin solid red;">In your Paymob dashboard, on the left sidebar, expand the Developer menu and select payment integrations. Add your payment integrations such as Online card, Accept Kiosk etc. For each payment integration ensure you enter <b><?php echo SITE_URL . 'paynotify.php?pg=paymob'?></b> as the Transaction Processed Callback and for the Transaction Response Callback, enter <b><?php echo SITE_URL . 'paynotify.php?pg=paymob&callback=true'?></b></p>
                            </div>                          
                        </div>             
                        

                        <hr>

                        <div class="form-group">
                        
                            <div class="col-sm-6">
                                <label for="google-maps-api-key">Google Maps API Key <span style="color:#999;font-size:12px;">(Optional)</span></label>
                                <p>Google Maps API Key used in Web Panel</p>
                                <input  type="text" class="form-control" id="google-maps-api-key" placeholder="" name="google-maps-api-key" value="<?php echo isset($settings_data2['google-maps-api-key']) ? (!empty(DEMO) ? mask_string($settings_data2['google-maps-api-key'] ) : $settings_data2['google-maps-api-key'] ) : ''; ?>" >
                                <button type="button" class="btn btn-info btn-sm" onclick="testGoogleMaps()" style="margin-top:10px;"><i class="fa fa-flask"></i> Test Connection</button>
                                <span id="gmaps-test-result" style="margin-left: 10px;"></span>
                            </div>

                        </div>

                        <hr>

                        <div class="form-group">
                        
                            <div class="col-sm-6">
                                <label for="sms-otp-service">Default OTP SMS Service <span style="color:#999;font-size:12px;">(Optional)</span></label>
                                <p>Set the default service that will be used for sending registration and login OTP SMS.</p>
                                <select id="sms-otp-service" name="sms-otp-service">
                                    <option <?php echo isset($settings_data2['sms-otp-service']) &&  ($settings_data2['sms-otp-service'] == 'firebase') ? 'selected' : ''; ?> value="firebase">Firebase</option>
                                    <option <?php echo isset($settings_data2['sms-otp-service']) &&  ($settings_data2['sms-otp-service'] == 'custom') ? 'selected' : ''; ?> value="custom">Custom</option>
                                    <option <?php echo isset($settings_data2['sms-otp-service']) &&  ($settings_data2['sms-otp-service'] == 'test-otp') ? 'selected' : ''; ?> value="test-otp">Test OTP</option>
                                </select>
                            </div>  
                            
                        </div>

                        <div class="form-group sms-otp-service-conf" id="custom-service-conf" style="display:none;">                            
                            
                            <div class="col-sm-12">
                                <p id="sms-otp-service-integration-note" style="font-size:12px;margin-top: 15px;display: inline-block;padding: 10px;border: thin solid red;">Please contact us for integration of your SMS service provider.</p>
                            </div>

                        </div>

                        <hr>


                        <div class="form-group" style="display:none;">
                        
                            <div class="col-sm-6">
                                <label for="google-push-server-key">FCM Server Push Key <span style="color:#999;font-size:12px;">(Optional)</span></label>
                                <p>Google Firebase Cloud Messaging Push Notification Server Key. Allows the apps to be notified by the server</p>
                                <input  type="text" class="form-control" id="google-push-server-key" placeholder="" name="google-push-server-key" value="<?php echo isset($settings_data2['google-push-server-key']) ? (!empty(DEMO) ? mask_string($settings_data2['google-push-server-key'] ) : $settings_data2['google-push-server-key'] ) : ''; ?>" >
                                <button type="button" class="btn btn-info btn-sm" onclick="testFirebase()" style="margin-top:10px;"><i class="fa fa-flask"></i> Test Connection</button>
                                <span id="firebase-test-result" style="margin-left: 10px;"></span>
                            </div>

                        </div>

                        <!-- <hr> -->


                        <div class="form-group">
                        
                            <div class="col-sm-4">
                                <label for="firebase-web-api-key">Firebase Web API Key <span style="color:#999;font-size:12px;">(Optional)</span></label>
                                <p>Enter your firebase Web API key</p>
                                <input  type="text" class="form-control" id="firebase-web-api-key" placeholder="" name="firebase-web-api-key" value="<?php echo isset($settings_data2['firebase-web-api-key']) ? (!empty(DEMO) ? mask_string($settings_data2['firebase-web-api-key'] ) : $settings_data2['firebase-web-api-key'] ) : ''; ?>" >
                            </div>

                            <div class="col-sm-4">
                                <label for="firebase-rtdb-url">Firebase RTDB URL <span style="color:#999;font-size:12px;">(Optional)</span></label>
                                <p>Enter the URL of the Firebase Realtime Database</p>
                                <input  type="text" class="form-control" id="firebase-rtdb-url" placeholder="" name="firebase-rtdb-url" value="<?php echo isset($settings_data2['firebase-rtdb-url']) ? (!empty(DEMO) ? mask_string($settings_data2['firebase-rtdb-url'] ) : $settings_data2['firebase-rtdb-url'] ) : ''; ?>" >
                            </div>

                            <div class="col-sm-4">
                                <label for="firebase-storage-bucket">Firebase Storage Bucket <span style="color:#999;font-size:12px;">(Optional)</span></label>
                                <p>Enter the Firebase Storage Bucket</p>
                                <input  type="text" class="form-control" id="firebase-storage-bucket" placeholder="" name="firebase-storage-bucket" value="<?php echo isset($settings_data2['firebase-storage-bucket']) ? (!empty(DEMO) ? mask_string($settings_data2['firebase-storage-bucket'] ) : $settings_data2['firebase-storage-bucket'] ) : ''; ?>" >
                            </div>

                        </div>

                        <hr>

                        <!-- PubNub Configuration -->
                        <div class="form-group">
                            <div class="col-sm-12">
                                <h4 style="color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px;">PubNub Real-time Tracking</h4>
                            </div>
                            <div class="col-sm-4">
                                <label for="pubnub-publish-key">PubNub Publish Key</label>
                                <p>Enter your PubNub Publish Key for real-time tracking</p>
                                <input type="text" class="form-control" id="pubnub-publish-key" placeholder="" name="pubnub-publish-key" value="<?php echo isset($settings_data2['pubnub-publish-key']) ? (!empty(DEMO) ? mask_string($settings_data2['pubnub-publish-key']) : $settings_data2['pubnub-publish-key']) : ''; ?>" >
                            </div>
                            <div class="col-sm-4">
                                <label for="pubnub-subscribe-key">PubNub Subscribe Key</label>
                                <p>Enter your PubNub Subscribe Key</p>
                                <input type="text" class="form-control" id="pubnub-subscribe-key" placeholder="" name="pubnub-subscribe-key" value="<?php echo isset($settings_data2['pubnub-subscribe-key']) ? (!empty(DEMO) ? mask_string($settings_data2['pubnub-subscribe-key']) : $settings_data2['pubnub-subscribe-key']) : ''; ?>" >
                            </div>
                            <div class="col-sm-4">
                                <label for="pubnub-secret-key">PubNub Secret Key</label>
                                <p>Enter your PubNub Secret Key</p>
                                <input type="text" class="form-control" id="pubnub-secret-key" placeholder="" name="pubnub-secret-key" value="<?php echo isset($settings_data2['pubnub-secret-key']) ? (!empty(DEMO) ? mask_string($settings_data2['pubnub-secret-key']) : $settings_data2['pubnub-secret-key']) : ''; ?>" >
                            </div>
                            <div class="col-sm-12" style="margin-top: 10px;">
                                <button type="button" class="btn btn-info btn-sm" onclick="testPubNub()"><i class="fa fa-flask"></i> Test PubNub Connection</button>
                                <span id="pubnub-test-result" style="margin-left: 10px;"></span>
                            </div>
                        </div>

                        <hr>

                        <!-- SMTP Email Configuration -->
                        <div class="form-group">
                            <div class="col-sm-12">
                                <h4 style="color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px;">SMTP Email Configuration</h4>
                            </div>
                            <div class="col-sm-3">
                                <label for="smtp-host">SMTP Host</label>
                                <p>SMTP server hostname (e.g., smtp.gmail.com)</p>
                                <input type="text" class="form-control" id="smtp-host" placeholder="" name="smtp-host" value="<?php echo isset($settings_data2['smtp-host']) ? $settings_data2['smtp-host'] : ''; ?>" >
                            </div>
                            <div class="col-sm-3">
                                <label for="smtp-port">SMTP Port</label>
                                <p>SMTP port (usually 587 or 465)</p>
                                <input type="text" class="form-control" id="smtp-port" placeholder="587" name="smtp-port" value="<?php echo isset($settings_data2['smtp-port']) ? $settings_data2['smtp-port'] : '587'; ?>" >
                            </div>
                            <div class="col-sm-3">
                                <label for="smtp-username">SMTP Username</label>
                                <p>SMTP username (usually your email)</p>
                                <input type="text" class="form-control" id="smtp-username" placeholder="" name="smtp-username" value="<?php echo isset($settings_data2['smtp-username']) ? $settings_data2['smtp-username'] : ''; ?>" >
                            </div>
                            <div class="col-sm-3">
                                <label for="smtp-password">SMTP Password</label>
                                <p>SMTP password or app password</p>
                                <input type="password" class="form-control" id="smtp-password" placeholder="" name="smtp-password" value="<?php echo isset($settings_data2['smtp-password']) ? (!empty(DEMO) ? mask_string($settings_data2['smtp-password']) : $settings_data2['smtp-password']) : ''; ?>" >
                            </div>
                            <div class="col-sm-12" style="margin-top: 10px;">
                                <button type="button" class="btn btn-info btn-sm" onclick="testSMTP()"><i class="fa fa-flask"></i> Test SMTP Connection</button>
                                <span id="smtp-test-result" style="margin-left: 10px;"></span>
                            </div>
                        </div>

                        <hr>

                        
                    <button type="submit" class="btn btn-primary btn-block" value="1" name="savesettings2" >Save</button> 
                </form>



                            
        </div>
        <!-- /.box-body -->
    </div>

    
    <script>
        var sms_otp_service_selected = $('#sms-otp-service').val();

        if(sms_otp_service_selected == 'firebase'){
            $('#custom-service-conf').fadeOut();
        }else if(sms_otp_service_selected == 'custom'){
            $('#custom-service-conf').fadeIn();
        }


        $('#sms-otp-service').on('change', function(){

            sms_otp_service_selected = $(this).val();

            if(sms_otp_service_selected == 'firebase'){
                $('#custom-service-conf').fadeOut();
            }else if(sms_otp_service_selected == 'custom'){
                $('#custom-service-conf').fadeIn();
            }

        })

        $('.remove-pg-btn').on('click', function(){
            let payment_gateway = $(this).data('pgremove');
            $(`#select-payment-gateway option[value=${payment_gateway}]`).show();
            switch(payment_gateway){
                case "paystack":
                $('#paystack-config-container').fadeOut();
                $('#paystack-pub-key').prop('disabled', true);
                $('#paystack-secret-key').prop('disabled', true);
                break;

                case "paypal":
                $('#paypal-config-container').fadeOut();
                $('#paypal-client-id').prop('disabled', true);
                $('#paypal-secret-key').prop('disabled', true);
                break;

                case "pesapal":
                $('#pesapal-config-container').fadeOut();
                $('#pesapal-consumer-key').prop('disabled', true);
                $('#pesapal-consumer-secret').prop('disabled', true);
                break;

                case "stripe":
                $('#stripe-config-container').fadeOut();
                $('#stripe-pub-key').prop('disabled', true);
                $('#stripe-secret-key').prop('disabled', true);
                break;

                case "paytr":
                $('#paytr-config-container').fadeOut();
                $('#paytr-merchant-id').prop('disabled', true);
                $('#paytr-merchant-key').prop('disabled', true);
                $('#paytr-merchant-salt').prop('disabled', true);
                break;

                case "paytm":
                $('#paytm-config-container').fadeOut();
                $('#paytm-merchant-id').prop('disabled', true);
                $('#paytm-merchant-key').prop('disabled', true);
                break;

                case "phonepe":
                $('#phonepe-config-container').fadeOut();
                $('#phonepe-merchant-id').prop('disabled', true);
                $('#phonepe-merchant-salt').prop('disabled', true);
                break;

                case "flutterwave":
                $('#flutterwave-config-container').fadeOut();
                $('#flutterwave-pub-key').prop('disabled', true);
                $('#flutterwave-secret-key').prop('disabled', true);
                break;

                case "payku":
                $('#payku-config-container').fadeOut();
                $('#payku-public-token').prop('disabled', true);
                $('#payku-private-token').prop('disabled', true);
                break;

                case "paymob":
                $('#paymob-config-container').fadeOut();
                $('#paymob-iframe-id').prop('disabled', true);
                $('#paymob-api-key').prop('disabled', true);
                $('#paymob-int-id').prop('disabled', true);
                break;

                case "midtrans":
                $('#midtrans-config-container').fadeOut();
                $('#midtrans-merchant-id').prop('disabled', true);
                $('#midtrans-client-key').prop('disabled', true);
                $('#midtrans-server-key').prop('disabled', true);
                break;


            }
        })



        $('#add-payment-gateway').on('click', function(e){
            let payment_gateway = $('#select-payment-gateway').val();
            $(`#select-payment-gateway option[value=${payment_gateway}]`).hide();
            switch(payment_gateway){
                case "paystack":
                $('#paystack-config-container').fadeIn();
                $('#paystack-pub-key').prop('disabled', false);
                $('#paystack-secret-key').prop('disabled', false);                
                break;

                case "paypal":
                $('#paypal-config-container').fadeIn();
                $('#paypal-client-id').prop('disabled', false);
                $('#paypal-secret-key').prop('disabled', false);
                break;

                case "pesapal":
                $('#pesapal-config-container').fadeIn();
                $('#pesapal-consumer-key').prop('disabled', false);
                $('#pesapal-consumer-secret').prop('disabled', false);
                break;

                case "stripe":
                $('#stripe-config-container').fadeIn();
                $('#stripe-pub-key').prop('disabled', false);
                $('#stripe-secret-key').prop('disabled', false);
                break;

                case "paytr":
                $('#paytr-config-container').fadeIn();
                $('#paytr-merchant-id').prop('disabled', false);
                $('#paytr-merchant-key').prop('disabled', false);
                $('#paytr-merchant-salt').prop('disabled', false);
                break;

                case "paytm":
                $('#paytm-config-container').fadeIn();
                $('#paytm-merchant-id').prop('disabled', false);
                $('#paytm-merchant-key').prop('disabled', false);
                break;

                case "phonepe":
                $('#phonepe-config-container').fadeIn();
                $('#phonepe-merchant-id').prop('disabled', false);
                $('#phonepe-merchant-salt').prop('disabled', false);
                break;

                case "flutterwave":
                $('#flutterwave-config-container').fadeIn();
                $('#flutterwave-pub-key').prop('disabled', false);
                $('#flutterwave-secret-key').prop('disabled', false);
                break;

                case "payku":
                $('#payku-config-container').fadeIn();
                $('#payku-public-token').prop('disabled', false);
                $('#payku-private-token').prop('disabled', false);
                break;

                case "paymob":
                $('#paymob-config-container').fadeIn();
                $('#paymob-iframe-id').prop('disabled', false);
                $('#paymob-api-key').prop('disabled', false);
                $('#paymob-int-id').prop('disabled', false);
                break;

                case "midtrans":
                $('#midtrans-config-container').fadeIn();
                $('#midtrans-merchant-id').prop('disabled', false);
                $('#midtrans-client-key').prop('disabled', false);
                $('#midtrans-server-key').prop('disabled', false);
                break;


            }
            
        })

        // Test Functions for API Keys - Server-side implementation
        function testGoogleMaps() {
            const apiKey = $('#google-maps-api-key').val();
            const resultSpan = $('#gmaps-test-result');
            
            if(!apiKey) {
                resultSpan.html('<span style="color: red;"><i class="fa fa-times-circle"></i> Please enter API key first</span>');
                return;
            }
            
            resultSpan.html('<span style="color: blue;"><i class="fa fa-spinner fa-spin"></i> Testing...</span>');
            
            $.ajax({
                url: 'test-api.php',
                method: 'POST',
                data: {
                    service: 'google_maps',
                    config: JSON.stringify({api_key: apiKey})
                },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        resultSpan.html('<span style="color: green;"><i class="fa fa-check-circle"></i> ' + response.message + '</span>');
                    } else {
                        resultSpan.html('<span style="color: red;"><i class="fa fa-times-circle"></i> ' + response.message + '</span>');
                    }
                },
                error: function() {
                    resultSpan.html('<span style="color: red;"><i class="fa fa-times-circle"></i> Connection error</span>');
                }
            });
        }

        function testFirebase() {
            const serverKey = $('#google-push-server-key').val();
            const resultSpan = $('#firebase-test-result');
            
            if(!serverKey) {
                resultSpan.html('<span style="color: red;"><i class="fa fa-times-circle"></i> Please enter server key first</span>');
                return;
            }
            
            resultSpan.html('<span style="color: blue;"><i class="fa fa-spinner fa-spin"></i> Testing...</span>');
            
            $.ajax({
                url: 'test-api.php',
                method: 'POST',
                data: {
                    service: 'firebase',
                    config: JSON.stringify({server_key: serverKey})
                },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        resultSpan.html('<span style="color: green;"><i class="fa fa-check-circle"></i> ' + response.message + '</span>');
                    } else {
                        resultSpan.html('<span style="color: red;"><i class="fa fa-times-circle"></i> ' + response.message + '</span>');
                    }
                },
                error: function() {
                    resultSpan.html('<span style="color: red;"><i class="fa fa-times-circle"></i> Connection error</span>');
                }
            });
        }

        function testPubNub() {
            const publishKey = $('#pubnub-publish-key').val();
            const subscribeKey = $('#pubnub-subscribe-key').val();
            const secretKey = $('#pubnub-secret-key').val();
            const resultSpan = $('#pubnub-test-result');
            
            if(!publishKey || !subscribeKey) {
                resultSpan.html('<span style="color: red;"><i class="fa fa-times-circle"></i> Please enter publish and subscribe keys</span>');
                return;
            }
            
            resultSpan.html('<span style="color: blue;"><i class="fa fa-spinner fa-spin"></i> Testing...</span>');
            
            $.ajax({
                url: 'test-api.php',
                method: 'POST',
                data: {
                    service: 'pubnub',
                    config: JSON.stringify({
                        publish_key: publishKey,
                        subscribe_key: subscribeKey,
                        secret_key: secretKey
                    })
                },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        resultSpan.html('<span style="color: green;"><i class="fa fa-check-circle"></i> ' + response.message + '</span>');
                    } else {
                        resultSpan.html('<span style="color: red;"><i class="fa fa-times-circle"></i> ' + response.message + '</span>');
                    }
                },
                error: function() {
                    resultSpan.html('<span style="color: red;"><i class="fa fa-times-circle"></i> Connection error</span>');
                }
            });
        }

        function testSMTP() {
            const host = $('#smtp-host').val();
            const port = $('#smtp-port').val();
            const username = $('#smtp-username').val();
            const password = $('#smtp-password').val();
            const resultSpan = $('#smtp-test-result');
            
            if(!host || !port) {
                resultSpan.html('<span style="color: red;"><i class="fa fa-times-circle"></i> Please enter host and port</span>');
                return;
            }
            
            resultSpan.html('<span style="color: blue;"><i class="fa fa-spinner fa-spin"></i> Testing...</span>');
            
            $.ajax({
                url: 'test-api.php',
                method: 'POST',
                data: {
                    service: 'smtp',
                    config: JSON.stringify({
                        host: host,
                        port: port,
                        username: username,
                        password: password
                    })
                },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        resultSpan.html('<span style="color: green;"><i class="fa fa-check-circle"></i> ' + response.message + '</span>');
                    } else {
                        resultSpan.html('<span style="color: red;"><i class="fa fa-times-circle"></i> ' + response.message + '</span>');
                    }
                },
                error: function() {
                    resultSpan.html('<span style="color: red;"><i class="fa fa-times-circle"></i> Connection error</span>');
                }
            });
        }
        
    </script>


