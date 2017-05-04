### Running the project

1. get ngrok running with fb messenger account: 
    https://medium.com/@Oskarr3/developing-messenger-bot-with-ngrok-5d23208ed7c8
    https://developers.facebook.com/docs/messenger-platform/guides/quick-start
2. get fb messenger credentials into .env file (can copy `env_example` file)
3. run the app using the PHP Built-In Server
4. nav to the FB page and start with a "hi"

### Planned Functionality

- PHP Features
  - PSR-4 Namespacing: https://mattstauffer.co/blog/a-brief-introduction-to-php-namespacing
- Symfony: Backend app
  - Basic app: http://symfony.com/doc/current/quick_tour/the_big_picture.html
- Maps: Google Static Maps API
  - Rendering static maps with route: http://stackoverflow.com/a/38574451/1817379
  - Custom map styles: https://mapstyle.withgoogle.com/
  - Custom route styles in static map: http://stackoverflow.com/a/30538699/1817379, https://developers.google.com/maps/documentation/static-maps/intro#Paths
- FB Messenger
  - API reference home: https://developers.facebook.com/docs/messenger-platform
    - Location - Quick Reply: https://developers.facebook.com/docs/messenger-platform/send-api-reference/quick-replies
    - Bar selection:
      - List Template: https://developers.facebook.com/docs/messenger-platform/send-api-reference/list-template
      - Generic Template: https://developers.facebook.com/docs/messenger-platform/send-api-reference/generic-template
    - Typing Indicator - Sender Action: https://developers.facebook.com/docs/messenger-platform/send-api-reference/sender-actions
    - Rendering the Image - Image Attachment: https://developers.facebook.com/docs/messenger-platform/send-api-reference/image-attachment
    - Final Button Responses - Share Button: https://developers.facebook.com/docs/messenger-platform/send-api-reference/share-button
    - Call Alex - Call Button: https://developers.facebook.com/docs/messenger-platform/send-api-reference/call-button
  - PHP API: https://github.com/pimax/fb-messenger-php
- ImageMagick: Rendering the final info card
  - API reference home: http://php.net/manual/en/book.imagick.php
    - Circle Cropping: http://stackoverflow.com/a/13810019/1817379
    - Layering images: http://php.net/manual/en/imagick.compositeimage.php
    - Writing text: http://php.net/manual/en/imagick.annotateimage.php
- Firebase: saving data to parseable DB
  - PHP client: https://github.com/ktamas77/firebase-php

