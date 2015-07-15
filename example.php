<?php
/**
 * Created for php-instagram-feed
 * User: Danny Hearnah
 * Author: Skyblue Creations Ltd.
 *
 * Date: 15/07/2015
 * Time: 08:24
 */

require 'class/InstagramFeed.php';

$username = 'thechubbyninja';
$instagram = new InstagramFeed($username);

// print a selection of images
$offset = 0;
$limit = 10;
$media = $instagram->getMedia($offset, $limit);

if ($media) {
	?>
	<ul>
		<?php
		foreach ($media as $m) {
			?>
			<li>
				<img src="<?= $m['display_src'] ?>"><br>
				<?= $m['caption'] ?><br>
				<?= $m['likes']['count'] ?> likes
			</li>
			<?php
		}
		?>
	</ul>
	<?php
}