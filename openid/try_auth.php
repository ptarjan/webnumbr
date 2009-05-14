<?php

require_once "common.php";

function doOpenID($openid) {
    $consumer = getConsumer();
    // Begin the OpenID authentication process.
    $auth_request = $consumer->begin($openid);

    // No auth request means we can't begin OpenID.
    if (!$auth_request) {
        die("Authentication error; not a valid OpenID.");
    }

    $sreg_request = Auth_OpenID_SRegRequest::build(
                                     // Required
                                     array(''),
                                     // Optional
                                     array('')
    );

    if ($sreg_request) {
        $auth_request->addExtension($sreg_request);
    }

    $policy_uris = $_GET['policies'];

    $pape_request = new Auth_OpenID_PAPE_Request($policy_uris);
    if ($pape_request) {
        $auth_request->addExtension($pape_request);
    }

    // Redirect the user to the OpenID server for authentication.
    // Store the token for this authentication so we can verify the
    // response.

    // send header and html form
    $redirect_url = $auth_request->redirectURL($_REQUEST['_root'], $_REQUEST['_done']);

    // If the redirect URL can't be built, display an error
    // message.
    if (Auth_OpenID::isFailure($redirect_url)) {
        displayError("Could not redirect to server: " . $redirect_url->message);
    } else {
        // Send redirect.
        header("Location: ".$redirect_url);
    }
    // Generate form markup and render it.
    $form_id = 'openid_message';
    $form_html = $auth_request->htmlMarkup($_REQUEST['_root'], $_REQUEST['_done'], false, array('id' => $form_id));

    // Display an error if the form markup couldn't be generated;
    // otherwise, render the HTML.
    if (Auth_OpenID::isFailure($form_html)) {
        displayError("Could not redirect to server: " . $form_html->message);
    } else {
        print $form_html;
    }
}

?>
