# Giphy Plugin for GNU Social
------------------------------
Simple plugin to check each posted local notice (Or tweet, or Quip) for the #giphy hashtag and insert a GIF image using the string after.
Example: `#giphy hello world`

## Known Issues

- For now in Qvitter the UI does not update properly when posting a ```#giphy``` hashtagged post. I am looking into this, but it is most likely because the post changes during the save to the db.

## Installing

1. ```cd /var/www/html/plugins``` (Or you GS Plugins directory)
2. ```git clone https://github.com/mitchellurgero/gs_giphy_plugin```
3. Make sure permissions make sense in web server
4. In ```config.php``` put: ```addPlugin('Giphy');```

## Updating

Just go into the ```plugin/giphy``` directory and type: ```git pull``` to update.