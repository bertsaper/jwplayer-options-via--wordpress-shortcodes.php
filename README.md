# jwplayer-options-via-wordpress-shortcodes
WordPress Short Code for Licensed JW Players

When this plugin is placed in wp-content/plugins and activated via the Plugins menu, this Short Code:

[videoInfo id="path_to_video.mp4|path_to_image.jpg"]

Will embed the JWPlayer, which defaults to HTML5 but will revert to Flash if needed. The video path and the image path are separated by a bar (|). 

If you want to embed more than one player per page, you will need to enter an alternative div name for adding a bar and a name after the image address:

[videoInfo id="path_to_video.mp4|path_to_image.jpg|AnotherDivName"]

You can choose any name other than "video-player1"", which is the default. A third player would need a name different from the other two, etc.

If you want to add a duration for your video, you will need to enter a div name and the duration in seconds:

[videoInfo id="path_to_video.mp4|path_to_image.jpg|AnotherDivName|532"]

You can overwrite the default width entered on the settings page. To do so, you will also need to enter a div name and a duration. This changes the width from the default to 40%:

[videoInfo id="path_to_video.mp4|path_to_image.jpg|DivName|532|40%"]

You can overwrite the default aspect ratio entered on the settings page. This requires the entry of a div name, a duration and a width. This will change the aspect ratio to 4:3:

[videoInfo id="path_to_video.mp4|path_to_image.jpg|DivName|532|100%|4:3"] 


These instructions and setup fields will display under Settings | JWPlayer Options Settings.
