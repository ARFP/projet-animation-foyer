// teaser.php
function resize_image_for_teaser($image_id, $width, $height) {
    $image = new Timber\Image($image_id);
    return $image->resize($width, $height);
}
