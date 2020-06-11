# FlashImage

A dockerized micro-service for resizing images and serving an appropriately sized one

### Features
- Auto resize image by supplying either the height or width [Only one parameter is required]
- Resizes JPEG, GIF, WEBP, BMP, WBMP, XBM and PNG image formats. All other formats will be resized based on PNG
- Resize images RESTfully and on the fly

### Usage
1. Clone or fork this repo
2. cd `hngi7task2php`
3. Run `composer install` to install dependencies
4. Edit `src/config.php` and set your application URL
5. Optionally set the maximum file size you can process. This defaults to 5MB

### Resizing Images
- You can resize image on the fly by visiting this URL: `$config['app_url']/w_<x>,h_<y>/<imageURLToBeResized>`. 
- Also, you may choose to resize RESTfully by sending a `POST` request to `$config['app_url']` specifying the width, height and image file.

### Documentation
To use the cloud version:
_The cloud version will soon be hosted._

