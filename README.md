# jwplayer-options-via--wordpress-shortcodes
WordPress Short Code for Licensed JW Players

This Short Code:

[videoInfo id="http://www.without-warning.net/video/advice_for_yoad_1.mp4|http://www.without-warning.net/video/advice_for_yoad_1.jpg"]
Will embed the JWPlayer, which defaults to HTML5 but will revert to Flash if needed. The video path and the image path are separated by a bar (|).

Please Note: If you want to embed more than one player per page, you will need to enter an alternative Div ID for by adding a bar and a name after the image address:

[videoInfo id="http://www.without-warning.net/video/advice_for_yoad_1.mp4|http://www.without-warning.net/video/advice_for_yoad_1.jpg|AnotherDivName"]
You can choose any name other than "video-player1"", which is the default. A third player would need a name different from the other two, etc.

You can also over-right the default width entered below. To do so, you will also need to enter a Div ID name. This changes the width from the default to 40%:

[videoInfo id="http://www.without-warning.net/video/advice_for_yoad_1.mp4|http://www.without-warning.net/video/advice_for_yoad_1.jpg|DivName|40%"]
Similarly, changing the aspect ratio of the player requires the entry of a Div ID name and a width. This will change the aspect ratio to 4:3:

[videoInfo id="http://www.without-warning.net/video/advice_for_yoad_1.mp4|http://www.without-warning.net/video/advice_for_yoad_1.jpg|DivName|100%|4:3"]
