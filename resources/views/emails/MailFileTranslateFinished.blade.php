<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta content="telephone=no" name="format-detection">
    <title></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap"/>
    <style type="text/css">a {text-decoration: none;}</style>
</head>
<body>
    <h2>Votre fichier a été traduis</h2>
    <p>Vous pouvez retrouver votre fichier {{ $file['targetLang'] }}.{{ $file['fileType'] }} en pièce jointe.</p>
    <p>Votre fichier à été traduis de "{{ $file['sourceLang'] }}" vers "{{ $file['targetLang'] }}".</p>
    <br>
        @if(!is_null($messageContent) || $messageContent != "")
            <p>Votre traducteur vous à laissé un message : </p>
            <p> " {{ $messageContent }} " </p>
        @endif
</body>
</html>
