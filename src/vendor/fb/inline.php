<?php
$tagged = TC::cache_get( 'fbphotos-200' );

if ( !$tagged )
{
	include_once "fbmain.php";
	TC::cache_set( 'fbphotos-200', $tagged, 0 );
}

if ($tagged)
{
	?>
	<div class="fb_album">
		<ul id="fb-thumbs">
		<?php foreach ( $tagged['data'] as $data ): ?>
			<li style="wdith:<?=$data['images'][3]['width']?>px;height:<?=$data['images'][3]['height']?>px;"><a href="<?=$data['images'][0]['source']?>" rel="fb-album" title="<?=$data['name']?>">
				<img src="<?=$data['images'][3]['source']?>" 
						 alt="Pic" 
						 width="<?=$data['images'][3]['width']?>" 
						 height="<?=$data['images'][3]['height']?>" />
			</a></li>
		<? endforeach; ?>
		</ul>
	</div>
	
	<?php
}
?>


<?php
/*
	<div class="fb_album">
		<ul>
		<?php foreach ( $albums['data'] as $album ): ?>
			<?php foreach( $photos[$album['id']] as $pid => $data ): ?>
				<li><a href="<?=$data['images'][0]['source']?>" rel="fb-album-<?=$album['id']?>" title="<?=$data['name']?>">
					<img src="<?=$data['images'][3]['source']?>" 
							 alt="Pic" 
							 width="<?=$data['images'][3]['width']?>" 
							 height="<?=$data['images'][3]['height']?>" />
				</a></li>
			<? endforeach; ?>
		<? endforeach; ?>
		</ul>
	</div>
<?php
include_once "fbmain.php";
if ($user)// && $albums && $photos)
{
	foreach ( $albums['data'] as $album )
	{
		?>
		<div class="fb_album" id="fb-album-<?=$album['id']?>">
			<h3><?=$album['name']?></h3>
			<ul>
			<?php foreach( $photos[$album['id']] as $pid => $data ): ?>
				<li><a href="<?=$data['images'][0]['source']?>" rel="fb-album-<?=$album['id']?>" title="<?=$data['name']?>">
					<img src="<?=$data['images'][3]['source']?>" 
							 alt="Pic" 
							 width="<?=$data['images'][3]['width']?>" 
							 height="<?=$data['images'][3]['height']?>" />
				</a></li>
			<? endforeach; ?>
			</ul>
		</div>
		<?
	}
}
?>
*/
?>