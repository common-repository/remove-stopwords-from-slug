<?php
/*
Plugin Name: Remove Stopwords From Slug
Plugin URI: http://www.berriart.com/remove-stopwords-from-slug/
Description: Removes stopwords from post slugs.
Version: 1.0.1
Author: Alberto Varela
Author URI: http://www.berriart.com/
*/

/*	Copyright 2008  Alberto Varela  (email : alberto@berriart.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/* Removing stopwords */
function remove_stopwords_from_slug($slug) {

	global $post;
	if ($post->ID && $post->post_name != '' ) {
		return $slug;
	}

	$stopwordsserialized = get_option('slugstopwords'); 
	$stopwordsArray = array();
	if( $stopwordsserialized != '' ) $stopwordsArray = unserialize( $stopwordsserialized );

	$slugArray = explode( "-", $slug );
	$slugArray = array_diff( $slugArray, $stopwordsArray );
	$slug = implode( "-", $slugArray );
	
	while( strpos( $slug, "--" ) !== false ) $slug = str_replace( "--", "-", $slug );
	
	if( substr( $slug, 0, 1 ) == "-" ) $slug = substr( $slug, 1 );
	if( substr( $slug, -1, 1 ) == "-" ) $slug = substr( $slug, 0, strlen($slug) -1 );

	return $slug;
}

// Options hook
function add_remove_stopwords_from_slug_options_page() {
    if (function_exists('add_options_page')) {
		add_options_page('Slug Stopwords', 'Slug Stopwords', 10, 'slugstopwords', 'remove_stopwords_from_slug_options_subpanel');
    }
}

// Options panel and form processing
function remove_stopwords_from_slug_options_subpanel() {
		if (isset($_POST['slugstopwords'])) {
			$slugstopwords = $_POST['slugstopwords'];
			$slugstopwordsArray = explode( ",", $slugstopwords );
			$newslugstopwordsArray = array();
			foreach( $slugstopwordsArray as $stopword ) {
				$stopword = trim( $stopword );
				if( $stopword != '' ) $newslugstopwordsArray[] = $stopword;
			}
			$slugstopwords = serialize( $newslugstopwordsArray );
			update_option('slugstopwords', $slugstopwords);
		}
	?>
<div class="wrap">
<form action="" method="post">
<h2>Remove Stopwords From Slug</h2>

	<p>Write here the <em>stopwords</em> that you want to remove from the <abbr title="Universal Resource Locator">URL</abbr> separated by commas. If you want an example or default stopwords for some languages you can donwload them from the next links:</p>
	<p><strong>English:</strong> <a href="http://www.berriart.com/wp-content/english-stopwords.txt">http://www.berriart.com/wp-content/english-stopwords.txt</a></p>
	<p><strong>Español:</strong> <a href="http://www.berriart.com/wp-content/espanol-stopwords.txt">http://www.berriart.com/wp-content/espanol-stopwords.txt</a></p>
	<small>If you have an stopwords list in other language send me please to alberto@berriart.com:</small>

<table class="form-table">
	<tr>
		<th><label for="slugstopwords">Stopwords</label></th>
		<td>
			<?php 
			$stopwords = "";
			$stopwordsserialized = get_option('slugstopwords'); 
			if( $stopwordsserialized != '' ) $stopwords = implode( ", ", unserialize( $stopwordsserialized ) );
			?>
			<textarea name="slugstopwords" id="slugstopwords" class="large-text code"><?php echo $stopwords; ?></textarea>
			Remember that the words you list here never won't be in the post slug. If you want to force a stopword to appear in the slug you must remove it from the list or uninstall the plugin temporaly.
		</td>
	</tr>
</table>

<p class="submit">
	<input type="submit" name="submit" class="button-primary" value="Save Changes" />
</p>
	<p>You can find more info <a href="http://www.berriart.com/remove-stopwords-from-slug/">here</a>, and if you find this plug-in useful, why don't you write a post and recommend it to your readers? ;)  </p>
</form>
</div>
	<?php
}

/* Doing it! */
add_action('admin_menu', 'add_remove_stopwords_from_slug_options_page');
add_filter('sanitize_title', 'remove_stopwords_from_slug', 10000);
register_activation_hook( __FILE__, 'remove_stopwords_from_slug_activate' );

?>
