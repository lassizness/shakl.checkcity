<?php
// include module
if(!CModule::IncludeModule("main")) return;

// include prolog
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $message = htmlspecialchars($_POST["message"]);

    // Checking the data
    if (empty($name) OR empty($message) OR !check_email($email)) {
        echo "Please fill in the form and try again.";
    } else {
        // Sending the form
        $arEventFields = array(
            "NAME" => $name,
            "EMAIL" => $email,
            "MESSAGE" => $message
        );

        if(!CEvent::Send("FEEDBACK_FORM", CSite::GetSiteByCurrentLanguage(), $arEventFields))
            echo "Error: message was not sent.";
        else
            echo "Thank you for contacting us.";
    }
}

// form
?>
<form action="<?=POST_FORM_ACTION_URI?>" method="POST">
    <input type="text" name="name" placeholder="Your name">
    <input type="email" name="email" placeholder="Your email">
    <textarea name="message" placeholder="Your message"></textarea>
    <input type="submit" name="submit" value="Send">
</form>
